<?php 
/**
 * @version 1.0
 * @package Booking Manager 
 * @subpackage Support Functions
 * @category Functions
 * 
 * @author wpdevelop
 * @link https://oplugins.com/
 * @email info@oplugins.com
 *
 * @modified 29.09.2015
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


 //                                                                              <editor-fold   defaultstate="collapsed"   desc=" V e r s i o n s " >    
////////////////////////////////////////////////////////////////////////////////
// V e r s i o n s
////////////////////////////////////////////////////////////////////////////////    

/** Get version
 * 
 * @return string
 */
function get_wpbm_version(){ 
	$version = 'free';
	return $version;
}
    
/** Check if user accidentially update Booking Manager Paid version to Free
 * 
 * @return bool
 */
function wpbm_is_updated_paid_to_free() {

	if ( ( wpbm_is_table_exists('wpbm_log') ) && ( ! class_exists('wpbm_personal') )  ) 
		return  true;
	else
		return false;                    
}
        
function wpbm_get_ver_sufix() {

	if ( strpos( strtolower( WPBM_VERSION ), 'multisite' ) !== false ) {
		$v_type = '-multi';
	} else if ( strpos( strtolower( WPBM_VERSION ), 'develop' ) !== false ) {
		$v_type = '-dev';
	} else {
		$v_type = '';
	}
	$v = '';
	if ( class_exists( 'wpbm_personal' ) )
		$v = 'ps' . $v_type;
	if ( class_exists( 'wpbm_pro' ) )
		$v = '';
	return $v;
}

function wpbm_up_link() {
	if ( ! wpbm_is_this_demo() ) 
		 $v = wpbm_get_ver_sufix();
	else $v = '';
	return 'https://oplugins.com/plugins/booking-manager/' . ( ( empty($v) ) ? '' : 'upgrade-' . $v  . '/' ) ;
}
    
/** Check if this demo website
 * 
 * @return bool
 */
function wpbm_is_this_demo() {
//return ! true;     //TODO: comment it. 2016-09-27    // Replaced!   
	if (
			(  ( isset( $_SERVER['SCRIPT_FILENAME'] ) ) && ( strpos( sanitize_text_field( wp_unslash($_SERVER['SCRIPT_FILENAME']) ), 'oplugins.com' ) !== false ) )
		||  (  ( isset( $_SERVER['HTTP_HOST'] ) ) && ( strpos( sanitize_text_field( wp_unslash($_SERVER['HTTP_HOST'])), 'oplugins.com' ) !== false )  )
	  ) 
		return true;
	else
		return false;
}

/** Get Warning Text  for Demo websites */
function wpbm_get_warning_text_in_demo_mode() {

	return '<div class="wpbm-settings-notice notice-warning"><strong>Warning!</strong> Demo test version does not allow changes to these items.</div>';
}

/** Show System Info (status) at item > Settings General page
 *  Link: http://server.com/wp-admin/admin.php?page=wpbm-settings&system_info=show#wpbm_general_settings_system_info_metabox
 */
function wpbm_system_info() {
	echo '---';
}



/** Check  if "Booking Calendar" installed/activated and return version number
 * 
 * @return string - 0 if not installed,  otherwise version num
 */
function wpbm_get_wpbc_version() {
	
	if ( ! defined( 'WP_BK_VERSION_NUM' ) )
		return 0;
	else 
		return WP_BK_VERSION_NUM;
}

/** Is activated Booking Calendar support WPBM
 * 
 * @return boolean
 */
function wpbm_is_wpbc_supported() {
	
	// 7.2.1 - its start version of Booking Calendar which  support integration  with   Booking Manager 2.0

	if ( version_compare( wpbm_get_wpbc_version(), '9.8') >= 0 ) {
		return true;
	} else {
		return false;
	}	
}

//                                                                              </editor-fold>


//                                                                              <editor-fold   defaultstate="collapsed"   desc=" F o r m a t t i n g " >    
////////////////////////////////////////////////////////////////////////////////
// F o r m a t t i n g
////////////////////////////////////////////////////////////////////////////////    
/**
 * Sanitize term to Slug format (no spaces, lowercase).
 * urldecode - reverse munging of UTF8 characters.
 *
 * @param mixed $value
 * @return string
 */
function wpbm_get_slug_format( $value ) {
    return  urldecode( sanitize_title( $value ) );
}

/**
 * Get Slug Format Option Value for saving to  the options table.
 * Replacing - to _ and restrict length to 64 characters.
 * 
 * @param string $value
 * @return string
 */
function wpbm_get_slug_format_4_option_name( $value ) {
    
    $value = wpbm_get_slug_format( $value );
    $value = str_replace('-', '_', $value);
    $value = substr($value, 0, 64);
    return $value;
}

/** Insert New Line symbols after <br> tags. Usefull for the settings pages to  show in redable view
 * 
 * @param type $param
 * @return type
 */
function wpbm_nl_after_br($param) {

	$value = preg_replace( "@(&lt;|<)br\s*/?(&gt;|>)(\r\n)?@", "<br/>", $param );

	return $value;
}

/**
 * Replace ** to <strong> and * to  <em>
 * 
 * @param String $text
 * @return string
 */
if ( ! function_exists( 'wpbm_recheck_strong_symbols' ) ) { 
function wpbm_recheck_strong_symbols( $text ){

	$patterns =  '/(\*\*)(\s*[^\*\*]*)(\*\*)/';    
	$replacement = '<strong>${2}</strong>';
	$value_return = preg_replace($patterns, $replacement, $text);

	$patterns =  '/(\*)(\s*[^\*]*)(\*)/';    
	$replacement = '<em>${2}</em>';
	$value_return = preg_replace($patterns, $replacement, $value_return);

	return $value_return;
}
}


/** Esacpe and replace any HTML entities
 * 
 * @param type $string
 * @return string
 */
function wpbm_esc_to_plain_text( $string ) {

//        //Replace <a href="http://server.com">Link</a> to Link( http://server.com )
//        $pattern = "/<a(.*)+href=[\"|']+([^\"']+)(?=(\"|'))[^>]*>(.*)<\/a>/" ;      //"/(?<=href=(\"|'))[^\"']+(?=(\"|'))/i";
//        $newurl = "$4 ($2)";
//        $string = preg_replace($pattern,$newurl,$string);


	// List of preg* regular expression patterns to search for replace in plain emails. More: https://raw.github.com/ushahidi/wp-silcc/master/class.html2text.inc
	$plain_search_array = array(
						   "/\r/",                                          // Non-legal carriage return
						   '/&(nbsp|#160);/i',                              // Non-breaking space
						   '/&(quot|rdquo|ldquo|#8220|#8221|#147|#148|#34|#034);/i', // Double quotes						//FixIn: 2.0.1.6
						   '/&(apos|rsquo|lsquo|#8216|#8217|#39|#039);/i',           // Single quotes						//FixIn: 2.0.1.6
						   '/&gt;/i',                                       // Greater-than
						   '/&lt;/i',                                       // Less-than
						   '/&#38;/i',                                      // Ampersand
						   '/&#038;/i',                                     // Ampersand
						   '/&amp;/i',                                      // Ampersand
						   '/&(copy|#169);/i',                              // Copyright
						   '/&(trade|#8482|#153);/i',                       // Trademark
						   '/&(reg|#174);/i',                               // Registered
						   '/&(mdash|#151|#8212);/i',                       // mdash
						   '/&(ndash|minus|#8211|#8722);/i',                // ndash
						   '/&(bull|#149|#8226);/i',                        // Bullet
						   '/&(pound|#163);/i',                             // Pound sign
						   '/&(euro|#8364);/i',                             // Euro sign
						   '/&#36;/',                                       // Dollar sign
						   '/&[^&;]+;/i',                                   // Unknown/unhandled entities
						   '/[ ]{2,}/'                                      // Runs of spaces, post-handling
					);

	// List of symbols for Replace
	$get_plain_replace_array = array(
							'',                                             // Non-legal carriage return
							' ',                                            // Non-breaking space
							'"',                                            // Double quotes
							"'",                                            // Single quotes
							'>',                                            // Greater-than
							'<',                                            // Less-than
							'&',                                            // Ampersand
							'&',                                            // Ampersand
							'&',                                            // Ampersand
							'(c)',                                          // Copyright
							'(tm)',                                         // Trademark
							'(R)',                                          // Registered
							'--',                                           // mdash
							'-',                                            // ndash
							'*',                                            // Bullet
							'£',                                            // Pound sign
							'EUR',                                          // Euro sign. € ?
							'$',                                            // Dollar sign
							'',                                             // Unknown/unhandled entities
							' '                                             // Runs of spaces, post-handling
				);		

	$newstring = preg_replace( $plain_search_array, $get_plain_replace_array, wp_strip_all_tags( $string ) );

	return $newstring;
}
//                                                                              </editor-fold>


