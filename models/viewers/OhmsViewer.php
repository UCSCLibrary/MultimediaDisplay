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
        return parent::InstallDefaults($params,$this->_paramInfo);

        //TODO - create temp dir within files directory
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
                'description' => 'The width in pixels of the Ohms Viewer panel through which the public views the content of this book.',
                'type' => 'int',
                //'value' => '',
                'required' => 'false',
                'default' => '500'
            ),
            array(
                'name' => 'height',
                'label' => 'Height',
                'description' => 'The height in pixels of the Ohms Viewer panel through which the public views the content of this book.',
                'type' => 'int',
                //'value' => '',
                'required' => 'false',
                'default' => '500'
            ),
            array(
                'name' => 'cacheFileName',
                'label' => 'Cache File Name',
                'description' => 'The name of the cache file to load.',
                'type' => 'string',
                //'value' => '',
                'required' => 'true',
                'default' => '',
                'files' => 'xml'
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
        $libDir = dirname(dirname(dirname(__FILE__))).'/libraries/ohmsviewer/';

        $config = parse_ini_file($libDir."config/config.ini",true);
        //print_r($config);
        //die();

        if(empty($params['cacheFileName'])) {
            throw new Exception('Item cannot be displayed. No cache file specified for Ohms Viewer.');
            return;
        }

        //todo - do we need paths in addition to urls? probably not.
        $cachefile = is_array($params['cacheFileName']) ? $params['cacheFileName'][0] : $params['cacheFileName'];
        require_once $libDir.'lib/CacheFile.class.php';

        $liburl = absolute_url('/plugins/MultimediaDisplay/libraries/ohmsviewer/');
        $liburl = str_replace('admin/','',$liburl);

        $cssurl = $liburl.'css/';
        $jsurl = $liburl.'js/';

        queue_css_url($cssurl.$config['css']);

        queue_css_url($cssurl.'viewer.css');
        queue_css_url($cssurl.'jquery-ui.toggleSwitch.css');
        queue_css_url($cssurl.'jquery-ui-1.8.16.custom.css');
        queue_css_url($cssurl.'font-awesome.css');

        queue_css_url($cssurl.'jquery.fancybox.css');
        queue_css_url($cssurl.'jquery.fancybox-buttons.css');
        queue_css_url($cssurl.'jquery.fancybox-thumbs.css');

        queue_js_url('//ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js');
        queue_js_url($jsurl.'jquery-ui.toggleSwitch.js');
        queue_js_url($jsurl.'viewer_flowplayer.js');
        //queue_js_url($jsurl.'flowplayer.min.js');
        //queue_js_url($jsurl.'flowplayer.ipad.min.js');
        queue_js_url($jsurl.'jquery.easing.1.3.js');
        queue_js_url($jsurl.'jquery.scrollTo-min.js');
//        queue_js_url($liburl.'viewer_'.$cacheFile->viewerjs,$libDir.'js/');
        queue_js_url($jsurl.'fancybox_2_1_5/source/jquery.fancybox.pack.js');
        queue_js_url($jsurl.'fancybox_2_1_5/source/helpers/jquery.fancybox-buttons.js');
        queue_js_url($jsurl.'fancybox_2_1_5/source/helpers/jquery.fancybox-media.js');
        queue_js_url($jsurl.'fancybox_2_1_5/source/helpers/jquery.fancybox-thumbs.js');
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
        if(empty($params['cacheFileName'])) {
            throw new Exception('Item cannot be displayed. No cache file specified for Ohms Viewer.');
            return;
        }

        $libDir = dirname(dirname(dirname(__FILE__))).'/libraries/ohmsviewer/';
        $config = parse_ini_file($libDir."config/config.ini",true);

        //todo - do we need paths in addition to urls? probably not.
        $cachefile = is_array($params['cacheFileName']) ? $params['cacheFileName'][0] : $params['cacheFileName'];
        require_once dirname(dirname(dirname(__FILE__))).'/libraries/ohmsviewer/lib/CacheFile.class.php';

        //
        $cacheFile = CacheFile::getInstance($cachefile,'/var/www/html/omeka/files/',$config);
        //dirname(dirname(dirname(dirname(__FILE__)))).'/files'


        ob_start();
        ?>
        <div id="audio-panel">
        <?php include_once dirname(dirname(dirname(__FILE__))).'/libraries/ohmsviewer/tmpl/player_'.$cacheFile->playername.'.tmpl.php'; ?>
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
              <?php include_once dirname(dirname(dirname(__FILE__))).'/libraries/ohmsviewer/tmpl/search.tmpl.php'; ?></div>
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

                jQuery('a.indexSegmentLink').on('click', function(e) {
                    var linkContainer = '#segmentLink' + jQuery(e.target).data('timestamp');

                    e.preventDefault();
                    if(jQuery(linkContainer).css("display") == "none")
                        {
                            jQuery(linkContainer).fadeIn(1000);
                        }
                    else
                        {
                            jQuery(linkContainer).fadeOut();
                        }
				
                    return false;
                });
		   
                jQuery('.segmentLinkTextBox').on('click', function() {
                    jQuery(this).select();
                });
			
                if(jumpToTime !== null)
                    {
                        jQuery('div.point').each(function(index) {
                            if(parseInt(jQuery(this).find('a.indexJumpLink').data('timestamp')) == jumpToTime)
                                {
                                    jumpLink = jQuery(this).find('a.indexJumpLink');
                                    jQuery('#accordionHolder').accordion({active: index});
                                    var interval = setInterval(function() {
						
                                        if(Math.floor(jQuery('#subjectPlayer').data('jPlayer').status.currentTime) == jumpToTime)  {
                                            clearInterval(interval);
                                        }
                                        else
                                            {
                                                jumpLink.click();
                                            }
                                    }, 500);
                                    jQuery(this).find('a.indexJumpLink').click();
                                }
                        });
                    }
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