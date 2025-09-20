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
		$file['content_type'] = 'text/calendar; charset=UTF-8';
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
		header( "Content-Disposition: inline; filename=\"" . $file['name'] . "\"" . "\n" );
		header( "Content-Transfer-Encoding: binary" . "\n" );

		if ( (int) $file['size'] > 0 )
			header( "Content-Length: " . $file['size'] . "\n" );


		echo $ics_export;

		exit;	
	}
	// Unknown error, or just opening some other page


}
add_action( 'template_redirect', 'wpbm_make_export_ics_feeds' );


// ---------------------------------------------------------------------------------------------------------------------
// Helpers for ICS export:
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Escape ICS text per RFC 5545: backslash, comma, semicolon; newlines -> \n
 *
 * @param string $text
 *
 * @return string
 */
function wpbc_ics_escape_text( $text ) {
	$text = (string) $text;
	$text = str_replace( array( "\\", ";", "," ), array( "\\\\", "\;", "\," ), $text );
	$text = str_replace( array( "\r\n", "\r" ), "\n", $text );
	$text = str_replace( "\n", "\\n", $text ); // single backslash-n.

	return $text;
}

/**
 * Format SQL local datetime ("Y-m-d H:i:s") as UTC Z iCal (YYYYMMDDTHHMMSSZ).
 *
 * @param string|null  $sql_dt Local SQL datetime or null for "now"
 * @param DateTimeZone $source_tz
 *
 * @return string
 */
function wpbc_to_ics_utc( $sql_dt, DateTimeZone $source_tz ) {
	$dt = $sql_dt ? DateTime::createFromFormat( 'Y-m-d H:i:s', $sql_dt, $source_tz )
		: new DateTime( 'now', $source_tz );
	if ( ! $dt ) {
		$dt = new DateTime( 'now', $source_tz );
	}
	$dt->setTimezone( new DateTimeZone( 'UTC' ) );

	return $dt->format( 'Ymd\THis\Z' );
}

/**
 * Format SQL local datetime as local (no Z) in iCal basic (YYYYMMDDTHHMMSS).
 *
 * @param string       $sql_dt
 * @param DateTimeZone $tz
 *
 * @return string
 */
function wpbc_to_ics_local( $sql_dt, DateTimeZone $tz ) {
	$dt = DateTime::createFromFormat( 'Y-m-d H:i:s', $sql_dt, $tz );
	if ( ! $dt ) {
		$dt = new DateTime( $sql_dt ?: 'now', $tz );
	}

	return $dt->format( 'Ymd\THis' );
}

/**
 * Normalize seconds to :00 for clean slots (avoid :01/:02 artifacts).
 *
 * @param string       $sql_dt
 * @param DateTimeZone $tz
 *
 * @return string  "Y-m-d H:i:00" on success, original input if parsing fails
 */
function wpbc_normalize_sec_sql( $sql_dt, DateTimeZone $tz ) {
	$dt = DateTime::createFromFormat( 'Y-m-d H:i:s', $sql_dt, $tz );
	if ( ! $dt ) {
		return $sql_dt;
	}

	return $dt->format( 'Y-m-d H:i:00' );
}

/**
 * Add/subtract whole days to a Y-m-d in a specific tz (DST-safe), return Y-m-d.
 *
 * @param string       $ymd  "Y-m-d"
 * @param int          $days can be negative
 * @param DateTimeZone $tz
 *
 * @return string       "Y-m-d"
 */
function wpbc_add_days_ymd( $ymd, $days, DateTimeZone $tz ) {
	$dt = DateTimeImmutable::createFromFormat( 'Y-m-d H:i:s', $ymd . ' 00:00:00', $tz );
	if ( ! $dt ) {
		$dt = new DateTimeImmutable( 'now', $tz );
	}
	$modifier = ( $days >= 0 ? '+' : '' ) . $days . ' days';

	return $dt->modify( $modifier )->format( 'Y-m-d' );
}

