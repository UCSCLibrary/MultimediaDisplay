<?php
/**
 * Multimedia Display Assignment configuration form 
 *
 * @package     MultimediaDisplay
 * @copyright   2014 UCSC Library Digital Initiatives
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * MultimediaDisplay add/edit profile assignment form class
 */
class Mmd_Form_Assign extends Omeka_Form
{
    private $_assignId;

    public function __construct($assign_id=null){
        $this->_assignId = $assign_id;
        parent::__construct();
    }

    /**
     * @var object The Assign record that is being edited, if any
     */
    protected $_assign;

    /**
     * Construct the form.
     *
     *@return void
     */
    public function init()
    {
        parent::init();
        $this->_registerElements($this->_assignId);
    }

    /**
     * Define the form elements.
     *
     *@return void
     */
    private function _registerElements($assign=null)
    {  
        //require_once(dirname(dirname(__FILE__)).'/models/Assign.php');
        //require_once(dirname(dirname(__FILE__)).'/models/Table/Assign.php');
        if(is_object($assign))
            $this->_assign = $assign;
        else if(is_numeric($assign)) {
            $this->_assign = get_record_by_id('MmdAssign',$assign);
        }
        if (empty($this->_assign)) {
            $this->_assign = new MmdAssign();
        }

        $assign = $this->_assign;

        $db = get_db();
        $table = $db->getTable('MmdProfile');
        $profiles = $db->getTable('MmdProfile')->findPairsForSelectForm();

        $types = $db->getTable('ItemType')->findPairsForSelectForm();
        $itemTypes = array('0'=>'Assign No Item Type');
        foreach($types as $key=>$value)
            $itemTypes[$key]=$value;

        $cltns = $db->getTable('Collection')->findPairsForSelectForm();
        $collections = array('0'=>'Assign No Collection');
        foreach($cltns as $key=>$value)
            $collections[$key]=$value;

	// Profile
        $this->addElement('select', 'profile_id', array(
            'label'         => 'Display Profile',
            'description'   => 'Which display profile would you like to assign?',
            'multiOptions'       => $profiles,
            'value'         => (string) $assign->profile_id
        )
        );

	// Item Type
        $this->addElement('select', 'item_type_id', array(
            'label'         => 'Item Type',
            'description'   => 'If you select an item type here, items of this type will be assigned to display using the selected profile.',
            'multiOptions'       => $itemTypes,
            'value'         => (string) $assign->item_type_id
        )
        );

	// Collection
        $this->addElement('select', 'collection_id', array(
            'label'         => 'Collection',
            'description'   => 'If you select a collection here, items in this collection will be assigned to display using the selected profile.',
            'multiOptions'       => $collections,
            'value'         => (string) $assign->collection_id
        )
        );

	// Filetypes
        $this->addElement('text', 'filetypes', array(
            'label'         => 'File Types',
            'description'   => 'Enter file extensions (without a period) here. Items with attached files with any of these extensions will be assigned to display with the selected profile. (e.g. "jpg,jpeg,png,gif,bmp")',
            'value'         => (string) $assign->filetypes
        )
        );
/*
        $checked = $assign->default ? 0 : 1;
	// Default
        $this->addElement('select', 'default', array(
            'label'         => 'Default',
            'description'   => 'Should items assigned to this display profile use it by default, or display normally and include a link to this display?',
            'multiOptions'       => array('Use display profile by default','Link to display profile'),
            'value'         => $checked
        )
        );
*/

    }

    /**
     *Process the form data and execute actions as necessary
     *
     *@return bool $success true if successful 
     */
    public function processPost()
    {
        if(empty($this->_assign))
            throw new Exception('Assign record not set!');
        
        $this->_assign->setPostData($_POST);
        $this->_assign->save();
        return 'Display Profile Assignment created successfully')
    }
}

