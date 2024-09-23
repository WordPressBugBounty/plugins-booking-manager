<?php
/**
 * @version 1.0
 * @package Booking Manager 
 * @subpackage Core
 * @category Items
 * 
 * @author wpdevelop
 * @link https://oplugins.com/
 * @email info@oplugins.com
 *
 * @modified 2014.07.29
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


if ( ! class_exists( 'WPBM_Init' ) ) :

    
// General Init Class    
final class WPBM_Init {
        
    static private $instance = NULL;
    public $admin_menu;					// Define Menu items
    public $js;							// JS  to load
    public $css;						// CSS to load	
    

/** Get Single Instance of this Class and Init Plugin */
public static function init() {
    
    if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WPBM_Init ) ) {
		
        self::$instance = new WPBM_Init;
        self::$instance->constants();
        self::$instance->includes();
        self::$instance->define_version();


        if ( class_exists( 'WPBM_ItemInstall' ) ) {                                 // Check if we need to run Install / Uninstal process.
            new WPBM_ItemInstall();
        }
        
		// Currently  transaltion  is loading from  hooks from  this file ../core/wpbm-translation.php
        // add_action('plugins_loaded', array(self::$instance, 'load_textdomain') );   // T r a n s l a t i o n			// TODO: Finish here
                
        $is_continue = self::$instance->start();                                // Make Ajax, Response or Define item ClASS

        if ( $is_continue ) {                                                   // Possible Load Admin or Front-End page
            
            self::$instance->js     = new WPBM_JS;
            self::$instance->css    = new WPBM_CSS;

            if( is_admin() ) {
                
                add_action( '_admin_menu',   array( self::$instance, 'define_admin_menu') );    // Define Menu  -  _admin_menu - Fires before the administration menu loads in the admin.
                add_action( 'admin_footer', 'wpbm_print_js', 50 );								// Load my Queued JavaScript Code at  the footer of the Admin Panel page. Executed in ALL Admin Menu Pages
                
            } else {  
                
                add_action( 'wp_enqueue_scripts', array(self::$instance->css, 'load'), 1000000001 );   // Load CSS at front-end side  // Enqueue Scripts to All Client pages 
                add_action( 'wp_enqueue_scripts', array(self::$instance->js,  'load'), 1000000001 );   // Load JavaScript files and define JS varibales at forn-end side
                add_action( 'wp_footer', 'wpbm_print_js', 50 );                 // Load my Queued JavaScript Code at  the footer  of the page, if executed "wp_footer" hook at the Theme.
            }            
        }
                
    }
    return self::$instance;        
}


