<?php /**
 * @version 1.0
 * @package Booking Manager 
 * @category Content of item Listing page
 * @author wpdevelop
 *
 * @web-site https://oplugins.com/
 * @email info@oplugins.com 
 * 
 * @modified 2015-11-13
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


/** Show Content
 *  Update Content
 *  Define Slug
 *  Define where to show
 */
class WPBM_Page_Single extends WPBM_Page_Structure {
    
        
    public function in_page() {
        return 'oplugins';
    }

    
    public function tabs() {
        
        $tabs = array();
        $tabs[ 'wpbm' ] = array(
                              'title'		=> __( 'Manage .ics', 'booking-manager' )	// Title of TAB    
                            , 'hint'		=> __( 'Upload .ics File', 'booking-manager' )	// Hint    
                            , 'page_title'	=> __( 'Booking Manager', 'booking-manager' )	// Title of Page    
                            , 'link'		=> ''								// Can be skiped,  then generated link based on Page and Tab tags. Or can  be extenral link
                            , 'position'	=> ''                               // 'left'  ||  'right'  ||  ''
                            , 'css_classes' => ''                               // CSS class(es)
                            , 'icon'		=> ''                               // Icon - link to the real PNG img
                            , 'font_icon'	=> 'glyphicon glyphicon-tasks'			// CSS definition  of forn Icon
                            , 'default'		=> true								// Is this tab activated by default or not: true || false. 
                            , 'disabled'	=> false                            // Is this tab disbaled: true || false. 
                            , 'hided'		=> ! true                             // Is this tab hided: true || false. 
                            , 'subtabs'		=> array()
            
        );        
        $subtabs = array();                
        // $tabs[ 'items' ][ 'subtabs' ] = $subtabs;        
//		  $subtabs['manage'] = array( 
//                              'type' => 'subtab'                                  // Required| Possible values:  'subtab' | 'separator' | 'button' | 'goto-link' | 'html'
//                            , 'title'		=> __( 'Manage .ics', 'booking-manager' )	// Title of TAB    
//                            , 'hint'		=> __( 'Upload .ics File', 'booking-manager' )	// Hint    
//                            , 'page_title'	=> __( 'Booking Manager', 'booking-manager' )	// Title of Page    
//                            , 'link' => ''                                      // link
//                            , 'position' => ''                                  // 'left'  ||  'right'  ||  ''
//                            , 'css_classes' => ''                               // CSS class(es)
//                            //, 'icon' => 'http://.../icon.png'                 // Icon - link to the real PNG img
//                            //, 'font_icon' => 'glyphicon glyphicon-envelope'   // CSS definition of Font Icon
//                            , 'default' =>  true                                // Is this sub tab activated by default or not: true || false. 
//                            , 'disabled' => false                               // Is this sub tab deactivated: true || false. 
//                            , 'checkbox'  => false                              // or definition array  for specific checkbox: array( 'checked' => true, 'name' => 'feature1_active_status' )   //, 'checkbox'  => array( 'checked' => $is_checked, 'name' => 'enabled_active_status' )
//                            , 'content' => 'content'                            // Function to load as conten of this TAB
//							, 'hided'		=>  true                             // Is this tab hided: true || false. 
//                        );
        
        $tabs[ 'wpbm' ]['subtabs'] = $subtabs;		
        return $tabs;        
  
    }