/**
 * Ensure DTEND is strictly after DTSTART (RFC 5545).
 *
 * - All-day mode: treat inputs as Y-m-d; if end <= start → end = start + 1 day (exclusive end).
 * - Timed mode : normalize to 'Y-m-d H:i:s'; if end <= start → end = start + 1 hour.
 *
 * @param string       $start_in  'Y-m-d' or 'Y-m-d H:i:s'
 * @param string       $end_in    'Y-m-d' or 'Y-m-d H:i:s' (can be empty)
 * @param DateTimeZone $tz
 * @param bool         $all_day
 *
 * @return array [ $start_out, $end_out ]  (all-day → Y-m-d; timed → 'Y-m-d H:i:00')
 */
function wpbc_ics_coerce_order( $start_in, $end_in, DateTimeZone $tz, $all_day ) {

	// Helper for all-day date math.
	$_add_days = function( $ymd, $days ) use ( $tz ) {
		return wpbc_add_days_ymd( $ymd, $days, $tz );
	};

	if ( $all_day ) {
		$sd = substr( (string) $start_in, 0, 10 );
		$ed = substr( (string) ($end_in !== '' ? $end_in : $sd), 0, 10 );
		if ( $ed <= $sd ) {
			$ed = $_add_days( $sd, 1 );
		}
		return array( $sd, $ed );
	}

	// Timed mode: normalize to full SQL datetimes.
	$ss = ( strlen( (string) $start_in ) > 10 ) ? $start_in : ( substr( (string) $start_in, 0, 10 ) . ' 00:00:00' );
	$ee = ( $end_in !== '' )
		? ( ( strlen( (string) $end_in ) > 10 ) ? $end_in : ( substr( (string) $end_in, 0, 10 ) . ' 00:00:00' ) )
		: '';

	$ds = DateTime::createFromFormat( 'Y-m-d H:i:s', $ss, $tz );
	if ( ! $ds ) { $ds = new DateTime( $ss ?: 'now', $tz ); }

	if ( $ee === '' ) {
		$de = clone $ds;
		$de->modify( '+1 hour' );
	} else {
		$de = DateTime::createFromFormat( 'Y-m-d H:i:s', $ee, $tz );
		if ( ! $de ) { $de = new DateTime( $ee, $tz ); }
	}
	if ( $de <= $ds ) {
		$de = clone $ds;
		$de->modify( '+1 hour' );
	}
	return array( $ds->format( 'Y-m-d H:i:00' ), $de->format( 'Y-m-d H:i:00' ) );
}


/**
 * Add a VTIMEZONE node exactly once per TZID.
 *
 * @param ZCiCal     $icalobj
 * @param string     $tzid
 * @param string|int $year_start "YYYY"
 * @param string|int $year_end   "YYYY"
 * @param array      $added_ref  reference array to track added TZIDs
 *
 * @return void
 */
/**
 * Add a VTIMEZONE node exactly once per TZID.
 * - For Europe/* zones: emit a compact RRULE-based CET/CEST block + X-LIC-LOCATION.
 * - For other zones: fall back to library helper (detailed transitions).
 */
