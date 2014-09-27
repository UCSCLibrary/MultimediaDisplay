<?php
class MyBase_View_Helper_FormDisplayParam extends Zend_View_Helper_FormText
{
    public function formDisplayParam($name, $value = null, $attribs = null)
    {
        $info = $this->_getInfo($name, $value, $attribs);
        extract($info);

        $xhtml = $this->formText($name, $value, $attribs);
        $xhtml = '<h1>pretext</h1>'.$xhtml.'<h1>posttext</h1>';
        return $xhtml;
    }
}
?>