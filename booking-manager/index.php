<?php
/*
Plugin Name: Booking Manager
Plugin URI: https://oplugins.com/plugins/booking-manager
Description: Showing events listing from .ics feeds or sync bookings from different sources to your website
Author: wpdevelop, oplugins
Author URI: https://oplugins.com/
Text Domain: booking-manager
Domain Path: /languages/
Version: 2.1.15
*/

/*  Copyright 2017-2025  www.oplugins.com  (email: info@oplugins.com),

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>
*/
    
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) die('<h3>Direct access to this file do not allow!</h3>');

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// PRIMARY URL CONSTANTS                        //////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////    

if ( ! defined( 'WPBM_VERSION_NUM' ) )		define( 'WPBM_VERSION_NUM',		'2.1.15' );

// ..\home\siteurl\www\wp-content\plugins\plugin-name\wpbm-item.php
if ( ! defined( 'WPBM_FILE' ) )             define( 'WPBM_FILE', __FILE__ ); 

// wpbm-item.php
if ( ! defined('WPBM_PLUGIN_FILENAME' ) )   define('WPBM_PLUGIN_FILENAME', basename( __FILE__ ) );                     

// plugin-name    
if ( ! defined('WPBM_PLUGIN_DIRNAME' ) )    define('WPBM_PLUGIN_DIRNAME',  plugin_basename( dirname( __FILE__ ) )  );  

// ..\home\siteurl\www\wp-content\plugins\plugin-name
if ( ! defined('WPBM_PLUGIN_DIR' ) )        define('WPBM_PLUGIN_DIR', untrailingslashit( plugin_dir_path( WPBM_FILE ) )  );

// http: //website.com/wp-content/plugins/plugin-name
if ( ! defined('WPBM_PLUGIN_URL' ) )        define('WPBM_PLUGIN_URL', untrailingslashit( plugins_url( '', WPBM_FILE ) )  );     

require_once WPBM_PLUGIN_DIR . '/core/wpbm.php'; 

/** 
 * 1) Rename all  files in plugin directory starting from wpbm -> prefix
 * 
 * 2) Replace Instruction:
 * 
, 'booking-manager') ->  , 'pluginnamelocale')
  _wpbm_     ->  _bk_ (...)       in get_wpbm_option ....
   WPBM      ->  PREFIX
   wpbm      ->  prefix
   bookingmanager -> booking ???   
   Booking Manager -> NEW_PLUGIN_NAME
 * 
 */