function wpbc_add_vtimezone_once( $icalobj, $tzid, $year_start, $year_end, array &$added_ref ) {
	if ( empty( $tzid ) ) {
		return;
	}
	if ( isset( $added_ref[ $tzid ] ) ) {
		return;
	}

	// Lightweight CET/CEST definition for Europe/* (covers Europe/Prague and peers).
	if ( strpos( $tzid, 'Europe/' ) === 0 ) {
		$vtz = new ZCiCalNode( 'VTIMEZONE', $icalobj->curnode );
		$vtz->addNode( new ZCiCalDataNode( 'TZID:' . $tzid ) );
		$vtz->addNode( new ZCiCalDataNode( 'X-LIC-LOCATION:' . $tzid ) );

		// STANDARD: last Sunday in October 03:00 -> CET (UTC+1).
		$std = new ZCiCalNode( 'STANDARD', $vtz );
		$std->addNode( new ZCiCalDataNode( 'DTSTART:19961027T030000' ) );
		$std->addNode( new ZCiCalDataNode( 'TZOFFSETFROM:+0200' ) );
		$std->addNode( new ZCiCalDataNode( 'TZOFFSETTO:+0100' ) );
		$std->addNode( new ZCiCalDataNode( 'TZNAME:CET' ) );
		$std->addNode( new ZCiCalDataNode( 'RRULE:FREQ=YEARLY;BYMONTH=10;BYDAY=-1SU' ) );

		// DAYLIGHT: last Sunday in March 02:00 -> CEST (UTC+2).
		$dst = new ZCiCalNode( 'DAYLIGHT', $vtz );
		$dst->addNode( new ZCiCalDataNode( 'DTSTART:19960331T020000' ) );
		$dst->addNode( new ZCiCalDataNode( 'TZOFFSETFROM:+0100' ) );
		$dst->addNode( new ZCiCalDataNode( 'TZOFFSETTO:+0200' ) );
		$dst->addNode( new ZCiCalDataNode( 'TZNAME:CEST' ) );
		$dst->addNode( new ZCiCalDataNode( 'RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=-1SU' ) );

	} else {
		// Fallback: detailed transitions for non‑Europe zones (safer across the globe).
		ZCTimeZoneHelper::getTZNode( (string) $year_start, (string) $year_end, $tzid, $icalobj->curnode );
	}

	$added_ref[ $tzid ] = true;
}

// ---------------------------------------------------------------------------------------------------------------------
// Export ICS here.
// ---------------------------------------------------------------------------------------------------------------------
/**
 * Define export ICS here.
 * For testing: http://beta/?feed=wpbm-ics
 *
 * @param array $param - [ 'wh_booking_type' => '1', 'wh_trash' => '' ].
 *
 * @return string
 */