    public function content() {
                
        // Checking ////////////////////////////////////////////////////////////
        
        do_action( 'wpbm_hook_settings_page_header', array( 'page' => $this->in_page() ) );								// Define Notices Section and show some static messages, if needed.
            
        // $this->settings_api();																						// Init Settings API & Get Data from DB
		 
        // Submit  /////////////////////////////////////////////////////////////
        
        $submit_form_name = 'wpbm_ics_files_form';                             // Define form name
        
        
        if ( isset( $_POST['is_form_sbmitted_'. $submit_form_name ] ) ) {

            // Nonce checking    {Return false if invalid, 1 if generated between, 0-12 hours ago, 2 if generated between 12-24 hours ago. }
            $nonce_gen_time = check_admin_referer( 'wpbm_settings_page_' . $submit_form_name  );  // Its stop show anything on submiting, if its not refear to the original page

            // Save Changes 
            $data_after_update = $this->update();                        
        }
         
        // $wpbm_user_role_master   = get_wpbm_option( 'wpbm_user_role_master' );										// O L D   W A Y:   Get Fields Data
		?><div class="wpbm-replace-container" style="display:none;"><?php  
		
			?><span class="wpbm_help_upgrade"><?php
		    wpbm_bs_dropdown_menu( array( 
                                        'title' => __( 'Help', 'booking-manager' ) 
                                      , 'font_icon' => 'glyphicon glyphicon-info-sign'
                                      , 'position' => 'right'
                                      , 'items' => array( 
                                               array( 'type' => 'link', 'title' => __('Shortcode configuration', 'booking-manager')
												    , 'url' => 'https://oplugins.com/plugins/wp-booking-manager/booking-manager-help/' //esc_url( admin_url( add_query_arg( array( 'page' => 'oplugins', 'tab' => 'wpbm', 'subtab' => 'help_shortcodes' ), 'admin.php' ) ) ) 
												   )
                                             , array( 'type' => 'divider' )
                                             ,  array( 'type' => 'link', 'title' => __('Help', 'booking-manager'), 'url' => 'https://oplugins.com/plugins/booking-manager/#faq' )
                                             //, array( 'type' => 'link', 'title' => __('FAQ', 'booking-manager'), 'url' => 'https://oplugins.com/plugins/booking-manager/#faq' )
                                             , array( 'type' => 'link', 'title' => __('Technical Support', 'booking-manager'), 'url' => 'mailto:support@oplugins.com?subject=booking-manager' )
                                             , array( 'type' => 'divider' )
                                             , array( 'type' => 'link', 'title' => __('Upgrade Now', 'booking-manager'), 'url' => wpbm_up_link()
                                                                        , 'attr' => array(
                                                                              'target' => '_blank'
                                                                            , 'style' => 'font-weight: 600;font-size: 1em;'
                                                                        ) 
                                                    )
                                        )
                        ) );  
			?></span><?php
			
		?></div><?php 
		?><script type="text/javascript">
		    jQuery(document).ready(function(){				
				jQuery( '.wpbm_help_upgrade' ).insertAfter( '.wpdevelop.wpdvlp-nav-tabs-container .wpdvlp-top-tabs .nav-tabs a:last' );
				//jQuery( '.wpdvlp-sub-tabs' ).hide();
			});	
		</script><?php
		
		
        ?><span class="wpdevelop"><?php   
		
			wpbm_js_for_items_page();				//		JavaScript:		-	Tooltips, Popover, Datepick (js & css)			
			
			// wpbm_items_toolbar();				//		T o o l b a r s	-	BS UI CSS Class              
			
        ?></span><?php     
        ?><div class="clear" style="height:0px;"></div><?php
        // Content  ////////////////////////////////////////////////////////////
        ?>
        <div class="clear" style="margin-bottom:10px;"></div>
        <span class="metabox-holder">
            <form  name="<?php echo $submit_form_name; ?>" id="<?php echo $submit_form_name; ?>" action="" method="post" >
                <?php 
                   // N o n c e   field, and key for checking   S u b m i t 
                   wp_nonce_field( 'wpbm_settings_page_' . $submit_form_name );
                ?><input type="hidden" name="is_form_sbmitted_<?php echo $submit_form_name; ?>" id="is_form_sbmitted_<?php echo $submit_form_name; ?>" value="1" />
                
                <div class="clear" style="margin-bottom:0px;"></div>

				<?php 
						wpbm_open_meta_box_section( 'wpbm_settings_ics_upload_section', __( 'Upload .ics file and show events', 'booking-manager' ) );

							$this->show_toolbar_upload();

						wpbm_close_meta_box_section();
						
				
		
						///////////////////////////////////////////////////////////
						$notice_id = 'wpbm_system_notice_ics_description';
						///////////////////////////////////////////////////////////
						if (   ( ! wpbm_section_is_dismissed( $notice_id ) )
							// || true 
						) {

							?><div  id="<?php echo $notice_id; ?>" 
									class="wpbm_system_notice wpbm_is_dismissible wpbm_is_hideable wpbm-settings-notice notice-info""
									data-nonce="<?php echo wp_create_nonce( $nonce_name = $notice_id . '_wpbmnonce' ); ?>"	
									data-user-id="<?php echo get_current_user_id(); ?>"
								style="margin: 20px 0 !important;line-height: 2em;padding: 10px 20px;"									
								><?php 

							wpbm_x_dismiss_button();

							//echo '<strong>' . __( 'Note!', 'booking-manager' ) . '</strong> ';

							$message_ics = sprintf( 
									__( '.ics - is a file format of iCalendar standard for exchanging calendar and scheduling information between different sources %s Using a common calendar format (.ics), you can keep all your calendars updated and synchronized.', 'booking-manager' )
									, 
									'<br/><em>(<strong><a href="https://www.airbnb.com/help/article/99/how-do-i-sync-my-airbnb-calendar-with-another-calendar" target="_blank">Airbnb</a></strong>, '
									. '<strong><a href="https://partnersupport.booking.com/hc/en-us/articles/213424709-How-do-I-export-my-calendar-" target="_blank">Booking.com</a></strong>, '
									. '<strong><a href="https://help.homeaway.com/articles/How-do-I-export-my-calendar-data-to-a-Google-calendar" target="_blank">HomeAway</a></strong>, '
									. '<strong><a href="https://rentalsupport.tripadvisor.com/articles/FAQ/noc-How-does-calendar-sync-work" target="_blank">TripAdvisor</a></strong>, '
									. '<strong><a href="https://help.vrbo.com/articles/How-do-I-export-my-calendar-data-to-a-Google-calendar" target="_blank">VRBO</a></strong>, '
									. '<strong><a href="https://helpcenter.flipkey.com/articles/FAQ/noc-How-does-calendar-sync-work" target="_blank">FlipKey</a></strong> '
									. str_replace( array( '.ics', 'iCalendar' ), array( '<strong>.ics</strong>', '<strong>iCalendar</strong>' ), 
												 __( 'and any other calendar that uses .ics format', 'booking-manager' )
												)
									. ')</em>.<br/>'					
								);
							$message_ics = str_replace( array( '.ics', 'iCalendar' ), array( '<strong>.ics</strong>', '<strong>iCalendar</strong>' ), $message_ics );
							echo $message_ics;


							?></div><?php
						}       
						///////////////////////////////////////////////////////////
				

						
						
						
						wpbm_open_meta_box_section( 'wpbm_settings_ics_listing_log', __( 'Log', 'booking-manager' ) );
						?><div class="wpbm_system_info_log"></div><?php
						wpbm_close_meta_box_section();
				/* ?>
                <input type="button" value="<?php _e('Send', 'booking-manager'); ?>" class="button button-primary wpbm_send_button" />  
                <input type="submit" value="<?php _e('Submit', 'booking-manager'); ?>" class="button button-primary wpbm_submit_button" />
                <?php /**/ 
				?>
            </form>
            <?php 
                
            ?>            
        </span>
        <?php 
		
		wpbm_show_wpbm_footer();			// Rating
		
		wpbm_ics_listing_ajax_js();
		
        $this->css();
    
        do_action( 'wpbm_hook_settings_page_footer', 'ics_files' );    
    }

