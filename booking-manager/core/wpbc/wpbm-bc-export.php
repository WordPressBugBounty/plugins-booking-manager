<?php 
/**
 * @version 1.0
 * @description Booking Calendar integration - Export
 * 
 * @author wpdevelop
 * @link https://oplugins.com/
 * @email info@oplugins.com
 *
 * @modified 2017-06-28
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly



////////////////////////////////////////////////////////////////////////////////////////////////////////////////////				
// E X P O R T
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	

/** Define ICS Feeds names
 * 
 *   wpbm-ics - Example of Feed Name.
 * 
 *   Have to  work  correctly immediately:		http://beta/?feed=wpbm-ics
 *   Nice looking link:							http://beta/feed/wpbm-ics		-- require to update permalink  structure at WordPress > Settings > Permalink Settings page by clicking  on "Save changes" button.
 *   Finally, to display your feed, you’ll first need to flush your WordPress rewrite rules. 
 *   The easiest way to do this is by logging in to the WordPress admin, and clicking Settings -> Permalinks. 
 *   Once here, just click Save Changes, which will flush the rewrite rules.
 *   
 *   Otherwsie need to run ONLY once this:
 * 
 *   global $wp_rewrite;
 *   $wp_rewrite->flush_rules();
 * 
 */
function wpbm_make_export_ics_feeds(){

	if ( function_exists( 'wp_parse_url' ) )
		$my_parsed_url = wp_parse_url( $_SERVER[ 'REQUEST_URI' ] );
	else
		$my_parsed_url = @parse_url( $_SERVER[ 'REQUEST_URI' ] );
	
	if ( false === $my_parsed_url ) {		// seriously malformed URLs, parse_url() may return FALSE. 
		return;
	}

	$my_parsed_url_path	 = trim( $my_parsed_url[ 'path' ] );
	$my_parsed_url_path	 = trim( $my_parsed_url_path, '/' );

	//FixIn: 2.0.5.4
	// check internal subfolder of WP,  like http://server.com/my-website/ ... link ...
	$server_url_sufix = '';
	if ( function_exists( 'wp_parse_url' ) )
		$wp_home_server_url = wp_parse_url( home_url() );
	else
		$wp_home_server_url = @parse_url( home_url() );

	if (  ( false !== $wp_home_server_url ) && (! empty( $wp_home_server_url[ 'path' ] ) )  ){		                            // seriously malformed URLs, parse_url() may return FALSE.
		$server_url_sufix	 = trim( $wp_home_server_url[ 'path' ] );       // [path] => /my-website
		$server_url_sufix	 = trim( $server_url_sufix, '/' );              // my-website
		if ( ! empty( $server_url_sufix ) ) {
			$check_sufix = substr( $my_parsed_url_path, 0, strlen( $server_url_sufix ) );
			if ( $check_sufix === $server_url_sufix ) {
				$my_parsed_url_path = substr( $my_parsed_url_path, strlen( $server_url_sufix ) );
				$my_parsed_url_path	= trim( $my_parsed_url_path, '/' );
			}
		}
	}
	//End FixIn: 2.0.5.4



	// Get booking resources and Export Feed URLS
	$is_continue = false;
	if ( function_exists( 'wpbc_br_cache' ) ) {
		
		$resources_cache = wpbc_br_cache();                                         // Get booking resources from  cache        
	    $resource_list = $resources_cache->get_resources();
		
		foreach ( $resource_list as $res_id => $res_arr) {
			
			if ( ! empty( $res_arr[ 'export' ] ) ) {
				$resource_feed_url = $res_arr[ 'export' ];
			} else {
				$resource_feed_url = '';
			}
			
			$resource_feed_url = trim( $resource_feed_url, '/' );
			if ( ! empty( $resource_feed_url ) ) {
				// if ( 0 === strpos( $my_parsed_url_path, $resource_feed_url ) ) {	// First coocurence of  $download_url_path in URL, like 'wpbm-ics-export' in 'http://beta/wpbm-ics-export/my-feed/'
				if ( $my_parsed_url_path === $resource_feed_url ) {
					$is_continue = true;
					break;
				}
			}
		}
	} else if ( function_exists( 'get_bk_option' ) ) {	

		//TODO: cehck  for Free version.		
		$res_id = 1;
		$resource_feed_url = get_bk_option( 'booking_resource_export_ics_url' );
		
		$resource_feed_url = trim( $resource_feed_url, '/' );
		
		if ( ! empty( $resource_feed_url ) ) {			
			if ( $my_parsed_url_path === $resource_feed_url ) {
				$is_continue = true;
			}
		}
		
	} else {
		return;	// No Booking Calendar active,  so  ust opening some other page
	}
	
	
	if ( true === $is_continue) {	

		$download_url_path = $resource_feed_url;

		$ics_export = wpbm_export_ics_feed__wpbm_ics( array( 'wh_booking_type' => $res_id ) );

		if ( headers_sent() ){
			die( 'headers_sent_before_download' );
		}

		////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// Prepare system before downloading set time limits, server output options 
		////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$disabled = explode( ',', ini_get( 'disable_functions' ) );
		$is_func_disabled = in_array( 'set_time_limit', $disabled );
		if ( ! $is_func_disabled && ! ini_get( 'safe_mode' ) ) {
			@set_time_limit( 0 );
		}
		//FixIn: 2.0.18.2
//		if ( function_exists( 'get_magic_quotes_runtime' ) && get_magic_quotes_runtime() && version_compare( phpversion(), '5.4', '<' ) ) {
//			set_magic_quotes_runtime( 0 );
//		}

		@session_write_close();
		if ( function_exists( 'apache_setenv' ) ) {
			@apache_setenv( 'no-gzip', 1 );
		}
		@ini_set( 'zlib.output_compression', 'Off' );

		@ob_end_clean();	// In case,  if somewhere opeded output buffer, may be  required for working fpassthru with  large files 


		////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// Set Headers before file download 
		////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$file = array();
		$file['content_type'] = 'text/calendar';
		$file['name'] = 'wpbm.ics';

		if ( function_exists( 'mb_detect_encoding' ) )                      //FixIn: 2.0.5.3
			$text_encoding = mb_detect_encoding( $ics_export );
		else
			$text_encoding = '';    //Unknown
		
//debuge( mb_detect_encoding( $ics_export ) );die;		
		
		if (    ( is_array(  $text_encoding ) ) 
			 && ( ! in_array( 'UTF-8', $text_encoding ) )  
		){
			$file['size'] = wpbm_get_bytes_from_str( $ics_export );
		} else {
			$file['size'] = 0;	// UTF-8 encoding,  so  size alculated incorrectly, probabaly
		}

		nocache_headers();		
		header( "Robots: none" . "\n" );
		@header( $_SERVER[ 'SERVER_PROTOCOL' ] . ' 200 OK' . "\n" );
		header( "Content-Type: " . $file['content_type'] . "\n" );
		header( "Content-Description: File Transfer" . "\n" );
		header( "Content-Disposition: attachment; filename=\"" . $file['name'] . "\"" . "\n" );
		header( "Content-Transfer-Encoding: binary" . "\n" );

		if ( (int) $file['size'] > 0 )
			header( "Content-Length: " . $file['size'] . "\n" );


		echo $ics_export;

		exit;	
	}
	// Unknown error, or just opening some other page


}
add_action( 'template_redirect', 'wpbm_make_export_ics_feeds' );