/** Define Admin Menu items */
public function define_admin_menu(){
    
    $update_count = wpbm_get_number_new_items();

    $title = 'oPlgns Panel';				//'&#223;<span style="font-size:0.75em;">&#920;&#920;</span>&kgreen;&imath;&eng;';   
$title = 'oPlugins Panel';
	if ( $update_count > 0 ){
        $update_count_title = "<span class='update-plugins count-$update_count' title=''><span class='update-count bk-update-count'>" . number_format_i18n($update_count) . "</span></span>" ;
        $title .= $update_count_title;
    }
    
    
    //global $menu;
    //if ( current_user_can(  ) ) {
    //$menu[] = array( '', 'read', 'separator-wpbm', '', 'wp-menu-separator wpbm' );
    //}
    // debuge($menu); 
    
    $wpbm_menu_position = get_wpbm_option( 'wpbm_menu_position' );
    switch ( $wpbm_menu_position ) {
        case 'top':
            $wpbm_menu_position = "3.15";
            break;
        case 'middle':    
            global $_wp_last_object_menu;                                       // The index of the last top-level menu in the object menu group
            $_wp_last_object_menu++;
            $wpbm_menu_position = $_wp_last_object_menu; // 58.9;
            break;
        case 'bottom':
            $wpbm_menu_position = "99.919";
            break;
        default:
            $wpbm_menu_position = "3.15";
            break;
    }
    
    
    self::$instance->admin_menu['master'] = new WPBM_Admin_Menus( 
                                                    'oplugins' , array (
                                                    'in_menu' => 'root'                                                               
                                                  , 'mune_icon_url' => '/assets/img/icon-16x16.png'      
                                                  , 'menu_title' => $title
                                                  , 'menu_title_second' => __('Manage', 'booking-manager') . ' .ics'
                                                  , 'page_header' => '...'
                                                  , 'browser_header' =>  __('Manage Data', 'booking-manager') 
                                                  , 'user_role' => get_wpbm_option( 'wpbm_user_role_master' )
                                                  , 'position' => $wpbm_menu_position // 3.3 - top           //( 58.9 )  // - middle
                                                                                /*  
                                                                                (Optional). Positions for Core Menu Items
                                                                                    2 Dashboard
                                                                                    4 Separator
                                                                                    5 Posts
                                                                                    10 Media
                                                                                    15 Links
                                                                                    20 Pages
                                                                                    25 Comments
                                                                                    59 Separator
                                                                                    60 Appearance
                                                                                    65 Plugins
                                                                                    70 Users
                                                                                    75 Tools
                                                                                    80 Settings
                                                                                    99 Separator
                                                                                     */
                                                                            )
                                                  
                                                );
if (0)   
    self::$instance->admin_menu['new']    = new WPBM_Admin_Menus( 
                                                    'wpbm-new' , array (
                                                    'in_menu' => 'oplugins'     
                                                  , 'menu_title'    => ucwords( __('Add New', 'booking-manager') )
                                                  , 'page_header'   => ucwords( __('Add New', 'booking-manager') )
                                                  , 'browser_header'=> ucwords( __('Files', 'booking-manager') ) 
                                                  , 'user_role' => get_wpbm_option( 'wpbm_user_role_addnew' )  
                                                                            )
                                                );

if (0)  
    self::$instance->admin_menu['settings'] = new WPBM_Admin_Menus( 
                                                    'wpbm-settings' , array (
                                                    'in_menu' => 'oplugins'                                                                         
                                                  , 'menu_title'    => __('Settings', 'booking-manager')
                                                  , 'page_header'   => __('General Settings', 'booking-manager')
                                                  , 'browser_header'=> __('Settings', 'booking-manager') 
                                                  , 'user_role' => get_wpbm_option( 'wpbm_user_role_settings' )
                                                                            )
                                                );        
    
}

    
    
    /** Get Menu Object
     * 
     * @param type  - menu type
     * @return boolean
     */
    public function get_menu_object( $type ) {

        if ( isset( self::$instance->admin_menu[ $type ] ) )
            return self::$instance->admin_menu[ $type ];
        else 
            return false;
    }


    // Define constants
    private function constants() {
        require_once WPBM_PLUGIN_DIR . '/core/wpbm-constants.php' ; 
    }
    
    
    // Include Files
    private function includes() {
        require_once WPBM_PLUGIN_DIR . '/core/wpbm-include.php' ; 
    }
    
        
    private function define_version() {
        
        // GET VERSION NUMBER
        $plugin_data = get_file_data_wpdev(  WPBM_FILE , array( 'Name' => 'Plugin Name', 'PluginURI' => 'Plugin URI', 'Version' => 'Version', 'Description' => 'Description', 'Author' => 'Author', 'AuthorURI' => 'Author URI', 'TextDomain' => 'Text Domain', 'DomainPath' => 'Domain Path' ) , 'plugin' );
        if (!defined('WPBM_VERSION'))    define('WPBM_VERSION',   $plugin_data['Version'] );
    }

    
    /**
     * Load Plugin Locale.
     * Look firstly in Global folder: /wp-content/languages/plugin_name
     *         then in Local  folder: /wp-content/plugins/plugin_name/languages/
     * and afterwards load default  : load_plugin_textdomain( ...
     */
    public function load_textdomain() {
        // Set filter for plugin's languages directory
        $plugin_lang_dir = WPBM_PLUGIN_DIR . '/languages/';
        $plugin_lang_dir = apply_filters( 'wpbm~languages_directory', $plugin_lang_dir );

        // Plugin locale filter
        $locale        = apply_filters( 'plugin_locale',  get_locale() , 'booking-manager');
        $mofile        = sprintf( '%1$s-%2$s.mo', 'booking-manager', $locale );

        // Setup paths to current locale file
        $mofile_local  = $plugin_lang_dir . $mofile;
        $mofile_global = WP_LANG_DIR . '/booking-manager/' . $mofile;

        if ( file_exists( $mofile_global ) ) {                      
            // Look in global /wp-content/languages/plugin_name folder
            load_textdomain( 'booking-manager', $mofile_global );                       
            
        } elseif ( file_exists( $mofile_local ) ) {                
            // Look in local /wp-content/plugins/plugin_name/languages/ folder
            load_textdomain( 'booking-manager', $mofile_local );                        
            
        } else {                
            // Load the default language files
            load_plugin_textdomain( 'booking-manager', false, $plugin_lang_dir );       
        }
    }    
    
    
    // Cloning instances of the class is forbidden
    public function __clone() {

        _doing_it_wrong( __FUNCTION__, __( 'Action is not allowed!' ), '1.0' );
    }

    
    // Unserializing instances of the class is forbidden
    public function __wakeup() {

        _doing_it_wrong( __FUNCTION__, __( 'Action is not allowed!' ), '1.0' );
    }

    
    // Initialization
    private function start(){
        
        if (  ( defined( 'DOING_AJAX' ) )  && ( DOING_AJAX )  ){                        // New A J A X    R e s p o n d e r
			
            require_once WPBM_PLUGIN_DIR . '/core/wpbm-ajax.php';                        // Ajax 
            
            return false;
        } else {                                                                        // Usual Loading of plugin

            // We are having Response, its executed in other file: wpbm-response.php
            if ( WPBM_RESPONSE )
                return false;
			
            ////////////////////////////////////////////////////////////////////
        }
        return true;
    }
    
}