		/** Currently is not being used */
		public function update() {                   

	return false;		

			$post_action_key = 'wpbm_action';
			if (  isset( $_POST[ $post_action_key ] )  && ( $_POST[ $post_action_key ] == 'go_send' )  ) {

				// Get Validated post
				$validated = array();                                               

				// Email
				$validated[ 'wpbm_textdata' ] = WPBM_Settings_API::validate_textarea_post_static( 'wpbm_textdata' );
	//debuge( $validated );  

				////////////////////////////////////////////////////////////////////////////////////////////////////////////

				wpbm_show_fixed_message ( __('Done', 'booking-manager'), 3  );			//, 'updated warning' );                // Show Message

				return array (   'validated_data' => $validated  );					// Exit, for do  not parse 
			}

			/** Buld data saving to DB from POST
			//$validated_fields = $this->settings_api()->validate_post();													// Get Validated Settings fields in $_POST request.        
			//$validated_fields = apply_filters( 'wpbm_settings_validate_fields_before_saving', $validated_fields );		// Hook for validated fields.

			// unset($validated_fields['wpbm_start_day_weeek']);															// Skip saving specific option, for example in Demo mode.
			//$this->settings_api()->save_to_db( $validated_fields );														// Save fields to DB
			//wpbm_show_changes_saved_message();
			//wpbm_show_fixed_message ( __('Done', 'booking-manager'), 0 );														// Show Message
			*/

			/** O L D   W A Y:   Saving Fields Data
			//      update_wpbm_option( 'wpbm_is_delete_if_deactive'
			//                       , WPBM_Settings_API::validate_checkbox_post('wpbm_is_delete_if_deactive') );  
			//      ( (isset( $_POST['wpbm_is_delete_if_deactive'] ))?'On':'Off') );
			*/

			return false;
		}

		
	/** Show Toolbar with Upload / List fields */
	function show_toolbar_upload() {		
		
		// Parameters for Ajax: 
		
		?><div  class="wpbm_listing_ics_bar"	 id="wpbm_listing_ics_bar"		 
				data-nonce="<?php echo wp_create_nonce( $nonce_name = 'wpbm_listing_ics_nonce_actn' ); ?>"	
				data-user-id="<?php echo get_current_user_id(); ?>"
			 ><?php
			?>
			<div class="wpbm_listing_div">
				<input type="text" 
					   class="wpbm_listing_url" name="wpbm_listing_url" id="wpbm_listing_url" 
					   placeholder="<?php _e( 'URL to .ics feed', 'booking-manager' ) ?>"				   
					   value="" wrap="off" 
					   />
				<?php if ( function_exists( 'wpbm_upload' ) ) {  ?>
					<a href="javascript:void(0)" class="button button-secondary wpbm_upload_btn"
							data-modal_title="<?php echo esc_attr( __( 'Choose file', 'booking-manager' ) ); ?>" 
							data-btn_title="<?php echo esc_attr( __( 'Insert file URL', 'booking-manager' ) ); ?>" 						   
							><?php _e('Upload / Select ', 'booking-manager' ); ?> <strong>(.ics)</strong></a>
				<?php } ?>
				<a class="button button-primary wpbm_listing_btn" href="javascript:void(0)"><?php _e('Show Events (.ics)', 'booking-manager'); ?></a>				
			</div>
			<?php 
			if ( function_exists( 'wpbm_upload' ) ) {																	// Get WPBM_Upload obj. instance
				
				$wpbm_upload = wpbm_upload();	

				$wpbm_upload->set_upload_button( '.wpbm_upload_btn' );

				$wpbm_upload->set_element_insert_url( '.wpbm_listing_url' );
			}
			?>
			<div class="clear"></div>
			<div class="wpbm_system_info_log0"></div>
			<div class="clear"></div>
		</div>
		<?php		
	}
	
	
	
