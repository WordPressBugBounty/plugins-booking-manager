<?php 
/**
 * @version 1.0
 * @description Booking Calendar integration - Support Functions
 * 
 * @author wpdevelop
 * @link https://oplugins.com/
 * @email info@oplugins.com
 *
 * @modified 2017-06-28
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////				
//   S u p p o r t    Functions for integration with Booking Calendar 
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	


// Final Class for Creation  new Bookings into  Booking Calendar 
final class WPBM_create_bookings_from_events {

	static private $instance = NULL;

	public static $current_ics_dates = array();					// unlike a const, static property values can be changed


	/** Init this class only once
	 * 
	 * @return obj
	 */
	public static function init() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WPBM_create_bookings_from_events ) ) {
			self::$instance = new WPBM_create_bookings_from_events;
		} 
		return self::$instance;       
	}


	/** Get array  of assigned form  fields from  the Booking Calendar plugin
	 * 
	 * @return array			array(
											[0] => Array (
													[ics_field_name] => title
													[type] => text
													[name] => name
												)
											[1] => Array (
													[ics_field_name] => description
													[type] => textarea
													[name] => details
												)
										)
	 */
	public static function get_assigned_form_fields() {

		if ( ! function_exists( 'get_bk_option' ) ) return false;

		$wpbc_assigned_form_fields = get_bk_option( 'booking_gcal_events_form_fields' ); 
		$wpbc_assigned_form_fields = maybe_unserialize( $wpbc_assigned_form_fields );    
		/** $wpbc_assigned_form_fields = array(
												[title]			=> text^name
												[description]	=> customform^textarea^details
												[where]			=> text^
											)
		 */


		$assigned_fields_arr = array();

		if ( is_array( $wpbc_assigned_form_fields ) ) {        //FixIn: 2.0.9.2
			foreach ( $wpbc_assigned_form_fields as $assign_ics_field_name => $wpbc_field_str ) {

				$wpbc_field_arr       = explode( '^', $wpbc_field_str );
				$wpbc_field_arr_count = count( $wpbc_field_arr ) - 1;

				if ( ! empty( $wpbc_field_arr[ $wpbc_field_arr_count ] ) ) {
					$assigned_fields_arr[] = array(
						'ics_field_name' => $assign_ics_field_name
					  , 'type'           => $wpbc_field_arr[ ( $wpbc_field_arr_count - 1 ) ]
					  , 'name'           => $wpbc_field_arr[ $wpbc_field_arr_count ]
					);
				}

			}
		}
		/**  $assigned_fields_arr = array(
											[0] => Array (
													[ics_field_name] => title
													[type] => text
													[name] => name
												)
											[1] => Array (
													[ics_field_name] => description
													[type] => textarea
													[name] => details
												)
										)
		 */
		return $assigned_fields_arr;
	}


	public static function set_ics_dates( $value ) {

		self::$current_ics_dates = $value;
	}

	public static function get_ics_dates() {
		return self::$current_ics_dates;
	}

	public static function erase_ics_dates() {
		self::$current_ics_dates = array();
	}



}
WPBM_create_bookings_from_events::init();		// Initial Start


/** Hook for overwriting saving dates
 * 
 *  //  From: ..\wp-content\plugins\booking\core\wpbc-dates.php  
 *  //	   	  apply_filters( 'wpbc_get_insert_sql_for_dates', $is_return_dates, $dates_in_diff_formats , $is_approved_dates, $booking_id, $is_return_only_array );	
 * 
 * @param type $is_return_dates
 * @param type $dates_in_diff_formats
 * @param type $is_approved_dates
 * @param type $booking_id
 * @param type $is_return_only_array
 * @return type
 */
function wpbm_get_insert_sql_for_dates( $is_return_dates, $dates_in_diff_formats , $is_approved_dates, $booking_id, $is_return_only_array ) {

	$dates_to_insert = WPBM_create_bookings_from_events::get_ics_dates();

	if ( empty( $dates_to_insert ) )
		return  $is_return_dates;

	$insert_arr = array();
	$insert = array();

	foreach ( $dates_to_insert as $date) {

		// Loop
		$insert_arr []= array( $booking_id, $date, $is_approved_dates );			
		$insert	    []= "('$booking_id', '$date', '$is_approved_dates' )";

	}

	$insert = implode( ',', $insert );

//debuge($booking_id, $insert);

	if ( $is_return_only_array ) {
		return $insert_arr;
	} else {
		return $insert;
	}

}


/** Hook for overwriting updating dates. Usually  used for do not allow to  make this updating.
 *
 *  //  From: ..\wp-content\plugins\booking\inc\_bl\biz_l.php
 *  //	   	  $is_continue = apply_filters( 'wpbc_is_reupdate_dates_to_child_resources', $is_exit, $booking_id, $bktype, $dates, $start_end_time_arr , $formdata, $skip_page_checking_for_updating );
 *
 * @param type $is_exit
 * @param type $booking_id
 * @param type $bktype
 * @param type $dates
 * @param type $start_end_time_arr
 * @param type $formdata
 * @param type $skip_page_checking_for_updating
 * @return boolean
 */
function wpbm_is_reupdate_dates_to_child_resources( $is_exit, $booking_id, $bktype, $dates, $start_end_time_arr , $formdata, $skip_page_checking_for_updating ) {
	return true;
}



/** Getting only Booking fields ( starting '_BOOKING' ) from  Parsed ICS array 
 * - its support helpful function
 * 
 * @param type $ics_array
 * @return type
 */
function wpbm_get_booking_fields_from_ics_array( $ics_array ) {

	$booking_array = array();

	foreach ( $ics_array as $ics_n => $ics_event_arr ) {

		$one_booking = array();

		foreach ( $ics_event_arr as $event_field_key => $event_field_value ) {

			$is_booking_field = strpos( $event_field_key, '_BOOKING' );
			if ( $is_booking_field !== false ) {
				$one_booking[ $event_field_key ] = $event_field_value;
			}
		}

		if ( ! empty( $one_booking ) ) {
			$booking_array[] = $one_booking;
		}
	}
	return $booking_array;
}