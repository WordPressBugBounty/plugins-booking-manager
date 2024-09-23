<?php 
/**
 * @version 1.0
 * @package ICS functions
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


require_once( WPBM_PLUGIN_DIR . '/assets/libs/icalendar/zapcallib.php' );


/** Download Feed .ICS  &  Parse to Array
 * 
 * @param string $ics_url				- URL to ics file
 * @return array | WP_Error on error		- Array  return array( 'events' => $ics_events, 'data' => $ics_data );	
														$ics_events = array (
																[DTSTART] => 20140716
																[DTEND] => 20140717
																[DTSTAMP] => 20170627T112020Z
																[UID] => d3ihiuv8nqbti9djmpetrgn5ok@google.com
																[CREATED] => 20140628T081753Z
																[DESCRIPTION] => 
																[LAST-MODIFIED] => 20140628T081753Z
																[LOCATION] => 
																[SEQUENCE] => 0
																[STATUS] => CONFIRMED
																[SUMMARY] => Test booking here
																[TRANSP] => TRANSPARENT
																[RRULE] => FREQ=DAILY;UNTIL=20140717
																[_BOOKING_DATES] => Array
																	(
																		[0] => 2014-07-16 00:00:00
																		[1] => 2014-07-17 00:00:00
																		[2] => 2014-07-17 00:00:00
																	)

																[_BOOKING_SUMMARY] => Test booking here
																[_BOOKING_DESCRIPTION] => 
																[_BOOKING_LOCATION] => 
																[_BOOKING_GUID] => d3ihiuv8nqbti9djmpetrgn5ok@google.com
																[_BOOKING_MODIFIED] => 2014-06-28 08:17:53
															)
*/
function wpbm_ics_file_to_array( $ics_url ) {

	$ics_content = wpbm_get_ssl_page_content( $ics_url );

	if ( false === $ics_content )
		return new WP_Error( 'wpbm_ics_url_error', '<strong>[WPBM Error]</strong> ' . __( 'Could not download URL' ) . ' ' . $ics_url , 'wrong_url' );
	

	$ics_events = wpbm_ics_content_to_array( $ics_content );
	
	return $ics_events;
}


/** Parse Content .ICS to array
 * 
 * @param string $ics_content		- ICS content
 * @return array | false on error	- ICS Events Array
 * @return array | false on error		- ICS Events	Array  return array( 'events' => $ics_events, 'data' => $ics_data );	
														$ics_events = array (
																[DTSTART] => 20140716
																[DTEND] => 20140717
																[DTSTAMP] => 20170627T112020Z
																[UID] => d3ihiuv8nqbti9djmpetrgn5ok@google.com
																[CREATED] => 20140628T081753Z
																[DESCRIPTION] => 
																[LAST-MODIFIED] => 20140628T081753Z
																[LOCATION] => 
																[SEQUENCE] => 0
																[STATUS] => CONFIRMED
																[SUMMARY] => Test booking here
																[TRANSP] => TRANSPARENT
																[RRULE] => FREQ=DAILY;UNTIL=20140717
																[_BOOKING_DATES] => Array
																	(
																		[0] => 2014-07-16 00:00:00
																		[1] => 2014-07-17 00:00:00
																		[2] => 2014-07-17 00:00:00
																	)

																[_BOOKING_SUMMARY] => Test booking here
																[_BOOKING_DESCRIPTION] => 
																[_BOOKING_LOCATION] => 
																[_BOOKING_GUID] => d3ihiuv8nqbti9djmpetrgn5ok@google.com
																[_BOOKING_MODIFIED] => 2014-06-28 08:17:53
															)
 */
