<?php
/**
 * MultimediaDisplay
 *
 * @copyright Copyright 2014 UCSC Library Digital Initiatives
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package MultimediaDisplay
 */

/**
 * The MultimediaDisplay defaults controller class.
 */
class MultimediaDisplay_DefaultsController extends Omeka_Controller_AbstractActionController
{    
 
/**
 * Install the default configuration for a viewer.
 *
 * @return void
 */
  public function installAction()
  {
    if(isset($_REQUEST['viewer']))
       $viewername = $_REQUEST['viewer'];

    //initialize flash messenger for success or fail messages
    $flashMessenger = $this->_helper->FlashMessenger;

    try{
        require_once(dirname(dirname(__FILE__)).'/models/viewers/AbstractViewer.php');
        require_once(dirname(dirname(__FILE__)).'/models/viewers/'.$viewername.'Viewer.php');
        $viewerClass = 'Mmd_'.$viewername."_Viewer";
        $viewer = new $viewerClass();
        $successMessage = $viewer->installDefaults();
    } catch (Exception $e){
      $flashMessenger->addMessage($e->getMessage(),'error');
    }

    if(!empty($successMessage))
      $flashMessenger->addMessage($successMessage,'success');

    $this->_helper->redirector->gotoUrl('multimedia-display');
  }
}