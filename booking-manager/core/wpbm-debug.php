<?php
/**
 * @version 1.1
 * @package Any
 * @category Dubug info showing
 * @author wpdevelop
 *
 * @web-site https://oplugins.com/
 * @email info@oplugins.com 
 * 
 * @modified 09.09.2015
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


    
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//  D e b u g    f u n c t i o n s       ///////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/** Show values of the arguments list */
if ( ! function_exists( 'debuge' ) ) {
function debuge() {
        $numargs = func_num_args();
        $var = func_get_args();
        $makeexit = is_bool( $var[count($var)-1] ) ? $var[ count($var) - 1 ]:false;
        echo "<div><pre class='prettyprint linenums'>";
        //print_r($var);
	    foreach ( $var as $ind => $item ) {

		    echo "\n<strong> [$ind] " . gettype( $item ) . "</strong>\n";

		    if ( 'string' === gettype( $item ) ) {
			    echo( htmlentities( $item ) );
		    } else {
			    print_r( $item );
		    }
	    }
        echo "</pre></div>";
        echo '<script type="text/javascript"> jQuery(".ajax_respond_insert, .ajax_respond").show(); </script>';
        if ( $makeexit ) {
            echo '<div style="font-size:18px;float:right;">' . get_num_queries(). '/'  . timer_stop(0, 3) . 'qps</div>';
            exit;
        }
    }
}

/** Show Speed of the execution and number of queries. */
if ( ! function_exists( 'debuge_speed' ) ) {
function debuge_speed() {
        echo '<div style="font-size:18px;float:right;">' . get_num_queries(). '/'  . timer_stop(0, 3) . 'qps</div>';
    }
}

/** Show error info
 */
if ( ! function_exists( 'debuge_error' ) ) {
function debuge_error( $msg , $file_name='', $line_num=''){
        echo get_debuge_error( $msg , $file_name , $line_num );
    }
}

if ( ! function_exists( 'get_debuge_error' ) ) {
function get_debuge_error( $msg , $file_name='', $line_num=''){

        $ver_num = ( ! defined('WPBM_VERSION') ) ? '' : '|V:' . WPBM_VERSION ;
        
        $last_db_error = '';
        global $EZSQL_ERROR;

		//FixIn: 2.0.31.1
		if (
			( ! empty( $EZSQL_ERROR ) )
			&& ( is_array( $EZSQL_ERROR ) )
			&& ( isset( $EZSQL_ERROR[ ( count( $EZSQL_ERROR ) - 1 ) ] ) )
		){

            $last_db_error2 = $EZSQL_ERROR[ (count($EZSQL_ERROR)-1)];

            if  ( (isset($last_db_error2['query'])) && (isset($last_db_error2['error_str'])) ) {

                $str   = str_replace( array( '"', "'" ), '', $last_db_error2['error_str'] );     
                $query = str_replace( array( '"', "'" ), '', $last_db_error2['query'] );     

                $str   = htmlspecialchars( $str,    ENT_QUOTES );
                $query = htmlspecialchars( $query , ENT_QUOTES );

                $last_db_error =  $str ;                
                $last_db_error .= '::<span style="color:#300;">'.$query.'</span>';
            }
        }        
        return $msg . '<br /><span style="font-size:11px;"> ['
                                                        .   'F:' . str_replace( dirname( $file_name ) , '' , $file_name )
                                                        . '| L:' . $line_num  
                                                        . $ver_num  
                                                        . '| DB:' . $last_db_error  
                            . '] </span>' ;
    }
}

/** Usage: if (  function_exists ('wpbm_check_post_key_max_number')) { wpbm_check_post_key_max_number(); } */
if ( ! function_exists( 'wpbm_check_post_key_max_number' ) ) {
function wpbm_check_post_key_max_number() {
        
        /*
        $post_max_totalname_length    = intval( ( ini_get( 'suhosin.post.max_totalname_length' ) ) ? ini_get( 'suhosin.post.max_totalname_length' ) : '9999999' );
        $request_max_totalname_length = intval( ( ini_get( 'suhosin.request.max_totalname_length' ) ) ? ini_get( 'suhosin.request.max_totalname_length' ) : '9999999' );
        $post_max_name_length         = intval( ( ini_get( 'suhosin.post.max_name_length' ) ) ? ini_get( 'suhosin.post.max_name_length' ) : '9999999' );
        $request_max_varname_length   = intval( ( ini_get( 'suhosin.request.max_varname_length' ) ) ? ini_get( 'suhosin.request.max_varname_length' ) : '9999999' );
        */
        $php_ini_vars = array( 
                                  'suhosin.post.max_totalname_length'
                                , 'suhosin.request.max_totalname_length'
                                , 'suhosin.post.max_name_length'
                                , 'suhosin.request.max_varname_length' 
                            );
        
        foreach ( $_POST as $key_name => $post_value ) {
            
            $key_length = strlen( $key_name );
            
            foreach ( $php_ini_vars as $php_ini_var ) {
                
                $php_ini_var_length    = intval( ( ini_get( $php_ini_var ) ) ? ini_get( $php_ini_var ) : '9999999' );
                
                if (  $key_length > $php_ini_var_length ) {
                    
                    wpbm_show_message_in_settings(  'Your php.ini configuration limited to ' 
                                                    . '<strong> '. $php_ini_var . ' = ' . $php_ini_var_length . '</strong>.' 
                                                    . ' '
                                                    . 'Plugin require at least ' . ( intval( $key_length ) + 1 ). ', '
                                                    . 'for saving option: ' . '<strong>'. $key_name    . '</strong>'
                                                  , 'error'
                                                  , __('Error' , 'booking-manager') . '.' );
                    
                }                
            }
        }        
    }
}


