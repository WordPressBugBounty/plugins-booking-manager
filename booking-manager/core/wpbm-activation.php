<?php
/**
 * @version 1.0
 * @package Booking Manager 
 * @subpackage Activation / Deactivation
 * @category Functions
 * @author      wpdevelop
 *
 * @web-site    https://oplugins.com/
 * @email       info@oplugins.com 
 * @modified    2016-03-17
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


/** Activation  & Deactivation  of Booking Manager  */
class WPBM_ItemInstall extends WPBM_Install {

    /** Overload Booking Manager option names and some other parameters */
    public function get_init_option_names() {
        
        add_wpbm_action( 'wpbm_activate_user', array( $this, 'wpbm_activate') );        // Hook  for MU User activation 
        
        return  array(
                  'option-version_num'                  => 'wpbm_version_num'
                , 'option-is_delete_if_deactive'        => 'wpbm_is_delete_if_deactive'
                , 'option-activation_process'           => 'wpbm_activation_process'
                , 'transient-wpbm_activation_redirect'  => '_wpbm_activation_redirect'
                , 'message-delete_data'                 =>  '<strong>' . __('Warning!', 'booking-manager') . '</strong> '
                                                            . __('All plugin data will be deleted when the plugin is deactivated.', 'booking-manager') 
                                                            . '<br />'
                                                            . sprintf( __('If you want to save your plugin data, please uncheck the %s"Delete data"%s at the' , 'booking-manager')
                                                                       , '<strong>', '</strong>') 
                                                            . '<a href="' . esc_url( admin_url( add_query_arg( array( 'page' => 'oplugins', 'tab' => 'wpbm-settings' ), 'admin.php' ) ) ) 
                                                                     . '#wpbm_general_settings_uninstall_metabox"> ' .  __('settings page', 'booking-manager') . '.' 
                                                            . ' </a>'
                , 'link_settings'                       => '<a href="' . esc_url( admin_url( add_query_arg( array( 'page' => 'oplugins', 'tab' => 'wpbm-settings' ), 'admin.php' ) ) ) 
                                                                       . '">'.__("Settings", 'booking-manager').'</a>'
                , 'link_whats_new'                      => ''
        );                
        
    }
    
    /** Check if was updated from lower to  high version */
    public function is_update_from_lower_to_high_version() {
        
        $is_make_activation = false;
        
		//TODO: Set  here correct Table Name about checking upgrade
		//
        // Check  conditions for different version about Upgrade
        if ( ( class_exists( 'wpbm_personal' ) ) && ( ! wpbm_is_table_exists( 'itemtypes' ) ) )
            $is_make_activation = true;

        return $is_make_activation;
    }

}




////////////////////////////////////////////////////////////////////////////////
//   A c t i v a t e    &    D e a c t i v a t e
////////////////////////////////////////////////////////////////////////////////

/** Activation */
function wpbm_activate() {
    
	// Check for blank  data install
	$wpbm_secret_key = get_wpbm_option( 'wpbm_date_format' );
	if ( empty( $wpbm_secret_key ) ) 
		$is_first_time_install = true;
	else
		$is_first_time_install = false;


    make_wpbm_action( 'wpbm_before_activation' );
    
    wpbm_load_translation();
    
    $version = get_wpbm_version();
    $is_demo = wpbm_is_this_demo();

    ////////////////////////////////////////////////////////////////////////////
    // Options
    ////////////////////////////////////////////////////////////////////////////
    $default_options_to_add = wpbm_get_default_options();
    
			
    foreach ( $default_options_to_add as $default_option_name => $default_option_value ) {
        
        add_wpbm_option( $default_option_name, $default_option_value );
    }

	
    ////////////////////////////////////////////////////////////////////////////
    // DB Tables
    ////////////////////////////////////////////////////////////////////////////
    
    ////////////////////////////////////////////////////////////////////////////
    // Other versions Activation
    ////////////////////////////////////////////////////////////////////////////
    make_wpbm_action( 'wpbm_other_versions_activation' );
          
    make_wpbm_action( 'wpbm_after_activation' );
}
add_wpbm_action( 'wpbm_activation',  'wpbm_activate' );



