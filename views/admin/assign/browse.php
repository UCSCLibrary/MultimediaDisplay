<?php 
/**
 * MultimediaDisplay Browse Assignments View file
 *
 * @copyright Copyright 2014 UCSC Library Digital Initiatives
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package MultimediaDisplay
 */

mmd_admin_header(array('Profile Assignments','Browse Profile Assignments'));
echo $this->partial('mmd-navigation.php');
?>
<!-- TODO LINK TO ADD PAGE -->
<div id="primary">
    <?php 
    echo flash();
?>
<a href="<?php echo url('multimedia-display/assign/add');?>">
<button class="mmd-assign-add big green button">Add New Display Profile </button>
</a>
<?php
if(empty($assigns)) {
?>
    <h3>There are currently no display profiles.</h3>
<?php
}else{
?>
    <table id="mmd-assign-table" >
    <tr class="mmd-assign-header" >
    <td>Profile</td>
    <td>Item Type</td>
    <td>Collection</td>
    <td class="widthless"></td>
    </tr>
<?php

    foreach($assigns as $assign) {
?>      
  <tr class="mmd-assign" id="assign-<?php echo $assign->id; ?>">
        <td class="mmd-assign-profile">
          <a href="<?php echo url('multimedia-display/assign/edit/assign/'.$assign->id);?>">
            <?php echo($assign->getProfileName()); ?>
          </a>
        </td class="mmd-assign-item-type">
        <td>
           <?php echo($assign->getItemTypeName()); ?>
        </td>
        <td class="mmd-assign-collection">
           <?php echo($assign->getCollectionName()); ?>
         </td>
        <td class="mmd-assign-buttons">

<a href="<?php echo url('multimedia-display/assign/edit/assign/'.$assign->id);?>">
<button class="mmd-assign-edit green button">Edit</button>
</a>


<a href="<?php echo url('multimedia-display/assign/delete/assign/'.$assign->id);?>">
<button class="mmd-assign-delete green button">Delete</button>
</a>

         </td>
       </tr>
 <?php
    } 
    ?>
    </table>
<?php 
}
    ?>
</div>

<?php
echo foot(); 
?>