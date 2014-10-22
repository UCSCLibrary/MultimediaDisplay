<?php 
/**
 * MultimediaDisplay Browse Profiles View file
 *
 * @copyright Copyright 2014 UCSC Library Digital Initiatives
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package MultimediaDisplay
 */

mmd_admin_header(array('Display Profiles'));
echo $this->partial('mmd-navigation.php');
?>
<!-- TODO LINK TO ADD PAGE -->
<div id="primary">
    <h1>Browse Display Profiles</h1>
    <?php 
    echo flash();
?>
<a href="<?php echo url('multimedia-display/profile/add');?>">
<button class="mmd-profile-add big green button">Add New Display Profile </button>
</a>
<?php
if(empty($profiles)) {
?>
<h3>There are currently no display profiles.</h3>
<?php
}else{
?>
    <table id="mmd-profile-table" >
    <tr class="mmd-profile-header" >
      <td class="widthless">Name</td>
      <td class="widthless">Viewer</td>
      <td>Description</td>
      <td class="widthless"></td>
    </tr>
<?php
    foreach($profiles as $profile) {
?>
      <tr class="mmd-profile" id="profile-<?php echo $profile->id; ?>">
        <td class="mmd-profile-name">
          <a href="<?php echo url('multimedia-display/profile/edit/profile/'.$profile->id);?>">
             <?php echo($profile->name); ?>
          </a>
        </td class="mmd-profile-viewer">
        <td>
           <?php echo($profile->viewer); ?>
        </td>
        <td class="mmd-profile-description">
           <?php echo($profile->description); ?>
         </td>
        <td class="mmd-profile-buttons">

<a href="<?php echo url('multimedia-display/profile/edit/profile/'.$profile->id);?>">
<button class="mmd-profile-edit green button">Edit</button>
</a>


<a href="<?php echo url('multimedia-display/profile/delete/profile/'.$profile->id);?>">
<button class="mmd-profile-delete green button">Delete</button>
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