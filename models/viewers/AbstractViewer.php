<?php
/**
 * Multimedia Display Abstract Viewer
 * 
 * @copyright Copyright 2014 UCSC Library Digital Initiatives
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package MultimediaDisplay
 */

/**
 * Multimedia Display Abstract Viewer class
 * 
 */
abstract class Mmd_Abstract_Viewer
{
    public $name;
    public $defaultProfileName;
    //public $allowedFormats;
    //public $allowedMimeTypes;

    //block below to define/implement in subclass
    protected $_jsPrefix;
    protected $_paramInfo;
    protected $_allowedFiletypes;
    public abstract function viewerHead();
    public abstract function getBodyHtml($params);

    /**
     * Retrieve viewer parameters
     *
     * @return array Information about all parameters supported for customizing the viewer display
     */
    public function getParameterInfo() {
        return $this->_paramInfo;
    }

    public function getJsPrefix() {
        return $this->_jsPrefix;
    }

     /**
     * Install default profile
     *
     * Install default item types, elements, and profiles for easy setup
     *
     * @param array An array of parameters with names and descriptions
     *  of the default values we are setting up
     * @return void
     */
    public function installDefaults($params) {

        if(!$itemType = get_db()->getTable('ItemType')->findByName($params['typeName'])) {   
            $itemType = new ItemType();
            $itemType->name = $params['typeName'];
            $itemType->description = $params['typeDesc'];
            $id = $itemType->save();
        }

        require_once(dirname(dirname(dirname(__FILE__))).'/models/MmdProfile.php');
        $profile = new MmdProfile();
        $profile->setViewer($this);
        $profile->name = $params['profileName'];
        $profile->viewer = $this->name;

        foreach($this->_paramInfo as $param) {
            //$element = new Element();

            //check if element exists in Dublin Core
            $element = get_db()->getTable('Element')->findByElementSetNameAndElementName('Dublin Core',$param['name']);
            if(empty($element)) {
                $element = get_db()->getTable('Element')->findByElementSetNameAndElementName('Dublin Core',ucfirst($param['name']));
            }
            if(empty($element)) {
                $element = get_db()->getTable('Element')->findByElementSetNameAndElementName('Dublin Core',strtoupper($param['name']));
            }
            if(empty($element)) {
                $element = get_db()->getTable('Element')->findByElementSetNameAndElementName('Item Type Metadata',$param['name']);
            }
            if(empty($element)) {
                $element = get_db()->getTable('Element')->findByElementSetNameAndElementName('Item Type Metadata',ucfirst($param['name']));
            }
            if(empty($element)) {
                $element = get_db()->getTable('Element')->findByElementSetNameAndElementName('Item Type Metadata',strtoupper($param['name']));
            }

            if(empty($element)) {
                $element = new Element();
                $element->setElementSet('Item Type Metadata');
                $element->setName($param['name']);
                $element->setDescription($param['description']);
                try {
                    $element->save();
                } catch (Exception $e) {
                    print_r($e);
                    die();
                }   
            }

            $itemType->addElementById($element->id);

            if(isset($param['default']) && $param['default'] !== '')
                $profile->setAuxParam($param['name'],$param['default'],1);
            $profile->setAuxParam($param['name'],$element->id,0);
        }
        $itemType->save();
        $item_type_id = $itemType->id;
        $profile->save();
        $profile_id = $profile->id;

        require_once(dirname(dirname(dirname(__FILE__))).'/models/MmdAssign.php');
        
        $assign = new MmdAssign();
        $assign->item_type_id = $item_type_id;
        $assign->profile_id = $profile_id;
        $assign->filetypes = $this->_allowedFiletypes;
        $assign->save();
    }

    /**
     * Revert to default profile
     *
     * Re-install default item types, elements, and profiles
     *
     * @return void
     */
    public static function RevertDefaults() {
        $defaultProfile = get_db()->getTable('MmdProfile')->findBy(array('name'=>$this->defaultProfileName));
        $defaultProfile->delete();
        self::InstallDefaults();
    }

}

?>