// Deactivate
function wpbm_deactivate() {

    ////////////////////////////////////////////////////////////////////////////
    // Options
    ////////////////////////////////////////////////////////////////////////////

    $default_options_to_add = wpbm_get_default_options();
    foreach ( $default_options_to_add as $default_option_name => $default_option_value) {
        
        delete_wpbm_option( $default_option_name );
    }   
    
    
    ////////////////////////////////////////////////////////////////////////////
    // Widgets
    ////////////////////////////////////////////////////////////////////////////
    delete_wpbm_option( 'wpbm_activation_redirect_for_version' );
    
    
    ////////////////////////////////////////////////////////////////////////////
    // DB Tables
    ////////////////////////////////////////////////////////////////////////////
    global $wpdb;
    // $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wpbm" );
 
    // Delete all users item windows states   
    if ( false === $wpdb->query( "DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE '%wpbm_%'" ) ){    // All users data
        debuge_error('Error during deleting user meta at DB',__FILE__,__LINE__);
        die();
    }
     
    ////////////////////////////////////////////////////////////////////////////
    // Other versions Deactivation
    ////////////////////////////////////////////////////////////////////////////
    make_wpbm_action('wpbm_other_versions_deactivation');                         
}
add_wpbm_action( 'wpbm_deactivation',  'wpbm_deactivate' );


/** Default Options 
 * 
 *  $option_name = '';
 *  $options_for_delete = wpbm_get_default_options( $option_name, $is_get_multiuser_general_options );
 */
function wpbm_get_default_options( $option_name = '' ) {

    $is_demo = wpbm_is_this_demo();

	$default_options = array();	
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// General Settings
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// Miscellaneous
	$default_options[ 'wpbm_start_day_weeek' ] = 0;
	$default_options[ 'wpbm_date_format' ] = get_option( 'date_format' );
	$default_options[ 'wpbm_time_format' ] = get_option( 'time_format' );	
	// Advanced
	$default_options[ 'wpbm_is_not_load_bs_script_in_admin' ] = 'Off';	
	/**
	$default_options[ 'wpbm_is_not_load_bs_script_in_client' ] = 'Off';	
	$default_options[ 'wpbm_is_load_js_css_on_specific_pages' ] = 'Off';
	$default_options[ 'wpbm_pages_for_load_js_css' ] = '';
	*/	
	
	$default_options[ 'wpbm_menu_position' ] = ( $is_demo ) ? 'bottom' : 'bottom';
	// User permissions
	$default_options[ 'wpbm_user_role_master' ] = ( $is_demo ) ? 'subscriber' : 'editor';
	//$default_options[ 'wpbm_user_role_addnew' ] = ( $is_demo ) ? 'subscriber' : 'editor';
	//$default_options[ 'wpbm_user_role_settings' ] = ( $is_demo ) ? 'subscriber' : 'editor';
	// Position
	
	// Uninstall
	$default_options[ 'wpbm_is_delete_if_deactive' ] = ($is_demo) ? 'On' : 'Off';
	$default_options[ 'wpbm_is_hide_details' ] = 'Off';     //FixIn: 2.0.12.3

	$default_options[ 'wpbm_listing_template' ] = 
		'<div class="wpbm-event">' . "\n"
	  . '     <div style="width:35%;float:left;margin-right:5%;">[DATES]</div>' . "\n"
	  . '     <div style="width:60%;float:left;">' . "\n"
	  . '          <h2>[SUMMARY]</h2> <span style="display:none;">[UID]</span>' . "\n"
	  . '          <p class="desciption">[DESCRIPTION]</p>' . "\n"
	  . '     </div>' . "\n"
	  . '     <div style="clear:both;"></div>' . "\n"
	  . '</div>' . "\n"
	  . '<hr />';
	
	if ( ! empty( $option_name ) ) {
		
		if ( isset( $default_options[ $option_name ] ) )
			return $default_options[ $option_name ];                        // Return 1 option
		else
			return  false;                                                  // Option does NOT exist
		
	} else {
		return $default_options;                                            // Return  ALL
	}
}