//                                                                              <editor-fold   defaultstate="collapsed"   desc=" S u p p o r t " >    
////////////////////////////////////////////////////////////////////////////////
//  S u p p o r t    Functions
////////////////////////////////////////////////////////////////////////////////

/** Replace shortcodes in string
 * 
 * @param string $subject - string to  manipulate
 * @param array $replace_array - array with  values to  replace                 // array( [wpbm_id] => 9, [id] => 9, [dates] => July 3, 2016 14:00 - July 4, 2016 16:00, .... )
 * @param mixed $replace_unknown_shortcodes - replace unknown params, if false, then  no replace unknown params
 * @return string
 */
function wpbm_replace_shortcodes( $subject, $replace_array , $replace_unknown_shortcodes = ' ' ) {

    $defaults = array(
        'ip'                => apply_wpbm_filter( 'wpbm_get_user_ip' )
        , 'blogname'        => wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES )
        , 'siteurl'         => get_site_url()
    );

    $replace = wp_parse_args( $replace_array, $defaults );

    foreach ( $replace as $replace_shortcode => $replace_value ) {

		$replace_value = esc_js( $replace_value );        // FixIn:

        $subject = str_replace( array(   '[' . $replace_shortcode . ']'
                                       , '{' . $replace_shortcode . '}' )
                                , $replace_value
                                , $subject );
    }

    // Remove all shortcodes, which is not replaced early.
    if ( $replace_unknown_shortcodes !== false )    
        $subject = preg_replace( '/[\s]{0,}[\[\{]{1}[a-zA-Z][0-9a-zA-Z:._-]{0,}[\]\}]{1}[\s]{0,}/', $replace_unknown_shortcodes, $subject );  

    
    return $subject;        
}

/** Simple hack  to  make array strings lowercase
 * 
 * @param type $array
 * @return type
 */
function wpbm_arraytolower( $array ){
	return unserialize( strtolower( serialize( $array ) ) );
}

/** Support 'hash_equals' this function at older servers than PHP 5.6.0 */
if ( !function_exists( 'hash_equals' ) ) {
	function hash_equals( $known_string, $user_string ) {
		$ret = 0;

		if ( strlen( $known_string ) !== strlen( $user_string ) ) {
			$user_string = $known_string;
			$ret = 1;
		}

		$res = $known_string ^ $user_string;

		for ( $i = strlen( $res ) - 1; $i >= 0; --$i ) {
			$ret |= ord( $res[$i] );
		}

		return !$ret;
	}
}

/** Check if this valid timestamp
 * 
 * @param string|int $timestamp
 * @return bool
 */
function wpbm_is_valid_timestamp( $timestamp ) {
	return (   ( (string) (int) $timestamp === $timestamp) 
			&& ($timestamp <= PHP_INT_MAX)
			&& ($timestamp >= ~PHP_INT_MAX) 
		   );
}
//                                                                              </editor-fold>

	
//                                                                              <editor-fold   defaultstate="collapsed"   desc=" F i l e s    &&    U R L s " >    
////////////////////////////////////////////////////////////////////////////////
//  F i l e s    &&    U R L s
////////////////////////////////////////////////////////////////////////////////

/** Get absolute URL to  relative plugin path.
 *  Depend from the WPBM_MIN contant can  be load minified version of file,  if its exist
 * @param string $path    - path
 * @return string
 */
function wpbm_plugin_url( $path ) {

	if ( ( defined( 'WPBM_MIN' ) ) && ( WPBM_MIN ) ){
		$path_min = $path;
		if ( substr( $path_min , -3 ) === '.js' ) {
			$path_min = substr( $path_min , 0, -3 ) . '.min.js';
		}
		if ( substr( $path_min , -4 ) === '.css' ) {
			$path_min = substr( $path_min , 0, -4 ) . '.min.css';
		}
		if (  file_exists( trailingslashit( WPBM_PLUGIN_DIR ) . ltrim( $path_min, '/\\' ) )  )  // check if this file exist
			return trailingslashit( WPBM_PLUGIN_URL ) . ltrim( $path_min, '/\\' );
	}
	return trailingslashit( WPBM_PLUGIN_URL ) . ltrim( $path, '/\\' );
}

/** Check  if such file exist or not.
 * 
 * @param string $path - relative path to  file (relative to plugin folder).
 * @return boolean true | false
 */
function wpbm_is_file_exist( $path ) {

	if (  file_exists( trailingslashit( WPBM_PLUGIN_DIR ) . ltrim( $path, '/\\' ) )  )  // check if this file exist
		return true;
	else 
		return false;
}
 
/** Set URL from absolute to relative (starting from /)
 * 
 * @param type $url
 * @return type
 */
function wpbm_set_relative_url( $url ){

	$url = esc_url_raw($url);

	$url_path = parse_url($url,  PHP_URL_PATH);
	$url_path =  ( empty($url_path) ? $url : $url_path );

	$url =  trim($url_path, '/');
	return  '/' . $url;
}

/** Get Correct Relative URL 
 * 
 * @param type $link
 * @return string
 */
function wpbm_make_link_relative( $link ){

	if ( $link  == get_option('siteurl') ) 
		$link = '/';
	$link = '/' . trim( wp_make_link_relative( $link ), '/' ); 

	return $link;        
}

/** Get Correct Absolute URL 
 * 
 * @param string $link
 * @return type
 */
function wpbm_make_link_absolute( $link ){

	if ( ( $link  != get_option('siteurl') ) && ( strpos($link, 'http') !== 0 ) )
		$link  = get_option('siteurl') . '/' . trim( wp_make_link_relative( $link ), '/' ); 
	return esc_js( $link ) ;
}


if (!function_exists ('get_file_data_wpdev')) {
    
	/** Get header info from this file, just for compatibility with WordPress 2.8 and older versions
	 * 
	 * @param type $file
	 * @param type $default_headers
	 * @param type $context
	 * @return type
	 */
	function get_file_data_wpdev( $file, $default_headers, $context = '' ) {
        // We don't need to write to the file, so just open for reading.
        $fp = fopen( $file, 'r' );		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen

        // Pull only the first 8kiB of the file in.
        $file_data = fread( $fp, 8192 );// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fread

        // PHP will close file handle, but we are good citizens.
        fclose( $fp );// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose

        if( $context != '' ) {
            $extra_headers = array();								//apply_filters( "extra_$context".'_headers', array() );

            $extra_headers = array_flip( $extra_headers );
            foreach( $extra_headers as $key=>$value ) {
                $extra_headers[$key] = $key;
            }
            $all_headers = array_merge($extra_headers, $default_headers);
        } else {
            $all_headers = $default_headers;
        }

        foreach ( $all_headers as $field => $regex ) {
            preg_match( '/' . preg_quote( $regex, '/' ) . ':(.*)$/mi', $file_data, ${$field});
            if ( !empty( ${$field} ) )
                ${$field} =  trim(preg_replace("/\s*(?:\*\/|\?>).*/", '',  ${$field}[1] ));
            else
                ${$field} = '';
        }

        $file_data = compact( array_keys( $all_headers ) );

        return $file_data;
    }
}


/** Get content from  specific URL
 * 
 * @param string $url
 * @return string|boolean (false on error)
 */
function wpbm_get_ssl_page_content( $url ) {

	$request = new WP_Http();

	$result = $request->request( $url
								 , array(							// Default Parameters
			'reject_unsafe_urls' => true,                           //FixIn: 2.0.29.1
			'user-agent' => 'Mozilla/5.0 (iPad; U; CPU OS 3_2_1 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Mobile/7B405'       //FixIn: 2.0.12.1
										//	'method' => 'GET',
										//	'timeout' => 5,																// timeout value for an HTTP request.
										//	'redirection' => 5,															// number of redirects allowed during an HTTP request.												
										//	'httpversion' => '1.0',												
										//	'user-agent' => 'WordPress/' . get_bloginfo( 'version' ) . '; ' . get_bloginfo( 'url' ),
										//	'reject_unsafe_urls' => false,
										//	'blocking' => true,
										//	'headers' => array(),
										//	'cookies' => array(),
										//	'body' => null,
										//	'compress' => false,
										//	'decompress' => true,
										//	'sslverify' => true,
										//	'sslcertificates' => ABSPATH . WPINC . '/certificates/ca-bundle.crt',
										//	'stream' => false,
										//	'filename' => null,
										//	'limit_response_size' => null
									) 
						);

	if ( 
		   ( ! is_wp_error( $result ) ) 
		&& ( $result[ 'response' ][ 'code' ] == '200' ) 
	) {

		return $result[ 'body' ];

	} else {

		//FixIn: 2.0.2.2
		if ( is_wp_error( $result ) ) {
			$error_message = $result->get_error_message();
		} else {
			$error_message = __( 'Unknown error during downloading feed', 'booking-manager' );

			// Show more detail info of not ability to  download .ics feeds.	//FixIn: 2.0.10.5
			$error_message .= $result[ 'body' ];
			//do_action( 'wpbc_admin_show_top_notice', $error_message, 'error', 5000 );
			//die;
		}
		do_action( 'wpbc_admin_show_top_notice', $error_message, 'error', 5000 );										// N_O_T_I_C_E  in  H_E_A_D_E_R
// debuge($error_message);																							//FixIn: 2.0.1.3	//FixIn: 2.0.8.2

		return false;
	}
}



