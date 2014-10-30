<?php
/**
 * Multimedia Display BookReader Viewer
 * 
 * @copyright Copyright 2014 UCSC Library Digital Initiatives
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package MultimediaDisplay
 */

/**
 * Multimedia Display BookReader Viewer class
 * 
 */
class Mmd_BookReader_Viewer extends Mmd_Abstract_Viewer
{

    public function __construct() {
        //parent::__construct();
        $this->setupParameters();
        $this->name = 'BookReader';
        $this->defaultProfileName = 'bookReaderDefault';
    }

     /**
     * Install default profile
     *
     * Install default item types, elements, and profiles for easy setup
     *
     * @return void
     */
    public function installDefaults($params = null) {
        $defaultParams = array(
            'typeName' => 'eBook',
            'typeDesc' => 'A digital representation of a bound, paged book, to be displayed using Internet Archive Book Reader. The content of this book may be stored either in Omeka or on the Internet Archive.',
            'profileName' => $this->defaultProfileName,
            'viewerName' => 'BookReader'
        );
        $params = empty($params) ? $defaultParams : $params;
        parent::InstallDefaults($params,$this->_paramInfo);
    }

    /**
     * Set up parameters for this viewer
     *
     * @return void
     */
    public function setupParameters() {
        $this->_paramInfo = array(
            array(
                'name' => 'title',
                'label' => 'Title',
                'description' => 'The title of this book, to be displayed in the BookReader header',
                'type' => 'string',
                //'value' => '',    //for enum type only
                'required' => 'false',
                'default' => ''
            ),
            array(
                'name' => 'url',
                'label' => 'Url',
                'description' => 'The URL associated with the content of this book. If this parameter is not set, Omeka will look for the pages of the book as files attached to this item.',
                'type' => 'string',
                //'value' => '',
                'required' => 'false',
                'default' => ''
            ),
            array(
                'name' => 'width',
                'label' => 'Width',
                'description' => 'The width in pixels of the BookReader panel through which the public views the content of this book.',
                'type' => 'int',
                //'value' => '',
                'required' => 'false',
                'default' => ''
            ),
            array(
                'name' => 'height',
                'label' => 'Height',
                'description' => 'The height in pixels of the BookReader panel through which the public views the content of this book.',
                'type' => 'int',
                //'value' => '',
                'required' => 'false',
                'default' => ''
            )
        );

    }

    /**
     * Queue header scripts
     *
     * Queues script libraries and stylesheets to include in header
     *
     * @return null
     */
    public function viewerHead($params) {
        $liburl = absolute_url('/plugins/MultimediaDisplay/libraries/bookreader/','',array(),true);
        $liburl = str_replace('admin/','',$liburl);

        queue_js_url('http://www.archive.org/bookreader/jquery-ui-1.8.5.custom.min.js');
        queue_js_url('http://www.archive.org/bookreader/dragscrollable.js');
        queue_js_url('http://www.archive.org/bookreader/jquery.colorbox-min.js');
        queue_js_url('http://www.archive.org/bookreader/jquery.ui.ipad.js');
        queue_js_url('http://www.archive.org/bookreader/jquery.bt.min.js');
        queue_js_url($liburl.'BookReader.js');
    }

    /**
     * Retrieve body html
     *
     * Retrieves markup to include in the main content body of item show pages
     *
     * @return string Html to include in the header, 
     * linking to stylesheets and javascript libraries
     */
    public function getBodyHtml($params) {   
        $liburl = absolute_url('plugins/MultimediaDisplay/libraries/bookreader/');
        $liburl = str_replace('admin/','',$liburl);
        ob_start();
?>
        
        <script type="text/javascript">
        jQuery('#bookreader').prependTo(jQuery('#primary'));
        </script>
        <?php
        return ob_get_clean();
    }
}

?>