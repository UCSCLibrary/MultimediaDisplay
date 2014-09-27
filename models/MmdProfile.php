<?php
/**
 * Multimedia Display Viewer Profile Record
 * 
 * @copyright Copyright 2014 UCSC Library Digital Initiatives
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package MultimediaDisplay
 */

/**
 * A Multimedia Display Viewer Profile Table row
 * 
 */
class MmdProfile extends Omeka_Record_AbstractRecord
{
    /**
     * @var int The record ID of the MmdProfile record
     */
    public $id;
    
    /**
     * @var string The name of the viewer profile
     */
    public $name;
    
    /**
     * @var string The name of the viewer class associated with this profile
     */
    public $viewer;

    /**
     * @var array Item independent values for viewer display options
     */
    private $_staticParams;

    /**
     * @var string Element IDs identifying where to find item dependent
     * viewer display options
     */
    private $_itemMaps;

    /**
     * @var object An instance of the Viewer class used by this display profile
     */
    private $_viewer;

    /**
     * Load viewer properties when instantiating class
     *
     * @param object the database object, defaults to standard Omeka db
     * @return null
     */
    public function __construct($db=null) {
        parent::__construct($db);

        if( !empty($this->viewer) ) {
            $this->setViewerByName($this->viewer);
        }

        try{
            $this->_item = get_current_record('Item');
        }catch(Exception $e) {
            //ignore for now
        }
    }

    public function loadParams() {
        $this->_loadAuxParams();
    }

    public function setViewerByName($viewerName) {
        $this->viewer = $viewerName;
        $viewerDir = dirname(dirname(__FILE__)).'/models/viewers/';
        $viewerFileName = $viewerName.'Viewer.php';
        $viewerClassName = 'Mmd_'.$viewerName.'_Viewer';
        require_once($viewerDir.'AbstractViewer.php');
        require_once($viewerDir.$viewerFileName);
        $viewer = new $viewerClassName();
        $this->_viewer = new $viewerClassName();
    }

    public function setViewer($viewer) {
//        echo '<br>setting viewer:<br>';
        $this->_viewer = $viewer;
        $this->viewer = $viewer->name;
    }
    
    public function afterSave($args) {
        parent::afterSave($args);
        $db = get_db();
        $this->_deleteAuxParams($db);
        $this->_saveAuxParams($db);

    }

    public function beforeDelete() {
        $db = get_db();
        $this->_deleteAuxParams($db);
    }

    public function setItem($item) {
        $this->_item = is_numeric($item) ? get_record_by_id('Item',$item) : $item;          
    }

public function setAuxParam($name,$value,$static) {
        if($static) {
            $this->_staticParams[$name] = $value;
        } else {
            $this->_itemMaps[$name] = $value;
        }
    }

    public function getAuxParam($paramName,$item='') {
        if($item==='')
            $item = $this->_item;
        if(isset($this->_itemMaps[$paramName])) {
            
        } else {
            return $this->_staticParams[$paramName];
        }
    }

    public function getAuxParams($item = '') {
        if(empty($this->_itemMaps) || empty($this->_staticParams))
            $this->_loadAuxParams();

        if($item==='')
            $item = $this->_item;
        $itemParams = array();
        foreach($this->_itemMaps as $paramName => $element_id) {
            $record = get_record('ElementText',array('item_id'=>$item->id,'element_id'=>$element_id));

            if(empty($record))
                continue;
            $itemParams[$paramName] = $record;
        }

        return array_merge($this->_staticParams,$itemParams);
    }

    public function getStaticParam($paramName) {
        if(empty($this->_staticParams[$paramName]))
            return false;
        return $this->_staticParams[$paramName];
    }

    public function getItemMap($paramName) {
        if(empty($this->_itemMaps[$paramName]))
            return false;
        return $this->_itemMaps[$paramName];
    }

    public function getViewer(){
        if(empty($this->_viewer)) {
            $this->setViewerByName($this->viewer);
        }
        return $this->_viewer;
    }

    public function head() {
        return $this->_viewer->head();
    }

    public function getBodyHtml($item = '') {
        
        if($item==='')
            $item = $this->_item;
        $params = $this->getAuxParams($item);
        $rv =  $this->_getJsDefs($item,$params);
        $rv.=  $this->getViewer()->getBodyHtml($params);
        return $rv;
    }

    private function _getJsDefs($item = '',$params) {
        $prefix = "mmd_br_";

        if($item==='')
            $item = $this->item;
        
        $rv = '<script type="text/javascript">';
        foreach($params as $key => $value) {
            $rv.= 'var '.$prefix.$key.' = "'.$value.'"; ';
        }
        $rv.= '</script>';

        return $rv;
    }

    private function _loadAuxParams($db=null) {
        $db = empty($db) ? get_db() : $db;

        $this->_staticParams = array();

        $sql = "SELECT `option`, value FROM `".$db->prefix."MmdProfileAux` WHERE profile_id = ".$this->id." AND static = 1";
        // die($sql);
        $response = $db->query($sql);

        foreach($response->fetchAll() as $row) {
            $this->_staticParams[$row['option']] = $row['value'];
        }

        $this->_itemMaps = array();

        $sql = "SELECT `option`, value FROM `".$db->prefix."MmdProfileAux` WHERE profile_id = $this->id AND static = 0";
        $response = $db->query($sql);

        foreach($response->fetchAll() as $row) {
            $this->_itemMaps[$row['option']] = $row['value'];
        }
    }

    private function _saveAuxParams($db){ 
        $auxParams = array();
        $sql = "Insert into `".$db->prefix."MmdProfileAux` (profile_id, `option`, value, static) values";

        $flag = false;

        if(is_array($this->_staticParams)) {
            foreach($this->_staticParams as $key => $value) {
                if($flag)
                    $sql .= ',';
                $sql .= " ($this->id,\"$key\", \"$value\", 1)";
                $flag = true;
            }
        }
        if(is_array($this->_itemMaps)) {
            foreach($this->_itemMaps as $key => $value) {
                if($flag)
                    $sql .= ',';
                $sql .= " ($this->id, \"$key\", \"$value\", 0)";
                $flag = true;
            }
        }
        $response = $db->query($sql);

        return true;
    }

    private function _deleteAuxParams($db) {
        $sql = "delete from `".$db->prefix."MmdProfileAux` where profile_id = $this->id";
        $db->query($sql);
        return true;
    }

}

?>