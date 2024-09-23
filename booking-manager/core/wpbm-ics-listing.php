<?php 
/**
 * @version 1.0
 * @package ICS Listing functions
 * @subpackage Import / Export ICS
 * @category ICS
 * 
 * @author wpdevelop
 * @link https://oplugins.com/
 * @email info@oplugins.com
 *
 * @modified 2017-03-01
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly



// [booking-manager-listing url='https://server.com/feed.ics' from='2017-08-06' until='week' until_offset='4h' max=500]
// [booking-manager-listing url='https://server.com/feed.ics' from='today' from_offset='5d' until='year-end' until_offset='4h' max=500]
function wpbm_ics_get_listing( $attr ) {
 
	//                                                                              <editor-fold   defaultstate="collapsed"   desc=" Parse / validate  parameters " >    
	/////////////////////////////////////////////////////////////////////
	// Parse / validate  parameters
	/////////////////////////////////////////////////////////////////////
	$defaults = array(
				   	  'url' => ''
					, 'from' => 'any' //'today'		// '00:00 today'
					, 'from_offset' => ''
					, 'until' => 'any' //'year-end'		// '00:00 today'
					, 'until_offset' => ''
					, 'max' => ''
					, 'is_all_dates_in' => false		// Conditional of Dates checking.  TRUE - Remove event if al least 1 day not in conditional interval,  FALSE - save event, if at leat one date in conditional interval
	);
	$shortcode = array();

	foreach ( $attr as $param_name => $param_value ) {

		switch ( $param_name) {							// Validate Params

			case 'url':					
				$shortcode[ $param_name ] = esc_url_raw( $param_value );		// $shortcode[ 'url' ]
				break;
			
			case 'from':										// 'now', 'today', 'week', 'month-start', 'month-end', 'year-start', 'any', 'date' = 2017-08-07
				$shortcode[ $param_name ] = $param_value;
				break;
						
			case 'from_offset':									// 5d,  10h, 5m, 30s	--	if  from: { 'now', 'today', 'week', 'month-start', 'month-end', 'year-start' }
				$shortcode[ $param_name ] = $param_value;				
				break;
			
			case 'until':										// 'now', 'today', 'week', 'month-start', 'month-end', 'year-end', 'any', 'date' = 2017-08-07
				$shortcode[ $param_name ] = $param_value;
				break;
						
			case 'until_offset':								// 5d,  10h, 5m, 30s	--	if  until: { 'now', 'today', 'week', 'month-start', 'month-end', 'year-end' }
				$shortcode[ $param_name ] = $param_value;				
				break;
			
			case 'max': 
				$shortcode[ $param_name ] = intval( $param_value );				
				break;
			
			case 'is_all_dates_in':								// Conditional of Dates checking.  TRUE - Remove event if al least 1 day not in conditional interval,  FALSE - save event, if at leat one date in conditional interval
				$shortcode[ $param_name ] = intval( $param_value );				
				break;
			
			default:
				$shortcode[ $param_name ] = $param_value;
				break;
		}			
	}		
	$shortcode = wp_parse_args( $shortcode, $defaults );
	//                                                                              </editor-fold>


	if ( empty( $shortcode[ 'url' ] ) )		return '<strong>[WPBM Error]</strong> ' . __( 'No URL for .ics feed.', 'booking-manager' );

	
	//                                                                              <editor-fold   defaultstate="collapsed"   desc=" Parse ICS Feed " >    
	///////////////////////////////////////////////////////////////
	// Parse ICS Feed
	///////////////////////////////////////////////////////////////

	$ics_array = wpbm_ics_file_to_array( $shortcode[ 'url' ] );

	if ( is_wp_error( $ics_array ) ) {
		$error_message = $ics_array->get_error_message();
		return $error_message;		
	}

	if ( $ics_array !== false ) {
		if ( function_exists( 'wpbm_get_booking_fields_from_ics_array' ) ) {
			$bk_array = wpbm_get_booking_fields_from_ics_array( $ics_array[ 'events' ] );
		} else {
			$bk_array =  $ics_array[ 'events' ];
		}
	} else
		$bk_array = array();
	//                                                                              </editor-fold>
	
	// Sort Events
	$bk_array = wpbm_sort_events_by( $bk_array );
	
	// Filter events	
	$bk_array = wpbm_clear_events_by_dates( $bk_array, $shortcode );

	// Filter events	
	$bk_array = wpbm_clear_events_by_count( $bk_array, $shortcode );

	
	/** array ( Array ( [_BOOKING_DATES] => Array
							(
								[0] => 2014-07-24 00:00:00
								[1] => 2014-07-25 00:00:00
								[2] => 2014-07-26 00:00:00
							)

						[_BOOKING_SUMMARY] => Reserve Room #1
						[_BOOKING_DESCRIPTION] => Just  description  about this event!:)
						[_BOOKING_LOCATION] => London
						[_BOOKING_UID] => lnrjn92gsavrkkpodtbvm3plfs@google.com
						[_BOOKING_MODIFIED] => 2014-07-20 07:21:43
					)
		            , ....
	 *  )
	 * 
	 */	


	// Filter events	
	$bk_array = wpbm_remove_dates_from_event_not_in_condition( $bk_array, $shortcode );
	// Re-Sort Events again,  because we was removed some dates
	$bk_array = wpbm_sort_events_by( $bk_array );
	

	//                                                                              <editor-fold   defaultstate="collapsed"   desc=" Listing Template " >    
	///////////////////////////////////////////////////////////////
	// Listing Template
	///////////////////////////////////////////////////////////////	

	
	$listing_template = '';
	// Simple Events listing
	foreach ( $bk_array as $evnt ) {
				
		$echo_row = wpbm_ics_get_listing_row( $evnt );
		
		$listing_template .= $echo_row;		
	}	
	//                                                                              </editor-fold>
	
	
	return $listing_template;
}



