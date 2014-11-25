<?php
/**
 * Multimedia Display
 *
 * This Omeka 2.0+ plugin 
 * 
 *
 * @copyright Copyright 2014 UCSC Library Digital Initiatives
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 *
 * @package MultimediaDisplay
 */

define('MMD_PLUGIN_DIR', dirname(__FILE__));
define('MMD_HELPERS_DIR', MMD_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'helpers');
define('MMD_FORMS_DIR', MMD_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'forms');
require_once MMD_HELPERS_DIR . DIRECTORY_SEPARATOR . 'ThemeHelpers.php';

/**
 * Multimedia Display plugin class
 */
class MultimediaDisplayPlugin extends Omeka_Plugin_AbstractPlugin
{
    /**
     * @var array Options for the plugin.
     */
    protected $_options = array(
        'mmd_supported_viewers'=>''
    );

    /**
     * @var array Hooks for the plugin.
     */
    protected $_hooks = array(
        'install',
        'uninstall',
        'define_acl',
        'admin_items_show',
        'public_items_show',
        'admin_head',
        'public_head'
    );
  
    /**
     * @var array Filters for the plugin.
     */
    protected $_filters = array('admin_navigation_main');

    /**
     * Define the plugin's access control list.
     *
     *@param array $args Parameters supplied by the hook
     *@return void
     */
    public function hookDefineAcl($args)
    {
        $args['acl']->addResource('MultimediaDisplay_Index');
    }

    /**
     * Load the plugin javascript & css when admin section loads
     *
     *@return void
     */
    public function hookAdminHead()
    {
        queue_js_file('MmdAdminScripts');
        queue_css_file('MultimediaDisplay');

        //$this->_applyAssignments();
    }

    /**
     * Load the plugin javascript & css when admin section loads
     *
     *@return void
     */
    public function hookPublicHead()
    {
        queue_css_file('MultimediaDisplay');
        $this->_applyAssignments();
    }

    private function _applyAssignments() {
        try{
            $item = get_current_record('Item');
        }catch(Exception $e) {
            if(empty($item))
                return;
        }
        $profiles = $this->_db->getTable('MmdProfile')->getAssignedProfiles($item);
        if(count($profiles)==0)
            return;
        foreach($profiles as $profile) 
            $profile->executeViewerHead();
    }

    /**
     * Add the Multimedia Display link to the admin main navigation.
     * 
     * @param array $nav Navigation array.
     * @return array $filteredNav Filtered navigation array.
     */
    public function filterAdminNavigationMain($nav)
    {
        $nav[] = array(
            'label' => __('Media Display'),
            'uri' => url('multimedia-display'),
            'resource' => 'MultimediaDisplay_Index',
            'privilege' => 'index'
        );
        return $nav;
    }

    /**
     * When the plugin installs, create the database tables 
     * 
     * @return void
     */
    public function hookInstall()
    {

        $db = $this->_db;
        try{
            $sql = "
            CREATE TABLE IF NOT EXISTS `$db->MmdAssign` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `profile_id` int(10) unsigned NOT NULL,
                `item_type_id` int(10) unsigned,
                `collection_id` int(10) unsigned,
                `default` bool default true,
                `filetypes` text,
                PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
            $this->_db->query($sql);

            $sql = "
            CREATE TABLE IF NOT EXISTS `$db->MmdProfile` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `name` text NOT NULL,
                `viewer` text NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
            $this->_db->query($sql);

            $sql = "
            CREATE TABLE IF NOT EXISTS `".$db->prefix."MmdProfileAux` (
                `profile_id` int(10) unsigned NOT NULL,
                `option` text NOT NULL,
                `value` text NOT NULL,
                `static` tinyint NOT NULL,
                `multiple` tinyint DEFAULT NULL
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
            $this->_db->query($sql);
        }catch(Exception $e) {
            throw $e; 
        }

        $this->_installOptions();
        //$viewerDir = dirname(__FILE__).'/models/viewers/';
        //require_once($viewerDir.'AbstractViewer.php');

        $viewers = array(
            //'Mirador'=>'Mirador',
            'Ohms'=>'OHMS Viewer',
            'MediaElement'=>'MediaElement.js',
            'BookReader'=>'Internet Archive Book Reader',
            'PanZoom'=>'PanZoom Image Zooming',
            'OpenSeaDragon'=>'OpenSeaDragon Jpeg2000 Viewer',
            //'Kaltura'=>'Kaltura',
//            'Youtube'=>'Youtube'
        );
        set_option('mmd_supported_viewers',serialize($viewers));
/*
        foreach($viewers as $viewerName => $viewerDisplayName) {
            $viewerFileName = $viewerName.'Viewer.php';
            $viewerClassName = 'Mmd_'.$viewerName.'_Viewer';
            require_once($viewerDir.$viewerFileName);
            $viewer = new $viewerClassName();
            $viewer->installDefaults();
       } 
*/

        //todo - create a temp directory in files/

    }

    /**
     * When the plugin uninstalls, delete the database tables 
     *which store the logs
     * 
     * @return void
     */
    public function hookUninstall()
    {
        $this->_uninstallOptions();
      try{
	$db = get_db();
	$sql = "DROP TABLE IF EXISTS `$db->MmdAssign`; ";
	$db->query($sql);
	$sql = "DROP TABLE IF EXISTS `$db->MmdProfile`; ";
	$db->query($sql);
	$sql = "DROP TABLE IF EXISTS `".$db->prefix."MmdProfileAux`; ";
	$db->query($sql);
      }catch(Exception $e) {
	throw $e;	
      }

      //TODO delete any temp directories

    }

    /**
     * Add viewer markup to public item display pages
     * 
     * @param array $args An array of parameters passed by the hook
     * @return void
     */
    public function hookPublicItemsShow($args)
    {
        $item = get_current_record('Item');
        if(empty($item))
            break;
        $profile = $this->_db->getTable('MmdProfile')->getPrimaryAssignedProfile($item);
        if(empty($profile))
            return;
        try{
            echo $profile->getBodyHtml();
        } catch (Exception $e) {
            echo "<h3>Error loading viewer</h3><p>".$e->getMessage()."</p>";
        }
    }


    /**
     * Add viewer markup to admin item display pages
     * 
     * @param array $args An array of parameters passed by the hook
     * @return void
     */
    public function hookAdminItemsShow($args)
    {
       
    }

}