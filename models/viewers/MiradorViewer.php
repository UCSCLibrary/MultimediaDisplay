<?php
/**
 * Multimedia Display Mirador Viewer
 * 
 * @copyright Copyright 2014 UCSC Library Digital Initiatives
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package MultimediaDisplay
 */

/**
 * Multimedia Display Mirador Viewer class
 * 
 */
class Mmd_Mirador_Viewer extends Mmd_Abstract_Viewer
{
    
    public function __construct() {
        //parent::__construct();
        $this->setupParameters();
        $this->name = 'Mirador';
        $this->defaultProfileName = 'miradorDefault';
    }

     /**
     * Install default profile
     *
     * Install default item types, elements, and profiles for easy setup
     *
     * @return void
     */
    public function installDefaults($params=null) {
        $defaultParams = array(
            'typeName' => 'Mirador Gallery',
            'typeDesc' => 'A collection of images to be displayed using the Mirador viewer',
            'profileName' => $this->defaultProfileName,
            'viewerName' => 'Mirador'
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
                'name' => 'width',
                'label' => 'Width',
                'description' => 'The width in pixels of the Mirador panel through which the public views the content of this book.',
                'type' => 'int',
                //'value' => '',
                'required' => 'false',
                'default' => '500'
            ),
            array(
                'name' => 'height',
                'label' => 'Height',
                'description' => 'The height in pixels of the Mirador panel through which the public views the content of this book.',
                'type' => 'int',
                //'value' => '',
                'required' => 'false',
                'default' => '500'
            ),
            array(
                'name' => 'thumbHeight',
                'label' => 'Thumbnail Height',
                'description' => 'The height in pixels of the thumbnail subpanel to preview other images.',
                'type' => 'int',
                //'value' => '',
                'required' => 'false',
                'default' => '250'
            ),
            array(
                'name' => 'title',
                'label' => 'Title',
                'description' => 'The title of the item displayed in Mirador',
                'type' => 'string',
                //'value' => '',
                'required' => 'false',
                'default' => ''
            ),
            array(
                'name' => 'manifestUri',
                'label' => 'Manifest Url',
                'description' => 'The URL of the Mirador manifest file for this display, if it is not attached to this item',
                'type' => 'string',
                //'value' => '',
                'required' => 'false',
                'default' => ''
            ),
            array(
                'name' => 'colManifestUri',
                'label' => 'Collection Manifest Url',
                'description' => 'The URL of the Mirador manifest file for the collection containing this display, if it is not attached to the collection containing the item',
                'type' => 'string',
                //'value' => '',
                'required' => 'false',
                'default' => ''
            ),
            array(
                'name' => 'colTitle',
                'label' => 'Collection Title',
                'description' => 'The title the collection containing this display, if it is not the collection containing the item',
                'type' => 'string',
                //'value' => '',
                'required' => 'false',
                'default' => ''
            ),
            array(
                'name' => 'location',
                'label' => 'Location',
                'description' => 'The location at which the images reside',
                'type' => 'string',
                //'value' => '',
                'required' => 'false',
                'default' => ''
            ),
            array(
                'name' => 'openAt',
                'label' => 'Page to Open First',
                'description' => 'The filename of the page to open first',
                'type' => 'string',
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
    public function viewerHead() {
        $liburl = absolute_url('/plugins/MultimediaDisplay/libraries/mirador/');
        $liburl = str_replace('admin/','',$liburl);

        queue_js_url($liburl.'mirador.js');

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
        <div id="viewer"></div>
        <script type="text/javascript" src="<?php echo($liburl.'MiradorDeploy.js'); ?>" />
        <?php
        return ob_get_clean();
    }

}

?>