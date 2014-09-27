<?php
$options = $this->element->getDecorator('ViewScript')->getOptions();

?>
<div class="mmd-profile-param field" style = "display:block;clear:left;">
        <?php echo $this->formLabel($this->element->getName(),
               $this->element->getLabel()) ;?>
        <?php echo $this->{$this->element->helper}(
            $this->element->getName(),
            $this->element->getValue(),
            $this->element->getAttribs()
        ) ;?>
<?php  echo $this->formSelect(
    $this->element->getName().'-element',
    $options['element_id'],
    array('class'=>'three columns omega'),
    $options['element_options']
    ); ?>
        <?php 
//echo $this->formErrors($this->element->getMessages());
?>
        <div class="description"><?php echo $this->element->getDescription() ?>
         </div>
    </div>