/** Count the number of bytes of a given string.
* Input string is expected to be ASCII or UTF-8 encoded.
* Warning: the function doesn't return the number of chars
* in the string, but the number of bytes.
* See http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
* for information on UTF-8.
*
* @param string $str The string to compute number of bytes
*
* @return The length in bytes of the given string.
*/
function wpbm_get_bytes_from_str( $str ) {
	// STRINGS ARE EXPECTED TO BE IN ASCII OR UTF-8 FORMAT
	// Number of characters in string
	$strlen_var = strlen( $str );

	$d = 0;			// string bytes counter

	// Iterate over every character in the string, escaping with a slash or encoding to UTF-8 where necessary
	for ( $c = 0; $c < $strlen_var; ++ $c ) {
		$ord_var_c = ord( $str[$c] );        //FixIn: 2.0.17.1
		switch ( true ) {
			case(($ord_var_c >= 0x20) && ($ord_var_c <= 0x7F)):		// characters U-00000000 - U-0000007F (same as ASCII)
				$d ++;
				break;
			case(($ord_var_c & 0xE0) == 0xC0):						// characters U-00000080 - U-000007FF, mask 110XXXXX
				$d += 2;
				break;
			case(($ord_var_c & 0xF0) == 0xE0):						// characters U-00000800 - U-0000FFFF, mask 1110XXXX
				$d += 3;
				break;
			case(($ord_var_c & 0xF8) == 0xF0):						// characters U-00010000 - U-001FFFFF, mask 11110XXX
				$d += 4;
				break;
			case(($ord_var_c & 0xFC) == 0xF8):						// characters U-00200000 - U-03FFFFFF, mask 111110XX
				$d += 5;
				break;
			case(($ord_var_c & 0xFE) == 0xFC):						// characters U-04000000 - U-7FFFFFFF, mask 1111110X
				$d += 6;
				break;
			default:
				$d ++;
		}
	}
	return $d;
}


//                                                                              </editor-fold>


//                                                                              <editor-fold   defaultstate="collapsed"   desc=" A d m i n    M e n u    L i n k s " >    	
////////////////////////////////////////////////////////////////////////////
// A d m i n    M e n u    L i n k s
////////////////////////////////////////////////////////////////////////////

/** Get URL to specific Admin Menu page
 * 
 * @param string $menu_type         -   { item | add | resources | settings }
 * @param boolean $is_absolute_url  - Absolute or relative url { default: true }
 * @return string                   - URL  to  menu
 */
function wpbm_get_menu_url( $menu_type, $is_absolute_url = true ) {

	switch ( $menu_type) {

		case 'master':														// Master
						$link = 'oplugins&tab=wpbm';
						break;

		case 'add':                                                         // Add New
		case 'new':                                                         // Add New
						$link = 'oplugins&wpbm-new';
						break;

		case 'settings':                                                    // Settings
		case 'options':
						$link = 'oplugins&tab=wpbm-settings';
						break;

		default:                                                            // Master
						$link = 'oplugins';
						break;
	}

	if ( $is_absolute_url ) {
		$link = admin_url( 'admin.php' ) . '?page=' . $link ;
	} 

	return $link;        
}

// // // // // // // // // // // // // // // // // // // // // // // // // /

/** Get URL of item Listing or Calendar Overview page
 * 
 * @param boolean $is_absolute_url  - Absolute or relative url { default: true }
 * @param boolean $is_old           - { default: true } 
 * @return string                   - URL  to  menu
 */
function wpbm_get_master_url( $is_absolute_url = true ) {
	return wpbm_get_menu_url( 'master', $is_absolute_url );
}

/** Get URL of item > Add item page 
 * 
 * @param boolean $is_absolute_url  - Absolute or relative url { default: true }
 * @param boolean $is_old           - { default: true } 
 * @return string                   - URL  to  menu
 */
function wpbm_get_new_wpbm_url( $is_absolute_url = true ) {
	return wpbm_get_menu_url( 'add', $is_absolute_url );
}

/** Get URL of item > Settings page 
 * 
 * @param boolean $is_absolute_url  - Absolute or relative url { default: true }
 * @param boolean $is_old           - { default: true } 
 * @return string                   - URL  to  menu
 */
function wpbm_get_settings_url( $is_absolute_url = true ) {
	return wpbm_get_menu_url( 'settings', $is_absolute_url );
}
    
// // // // // // // // // // // // // // // // // // // // // // // // // /

/** Check if this item Listing or Calendar Overview page
 * @param string $server_param -  'REQUEST_URI' | 'HTTP_REFERER'  Default: 'REQUEST_URI'
 * @return boolean true | false
 */
function wpbm_is_master_page( $server_param = 'REQUEST_URI' ) { 

	if (  ( is_admin() ) && isset($_SERVER[ $server_param ]) &&
		  ( strpos(sanitize_text_field( wp_unslash($_SERVER[ $server_param ])),'page=oplugins') !== false ) &&  // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
		  ( strpos(sanitize_text_field( wp_unslash($_SERVER[ $server_param ])),'tab=wpbm-') === false ) &&		// not the settings   // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
		  (	   ( strpos(sanitize_text_field( wp_unslash($_SERVER[ $server_param ])),'tab=wpbm') !== false )		// tab specified   // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
			|| ( strpos(sanitize_text_field( wp_unslash($_SERVER[ $server_param ])),'tab=') === false )  )		// or tab not specified at all   // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
		) {
		return true;
	} 
	return false;
}

/** Check if this item > Add item page 
 * @param string $server_param -  'REQUEST_URI' | 'HTTP_REFERER'  Default: 'REQUEST_URI'
 * @return boolean true | false
 */
function wpbm_is_new_wpbm_page( $server_param = 'REQUEST_URI' ) {

	if (  ( is_admin() ) && isset($_SERVER[ $server_param ]) &&
		  ( strpos(sanitize_text_field( wp_unslash($_SERVER[ $server_param ])),'page=oplugins') !== false ) &&   // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
		  ( strpos(sanitize_text_field( wp_unslash($_SERVER[ $server_param ])),'tab=wpbm-new') !== false )   // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
		) {
		return true;
	} 
	return false;
}


/** Check if this item > Settings page 
 * @param string $server_param -  'REQUEST_URI' | 'HTTP_REFERER'  Default: 'REQUEST_URI'
 * @return boolean true | false
 */    
function wpbm_is_settings_page( $server_param = 'REQUEST_URI' ) {

	if (  ( is_admin() ) && isset($_SERVER[ $server_param ]) &&
		  ( strpos(sanitize_text_field( wp_unslash($_SERVER[ $server_param ])),'page=oplugins') !== false ) &&   // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
		  ( strpos(sanitize_text_field( wp_unslash($_SERVER[ $server_param ])),'tab=wpbm-settings') !== false )   // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
		) {
		return true;
	} 
	return false;
}

//                                                                              </editor-fold>
    

//                                                                              <editor-fold   defaultstate="collapsed"   desc=" A d m i n    U I    E l e m e n t s " >        
////////////////////////////////////////////////////////////////////////////
// A d m i n    U I    E l e m e n t s
////////////////////////////////////////////////////////////////////////////

/** Get Number of new items
 * 
 * @return int
 */
function wpbm_get_number_new_items(){
	return 0;
}


/** Show Admin    B A R    .
 * 
 * @global type $wp_admin_bar
 * @return type
 */