function wpbm_export_ics_feed__wpbm_ics( $param = array( 'wh_booking_type' => '1', 'wh_trash' => '' ) ) {

	// FixIn: 2.1.13.1 – timezone-safe, UTC stamps, OTA all‑day, single VTIMEZONE, no closures.

	if ( ! function_exists( 'wpbc_api_get_bookings_arr' ) ) {
		return '';
	}

	// Resolve export timezone: option 'booking_gcal_timezone' if set, else WP site tz.
	$site_tz_obj  = wp_timezone();
	$tzid_opt_raw = (string) get_bk_option( 'booking_gcal_timezone' );
	$tzid_opt     = trim( $tzid_opt_raw );                 // "" when “Default” is selected

	// Validate TZID (must be a known IANA zone).
	$tzid = '';
	if ( $tzid_opt !== '' ) {
		try {
			// Will throw on invalid IDs (e.g., typos).
			$check = new DateTimeZone( $tzid_opt );
			$tzid  = $tzid_opt;                         // valid — we’ll emit TZID and VTIMEZONE.
		}
		catch ( Exception $e ) {
			$tzid = '';                                  // invalid — treat as Default (no TZID emitted).
		}
	}

	// Export timezone object (priority: booking_gcal_timezone > site tz).
	$export_tz = ( $tzid !== '' ) ? new DateTimeZone( $tzid ) : $site_tz_obj;

	// End date of getting bookings (cutoff +2 years) – done in export tz.
	$cutoff                    = ( new DateTimeImmutable( 'now', $export_tz ) )->modify( '+2 years' )->format( 'Y-m-d' );
	$param['wh_booking_date2'] = $cutoff;

	// Export only approved bookings (FixIn: 2.0.11.1 / 8.5.2.3).
	$booking_is_ics_export_only_approved = get_bk_option( 'booking_is_ics_export_only_approved' );
	$param['wh_approved']                = ( 'On' === $booking_is_ics_export_only_approved ) ? '1' : '';

	// '' | 'imported' | 'plugin' (FixIn: 8.8.3.19 / 2.0.20.2).
	if ( 'imported' === get_bk_option( 'booking_is_ics_export_imported_bookings' ) ) {
		$param['wh_sync_gid'] = 'imported';
	}
	if ( 'plugin' === get_bk_option( 'booking_is_ics_export_imported_bookings' ) ) {
		$param['wh_sync_gid'] = 'plugin';
	}

	$_REQUEST['tab'] = 'vm_calendar';                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      // FixIn: 10.12.3.2 / 8.5.2.15 / 2.0.11.2.

	// Get array of bookings.
	$bookings_arr = wpbc_api_get_bookings_arr( $param );

	ob_start();

	// Create the iCal object.
	$icalobj = new ZCiCal();

	// Calendar headers (library adds VERSION/PRODID; we add refresh hints).
	$datanode                                       = new ZCiCalDataNode( 'REFRESH-INTERVAL;VALUE=DURATION:PT10M' );                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  // 10 minutes.
	$icalobj->curnode->data[ $datanode->getName() ] = $datanode;
	$datanode                                       = new ZCiCalDataNode( 'X-PUBLISHED-TTL:PT10M' ); // 10 minutes.
	$icalobj->curnode->data[ $datanode->getName() ] = $datanode;

	$all_day_export = ( 'On' === get_wpbm_option( 'wpbm_is_export_only_full_days' ) ); // OTA toggle.

	// Host for UIDs (global uniqueness).
	$host = wp_parse_url( home_url(), PHP_URL_HOST );
	if ( empty( $host ) ) {
		$host = 'example.org';
	}
	if ( 'localhost' === $host ) {
		$host = 'localhost.localdomain';
	}

	// Track VTIMEZONE additions per TZID.
	$__wpbc_added_vtimezones = array();

	foreach ( $bookings_arr['bookings'] as $bk_id => $bk_arr ) {

		// Create the master event within the iCal object.
		$eventobj = new ZCiCalNode( 'VEVENT', $icalobj->curnode );

		// ---------------------------------------------------------------------------------------------
		// SUMMARY
		// ---------------------------------------------------------------------------------------------
		if ( function_exists( 'get_title_for_showing_in_day' ) ) {
			$what_show_in_day_template = get_bk_option( 'booking_default_title_in_day_for_calendar_view_mode' );
			$title                     = get_title_for_showing_in_day( $bk_id, $bookings_arr['bookings'], $what_show_in_day_template );
		} else {
			$title = ( isset( $bk_arr->form_data['name'] ) ? ' ' . esc_textarea( $bk_arr->form_data['name'] ) : '' );
			$title .= ( isset( $bk_arr->form_data['firstname'] ) ? ' ' . esc_textarea( $bk_arr->form_data['firstname'] )
				: '' );
			$title .= ( isset( $bk_arr->form_data['secondname'] )
				? ' ' . esc_textarea( $bk_arr->form_data['secondname'] ) : '' );
			$title .= ( isset( $bk_arr->form_data['lastname'] ) ? ' ' . esc_textarea( $bk_arr->form_data['lastname'] )
				: '' );
		}

		$is_hide_details = get_wpbm_option( 'wpbm_is_hide_details' );
		if ( 'On' === $is_hide_details ) {
			$title = '';
		}
		$title     = ltrim( (string) $title ); // optional: avoid leading space.
		$title_ics = wpbc_ics_escape_text( wpbm_esc_to_plain_text( $title ) );
		$eventobj->addNode( new ZCiCalDataNode( 'SUMMARY:' . $title_ics ) );

		// ---------------------------------------------------------------------------------------------
		// DESCRIPTION
		// ---------------------------------------------------------------------------------------------
		$description_raw = ( 'On' === $is_hide_details ) ? '' : $bk_arr->form_show;
		$description_raw = ltrim( (string) $description_raw );   // remove leading whitespace/newlines.
		$description_ics = wpbc_ics_escape_text( wpbm_esc_to_plain_text( $description_raw ) );
		$description_ics = preg_replace('/\s+/', ' ', $description_ics);
		$eventobj->addNode( new ZCiCalDataNode( 'DESCRIPTION:' . $description_ics ) );
		// ---------------------------------------------------------------------------------------------
		// START & END (master span from dates_short)
		// ---------------------------------------------------------------------------------------------
		$event_start = $bk_arr->dates_short[0];
		if ( ( count( $bk_arr->dates_short ) > 1 ) && ( '-' === $bk_arr->dates_short[1] ) ) {
			$event_end          = $bk_arr->dates_short[2];
			$event_end          = wpbm_get_next_date_if_it_full_date( $event_end ); // make end exclusive for YYYY-mm-dd
			$period_start_index = 3;
		} else { // comma or single day.
			$event_end          = $bk_arr->dates_short[0];
			$event_end          = wpbm_get_next_date_if_it_full_date( $event_end );
			$period_start_index = 1;
		}

		if ( $all_day_export ) {
			// OTA all-day mode (end exclusive).
			$event_start = substr( $event_start, 0, 10 ); // Y-m-d
			$event_end   = substr( $event_end, 0, 10 );   // Y-m-d
			if ( $event_start === $event_end ) {
				$event_end = wpbc_add_days_ymd( $event_end, 1, $export_tz );
			}
		} else {
			// Timed mode: normalize seconds to :00 for clean slots.
			if ( strlen( $event_start ) > 10 ) {
				$event_start = wpbc_normalize_sec_sql( $event_start, $export_tz );
			}
			if ( strlen( $event_end ) > 10 ) {
				$event_end = wpbc_normalize_sec_sql( $event_end, $export_tz );
			}
		}

		// Add VTIMEZONE once (timed mode with TZID).
		if ( ! $all_day_export && ! empty( $tzid ) ) {
			wpbc_add_vtimezone_once( $icalobj, $tzid, substr( $event_start, 0, 4 ), substr( $event_end, 0, 4 ), $__wpbc_added_vtimezones );
		}

		// DTSTART / DTEND for the master VEVENT (coerced so DTEND > DTSTART).
		if ( $all_day_export ) {
			list( $event_start, $event_end ) = wpbc_ics_coerce_order( $event_start, $event_end, $export_tz, true );
			$eventobj->addNode( new ZCiCalDataNode( 'DTSTART;VALUE=DATE:' . preg_replace( '/[^0-9]/', '', $event_start ) ) );
			$eventobj->addNode( new ZCiCalDataNode( 'DTEND;VALUE=DATE:'   . preg_replace( '/[^0-9]/', '', $event_end ) ) );
		} else {
			list( $event_start_fixed, $event_end_fixed ) = wpbc_ics_coerce_order( $event_start, $event_end, $export_tz, false );
			if ( ! empty( $tzid ) ) {
				$eventobj->addNode( new ZCiCalDataNode( 'DTSTART;TZID=' . $tzid . ':' . wpbc_to_ics_local( $event_start_fixed, $export_tz ) ) );
				$eventobj->addNode( new ZCiCalDataNode( 'DTEND;TZID='   . $tzid . ':' . wpbc_to_ics_local( $event_end_fixed,   $export_tz ) ) );
			} else {
				$eventobj->addNode( new ZCiCalDataNode( 'DTSTART:' . wpbc_to_ics_utc( $event_start_fixed, $export_tz ) ) );
				$eventobj->addNode( new ZCiCalDataNode( 'DTEND:'   . wpbc_to_ics_utc( $event_end_fixed,   $export_tz ) ) );
			}
		}

		// ---------------------------------------------------------------------------------------------
		// STATUS / TRANSP
		// ---------------------------------------------------------------------------------------------
		$ics_status = 'TENTATIVE';
		if ( ! empty( $bk_arr->trash ) ) {
			$ics_status = 'CANCELLED';
		} elseif ( ! empty( $bk_arr->dates[0]->approved ) ) {
			$ics_status = 'CONFIRMED';
		}
		$eventobj->addNode( new ZCiCalDataNode( 'STATUS:' . $ics_status ) );
		$eventobj->addNode( new ZCiCalDataNode( 'TRANSP:OPAQUE' ) );

		// ---------------------------------------------------------------------------------------------
		// Expand extra dates/ranges into standalone VEVENTs (better than RDATE/RRULE for OTA/clients)
		// ---------------------------------------------------------------------------------------------
		$segments = array();         // each: ['start_sql' => 'Y-m-d[ H:i:s]', 'end_sql' => 'Y-m-d[ H:i:s]'|null]
		$prev     = '';
		$current  = null;

		foreach ( $bk_arr->dates_short as $i => $tok ) {
			if ( $i < $period_start_index ) {
				continue;
			}
			if ( ',' !== $tok && '-' !== $tok ) {
				if ( '-' === $prev ) {
					$tok_norm = wpbm_get_next_date_if_it_full_date( $tok ); // range end (exclusive if Y-m-d)
				} else {
					$tok_norm = wpbm_get_only_date_if_it_full_date( $tok );  // start or single
				}
				if ( '-' === $prev ) {
					if ( $current ) {
						$current['end_sql'] = $tok_norm;
					}
				} else {
					$current = array( 'start_sql' => $tok_norm, 'end_sql' => null );
				}
			} elseif ( ',' === $tok ) {
				if ( $current ) {
					$segments[] = $current;
					$current    = null;
				}
			}
			$prev = $tok;
		}
		if ( $current ) {
			$segments[] = $current;
		}

		if ( ! empty( $segments ) ) {
			// Duration basis for timed events.
			$master_duration = 0;
			if ( ! $all_day_export ) {
				$basis_start     = isset( $event_start_fixed ) ? $event_start_fixed : $event_start;
				$basis_end       = isset( $event_end_fixed )   ? $event_end_fixed   : $event_end;
				$dts             = DateTime::createFromFormat( 'Y-m-d H:i:s', $basis_start, $export_tz );
				$dte             = DateTime::createFromFormat( 'Y-m-d H:i:s', $basis_end,   $export_tz );
 				$master_duration = ( $dts && $dte && $dte > $dts ) ? ( $dte->getTimestamp() - $dts->getTimestamp() )
					: 3600;
			}

			$seg_idx = 0;
			foreach ( $segments as $s ) {
				++ $seg_idx;
				$ev = new ZCiCalNode( 'VEVENT', $icalobj->curnode );

				if ( $all_day_export ) {
					$start_d = substr( $s['start_sql'], 0, 10 );
					$end_d   = ! empty( $s['end_sql'] ) ? substr( $s['end_sql'], 0, 10 ) : $start_d;
					list( $start_d, $end_d ) = wpbc_ics_coerce_order( $start_d, $end_d, $export_tz, true );

					$ev->addNode( new ZCiCalDataNode( 'DTSTART;VALUE=DATE:' . preg_replace( '/[^0-9]/', '', $start_d ) ) );
					$ev->addNode( new ZCiCalDataNode( 'DTEND;VALUE=DATE:' . preg_replace( '/[^0-9]/', '', $end_d ) ) );

				} else {
					$start_sql = ( strlen( $s['start_sql'] ) > 10 )
						? wpbc_normalize_sec_sql( $s['start_sql'], $export_tz ) : $s['start_sql'];
					if ( ! empty( $s['end_sql'] ) ) {
						$end_sql = ( strlen( $s['end_sql'] ) > 10 )
							? wpbc_normalize_sec_sql( $s['end_sql'], $export_tz ) : $s['end_sql'];
					} else {
						$dt = DateTime::createFromFormat( 'Y-m-d H:i:s', $start_sql, $export_tz );
						if ( $dt ) {
							$dt->modify( '+' . $master_duration . ' seconds' );
							$end_sql = $dt->format( 'Y-m-d H:i:s' );
						} else {
							$end_sql = $start_sql;
						}
					}

					// Ensure DTEND > DTSTART for each segment.
					list( $start_sql, $end_sql ) = wpbc_ics_coerce_order( $start_sql, $end_sql, $export_tz, false );

					if ( ! empty( $tzid ) ) {
						$ev->addNode( new ZCiCalDataNode( 'DTSTART;TZID=' . $tzid . ':' . wpbc_to_ics_local( $start_sql, $export_tz ) ) );
						$ev->addNode( new ZCiCalDataNode( 'DTEND;TZID='   . $tzid . ':' . wpbc_to_ics_local( $end_sql,   $export_tz ) ) );
					} else {
						$ev->addNode( new ZCiCalDataNode( 'DTSTART:' . wpbc_to_ics_utc( $start_sql, $export_tz ) ) );
						$ev->addNode( new ZCiCalDataNode( 'DTEND:'   . wpbc_to_ics_utc( $end_sql,   $export_tz ) ) );
					}
				}

				// Copy common fields.
				$ev->addNode( new ZCiCalDataNode( 'SUMMARY:' . $title_ics ) );
				$ev->addNode( new ZCiCalDataNode( 'DESCRIPTION:' . $description_ics ) );
				$ev->addNode( new ZCiCalDataNode( 'STATUS:' . $ics_status ) );
				$ev->addNode( new ZCiCalDataNode( 'TRANSP:OPAQUE' ) );

				// Unique UID per extra VEVENT.
				$uid_extra = $bk_id . '-' . $seg_idx . '@' . $host;
				$ev->addNode( new ZCiCalDataNode( 'UID:' . $uid_extra ) );

				// Timestamps in UTC Z.
				$ev->addNode( new ZCiCalDataNode( 'DTSTAMP:' . wpbc_to_ics_utc( null, $export_tz ) ) );
				$ev->addNode( new ZCiCalDataNode( 'CREATED:' . wpbc_to_ics_utc( $bk_arr->modification_date, $export_tz ) ) );
				$ev->addNode( new ZCiCalDataNode( 'LAST-MODIFIED:' . wpbc_to_ics_utc( $bk_arr->modification_date, $export_tz ) ) );
			}
		}

		// ---------------------------------------------------------------------------------------------
		// UID (master)
		// ---------------------------------------------------------------------------------------------
		$uid = $bk_id . '@' . $host; // stable UID not tied to start time.
		$eventobj->addNode( new ZCiCalDataNode( 'UID:' . $uid ) );

		// DTSTAMP / CREATED / LAST-MODIFIED in UTC Z.
		$eventobj->addNode( new ZCiCalDataNode( 'DTSTAMP:' . wpbc_to_ics_utc( null, $export_tz ) ) );
		$eventobj->addNode( new ZCiCalDataNode( 'CREATED:' . wpbc_to_ics_utc( $bk_arr->modification_date, $export_tz ) ) );
		$eventobj->addNode( new ZCiCalDataNode( 'LAST-MODIFIED:' . wpbc_to_ics_utc( $bk_arr->modification_date, $export_tz ) ) );

		// ---------------------------------------------------------------------------------------------
		// ATTENDEE (optional)
		// ---------------------------------------------------------------------------------------------
		if ( isset( $bk_arr->form_data['email'] ) && ( 'On' !== $is_hide_details ) ) {
			$cn_title = ( isset( $bk_arr->form_data['name'] ) ? ' ' . esc_textarea( $bk_arr->form_data['name'] ) : '' );
			$cn_title .= ( isset( $bk_arr->form_data['firstname'] )
				? ' ' . esc_textarea( $bk_arr->form_data['firstname'] ) : '' );
			$cn_title .= ( isset( $bk_arr->form_data['secondname'] )
				? ' ' . esc_textarea( $bk_arr->form_data['secondname'] ) : '' );
			$cn_title .= ( isset( $bk_arr->form_data['lastname'] )
				? ' ' . esc_textarea( $bk_arr->form_data['lastname'] ) : '' );

			$cn_clean       = trim( wpbm_esc_to_plain_text( $cn_title ) );
			$cn_clean       = addcslashes( $cn_clean, "\\,;\"" ); // escape per RFC
			$attendee_email = $bk_arr->form_data['email'];
			$eventobj->addNode( new ZCiCalDataNode( 'ATTENDEE;CN="' . $cn_clean . '":MAILTO:' . $attendee_email ) );
		}
	}

	// Output iCalendar text.
	// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $icalobj->export();

	$ics_export = ob_get_contents();
	$ics_export = trim( $ics_export );
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