<?php
/**
 * MultimediaDisplay
 *
 * @copyright Copyright 2014 UCSC Library Digital Initiatives
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package MultimediaDisplay
 */

/**
 * The MultimediaDisplay Assignment controller class.
 */
class MultimediaDisplay_AssignController extends Omeka_Controller_AbstractActionController
{    

/**
 * By default, forward us to browsing existing assignments
 *
 * This action runs when a user navigates to the assignments page
 * without specifying another action.
 *
 * @return void
 */
  public function indexAction()
  {
      $this->_forward('browse');
  }

/**
 * Add a new assignment
 *
 * This action runs when a user adds a new assignment.
 *
 * @return void
 */
  public function addAction()
  {
      $this->_includeForm();
  }

/**
 * Browse display profiles
 *
 * This action runs when a user browses existing assignments.
 *
 * @return void
 */
  public function browseAction()
  {
      $assigns = get_db()->getTable('MmdAssign')->findAll();
      if(count($assigns)==0)
          $this->forward('add');
      $this->view->assigns = $assigns;
  }

/**
 * Edit display profiles
 *
 * This action runs when a user modifies an existing assignment.
 *
 * @return void
 */
  public function editAction()
  {
      $assign_id = $this->_getParam('assign');
      $this->_includeForm($assign_id);
  }

/**
 * Delete display profile
 *
 * This action runs when a user deletes a given assignment.
 *
 * @return void
 */
  public function deleteAction()
  {
      $flashMessenger = $this->_helper->FlashMessenger;

      //delete the assignment
      $assign_id = $this->_getParam('assign');
      try{
          if($assign = get_record_by_id('MmdAssign',$assign_id)) {
              $assign->delete();
              $flashMessenger->addMessage('Assignment deleted successfully','success');
          } else {
              $flashMessenger->addMessage('Error deleting assignment. Profile not found.','error');
              $this->forward('browse');
          }
      } catch(Exception $e) {
	  $flashMessenger->addMessage('Error deleting profile assignment','error');
      }

      //forward to browse
      $this->forward('browse');
  }



  private function _includeForm( $assign_id = 0 ) {
      
    include_once(dirname(dirname(__FILE__))."/forms/AssignForm.php");

    if($assign_id > 0) {
        $form = new Mmd_Form_Assign($assign_id);
    } else {
        $form = new Mmd_Form_Assign();
    }
    $form->setAttrib('id','mmd-assign-edit');

    //initialize flash messenger for success or fail messages
    $flashMessenger = $this->_helper->FlashMessenger;

    try{
      if ($this->getRequest()->isPost()){
	if($form->isValid($this->getRequest()->getPost()))
	  $successMessage = $form->processPost();
	else 
	  $flashMessenger->addMessage('Error processing form!','error');
      } 
    } catch (Exception $e) {
      $flashMessenger->addMessage($e->getMessage(),'error');
    }

    if( isset($successMessage) )
      $flashMessenger->addMessage($successMessage,'success');

    $this->view->form = $form;

  }

}