/** Define export ICS here  
 *  For testing: http://beta/?feed=wpbm-ics
 */
function wpbm_export_ics_feed__wpbm_ics( $param = array( 'wh_booking_type' => '1', 'wh_trash' => '' ) ) {               //FixIn: 2.0.2.3

	if ( ! function_exists( 'wpbc_api_get_bookings_arr' ) )
		return '';

//	// Start date of getting bookings
//	$real_date = strtotime( '-1 year' );
//	$wh_booking_date = date_i18n( "Y-m-d", $real_date );
//	$param['wh_booking_date'] = $wh_booking_date;
	// End date of getting bookings
	$real_date = strtotime( '+2 years' );                           //FixIn: 2.0.7.1
	$wh_booking_date2 = date_i18n( "Y-m-d", $real_date );
	$param['wh_booking_date2' ] = $wh_booking_date2;

	// Export only approved bookings                                //FixIn: 2.0.11.1   //FixIn: 8.5.2.3
	$booking_is_ics_export_only_approved = get_bk_option( 'booking_is_ics_export_only_approved' );
	if ( 'On' == $booking_is_ics_export_only_approved ) {
		$param['wh_approved'] = '1';
	} else {
		$param['wh_approved'] = '';
	}

	//'' | 'imported' | 'plugin'                       //FixIn: 8.8.3.19        //FixIn: 2.0.20.2
	if ( 'imported' == get_bk_option( 'booking_is_ics_export_imported_bookings' ) ) {
		$param['wh_sync_gid'] = 'imported';
	}
	if ( 'plugin' == get_bk_option( 'booking_is_ics_export_imported_bookings' ) ) {
		$param['wh_sync_gid'] = 'plugin';
	}



	$_REQUEST['view_mode']= 'vm_calendar';      //FixIn: 8.5.2.15       2.0.11.2
	// Get array of bookings.
	$bookings_arr = wpbc_api_get_bookings_arr( $param );


	ob_start();
			
	// create the ical object
	$icalobj = new ZCiCal();


	// 5.7. REFRESH-INTERVAL Property: https://www.rfc-editor.org/rfc/rfc7986.html#section-5.7  https://icalendar.org/New-Properties-for-iCalendar-RFC-7986/5-7-refresh-interval-property.html

	/**
	 *	4.3.6   Duration        https://www.rfc-editor.org/rfc/rfc2445.txt

		   Value Name: DURATION

		   Purpose: This value type is used to identify properties that contain
		   a duration of time.

		   Formal Definition: The value type is defined by the following
		   notation:

		     dur-value  = (["+"] / "-") "P" (dur-date / dur-time / dur-week)

		     dur-date   = dur-day [dur-time]
		     dur-time   = "T" (dur-hour / dur-minute / dur-second)
		     dur-week   = 1*DIGIT "W"
		     dur-hour   = 1*DIGIT "H" [dur-minute]
		     dur-minute = 1*DIGIT "M" [dur-second]
		     dur-second = 1*DIGIT "S"
		     dur-day    = 1*DIGIT "D"

		   Description: If the property permits, multiple "duration" values are
		   specified by a COMMA character (US-ASCII decimal 44) separated list
		   of values. The format is expressed as the [ISO 8601] basic format for
		   the duration of time. The format can represent durations in terms of
		   weeks, days, hours, minutes, and seconds.
		   No additional content value encoding (i.e., BACKSLASH character
		   encoding) are defined for this value type.

		   Example: A duration of 15 days, 5 hours and 20 seconds would be:

		     P15DT5H0M20S

		   A duration of 7 weeks would be:

		     P7W
	 */

	$datanode = new ZCiCalDataNode( "REFRESH-INTERVAL;VALUE=DURATION:PT10M" );      //  PERIOD TIME 10 Minutes          //FixIn: 2.0.30.1
	//$datanode = new ZCiCalDataNode( "REFRESH-INTERVAL;VALUE=DURATION:PT1H" );     //  PERIOD TIME 1 HOUR
	$icalobj->curnode->data[ $datanode->getName() ] = $datanode;

	// This property  for MS Outlook:       // https://learn.microsoft.com/en-us/openspecs/exchange_server_protocols/ms-oxcical/1fc7b244-ecd1-4d28-ac0c-2bb4df855a1f  or
	$datanode = new ZCiCalDataNode( "X-PUBLISHED-TTL:PT10M" );                      //  PERIOD TIME 10 Minutes      -   Outlook
	$icalobj->curnode->data[ $datanode->getName() ] = $datanode;


	//TODO: Some interesting info  about updates of events: https://stackoverflow.com/questions/65812300/publishing-frequently-changing-icalendar-via-methodpublish-in-combination-with

	foreach ( $bookings_arr['bookings'] as $bk_id => $bk_arr ) {

		// create the event within the ical object
		$eventobj = new ZCiCalNode( 'VEVENT', $icalobj->curnode );

		////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// SUMMARY  [Title]
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////
						
		if ( function_exists( 'get_title_for_showing_in_day' ) ) {	// Get title  of booking pipiline in "Calendar Overview" page
			
			if ( true ) {																									// admin
				$what_show_in_day_template = get_bk_option( 'booking_default_title_in_day_for_calendar_view_mode' );
			} else {																										// front-end
				$what_show_in_day_template = get_bk_option( 'booking_default_title_in_day_for_timeline_front_end' );
			}
			//$title = esc_textarea( get_title_for_showing_in_day( $bk_id, $bookings_arr['bookings'], $what_show_in_day_template ) );
			$title = get_title_for_showing_in_day( $bk_id, $bookings_arr['bookings'], $what_show_in_day_template );     //FixIn: 2.0.1.6.1
			
		} else {
			
			$title =   ( ( isset( $bk_arr->form_data['name'] ) ) ? ' ' . esc_textarea( $bk_arr->form_data['name'] ) : '' )
					.  ( ( isset( $bk_arr->form_data['firstname'] ) ) ? ' ' . esc_textarea( $bk_arr->form_data['firstname'] ) : '' )
					.  ( ( isset( $bk_arr->form_data['secondname'] ) ) ? ' ' . esc_textarea( $bk_arr->form_data['secondname'] ) : '' )
					.  ( ( isset( $bk_arr->form_data['lastname'] ) ) ? ' ' . esc_textarea( $bk_arr->form_data['lastname'] ) : '' );			
		}

		// Insert  the name of booking resource  into title of .ics event
		//$title = $bk_arr->form_data['_all_fields_']['resource_title']->title . ' - ' . $title;

		$is_hide_details =  get_wpbm_option( 'wpbm_is_hide_details' );
		if ( 'On' === $is_hide_details ) {      //FixIn: 2.0.12.3
			$title = '';
		}
		$title = wpbm_esc_to_plain_text( $title );                                                                      //FixIn: 2.0.1.6.1

//$title = substr($title, 0, 5);
		$eventobj->addNode( new ZCiCalDataNode( 'SUMMARY:' . trim( $title ) ) );

		////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// DESCRIPTION
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$description = $bk_arr->form_show;
//		if ( ( ! empty($bk_arr->dates) ) && ( $bk_arr->dates[0]->approved ) ){
//			$description .= '<br>Status: Approved';
//		} else {
//			$description .= '<br>Status: Pending';
//		}

		//$description = html_entity_decode( sanitize_text_field( $description ) ); 
		//$description =  trim( wp_specialchars_decode( esc_html( stripslashes( $description ) ), ENT_QUOTES ) );
		if ( 'On' === $is_hide_details ) {      //FixIn: 2.0.12.3
			$description = '';
		}
		$description = wpbm_esc_to_plain_text(  $description );

//debuge($description . '!');die;
//$description = substr($description, 0, 1);
		$description = preg_replace( array( "/\n/" ), array( '' ), $description );
//debuge( ZCiCal::formatContent( $description ) );die;
		$eventobj->addNode( new ZCiCalDataNode( 'DESCRIPTION:' . ZCiCal::formatContent( $description ) ) );
		
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// START & END DATES
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$event_start =  $bk_arr->dates_short[ 0 ];
		if ( ( count($bk_arr->dates_short) > 1 ) && ( '-' === $bk_arr->dates_short[ 1 ] ) ) {
			
			$event_end   = $bk_arr->dates_short[ 2 ];
				
			$event_end = wpbm_get_next_date_if_it_full_date( $event_end );
				
			$period_start_index = 3;
		} else {											// comma
			$event_end   = $bk_arr->dates_short[ 0 ];
			
			$event_end = wpbm_get_next_date_if_it_full_date( $event_end );

			$period_start_index = 1;
		}
		
		// add start date
		$eventobj->addNode( new ZCiCalDataNode( "DTSTART:" . ZCiCal::fromSqlDateTime( $event_start ) ) );

		// add end date
		$eventobj->addNode( new ZCiCalDataNode( "DTEND:" . ZCiCal::fromSqlDateTime( $event_end ) ) );
		
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// RDATE   -  Rule for other different dates.
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//
		//		RDATE;VALUE=PERIOD:19960403T020000Z/19960403T040000Z,
        //		19960404T010000Z/PT3H		
		//
		$rdate = array();
		$previos_value = '';
		foreach ( $bk_arr->dates_short as $short_ind => $bk_date ) {
			
			if ( $short_ind < $period_start_index )
				continue;
			
			if (   ( $bk_date !== ',' ) && ( $bk_date !== '-' )   ) {

				if ( $previos_value == '-' ) {
					$bk_date = wpbm_get_next_date_if_it_full_date( $bk_date );
				} else {
					$bk_date = wpbm_get_only_date_if_it_full_date( $bk_date );
				}

				if(strlen($bk_date) > 10){
					// Date with Time
					$rdate[]= ZCiCal::fromSqlDateTime( $bk_date );
				} else {
					// Only Full Dates      //FixIn: 2.1.10.1
					$rdate[]= ZCiCal::fromSqlDateTime( $bk_date );
					$rdate[] = '/';
					$rdate[]= ZCiCal::fromSqlDateTime( $bk_date );
				}

			} else if ( ( $bk_date === '-' ) && ( count($rdate) > 0 ) ) {
				$rdate[] = '/';				// PERIOD

			} else if ( ( $bk_date === ',' ) && ( count($rdate) > 0 ) ) {
				$rdate[] = ',';				// Other date
			}
			$previos_value = $bk_date;
		}
		if ( ! empty( $rdate ) ) {
			$rdate = implode( '', $rdate );
			// add RDATE
			$eventobj->addNode( new ZCiCalDataNode( "RDATE;VALUE=PERIOD:" . $rdate ) );
			
		}

		////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// Timezone
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		/* Timezone must be a supported PHP timezone (see http://php.net/manual/en/timezones.php )
		 * Note: multi-word timezones must use underscore "_" separator
		 * Example:     $tzid = "America/New_York";
		 */

		$tzid = get_bk_option( 'booking_gcal_timezone' );

		if ( ! empty( $tzid ) ) {
			// Add timezone data
			ZCTimeZoneHelper::getTZNode( substr( $event_start, 0, 4 ), substr( $event_end, 0, 4 ), $tzid, $icalobj->curnode );      //FixIn: 2.0.3.3
		}

		////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// UID
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$uid = $event_start . '_' . $bk_id . "@demo.icalendar.org";
		$uid = str_replace( array( '_', ' ' ), '-', $uid );                 //FixIn: 2.1.7.1
		$eventobj->addNode( new ZCiCalDataNode( "UID:" . $uid ) );
		
		// DTSTAMP is a required item in VEVENT
		$eventobj->addNode( new ZCiCalDataNode( "DTSTAMP:" . ZCiCal::fromSqlDateTime() ) );

		$eventobj->addNode( new ZCiCalDataNode( "CREATED:" . ZCiCal::fromSqlDateTime( $bk_arr->modification_date ) ) );
		$eventobj->addNode( new ZCiCalDataNode( "LAST-MODIFIED:" . ZCiCal::fromSqlDateTime( $bk_arr->modification_date ) ) );
		
		
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// STATUS:CONFIRMED		( "TENTATIVE" / "CONFIRMED" / "CANCELLED" )												// This property defines the overall status for EVENT
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		$ics_status = 'TENTATIVE';
		if ( ! ( empty( $bk_arr->trash ) ) ) {
			$ics_status = 'CANCELLED';
		} elseif ( ! ( empty( $bk_arr->dates[0]->approved ) ) ) {
			$ics_status = 'CONFIRMED';
		}
		$eventobj->addNode( new ZCiCalDataNode( "STATUS:" . $ics_status ) );    //FixIn: 2.0.11.3       //FixIn: 2.0.12.2

		// Check more here: https://icalendar.org/iCalendar-RFC-5545/3-2-9-free-busy-time-type.html                     //FixIn: 2.1.9.1
		$ics_busy = 'BUSY';                                  // ("FREE" / "BUSY" / "BUSY-UNAVAILABLE" / "BUSY-TENTATIVE"
		$eventobj->addNode( new ZCiCalDataNode( "FBTYPE:" . $ics_busy ) );

		//FixIn: 2.0.20.1
		//ATTENDEE;SENT-BY=MAILTO:jan_doe@host1.com;CN=John Smith:MAILTO:jsmith@host1.com           // More here https://www.kanzaki.com/docs/ical/attendee.html
		if ( isset( $bk_arr->form_data['email'] ) ) {

			$cn_title = ( ( isset( $bk_arr->form_data['name'] ) ) ? ' ' . esc_textarea( $bk_arr->form_data['name'] ) : '' )
			            . ( ( isset( $bk_arr->form_data['firstname'] ) ) ? ' ' . esc_textarea( $bk_arr->form_data['firstname'] ) : '' )
			            . ( ( isset( $bk_arr->form_data['secondname'] ) ) ? ' ' . esc_textarea( $bk_arr->form_data['secondname'] ) : '' )
			            . ( ( isset( $bk_arr->form_data['lastname'] ) ) ? ' ' . esc_textarea( $bk_arr->form_data['lastname'] ) : '' );

			$attendee_email = $bk_arr->form_data['email'];
			if ( 'On' !== $is_hide_details ) {
				$eventobj->addNode( new ZCiCalDataNode( "ATTENDEE;CN=" . $cn_title . ":MAILTO:" . $attendee_email ) );
			}
		}

		// RRULE:FREQ=WEEKLY;COUNT=3;BYDAY=WE,SA
		// LOCATION:South Australia\, Australia
		// SEQUENCE:1									// defines the revision sequence number of the calendar component within a sequence of revisions 
		// TRANSP:OPAQUE								// This property defines whether or not an event is transparent to busy time searches	;Default value is OPAQUE
		//		  OPAQUE		-  Blocks or opaque on busy time searches.
		//		  TRANSPARENT 	-  Transparent on busy time searches.
		
	}

	echo $icalobj->export();			// write iCalendar feed to stdout

	$ics_export = ob_get_contents();

	$ics_export = trim( $ics_export );      //FixIn: 2.1.9.2

	ob_end_clean();

	return $ics_export;
}


