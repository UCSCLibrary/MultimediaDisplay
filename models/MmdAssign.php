<?php
/**
 * Multimedia Display Viewer Assignment Record
 * 
 * @copyright Copyright 2014 UCSC Library Digital Initiatives
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package MultimediaDisplay
 */

/**
 * A Multimedia Display Viewer Assignment Table row
 * 
 */
class MmdAssign extends Omeka_Record_AbstractRecord
{
    /**
     * @var int The record ID of the MmdAssign record
     */
    public $id;
    
    /**
     * @var int The record ID of the Viewer Profile to be linked
     */
    public $profile_id;
    
    /**
     * @var int The record ID of the item type 
     * to be displayed using this viewer profile
     */
    public $item_type_id;
    
    /**
     * @var int The record ID of the collection whose items are 
     * to be displayed using this viewer profile
     */
    public $collection_id;
    
    /**
     * @var bool Indicates whether to use this display profile 
     * by default or not 
     */
    public $default;
    
    /**
     * @var string File extensions (not including periods) for items 
     * which should be displayed using this viewer profile, comma separated
     */
    public $filetypes;

    public function getProfileName() {
        if(is_numeric($this->profile_id)) {
            $profile = get_record_by_id('MmdProfile',$this->profile_id);
            return $profile->name;
        }
    }

    public function getItemTypeName() {
        if(is_numeric($this->item_type_id)) {
            $itemType = get_record_by_id('ItemType',$this->item_type_id);
            return $itemType->name;
        }
    }

    public function getCollectionName() {
        if(is_numeric($this->collection_id)) {
            $collection = get_record_by_id('Collection',$this->collection_id);
            return $collection->name;
        }
    }
}

?>