function wpbm_ics_content_to_array( $ics_content ) {
	
	if ( empty( $ics_content ) )
		return false;

	if ( false === strpos( $ics_content, 'VEVENT' ) ) {
		return new WP_Error( 'wpbm_ics_error', '<strong>[WPBM Error]</strong> ' . __( 'File does not contain events' ) . ' ' , 'wrong_ics_content' );
	}
	
	$ics_content = trim($ics_content);                      //FixIn: 2.1.9.2
	$icalobj = new ZCiCal( $ics_content );					// create the ical object

	
	$events_count = 0;
	$ics_events = array();

	
	if ( isset( $icalobj->tree->child ) ) {
		foreach ( $icalobj->tree->child as $node ) {
			
			if ( $node->getName() == "VEVENT" ) {

				$ics_events[ $events_count ] = array();

				foreach ( $node->data as $key => $value ) {
					
					$ics_events[ $events_count ][ $key ] = $value->getValues();
				}
				$events_count++;
			} 
		}
	}
	
	
	/** Data,  like: array (	[PRODID] => -//Calendar Labs//Calendar 1.0//EN
								[VERSION] => 2.0
								[CALSCALE] => GREGORIAN
								[METHOD] => PUBLISH
								[X-WR-CALNAME] => Us Holidays
								[X-WR-TIMEZONE] => America/New_York
	 */
	$ics_data = array();
	if ( isset( $icalobj->tree->data ) ) {
		foreach ( $icalobj->tree->data as $node ) {			
			$ics_data[ $node->getName() ] = $node->getValues();			
		}
	}


	// Add Booking fields,  like "_BOOKING_DATES, _BOOKING_SUMMARY, ..." 
	foreach ( $ics_events as $i_key => $i_event) {

		//FixIn: 2.0.23.1
		/**
		 *  The spec says that if DTSTART has a DATE data type, and there is no DTEND then the event finishes at the end of the day that it starts.
		 *  But if DTSTART has a full DATE-TIME data type, and there is no DTEND then it finishes at the same time that it starts.
         *
		 * It's in section 3.6.1 of RFC 5545 (https://www.rfc-editor.org/rfc/rfc5545#page-54):
         *
		 *  For cases where a "VEVENT" calendar component specifies a "DTSTART" property with a DATE value type but no "DTEND" nor "DURATION" property,
		 *  the event's duration is taken to be one day. For cases where a "VEVENT" calendar component specifies a "DTSTART" property with a DATE-TIME value
		 *  type but no "DTEND" property, the event ends on the same calendar date and time of day specified by the "DTSTART" property.
		 */
		if (      ( empty( $ics_events[ $i_key ]['DTEND'] ) )
		     && ( ! empty( $ics_events[ $i_key ]['DTSTART'] ) ) ) {

			$ics_events[ $i_key ]['DTEND'] = 'null';
		}


		$dates_arr = wpbm_get_dates_arr_from_ics_dates(   $ics_events[ $i_key ][ 'DTSTART' ]
														, $ics_events[ $i_key ][ 'DTEND' ]
														, ( empty( $ics_events[ $i_key ]['RRULE'] ) ? '' : $ics_events[ $i_key ]['RRULE'] ) 
														, ( empty( $ics_events[ $i_key ]['RDATE'] ) ? '' : $ics_events[ $i_key ]['RDATE'] ) 
						);
		
		
		// Bookings Fields
		$ics_events[ $i_key ]['_BOOKING_DATES']		= $dates_arr;		

		$ics_events[ $i_key ]['_BOOKING_SUMMARY'] = '';
		$ics_events[ $i_key ]['_BOOKING_DESCRIPTION'] = '';
		$ics_events[ $i_key ]['_BOOKING_LOCATION'] = '';
		$ics_events[ $i_key ]['_BOOKING_UID'] = '';
		$ics_events[ $i_key ]['_BOOKING_MODIFIED'] = '';
		$ics_events[ $i_key ]['_BOOKING_ATTENDEE'] = '';

		if ( ! empty( $ics_events[ $i_key ]['SUMMARY'] ) ) {
			$ics_events[ $i_key ]['_BOOKING_SUMMARY'] = $ics_events[ $i_key ]['SUMMARY'];
		}
		if ( ! empty( $ics_events[ $i_key ]['DESCRIPTION'] ) ) {
			$ics_events[ $i_key ]['_BOOKING_DESCRIPTION'] = $ics_events[ $i_key ]['DESCRIPTION'];
		}
		if ( ! empty( $ics_events[ $i_key ]['LOCATION'] ) ) {
			$ics_events[ $i_key ]['_BOOKING_LOCATION'] = $ics_events[ $i_key ]['LOCATION'];
		}

		if ( ! empty( $ics_events[ $i_key ]['ATTENDEE'] ) ) {
			$ics_events[ $i_key ]['_BOOKING_ATTENDEE'] = $ics_events[ $i_key ]['ATTENDEE'];
		}
		// 15ig8t0i739kajgjc8ekc386dt@google.com	-  ID of event from  ICS
		// 15ig8t0i739kajgjc8ekc386dt_20170815		-  ID of event during import from Google Calendar  (probably  created by ID before @ + first  date)
		if ( ! empty( $ics_events[ $i_key ]['UID'] ) ) {
			$ics_events[ $i_key ]['_BOOKING_UID'] = $ics_events[ $i_key ]['UID'];
		}
		if ( ! empty( $ics_events[ $i_key ]['LAST-MODIFIED'] ) ) {
			$ics_events[ $i_key ]['_BOOKING_MODIFIED'] = date_i18n( 'Y-m-d H:i:s', ZDateHelper::fromiCaltoUnixDateTime( $ics_events[ $i_key ]['LAST-MODIFIED'] ) );
		}
		
	}
	
	return array( 'data' => $ics_data, 'events' => $ics_events );	
}


