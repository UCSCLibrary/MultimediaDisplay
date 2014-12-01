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
class Mmd_PDF_Viewer extends Mmd_Abstract_Viewer
{

    public function __construct() {
        //parent::__construct();
        $this->setupParameters();
        $this->name = 'PDF';
        $this->defaultProfileName = 'pdfDefault';
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
            'typeName' => 'pdf',
            'typeDesc' => 'A digital resource attached as a Portable Document Format (PDF) file.',
            'profileName' => $this->defaultProfileName,
            'viewerName' => 'PDF'
        );
        $params = empty($params) ? $defaultParams : $params;
        return parent::InstallDefaults($params,$this->_paramInfo);
    }

    /**
     * Set up parameters for this viewer
     *
     * @return void
     */
    public function setupParameters() {
        $this->_paramInfo = array(
            array(
                'name' => 'url',
                'label' => 'Url',
                'description' => 'The URL at which the pdf is located, if it is not attached to the Omeka item.',
                'type' => 'string',
                //'value' => '',
                'required' => 'false',
                'default' => '',
                'files' => 'pdf'
            ),
                array(
                'name' => 'width',
                'label' => 'Width',
                'description' => 'The width in pixels of the panel through which the public views the content of this book.',
                'type' => 'int',
                //'value' => '',
                'required' => 'false',
                'default' => '800'
            ),
            array(
                'name' => 'height',
                'label' => 'Height',
                'description' => 'The height in pixels of the panel through which the public views the content of this book.',
                'type' => 'int',
                //'value' => '',
                'required' => 'false',
                'default' => '600'
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
        $liburl = absolute_url('/plugins/MultimediaDisplay/libraries/pdf/','',array(),true);
        $liburl = str_replace('admin/','',$liburl);

        queue_js_url($liburl.'src/shared/util.js');
        queue_js_url($liburl.'src/display/api.js');
        queue_js_url($liburl.'src/display/metadata.js');
        queue_js_url($liburl.'src/display/canvas.js');
        queue_js_url($liburl.'src/display/webgl.js');
        queue_js_url($liburl.'src/display/pattern_helper.js');
        queue_js_url($liburl.'src/display/font_loader.js');
        queue_js_url($liburl.'src/display/annotation_helper.js');

        queue_js_string('PDFJS.workerSrc = \''.$liburl.'src/worker_loader.js\';');
        queue_js_string('pdfFile = \''.$params['url'][0]['url'].'\';');

        queue_js_url($liburl.'displayPDF.js');
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
?>  
        <div id="pdf-viewer-controls">
        <button id="prev" class="pdf-nav-button"><</button>
        <button id="next" class="pdf-nav-button">></button>
        <span id="page_num_div">
          Page:
        <span id="page_num">1</span>
        /
        <span id="page_count"></span>
        </span>
        <button id="zoom-in" class="pdf-zoom-button">+</button>
        <button id="zoom-out" class="pdf-zoom-button">-</button>
        </div>
        <div id="pdf-container">

        
        <canvas id="pdf-viewer" width="600" height="800"></canvas>
        </div>
        <script type="text/javascript">
        jQuery('#content').find('h1').after(jQuery('#pdf-container'));
        jQuery('#content').find('h1').after(jQuery('#pdf-viewer-controls'));
        </script>
        <style>
        #pdf-container {
          max-height: <?php echo $params['height'];?>px;
          max-width: <?php echo $params['width'];?>px;
          height: <?php echo $params['height'];?>px;
          width: <?php echo $params['width'];?>px;
          overflow: auto;    
          padding-top: 20px;
        }
#pdf-viewer-controls {
    max-width:<?php echo $params['width']?>px;
}
        #pdf-viewer-controls > .pdf-nav-button {
  background: none repeat scroll 0 0 transparent;
    border-radius: 20px;
    font-size: 50pt;
    opacity: 0.5;
    padding: 15px;
       }
        #pdf-viewer-controls > .pdf-zoom-button {
  background: none repeat scroll 0 0 transparent;
    border-radius: 10px;
    font-size: 30pt;
    opacity: 0.5;
    padding: 5px;
float:right;
       }
        #pdf-viewer-controls > button {
  background: none repeat scroll 0 0 transparent;
    border: 1px solid black;
  color: black !important;
    position: relative;
       }

#prev {
    left: 20px;
  top: <?php echo($params['height'] / 2 - 20);?>px;

}
        #next {
    left: <?php echo($params['width'] - 160);?>px;
    top: <?php echo($params['height'] / 2 - 20);?>px;
       }
        </style>
        <?php
        return true;
    }
}

?>