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
                'description' => 'The width of the panel through which the public views the content of this book. Accepts syntax "250px", "250", or "70%". If an integer is given, it is interpreted as a number of pixels.',
                'type' => 'css',
                //'value' => '',
                'required' => 'false',
                'default' => '800'
            ),
            array(
                'name' => 'height',
                'label' => 'Height',
                'description' => 'The height of the panel through which the public views the content of this book. Accepts syntax "250px", "250", or "70%". If an integer is given, it is interpreted as a number of pixels.',
                'type' => 'css',
                //'value' => '',
                'required' => 'false',
                'default' => '600'
            )
        );
    }

    private function _filterCssParams($params,$indices) {
        foreach($indices as $index) {
            if(is_numeric($params[$index])) {
                $params[$index] = $params[$index].'px';
            }
        }
        return $params;
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
        $params = $this->_filterCssParams($params,array('width','height'));
?>  

        <div id="pdf-container">

        <button id="prev" class="pdf-nav-button"><</button>
        <button id="next" class="pdf-nav-button">></button>

        <div id="pdf-viewer-controls">

        <span id="page_num_div_left" class="page_num_div">
        <span id="page_num_left">1</span>
        /
        <span class="page_count" id="page_count_left"></span>
        </span>

        <span id="page_num_div_right" class="page_num_div">
        <span id="page_num_right">1</span>
        /
        <span class="page_count" id="page_count_right"></span>
        </span>

        <button id="zoom-in" class="pdf-zoom-button">+</button>
        <button id="zoom-out" class="pdf-zoom-button">-</button>
        </div>

        <div id="pdf-pages-container">
	        <div id="pdf-container-left" class="pdf-page-container">
                   <canvas id="pdf-viewer-left" width="<?php echo $params['halfwidth'];?>" height="<?php echo $params['height'];?>"></canvas>
	        </div>
                <div id="pdf-container-right" class="pdf-page-container">
                    <canvas id="pdf-viewer-right" width="<?php echo $params['halfwidth'];?>" height="<?php echo $params['height'];?>"></canvas>
	        </div>
        </div>

        </div>

        <script type="text/javascript">
        jQuery('#content').find('h1').after(jQuery('#pdf-container'));
        //jQuery('#pdf-container').prepend();
        //jQuery('#content').find('h1').after(jQuery('#pdf-viewer-controls'));
	jQuery(document).ready(function() {
	    jQuery('#itemfiles').hide();
	  });
        </script>
        <style>
        .page_num_div {
          bottom: 4%;
          position: absolute;
            font-size:1.1em;
         }
        #page_num_div_left {
           left:25%;
        }
        #page_num_div_right {
            left:75%;
        }

//#pdf-container-left {
//border-right:1px solid black;
//}

        #pdf-container {
          position:relative;
          max-height: <?php echo $params['height'];?>;
          max-width: <?php echo $params['width'];?>;
          height: <?php echo $params['height'];?>;
          width: <?php echo $params['width'];?>;    
          padding-bottom: 20px !important;
        }
        #pdf-pages-container {
          height: 100%;
          width: 100%;
    
          overflow: auto;    
          padding-top: 20px;
        }
        .pdf-page-container {
        float:left;
          height: 100%;
          width: 50%;
          overflow: auto;    
        }
#pdf-viewer-controls {
    //max-width:<?php echo $params['width']?>;
  width:100%;
  height:0%
}


#pdf-container > .pdf-nav-button {
  background: none repeat scroll 0 0 transparent;
    border-radius: 20px;
    font-size: 50pt;
    opacity: 0.5;
    padding: 15px;    
    height: auto;
    margin: 0;
    padding: 0 15px;
position: absolute;
top: 40%;
    border: 1px solid black;
  color: black !important;
    position: absolute;
       }
        #pdf-viewer-controls > .pdf-zoom-button {
  background: none repeat scroll 0 0 transparent;
    border-radius: 10px;
    font-size: 30pt;
    opacity: 0.5;
    padding: 5px;    
    height: auto;
    line-height: 0.8em;
    opacity: 0.5;
    padding: 0 7px;
    right: -82%;
       }
        #pdf-viewer-controls > button {
  background: none repeat scroll 0 0 transparent;
    border: 1px solid black;
  color: black !important;
    position: absolute;
       }
       #pdf-viewer-controls > #zoom-in {
right:3%;
}
       #pdf-viewer-controls > #zoom-out {
right: 8%;
 }

#prev {
    left: 3%;

}
        #next {
          right:3%;
       }
        </style>
        <?php
        return;
    }
}

?>