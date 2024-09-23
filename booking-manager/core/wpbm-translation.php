<?php  /**
 * @version 1.0
 * @package Booking Manager 
 * @subpackage Translations Functions
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
//   Transaltions   ////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

// New in WP 6.2 - firstly loaded transaltion  from  wp-content/languges and then chek  only 
// use the override_load_textdomain filter to load your text domains manually.
// apply_filters( 'override_load_textdomain', bool $override, string $domain, string $mofile )

/** Check text  for active language section
 * 
 * @param string $content_orig
 * @return string
 * Usage:
 * $text = apply_wpbm_filter('wpbm_check_for_active_language',  $text );
 */    
function wpbm_check_for_active_language($content_orig){

    $content = $content_orig;

    $languages = array();
    $content_ex = explode('[lang',$content);

    foreach ($content_ex as $value) {

        if (substr($value,0,1) == '=') {

            $pos_s = strpos($value,'=');
            $pos_f = strpos($value,']');
            $key = trim( substr($value, ($pos_s+1), ($pos_f-$pos_s-1) ) );
            $value_l = trim( substr($value,  $pos_f+1  ) );
            $languages[$key] = $value_l;

        } else  
            $languages['default'] = $value;
    }

    $locale = wpbm_get_locale();
    // $locale = 'fr_FR';

    if ( isset( $languages[$locale] ) ) $return_text = $languages[ $locale ];
    else                                $return_text = $languages[ 'default' ];

    $return_text = wpbm_check_qtranslate( $return_text, $locale );
    
    $return_text = wpbm_check_wpml_tags( $return_text, $locale );               //FixIn: 5.4.5.8
    
    return $return_text;
}


/**  Register and Translate everything in [wpml]Some Text to translate[/wpml] tags.
 * 
 * @param string $text
 * @param string $locale
 * @return string
 */
function wpbm_check_wpml_tags( $text, $locale='' ) {                            //FixIn: 5.4.5.8

    if ( $locale == '' ) {
        $locale = wpbm_get_locale();
    }
    if ( strlen( $locale ) > 2 ) {
        $locale = substr($locale, 0, 2 );
    }

    $is_tranlsation_exist_s = strpos( $text, '[wpml]' );
    $is_tranlsation_exist_f = strpos( $text, '[/wpml]' );

    if ( ( $is_tranlsation_exist_s !== false )  &&  ( $is_tranlsation_exist_f !== false ) )  {

        $shortcode = 'wpml';

        // Find anything between [wpml] and [/wpml] shortcodes. Magic here: [\s\S]*? - fit to any text
        preg_match_all( '/\[' . $shortcode . '\]([\s\S]*?)\[\/' . $shortcode . '\]/i', $text, $wpml_translations, PREG_SET_ORDER );               
//debuge( $wpml_translations );

        foreach ( $wpml_translations as $translation ) {                
            $text_to_replace      = $translation[0];
            $translation_to_check = $translation[1];

            if ( function_exists ( 'icl_register_string' ) ){

                if ( false ) {   // Depricated functions

                    // Help: https://wpml.org/documentation/support/translation-for-texts-by-other-plugins-and-themes/  
                    icl_register_string('Item Calendar', 'wpbm-' . tag_escape( $translation_to_check ) , $translation_to_check );

                    //TODO: Need to  execurte this after deactivation  of plugin  or after updating of some option...
                    //icl_unregister_string ( 'Item Calendar', 'wpbm-' . tag_escape( $translation_to_check ) );                      

                    if ( function_exists ( 'icl_translate' ) ){
                        $translation_to_check = icl_translate ( 'Item Calendar', 'wpbm-' . tag_escape( $translation_to_check ) , $translation_to_check  );
                    }

                } else { // WPML Version: 3.2

                    // Help info:  do_action( 'wpml_register_single_string', string $context, string $name, string $value )
                    // https://wpml.org/wpml-hook/wpml_register_string_for_translation/
                    do_action( 'wpml_register_single_string', 'Item Calendar', 'wpbm-' . tag_escape( $translation_to_check ) , $translation_to_check );


                    // Help info:  apply_filters( 'wpml_translate_single_string', string $original_value, string $context, string $name, string $$language_code )
                    // https://wpml.org/wpml-hook/wpml_translate_single_string/
                    //$translation_to_check = apply_filters( 'wpml_translate_single_string', $translation_to_check, 'Item Calendar',  'wpbm-' . tag_escape( $translation_to_check ) );
                    $language_code = $locale;
                    $translation_to_check = apply_filters( 'wpml_translate_single_string', $translation_to_check, 'Item Calendar',  'wpbm-' . tag_escape( $translation_to_check ), $language_code );

                }
            }                
            $text = str_replace( $text_to_replace, $translation_to_check, $text );
        }            
    }  

    return $text;
}

    
function wpbm_check_qtranslate( $text, $locale='' ){
    
    if ($locale == '') {
        $locale = wpbm_get_locale();
    }
    if (strlen($locale)>2) {
        $locale = substr($locale, 0 ,2);
    }

    $is_tranlsation_exist = strpos($text, '<!--:'.$locale.'-->');

    if ($is_tranlsation_exist !== false) {
        $tranlsation_end = strpos($text, '<!--:-->', $is_tranlsation_exist);

        $text = substr($text, $is_tranlsation_exist , ($tranlsation_end - $is_tranlsation_exist ) );
    }

    return $text;
}