/**  Get Dates array from START & END times & RULES
 *   Examples:			2017-07-10 00:00:00  --  2017-07-13 00:00:00		on		FREQ=MONTHLY;COUNT=4;BYDAY=2MO
 *				OR      2017-06-14 12:00:00  --  2017-06-14 15:30:00		on		FREQ=WEEKLY;COUNT=3;BYDAY=WE,SA
 * 
 * @param string $start_date_ics		// 20170614T120000
 * @param string $end_date_ics			// 20170614T153000
 * @param string $ics_rules				// FREQ=WEEKLY;COUNT=3;BYDAY=WE,SA
 * @param string $ics_rdates			// 20170826T120001,20170830T120001
 * @return array						// Array (
												[0] => 2017-06-14 12:00:01
												[1] => 2017-06-14 15:30:02
												[2] => 2017-06-17 12:00:01
												[3] => 2017-06-17 15:30:02
												[4] => 2017-06-21 12:00:01
												[5] => 2017-06-21 15:30:02
											)
 */
function wpbm_get_dates_arr_from_ics_dates( $start_date_ics, $end_date_ics, $ics_rules , $ics_rdates ) {

		//FixIn: 2.0.23.1
		/**
		 *  The spec says that if DTSTART has a DATE data type, and there is no DTEND then the event finishes at the end of the day that it starts.
		 *  But if DTSTART has a full DATE-TIME data type, and there is no DTEND then it finishes at the same time that it starts.
         *
		 * It's in section 3.6.1 of RFC 5545 (https://www.rfc-editor.org/rfc/rfc5545#page-54):
         *
		 *  For cases where a "VEVENT" calendar component specifies a "DTSTART" property with a DATE value type but no "DTEND" nor "DURATION" property,
		 *  the event's duration is taken to be one day. For cases where a "VEVENT" calendar component specifies a "DTSTART" property with a DATE-TIME value
		 *  type but no "DTEND" property, the event ends on the same calendar date and time of day specified by the "DTSTART" property.
		 */
		if ('null' === $end_date_ics) {
			$end_date_ics   = wpbm_ics_date_reset_seconds( $start_date_ics );						// 20170614T120000

			$start_date_unix_calc = ZDateHelper::fromiCaltoUnixDateTime(
																		wpbm_ics_date_reset_seconds( $start_date_ics )
											 );		// 1499644800	- 20170710

			$end_date_ics = strtotime( '+1 minute',  $start_date_unix_calc );
			$end_date_ics = ZDateHelper::fromUnixDateTimetoiCal( $end_date_ics );
		}


		$start_date_ics = wpbm_ics_date_reset_seconds( $start_date_ics );					// 20170614T120000			
		$end_date_ics   = wpbm_ics_date_reset_seconds( $end_date_ics );						// 20170614T120000

																						// FREQ=MONTHLY;COUNT=4;BYDAY=2MO - Rules
		$start_date_unix	= ZDateHelper::fromiCaltoUnixDateTime( $start_date_ics );		// 1499644800	- 20170710
		$end_date_unix		= ZDateHelper::fromiCaltoUnixDateTime( $end_date_ics );			// 1499904000	- 20170713

		$time_difference = $end_date_unix - $start_date_unix;							// 259200 - Difference in seconds

		$start_date = date_i18n( 'Y-m-d H:i:s', $start_date_unix );						// 2017-07-10 00:00:00
		$end_date   = date_i18n( 'Y-m-d H:i:s', $end_date_unix );						// 2017-07-13 00:00:00	

		// Get Unix Time for Strat Date at  midnight -- 2017-07-10
		$start_date_midnight_unix = strtotime( date_i18n( 'Y-m-d 00:00:00', $start_date_unix ) );			
		$start_date_midnight = date_i18n( 'Y-m-d H:i:s', $start_date_midnight_unix );	// Same in MySQL format: 2017-07-10 00:00:00

		////////////////////////////////////////////////////////////////////////////////////////////////////////////

		$internal_dates =array();

		// Get Unix Time for Strat Date at  midnight -- 2017-07-10
		$end_date_midnight_unix = strtotime( date_i18n( 'Y-m-d 00:00:00', $end_date_unix ) );				
		$end_date_midnight = date_i18n( 'Y-m-d H:i:s', $end_date_midnight_unix );	// Same in MySQL format: 2018-01-14 00:00:00
		
		// Get internal days between START and END dates:								// array( [0] => 1499731200 [1] => 1499817600 )
		// Need to  check  relative to  the END MIDNIGHT, becase if 
		// we have END DATE for specific time, like		2018-01-14 02:30:02		, we do NOT have this date as		2018-01-14 00:00:00
		while( ( $start_date_midnight_unix + ( 60*60*24 ) ) < $end_date_midnight_unix ) {
			$start_date_midnight_unix += 60*60*24;										// Get next day
			$internal_dates[] = $start_date_midnight_unix;
		}

		////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// Get Initial  Dates interval
		$initial_time_interval_unix = array();

		// In case if we have start time, like 15:00:00 we will trasnform  it to 15:00:01 - for internal usage of Booking Calendar 
		if ( substr( $start_date, 11 ) != '00:00:00' )	$initial_time_interval_unix[] = $start_date_unix + 1;			// Start time for specific time slot
		else											$initial_time_interval_unix[] = $start_date_unix ;				// Full date

		foreach ( $internal_dates as $internal_date_unix ) {
			$initial_time_interval_unix[] = $internal_date_unix;
		}

		// Same for end date/time +2 seconds for having time like 10:00:02
		if ( substr( $end_date, 11 ) != '00:00:00' )	$initial_time_interval_unix[] = $end_date_unix - 8;			    // End time for specific time slot      //FixIn: 2.0.22.2       //FixIn: 9.0.1.3      //FixIn: 2.0.21.1
		// We do not need to  add END date,  if the TIME == '00:00:00',  because EVENT ended at  previous date

		////////////////////////////////////////////////////////////////////////////////////////////////////////////
			
		// Here we are getting SHIFT in  seonds starting from INITIAL start DATE_TIME	
		$initial_time_interval_shift_unix = array();
		foreach ( $initial_time_interval_unix as $internal_date_unix ) {
			$initial_time_interval_shift_unix[] = $internal_date_unix - $start_date_unix;
		}

		// $initial_time_interval_shift_unix >>>   FULL DAYS:	array( [0] => 0 [1] => 86400 [2] => 172800 )
		// $initial_time_interval_shift_unix >>>   TIMES:		array( [0] => 1 [1] => 12602 )

		
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		// Get DATES depsnding from RULES imported from ICS
		$ics_start_dates_unix = array();
		if ( ! empty( $ics_rules ) ) {
			
			// Parse RULE and Get Dates array
			$rDates = new ZCRecurringDate(
										      $ics_rules
											, strtotime( $start_date_ics )
										);
			$ics_start_dates_unix = $rDates->getDates();							// MAX DATE: strtotime( $ics_events[ $i_key ][ 'DTEND' ] )  );
			sort( $ics_start_dates_unix );											// Sort
			$ics_start_dates_unix = array_unique( $ics_start_dates_unix );			// Remove Duplicates
		}
		// Example: Array ( [0] => 1499644800 [1] => 1502668800 [2] => 1505088000  [3] => 1507507200 )			or		array()

		// If we are having rDates
		if ( ! empty( $ics_rdates ) ) {
			$ics_rdates = explode( ',', $ics_rdates );

			foreach ( $ics_rdates as $ics_rdates_key => $ics_rdates_value ) {
				
				$ics_rdates_value = wpbm_ics_date_reset_seconds( $ics_rdates_value );					// 20170614T120000			
				$ics_rdates_value = ZDateHelper::fromiCaltoUnixDateTime( $ics_rdates_value );			// // 1497441600								
				$ics_rdates[ $ics_rdates_key ] = $ics_rdates_value;				
			}			
			// We need to  add also  to these array  start day
			$ics_rdates[] = strtotime( $start_date_ics );
			
			sort( $ics_rdates );													// Sort
	
			$ics_start_dates_unix = array_merge( $ics_start_dates_unix, $ics_rdates );
			$ics_start_dates_unix = array_unique( $ics_start_dates_unix );			// Remove Duplicates
		}
		
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				
		$dates_arr_unix = array();

		// We are having some RULES 
		if ( ! empty( $ics_start_dates_unix ) ) {			
			foreach ( $ics_start_dates_unix as $ics_start_date_unix ) {				// Depend from rules, we are having starting interval  of our EVENT
				
				foreach ( $initial_time_interval_shift_unix as $shift_unix ) {		// Because EVENT can have several  dates or times, we add our SHIFT interval from INITIAL
					
					$dates_arr_unix[] = $ics_start_date_unix + $shift_unix;
				}		
			}
		} else {	// No Rules
			
			foreach ( $initial_time_interval_unix as $initial_time_unix ) {			// So we are getting only INITIAL interval
				$dates_arr_unix[] = $initial_time_unix;
			}		
		}
		//ex:  Array ( [0] => 1497441601 [1] => 1497454202 [2] => 1497700801 [3] => 1497713402 [4] => 1498046401 [5] => 1498059002 )	
	
		$dates_arr_mysql = array();
		foreach ( $dates_arr_unix as $date_unix ) {
			$dates_arr_mysql[] = date_i18n( 'Y-m-d H:i:s', $date_unix );
		}
		//ex:  Array ( [0] => 2017-06-14 12:00:01 [1] => 2017-06-14 15:30:02 [2] => 2017-06-17 12:00:01 [3] => 2017-06-17 15:30:02 [4] => 2017-06-21 12:00:01 [5] => 2017-06-21 15:30:02 )	

	return $dates_arr_mysql;
}


/** Reset any seconds in ics date and return it,
 *  Its useful, if such  info  was exported by  Booking Calendar and now is importing again.
 * 
 * @param string $ics_date_time		- 20170614T120001
 * @return string					- 20170614T120000
 */
function wpbm_ics_date_reset_seconds( $ics_date_time ) {
		
	// 1497441601
	$date_time_unix = ZDateHelper::fromiCaltoUnixDateTime( $ics_date_time );
	
	// 2017-07-10 00:00:01
	$date_time_sql = date_i18n( 'Y-m-d H:i:s', $date_time_unix );										

	if( strlen( $date_time_sql ) > 10 ) {
		$date_time_sql = substr($date_time_sql, 0, -2 ) . '00';	// Reset  seconds to 00,  if previously we was have 01 or 02
	}
	// 2017-07-10 00:00:00
					
	$date_time_unix = strtotime( $date_time_sql );
			
	$ics_date_time =  ZDateHelper::fromUnixDateTimetoiCal( $date_time_unix );
	
	return  $ics_date_time;
}