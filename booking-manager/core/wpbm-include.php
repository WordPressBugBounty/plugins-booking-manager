<?php /**
 * @version 1.0
 * @package Booking Manager 
 * @subpackage Files Loading
 * @category Items
 * 
 * @author wpdevelop
 * @link https://oplugins.com/
 * @email info@oplugins.com
 *
 * @modified 29.09.2015
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly

////////////////////////////////////////////////////////////////////////////////
//   L O A D   F I L E S
////////////////////////////////////////////////////////////////////////////////

require_once( WPBM_PLUGIN_DIR . '/core/any/class-css-js.php' );                 // Abstract. Loading CSS & JS files                 = Package: Any =
require_once( WPBM_PLUGIN_DIR . '/core/any/class-admin-settings-api.php' );     // Abstract. Settings API.        
require_once( WPBM_PLUGIN_DIR . '/core/any/class-admin-page-structure.php' );   // Abstract. Page Structure in Admin Panel    
require_once( WPBM_PLUGIN_DIR . '/core/any/class-admin-menu.php' );             // CLASS. Menus of plugin
require_once( WPBM_PLUGIN_DIR . '/core/any/admin-bs-ui.php' );                  // Functions. Toolbar BS UI Elements
if( is_admin() ) {
	require_once WPBM_PLUGIN_DIR . '/core/any/wpbm-class-dismiss.php';			// Class - Dismiss     	
	require_once WPBM_PLUGIN_DIR . '/core/any/wpbm-class-notices.php';			// Class - Notices                	
}

////////////////////////////////////////////////////////////////////////////////
// Functions	
////////////////////////////////////////////////////////////////////////////////
require_once( WPBM_PLUGIN_DIR . '/core/wpbm-debug.php' );                       // Debug                                            = Package: WPBM =
require_once( WPBM_PLUGIN_DIR . '/core/wpbm-core.php' );                        // Core 
require_once( WPBM_PLUGIN_DIR . '/core/wpbm-translation.php' );                 // Translations 
require_once( WPBM_PLUGIN_DIR . '/core/wpbm-functions.php' );                   // Functions	
	
//-----------------------------------------------------------------------------------------
// Working files:  ... //        
		require_once( WPBM_PLUGIN_DIR . '/core/wpbm-shortcodes.php' );					// Shortcodes
        require_once( WPBM_PLUGIN_DIR . '/core/wpbm-ics.php' );							// ICS
        require_once( WPBM_PLUGIN_DIR . '/core/wpbm-ics-listing.php' );					// ICS Listing functionality
		
		if ( true ) {	// Only  if wpbc installed
			require_once( WPBM_PLUGIN_DIR . '/core/wpbc/wpbm-bc.php' );						// Booking Calendar native integration
			require_once( WPBM_PLUGIN_DIR . '/core/wpbc/wpbm-bc-import.php' );				// Booking Calendar native integration
			require_once( WPBM_PLUGIN_DIR . '/core/wpbc/wpbm-bc-export.php' );				// Booking Calendar native integration		        
		}
//-----------------------------------------------------------------------------------------
		
require_once( WPBM_PLUGIN_DIR . '/core/wpbm-upload.php' );						// Upload Functions		
require_once( WPBM_PLUGIN_DIR . '/core/wpbm-emails.php' );                      // Emails

// JS & CSS		////////////////////////////////////////////////////////////////
require_once( WPBM_PLUGIN_DIR . '/core/wpbm-css.php' );                         // Load CSS
require_once( WPBM_PLUGIN_DIR . '/core/wpbm-js.php' );                          // Load JavaScript and define JS Varibales

// Admin UI ////////////////////////////////////////////////////////////////////
require_once( WPBM_PLUGIN_DIR . '/core/admin/wpbm-dashboard.php' );             // Dashboard Widget

// Admin Pages	////////////////////////////////////////////////////////////////
if( is_admin() ) {
//-----------------------------------------------------------------------------------------
		require_once( WPBM_PLUGIN_DIR . '/core/admin/page-root-ics.php' );				// Master page		
		// require_once( WPBM_PLUGIN_DIR . '/core/admin/exmpl-page-files-add.php' );				// Get Upload functionality  from  this file for uploading .ICS file
		require_once( WPBM_PLUGIN_DIR . '/core/admin/page-settings.php' );				// Settings page 
			require_once( WPBM_PLUGIN_DIR . '/core/admin/api-settings.php' );			// Settings API
		require_once( WPBM_PLUGIN_DIR . '/core/admin/page-settings-listing.php' );		// Settings > Listing page 
	require_once WPBM_PLUGIN_DIR . '/core/admin/wpbm-toolbar-tiny.php';			// Tiny Toolbar - insert shortcodes
//-----------------------------------------------------------------------------------------
}
/////////////////////////////////////////////////////////////////////////////////
require_once( WPBM_PLUGIN_DIR . '/core/any/activation.php' );
require_once( WPBM_PLUGIN_DIR . '/core/wpbm-activation.php' );

make_wpbm_action( 'wpbm_loaded_php_files' );