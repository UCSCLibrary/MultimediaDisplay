<?php
/**
 * Multimedia Display Ohms Viewer
 * 
 * @copyright Copyright 2014 UCSC Library Digital Initiatives
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package MultimediaDisplay
 */

/**
 * Multimedia Display Ohms Viewer class
 * 
 */
class Mmd_Ohms_Viewer extends Mmd_Abstract_Viewer
{
    
    public function __construct() {
        //parent::__construct();
        $this->setupParameters();
        $this->name = 'Ohms';
        $this->defaultProfileName = 'ohmsViewerDefault';
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
            'typeName' => 'Synchonized Oral History',
            'typeDesc' => 'Oral history object synchronized with text, to be displayed using the OHMS viewer.',
            'profileName' => $this->defaultProfileName,
            'viewerName' => 'Ohms'
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

        $liburl = absolute_url('/plugins/MultimediaDisplay/libraries/ohmsviewer/');
        $liburl = str_replace('admin/','',$liburl);

//        queue_css_file($config[$cacheFile->repository]['css']);

        queue_css_url($liburl.'viewer.css');
        queue_css_url($liburl.'jquery-ui.toggleSwitch.css');
        queue_css_url($liburl.'jquery-ui-1.8.16.custom.css');
        queue_css_url($liburl.'font-awesome.css');

        queue_css_url($liburl.'jquery.fancybox.css');
        queue_css_url($liburl.'jquery.fancybox-buttons.css');
        queue_css_url($liburl.'jquery.fancybox-thumbs.css');

        queue_js_url('//ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js');
        queue_js_url($liburl.'jquery-ui.toggleSwitch.js');
        queue_js_url($liburl.'flowplayer.min.js');
        queue_js_url($liburl.'flowplayer.ipad.min.js');
        queue_js_url($liburl.'jquery.easing.1.3.js');
        queue_js_url($liburl.'jquery.scrollTo-min.js');
//        queue_js_url($liburl.'viewer_'.$cacheFile->viewerjs,$libDir.'js/');
        queue_js_url($liburl.'fancybox_2_1_5/source/jquery.fancybox.pack.js');
        queue_js_url($liburl.'jquery.fancybox-buttons.js');
        queue_js_url($liburl.'jquery.fancybox-media.js');
        queue_js_url($liburl.'jquery.fancybox-thumbs.js');
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
        require_once 'lib/CacheFile.class.php';
        $cacheFile = CacheFile::getInstance($params['cacheFileName'],$params['tmpDir'],$params);
        
        ob_start();
        ?>
        <div id="audio-panel">
        <?php include_once 'tmpl/player_'.$cacheFile->playername.'.tmpl.php'; ?>
        </div>
        <div id="main">
          <div id="main-panels">
            <div id="content-panel">
              <div id="transcript-panel">
                <?php echo $cacheFile->transcript; ?>
              </div>
              <div id="index-panel">
                <?php echo $cacheFile->index; ?>
              </div>
            </div>
            <div id="searchbox-panel">
              <?php include_once 'tmpl/search.tmpl.php'; ?></div>
            </div>
          </div>
        </div>
              
        <div style="clear:both; color:white; margin-top:30px;text-align:left;">
          <p>
<?php
              if($cacheFile->rights) {
                 echo '<span><h3>Rights Statement:</h3>';
                 echo $cacheFile->rights;
                 echo '</span>';
              }
?>
          </p>
          <p>
<?php
            if($cacheFile->usage) {
              echo '<span><h3>Usage Statement:</h3>';
              echo $cacheFile->usage;
              echo '</span>';
             } 
?>
          </p>
        </div>
            <script type="text/javascript">
            jQuery(document).ready(function() {
        jQuery(".fancybox").fancybox();
        jQuery(".various").fancybox({
            maxWidth : width,
            maxHeight : height,
            fitToView : false,
            width : '70%',
            height : '70%',
            autoSize : false,
            closeClick : false,
            openEffect : 'none',
            closeEffect : 'none'
            });
        jQuery('.fancybox-media').fancybox({
            openEffect : 'none',
            closeEffect : 'none',
            width : '80%',
            height : '80%',
            fitToView : true,
            helpers : {
      media : {}
    }
    });
        jQuery(".fancybox-button").fancybox({
      prevEffect : 'none',
            nextEffect : 'none',
            closeBtn : false,
            helpers : {
      title : { type : 'inside' },
            buttons : {}
    }
    });
    });
            var cachefile = '<?php echo $cacheFile->cachefile; ?>';
      </script>
<?php
        return ob_get_clean();
    }
}
?>