/** Get .ics Event Listing Row
 * 
 * @param array $evnt
 * @return string
 */
function wpbm_ics_get_listing_row( $evnt ) {

	/**
		Array(
            [_BOOKING_DATES] => Array
                (
                    [0] => 2014-07-24 00:00:00
                    [1] => 2014-07-25 00:00:00
                    [2] => 2014-07-26 00:00:00
                )

            [_BOOKING_SUMMARY]		=> Reserve Room #1
            [_BOOKING_DESCRIPTION]	=> Just  description  about this event!:)
            [_BOOKING_LOCATION]		=> London
            [_BOOKING_UID]			=> 9vxvxvv2gsavrkkpodtbvm3plfsvxcvxcvcxv@google.com
            [_BOOKING_MODIFIED]		=> 2014-07-20 07:21:43
        )
	 */
	
	///////////////////////////////////////////////////////////////
	// Listing Template
	///////////////////////////////////////////////////////////////		
	$row_template = get_wpbm_option( 'wpbm_listing_template' );                 
	
	// Normilize
	$replace_array = array();
	foreach ( $evnt as $key => $value ) {
		$key = str_replace( '_BOOKING_', '', $key );
		if ( is_array( $value ) )
			$value = implode( ',', $value );
		$replace_array[ $key ] = $value;
	}

	//FixIn: 2.0.11.4
	if (function_exists('wpbc_get_bookings_url')) {

		$my_booking_id               = wpbm_get_booking_id_by_UID( $replace_array['UID'] );
		$replace_array['BOOKING_ID'] = $my_booking_id;

		//FixIn: 2.0.11.5
		$replace_array['BOOKING_LINK'] = htmlspecialchars_decode(
																	//    '<a href="' .
																	esc_url( wpbc_get_bookings_url() . '&view_mode=vm_listing&tab=actions&wh_booking_id=' . $my_booking_id )
																	//    . '">' . __('here', 'booking') . '</a>'
		);
	}

	//FixIn: 2.0.6.1 - Set timezone frrom  Booking > Settings > Sync  page for booking listing shortcode
	$tzid = get_bk_option( 'booking_gcal_timezone' );
	if ( ! empty( $tzid ) ) {
		$dates_array = explode( ',', $replace_array['DATES'] );
		foreach ( $dates_array as $dk => $day ) {
			//$dates_array[ $dk ]  = ZDateHelper::toLocalDateTime( get_gmt_from_date( $day ), $tzid );
			//debuge($day);
			$dates_array[ $dk ] = ZDateHelper::toLocalDateTime( $day, $tzid );
		}
		$replace_array['DATES'] = implode( ',', $dates_array );
	}

	if ( ! empty( $replace_array['DATES'] ) )
		$replace_array['DATES'] = wpbm_get_dates_short_format( $replace_array['DATES'] );
	
	$echo_row = wpbm_replace_shortcodes( $row_template, $replace_array );
	
	return $echo_row;
}


	
/** Sort events by Check In days
 * 
 * @param array $ics_events_arr
 * @param string $sort_key		- Default: '_BOOKING_DATES'
 * @return array
 */	
function wpbm_sort_events_by( $ics_events_arr, $sort_key = '_BOOKING_DATES' ) {
		
	// Get array with check in date
	$check_in_arr = array();	
	foreach ( $ics_events_arr as $key => $event_arr) {
		$event_dates = $event_arr[ $sort_key ];
		sort( $event_dates );
		$check_in_arr[ $key ] = $event_dates[ 0 ];
	}
	
	asort( $check_in_arr, SORT_STRING );	// Sort an array and maintain index association
	
	// Sort inpur arr,  by keys from  previous check in array
	$sorted_events_arr = array();
	foreach ( $check_in_arr as $key => $event_check_in) {
		$sorted_events_arr[] = $ics_events_arr[ $key ];
	}
	
	return $sorted_events_arr;
}



// TODO:
// - list events by days and not events ??? currently if (0) {...} 