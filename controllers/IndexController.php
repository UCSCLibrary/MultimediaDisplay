<?php
/**
 * MultimediaDisplay
 *
 * @copyright Copyright 2014 UCSC Library Digital Initiatives
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package MultimediaDisplay
 */

/**
 * The MultimediaDisplay index controller class.
 */
class MultimediaDisplay_IndexController extends Omeka_Controller_AbstractActionController
{    
 
/**
 * The default action to display the import from and process it.
 *
 * This action runs before loading the main import form. It 
 * processes the form output if there is any, and populates
 * some variables used by the form.
 *
 * @return void
 */
  public function indexAction()
  {
    include_once(dirname(dirname(__FILE__))."/forms/config_form.php");
    $form = new Mmd_Form_Config();

    //initialize flash messenger for success or fail messages
    $flashMessenger = $this->_helper->FlashMessenger;

    try{
      if ($this->getRequest()->isPost()){
	if($form->isValid($this->getRequest()->getPost()))
	  $successMessage = Mmd_Form_Config::ProcessPost();
	else 
	  $flashMessenger->addMessage('Sorry, we\'re having trouble processing your changes. Please check your form entries and try again.','error');
      } 
    } catch (Exception $e){
      $flashMessenger->addMessage($e->getMessage(),'error');
    }

    if(isset($successMessage))
      $flashMessenger->addMessage($successMessage,'success');
    $this->view->form = $form;
  }


}