function wpbm_load_translation(){
    
    //$locale = 'fr_FR'; wpbm_load_locale( $locale ); 
    
    if ( ! wpbm_load_locale() ) { 
        wpbm_load_locale('en_US');         
    }
    
    $locale = wpbm_get_locale();
}
add_action('plugins_loaded', 'wpbm_load_translation',1000);

/**  Overload loading of plugin transaltion  files from    "wp-content/plugins/languages" -> "wp-content/plugins/plugin_name/languages"
 * 
 * W:\home\beta\www/wp-content/languages/plugins/booking-manager-it_IT.mo   ->   W:\home\beta\www\wp-content\plugins\booking-manager/languages/booking-manager-it_IT.mo
 * 
 * @param string $mofile
 * @param type $domain
 * @return string
 */
function wpbm_load_custom_plugin_translation_file( $mofile, $domain ) {

    if ( $domain == 'booking-manager' ) {
        
        $mofile =  WPBM_PLUGIN_DIR . '/languages/' . basename( $mofile );         
    }

    return $mofile;
}
add_filter( 'load_textdomain_mofile', 'wpbm_load_custom_plugin_translation_file' , 10, 2 );


function wpbm_load_locale( $locale = '' ) { 
    
    if ( empty( $locale ) ) 
        $locale = wpbm_get_locale();
    
    if ( ! empty( $locale ) ) {

        $domain = 'booking-manager'; 
        $mofile = WPBM_PLUGIN_DIR  . '/languages/' . $domain . '-' . $locale . '.mo';
        
        if ( file_exists( $mofile ) ) {
                                                                            
            $plugin_rel_path = WPBM_PLUGIN_DIRNAME . '/languages'  ;
            return load_plugin_textdomain( $domain, false, $plugin_rel_path ) ;
        } else {
			unload_textdomain( $domain );
		}
    } 
    
    return false;
}


function wpbm_get_locale() {
    
    if ( defined( 'WPBM_LOCALE_RELOAD' ) ) 
        return WPBM_LOCALE_RELOAD;
    
	if( function_exists( 'get_user_locale' ) )
		$locale = is_admin() ? get_user_locale() : get_locale();
	else
		$locale = get_locale();

	define( 'WPBM_LOCALE_RELOAD', $locale );
    
    return $locale;
	
}


function wpbm_recheck_plugin_locale( $locale, $plugin_domain = '' ) {       //FixIn: 2.0.9.1

    if ( $plugin_domain == 'booking-manager' )                              //FixIn: 2.0.9.1
        if ( defined('WPBM_RELOAD') )
            return WPBM_RELOAD;

    return $locale;
}
add_filter( 'plugin_locale', 'wpbm_recheck_plugin_locale', 100, 2 );            // When load_plugin_text_domain is work, its get def locale and not that, we send to it so need to reupdate it