function wp_admin_bar_items_menu(){

	global $wp_admin_bar;

	$current_user = wp_get_current_user();

	$curr_user_role = get_wpbm_option( 'wpbm_user_role_master' );
	$level = 10;
	if ($curr_user_role == 'administrator')       $level = 10;
	else if ($curr_user_role == 'editor')         $level = 7;
	else if ($curr_user_role == 'author')         $level = 2;
	else if ($curr_user_role == 'contributor')    $level = 1;
	else if ($curr_user_role == 'subscriber')     $level = 0;

	if ( ( $current_user->user_level < $level ) || ! is_admin_bar_showing() )
		return;


	$update_count = wpbm_get_number_new_items();	// 0

	$title = 'Booking Manager';
	$update_title =  $title;


	if ( $update_count > 0 ) {
		$update_count_title = "&nbsp;<span class='wpbm-count bk-update-count' style='background: #f0f0f1;color: #2c3338;display: inline;padding: 2px 5px;font-weight: 600;border-radius: 10px;'>" . number_format_i18n($update_count) . "</span>" ; //id='wpbm-count'
		$update_title .= $update_count_title;
	}

	$link_items	   = wpbm_get_master_url();
	$link_settings = wpbm_get_settings_url();


	$wp_admin_bar->add_menu(
			array(
				'id' => 'bar_wpbm',
				'title' => $update_title ,
				'href' => wpbm_get_master_url()
				)
			);


	 $curr_user_role_settings = get_wpbm_option( 'wpbm_user_role_settings' );
	 $level = 10;
	 if ($curr_user_role_settings == 'administrator')       $level = 10;
	 else if ($curr_user_role_settings == 'editor')         $level = 7;
	 else if ($curr_user_role_settings == 'author')         $level = 2;
	 else if ($curr_user_role_settings == 'contributor')    $level = 1;
	 else if ($curr_user_role_settings == 'subscriber')     $level = 0;

	 if (   ( ($current_user->user_level < $level)   ) || !is_admin_bar_showing() ) return;

	$wp_admin_bar->add_menu(
			array(
				'id' => 'bar_wpbm_new',
				'title' => __( 'Add New', 'booking-manager'),
				'href' => wpbm_get_new_wpbm_url(),
				'parent' => 'bar_wpbm',
			)
	);



	$wp_admin_bar->add_menu(
			array(
				'id' => 'bar_wpbm_settings',
				'title' => __( 'Settings', 'booking-manager'),
				'href' => wpbm_get_settings_url(),
				'parent' => 'bar_wpbm',
			)
	);

			$wp_admin_bar->add_menu(
					array(
						'id' => 'bar_wpbm_settings_email',
						'title' => __( 'Emails', 'booking-manager'),
						'href' => $link_settings . '&tab=email',
						'parent' => 'bar_wpbm_settings'
					)
			);

}
// add_action( 'admin_bar_menu', 'wp_admin_bar_items_menu', 70 );		// Add Admin Bar


/** Show Rating link at footer */
function wpbm_show_wpbm_footer(){ 

	// Nothing here.
}
//                                                                              </editor-fold>    
    
    
//                                                                              <editor-fold   defaultstate="collapsed"   desc=" DB - cheking if table, field or index exists " >        
////////////////////////////////////////////////////////////////////////////
// DB - cheking if table, field or index exists
////////////////////////////////////////////////////////////////////////////

/**
 * Check if table exist
 * 
 * @global type $wpdb
 * @param string $tablename
 * @return 0|1
 */
function wpbm_is_table_exists( $tablename ) {

	global $wpdb;

	if ( ( ! empty( $wpdb->prefix ) ) && ( strpos( $tablename, $wpdb->prefix ) === false ) ) {
		$tablename = $wpdb->prefix . $tablename;
	}



	$res = $wpdb->get_results( $wpdb->prepare( "SHOW TABLES LIKE %s", $tablename ) );    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching

	return count( $res );                                                     //FixIn 5.4.3.
}


/**
 * Check if table exist
 * 
 * @global type $wpdb
 * @param string $tablename
 * @param type $fieldname
 * @return 0|1
 */
