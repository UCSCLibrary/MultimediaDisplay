<?php
/**
 * MultimediaDisplay
 *
 * @copyright Copyright 2014 UCSC Library Digital Initiatives
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package MultimediaDisplay
 */

/**
 * The MultimediaDisplay Display Profile controller class.
 */
class MultimediaDisplay_ProfileController extends Omeka_Controller_AbstractActionController
{    

/**
 * By default, forward us to browsing existing display profiles
 *
 * This action runs when a user navigates to the profiles page
 * without specifying another action.
 *
 * @return void
 */
  public function indexAction()
  {
      $this->_forward('browse');
  }

/**
 * Add a new display profile
 *
 * This action runs when a user adds a new display profile.
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
 * This action runs when a user browses existing display profiles.
 *
 * @return void
 */
  public function browseAction()
  { 
      
      $profiles = get_db()->getTable('MmdProfile')->findAll();
      if(count($profiles)==0)
          $this->forward('add');
      $this->view->profiles = $profiles;
  }

/**
 * Edit display profiles
 *
 * This action runs when a user modifies an existing display profile
 *
 * @return void
 */
  public function editAction()
  {                     
      $profile_id = $this->_getParam('profile');
      $this->_includeForm($profile_id);
  }

/**
 * Delete display profile
 *
 * This action runs when a user deletes a given display profile.
 *
 * @return void
 */
  public function deleteAction()
  {
      $flashMessenger = $this->_helper->FlashMessenger;

      //delete the profile
      $profile_id = $this->_getParam('profile');
      try{
          $profile = get_record_by_id('MmdProfile',$profile_id);
          $profile->delete();
      } catch(Exception $e) {
	  $flashMessenger->addMessage('Error deleting profile','error');
      }

      $flashMessenger->addMessage('Profile deleted successfully','success');

      //forward to browse
      $this->forward('browse');
  }

  private function _includeForm( $profile_id = 0 ) {
      
    include_once(dirname(dirname(__FILE__))."/forms/ProfileForm.php");
    if($profile_id > 0)
        $form = new Mmd_Form_Profile($profile_id);
    else
        $form = new Mmd_Form_Profile();

    //initialize flash messenger for success or fail messages
    $flashMessenger = $this->_helper->FlashMessenger;

    try{
      if ($this->getRequest()->isPost()){
	if($form->isValid($this->getRequest()->getPost()))
	  $successMessage = Mmd_Form_Profile::ProcessPost();
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