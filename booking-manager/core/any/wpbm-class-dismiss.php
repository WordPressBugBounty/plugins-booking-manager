<?php /**
 * @version 1.0
 * @description Dismiss Class
 * @category Dismiss panels Class
 * @author wpdevelop
 *
 * @web-site https://oplugins.com/
 * @email info@oplugins.com 
 * 
 * @modified 2015-11-13
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly

/** Dismiss Class
 * @usage:  
 * 
 * Inline Setting Notice Dismiss'
 * 
		$notice_id = 'wpbm_upload_help_section';
		if ( ! wpbm_section_is_dismissed( $notice_id ) ) {

			?><div  id="<?php echo $notice_id; ?>" 
					class="wpbm_system_notice wpbm_is_dismissible wpbm_is_hideable notice-warning wpbm_internal_notice"
					data-nonce="<?php echo wp_create_nonce( $nonce_name = $notice_id . '_wpbmnonce' ); ?>"	
					data-user-id="<?php echo get_current_user_id(); ?>"
				><?php 
			wpbm_x_dismiss_button();
			....
		?></div><?php	
 *
 *		System Notice
 *
 		$notice_id = 'wpbm_system_notice_free_instead_paid';		
		if ( ! wpbm_section_is_dismissed( $notice_id ) ) {
			?><div  id="<?php echo $notice_id; ?>" 
					class="wpbm_system_notice wpbm_is_dismissible wpbm_is_hideable updated notice-warning"
					data-nonce="<?php echo wp_create_nonce( $nonce_name = $notice_id . '_wpbmnonce' ); ?>"	
					data-user-id="<?php echo get_current_user_id(); ?>"
				><?php 			
			wpbm_x_dismiss_button();			
			...	
			?></div><?php
 * 
 */
final class WPBM_Dismiss {
	
    static private $instance = NULL;											// Define only one instance of this class
	
	/** Get only one instance of this class
	 * 
	 * @return class WPBM_Dismiss
	 */
	public static function init() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WPBM_Dismiss ) ) {
			
			self::$instance = new WPBM_Dismiss;			
									
			// JS & CSS
			add_action( 'wpbm_enqueue_js_files',  array( self::$instance, 'wpbm_js_load_files' ),     50  );
			add_action( 'wpbm_enqueue_css_files', array( self::$instance, 'wpbm_enqueue_css_files' ), 50  );
			
			// Ajax Handlers.		Note. "locale_for_ajax" recehcked in wpbm-ajax.php
			add_action( 'wp_ajax_'		    . 'WPBM_DISMISS', array( self::$instance, 'wpbm_ajax_' . 'WPBM_DISMISS' ) );	// Admin & Client (logged in usres)  
			//add_action( 'wp_ajax_nopriv_' . 'WPBM_DISMISS', array( self::$instance, 'wpbm_ajax_' . 'WPBM_DISMISS' ) );	// Client         (not logged in)        
		}
		
		return self::$instance;        			
	}

	
	/** Ajax Handler 
	 * for request like: 
	 *                  action:     'WPBM_DISMISS',
                        user_id:    panel_obj.user_id ,
                        nonce:      panel_obj.nonce,
                        element_id: panel_obj.id,
						is_closed:  1
	 * 
	 */
	public function wpbm_ajax_WPBM_DISMISS() {
		
		if ( ! isset( $_POST['element_id'] ) || empty( $_POST['element_id'] ) ) {
			exit;
		}
		
		$action_name = $_POST['element_id'] . '_wpbmnonce';
		$nonce_post_key = 'nonce';

		// Check Security
		$result = check_ajax_referer( $action_name, $nonce_post_key );

		// Save status
		update_user_option(  (int) $_POST[ 'user_id' ], 'wpbm_win_' . esc_attr( $_POST[ 'element_id' ] ), (int) $_POST[ 'is_closed' ]  );

		// FixIn: 2.0.2.1		//Fix: We need to  comment this line,  because previously its possible that  we already  sent some messages,  and its does not correct  json format in this case.
								//Fix: of showing "parsererror ~ SyntaxError: JSON.parse: unexpected character at line 1 column 1 of the JSON data"
		// send JSON 	
		// wp_send_json( array( 'response' => 'success' ) );																// Return JS OBJ: response_data = { response: "success" } in "dismiss.js"
																														// This function call wp_die( '', '', array( 'response' => null, ) )
		wp_die( '', '', array( 'response' => null  ) );
	}

	
	/** JSS */
	public function wpbm_js_load_files( $where_to_load ) {
		
		$in_footer = true;
		
		if ( ( is_admin() ) && ( in_array( $where_to_load, array( 'admin', 'both' ) ) ) ) {
			
			wp_enqueue_script( 'wpbm-dismiss', wpbm_plugin_url( '/core/any/js/dismiss.js' ), array( 'wpbm-global-vars' ), '1.1', $in_footer );
		}
	}

	
	/** CSS */
	public function wpbm_enqueue_css_files( $where_to_load ) {

		if ( ( is_admin() ) && ( in_array( $where_to_load, array( 'admin', 'both' ) ) ) ) {

			wp_enqueue_style( 'wpbm-dismiss', wpbm_plugin_url( '/core/any/css/dismiss.css' ), array(), WPBM_VERSION_NUM );
		}
	}


	/** Check if this section dismissed or not.
	 * 
	 * @param string $section_html_id
	 * @return boolean
	 */
	public function is_dismissed( $section_html_id ) {
        
        if ( '1' == get_user_option( 'wpbm_win_' . $section_html_id ) ) 
			return true;                                                       
		else 
			return false;
	}
		 	
 }

 
function wpbm_dismiss() {
    return WPBM_Dismiss::init();
}
wpbm_dismiss();																	// Run


/** Check  if specific section dismissed or not
 * 
 * @param type $section_html_id
 * @return boolean
 */
function wpbm_section_is_dismissed( $section_html_id ) {
	
	$wpbm_dismiss = wpbm_dismiss();
	
	return $wpbm_dismiss->is_dismissed( $section_html_id );	
}


/** Show dismiss X button 
 * 
 * @param string $title
 * @param array $attributes_arr - array of attributes, like: array( 'class' => 'wpbm_dismiss' )
 * @param bool $echo
 * @return string of dismiss button
 */
function wpbm_x_dismiss_button( $title = '&times;', $attributes_arr = array(), $echo = true ) {

	$defaults = array(
		  'style' => ''
		, 'class' => 'wpbm_dismiss'
		, 'title' => esc_js( __( 'Close', 'booking-manager' ) )
	);
	$attributes_arr = wp_parse_args( $attributes_arr, $defaults );
	
	$attr_echo = array();
	foreach ( $attributes_arr as $attr_name => $attr_value ) {
		$attr_echo[] = esc_attr( $attr_name ) . '="' . esc_attr( $attr_value ) . '"';
	}
	$attr_echo = implode( ' ', $attr_echo );	
	
	if ( ! $echo ) { ob_start(); }	
	
	?><a href="javascript:void(0)" <?php echo $attr_echo; ?> ><?php  echo $title;  ?></a><?php 
			
	if ( ! $echo ) { return ob_get_clean(); }
}