function wpbm_is_field_in_table_exists( $tablename , $fieldname) {
	global $wpdb;
	if ( (! empty($wpdb->prefix) ) && ( strpos($tablename, $wpdb->prefix) === false ) ) $tablename = $wpdb->prefix . $tablename ;
	$sql_check_table = "SHOW COLUMNS FROM {$tablename}" ;

	$res = $wpdb->get_results( $sql_check_table ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared

	foreach ($res as $fld) {
		if ($fld->Field == $fieldname) return 1;
	}

	return 0;
}


/**
 * Check if index exist
 * 
 * @global type $wpdb
 * @param string $tablename
 * @param type $fieldindex
 * @return 0|1
 */
function wpbm_is_index_in_table_exists( $tablename , $fieldindex) {
	global $wpdb;
	if ( (! empty($wpdb->prefix) ) && ( strpos($tablename, $wpdb->prefix) === false ) ) $tablename = $wpdb->prefix . $tablename ;

	$res = $wpdb->get_results( $wpdb->prepare("SHOW INDEX FROM {$tablename} WHERE Key_name = %s", $fieldindex ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	if (count($res)>0) return 1;
	else               return 0;
}

//                                                                              </editor-fold>
		
 
//                                                                              <editor-fold   defaultstate="collapsed"   desc=" E s c a p i n g " >    	
////////////////////////////////////////////////////////////////////////////
// E s c a p i n g
////////////////////////////////////////////////////////////////////////////

/** Transform the REQESTS parameters (GET and POST) into URL
 * 
 * @param type $page_param
 * @param array $exclude_params
 * @param type $only_these_parameters
 * @return type
 */
function wpbm_get_params_in_url( $page_param , $exclude_params = array(), $only_these_parameters = false, $is_escape_url = false, $only_get = false ){

	$exclude_params[] = 'page';

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	if ( isset( $_GET['page'] ) ) { $page_param = $_GET['page']; }

	$get_paramaters = array( 'page' => $page_param );

	if ( $only_get )
		$check_params = $_GET;  // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	else 
		$check_params = $_REQUEST;  // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
//debuge($check_params);    
	foreach ( $check_params as $prm_key => $prm_value ) {

		// Skip  parameters arrays,  like $_GET['rvaluation_to'] = Array ( [0] => 6,  [1] => 14,  [2] => 14 )
		if ( 
			   (  is_string( $prm_value ) )  
			|| ( is_numeric( $prm_value ) ) 
			) {    

			if ( strlen( $prm_value ) > 1000 ) {                                    // Check  about TOOO long parameters,  if it exist  then  reset it.
				$prm_value = '';
			}

			if ( ! in_array( $prm_key, $exclude_params ) )
				if ( ( $only_these_parameters === false ) || ( in_array( $prm_key, $only_these_parameters ) ) )
						$get_paramaters[ $prm_key ] = $prm_value;
		}
	}
//debuge($check_params, $get_paramaters, $exclude_params );    
	$url = admin_url( add_query_arg(  $get_paramaters , 'admin.php' ) );

	if ( $is_escape_url )
		$url = esc_url( $url );

	return $url;

	/*      // Old variant:
			if ( isset( $_GET['page'] ) ) $page_param = $_GET['page'];

			$url_start = 'admin.php?page=' . $page_param . '&';    
			$exclude_params[] = 'page';
			foreach ( $_REQUEST as $prm_key => $prm_value ) {

				if ( !in_array( $prm_key, $exclude_params ) )
					if ( ($only_these_parameters === false) || ( in_array( $prm_key, $only_these_parameters ) ) )

						$url_start .= $prm_key . '=' . $prm_value . '&';

			}
			$url_start = substr( $url_start, 0, -1 );

			return $url_start;
	 */     
}


/** Clean Request Parameters
 * 
 */
function wpbm_check_request_paramters() { 

	$clean_params = array();  

	$clean_params[ 'wh_wpbm_id' ]			= 'digit_or_csd';		// '0' | '1' | ''
	$clean_params[ 'wh_wpbm_date' ]			= 'digit_or_date';		// number | date 2016-07-20
	$clean_params[ 'wh_wpbm_datenext' ]		= 'd';					// '1' | '2' ....
	$clean_params[ 'wh_pay_statuscustom' ]	= 's';					//string   !!! LIKE  !!!
	$clean_params[ 'wh_pay_status' ]		= array( 'all', 'group_ok', 'group_unknown', 'group_pending', 'group_failed' );

	foreach ( $clean_params as $request_key => $clean_type ) {

		// elements only listed in array::
		if (  is_array( $clean_type ) ) {                                       // check  only values from  the list  in this array

			if ( ( isset( $_REQUEST[ $request_key ] ) ) &&  ( ! in_array( $_REQUEST[ $request_key ], $clean_type ) ) )  // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				$clean_type = 's';    
			else 
				$clean_type = 'checked_skip_it';
		} 

		switch ( $clean_type ) {

			case 'checked_skip_it':

				break;

			case 'digit_or_date':                                            // digit or comma separated digit
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				if ( isset( $_REQUEST[ $request_key ] ) ) { $_REQUEST[ $request_key ] = wpbm_clean_digit_or_date( $_REQUEST[ $request_key ] ); }        // nums

				break;

			case 'digit_or_csd':                                            // digit or comma separated digit
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				if ( isset( $_REQUEST[ $request_key ] ) ) { $_REQUEST[ $request_key ] = wpbm_clean_digit_or_csd( $_REQUEST[ $request_key ] ); }       // nums

				break;

			case 's':                                                       // string
				 // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				if ( isset( $_REQUEST[ $request_key ] ) ) { $_REQUEST[ $request_key ] = wpbm_clean_like_string_for_db( $_REQUEST[ $request_key ] ); }

				break;

			case 'd':                                                       // digit
				 // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				if (( isset( $_REQUEST[ $request_key ] ) ) && ( $_REQUEST[ $request_key ] !== '' )) { $_REQUEST[ $request_key ] = intval( $_REQUEST[ $request_key ] ); }

				break;

			default:
				        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				if ( isset( $_REQUEST[ $request_key ] ) ) { $_REQUEST[ $request_key ] = intval( $_REQUEST[ $request_key ] ); }
				break;
		}


	}

}

    
/** Check  paramter  if it number or comma separated list  of numbers
 * 
 * @global type $wpdb
 * @param string $value
 * @return string
 * 
 * Exmaple:
					wpbm_clean_digit_or_csd( '12,a,45,9' )                  => '12,0,45,9'
 * or
					wpbm_clean_digit_or_csd( '10a' )                        => '10
 * or
					wpbm_clean_digit_or_csd( array( '12,a,45,9', '10a' ) )  => array ( '12,0,45,9',  '10' )
 */
function wpbm_clean_digit_or_csd( $value ) {                                //FixIn:6.2.1.4 

	if ( $value === '' ) return $value;


	if ( is_array( $value ) ) {
		foreach ( $value as $key => $check_value ) {
			$value[ $key ] = wpbm_clean_digit_or_csd( $check_value ); 
		}
		return $value;
	}


	global $wpdb;

	$value = str_replace( ';', ',', $value );

	$array_of_nums = explode(',', $value);

	$result = array();
	foreach ($array_of_nums as $check_element) {
		$result[] = $wpdb->prepare( "%d", $check_element );
	}
	$result = implode(',', $result );
	return $result;
}
    
    
/** Cehck  about Valid date,  like 2016-07-20 or digit
 * 
 * @param string $value
 * @return string or int
 */
function wpbm_clean_digit_or_date( $value ) {                               //FixIn:6.2.1.4

	if ( $value === '' ) return $value;

	if ( preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $value ) ) {

		return $value;                                                      // Date is valid in format: 2016-07-20
	} else {
		return intval( $value );
	}

}
    

/** Check $value for injection here
 * 
 * @param type $value
 * @return type
 */
function wpbm_clean_parameter( $value ) {

	$value = preg_replace( '/<[^>]*>/', '', $value );                       // clean any tags
	$value = str_replace( '<', ' ', $value ); 
	$value = str_replace( '>', ' ', $value ); 
	$value = wp_strip_all_tags( $value );

	// Clean SQL injection    
	$value = esc_sql( $value );

	return $value; 
}


function wpbm_esc_like( $value_trimmed ) {

	global $wpdb;
	if ( method_exists( $wpdb ,'esc_like' ) )
		return $wpdb->esc_like( $value_trimmed );                           // Its require minimum WP 4.0.0
	else
		return addcslashes( $value_trimmed, '_%\\' );                       // Direct implementation  from $wpdb->esc_like(
}


/** Clean user string for using in SQL LIKE statement - append to  LIKE sql
 * 
 * @param string $value - to clean
 * @return string       - escaped
 *                                  Exmaple:    
 *                                              $search_escaped_like_title = wpbm_clean_like_string_for_append_in_sql_for_db( $input_var );
 * 
 *                                              $where_sql = " WHERE title LIKE ". $search_escaped_like_title ." ";
 */
function wpbm_clean_like_string_for_append_in_sql_for_db( $value ) {
	global $wpdb;

	$value_trimmed = trim( stripslashes( $value ) );
	$wild          = '%';
	$like          = $wild . wpbm_esc_like( $value_trimmed ) . $wild;
	$sql           = $wpdb->prepare( "'%s'", $like );  // phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.QuotedSimplePlaceholder

	return $sql;    


/* Help:
	 * First half of escaping for LIKE special characters % and _ before preparing for MySQL.
 * Use this only before wpdb::prepare() or esc_sql().  Reversing the order is very bad for security.
 *
 * Example Prepared Statement:
 *
 *     $wild = '%';
 *     $find = 'only 43% of planets';
 *     $like = $wild . wpbm_esc_like( $find ) . $wild;
 *     $sql  = $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_content LIKE '%s'", $like );
 *
 * Example Escape Chain:
 *
 *     $sql  = esc_sql( wpbm_esc_like( $input ) );
 */        

}


/** Clean string for using in SQL LIKE requests inside single quotes:    WHERE title LIKE '%". $escaped_search_title ."%' 
 *  Replaced _ to \_     % to \%      \   to   \\
 * @param string $value - to clean
 * @return string       - escaped
 *                                  Exmaple:    
 *                                              $search_escaped_like_title = wpbm_clean_like_string_for_db( $input_var );
 * 
 *                                              $where_sql = " WHERE title LIKE '%". $search_escaped_like_title ."%' ";
 * 
 *                                  Important! Use SINGLE quotes after in SQL query:  LIKE '%".$data."%'
 */
function wpbm_clean_like_string_for_db( $value ){

	global $wpdb;

	$value_trimmed = trim( stripslashes( $value ) );

	$value_trimmed =  wpbm_esc_like( $value_trimmed );

	$value = trim( $wpdb->prepare( "'%s'",  $value_trimmed ) , "'" ); // phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.QuotedSimplePlaceholder

	return $value;

/* Help:
	 * First half of escaping for LIKE special characters % and _ before preparing for MySQL.
 * Use this only before wpdb::prepare() or esc_sql().  Reversing the order is very bad for security.
 *
 * Example Prepared Statement:
 *
 *     $wild = '%';
 *     $find = 'only 43% of planets';
 *     $like = $wild . wpbm_esc_like( $find ) . $wild;
 *     $sql  = $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_content LIKE '%s'", $like );
 *
 * Example Escape Chain:
 *
 *     $sql  = esc_sql( wpbm_esc_like( $input ) );
 */        
}


/** Escape string from SQL for the HTML form field
 * 
 * @param string $value
 * @return string
 * 
 * Used: esc_sql function.
 * 
 * https://codex.wordpress.org/Function_Reference/esc_sql 
 * Note: Be careful to use this function correctly. It will only escape values to be used in strings in the query. 
 * That is, it only provides escaping for values that will be within quotes in the SQL (as in field = '{$escaped_value}'). 
 * If your value is not going to be within quotes, your code will still be vulnerable to SQL injection. 
 * For example, this is vulnerable, because the escaped value is not surrounded by quotes in the SQL query: 
 * ORDER BY {$escaped_value}. As such, this function does not escape unquoted numeric values, field names, or SQL keywords. 
 *         
 */
function wpbm_clean_string_for_form( $value ){

	global $wpdb;

	$value_trimmed = trim( stripslashes( $value ) );

	$esc_sql_value =  esc_sql(  $value_trimmed );

	//$value = trim( $wpdb->prepare( "'%s'",  $esc_sql_value ) , "'" );

	$esc_sql_value = trim( stripslashes( $esc_sql_value ) );

	return $esc_sql_value;

}
//                                                                              </editor-fold>

    
//                                                                              <editor-fold   defaultstate="collapsed"   desc=" U s e r s " >    
////////////////////////////////////////////////////////////////////////////////
//  U s e r s
////////////////////////////////////////////////////////////////////////////////

/** Get ID of active user
 * 
 * @return type
 */
function get_wpbm_current_user_id() {
	$user = wp_get_current_user();
	return ( isset( $user->ID ) ? (int) $user->ID : 0 );
}


/** Check  if Current User have specific Role
 * 
 * @return bool Whether the current user has the given capability. 
 */
function wpbm_is_current_user_have_this_role( $user_role ) {

   if ( $user_role == 'administrator' )  $user_role = 'activate_plugins';
   if ( $user_role == 'editor' )         $user_role = 'publish_pages';
   if ( $user_role == 'author' )         $user_role = 'publish_posts';
   if ( $user_role == 'contributor' )    $user_role = 'edit_posts';
   if ( $user_role == 'subscriber')      $user_role = 'read';

   return current_user_can( $user_role );
}


function wpbm_get_user_ip() {
	return '---';
}
add_wpbm_filter( 'wpbm_get_user_ip', 'wpbm_get_user_ip' );
//                                                                              </editor-fold>


//                                                                              <editor-fold   defaultstate="collapsed"   desc=" Mesages for Admin panel  " >    
////////////////////////////////////////////////////////////////////////////////    
// Mesages for Admin panel 
////////////////////////////////////////////////////////////////////////////////    

function wpbm_show_fixed_message( $message, $time_to_show , $message_type = 'updated' , $notice_id = 0, $is_dismissible = false ) {

		// Generate unique HTML ID  for the message
		if ( $notice_id == 0 )
			$notice_id =  intval( time() * wp_rand(10, 100) );

		$notice_id = 'wpbm_system_notice_' . $notice_id;

		$is_dismissible = false;

		if ( 
			   ( ( $is_dismissible ) && ( ! wpbm_section_is_dismissed( $notice_id ) ) )
			|| ( ! $is_dismissible )
			 // || true 
		){

			?><div  id="<?php echo esc_attr($notice_id); ?>"
					class="wpbm_system_notice wpbm_is_dismissible wpbm_is_hideable <?php echo esc_attr( $message_type ); ?>"
					data-nonce="<?php echo esc_attr(wp_create_nonce( $nonce_name = $notice_id . '_wpbmnonce' )); ?>"
					data-user-id="<?php echo esc_attr(get_current_user_id()); ?>"
				><?php 

			wpbm_x_dismiss_button();

			echo wp_kses_post($message);

			?></div><?php

			// Get the time of message showing
			$time_to_show = intval( $time_to_show ) * 1000;

			 if ( $time_to_show > 0 ) { 
				?> <script type="text/javascript">                              				
						jQuery('#<?php echo esc_attr($notice_id); ?>').animate({opacity: 1},<?php echo esc_attr( $time_to_show ); ?>).fadeOut( 2000 );
				</script> <?php
			 }			
		}       	
}


/** Show Ajax message at the top of page
 * 
 * @param type $message
 * @param type $time_to_show
 * @param type $is_error
 */
function wpbm_show_ajax_message( $message, $time_to_show = 3000, $is_error = false ) {

	// Recheck  for any "lang" shortcodes for replacing to correct language
	$message =  apply_wpbm_filter('wpbm_check_for_active_language', $message );

	// Escape any JavaScript from  message
	$notice =   html_entity_decode( esc_js( $message ) ,ENT_QUOTES) ;

	?><script type="text/javascript">
		var my_message = '<?php echo esc_js( $notice ); ?>';
		wpbm_admin_show_message( my_message, '<?php echo ( $is_error ? 'error' : 'success' ); ?>', <?php echo esc_attr($time_to_show); ?> );
	</script><?php
}


/** Show "Saved Changes" message at  the top  of settings page.
 * 
 */    
function wpbm_show_changes_saved_message() {
	wpbm_show_message ( __('Changes saved.', 'booking-manager'), 5 );
}    


/** Show Message at  Top  of Admin Pages
 * 
 * @param type $message         - mesage to  show
 * @param type $time_to_show    - number of seconds to  show, if 0 or skiped,  then unlimited time.
 * @param type $message_type    - Default: updated   { updated | error | notice }
 */
function wpbm_show_message ( $message, $time_to_show , $message_type = 'updated') {

	// Generate unique HTML ID  for the message
	$inner_message_id =  intval( time() * wp_rand(10, 100) );

	// Get formated HTML message
	$notice = wpbm_get_formated_message( $message, $message_type, $inner_message_id );

	// Get the time of message showing
	$time_to_show = intval( $time_to_show ) * 1000;

	// Show this Message
	?> <script type="text/javascript">                              
		if ( jQuery('.wpbm_admin_message').length ) {
				jQuery('.wpbm_admin_message').append( '<?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo ($notice);
					?>' );
			<?php if ( $time_to_show > 0 ) { ?>
				jQuery('#wpbm_inner_message_<?php echo esc_attr($inner_message_id); ?>').animate({opacity: 1},<?php echo esc_attr($time_to_show); ?>).fadeOut( 2000 );
			<?php } ?>
		}
	</script> <?php
}


/** Escape and prepare message to  show it
 * 
 * @param type $message                 - message
 * @param type $message_type            - Default: updated   { updated | error | notice }
 * @param string $inner_message_id      - ID of message DIV,  can  be skipped
 * @return string
 */
function wpbm_get_formated_message ( $message, $message_type = 'updated', $inner_message_id = '') {


	// Recheck  for any "lang" shortcodes for replacing to correct language
	$message =  apply_wpbm_filter('wpbm_check_for_active_language', $message );

	// Escape any JavaScript from  message
	$notice =   html_entity_decode( esc_js( $message ) ,ENT_QUOTES) ;

	$notice .= '<a class="close tooltip_left" rel="tooltip" title="'. esc_js(__("Hide", 'booking-manager')). '" data-dismiss="alert" href="javascript:void(0)" onclick="javascript:jQuery(this).parent().hide();">&times;</a>';

	if (! empty( $inner_message_id ))
		$inner_message_id = 'id="wpbm_inner_message_'. $inner_message_id .'"';

	$notice = '<div '.$inner_message_id.' class="wpbm_inner_message '. $message_type . '">' . $notice . '</div>';

	return  $notice;
}


/** Show system info  in settings page
 * 
 * @param string $message                     ...  
 * @param string $message_type                'info' | 'warning' | 'error'
 * @param string $title                       __('Important!' , 'booking-manager')  |  __('Note' , 'booking-manager')
 * 
 * Exmaple:     wpbm_show_message_in_settings( __( 'Nothing Found', 'booking-manager'), 'warning', __('Important!' , 'booking-manager') );
 */
function wpbm_show_message_in_settings( $message, $message_type = 'info', $title = '' , $is_echo = true ) {

	$message_content = '';

	$message_content .= '<div class="clear"></div>';

	$message_content .= '<div class="wpbm-settings-notice notice-' . $message_type . '" style="text-align:left;">';

	if ( ! empty( $title ) )
		$message_content .=  '<strong>' . esc_js( $title ) . '</strong> ';

	$message_content .= html_entity_decode( esc_js( $message ) ,ENT_QUOTES) ;

	$message_content .= '</div>';

	$message_content .= '<div class="clear"></div>';

	if ( $is_echo )
		echo wp_kses_post( $message_content );
	else
		return $message_content;

}
//                                                                              </editor-fold>


//                                                                              <editor-fold   defaultstate="collapsed"   desc=" Settings Meta Boxes " >    
////////////////////////////////////////////////////////////////////////////////    
// Settings Meta Boxes
////////////////////////////////////////////////////////////////////////////////    
function wpbm_open_meta_box_section( $metabox_id, $title ) {

	$my_close_open_win_id = $metabox_id . '_metabox';
	//FixIn: 2.0.16.1
    ?>
    <div class='meta-box'>
        <div
                id="<?php echo esc_attr($my_close_open_win_id); ?>"
                class="postbox <?php if ( '1' == get_user_option( 'wpbm_win_' . $my_close_open_win_id ) ) echo 'closed'; ?>"
            ><div class="postbox-header" style="display: flex;flex-flow: row nowrap;border-bottom: 1px solid #ccd0d4;"><?php //FixIn: 8.7.8.1 ?>
				<h3 class='hndle' style="flex: 1 1 auto;border: none;">
                  <span><?php  echo wp_kses_post( $title ); ?></span>
			  	</h3>
				<div  title="<?php echo esc_attr(__('Click to toggle','booking-manager')); ?>"
                    class="handlediv"
                    onclick="javascript:wpbm_verify_window_opening(<?php echo esc_attr( get_wpbm_current_user_id() ); ?>, '<?php echo esc_attr($my_close_open_win_id); ?>');"
                ><br/></div>
			</div>
            <div class="inside">
    <?php
}

function wpbm_close_meta_box_section() {
	?>
			  </div> 
		</div> 
	</div>                        
	<?php
}
//                                                                              </editor-fold>


												// from Toolbar
//                                                                              <editor-fold   defaultstate="collapsed"   desc=" M o d a l s " >    
////////////////////////////////////////////////////////////////////////////////    
//  M o d a l s
////////////////////////////////////////////////////////////////////////////////

/** Start Loyouts - Modal Window structure */    
function wpbm_write_content_for_modals_start_here() {
    
    ?><span id="wpbm_content_for_modals"></span><?php
}
add_wpbm_action( 'wpbm_write_content_for_modals', 'wpbm_write_content_for_modals_start_here');    
//                                                                              </editor-fold>


//                                                                              <editor-fold   defaultstate="collapsed"   desc=" Inline     JavaScript " >    
////////////////////////////////////////////////////////////////////////////////
// Inline    J a v a S c r i p t    to Footer page
////////////////////////////////////////////////////////////////////////////////
/**
 * Queue  JavaScript for later output at  footer
 *
 * @param string $code
 */
function wpbm_enqueue_js( $code ) {
	global $wpbm_queued_js;

	if ( empty( $wpbm_queued_js ) ) {
		$wpbm_queued_js = '';
	}

	$wpbm_queued_js .= "\n" . $code . "\n";
}


/**
 * Output any queued javascript code in the footer.
 */
function wpbm_print_js() {

	global $wpbm_queued_js;

	if ( ! empty( $wpbm_queued_js ) ) {

		echo "<!-- WPBM JavaScript -->\n<script type=\"text/javascript\">\njQuery(function($) {";

		$wpbm_queued_js = wp_check_invalid_utf8( $wpbm_queued_js );

		$wpbm_queued_js = wp_specialchars_decode( $wpbm_queued_js , ENT_COMPAT);            // Converts double quotes  '&quot;' => '"'

		$wpbm_queued_js = preg_replace( '/&#(x)?0*(?(1)27|39);?/i', "'", $wpbm_queued_js );
		$wpbm_queued_js = str_replace( "\r", '', $wpbm_queued_js );

		echo $wpbm_queued_js . "});\n</script>\n<!-- End WPBM JavaScript -->\n";	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		$wpbm_queued_js = '';
		unset( $wpbm_queued_js );
	}
}

//                                                                              </editor-fold>

												// from Toolbar
//                                                                              <editor-fold   defaultstate="collapsed"   desc=" JS & CSS - Tooltips & Popover" >    
////////////////////////////////////////////////////////////////////////////////
// JS & CSS
////////////////////////////////////////////////////////////////////////////////

/** Load suport JavaScript for "Items" page*/
function wpbm_js_for_items_page() {
    
    $is_use_hints = get_wpbm_option( 'wpbm_is_use_hints_at_admin_panel'  );
    if ( $is_use_hints == 'On' )
      wpbm_bs_javascript_tooltips();                                            // JS Tooltips

    wpbm_bs_javascript_popover();                                               // JS Popover        
    
    //wpbm_datepicker_js();                                                       // JS  Datepicker
    wpbm_datepicker_css();                                                      // CSS DatePicker
}


/** Datepicker activation JavaScript */
function wpbm_datepicker_js() {
    
    ?><script type="text/javascript">
        jQuery(document).ready( function(){

            function applyCSStoDays( date ){
                return [true, 'date_available']; 
            }
            jQuery('input.wpbm-filters-section-calendar').datepick(
                {   beforeShowDay: applyCSStoDays,
                    showOn: 'focus',
                    multiSelect: 0,
                    numberOfMonths: 1,
                    stepMonths: 1,
                    prevText: '&laquo;',
                    nextText: '&raquo;',
                    dateFormat: 'yy-mm-dd',
                    changeMonth: false,
                    changeYear: false,
                    minDate: null, 
                    maxDate: null, //'1Y',
                    showStatus: false,
                    multiSeparator: ', ',
                    closeAtTop: false,
                    // firstDay:<?php //echo get_wpbm_option( 'wpbm_start_day_weeek' ); ?>,
                    gotoCurrent: false,
                    hideIfNoPrevNext:true,
                    useThemeRoller :false,
                    mandatory: true
                }
            );
        });
        </script><?php 
}


/** Support CSS - datepick,  etc... */
function wpbm_datepicker_css(){
    ?>
    <style type="text/css">
        #datepick-div .datepick-header {
               width: 172px !important;
        }
        #datepick-div {
            -border-radius: 3px;
            -box-shadow: 0 0 2px #888888;
            -webkit-border-radius: 3px;
            -webkit-box-shadow: 0 0 2px #888888;
            -moz-border-radius: 3px;
            -moz-box-shadow: 0 0 2px #888888;
            width: 172px !important;
        }
        #datepick-div .datepick .datepick-days-cell a{
            font-size: 12px;
        }
        #datepick-div table.datepick tr td {
            border-top: 0 none !important;
            line-height: 24px;
            padding: 0 !important;
            width: 24px;
        }
        #datepick-div .datepick-control {
            font-size: 10px;
            text-align: center;
        }
        #datepick-div .datepick-one-month {
            height: auto;
        }
    </style>
    <?php
}            


