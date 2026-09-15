<?php
/**
 * @version 1.0
 * @package Booking Manager 
 * @subpackage Ajax Responder
 * @category Items
 * 
 * @author wpdevelop
 * @link https://oplugins.com/
 * @email info@oplugins.com
 *
 * @modified 2014.05.26
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly

////////////////////////////////////////////////////////////////////////////////
//    S u p p o r t    f u n c t i o n s    f o r     A j a x    ///////////////
////////////////////////////////////////////////////////////////////////////////

// Verify the nonce.    
function wpbm_check_nonce_in_admin_panel( $action_check = 'wpbm_ajax_admin_nonce' ){    
  
    $nonce = ( isset( $_REQUEST[ 'wpbm_nonce' ] ) ) ? $_REQUEST[ 'wpbm_nonce' ] : '';

	if ( '' === $nonce ) return false;											// Its was request  from  some other plugin
	
    if ( ! wp_verify_nonce( $nonce, $action_check ) ) {                         // This nonce is not valid.     
        ?>
        <script type="text/javascript">
           jQuery("#ajax_respond").after( "<div class='wpdevelop'><div class='alert alert-warning alert-danger'><?php 
            printf(__('%sError!%s Request do not pass security check! Please refresh the page and try one more time.' , 'booking-manager'),'<strong>','</strong>');
            ?></div></div>" );  
           if ( jQuery("#ajax_message").length )     
            jQuery("#ajax_message").slideUp();
        </script>
        <?php
        die;                
    } 
	return  true;																										//FixIn: 7.2.1.10
}


// Check and (re)Load specific Locale for the Ajax request - based on "admin_init" hook
function wpbm_check_locale_for_ajax() {
        
    add_wpbm_filter( 'wpbm_check_for_active_language', 'wpbm_check_for_active_language' );   // Add Hook for ability  to check  the content for active lanaguges

	if ( isset( $_POST[ 'wpbm_active_locale' ] ) ) {	// Reload locale according request parameter
		global $l10n;
		if ( isset( $l10n[ 'booking-manager' ] ) )
			unset( $l10n[ 'booking-manager' ] );

		if ( ! defined( 'WPBM_RELOAD' ) )
			define( 'WPBM_RELOAD', esc_js( $_POST[ 'wpbm_active_locale' ] ) );

		// Reload locale settings, its required for the correct  dates format
		if ( isset( $l10n[ 'default' ] ) )
			unset( $l10n[ 'default' ] );			   // Unload locale     
		add_filter( 'locale', 'wpbm_get_locale', 999 );					   // Set filter to load the locale of the Booking Manager
		load_default_textdomain();										  // Load default locale            
		global $wp_locale;
		$wp_locale = new WP_Locale();									   // Reload class

		wpbm_load_locale( WPBM_RELOAD );
	}
}
 

////////////////////////////////////////////////////////////////////////////////
//    A j a x    H o o k s    f o r    s p e c i f i c    A c t i o n s    /////
////////////////////////////////////////////////////////////////////////////////


function wpbm_ajax_WPBM_USER_SAVE_WINDOW_STATE() {
	if ( ! wpbm_check_nonce_in_admin_panel() ) {
		return false;
	}

	$requested_user_id = isset( $_POST['user_id'] ) && is_scalar( $_POST['user_id'] )
		? absint( wp_unslash( (string) $_POST['user_id'] ) )
		: 0;
	$authorized_user_id = wpbm_get_authorized_user_option_target_id( $requested_user_id );
	if ( 0 === $authorized_user_id ) {
		status_header( 403 );
		wp_send_json_error( array( 'message' => __( 'You can update only your own interface preferences.', 'booking-manager' ) ) );
	}

	$window_name = isset( $_POST['window'] ) && is_scalar( $_POST['window'] )
		? sanitize_text_field( wp_unslash( (string) $_POST['window'] ) )
		: '';
	if ( '' === $window_name ) {
		status_header( 400 );
		wp_send_json_error( array( 'message' => __( 'The interface preference is not valid.', 'booking-manager' ) ) );
	}

	$is_closed = isset( $_POST['is_closed'] ) && is_scalar( $_POST['is_closed'] )
		? (int) wp_unslash( (string) $_POST['is_closed'] )
		: 0;
	update_user_option( $authorized_user_id, 'wpbm_win_' . $window_name, $is_closed );
	wp_send_json_success();
}


/** Save Custom User Data */
function wpbm_ajax_WPBM_USER_SAVE_CUSTOM_DATA() {
	if ( ! wpbm_check_nonce_in_admin_panel() ) {
		return false;
	}

	$requested_user_id = isset( $_POST['user_id'] ) && is_scalar( $_POST['user_id'] )
		? absint( wp_unslash( (string) $_POST['user_id'] ) )
		: 0;
	$authorized_user_id = wpbm_get_authorized_user_option_target_id( $requested_user_id );
	if ( 0 === $authorized_user_id ) {
		status_header( 403 );
		wp_send_json_error( array( 'message' => __( 'You can update only your own interface preferences.', 'booking-manager' ) ) );
	}

	$data_name = isset( $_POST['data_name'] ) && is_scalar( $_POST['data_name'] )
		? sanitize_text_field( wp_unslash( (string) $_POST['data_name'] ) )
		: '';
	$data_value = isset( $_POST['data_value'] ) && is_scalar( $_POST['data_value'] )
		? wp_unslash( (string) $_POST['data_value'] )
		: '';
	if ( '' === $data_name ) {
		status_header( 400 );
		wp_send_json_error( array( 'message' => __( 'The interface preference is not valid.', 'booking-manager' ) ) );
	}
	$is_reload = ! empty( $_POST['is_reload'] );

    /*  Exmaple of $_POST:
        [data_name] => add_wpbm_calendar_options
        [data_value] => calendar_months_count=1&calendar_months_num_in_1_row=1&calendar_width=500px&calendar_cell_height
     */
	$post_param = explode( '&', $data_value );                                   // "&" was set by jQuery.param( data_params ) in client side.
    $data_to_save = array();
    foreach ( $post_param as $param ) {
        $param_data = explode( '=', $param );
                
        $data_to_save[ $param_data[0] ] = ( isset( $param_data[1] ) ) ? esc_attr( $param_data[1] ) : '';
    }
    /*  Exmaple: 
        Array
        (
            [calendar_months_count] => 1
            [calendar_months_num_in_1_row] => 1
            [calendar_width] => 500px
            [calendar_cell_height] => 
        )
     */

    // Save Custom User Data
	update_user_option( $authorized_user_id, 'wpbm_custom_' . $data_name, serialize( $data_to_save ) );

    ?>  <script type="text/javascript">            
            var my_message = '<?php echo html_entity_decode( esc_js( __('Saved' , 'booking-manager') ),ENT_QUOTES) ; ?>';
            wpbm_admin_show_message( my_message, 'success', 1000 ); 
            <?php if ( $is_reload ) { ?>
            setTimeout(function ( ) {location.reload(true);} ,1500);
            <?php } ?>
        </script> <?php
    die();
    
}


