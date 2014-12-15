<?php
/**
 * Multimedia Display PanZoom Viewer
 * 
 * @copyright Copyright 2014 UCSC Library Digital Initiatives
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package MultimediaDisplay
 */

/**
 * Multimedia Display PanZoom Viewer class
 * 
 */
class Mmd_PanZoom_Viewer extends Mmd_Abstract_Viewer
{

    public function __construct() {
        //parent::__construct();
        $this->setupParameters();
        $this->name = 'PanZoom';
        $this->defaultProfileName = 'panZoomDefault';
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
            'typeName' => 'Zoomable Image',
            'typeDesc' => 'An item with one or more image attachments which should be available will zoom capabilities',
            'profileName' => $this->defaultProfileName,
            'viewerName' => 'PanZoom'
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
                'name' => 'image',
                'label' => 'Image Location',
                'description' => 'The url of the image to zoom into.',
                'type' => 'string',
                //'value' => '',
                'required' => 'true',
                'default' => '',
                'files' => 'jpg,png,bmp,gif,tif'
            ),
            array(
                'name' => 'width',
                'label' => 'Width',
                'description' => 'Width of the media display. Accepts syntax "250px", "250", or "70%". If an integer is given, it is interpreted as a number of pixels.',
                'type' => 'css',
                //'value' => '',    //for enum type only
                'required' => 'false',
                'default' => '400'
            ),
            array(
                'name' => 'height',
                'label' => 'Height',
                'description' => 'Height of the media display. Accepts syntax "250px", "250", or "70%". If an integer is given, it is interpreted as a number of pixels.',
                'type' => 'css',
                //'value' => '',    //for enum type only
                'required' => 'false',
                'default' => '300'
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
        $libUrl = absolute_url('plugins/MultimediaDisplay/libraries/panzoom/');
        $libUrl = str_replace('admin/','',$libUrl);

        queue_js_url($libUrl.'panzoom.js');
//        queue_css_url($libUrl.'mediaelementplayer.css');
    }

    private function _filterCssParams($params,$indices) {
        foreach($indices as $index) {
            if(is_numeric($params[$index]))
                $params[$index] = $params[$index].'px';
        }
        return $params;
    }

    /**
     * Retrieve body html
     *
     * Retrieves markup to include in the main content body of item show pages
     *
     * @return string Html to include in the header, 
     * linking to stylesheets and javascript libraries
     */
    public function getBodyHtml($params) 
    {
        $params = $this->_filterCssParams($params,array('width','height'));
      // print_r($params);
      //      die('pp');
         if(empty($params['image'])) {
            throw new Exception('Item cannot be displayed. No image location specified for PanZoom Viewer.');
            return;
        }
?>
        <div class="panzoom-elements element">
         <div class="panzoom-container">
<?php
         if(is_array($params['image'])) {
             foreach($params['image'] as $image) {
                 echo '<img src="'.$image['url'].'" class="panzoom-image"/>';
             }
         } else {
             echo '<img src="'.$params['image'].'" class="panzoom-image" />';
         }
?>
         </div>
         <div class="buttons">
         <button class="zoom-in">Zoom In</button>
         <button class="zoom-out">Zoom Out</button>
         <input class="zoom-range" type="range" step="0.05" min="0.1" max="5">
         <button class="reset">Reset</button>
         </div>
        </div>

	    <style>
	    .panzoom-container {
	    width:<?php echo $params['width'];?>;
    height:<?php echo $params['height']; ?>;
	    }
</style>


        <script>
    function resetPanzoom() {
	   var canvas = jQuery('.panzoom-image');
	 var container = canvas.parent();
	 var image_height = canvas.height();
	 var image_width = canvas.width();
	 var container_height = container.height();
	 var container_width = container.width();
	 var image_center_left = image_width / 2;
	 var image_center_top = image_height / 2;
	 var zoom_factor;

	 if(image_height > image_width)
	   zoom_factor = container_height / image_height;
        else
	  zoom_factor = container_width / image_width;
	 	 
	 //Calculate new image dimensions after zoom
	 image_width = image_width * zoom_factor;
	 image_height = image_height * zoom_factor;

	 var image_offset_left = image_center_left - (image_width / 2.0);
	 var image_offset_top = image_center_top - (image_height / 2.0);

	 //Calculate desired offset for image
	 var new_offset_left = (container_width - image_width) / 2.0;
	 var new_offset_top = (container_height - image_height) / 2.0;

	 //Pan to set desired offset for image
	 var pan_left = new_offset_left - image_offset_left;
	 var pan_top = new_offset_top - image_offset_top;
	 $panzoom.panzoom("pan", pan_left, pan_top);
	 $panzoom.panzoom("zoom",zoom_factor,{animate: false });

	 }

	 jQuery('#itemfiles').hide();
	 jQuery('#content').find('h1').after(jQuery('.panzoom-elements'));

	 $panzoom = jQuery('.panzoom-image').panzoom({
	   "minScale": 0.05,
	     $zoomIn: jQuery(".zoom-in"),
	     $zoomOut: jQuery(".zoom-out"),
	     $zoomRange: jQuery(".zoom-range"),
	     //$reset: jQuery(".reset")
	  });
	 resetPanzoom();
	 jQuery(".reset").click(resetPanzoom);
        </script>
<?php
        return ;
    }
}

?>