/** Sortable Table JavaScript */
function wpbm_sortable_js() {
    ?>
    <script type="text/javascript">        
        // Activate Sortable Functionality    
        jQuery( document ).ready(function(){

            jQuery('.wpbm_input_table tbody th').css('cursor','move');

            jQuery('.wpbm_input_table tbody td.sort').css('cursor','move');

            jQuery('.wpbm_input_table.sortable tbody').sortable({
                    items:'tr',
                    cursor:'move',
                    axis:'y',
                    scrollSensitivity:40,
                    forcePlaceholderSize: true,
                    helper: 'clone',
                    opacity: 0.65,
                    placeholder: '.wpbm_sortable_table .sort',
                    start:function(event,ui){
                            ui.item.css('background-color','#f6f6f6');
                    },
                    stop:function(event,ui){
                            ui.item.removeAttr('style');
                    }
            });
        });
    </script>
    <?php
    
}
//                                                                              </editor-fold>


//                                                                              <editor-fold   defaultstate="collapsed"   desc=" R e l o a d    p a g e " >    
////////////////////////////////////////////////////////////////////////////////
// R e l o a d    p a g e
////////////////////////////////////////////////////////////////////////////////
/**
 * Reload page by using JavaScript
 * 
 * @param string $url - URL of page to  load
 */