else:   // Its seems that  some instance of Booking Manager still activted!!!
    

    function wpbm_show_activation_error() {

        $message_type = 'error';
        $title        = __( 'Error' , 'booking-manager') . '!';
        $message      = __( 'Please deactivate previous old version of' , 'booking-manager') . ' ' . 'booking-manager';
        
        $wpbm_version_num = get_option( 'wpbm_version_num');        
        if ( ! empty( $wpbm_version_num ) )
            $message .= ' <strong>' . $wpbm_version_num . '</strong>'; 
        
        
        $is_delete_if_deactive =  get_wpbm_option( 'wpbm_is_delete_if_deactive' ); // check

        if ( $is_delete_if_deactive == 'On' ) { 
            
            $message .= '<br/><br/> <strong>Warning!</strong> ' . 'All plugin data will be deleted when plugin had deactivated.' . ' '
                . sprintf( 'If you want to save your plugin data, please uncheck the %s"Delete plugin data"%s at the', '<strong>', '</strong>') . ' ' . __( 'Settings' , 'booking-manager') . '.';
        }
        
        $message_content = '';

        $message_content .= '<div class="clear"></div>';

        $message_content .= '<div class="updated wpbm-settings-notice notice-' . $message_type . ' ' . $message_type . '" style="text-align:left;padding:10px;">';

        if ( ! empty( $title ) )
        $message_content .=  '<strong>' . esc_js( $title ) . '</strong> ';

        $message_content .= html_entity_decode( esc_js( $message ) ,ENT_QUOTES) ;

        $message_content .= '</div>';

        $message_content .= '<div class="clear"></div>';
        
        echo $message_content;
    }    
    
    add_action('admin_notices', 'wpbm_show_activation_error');    
    
    return;         // Exit

endif;


/**
 * The main function responsible for returning the one true Instance to functions everywhere.
 *
 * Example: <?php $wpbm = WPBM(); ?>
 */
function WPBM() {
    return WPBM_Init::init();
}



// Start
WPBM();



//if (  ! defined( 'SAVEQUERIES') ) define('SAVEQUERIES', true);

 //add_action( 'admin_footer', 'wpbm_show_debug_info', 130 ); 
function wpbm_show_debug_info() {
    
    $request_uri = $_SERVER['REQUEST_URI'];                                 // FixIn:5.4.1
    if ( strpos( $request_uri, 'page=wpbm') === false ) {
        return;
    }
    echo '<div style="width:800px;margin:10px auto;"><style type="text/css"> a:link{background: inherit !important; } pre { white-space: pre-wrap; }</style>'; 
    
phpinfo();  echo '</div>'; return;
    
    ?><div style="width:auto;margin:0 0 0 215px;font-size:11px;    "><?php 

// SYSTEM  INFO SHOWING ////////////////////////////////////////////////////////
    
    //Note firstly  need to  define this in functions.php file:   define('SAVEQUERIES', true);
    global $wpdb;
    echo '<div class="clear"></div>START SYSTEM<pre>';
        $qq_kk = 0;
        $total_time = 0;
        $total_num = 0;
        foreach ( $wpdb->queries as $qq_k => $qq ) {
            if ( 
                       ( strpos( $qq[0], 'booking-manager') !== false ) 

                ) {
                if ( $qq[1] > 0.002 ) { echo '<div style="color:#A77;font-weight:bold;">'; }
                debuge($qq_kk++, $qq);
                $total_time += $qq[1];
                $total_num++;
                if ( $qq[1] > 0.002 ) { echo '</div>'; }
            }
        }

        echo '<div><pre class="prettyprint linenums" style="font-size:18px;">[' . $total_num . '/' . $total_time . '] WPBM Requests TOTAL TIME</pre></div>';
    
        echo '<div class="clear"></div>'; 

        echo '<div><pre class="prettyprint linenums" style="font-size:18px;">' . get_num_queries(). '/'  . timer_stop(0, 3) . 'qps</pre></div>';
        
        echo '<div class="clear"></div>'; 
            
    echo "</pre>";
    ?><br/><br/><br/><br/><br/><br/><?php
    echo '<div class="clear"></div>'; 

////////////////////////////////////////////////////////////////////////////////
    ?></div><?php
    
    echo '</div>';
}