    //                                                                              <editor-fold   defaultstate="collapsed"   desc=" C S S " >    
    public function css() {
        ?>
        <style type="text/css">
			.wpbm_listing_ics_bar .wpbm_listing_btn, 
			.wpbm_listing_ics_bar .wpbm_upload_btn{
				float:left;
				margin:9px 5px 10px 1px;	
			
			}		
			.wpbm_listing_ics_bar .wpbm_listing_div {
				float:left;
				width:100%;				
			}
			.wpbm_listing_ics_bar .wpbm_listing_br_selection,
			.wpbm_listing_ics_bar .wpbm_listing_url {
				float:left;
				width:28%;
				height: 2em;
				padding: 2px;
				border-radius: 0;
				margin:10px 5px 10px 1px;
				/* FixIn: 2.0.13.1 */
				min-height: 28px;
				height: 28px;
				margin: 9px 5px 10px 1px;
			}
			.wpbm_listing_ics_bar .wpbm_listing_url {
				width:50%;				
				padding: 2px 5px;
			}			
			.wpbm_system_info_log {
				font-size: 11px;
				line-height: 1.5em;
				/* border: 2px dashed #e85; */
				padding: 5px 20px;
				margin-top:10px;				
			}
			
 			#wpbm_textdata {				
				width: 100%;
				font-size: 1.4em;
				font-weight: 600;				
			}
 			/* iPad mini and all iPhones  and other Mobile Devices */
			@media (max-width: 782px) { 
				.wpbm_listing_ics_bar .wpbm_listing_url {
					width:100%;
				}
				.wpbm_page .wpbm_send_button {                															
					padding: 2px;										
					margin-top: 1px;
				}
			}
        </style>
        <?php
    }    
	//                                                                              </editor-fold>	
}
add_action('wpbm_menu_created', array( new WPBM_Page_Single() , '__construct') );    // Executed after creation of Menu


