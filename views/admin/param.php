<?php
$options = $this->element->getDecorator('ViewScript')->getOptions();
$checked = empty($options['files'])||$options['files']=='' ? '' : 'checked';
?>
<div class="mmd-profile-param field" style = "display:block;clear:left;">


        <?php echo $this->formLabel($this->element->getName(),
               $this->element->getLabel()." (default)");?>

        <div class="description"><?php echo $this->element->getDescription() ?>
         </div>
<p>
  Enter the default value for this parameter here. This value can be overridden by item level metadata.   
</p>

        <?php echo $this->{$this->element->helper}(
            $this->element->getName(),
            $this->element->getValue(),
            $this->element->getAttribs()
        ) ;?>


<p>
  Choose the metadata element that individual items should use to override the default value of this parameter.   
</p>

<?php  echo $this->formSelect(
    'MmdParamElement['.$paramName.']',
    $options['element_id'],
    array('class'=>'three columns omega'),
    $options['element_options']
    ); ?>
        <?php
if(!empty($options['files'])) {
?>


<p>
  Indicate whether uploaded files of the correct type should be used for this parameter when applicable.   
</p>

Use attached files:<input class="use-files" type="checkbox" id="use-files-<?php echo $options['element_id'];?>" name="MmdParamFiles[<?php echo $paramName; ?>]" checked="<?php echo $options['files'];?>" /> 
File extensions to accept:<input class="profile-extension" type="text" id="extensions-<?php echo $options['element_id'];?>" name="extensions_<?php echo $paramName; ?>" value="<?php echo $options['extensions'];?>" /> 
<?php
}?>

    </div>