/** Write debuge log to file ../wp-content/upload/wpbm_debug.log
 * 
 * @param type $param
 */
if ( ! function_exists( 'debuge_log' ) ) {
	function debuge_log( $param ) {
	
	
		$content =    "\n---\n" 
					. 'Log: [' . date_i18n( 'Y-m-d H:m:s' ) . ']'
					. "\n---\n"
					. str_replace( ',', "\n,", json_encode( $param ) );


		// Install files and folders for uploading files and prevent hotlinking
		$upload_dir = wp_upload_dir();
	
		$files = array(
			array(
				'base'    => $upload_dir['basedir'],
				'file'    => 'wpbm_debug.log',
				'content' => $content

			)
		);

		foreach ( $files as $file ) {

			if (   ( wp_mkdir_p( $file['base'] ) )												// Recursive directory creation based on full path.
				//&& ( ! file_exists( trailingslashit( $file['base'] ) . $file['file'] ) )		// If file not exist			
			) {

				// Append new lines to bottom (if we need to rewrite,  then  use 'w'
				if ( $file_handle = @fopen( trailingslashit( $file['base'] ) . $file['file'], 'a' ) ) {

					fwrite( $file_handle, $file['content'] );
					fclose( $file_handle );
				}
			}
		}
	
	}
}



/** Show System debug log for Beta features.
 * 
 * @param mixed $show_debug_info
 * 
 * Example of usage:
 * 
   		$is_show_debug_info = (  ( get_bk_option( 'booking_is_show_system_debug_log' ) == 'On' ) ? true : false );
		if ( $is_show_debug_info )
			add_action( 'wpbm_show_debug', 'wpbm_start_showing_debug', 10, 1 );			
		...
        //-
	    do_action( 'wpbm_show_debug', array( 'after import' , $ics_array) );
        ...
		if ( $is_show_debug_info )		
			remove_action( 'wpbm_show_debug', 'wpbm_start_showing_debug', 10 );
 */
function wpbm_start_showing_debug( $show_debug_info ) {											//FixIn: 7.2.1.15
		
	// Make first  item  as header - bold
	if ( is_array(  $show_debug_info ) )
		$show_debug_info[0] = '<strong>' . $show_debug_info[0] . '</strong>';
	
	// Get evrything in human redable view
	$show_debug_info = print_r ( $show_debug_info, true );

	// Remove unnecesary new lines
	$show_debug_info = preg_replace( '/Array[\n\r\s]*\(/', 'array(', $show_debug_info );
	$show_debug_info = preg_replace( '/\)\n/', ")", $show_debug_info );
	
	// Show
	echo "<div><pre class='prettyprint linenums'>";
	
	echo( $show_debug_info );
	
	echo "</pre></div>";
	
	echo '<hr/>';
	
	// For system  debug log in Ajax request  show it
	?><script type="text/javascript"> jQuery( '.wpbm_system_info_log' ).show(); </script><?php
}
	 
	 
/** Show Top Notice in Admin panel  by writing in-line JS
 * 
 * @param string $message
 * @param string $message_type		- (default notice) notice | error | warning | info | success
 * @param int $seconds_to_show		- (default 3000) - delay in microseconds
 * @param bool $is_append			- (default true) - append notice instead of replacing
 */
function wpbm_admin_show_top_notice( $message, $message_type = 'info', $seconds_to_show = 3000, $is_append = true ) {

	if ( ! is_admin() ) return;
	
	// Show Notice in Admin header
	?><script type="text/javascript">
		wpbm_admin_show_message( '<?php echo html_entity_decode( esc_js(  $message ), ENT_QUOTES ); ?>'
								, '<?php echo $message_type; ?>'
								, <?php echo intval( $seconds_to_show ); ?> 
								, <?php echo ( $is_append ? 'true' : 'false' ); ?> 
							);				
	</script><?php	
}
add_action( 'wpbm_admin_show_top_notice', 'wpbm_admin_show_top_notice', 10, 3 );

