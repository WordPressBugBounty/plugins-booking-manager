<?php /**
 * @version 1.0
 * @description Notices Class
 * @category Show system Notices
 * @author wpdevelop
 *
 * @web-site https://oplugins.com/
 * @email info@oplugins.com 
 * 
 * @modified 2015-11-13
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly

/** Showing our system notices in admin panel */
class WPBM_Notices {
    
	
    function __construct() {
    
		// Hooks for showing notices only at specific admin pages
        add_action( 'wpbm_hook_wpbm_page_header',		array( $this, 'show_system_notices' ) );	
		add_action( 'wpbm_settings_after_header',	array( $this, 'show_system_notices' ) );
    }    
	
	
	/** Check  and show some system  messages 
	 * 
	 * @param array $page_arr					 array( 'page' => $this->in_page() ) ||  array( 'page' => $this->in_page(), 'subpage' => 'emails_settings' )
	 */
	public function show_system_notices( $page_arr ) {

		if ( ! in_array( $page_arr, array( 'oplugins', 'opl-settings' ) ) ) 
			return false;
		
				
		///////////////////////////////////////////////////////////
		$notice_id = 'wpbm_system_notice_free_instead_paid';
		///////////////////////////////////////////////////////////
		if (	    wpbm_is_updated_paid_to_free()
				&& ( ! wpbm_section_is_dismissed( $notice_id ) )
			// || true 
		) {

			?><div  id="<?php echo $notice_id; ?>" 
					class="wpbm_system_notice wpbm_is_dismissible wpbm_is_hideable updated notice-warning"
					data-nonce="<?php echo wp_create_nonce( $nonce_name = $notice_id . '_wpbmnonce' ); ?>"	
					data-user-id="<?php echo get_current_user_id(); ?>"
				><?php 
			
			wpbm_x_dismiss_button();
			
			echo '<strong>' . __( 'Warning!', 'booking-manager' ) . '</strong> ';
			printf( __( 'Probably you updated your paid version of Booking Manager by free version or update process failed. You can request the new update of your paid version at %1sthis page%2s.', 'booking-manager' )
					, '<a href="https://oplugins.com/plugins/wp-booking-manager/booking-manager-update/" target="_blank">', '</a>' );
			
			?></div><?php
		}       
		///////////////////////////////////////////////////////////
		
			
		if ( ! in_array( $page_arr, array( 'oplugins' ) ) ) 
			return false;
		
		
		
			
		
		///////////////////////////////////////////////////////////
		$notice_id = 'wpbm-panel-get-started';
		///////////////////////////////////////////////////////////
		
		if ( ! wpbm_section_is_dismissed( $notice_id ) ) {			
			?>
			<style type="text/css" media="screen">
				/* WPBM Welcome Panel */                
				.wpbm-panel .welcome-panel {
					background: linear-gradient(to top, #F5F5F5, #FAFAFA) repeat scroll 0 0 #F5F5F5;
					border-color: #DFDFDF;
					position: relative;
					overflow: auto;
					margin: 10px 0;
					padding: 23px 10px 12px;
					border-width: 1px;
					border-style: solid;
					border-radius: 3px;
					font-size: 13px;
					line-height: 2.1em;
				}
				.wpbm-panel .welcome-panel h3 {
					margin: 0;
					font-size: 21px;
					font-weight: 400;
					line-height: 1.2;
				}
				.wpbm-panel .welcome-panel h4 {
					margin: 1.33em 0 0;
					font-size: 13px;
					font-weight: 600;
				}
.wpbm-panel .welcome-panel .wpbm-help-message h4 {	
	font-size: 1.1em;	
}
				.wpbm-panel .welcome-panel a{
					color:#21759B;
				}
				.wpbm-panel .welcome-panel .about-description {
					font-size: 16px;
					margin: 0;
				}
				.wpbm-panel .welcome-panel .welcome-panel-close {
					position: absolute;
					top: 5px;
					right: 10px;
					padding: 8px 3px;
					font-size: 13px;
					text-decoration: none;
					line-height: 1;
				}
				.wpbm-panel .welcome-panel .welcome-panel-close:before {
					content: ' ';
					position: absolute;
					left: -12px;
					width: 10px;
					height: 100%;
					background: url('../wp-admin/images/xit.gif') 0 7% no-repeat;
				}
				.wpbm-panel .welcome-panel .welcome-panel-close:hover:before {
					background-position: 100% 7%;
				}
				.wpbm-panel .welcome-panel .button.button-hero {
					margin: 15px 0 3px;
				}
				.wpbm-panel .welcome-panel-content {
					margin-left: 13px;
					max-width: 1500px;
				}
				.wpbm-panel .welcome-panel .welcome-panel-column-container {
					clear: both;
					overflow: hidden;
					position: relative;
				}
.wpbm-panel .welcome-panel .welcome-panel-column-container.wpbm-help-message {
	background: #fff;
	border: 1px solid #eee;
	border-radius: 1px;					
}
				.wpbm-panel .welcome-panel .welcome-panel-column {
					width: 32%;
					min-width: 200px;
					float: left;
				}
				.ie8 .wpbm-panel .welcome-panel .welcome-panel-column {
					min-width: 230px;
				}
				.wpbm-panel .welcome-panel .welcome-panel-column:first-child {
					width: 36%;
				}
				.wpbm-panel .welcome-panel-column p {
					margin-top: 7px;
				}
				.wpbm-panel .welcome-panel .welcome-icon {
					background: none;    
					display: block;
					padding: 2px 0 8px 2px;    
				}
				.wpbm-panel .welcome-panel .welcome-add-page {
					background-position: 0 2px;
				}
				.wpbm-panel .welcome-panel .welcome-edit-page {
					background-position: 0 -90px;
				}
				.wpbm-panel .welcome-panel .welcome-learn-more {
					background-position: 0 -136px;
				}
				.wpbm-panel .welcome-panel .welcome-comments {
					background-position: 0 -182px;
				}
				.wpbm-panel .welcome-panel .welcome-view-site {
					background-position: 0 -274px;
				}
				.wpbm-panel .welcome-panel .welcome-widgets-menus {
					background-position: 1px -229px;
					line-height: 14px;
				}
				.wpbm-panel .welcome-panel .welcome-write-blog {
					background-position: 0 -44px;
				}
				.wpbm-panel .welcome-panel .welcome-panel-column ul {
					margin: 0.8em 1em 1em 0;
				}
				.wpbm-panel .welcome-panel .welcome-panel-column li {
					line-height: 1.7em;
					list-style-type: none;
					margin:0;
					padding:0;
				}
.wpbm-panel .welcome-panel .wpbm-help-message .welcome-panel-column li {				
	list-style-type: none;
	list-style-position: inside;
	line-height: 1.7em;
	margin:15px 0 10px;
	font-size: 13px;
}
.wpbm-panel .welcome-panel .wpbm-help-message .welcome-panel-column li div {
	display: inline;
}
.welcome-panel-footer-collumn {
	width:33%;
	text-align: center;
	font-style:italic;
	float:left;
}
				@media screen and (max-width: 870px) {
.welcome-panel-footer-collumn {
	width:100%;
	float:none;
}
					
					.wpbm-panel .welcome-panel .welcome-panel-column,
					.wpbm-panel .welcome-panel .welcome-panel-column:first-child {
						display: block;
						float: none;
						width: 100%;
					}
					.wpbm-panel .welcome-panel .welcome-panel-column li {
						display: inline-block;
						margin-right: 13px;
					}
					.wpbm-panel .welcome-panel .welcome-panel-column ul {
						margin: 0.4em 0 0;
					}
					.wpbm-panel .welcome-panel .welcome-icon {
						padding-left: 25px;
					}
				}
			</style>                
			<div	id="<?php echo $notice_id ?>" 
					class="wpbm-panel wpbm_is_dismissible wpbm_is_hideable "
					data-nonce="<?php echo wp_create_nonce( $nonce_name = $notice_id . '_wpbmnonce' ); ?>"	
					data-user-id="<?php echo get_current_user_id(); ?>"		 

				 ><div class="welcome-panel"><?php 

			wpbm_x_dismiss_button( '&times;', array( 'style' => 'font-size:1.5em;margin-top:-0.8em;' ) ); 
			
			?>
			<div class="welcome-panel-content">
				<p class="about-description"><?php //_e( 'We&#8217;ve assembled some links to get you started:', 'booking-manager'); 
					$message_ics = sprintf( __( 'List events or bookings from different sources at your website with easy via .ics feeds', 'booking-manager' ) ); 
					$message_ics = str_replace( array( '.ics' ), array( '<strong>.ics</strong>' ), $message_ics );
					echo $message_ics;
				?></p>
				<div class="welcome-panel-column-container wpbm-help-message ">
					<div class="welcome-panel-column">
						<h4><?php _e( 'How to start showing events from .ics feeds (files)?', 'booking-manager'); ?></h4>
						<ul>                          
							<li><div class="welcome-icon"><?php 
									echo '1. '; 
									_e( 'Upload .ics file via this page or simply copy URL to .ics feed from external website.', 'booking-manager' );
								?></div>
							</li>                            
							<li><div class="welcome-icon"><?php							
									echo '2. ';
									printf( __( 'Insert into page the shortcode for listing events from .ics feed.', 'booking-manager' ) );
									echo '<br/>';
									printf( __( 'Read more about %sshortcode configuration here%s.', 'booking-manager' ), '<a href="https://oplugins.com/plugins/wp-booking-manager/booking-manager-help/">', '</a>' );
								?></div>
							</li>							
					</div>
					<div class="welcome-panel-column">
						<h4><?php _e( 'Next Steps', 'booking-manager'); ?></h4>
						<ul>
							<li><div class="welcome-icon"><?php
								printf( __( 'Configure different options in %sSettings%s.' , 'booking-manager'),
									'<a href="' . esc_url( wpbm_get_settings_url() ) . '">', '</a>' );
							?></div></li>                            
							<li><div class="welcome-icon"><?php
								printf( __( 'Configure your predefined %sEvents Listing Template%s.', 'booking-manager'),
									'<a href="' . esc_url(  wpbm_get_settings_url() . '&subtab=listing' ) . '">', '</a>' );
							?></div></li>
						</ul>
					</div>
					<div class="welcome-panel-column welcome-panel-last">
						<h4><?php _e( 'Integration with', 'booking-manager'); ?> <a href="https://wordpress.org/plugins/booking/" target="_blank">Booking Calendar</a></h4>
						<?php  
						
							printf( __( 'This plugin have native integration with %s plugin.', 'booking-manager' )
								  , '<a href="https://wordpress.org/plugins/booking/" target="_blank"><strong>Booking Calendar</strong></a>' );
							
							echo '<br />';
							printf( __( 'It can sync bookings from %s with different sources', 'booking-manager' )
								  , '<a href="https://wordpress.org/plugins/booking/" target="_blank"><strong>Booking Calendar</strong></a>' );
							
							echo ' <em>(<strong><a href="https://www.airbnb.com/help/article/99/how-do-i-sync-my-airbnb-calendar-with-another-calendar" target="_blank">Airbnb</a></strong>, '
							. '<strong><a href="https://partnersupport.booking.com/hc/en-us/articles/213424709-How-do-I-export-my-calendar-" target="_blank">Booking.com</a></strong>, '
							. '<strong><a href="https://help.homeaway.com/articles/How-do-I-export-my-calendar-data-to-a-Google-calendar" target="_blank">HomeAway</a></strong>, '
							. '<strong><a href="https://rentalsupport.tripadvisor.com/articles/FAQ/noc-How-does-calendar-sync-work" target="_blank">TripAdvisor</a></strong>, '
							. '<strong><a href="https://help.vrbo.com/articles/How-do-I-export-my-calendar-data-to-a-Google-calendar" target="_blank">VRBO</a></strong>, '
							. '<strong><a href="https://helpcenter.flipkey.com/articles/FAQ/noc-How-does-calendar-sync-work" target="_blank">FlipKey</a></strong> '
							. str_replace( array( '.ics', 'iCalendar' ), array( '<strong>.ics</strong>', '<strong>iCalendar</strong>' ), 
										 __( 'and any other calendar that uses .ics format', 'booking-manager' )
										)
							. ')</em>.<br/>'												
						?>
					</div>
				</div>
				<div class="welcome-panel-footer">
					<div class="welcome-panel-footer-collumn"><?php
								printf( __( 'Still having questions? Contact %sSupport%s.', 'booking-manager'),
									'<a href="https://oplugins.com/plugins/booking-manager/#support" target="_blank">',
									'</a>' );
					?></div><div class="welcome-panel-footer-collumn"><?php
								printf( __( 'Do you require new feature? Send your %ssuggestion%s to us.', 'booking-manager'),
									'<a href="mailto:newfeature@oplugins.com?Subject=booking-manager" target="_blank">',
									'</a>' );
					?></div><div class="welcome-panel-footer-collumn"><?php								
					printf( __( 'Need even more functionality? Check %s higher versions %s', 'booking-manager'),
							'<a href="https://oplugins.com/plugins/booking-manager/" target="_blank">',
							'</a>' 
						); ?>
					</div>	
					<div style="clear:both;"></div>
				</div>
				
			</div> 
			<?php
        
			?></div></div><?php			
		}
		
		
		
	}
	
}
 
new WPBM_Notices();																// Run