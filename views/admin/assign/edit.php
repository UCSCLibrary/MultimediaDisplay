<?php 
/**
 * MultimediaDisplay Edit Assignment View file
 *
 * @copyright Copyright 2014 UCSC Library Digital Initiatives
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package MultimediaDisplay
 */

mmd_admin_header(array('Profile Assignments','Edit Profile Assignment'));
echo $this->partial('mmd-navigation.php');
?>
<h3>Edit Profile Assignment</h3>
<div id="primary">
<section class="seven columns alpha">
    <?php echo flash(); ?>
    <?php echo $form; ?>
</section>

<section class="three columns omega">
        <div id="save" class="panel">
          <button type="submit" class="big green button" form='mmd-assign-edit'>Save</button>
        </div>
    </section>
</div>
<?php
echo foot(); 
?>