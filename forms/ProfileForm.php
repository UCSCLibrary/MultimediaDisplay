<?php
/**
 * Multimedia Display Profile add/edit form 
 *
 * @package     MultimediaDisplay
 * @copyright   2014 UCSC Library Digital Initiatives
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * MultimediaDisplay add/edit display profile form class
 */
class Mmd_Form_Profile extends Omeka_Form
{
    private $_profileId;

    public function __construct($profile_id=null){
        $this->_profileId = $profile_id;
        parent::__construct();
    }

    /**
     * Construct the form.
     *
     *@return void
     */
    public function init()
    {
        parent::init();
        $this->_registerElements($this->_profileId);
    } 

    /**
     * Define the form elements.
     *
     *@return void
     */
    private function _registerElements($profile_id=null)
    {  
        if(!is_null($profile_id)) {
            $profile = get_record_by_id('MmdProfile',$profile_id);
            $profile->loadParams();
            $profile->getItemMap('title');
            $viewername = $profile->viewer;
            $profilename = $profile->name;
            $profiledesc = $profile->description;
            //TODO (I think) include hidden field for profile ID
        } else {
            $profile = null;
            $profile_id = null;
            $params = array();
            $viewername="";
            $profilename = "";
            $profiledesc = "";
        }
        
        $this->addElement('text','mmdProfileName', 
           array(
               'label' => __('Profile Name'),
               'description' => __('Choose a name to identify your display profile.'),
               'order' => 1,
               'value' => $profilename
           )
        );
        
        $this->addElement('text','mmdProfileDesc', 
           array(
               'label' => __('Profile Description'),
               'description' => __('Describe your display profile.'),
               'order' => 2,
               'value' => $profiledesc
           )
        );
        
        $selectViewerArray =  array(
            'label' => __('Select Viewer'),
            'description' => __('Select a viewer to use for this display profile. Viewer specific options will then appear below.'),
            'order' => 3,
            'multiOptions' => $this->_getViewerOptions(),
            'value' => $viewername
        );
        
        if($profile_id)
          $selectViewerArray['readonly']='readonly';
               

        $this->addElement('select','mmdProfileViewer',$selectViewerArray);
            
        if($viewername != "") {
            $displayGroup = $this->_registerViewerElements($viewername,$profile);
        } else {
            $viewers_string = get_option('mmd_supported_viewers');
            $viewers = unserialize($viewers_string);
            $order = 100;
            foreach( $viewers as $viewerslug => $viewername ) {
                
                $displayGroup = $this->_registerViewerElements($viewerslug);     
                $this->addDisplayGroup(
                    $displayGroup,
                    $viewerslug,
                    array(
                        'legend' => $viewername.' Parameters',
                        'order' => $order,
                        'class' => 'mmd-viewer-params-fieldset'
                    )
                );
                $order++;
            }
        }

        $this->addElement(
            'hidden',
            'mmdProfileId',
            array('value' => $profile_id)
        );

	$this->addElement('hash','csrf_token');

        // Submit:
        $this->addElement('submit', 'mmd-profile-submit', array(
            'label' => __('Save Display Profile'),
            'order' => 200,
            'value' => $profile_id,
            'class' => 'savebutton'
        ));
    }

    private function _registerViewerElements($viewername,$profile = '') {
        static $order=3;
        $group = array();
        require_once(dirname(dirname(__FILE__)).'/models/viewers/AbstractViewer.php');
        require_once(dirname(dirname(__FILE__)).'/models/viewers/'.$viewername.'Viewer.php');
        if($profile==='')
          $profile = new MmdProfile();
        $viewerClass = 'Mmd_'.$viewername."_Viewer";
        $viewer = new $viewerClass();
        $params = $viewer->getParameterInfo();

        foreach($params as $i => $param) {
            $group[]=$viewername.'_'.$param['name'];
            $order++;
            $profile->getItemMap($param['name']);
            $unit = isset($param['unit']) ? $param['unit'] : '';
            if($profile=='') {
                $value='';
                $elementID='';
            }else {
                $value = $profile->getStaticParam($param['name']);
                $elementID = $profile->getItemMap($param['name']);
		$fileParam = $profile->getFileParam($param['name']);
            }

            //$files = isset( $param['files'] ) ? $profile->getFileParam($param['name']) : null;
            $files = isset( $param['files'] ) ? true : null;
	    $extensions = is_array($files) ? $files['extensions'] : '';
	    $extensions = ($fileParam['extensions'] == '') ? $extensions : $fileParam['extensions'];


            switch($param['type']) {

            case 'string' :
                $this->addElement('text',$viewername.'_'.$param['name'], 
                array(
                    'label' => __($param['label']),
                    'class' => 'five columns alpha',
                    'description' => __($param['description']),
                    'value' => $value,
                    'order' => $order,
                    'decorators' => $this->_getParamDecorators($param['name'],$elementID,$unit,$files,$extensions)
                )
                );   
                break;

            case 'int' :
                $this->addElement('text',$viewername.'_'.$param['name'], 
                array(
                    'label' => __($param['label']),
                    'class' => 'five columns alpha',
                    'description' => __($param['description']),
                    'value' => $value,
                    'order' => $order,
                    'decorators' => $this->_getParamDecorators($param['name'],$elementID,$unit,$files,$extensions),
                    'validators' => array('digits')
                )
                );   
                break;


            case 'css' :
             $this->addElement('text',$viewername.'_'.$param['name'], 
                   array(
                       'label' => __($param['label']),
                       'class' => 'five columns alpha',
                       'description' => __($param['description']),
                       'value' => $value,
                       'order' => $order,
                       'decorators' => $this->_getParamDecorators($param['name'],$elementID,$unit,$files,$extensions),
                       'validators' => array(
                           array('regex', false, '/\d+\s?(px$)?(%$)?/i')
                       )
                   )
                );   
                break;

            case 'float' :
                $this->addElement('text',$viewername.'_'.$param['name'],
                array(
                    'label' => __($param['label']),
                    'class' => 'five columns alpha',
                    'description' => __($param['description']),
                    'value' => $value,
                    'order' => $order,
                    'decorators' => $this->_getParamDecorators($param['name'],$elementID,$unit,$files,$extensions),
                    'validators' => array('float')
                )
                );   
                break;

            case 'enum' :
                $this->addElement(
                    'select',
                    $viewername.'_'.$param['name'],
                    array(
                        'label' => __($param['label']),
                        'class' => 'five columns alpha',
                        'description' => __($param['description']),
                        'value' => $value,
                        'order' => $order,
                        'multiOptions' => $param['value'],
                        'decorators' => $this->_getParamDecorators($param['name'],$elementID,$unit,$files,$extensions)
                    )
                );   
                break;
            }
        }
        return $group;
    }

