<?php
/**
 * Multimedia Display MediaElement Viewer
 * 
 * @copyright Copyright 2014 UCSC Library Digital Initiatives
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package MultimediaDisplay
 */

/**
 * Multimedia Display MediaElement Viewer class
 * 
 */
class Mmd_MediaElement_Viewer extends Mmd_Abstract_Viewer
{

    public function __construct() {
        //parent::__construct();
        $this->setupParameters();
        $this->name = 'MediaElement';
        $this->defaultProfileName = 'mediaElementDefault';
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
            'typeName' => 'Streaming Media',
            'typeDesc' => 'A video or audio file to be streamed from a standard streaming server',
            'profileName' => $this->defaultProfileName,
            'viewerName' => 'MediaElement'
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
                'name' => 'url',
                'label' => 'URL',
                'description' => 'URL of streaming media file',
                'type' => 'string',
                //'value' => '',    //for enum type only
                'required' => 'true',
                'default' => '',
                'files' => 'xml'
            ),
            array(
                'name' => 'width',
                'label' => 'Width',
                'description' => 'Width of the media display',
                'type' => 'int',
                //'value' => '',    //for enum type only
                'required' => 'false',
                'default' => '400'
            ),
            array(
                'name' => 'height',
                'label' => 'Height',
                'description' => 'Height of the media display',
                'type' => 'int',
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
        $libUrl = absolute_url('plugins/MultimediaDisplay/libraries/mediaelement/build/');
        $libUrl = str_replace('admin/','',$libUrl);

        queue_js_url($libUrl.'mediaelement-and-player.min.js');
        queue_css_url($libUrl.'mediaelementplayer.css');
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
        $params['url'] = isset($params['url'][0]) ? $params['url'][0] : $params['url'];
        $params['url'] = isset($params['url']['url']) ? $params['url']['url'] : $params['url'];

        $params['url'] = strpos($params['url'],'.mp4') ? str_replace('.mp4','',$params['url']) : $params['url'];
        ob_start();
?>
        <video width="<?php echo($params['width']);?>" height="<?php echo($params['height']);?>" poster="<?php //echo($params['posterFilename']);?>" controls="controls" preload="none">
           <!-- MP4 for Safari, IE9, iPhone, iPad, Android, and Windows Phone 7 -->
           <source type="video/mp4" src="<?php echo($params['url']);?>.mp4" />
           <!-- WebM/VP8 for Firefox4, Opera, and Chrome -->
           <source type="video/webm" src="<?php echo($params['url']);?>.webm" />
           <!-- Ogg/Vorbis for older Firefox and Opera versions -->
           <source type="video/ogg" src="<?php echo($params['url']);?>.ogv" />
<?php /*
           <!-- Optional: Add subtitles for each language -->
           <track kind="subtitles" src="subtitles.srt" srclang="en" />
           <!-- Optional: Add chapters -->
           <track kind="chapters" src="chapters.srt" srclang="en" />

      */?>
           <!-- Flash fallback for non-HTML5 browsers without JavaScript -->
           <object width="<?php echo($params['width']);?>" height="<?php echo($params['height']);?>" type="application/x-shockwave-flash" data="flashmediaelement.swf">
              <param name="movie" value="flashmediaelement.swf" />
              <param name="flashvars" value="controls=true&file=<?php echo($params['url'])?>.mp4" />
              <!-- Image as a last resort -->
              <img src="<?php //echo($params['posterFilename']);?>" width="<?php echo($params['width']);?>" height="<?php echo($params['height']);?>" title="No video playback capabilities" />
           </object>
        </video>
        <script>
           jQuery('video,audio').prependTo(jQuery('#primary'));
           jQuery('video,audio').mediaelementplayer(/* Options */);
        </script>
<?php
        return ob_get_clean();
    }
}

?>