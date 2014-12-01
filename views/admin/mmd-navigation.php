<?php 
/**
 * MultimediaDisplay Admin Nav Bar View Helper file
 *
 * @copyright Copyright 2014 UCSC Library Digital Initiatives
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package MultimediaDisplay
 */

?>

<nav id="section-nav" class="navigation vertical">
<?php
    
    $navArray = array(
        'Setup' => array('label'=>__('Setup'), 'uri'=>url('multimedia-display/defaults') ),
        'Manage Display Profiles' => array('label'=>__('Manage Display Profiles'), 'uri'=>url('multimedia-display/profile') ),
        'Manage Profile Assignments' => array('label'=> __('Manage Profile Assignments'), 'uri'=>url('multimedia-display/assign') ) 
    );
 
    echo nav($navArray, 'mmd_navigation');
?>
</nav>