function wpbm_reload_page_by_js( $url ) {

	$redir = html_entity_decode( esc_url( $url ) );

	if ( ! empty( $redir ) ) {
		?>
		<script type="text/javascript">                
			window.location.href = '<?php echo esc_url($redir); ?>';
		</script>
		<?php
	}
}


/** Redirect browser to a specific page
 * 
 * @param string $url - URL of page to redirect
 */
function wpbm_redirect( $url ) {

	$url = wpbm_make_link_absolute( $url );

	$url = html_entity_decode( esc_url( $url ) );

	echo '<script type="text/javascript">';
	echo 'window.location.href="'.esc_url($url).'";';
	echo '</script>';
	echo '<noscript>';
	echo '<meta http-equiv="refresh" content="0;url='.esc_url($url).'" />';
	echo '</noscript>';
}
//                                                                              </editor-fold>


//                                                                              <editor-fold   defaultstate="collapsed"   desc=" P a g i n a t i o n    o f    T a b l e    L  i s t i n g " >    
/** Show    P a g i n a t i o n
 * 
 * @param int $summ_number_of_items     - total  number of items
 * @param int $active_page_num          - number of activated page
 * @param int $num_items_per_page       - number of items per page
 * @param array $only_these_parameters  - array of keys to exclude from links
 * @param string $url_sufix             - usefule for anchor to  HTML section  with  specific ID,  Example: '#my_section'
 */
