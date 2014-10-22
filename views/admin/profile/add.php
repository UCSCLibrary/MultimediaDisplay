<?php 
/**
 * MultimediaDisplay Add Profile View file
 *
 * @copyright Copyright 2014 UCSC Library Digital Initiatives
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package MultimediaDisplay
 */

mmd_admin_header(array('Display Profiles'));
echo $this->partial('mmd-navigation.php');
?>

<div id="primary">
    <h1>Add New Display Profile</h1>
    <?php echo flash(); ?>
    <?php echo $form; ?>
</div>

<?php
echo foot(); 
?>