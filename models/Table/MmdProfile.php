<?php
/**
 * Multimedia Display Viewer Profile Table
 * 
 * @copyright Copyright 2007-2012 UCSC Library Digital Initiatives
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package MultimediaDisplay
 */

/**
 * The Multimedia Display Viewer Profile Table class
 * 
 */
class Table_MmdProfile extends Omeka_Db_Table
{
    /**
     * Used to create options for HTML select form elements.
     *
     * @return array
     */
    protected function _getColumnPairs()
    {
        $alias = $this->getTableAlias();
        return array($alias . '.id', $alias . '.name');
    } 

    /**
     * Retrieve currently assigned profiles
     *
     * return array An array of active profile records
     */
    public function getActiveProfiles() {
        $activeProfiles=array();
        return $activeProfiles;
    }

    /**
     * Retrieve the profiles assigned to a given item or item id
     *
     * return array An array of profile records
     * assigned to the given item, or null
     * if the item is not assigned to a profile.
     */

    public function getAssignedProfiles($item) {
        if(is_numeric($item))
            $item_id = $item;
        else if(is_object($item))
            $item_id = $item->id;

        $profiles = array();

        $db = get_db();
        $assignments = $db->getTable('MmdAssign')->findAll();
        //print_r($assignments);
        //die();

        foreach ($assignments as $assignment) {

            if($assignment->item_type_id > 0 && $assignment->item_type_id != $item->item_type_id)
                continue;
            if($assignment->collection_id > 0 && $assignment->collection_id != $item->collection_id)
                continue;


              $filetypestring = $assignment->filetypes;     

            if(!empty($filetypestring)) {

                $files = $item->getFiles();
                $filetypes = explode(',',$filetypestring);
                $flag = false;
                foreach($files as $file) {
                    //TODO does $file->getExtension include period? case? formatting? Should I maybe test mimetype as well, or is that just too unwieldly?
                    if(in_array($file->getExtension,$filetypes))
                        $flag = true;
                }
                if(!$flag)
                    continue;
            }
            $profiles[] = $this->find($assignment->profile_id);
        }
        return $profiles;
    }

    /**
     * Retrieve the first profile assigned to a given item or item id
     */
  public function getPrimaryAssignedProfile($item) {
        $profiles = $this->getAssignedProfiles($item);
        if(count($profiles)>0)
            return $profiles[0];
    }

}
?>