<?php
/**
 * MultimediaDisplay
 *
 * @copyright Copyright 2014 UCSC Library Digital Initiatives
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package MultimediaDisplay
 */

/**
 * The MultimediaDisplay Ajax controller class.
 */
class MultimediaDisplay_AjaxController extends Omeka_Controller_AbstractActionController
{    
 
/**
 * Retrieves parameters for a given viewer and returns the form element html
 *
 * @return void
 */
  public function viewparamAction()
  {
      
    $ajaxContext = $this->_helper->getHelper('AjaxContext');
    $ajaxContext->addActionContext('viewparam', 'html')->initContext();

    $viewerName = $this->_getParam('viewer', null);

    $viewerClass = 'Mmd_'.$viewername."_Viewer";
    $viewer = new $viewerClass();
    $params = $viewer->getParameterInfo();
            
    foreach($params as $param) {
        $element = new Mmd_Form_Element_Viewparam($viewerName,$param);
    }

    //$element = new Zend_Form_Element_Text("newName$id");
    //$element->setRequired(true)->setLabel('Name');

    $this->view->field = $element->__toString();

  }


}