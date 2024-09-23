<?php 
/**
 * @version 1.0
 * @package Booking Manager 
 * @subpackage Core Functions
 * @category Functions
 * 
 * @author wpdevelop
 * @link https://oplugins.com/
 * @email info@oplugins.com
 *
 * @modified 29.09.2015
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly

////////////////////////////////////////////////////////////////////////////////
//  Internal plugin action hooks system      ///////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
global $wpbm_action, $wpbm_filter;


function add_wpbm_filter($filter_type, $filter) {
    global $wpbm_filter;

    $args = array();
    if ( is_array($filter) && 1 == count($filter) && is_object($filter[0]) ) // array(&$this)
        $args[] =& $filter[0];
    else
        $args[] = $filter;
    for ( $a = 2; $a < func_num_args(); $a++ )
        $args[] = func_get_arg($a);

    if ( is_array($wpbm_filter) )

        if ( isset($wpbm_filter[$filter_type]) ) {
            if ( is_array($wpbm_filter[$filter_type]) )
                $wpbm_filter[$filter_type][]= $args;
            else
                $wpbm_filter[$filter_type]= array($args);
        } else
            $wpbm_filter[$filter_type]= array($args);
    else
        $wpbm_filter = array( $filter_type => array( $args ) ) ;
}

function remove_wpbm_filter($filter_type, $filter) {
    global $wpbm_filter;

    if ( isset($wpbm_filter[$filter_type]) ) {
        for ($i = 0; $i < count($wpbm_filter[$filter_type]); $i++) {
            if ( $wpbm_filter[$filter_type][$i][0] == $filter ) {
                $wpbm_filter[$filter_type][$i] = null;
                return;
            }
        }
    }
}

function apply_wpbm_filter($filter_type) {
    global $wpbm_filter;


    $args = array();
    for ( $a = 1; $a < func_num_args(); $a++ )
        $args[] = func_get_arg($a);

    if ( count($args) > 0 )
        $value = $args[0];
    else
        $value = false;

    if ( is_array($wpbm_filter) )
        if ( isset($wpbm_filter[$filter_type]) )
            foreach ($wpbm_filter[$filter_type] as $filter) {
                $filter_func = array_shift($filter);
                $parameter = $args;
                $value =  call_user_func_array($filter_func,$parameter );
            }
    return $value;
}


function make_wpbm_action($action_type) {
    global $wpbm_action;


    $args = array();
    for ( $a = 1; $a < func_num_args(); $a++ )
        $args[] = func_get_arg($a);

    if ( is_array($wpbm_action) )
        if ( isset($wpbm_action[$action_type]) )
            foreach ($wpbm_action[$action_type] as $action) {
                $action_func = array_shift($action);
                $parameter = $action;
                call_user_func_array($action_func,$args );
            }
}

function add_wpbm_action($action_type, $action) {
    global $wpbm_action;

    $args = array();
    if ( is_array($action) && 1 == count($action) && is_object($action[0]) ) // array(&$this)
        $args[] =& $action[0];
    else
        $args[] = $action;
    for ( $a = 2; $a < func_num_args(); $a++ )
        $args[] = func_get_arg($a);

    if ( is_array($wpbm_action) )
        if ( isset($wpbm_action[$action_type]) ) {
            if ( is_array($wpbm_action[$action_type]) )
                $wpbm_action[$action_type][]= $args;
            else
                $wpbm_action[$action_type]= array($args);
        } else
                $wpbm_action[$action_type]= array($args);

    else
        $wpbm_action = array( $action_type => array( $args ) ) ;
}

function remove_wpbm_action($action_type, $action) {
    global $wpbm_action;

    if ( isset($wpbm_action[$action_type]) ) {
        for ($i = 0; $i < count($wpbm_action[$action_type]); $i++) {
            if ( $wpbm_action[$action_type][$i][0] == $action ) {
                $wpbm_action[$action_type][$i] = null;
                return;
            }
        }
    }
}


function get_wpbm_option( $option, $default = false ) {

    $u_value = apply_wpbm_filter('wpbm_get_option', 'no-values'  , $option, $default );
    if ( $u_value !== 'no-values' ) return $u_value;

    return get_option( $option, $default  );
}

function update_wpbm_option ( $option, $newvalue ) {

    $u_value = apply_wpbm_filter('wpbm_update_option', 'no-values'  , $option, $newvalue );
    if ( $u_value !== 'no-values' ) return $u_value;

    return update_option($option, $newvalue);
}

function delete_wpbm_option ( $option ) {

    $u_value = apply_wpbm_filter('wpbm_delete_option', 'no-values'  , $option );
    if ( $u_value !== 'no-values' ) return $u_value;

    return delete_option($option );
}

function add_wpbm_option( $option, $value = '', $deprecated = '', $autoload = 'yes' ) {

    $u_value = apply_wpbm_filter('wpbm_add_option', 'no-values'  , $option, $value, $deprecated,  $autoload );
    if ( $u_value !== 'no-values' ) return $u_value;

    return add_option( $option, $value  , $deprecated  , $autoload   );
}
