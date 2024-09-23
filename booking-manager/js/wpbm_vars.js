/**
 * @version 1.0
 * @package Booking Manager 
 * @subpackage JS Variables
 * @category Scripts
 * 
 * @author wpdevelop
 * @link https://oplugins.com/
 * @email info@oplugins.com
 *
 * @modified 2014.05.20
 */

////////////////////////////////////////////////////////////////////////////////
// Eval specific variable value (integer, bool, arrays, etc...)
////////////////////////////////////////////////////////////////////////////////

function wpbm_define_var( wpbm_global_var ) {
    if (wpbm_global_var === undefined) { return null; }
    else { return JSON.parse(wpbm_global_var); }                          //FixIn:6.1   //FixIn: 2.0.18.4
}

////////////////////////////////////////////////////////////////////////////////
// Define global Booking Manager Varibales based on Localization
////////////////////////////////////////////////////////////////////////////////
var wpbm_ajaxurl                       = wpbm_global1.wpbm_ajaxurl; 
var wpbm_plugin_url                    = wpbm_global1.wpbm_plugin_url;
var wpbm_today                         = wpbm_define_var( wpbm_global1.wpbm_today );
var wpbm_plugin_filename               = wpbm_global1.wpbm_plugin_filename;
var message_verif_requred               = wpbm_global1.message_verif_requred;
var message_verif_requred_for_check_box = wpbm_global1.message_verif_requred_for_check_box;
var message_verif_requred_for_radio_box = wpbm_global1.message_verif_requred_for_radio_box;
var message_verif_emeil                 = wpbm_global1.message_verif_emeil;
var message_verif_same_emeil            = wpbm_global1.message_verif_same_emeil;
var wpbm_active_locale                  = wpbm_global1.wpbm_active_locale;
var wpbm_message_processing             = wpbm_global1.wpbm_message_processing;
var wpbm_message_deleting               = wpbm_global1.wpbm_message_deleting;
var wpbm_message_updating               = wpbm_global1.wpbm_message_updating;
var wpbm_message_saving                 = wpbm_global1.wpbm_message_saving;