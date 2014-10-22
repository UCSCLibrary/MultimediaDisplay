<?php
$options = $this->element->getDecorator('ViewScript')->getOptions();
$checked = empty($options['files'])||$options['files']=='' ? '' : 'checked';
?>
<div class="mmd-profile-param field" style = "display:block;clear:left;">

<?php if(!empty($options['files'])) {
?><input class="use-files" type="checkbox" id="use-files-<?php echo $options['element_id'];?>" name="MmdParamFiles[<?php echo $paramName; ?>]" checked="<?php echo $options['files'];?>" /> <?php
}?>

        <?php echo $this->formLabel($this->element->getName(),
               $this->element->getLabel()) ;?>
        <?php echo $this->{$this->element->helper}(
            $this->element->getName(),
            $this->element->getValue(),
            $this->element->getAttribs()
        ) ;?>
<?php  echo $this->formSelect(
    'MmdParamElement['.$paramName.']',
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