//                                                                              <editor-fold   defaultstate="collapsed"   desc=" A J A X    R e q u e s t " >    
////////////////////////////////////////////////////////////////////////////////
// AJAX  Request
////////////////////////////////////////////////////////////////////////////////

/** JavaScript for Ajax */
function wpbm_ics_listing_ajax_js() {
	
	$ajx_el_id = 'wpbm_listing_ics_bar';
	
	// In "wpbm-ajax.php" having this:			, 'WPBM_LISTING_ICS_URL' => 'admin'
	
	?>
	<script type="text/javascript">
		// Ajax Request
		jQuery( function ( $ ) {																						// Shortcut to  jQuery(document).ready(function(){ ... });

			jQuery( '.wpbm_listing_ics_bar' ).on( 'click', '.wpbm_listing_btn', function ( event ) {					// This delegated event, can be run, when DOM element added after page loaded

				wpbm_admin_show_message_processing( '' ); 

				var jq_el = jQuery( this ).closest( '.wpbm_listing_ics_bar' );

				var params_obj = {};
				params_obj.id      = jq_el.attr( 'id' );
				params_obj.nonce   = jq_el.attr( 'data-nonce' );
				params_obj.user_id = jq_el.attr( 'data-user-id' );
				
				params_obj.wpbm_listing_url			= jQuery( '#wpbm_listing_url' ).val();
//	params_obj.wpbm_listing_br_selection = 1;
//	if ( jQuery( '#wpbm_listing_br_selection option' ).length > 0 )
//		params_obj.wpbm_listing_br_selection = jQuery( '#wpbm_listing_br_selection option' ).filter( ':selected' ).val();
				
// console.log(params_obj);

				jQuery.post( wpbm_ajaxurl, {
											action:     'WPBM_LISTING_ICS_URL',
											user_id:    params_obj.user_id ,
											nonce:      params_obj.nonce,
											params:		params_obj
										},                                            
								function ( response_data, textStatus, jqXHR ) {											// success	
									
									var my_message = '<?php echo html_entity_decode( esc_js( __('Done' ,'booking-manager') ),ENT_QUOTES) ; ?>';
									wpbm_admin_show_message( my_message, 'info', 10000 , false );
								
									//console.log( response_data ); console.log( textStatus); console.log( jqXHR );     // Debug
									//jQuery( '.wpbm_system_info_log' ).show();				//Show Debug info
									response_data = response_data.replace( '{"response":"success"}', '' );
									jQuery( '.wpbm_system_info_log' ).html( response_data );                            // For ability to show response, add such  DIV element to page
									wpbm_scroll_to('#wpbm_settings_ics_listing_log_metabox' );
								}
						).fail( function ( jqXHR, textStatus, errorThrown ) {    
							wpbm_admin_show_message( '<strong style="text-transform: uppercase;">' + textStatus + '</strong> ~ ' + errorThrown , 'error', 5000 );
							if ( window.console && window.console.log ){ console.log( 'Ajax_Error', jqXHR, textStatus, errorThrown ); }     
						})  
						// .done( function ( data, textStatus, jqXHR ) {   if ( window.console && window.console.log ){ console.log( 'second success', data, textStatus, jqXHR ); }    })                
						// .always( function ( data_jqXHR, textStatus, jqXHR_errorThrown ) {   if ( window.console && window.console.log ){ console.log( 'always finished', data_jqXHR, textStatus, jqXHR_errorThrown ); }     })
						;

			});

		});		
	</script>
	<?php	
}