////////////////////////////////////////////////////////////////////////////////
//    R u n     A j a x                       //////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
if (  is_admin() && ( defined( 'DOING_AJAX' ) ) && ( DOING_AJAX )  ) {
	
    // Reload Locale if its required
    add_action( 'admin_init', 'wpbm_check_locale_for_ajax' );    

    // Hooks list 
    $actions_list = array(   
							 'WPBM_USER_SAVE_WINDOW_STATE'  => 'admin'
                            ,'WPBM_USER_SAVE_CUSTOM_DATA'	=> 'admin'
		
							, 'WPBM_LISTING_ICS_URL'		=> 'admin'
                         );
    
    foreach ($actions_list as $action_name => $action_where) {
        
        if ( ( isset($_POST['action']) ) && ( $_POST['action'] == $action_name ) ){
            
            if ( ( $action_where == 'admin' ) || ( $action_where == 'both' ) ) 
                add_action( 'wp_ajax_'        . $action_name, 'wpbm_ajax_' . $action_name);      // Admin & Client (logged in usres)
            
            if ( ( $action_where == 'both' ) || ( $action_where == 'client' ) ) 
                add_action( 'wp_ajax_nopriv_' . $action_name, 'wpbm_ajax_' . $action_name);      // Client         (not logged in)        
        }
    }	
} 