/** Get Next day in MySQL format without time,  if current day  its Full day - ending with 00:00:00
 * 
 * @param string $mysql_date	- 2017-07-12 00:00:00
 * @return string				- 2017-07-13
 */
function wpbm_get_next_date_if_it_full_date( $mysql_date ) {
	
	// Check if this date ending with 00:00:00 its means that it full  day,  
	// so we need to add 24 hours for ending at 23:59:59 (basically  00:00:00 of next  day) instead of at  start  of current day 00:00:00
	if (   ( strlen( $mysql_date ) > 10 ) // , like 2017-07-12 00:00:00
		&& ( substr( $mysql_date, 11 ) == '00:00:00' ) 
	) {

		$mysql_date = date_i18n( "Y-m-d 00:00:00",  strtotime( $mysql_date ) );
		$mysql_date = strtotime( $mysql_date );
		$mysql_date = date_i18n( "Y-m-d",  strtotime( '+1 day', $mysql_date ) );											
	} 

	return $mysql_date;	
}


/** Get day in MySQL format without time,  if current day  its Full day - ending with 00:00:00
 * 
 * @param string $mysql_date	- 2017-07-12 00:00:00
 * @return string				- 2017-07-12
 */
function wpbm_get_only_date_if_it_full_date( $mysql_date ) {

	if (   ( strlen( $mysql_date ) > 10 ) // , like 2017-07-12 00:00:00
		&& ( substr( $mysql_date, 11) == '00:00:00' ) 
	) {					
		$mysql_date = substr( $mysql_date, 0, 10 );
	}

	return $mysql_date;
}