function wpbm_show_pagination( $summ_number_of_items, $active_page_num, $num_items_per_page , $only_these_parameters = false, $url_sufix = '' ) {

	if ( empty( $num_items_per_page ) ) {
		$num_items_per_page = '10';
	}

	$pages_number = ceil( $summ_number_of_items / $num_items_per_page );
	if ( $pages_number < 2 )
		return;

			//Fix: 5.1.4 - Just in case we are having tooo much  resources, then we need to show all resources - and its empty string
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash,  	WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			if ( ( isset($_REQUEST['wh_wpbm_type'] ) ) && ( strlen($_REQUEST['wh_wpbm_type']) > 1000 ) ) { $_REQUEST['wh_wpbm_type'] = ''; }

	// First  parameter  will overwriten by $_GET['page'] parameter
	$bk_admin_url = wpbm_get_params_in_url( wpbm_get_master_url( false ), array('page_num'), $only_these_parameters );


	?>
	<span class="wpdevelop wpbm-pagination">
		<div class="container-fluid">  
			<div class="row">
				<div class="col-sm-12 text-center control-group0">
					<nav class="btn-toolbar">
					  <div class="btn-group wpbm-no-margin" style="float:none;">

						<?php if ( $pages_number > 1 ) { ?>
								<a class="button button-secondary <?php echo ( $active_page_num == 1 ) ? ' disabled' : ''; ?>" 
								   href="<?php echo esc_url($bk_admin_url); ?>&page_num=<?php if ($active_page_num == 1) { echo esc_attr( $active_page_num ); } else { echo esc_attr($active_page_num-1); } echo esc_attr( $url_sufix ); ?>">
									<?php esc_html_e('Prev', 'booking-manager'); ?>
								</a>
						<?php } 

						/** Number visible pages (links) that linked to active page, other pages skipped by "..." */
						$num_closed_steps = 3;

						for ( $pg_num = 1; $pg_num <= $pages_number; $pg_num++ ) {

								if ( ! ( 
										   ( $pages_number > ( $num_closed_steps * 4) ) 
										&& ( $pg_num > $num_closed_steps ) 
										&& ( ( $pages_number - $pg_num + 1 ) > $num_closed_steps ) 
										&& (  abs( $active_page_num - $pg_num ) > $num_closed_steps )  
								   ) ) {
									?> <a class="button button-secondary <?php if ($pg_num == $active_page_num ) echo ' active'; ?>" 
										 href="<?php echo esc_attr( $bk_admin_url ); ?>&page_num=<?php echo esc_attr( $pg_num);  echo esc_attr( $url_sufix); ?>">
										<?php echo esc_html($pg_num); ?>
									  </a><?php 

									if ( ( $pages_number > ( $num_closed_steps * 4) ) 
											&& ( ($pg_num+1) > $num_closed_steps ) 
											&& ( ( $pages_number - ( $pg_num + 1 ) ) > $num_closed_steps ) 
											&&  ( abs($active_page_num - ( $pg_num + 1 ) ) > $num_closed_steps )  
										) {
										echo ' <a class="button button-secondary disabled" href="javascript:void(0);">...</a> ';
									}
								}
						}

						if ( $pages_number > 1 ) { ?>
								<a class="button button-secondary <?php echo ( $active_page_num == $pages_number ) ? ' disabled' : ''; ?>" 
								   href="<?php echo esc_attr( $bk_admin_url ); ?>&page_num=<?php  if ($active_page_num == $pages_number) { echo esc_attr( $active_page_num); } else { echo esc_attr($active_page_num+1); }  echo esc_attr( $url_sufix); ?>">
									<?php esc_html_e('Next', 'booking-manager'); ?>
								</a>
						<?php } ?>

					  </div>
					</nav>
				</div>
			</div>
		</div>
	</span>
	<?php
}
//                                                                              </editor-fold>


//                                                                              <editor-fold   defaultstate="collapsed"   desc=" D a t e s " >    
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Dates Format
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


/** Get Formated Date & time
 * 
 * @param string	$date_sql			- 2017-07-31 00:00:00  ||  2017-07-31
 * @param string	$date_format		- Optional.		-	"m / d / Y, D   H:i:s"
 * @param string	$seperator			- Optional.		-	" "
 * @return string	-	July 29, 2014 12:00 am
 */
function wpbm_get_date_time_formatted( $date_sql,  $date_format = false, $seperator = ' ', $skip_midnight_time = false  ) {
	
	$return_date = wpbm_get_date_formatted( $date_sql, $date_format );
	
	$return_time = wpbm_get_time_formatted( $date_sql,  $date_format, $skip_midnight_time ); 
	if ( ! empty( $return_time ) )
		$return_date .= $seperator . $return_time;
	
	return $return_date;
}


/** Get Formated Date
 * 
 * @param string	$date_sql			- 2017-07-31 00:00:00  ||  2017-07-31
 * @param string	$date_format		- Optional.		-	"m / d / Y, D"
 * @param bool	$skip_midnight_time		- Default false	- if 00:00:00 then return '';
 * @return string	-	July 29, 2014
 */
function wpbm_get_date_formatted( $date_sql, $date_format = false ) {

    if ( $date_format === false )   $date_format = get_wpbm_option( 'wpbm_date_format' );
    if ( empty( $date_format ) )    $date_format = "m / d / Y, D";
    
	$formated_date = date_i18n( $date_format, strtotime( $date_sql ) );
	
	return $formated_date;
}


/** Get Formated Date & time
 * 
 * @param string	$date_sql			- 2017-07-31 00:00:00  ||  2017-07-31
 * @param string	$time_format		- Optional.		-	"H:i:s"
 * @return string	-	12:00 am
 */
function wpbm_get_time_formatted( $date_sql,  $time_format = false , $skip_midnight_time = false ) {

	if ( ( $skip_midnight_time ) && ( '00:00:00' == substr( $date_sql, -8 ) ) )
		return '';
	
	if ( $time_format === false )   $time_format = get_wpbm_option( 'wpbm_time_format' );
    if ( empty( $time_format ) )    $time_format = 'h:i a';
    
	$formated_date = date_i18n( $time_format, strtotime( $date_sql ) );
	
	return $formated_date;    
}


/** Check if "current_day" is tomorrow from "next_day"
 * 
 * @param string $current_day_sql_check        : 2015-02-29 00:00:00
 * @param string $next_day_sql_check		   : 2015-02-30 00:00:00
 * @return boolean              : true | false
 */
function wpbm_is_next_day( $current_day_sql_check, $next_day_sql_check  ) {

	// Current day
	$current_day_unix = strtotime( $current_day_sql_check );
	
	$current_day_midnight_sql  = date_i18n( 'Y-m-d', $current_day_unix );
	$current_day_midnight_unix = strtotime( $current_day_midnight_sql );
	
	$calc_next_day_unix = strtotime( '+1 day',  $current_day_midnight_unix );
	
	// Next day
	$next_day_unix = strtotime( $next_day_sql_check );	
	$next_day_midnight_sql  = date_i18n( 'Y-m-d', $next_day_unix );
	$next_day_midnight_unix = strtotime( $next_day_midnight_sql );
	
	
	if ( $calc_next_day_unix ==  $next_day_midnight_unix ) 
		return true; 
    else                           
		return false;		
}

/** Check if "current_day" is same day  of "other_day"
 * 
 * @param string $current_day_sql_check        : 2015-02-29 00:00:00
 * @param string $other_day_sql_check		   : 2015-02-30 00:00:00
 * @return boolean              : true | false
 */
function wpbm_is_this_same_day( $current_day_sql_check, $other_day_sql_check  ) {

	// Current day
	$current_day_unix = strtotime( $current_day_sql_check );
	
	$current_day_midnight_sql  = date_i18n( 'Y-m-d', $current_day_unix );
	$current_day_midnight_unix = strtotime( $current_day_midnight_sql );
	
	// Other day
	$other_day_unix = strtotime( $other_day_sql_check );	
	$other_day_midnight_sql  = date_i18n( 'Y-m-d', $other_day_unix );
	$other_day_midnight_unix = strtotime( $other_day_midnight_sql );
	
	
	if ( $current_day_midnight_unix ==  $other_day_midnight_unix ) 
		return true; 
    else                           
		return false;		
}


/** Get days in short format view
 * 
 * @param string $days        Dates: 15.05.2015, 16.05.2015, 17.05.2015
 * @return string           Dates in format: 15.05.2015 - 17.05.2015
 */
function wpbm_get_dates_short_format( $dates_sql_csv ) {                                 // $days - string with comma seperated dates

    if ( empty( $dates_sql_csv ) )
		return '';

	$days = explode( ',', $dates_sql_csv );

	$previosday = false;
	$result_string = '';
	$last_show_day = '';

	foreach ( $days as $day ) {
		
		$is_fin_at_end = false;
		
		if ( $previosday === false ) {					// First Day
			
			$result_string = wpbm_get_date_time_formatted( $day, false, ' ', true );					// echo format for first day
			$last_show_day = $day;
			$previosday    = $day;													// Set previos day for next loop
			
		} else  {										// Not first day
			
			if ( 
				   wpbm_is_next_day( $previosday, $day ) 
				|| wpbm_is_this_same_day( $previosday, $day   ) 
			) {											// Check  if $day next  day from previous
				
				$previosday = $day;													// Set previos day for next loop
				$is_fin_at_end = true;
			} else {
				if ( $last_show_day !== $previosday ) {								// check if previos day was show or no
					$result_string .= ' - ' . wpbm_get_date_time_formatted( $previosday, false, ' ', true  );	// assign in needed format this day
				}
				$result_string .= ', ' . wpbm_get_date_time_formatted( $day, false, ' ', true  );			// assign in needed format this day
				$previosday    = $day;													// Set previos day for next loop
				$last_show_day = $day;
			}
		} 
		
	}

	if ( $is_fin_at_end ) {
		$result_string .= ' - ' . wpbm_get_date_time_formatted( $day, false, ' ', true  );
	}

	return $result_string;
}

//                                                                              </editor-fold>