/** Ajax Response */
function wpbm_ajax_WPBM_LISTING_ICS_URL() {

	if ( ! isset( $_POST['params'] ) || empty( $_POST['params'] ) ) {
		exit;
	}

	// Check Security
	$action_nonce_name	= 'wpbm_listing_ics_nonce_actn';
	$nonce_post_key = 'nonce';		
	$result = check_ajax_referer( $action_nonce_name, $nonce_post_key );												// Check Security

	$is_show_debug_info = false;
	if ( function_exists( 'get_bk_option' ) )
		$is_show_debug_info = (  ( get_bk_option( 'booking_is_show_system_debug_log' ) == 'On' ) ? true : false );			// Based on "Booking Calendar" - "show_system_debug_log" option !!!
	
	if ( $is_show_debug_info )
		add_action( 'wpbm_show_debug', 'wpbm_start_showing_debug', 10, 1 );

	do_action( 'wpbm_show_debug', array( 'Import Parameters' , $_POST ) );												//  S_Y_S_T_E_M    L_O_G

	if ( empty( $_POST[ 'params' ][ 'wpbm_listing_url'] ) ) {		
		do_action( 'wpbm_admin_show_top_notice', __( 'No ics url feed', 'booking-manager' ), 'error', 5000 );					// N_O_T_I_C_E  in  H_E_A_D_E_R		
		return  false;
	}	

	$params = array(
					  'url'				=> esc_url_raw(  $_POST[ 'params' ][ 'wpbm_listing_url'] )						//FixIn: 2.0.14.3
					, 'from'			=> 'any'				//'today'		// '00:00 today'
					, 'from_offset'		=> ''
					, 'until'			=> 'any'				//'year-end'		// '00:00 today'
					, 'until_offset'	=> ''
					, 'max'				=> ''
					, 'is_all_dates_in' => true		
	);
	$listing_echo = wpbm_ics_get_listing( $params );

	if ( $is_show_debug_info )
		do_action( 'wpbm_show_debug', array( '$listing_echo', $is_show_debug_info, $listing_echo ) );											//  S_Y_S_T_E_M    L_O_G
	
	///////////////////////////////////////////////////////////////////////////////////////
	// Get Listing Shortcode
	///////////////////////////////////////////////////////////////////////////////////////
	ob_start();
	?>
	<div class="wpbm-settings-notice notice-info" 
		 style="text-align:left;border-top:1px solid #f0f0f0;border-right:1px solid #f0f0f0;line-height: 2em;font-size: 13px;margin:10px 0 20px;">
		<?php echo ( esc_js( __( 'Insert this shortcode into page for showing these events at front-end side of your website.', 'booking-manager' ) ) ); ?>
		<br>
		<code>[booking-manager-listing url='<?php echo esc_url(  $_POST[ 'params' ][ 'wpbm_listing_url'] ); ?>' from='any' until='any']</code> 		
	</div>
	<div class="clear" style="border-top: 2px dashed #e85;height:20px;"></div>
	<?php 
	$echo_results = ob_get_contents();
	
    ob_end_clean();
	///////////////////////////////////////////////////////////////////////////////////////
	
	echo $echo_results. $listing_echo;

	if ( $is_show_debug_info )		
		remove_action( 'wpbm_show_debug', 'wpbm_start_showing_debug', 10 );

	/*	
	if ( $is_show_debug_info ) {
		// Showingdebug log section
		?><script type="text/javascript"> jQuery( '.wpbm_system_info_log' ).show(); </script><?php
	}
	*/

	// send JSON
	// FixIn: 2.0.2.1		//Fix: We need to  comment this line,  because previously its possible that  we already  sent some messages,  and its does not correct  json format in this case.
							//Fix: of showing "parsererror ~ SyntaxError: JSON.parse: unexpected character at line 1 column 1 of the JSON data"
	//wp_send_json( array( 'response' => 'success' ) );												// Return JS OBJ: response_data = { response: "success" }
	wp_die( '', '', array( 'response' => null  ) );
}
//                                                                              </editor-fold>