<?php 
/**
 * MultimediaDisplay Plugin Settings View file
 *
 * @copyright Copyright 2014 UCSC Library Digital Initiatives
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package MultimediaDisplay
 */

mmd_admin_header(array('Multimedia Display','Edit Plugin Settings'));
echo $this->partial('mmd-navigation.php');

?>

<div id="primary">
    <?php echo flash(); ?>
    <p>
    This plugin allows you to display content using any of the installed viewers.
    Each viewer requires configuration options to tell it where to find the media files to display, and how exactly to display them. You can save these options using <em>Display Profiles</em>. Each display profile contains all options necessary for one of the installed viewers to display media for an item.
    </p>
    <p>
    After you create a display profile, you need to assign it to the items you want to display using that profile. Each <em>Profile Assignment</em> assigns a set of items (by item type, collection, or type of attached file) to a display profile. 
    </p>
    <p>
    If you want to get this up and running quickly, you can use this page to set all this up automatically. For each viewer, this form will create a new item type with fields for any metadata the viewer might require. A default display profile is also created for this viewer, and a profile assignment is created to link the default profile to the default item type. 
    </p>
    <form id="mmd-install-defaults" action="multimedia-display/defaults/install">
    <fieldset>
      <legend>Install Default Configurations</legend>

      <?php
   
    foreach ($viewers as $viewerslug => $viewername) {
?>
      <div class="viewer-defaults-div">
      <div class="viewer-name-div">
        <?php echo $viewername;?>
      </div>
      <button class="green button" type="submit" name="viewer" value="<?php echo $viewerslug;?>">Install Defaults</button>
      </div>
<?php
    }
      ?>
    </fieldset>
    </form>

</div>

<?php
echo foot(); 
?>