    private function _getDefaultViewerExtensions($paramName) {
      return 'jpg,gif';
    } 

    private function _getParamDecorators($param_name,$element_id,$unit,$files,$extensions=null) {
        $viewScriptOptions = array(
            'viewScript' => 'param.php',
            'paramName' => $param_name,
            'unit' => $unit,
            'element_id' => $element_id,
            'element_options' => $this->_getElementOptions()
        );
        if(!is_null($files))
            $viewScriptOptions['files'] = $files;
        if(!is_null($extensions))
            $viewScriptOptions['extensions'] = $extensions;
	else
	  $viewScriptOptions['extensions'] = $this->_getDefaultViewerExtensions($param_name);
        $decorators = array(
            array('ViewScript',
            $viewScriptOptions,
            array('label'),
            array('description')
            )
        );
        return $decorators;
    }
    
    /**
     *Process the form data and execute actions as necessary
     *
     *@return bool $success true if successful 
     */
    public static function ProcessPost()
    {
        if(!isset($_REQUEST['mmdProfileId']))
            return;
        $profile_id = $_REQUEST['mmdProfileId'];
        if( $profile_id > 0 ) {
            $profile = get_record_by_id('MmdProfile',$profile_id);
        }else
            $profile = new MmdProfile();

        $profile->name = $_REQUEST['mmdProfileName'];
        $profile->description = $_REQUEST['mmdProfileDesc'];
        $profile->viewer = $_REQUEST['mmdProfileViewer'];

        if( isset($_REQUEST['MmdParamElement']) && is_array($_REQUEST['MmdParamElement'] )) {
            $files = isset($_REQUEST['MmdParamFiles']) ? $_REQUEST['MmdParamFiles'] : array();
            foreach($_REQUEST['MmdParamElement'] as $paramName => $elementId) {
                if($elementId > 0) {
                    $profile->setAuxParam(
                        $paramName,
                        $elementId,
                        0
                    );
                }
                if(isset($_REQUEST[$profile->viewer.'_'.$paramName]) && $_REQUEST[$profile->viewer.'_'.$paramName]!='') {
                    $profile->setAuxParam(
                        $paramName,
                        $_REQUEST[$profile->viewer.'_'.$paramName],
                        1
                    );
                }
                if( isset( $files[$paramName] )) {
		  //		  die($_REQUEST['extensions_'.$paramName]);
                    $profile->setAuxParam(
                        $paramName,
                        //array('extensions'=>$_REQUEST['extensions_'.$paramName],'multiple'=>'true'),
			array('extensions'=>$_REQUEST['extensions_'.$paramName]),
                        2
                    );
                }
            }
        }
        $profile->save();
        return('Display Profile saved successfully.');
    }

    private function _getViewerOptions() {
        $viewers = unserialize(get_option('mmd_supported_viewers'));
        $viewers = array_merge(array('0'=>'Select Viewer'),$viewers);
        return $viewers;
    }

    /**
     * Get an array to be used in html select input
 containing all elements.
     * 
     * @return array $elementOptions Array of options for a dropdown
     * menu containing all elements applicable to records of type Item
     */
    private function _getElementOptions()
    {
        $db = get_db();
        $sql = "
        SELECT es.name AS element_set_name, e.id AS element_id, 
        e.name AS element_name, it.name AS item_type_name
        FROM {$db->ElementSet} es 
        JOIN {$db->Element} e ON es.id = e.element_set_id 
        LEFT JOIN {$db->ItemTypesElements} ite ON e.id = ite.element_id 
        LEFT JOIN {$db->ItemType} it ON ite.item_type_id = it.id 
         WHERE es.record_type IS NULL OR es.record_type = 'Item' 
        ORDER BY es.name, it.name, e.name";
        $elements = $db->fetchAll($sql);
        //$options = array();
        $options = array('' => __('None Selected'));
        foreach ($elements as $element) {
            $optGroup = $element['item_type_name'] 
                      ? __('Item Type') . ': ' . __($element['item_type_name']) 
                      : __($element['element_set_name']);
            $value = __($element['element_name']);
            
            $options[$optGroup][$element['element_id']] = $value;
        }
        return $options;
    }

}