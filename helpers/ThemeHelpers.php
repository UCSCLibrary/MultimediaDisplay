<?php 
/**
 * MultimediaDisplay global helper functions
 *
 * @copyright Copyright 2014 UCSC Library Digital Initiatives
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package MultimediaDisplay
 */


function mmd_admin_header($subsections = array())
{
    $mainTitle = __('Multimedia Display');
    $subsections = array_merge(array($mainTitle), $subsections);
    $displayTitle = implode(' | ', $subsections);
    $head = array('title' => $displayTitle,
            'bodyclass' => 'multimedia-display',
            'content_class' => 'horizontal-nav');
    echo head($head);
}

?>