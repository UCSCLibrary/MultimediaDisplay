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
                'description' => 'The URL associated with the content of this book. If this parameter is not set, Omeka will look for the pages of the book as files attached to this item.',
                'type' => 'string',
                //'value' => '',
                'required' => 'false',
                'default' => '',
                'files' => 'bmp,jpg,gif,png'
            ),
            array(
                'name' => 'width',
                'label' => 'Width',
                'description' => 'The width in pixels of the BookReader panel through which the public views the content of this book.',
                'type' => 'int',
                //'value' => '',
                'required' => 'false',
                'default' => '800'
            ),
            array(
                'name' => 'height',
                'label' => 'Height',
                'description' => 'The height in pixels of the BookReader panel through which the public views the content of this book.',
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
        if(is_array($params['url'])) {           
            $liburl = absolute_url('/plugins/MultimediaDisplay/libraries/bookreader/','',array(),true);
            $liburl = str_replace('admin/','',$liburl);

            queue_js_url('http://www.archive.org/bookreader/jquery-ui-1.8.5.custom.min.js');
            queue_js_url('http://www.archive.org/bookreader/dragscrollable.js');
            queue_js_url('http://www.archive.org/bookreader/jquery.colorbox-min.js');
            queue_js_url('http://www.archive.org/bookreader/jquery.ui.ipad.js');
            queue_js_url('http://www.archive.org/bookreader/jquery.bt.min.js');
            queue_js_url($liburl.'BookReader.js');
        }
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
        if(!is_array($params['url'])){
            $split = explode('archive.org/stream/',$params['url']);
            if(count($split)>1)
                $url = $split[1];
            $split = explode('#',$url);
            if(count($split)>1)
                $url = $split[0];
            
            ?>
            <div id="bookreader-div">
            <iframe src="http://www.archive.org/stream/<?php echo $url;?>#mode/2up?ui=embed" width="<?php echo $params['width'];?>" height="<?php echo $params['height'];?>"></iframe>
            <?php 
            //'.$params[''].'
            ?></div>
            <?php
            //return;
        } else {
?>
        
        <div id="BookReader"></div>
        <script type="text/javascript">
        jQuery('#bookreader').prependTo(jQuery('#content'));
        br = new BookReader();
        bookreader("bookreader", ArchiveBook("tomsawyer"));

        br.getPageWidth = function(index) {
            return <?php echo $params['width'];?>;
        }
        br.getPageHeight = function(index) {
            return <?php echo $params['width'];?>;
        }
        br.bookTitle= '';
        br.bookUrl  = 'http://openlibrary.org';
        br.init();

        </script>
<?php  }  ?>
        
        <script type="text/javascript">
        jQuery('#content').find('h1').after(jQuery('#bookreader-div'));
        </script>

        <?php
        return true;
    }
}

?>