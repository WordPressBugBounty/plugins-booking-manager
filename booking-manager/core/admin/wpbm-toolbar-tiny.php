<?php
/**
 * @version 1.0
 * @package Booking Manager 
 * @subpackage Files Loading
 * @category Bookings
 * 
 * @author wpdevelop
 * @link https://oplugins.com/plugins/booking-manager/
 * @email info@oplugins.com
 *
 * @modified 2017-08-01
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


class WPBM_TinyMCE_Buttons {
	
	
    //                                                                              <editor-fold   defaultstate="collapsed"   desc=" I n i t    +    H o o k s" >    
    
    private $settings = array();
    
    function __construct( $params ) {
        
        $this->settings = array(
							  'tiny_prefix'         => 'wpbm_tiny'
							, 'tiny_icon_url'       => WPBM_PLUGIN_URL . '/assets/img/icon-16x16.png'
							, 'tiny_js_plugin'      => WPBM_PLUGIN_URL . '/js/wpbm_tinymce_btn.js'
							, 'tiny_js_function'    => 'wpbm_init_tinymce_buttons'										// This function NAME exist inside of this JS file: ['tiny_js_plugin']
							, 'tiny_btn_row'        => 1
							, 'pages_where_insert'  => array( 'post-new.php', 'page-new.php', 'post.php', 'page.php' )
							, 'buttons'             => array(
																'wpbm_insert' => array(
																						'hint'  => __('Insert Shortcode' , 'booking-manager' )
																					  , 'title' => __('Insert Shortcode' , 'booking-manager' )
																					  , 'js_func_name_click'    => 'wpbm_tiny_btn_click'
																					  , 'img'   => WPBM_PLUGIN_URL . '/assets/img/icon-16x16.png'
																				  )
													)
                            );
        
        $this->settings = wp_parse_args( $params, $this->settings );
        
        add_action( 'init', array( $this, 'define_init_hooks' ) );   // Define init hooks
        
    }
    
    /** Init all hooks for showing Button in Tiny Toolbar */
    public function define_init_hooks() {
        
        // Don't bother doing this stuff if the current user lacks permissions
        if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )  return;

        if (  ( in_array( basename($_SERVER['PHP_SELF'] ),  $this->settings['pages_where_insert'] ) ) 
              // && ( get_user_option('rich_editing') == 'true' )
            ) {

			/////////////////////////////////////////////////////////////////////////////////////
			//  A D D I N G    B u t t o n     to    toolbar
			/////////////////////////////////////////////////////////////////////////////////////
            // Load JS file  - TinyMCE plugin
            add_filter( 'mce_external_plugins', array( $this, 'load_tiny_js_plugin' ) );

            // Add the custom TinyMCE buttons
            if ( 1 === $this->settings['tiny_btn_row'] ) add_filter( 'mce_buttons',                                     array( $this, 'add_tiny_button' ) );
            else                                         add_filter( 'mce_buttons_' . $this->settings['tiny_btn_row'] , array( $this, 'add_tiny_button' ) );
                                                                                    
            // Add the old style button to the non-TinyMCE editor views
			//FixIn: 2.0.8.2 - compatibility with Gutenberg 4.1- 4.3 ( or newer ) at edit post page.
            //add_action( 'edit_form_advanced',   array( $this, 'add_html_button' ) );                 // Fires after 'normal' context meta boxes have been output
            add_action( 'edit_page_form',       array( $this, 'add_html_button' ) );
            add_action( 'admin_head',           array( $this, 'insert_button') );
			/////////////////////////////////////////////////////////////////////////////////////
			
			// Modal Content
            add_action( 'admin_footer',         array( $this, 'modal_content' ) );
            
            // JS        
            wp_enqueue_script(      'wpdevelop-bootstrap',       wpbm_plugin_url( '/assets/libs/bootstrap/js/bootstrap.js' ), array( 'jquery' ),              '3.3.5.1');        
			
			////////////////////////////////////////////////////////////////////////////////////////////////////////////
            // M o d a l s  -  proxy wpbc_modal no conflict object - usage like jQuery('#wpbm_tiny_modal').wpbc_modal({
			////
			// Because name "wpbc-wpdevelop-bootstrap" load this script only ONCE (in the Booking Calendar or in this plugin)
            wp_enqueue_script( 'wpbc-wpdevelop-bootstrap', wpbm_plugin_url( '/js/wpbm_bs_no_conflict.js' ), array( 'wpdevelop-bootstrap' ), '1.0' );
			////////////////////////////////////////////////////////////////////////////////////////////////////////////

			// // Can not use this,  because its start support only  from WP 4.5 :(
            // // wp_add_inline_script(   'wpdevelop-bootstrap', "var wpbc_modal = jQuery.fn.modal.noConflict(); jQuery.fn.wpbc_modal = wpbc_modal;" );   // Define proxy wpbm_model no conflict  object - usage like jQuery('#wpbm_tiny_modal').wpbc_modal({

            // wp_enqueue_script( 'jquery-ui-dialog'  );


            // CSS
            wp_enqueue_style( 'wpdevelop-bts',              wpbm_plugin_url( '/assets/libs/bootstrap/css/bootstrap.css' ),          array(), '3.3.5.1');
            wp_enqueue_style( 'wpdevelop-bts-theme',        wpbm_plugin_url( '/assets/libs/bootstrap/css/bootstrap-theme.css' ),    array(), '3.3.5.1');
            
            wp_enqueue_style( 'wpbm-admin-support',         wpbm_plugin_url( '/core/any/css/admin-support.css' ),       array(), WPBM_VERSION_NUM);            
            wp_enqueue_style( 'wpbm-admin-modal-popups',    wpbm_plugin_url( '/css/modal.css' ),                        array(), WPBM_VERSION_NUM);            
            
            wp_enqueue_style( 'wpbm-admin-pages',           wpbm_plugin_url( '/css/admin.css' ),                        array(), WPBM_VERSION_NUM);            
            wp_enqueue_style( 'wpbm-admin-menu',            wpbm_plugin_url( '/core/any/css/admin-menu.css' ),          array(), WPBM_VERSION_NUM);
            //wp_enqueue_style( 'wpbm-admin-toolbar',         wpbm_plugin_url( '/core/any/css/admin-toolbar.css' ),       array(), WPBM_VERSION_NUM);
            
            add_action( 'admin_footer',         array( $this, 'write_js' ) );		// Write JavaScript
            add_action( 'admin_footer',         array( $this, 'write_css' ) );		// Write CSS
			
        }            
    }
    //                                                                              </editor-fold>

	
    //                                                                              <editor-fold   defaultstate="collapsed"   desc=" TinyMCE - Add Button " >    

    /** Load JS file  - TinyMCE plugin
     * 
     * @param array $plugins
     * @return array
     */
    public function load_tiny_js_plugin( $plugins ){
    
        $plugins[ $this->settings['tiny_prefix'] . '_quicktags'] = $this->settings['tiny_js_plugin'];
        
        return $plugins;
    }
    
    
    /** Add the custom TinyMCE buttons
     * 
     * @param array $buttons
     * @return array
     */
    public function add_tiny_button( $buttons ) {
                
        array_push( $buttons, "separator" );
        
        foreach ( $this->settings['buttons'] as $type => $strings ) {
            array_push( $buttons, $this->settings['tiny_prefix'] . '_' . $type );
        }

        return $buttons;        
    }
    
    
    /** Add the old style button to the non-TinyMCE editor views */
    public function add_html_button() {
        
        $buttonshtml = '';
        
        foreach ( $this->settings['buttons'] as $type => $props ) {

            $buttonshtml .= '<input type="button" class="ed_button button button-small" onclick="'
                                .$props['js_func_name_click'].'(\'' . $type . '\')" title="' . $props['hint'] . '" value="' . $props['title'] . '" />';
        }
        
        ?><script type="text/javascript">
            // <![CDATA[            
                function wpbm_add_html_button_to_toolbar(){                     // Add buttons  ( HTML view )
                    if ( jQuery( '#ed_toolbar' ).length == 0 ) 
                        setTimeout( 'wpbm_add_html_button_to_toolbar()' , 100 );
                    else 
                        jQuery("#ed_toolbar").append( '<?php echo wp_specialchars_decode( esc_js( $buttonshtml ), ENT_COMPAT ); ?>' );
                }                
                jQuery(document).ready(function(){ 
                    setTimeout( 'wpbm_add_html_button_to_toolbar()' , 100 );
                });
                
            // ]]>
        </script><?php
        
    }
    
    
    public function insert_button() {
        
        $script = '';
    
        if ( ! empty( $this->settings['buttons'] ) ){
            
            $script .= '<script type="text/javascript">';

            $script .= ' function '. $this->settings['tiny_js_function'] . '(ed, url) {';

                foreach ( $this->settings['buttons'] as $type => $props ) {

                    $script .=  " if ( typeof ".$props['js_func_name_click']." == 'undefined' ) return; ";
                    $script .=  "  ed.addButton('".  $this->settings['tiny_prefix'] . '_' . $type ."', {";
                    $script .=  "		title : '". $props['hint'] ."',";
                    $script .=  "		image : '". $props['img'] ."',";
                    $script .=  "		onclick : function() {";
                    $script .=  "			". $props['js_func_name_click'] ."('". $type ."');";
                    $script .=  "		}";
                    $script .=  "	});";
                }
            
            $script .=  ' }';

            $script .= '</script>';
            
            echo $script;
        }        
    }
    
    //                                                                              </editor-fold>
        

    //                                                                              <editor-fold   defaultstate="collapsed"   desc=" M o d a l    C o n t e n t     S t r u c t u r e  " >    
    public function modal_content() {
        
        ?><span class="wpdevelop wpbm_page"><div class="visibility_container clearfix-height" style="display:block;"><?php
        ?><div id="wpbm_tiny_modal" class="modal wpbm_popup_modal wpbm_tiny_modal" tabindex="-1" role="dialog">
              <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">   
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><?php 
                            _e( 'Insert Shortcode' , 'booking-manager' ); 
                            echo ' - <span class="wpbm_shortcode_title">'; 
							
							foreach ( $this->settings['buttons'] as $type => $props ) {
								echo $props['title'];
								break;
							}
                            echo '</span>';  
                        ?></h4>                    
                    </div>
                    <div class="modal-body">
                        <div class="clear" style="height:5px;"></div>
                        <input name="wpbm_shortcode_type" id="wpbm_shortcode_type" value="<?php echo $this->get_default_shortcode(); ?>" autocomplete="off" type="hidden" />
                        <?php
                        
                        // Tabs 
                        wpbm_bs_toolbar_tabs_html_container_start();

                            $wpbm_tabs = $this->get_tiny_tabs();
							
                            foreach ( $wpbm_tabs as $key => $title ) {

                                wpbm_bs_display_tab(   array(
                                                                'title'         => $title 
                                                                // , 'hint' => array( 'title' => __('Manage bookings' , 'booking-manager' ) , 'position' => 'top' )
                                                                , 'onclick'     =>    "jQuery( '.wpbm_tiny_modal .visibility_container').hide();"
                                                                                    . "jQuery( '#wpbm_tiny_container_". $key ."' ).show();"
                                                                                    . "jQuery( '#wpbm_shortcode_type' ).val('" . $key . " ');"
                                                                                    . "jQuery( '.wpbm_tiny_modal .nav-tab').removeClass('nav-tab-active');"
                                                                                    . "jQuery( this ).addClass('nav-tab-active');"
                                                                                    . "jQuery( '.wpbm_tiny_modal .nav-tab i.icon-white').removeClass('icon-white');"
                                                                                    . "jQuery( '.wpbm_tiny_modal .nav-tab-active i').addClass('icon-white');"
                                                                                    . "wpbm_set_shortcode();"                                    

                                                                , 'font_icon'   => ''
                                                                , 'default'     => ( $key == $this->get_default_shortcode() ) ? true : false
                                                                , 'checkbox'    => false
                                                ) ); 
                            }

                        wpbm_bs_toolbar_tabs_html_container_end();

                        wpbm_clear_div();

                        foreach ( $wpbm_tabs as $key => $title ) {

                                ?><div id="wpbm_tiny_container_<?php echo $key; ?>" class="visibility_container clearfix-height" style="<?php 
																						echo ( ( $key == $this->get_default_shortcode() ) ? '' : 'display:none;' ); ?>"><?php 

                                    if ( function_exists( 'wpbm_shortcode_' . str_replace( '-', '_', $key ) ) ) {
                                        // $this->{'shortcode_' . $key}( $key );
										call_user_func( 'wpbm_shortcode_' . str_replace( '-', '_', $key ) , $key );
                                    }            

                                ?></div><?php 
                        }

                        wpbm_clear_div();
                    ?>
                        <input name="wpbm_text_put_in_shortcode" id="wpbm_text_put_in_shortcode" class="put-in" readonly="readonly" onfocus="this.select()" type="text" />
                    </div>
                    <div class="modal-footer" style="text-align:center;"> 

                        <a href="javascript:void(0)" class="button button-primary"  style="float:none;"                                        
                           onclick="javascript:wpbm_send_text_to_editor( jQuery('#wpbm_text_put_in_shortcode').val().trim() );wpbm_tiny_close();"
                           ><?php _e( 'Insert into page' ); ?></a> <a href="javascript:void(0)" class="button" style="float:none;" data-dismiss="modal"><?php _e('Close' , 'booking-manager' ); ?></a>

                   </div>
                </div><!-- /.modal-content -->
              </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <?php  
        ?></div></span><?php        
    }
    //                                                                              </editor-fold>
	
	
	//                                                                              <editor-fold   defaultstate="collapsed"   desc=" Get Shortcode Tabs " >    
	
	function get_tiny_tabs() {
		
		if ( function_exists( 'wpbm_get_tiny_tabs' ) )
			return wpbm_get_tiny_tabs();
		else
			return array( 'Tabs_not_defined' => 'No Tabs :(' );
	}
	

	function get_default_shortcode() {

		$shortcodes = $this->get_tiny_tabs();

		reset( $shortcodes );

		$first_key = key( $shortcodes );

		return $first_key;
	}
	
	//                                                                              </editor-fold>
	
	
	//                                                                              <editor-fold   defaultstate="collapsed"   desc=" J S  /  C S S " >    
	
	public function write_css() {
		?>
        <!-- WPBM CSS -->
		<style type="text/css">
			.wpbm_offset_datetime_from,
			.wpbm_offset_datetime_until {
				display:none;
			}
			#wpbm_tiny_modal .wpbm_text_near_select {
				width:7em;
				height: 28px;
				vertical-align:middle;
				margin-right:5px;
			}
			#wpbm_tiny_modal .wpbm_select_near_text {
				width:6em;
				margin-right:5px;
			}
			/* Mobile *********************************************************************/
			@media (max-width: 782px) {  
				#wpbm_tiny_modal .wpbm_select_near_text, 
				#wpbm_tiny_modal .wpbm_text_near_select {
					width:100% !important;
					height: auto;
					vertical-align:middle;
					margin:0 0 10px 0;
				}
			}		    
		</style>
		<!-- End WPBM CSS -->
		<?php		
	}
	
    public function write_js() {
        ?>
        <!-- WPBM JavaScript -->
        <script type="text/javascript">			
			// Parse shortcode on initial opening
			jQuery(document).ready(function(){
				
				wpbm_set_shortcode();
			});
			
			// Shortcode Parsing - based on external JS
            function wpbm_set_shortcode(){
                
				jQuery( '#wpbm_text_put_in_shortcode' ).css( 'color', '#333' );
				
                var wpbm_shortcode = '[';                
                var wpbm_shortcode_type = jQuery( '#wpbm_shortcode_type' ).val().trim();
                
				///////////////////////////////////////////////////////////////////////////////////////
				if( typeof( check__wpbm_ics_listing ) == 'function' ) {
					wpbm_shortcode = check__wpbm_ics_listing( wpbm_shortcode );
				}
				///////////////////////////////////////////////////////////////////////////////////////
				if( typeof( check__wpbm_ics_import ) == 'function' ) {
					wpbm_shortcode = check__wpbm_ics_import( wpbm_shortcode );
				}
				///////////////////////////////////////////////////////////////////////////////////////
                
                wpbm_shortcode += ']';
                
                jQuery( '#wpbm_text_put_in_shortcode' ).val( wpbm_shortcode );
            }
				
            
            // Open TinyMCE Modal 
            function wpbm_tiny_btn_click( tag ) {
                
                jQuery('#wpbm_tiny_modal').wpbc_modal({
                    keyboard: false
                  , backdrop: true
                  , show: true
                });
            }            
            
            
            // Close TinyMCE Modal 
            function wpbm_tiny_close() {
                
                jQuery('#wpbm_tiny_modal').wpbc_modal('hide');
            }    


            // Send text  to editor 			 
            function wpbm_send_text_to_editor( h ) {
                    var ed, mce = typeof(tinymce) != 'undefined', qt = typeof(QTags) != 'undefined';

                    if ( !wpActiveEditor ) {
                            if ( mce && tinymce.activeEditor ) {
                                    ed = tinymce.activeEditor;
                                    wpActiveEditor = ed.id;
                            } else if ( !qt ) {
                                    return false;
                            }
                    } else if ( mce ) {
                            if ( tinymce.activeEditor && (tinymce.activeEditor.id == 'mce_fullscreen' || tinymce.activeEditor.id == 'wp_mce_fullscreen') )
                                    ed = tinymce.activeEditor;
                            else
                                    ed = tinymce.get(wpActiveEditor);
                    }

                    if ( ed && !ed.isHidden() ) {
                            // restore caret position on IE
                            if ( tinymce.isIE && ed.windowManager.insertimagebookmark )
                                    ed.selection.moveToBookmark(ed.windowManager.insertimagebookmark);

                            if ( h.indexOf('[caption') !== -1 ) {
                                    if ( ed.wpSetImgCaption )
                                            h = ed.wpSetImgCaption(h);
                            } else if ( h.indexOf('[gallery') !== -1 ) {
                                    if ( ed.plugins.wpgallery )
                                            h = ed.plugins.wpgallery._do_gallery(h);
                            } else if ( h.indexOf('[embed') === 0 ) {
                                    if ( ed.plugins.wordpress )
                                            h = ed.plugins.wordpress._setEmbed(h);
                            }

                            ed.execCommand('mceInsertContent', false, h);
                    } else if ( qt ) {
                            QTags.insertContent(h);
                    } else {
                            document.getElementById(wpActiveEditor).value += h;
                    }

                    try{tb_remove();}catch(e){};
            }            
			
        </script>
        <!-- End WPBM JavaScript -->
        <?php

    }
    //                                                                              </editor-fold>
}


$wpbm_pages_where_insert_btn = array( 'post-new.php', 'page-new.php', 'post.php', 'page.php' );

if ( in_array( basename($_SERVER['PHP_SELF'] ),  $wpbm_pages_where_insert_btn ) ) {

	// Start Class for Tiny Toolbar
    new WPBM_TinyMCE_Buttons( 
                            array(
                                      'tiny_prefix'     => 'wpbm_tiny'
                                    , 'tiny_icon_url'   => WPBM_PLUGIN_URL . '/assets/img/icon-16x16.png'
                                    , 'tiny_js_plugin'  => WPBM_PLUGIN_URL . '/js/wpbm_tinymce_btn.js'
                                    , 'tiny_js_function' => 'wpbm_init_tinymce_buttons'                     // This function NAME exist inside of this file: ['tiny_js_plugin']
                                    , 'tiny_btn_row'    => 1
                                    , 'pages_where_insert' => $wpbm_pages_where_insert_btn
                                    , 'buttons'            => array(
                                                                'wpbm_insert' => array(
                                                                                              'hint'  => __('Booking Manager Shortcodes' , 'booking-manager' )
                                                                                            , 'title' => __('Booking Manager' , 'booking-manager' )
                                                                                            , 'js_func_name_click'    => 'wpbm_tiny_btn_click'
                                                                                            , 'img'   => WPBM_PLUGIN_URL . '/assets/img/icon-16x16.png'
                                                                                        )
                                                                )
                            )
                        );
}



//                                                                              <editor-fold   defaultstate="collapsed"   desc=" S u p p o r t " >    
function wpbm_get_bk_resources_toolbar_tiny() {

	if ( ! class_exists( 'wpdev_bk_personal' ) ) 
		return array();

	$resources_cache = wpbc_br_cache();                                     // Get booking resources from  cache        

	$resource_objects = $resources_cache->get_resources();
	// $resource_objects = $resources_cache->get_single_parent_resources();

	//$resource_options = $params['resources'];

	foreach ( $resource_objects as $br) {

		$br_option = array();
		$br_option['title'] = apply_bk_filter('wpdev_check_for_active_language', $br['title'] );

		if ( (isset( $br['parent'] )) && ($br['parent'] == 0 ) && (isset( $br['count'] )) && ($br['count'] > 1 ) )
			$br_option['title'] .= ' [' . __('parent resource', 'booking') . ']';

		$br_option['attr'] = array();
		$br_option['attr']['class'] = 'wpbc_single_resource';
		if ( isset( $br['parent'] ) ) {
			if ( $br['parent'] == 0 ) {
				if (  ( isset( $br['count'] ) ) && ( $br['count'] > 1 )  )
					$br_option['attr']['class'] = 'wpbc_parent_resource';
			} else {
				$br_option['attr']['class'] = 'wpbc_child_resource';
			}
		} 

		$sufix = '';

		$resource_options[ $br['id'] . $sufix ] = $br_option;

		if ( $resource_options[ $br['id'] ]['attr']['class'] === 'wpbc_child_resource' ) {
			$resource_options[ $br['id'] ]['title'] = ' &nbsp;&nbsp;&nbsp; ' . $resource_options[ $br['id'] ]['title'];
		}
	}
	return $resource_options;
}
//                                                                              </editor-fold>

	
//////////////////////////////////////////////////////////////////////////////////////////
// Defining Shortcode Tabs [shortcodes]
//////////////////////////////////////////////////////////////////////////////////////////
function wpbm_get_tiny_tabs() {

	$wpbm_tabs = array();
	
	$wpbm_tabs[ 'booking-manager-listing' ] = __( 'Listing .ics feed' , 'booking-manager' );
	
	//if ( wpbm_is_wpbc_supported() )
		$wpbm_tabs[ 'booking-manager-import' ] = __( 'Import .ics feed into' , 'booking-manager' ) . ' ' . '<strong>WPBC</strong>';							
	
	return $wpbm_tabs;
}



//////////////////////////////////////////////////////////////////////////////////////////
// L i s t i n g
//////////////////////////////////////////////////////////////////////////////////////////
function wpbm_shortcode_booking_manager_listing( $shortcode_section_key ) {
	
	wpbm_shortcode_booking_manager_listing_js();
	
	?><table class="form-table booking_manager_listing"><tbody><?php   

		////////////////////////////////////////////////////////////////////
		// Feed URL
		////////////////////////////////////////////////////////////////////
		WPBM_Settings_API::field_text_row_static( 'wpbm_ics_listing_url'
												, array(  
														  'type'              => 'text'
														, 'title'             => __('URL', 'booking-manager' )
														, 'placeholder'       => str_replace( array( '"', "'" ), '', __('URL to .ics feed or file', 'booking-manager' ) )
														, 'description'       => ''//__('Enter', 'booking-manager' ) . ' ' . __('URL to .ics feed', 'booking-manager' )
														, 'description_tag'   => 'span'
														, 'group'             => $shortcode_section_key
														, 'tr_class'          => $shortcode_section_key . '_standard_section'
														, 'class'             => ''
														, 'css'               => 'width:100%;'
														, 'only_field'        => false
														, 'attr'              => array()                                                    
														, 'value'             => 'https://calendar.google.com/calendar/ical/CALENDAR_ID/public/basic.ics'
													)
								);

		////////////////////////////////////////////////////////////////////
		//   F r o m
		////////////////////////////////////////////////////////////////////      
		
		?><tr valign="top" class="<?php echo $shortcode_section_key . '_standard_section wpbm_section_listing_from'; ?>">
			<th scope="row" style="vertical-align: middle;font-weight:400;font-style: italic;">
				<label for="wpbm_ics_listing_from" class="wpbm-form-text"><?php  
					echo __('From' , 'booking-manager' ); 
				?></label>
			</th>                
			<td class=""><fieldset><?php 
	
			$wpbc_options = array(
								'now'			=> __( 'Now', 'booking-manager' )
							  , 'today'			=> __( '00:00 Today', 'booking-manager' )
							  , 'week'			=> __( 'Start of current week', 'booking-manager' )
							  , 'month-start'	=> __( 'Start of current month', 'booking-manager' )
							  , 'month-end'		=> __( 'End of current month', 'booking-manager' )
							  , 'year-start'	=> __( 'Start of current year', 'booking-manager' )
							  , 'any'			=> __( 'The start of time', 'booking-manager' )
							  , 'date'			=> __( 'Specific date / time', 'booking-manager' )
						);
                                                                                            
			WPBM_Settings_API::field_select_row_static(   'wpbm_ics_listing_from'
													, array(  
															  'type'              => 'select'
															, 'title'             => __('From', 'booking-manager' )
															, 'description'       => ''
															, 'description_tag'   => 'span'
															, 'label'             => ''
															, 'multiple'          => false
															, 'group'             => $shortcode_section_key
															, 'tr_class'          => $shortcode_section_key . '_standard_section'
															, 'class'             => ''
															, 'css'               => 'margin-right:10px;'
															, 'only_field'        => true
															, 'attr'              => array()                                                    
															, 'value'             => 'today'
															, 'options'           => $wpbc_options
														)
						);	
			
			?><label for="wpbm_ics_listing_from_offset" class="wpbm-form-text wpbm_from_offset" style="font-weight:400;"><?php  
				echo __('Offset' , 'booking-manager' ); 
			?></label>
			<label for="wpbm_ics_listing_from_offset" class="wpbm-form-text wpbm_from_date" style="font-weight:400;display:none;"><?php  
				echo __('Date' , 'booking-manager' ); 
			?></label><?php 
		
				WPBM_Settings_API::field_text_row_static( 'wpbm_ics_listing_from_offset'  
															, array(
																	'type'              => 'text'
																  , 'title'             => __('Offset', 'booking-manager' )
																  , 'placeholder'       => ''
																  , 'description'       => ''
																  , 'description_tag'   => 'span'
																  , 'group'             => $shortcode_section_key
																  , 'tr_class'          => $shortcode_section_key . '_standard_section'
																  , 'class'             => 'wpbm_text_near_select'
																  , 'css'               => 'width:7em;'
																  , 'only_field'        => true
																  , 'attr'              => array()                                                    
																  , 'value'             => ''
																)
						);
				WPBM_Settings_API::field_select_row_static(  'wpbm_ics_listing_from_offset_type'
															, array(  
																	  'type'              => 'select'
																	, 'title'             => ''
																	, 'description'       => ''
																	, 'description_tag'   => 'span'
																	, 'label'             => ''
																	, 'multiple'          => false
																	, 'group'             => $shortcode_section_key
																	, 'class'             => 'wpbm_select_near_text wpbm_from_offset'
																	, 'css'               => 'width:6em;'
																	, 'only_field'        => true
																	, 'attr'              => array()                                                    
																	, 'value'             => date( 'Y' )
																	, 'options'			  => array(
																								    'day'	 => __( 'days' ,'booking-manager' )
																								  , 'hour'	 => __( 'hours' ,'booking-manager' )
																								  , 'minute' => __( 'minutes' ,'booking-manager' )
																								  , 'second' => __( 'seconds' ,'booking-manager' )
																							)
																	//, 'options'           => array_combine( range( ( date('Y') - 1 ), ( date('Y') + 10 ) ), range( ( date('Y') - 1 ), ( date('Y') + 10 ) )  )
																)
								);   
			?><span class="description wpbm_from_offset"> <?php _e('You can specify an optional offset from you chosen start point. The offset can be negative.', 'booking-manager' );  ?></span>
			  <span class="wpbm_from_date" style="display:none;">
					<em><?php printf( __( 'Type your date in format %s. Example: %s', 'booking-manager' ), 'Y-m-d', '2017-08-02' ); ?></em>
				</span>							
				</fieldset></td>
		</tr><?php 
		
		

		////////////////////////////////////////////////////////////////////
		//   U n t i l 
		////////////////////////////////////////////////////////////////////
		
		?><tr valign="top" class="<?php echo $shortcode_section_key . '_standard_section wpbm_section_listing_until'; ?>">
			<th scope="row" style="vertical-align: middle;font-weight:400;font-style: italic;">
				<label for="wpbm_ics_listing_until" class="wpbm-form-text"><?php  
					echo __('Until' , 'booking-manager' ); 
				?></label>
			</th>                
			<td class=""><fieldset><?php 
	
		$wpbc_options = array(
								'now'			=> __( 'Now', 'booking-manager' )
							  , 'today'			=> __( '00:00 Today', 'booking-manager' )
							  , 'week'			=> __( 'End of current week', 'booking-manager' )
							  , 'month-start'	=> __( 'Start of current month', 'booking-manager' )
							  , 'month-end'		=> __( 'End of current month', 'booking-manager' )
							  , 'year-end'		=> __( 'End of current year', 'booking-manager' )
							  , 'any'			=> __( 'The end of time', 'booking-manager' )
							  , 'date'			=> __( 'Specific date / time', 'booking-manager' )
						);
                                                                                            
		WPBM_Settings_API::field_select_row_static(   'wpbm_ics_listing_until'
													, array(  
															  'type'              => 'select'
															, 'title'             => __('Until', 'booking-manager' )
															, 'description'       => ''
															, 'description_tag'   => 'span'
															, 'label'             => ''
															, 'multiple'          => false
															, 'group'             => $shortcode_section_key
															, 'tr_class'          => $shortcode_section_key . '_standard_section'
															, 'class'             => ''
															, 'css'               => 'margin-right:10px;'
															, 'only_field'        => true
															, 'attr'              => array()                                                    
															, 'value'             => 'year-end'
															, 'options'           => $wpbc_options
														)
						);		
		
				?><label for="wpbm_ics_listing_until_offset" class="wpbm-form-text wpbm_until_offset" style="font-weight:400;"><?php  
					echo __('Offset' , 'booking-manager' ); 
				?></label>
				<label for="wpbm_ics_listing_until_offset" class="wpbm-form-text wpbm_until_date" style="font-weight:400;display:none;"><?php  
					echo __('Date' , 'booking-manager' ); 
				?></label><?php 

				WPBM_Settings_API::field_text_row_static( 'wpbm_ics_listing_until_offset'  
															, array(
																	'type'              => 'text'
																  , 'title'             => __('Offset', 'booking-manager' )
																  , 'placeholder'       => ''
																  , 'description'       => ''
																  , 'description_tag'   => 'span'
																  , 'group'             => $shortcode_section_key
																  , 'tr_class'          => $shortcode_section_key . '_standard_section'
																  , 'class'             => 'wpbm_text_near_select'
																  , 'css'               => 'width:7em;'
																  , 'only_field'        => true
																  , 'attr'              => array()                                                    
																  , 'value'             => ''
																)
						);
				WPBM_Settings_API::field_select_row_static(  'wpbm_ics_listing_until_offset_type'
															, array(  
																	  'type'              => 'select'
																	, 'title'             => ''
																	, 'description'       => ''
																	, 'description_tag'   => 'span'
																	, 'label'             => ''
																	, 'multiple'          => false
																	, 'group'             => $shortcode_section_key
																	, 'class'             => 'wpbm_select_near_text wpbm_until_offset'
																	, 'css'               => 'width:6em;'
																	, 'only_field'        => true
																	, 'attr'              => array()                                                    
																	, 'value'             => date( 'Y' )
																	, 'options'			  => array(
																								    'day'	 => __( 'days' ,'booking-manager' )
																								  , 'hour'	 => __( 'hours' ,'booking-manager' )
																								  , 'minute' => __( 'minutes' ,'booking-manager' )
																								  , 'second' => __( 'seconds' ,'booking-manager' )
																							)
																	//, 'options'           => array_combine( range( ( date('Y') - 1 ), ( date('Y') + 10 ) ), range( ( date('Y') - 1 ), ( date('Y') + 10 ) )  )
																)
								);   
			?><span class="description wpbm_until_offset"> <?php _e('You can specify an optional offset from you chosen end point. The offset can be negative.', 'booking-manager' );  ?></span>
			  <span class="wpbm_until_date" style="display:none;">
					<em><?php printf( __( 'Type your date in format %s. Example: %s', 'booking-manager' ), 'Y-m-d', '2017-08-02' ); ?></em>
				</span>							
				</fieldset></td>
		</tr><?php 
		
		////////////////////////////////////////////////////////////////////
		// Maximum number
		////////////////////////////////////////////////////////////////////
		WPBM_Settings_API::field_select_row_static(   'wpbm_ics_listing_max'
													, array(  
															  'type'              => 'select'
															, 'title'             => __('Maximum number', 'booking-manager' )
															, 'description'       => __('You can specify the maximum number of events.' , 'booking-manager' )
															, 'description_tag'   => 'span'
															, 'label'             => ''
															, 'multiple'          => false
															, 'group'             => $shortcode_section_key
															, 'tr_class'          => $shortcode_section_key . '_standard_section'
															, 'class'             => ''
															, 'css'               => ''
															, 'only_field'        => false
															, 'attr'              => array()                                                    
															, 'value'             => 0
															, 'options'           => array_combine( 
																									  array_merge( array( '0' ), range( 500, 10 , 10 ) ) 
																									, array_merge( array( '&nbsp;-&nbsp;' ),   range( 500, 10 , 10 ) ) 
																						)
														)
						);                

		WPBM_Settings_API::field_select_row_static(  'wpbm_listing_is_all_dates_in'
													, array(  
															  'type'              => 'select'
															, 'title'             => __( 'Event condition', 'booking-manager')
															, 'description'       => __( 'Show event, if all dates or only some date(s) within conditional paramters from / untill', 'booking-manager' )
															, 'description_tag'   => 'span'
															, 'label'             => ''
															, 'multiple'          => false
															, 'group'             => $shortcode_section_key
															, 'tr_class'          => $shortcode_section_key . '_standard_section'
															, 'class'             => ''
															, 'css'               => 'margin-right:10px;'
															, 'only_field'        => false
															, 'attr'              => array()                                                    
															, 'value'             => '0'
															, 'options'           => array(
																							'0' => __( 'Some date(s) in conditional interval', 'booking-manager' )
																						  , '1' => __( 'Strict. All dates in conditional interval', 'booking-manager' )																						  
																						)
														)
						);
		
            ?></tbody></table><?php   
			
/*            
echo "<pre style=''>
Example of shortcode usage:    
[booking-manager-listing url='https://calendar.google.com/calendar/ical/CALENDAR_ID/public/basic.ics' from='2017-09-01' until='month-end']
</pre>";            
*/
}

function wpbm_shortcode_booking_manager_listing_js() {
	?>
	<script type="text/javascript">
	
//		// Show / Hide Offsets
//		jQuery( function ( $ ) {                                                                            // Shortcut to  jQuery(document).ready(function(){ ... });
//			jQuery( '.wpbm_tr_wpbm_ics_listing_show_offset_until' ).on( 'click', '.show_link', function ( event ) {             // This delegated event, can be run, when DOM element added after page loaded
//				jQuery( this ).hide();
//			});
//		});
			
		jQuery( function ( $ ) {                                                                            // Shortcut to  jQuery(document).ready(function(){ ... });
			
			jQuery( '#wpbm_ics_listing_url,#wpbm_ics_listing_from,#wpbm_ics_listing_from_offset,#wpbm_ics_listing_from_offset_type,'
					+ '#wpbm_ics_listing_until,#wpbm_ics_listing_until_offset,#wpbm_ics_listing_until_offset_type,#wpbm_ics_listing_max,#wpbm_listing_is_all_dates_in').on( 'change', function(){ 
                           
                    wpbm_set_shortcode();
			});                                     		
		});
	
		// [booking-manager-listing ... ]
		function check__wpbm_ics_listing( wpbm_shortcode ){

			//var wpbm_shortcode = '[';                
			var wpbm_shortcode_type = jQuery( '#wpbm_shortcode_type' ).val().trim();

			////////////////////////////////////////////////////////////////
			// [booking-manager-listing]
			////////////////////////////////////////////////////////////////
			if ( wpbm_shortcode_type == 'booking-manager-listing' ) {   
				
				wpbm_shortcode += 'booking-manager-listing';
				
				////////////////////////////////////////////////////////////////
				// URL
				////////////////////////////////////////////////////////////////
				if ( jQuery( '#wpbm_ics_listing_url' ).val().trim() != '' ) {
					var wpbm_shortcode_url_temp = jQuery( '#wpbm_ics_listing_url' ).val().trim().replace(/'/gi, '');
					wpbm_shortcode += ' url=\'' + wpbm_shortcode_url_temp + '\'';
				} else {
					wpbm_shortcode = '[ URL is required ' 
					jQuery( '#wpbm_text_put_in_shortcode' ).css( 'color', '#F00' );
					return wpbm_shortcode;	
				}
				
				
				////////////////////////////////////////////////////////////////
				// FROM 
				////////////////////////////////////////////////////////////////
				var p_from		  = jQuery( '#wpbm_ics_listing_from' ).val().trim();
				var p_from_offset = jQuery( '#wpbm_ics_listing_from_offset' ).val().trim();
				
				// Hide | Show date/offset labels
				if ( p_from == 'date' ) {
					jQuery( '.booking_manager_listing .wpbm_from_offset' ).hide();
					jQuery( '.booking_manager_listing .wpbm_from_date' ).show();						
				} else {
					jQuery( '.booking_manager_listing .wpbm_from_offset' ).show();
					jQuery( '.booking_manager_listing .wpbm_from_date' ).hide();						
				}
				if ( p_from == 'any' ) {
					jQuery( '.booking_manager_listing .wpbm_from_offset' ).hide();
					jQuery( '.booking_manager_listing .wpbm_from_date, #wpbm_ics_listing_from_offset' ).hide();	
				} else {
					jQuery( '#wpbm_ics_listing_from_offset' ).show();	
				}
				
				
				if ( p_from != 'date' ){					
					
					wpbm_shortcode += ' from=\'' + p_from + '\'';

					if ( ( p_from != 'any' ) && ( p_from_offset != '' ) ){					
						p_from_offset = parseInt( p_from_offset );
						if ( ! isNaN( p_from_offset ) )
							wpbm_shortcode += ' from_offset=\'' + p_from_offset
															+ jQuery( '#wpbm_ics_listing_from_offset_type' ).val().trim().charAt(0) 					
													 + '\'';
					}
					
				} else if ( ( p_from == 'date' ) && ( p_from_offset != '' ) ){		// If selected Date
				
					wpbm_shortcode += ' from=\'' + p_from_offset + '\'';					
				}
				
				////////////////////////////////////////////////////////////////
				// Until 
				////////////////////////////////////////////////////////////////
				var p_until		  = jQuery( '#wpbm_ics_listing_until' ).val().trim();
				var p_until_offset = jQuery( '#wpbm_ics_listing_until_offset' ).val().trim();
				
				// Hide | Show date/offset labels
				if ( p_until == 'date' ) {
					jQuery( '.booking_manager_listing .wpbm_until_offset' ).hide();
					jQuery( '.booking_manager_listing .wpbm_until_date' ).show();					
				} else {
					jQuery( '.booking_manager_listing .wpbm_until_offset' ).show();
					jQuery( '.booking_manager_listing .wpbm_until_date' ).hide();					
				}
				if ( p_until == 'any' ) {
					jQuery( '.booking_manager_listing .wpbm_until_offset' ).hide();
					jQuery( '.booking_manager_listing .wpbm_until_date, #wpbm_ics_listing_until_offset' ).hide();	
				} else {
					jQuery( '#wpbm_ics_listing_until_offset' ).show();	
				}
				
				
				if ( p_until != 'date' ){					
					
					wpbm_shortcode += ' until=\'' + p_until + '\'';

					if ( ( p_until != 'any' ) && ( p_until_offset != '' ) ){					
						p_until_offset = parseInt( p_until_offset );
						if ( ! isNaN( p_until_offset ) )
							wpbm_shortcode += ' until_offset=\'' + p_until_offset
															+ jQuery( '#wpbm_ics_listing_until_offset_type' ).val().trim().charAt(0) 					
													 + '\'';
					}
					
				} else if ( ( p_until == 'date' ) && ( p_until_offset != '' ) ){	// If selected Date
				
					wpbm_shortcode += ' until=\'' + p_until_offset + '\'';
				}

				////////////////////////////////////////////////////////////////
				// Max
				////////////////////////////////////////////////////////////////
				var p_max = parseInt( jQuery( '#wpbm_ics_listing_max' ).val().trim() );	
				if ( p_max != 0 ) {
					wpbm_shortcode += ' max=' + p_max;
				}
				
				////////////////////////////////////////////////////////////////
				// is_all_dates_in
				////////////////////////////////////////////////////////////////
				var p_is_all_dates_in = parseInt( jQuery( '#wpbm_listing_is_all_dates_in' ).val().trim() );	
				
				if ( p_is_all_dates_in != 0 ) {
					wpbm_shortcode += ' is_all_dates_in=' + p_is_all_dates_in;
				}
				

			}                    

			return wpbm_shortcode;	
		}
	
	</script>
	<?php
}


//////////////////////////////////////////////////////////////////////////////////////////
// I m p o r t
//////////////////////////////////////////////////////////////////////////////////////////
function wpbm_shortcode_booking_manager_import( $shortcode_section_key ) {
	
	wpbm_shortcode_booking_manager_import_js();
	
	?><table class="form-table booking_manager_import"><tbody><?php   

		////////////////////////////////////////////////////////////////////
		// Feed URL
		////////////////////////////////////////////////////////////////////
		WPBM_Settings_API::field_text_row_static( 'wpbm_ics_import_url'
												, array(  
														  'type'              => 'text'
														, 'title'             => __('URL', 'booking-manager' )
														, 'placeholder'       => str_replace( array( '"', "'" ), '', __('URL to .ics feed or file', 'booking-manager' ) )
														, 'description'       => ''//__('Enter', 'booking-manager' ) . ' ' . __('URL to .ics feed', 'booking-manager' )
														, 'description_tag'   => 'span'
														, 'group'             => $shortcode_section_key
														, 'tr_class'          => $shortcode_section_key . '_standard_section'
														, 'class'             => ''
														, 'css'               => 'width:100%;'
														, 'only_field'        => false
														, 'attr'              => array()                                                    
														, 'value'             => 'https://calendar.google.com/calendar/ical/CALENDAR_ID/public/basic.ics'
													)
								);
		
		////////////////////////////////////////////////////////////////////
		// Booking Resources
		////////////////////////////////////////////////////////////////////        
		if ( class_exists( 'wpdev_bk_personal' ) ){ 

			WPBM_Settings_API::field_select_row_static(  'wpbm_ics_import_booking_resource'
														, array(  
																  'type'              => 'select'
																, 'title'             => __('Booking resource', 'booking-manager')
																, 'description'       => __( 'Select booking resource, where event will be imported', 'booking-manager' )
																, 'description_tag'   => 'span'
																, 'label'             => ''
																, 'multiple'          => false
																, 'group'             => $shortcode_section_key
																, 'tr_class'          => $shortcode_section_key . '_standard_section'
																, 'class'             => ''
																, 'css'               => 'margin-right:10px;'
																, 'only_field'        => false
																, 'attr'              => array()                                                    
																, 'value'             => ''
																, 'options'           => wpbm_get_bk_resources_toolbar_tiny()
															)
							);
		}
		
		////////////////////////////////////////////////////////////////////
		// import_conditions
		////////////////////////////////////////////////////////////////////        		
		WPBM_Settings_API::field_select_row_static(  'wpbm_ics_import_import_conditions'
													, array(  
															  'type'              => 'select'
															, 'title'             => __( 'Import condition', 'booking-manager')
															, 'description'       => __( 'Whether import events for dates, that already booked in specific booking resource', 'booking-manager' )
															, 'description_tag'   => 'span'
															, 'label'             => ''
															, 'multiple'          => false
															, 'group'             => $shortcode_section_key
															, 'tr_class'          => $shortcode_section_key . '_standard_section'
															, 'class'             => ''
															, 'css'               => 'margin-right:10px;'
															, 'only_field'        => false
															, 'attr'              => array()                                                    
															, 'value'             => ''
															, 'options'           => array(
																							'' => __( 'Import in any case', 'booking-manager' )
																						  , 'if_dates_free' => __( 'Import only, if days are available', 'booking-manager' )																						  
																						)
														)
						);

		////////////////////////////////////////////////////////////////////
		//   F r o m
		////////////////////////////////////////////////////////////////////      
		
		?><tr valign="top" class="<?php echo $shortcode_section_key . '_standard_section wpbm_section_import_from'; ?>">
			<th scope="row" style="vertical-align: middle;font-weight:400;font-style: italic;">
				<label for="wpbm_ics_import_from" class="wpbm-form-text"><?php  
					echo __('From' , 'booking-manager' ); 
				?></label>
			</th>                
			<td class=""><fieldset><?php 
	
			$wpbc_options = array(
								'now'			=> __( 'Now', 'booking-manager' )
							  , 'today'			=> __( '00:00 Today', 'booking-manager' )
							  , 'week'			=> __( 'Start of current week', 'booking-manager' )
							  , 'month-start'	=> __( 'Start of current month', 'booking-manager' )
							  , 'month-end'		=> __( 'End of current month', 'booking-manager' )
							  , 'year-start'	=> __( 'Start of current year', 'booking-manager' )
							  , 'any'			=> __( 'The start of time', 'booking-manager' )
							  , 'date'			=> __( 'Specific date / time', 'booking-manager' )
						);
                                                                                            
			WPBM_Settings_API::field_select_row_static(   'wpbm_ics_import_from'
													, array(  
															  'type'              => 'select'
															, 'title'             => __('From', 'booking-manager' )
															, 'description'       => ''
															, 'description_tag'   => 'span'
															, 'label'             => ''
															, 'multiple'          => false
															, 'group'             => $shortcode_section_key
															, 'tr_class'          => $shortcode_section_key . '_standard_section'
															, 'class'             => ''
															, 'css'               => 'margin-right:10px;'
															, 'only_field'        => true
															, 'attr'              => array()                                                    
															, 'value'             => 'today'
															, 'options'           => $wpbc_options
														)
						);	
			
			?><label for="wpbm_ics_import_from_offset" class="wpbm-form-text wpbm_from_offset" style="font-weight:400;"><?php  
				echo __('Offset' , 'booking-manager' ); 
			?></label>
			<label for="wpbm_ics_import_from_offset" class="wpbm-form-text wpbm_from_date" style="font-weight:400;display:none;"><?php  
				echo __('Date' , 'booking-manager' ); 
			?></label><?php 
		
				WPBM_Settings_API::field_text_row_static( 'wpbm_ics_import_from_offset'  
															, array(
																	'type'              => 'text'
																  , 'title'             => __('Offset', 'booking-manager' )
																  , 'placeholder'       => ''
																  , 'description'       => ''
																  , 'description_tag'   => 'span'
																  , 'group'             => $shortcode_section_key
																  , 'tr_class'          => $shortcode_section_key . '_standard_section'
																  , 'class'             => 'wpbm_text_near_select'
																  , 'css'               => 'width:7em;'
																  , 'only_field'        => true
																  , 'attr'              => array()                                                    
																  , 'value'             => ''
																)
						);
				WPBM_Settings_API::field_select_row_static(  'wpbm_ics_import_from_offset_type'
															, array(  
																	  'type'              => 'select'
																	, 'title'             => ''
																	, 'description'       => ''
																	, 'description_tag'   => 'span'
																	, 'label'             => ''
																	, 'multiple'          => false
																	, 'group'             => $shortcode_section_key
																	, 'class'             => 'wpbm_select_near_text wpbm_from_offset'
																	, 'css'               => 'width:6em;'
																	, 'only_field'        => true
																	, 'attr'              => array()                                                    
																	, 'value'             => 'day'
																	, 'options'			  => array(
																								    'day'	 => __( 'days' ,'booking-manager' )
																								  , 'hour'	 => __( 'hours' ,'booking-manager' )
																								  , 'minute' => __( 'minutes' ,'booking-manager' )
																								  , 'second' => __( 'seconds' ,'booking-manager' )
																							)
																	//, 'options'           => array_combine( range( ( date('Y') - 1 ), ( date('Y') + 10 ) ), range( ( date('Y') - 1 ), ( date('Y') + 10 ) )  )
																)
								);   
			?><span class="description wpbm_from_offset"> <?php _e('You can specify an optional offset from you chosen start point. The offset can be negative.', 'booking-manager' );  ?></span>
			  <span class="wpbm_from_date" style="display:none;">
					<em><?php printf( __( 'Type your date in format %s. Example: %s', 'booking-manager' ), 'Y-m-d', '2017-08-02' ); ?></em>
				</span>							
				</fieldset></td>
		</tr><?php 
		
		

		////////////////////////////////////////////////////////////////////
		//   U n t i l 
		////////////////////////////////////////////////////////////////////
		
		?><tr valign="top" class="<?php echo $shortcode_section_key . '_standard_section wpbm_section_import_until'; ?>">
			<th scope="row" style="vertical-align: middle;font-weight:400;font-style: italic;">
				<label for="wpbm_ics_import_until" class="wpbm-form-text"><?php  
					echo __('Until' , 'booking-manager' ); 
				?></label>
			</th>                
			<td class=""><fieldset><?php 
	
		$wpbc_options = array(
								'now'			=> __( 'Now', 'booking-manager' )
							  , 'today'			=> __( '00:00 Today', 'booking-manager' )
							  , 'week'			=> __( 'End of current week', 'booking-manager' )
							  , 'month-start'	=> __( 'Start of current month', 'booking-manager' )
							  , 'month-end'		=> __( 'End of current month', 'booking-manager' )
							  , 'year-end'		=> __( 'End of current year', 'booking-manager' )
							  , 'any'			=> __( 'The end of time', 'booking-manager' )
							  , 'date'			=> __( 'Specific date / time', 'booking-manager' )
						);
                                                                                            
		WPBM_Settings_API::field_select_row_static(   'wpbm_ics_import_until'
													, array(  
															  'type'              => 'select'
															, 'title'             => __('Until', 'booking-manager' )
															, 'description'       => ''
															, 'description_tag'   => 'span'
															, 'label'             => ''
															, 'multiple'          => false
															, 'group'             => $shortcode_section_key
															, 'tr_class'          => $shortcode_section_key . '_standard_section'
															, 'class'             => ''
															, 'css'               => 'margin-right:10px;'
															, 'only_field'        => true
															, 'attr'              => array()                                                    
															, 'value'             => 'year-end'
															, 'options'           => $wpbc_options
														)
						);		
		
				?><label for="wpbm_ics_import_until_offset" class="wpbm-form-text wpbm_until_offset" style="font-weight:400;"><?php  
					echo __('Offset' , 'booking-manager' ); 
				?></label>
				<label for="wpbm_ics_import_until_offset" class="wpbm-form-text wpbm_until_date" style="font-weight:400;display:none;"><?php  
					echo __('Date' , 'booking-manager' ); 
				?></label><?php 

				WPBM_Settings_API::field_text_row_static( 'wpbm_ics_import_until_offset'  
															, array(
																	'type'              => 'text'
																  , 'title'             => __('Offset', 'booking-manager' )
																  , 'placeholder'       => ''
																  , 'description'       => ''
																  , 'description_tag'   => 'span'
																  , 'group'             => $shortcode_section_key
																  , 'tr_class'          => $shortcode_section_key . '_standard_section'
																  , 'class'             => 'wpbm_text_near_select'
																  , 'css'               => 'width:7em;'
																  , 'only_field'        => true
																  , 'attr'              => array()                                                    
																  , 'value'             => ''
																)
						);
				WPBM_Settings_API::field_select_row_static(  'wpbm_ics_import_until_offset_type'
															, array(  
																	  'type'              => 'select'
																	, 'title'             => ''
																	, 'description'       => ''
																	, 'description_tag'   => 'span'
																	, 'label'             => ''
																	, 'multiple'          => false
																	, 'group'             => $shortcode_section_key
																	, 'class'             => 'wpbm_select_near_text wpbm_until_offset'
																	, 'css'               => 'width:6em;'
																	, 'only_field'        => true
																	, 'attr'              => array()                                                    
																	, 'value'             => 'day'
																	, 'options'			  => array(
																								    'day'	 => __( 'days' ,'booking-manager' )
																								  , 'hour'	 => __( 'hours' ,'booking-manager' )
																								  , 'minute' => __( 'minutes' ,'booking-manager' )
																								  , 'second' => __( 'seconds' ,'booking-manager' )
																							)
																	//, 'options'           => array_combine( range( ( date('Y') - 1 ), ( date('Y') + 10 ) ), range( ( date('Y') - 1 ), ( date('Y') + 10 ) )  )
																)
								);   
			?><span class="description wpbm_until_offset"> <?php _e('You can specify an optional offset from you chosen end point. The offset can be negative.', 'booking-manager' );  ?></span>
			  <span class="wpbm_until_date" style="display:none;">
					<em><?php printf( __( 'Type your date in format %s. Example: %s', 'booking-manager' ), 'Y-m-d', '2017-08-02' ); ?></em>
				</span>							
				</fieldset></td>
		</tr><?php 
		
		////////////////////////////////////////////////////////////////////
		// Maximum number
		////////////////////////////////////////////////////////////////////
		WPBM_Settings_API::field_select_row_static(   'wpbm_ics_import_max'
													, array(  
															  'type'              => 'select'
															, 'title'             => __('Maximum number', 'booking-manager' )
															, 'description'       => __('You can specify the maximum number of events.' , 'booking-manager' )
															, 'description_tag'   => 'span'
															, 'label'             => ''
															, 'multiple'          => false
															, 'group'             => $shortcode_section_key
															, 'tr_class'          => $shortcode_section_key . '_standard_section'
															, 'class'             => ''
															, 'css'               => ''
															, 'only_field'        => false
															, 'attr'              => array()                                                    
															, 'value'             => 0
															, 'options'           => array_combine( 
																									  array_merge( array( '0' ), range( 500, 10 , 10 ) ) 
																									, array_merge( array( '&nbsp;-&nbsp;' ),   range( 500, 10 , 10 ) ) 
																						)
														)
						);                
                
		
		WPBM_Settings_API::field_select_row_static(  'wpbm_import_is_all_dates_in'
													, array(  
															  'type'              => 'select'
															, 'title'             => __( 'Event condition', 'booking-manager')
															, 'description'       => __( 'Imort booking, if all dates or only some date(s) within conditional paramters from / untill', 'booking-manager' )
															, 'description_tag'   => 'span'
															, 'label'             => ''
															, 'multiple'          => false
															, 'group'             => $shortcode_section_key
															, 'tr_class'          => $shortcode_section_key . '_standard_section'
															, 'class'             => ''
															, 'css'               => 'margin-right:10px;'
															, 'only_field'        => false
															, 'attr'              => array()                                                    
															, 'value'             => '1'
															, 'options'           => array(
																							'0' => __( 'Some date(s) in conditional interval', 'booking-manager' )
																						  , '1' => __( 'Strict. All dates in conditional interval', 'booking-manager' )																						  
																						)
														)
						);
		
            ?></tbody></table><?php   
			

}


function wpbm_shortcode_booking_manager_import_js() {
	?>
	<script type="text/javascript">
//		// Show / Hide Offsets
//		jQuery( function ( $ ) {                                                                            // Shortcut to  jQuery(document).ready(function(){ ... });
//			jQuery( '.wpbm_tr_wpbm_ics_listing_show_offset_until' ).on( 'click', '.show_link', function ( event ) {             // This delegated event, can be run, when DOM element added after page loaded
//				jQuery( this ).hide();
//			});
//		});
			
		jQuery( function ( $ ) {                                                                            // Shortcut to  jQuery(document).ready(function(){ ... });
			
			jQuery( '#wpbm_ics_import_url,#wpbm_ics_import_from,#wpbm_ics_import_from_offset,#wpbm_ics_import_from_offset_type,'
					+ '#wpbm_ics_import_until,#wpbm_ics_import_until_offset,#wpbm_ics_import_until_offset_type,#wpbm_ics_import_max,'
					+ '#wpbm_ics_import_booking_resource,#wpbm_ics_import_import_conditions,#wpbm_import_is_all_dates_in').on( 'change', function(){ 
                           
                    wpbm_set_shortcode();
			});                                     		
		});
	
		// [booking-manager-import ... ]
		function check__wpbm_ics_import( wpbm_shortcode ){

			//var wpbm_shortcode = '[';                
			var wpbm_shortcode_type = jQuery( '#wpbm_shortcode_type' ).val().trim();

			////////////////////////////////////////////////////////////////
			// [booking-manager-import]
			////////////////////////////////////////////////////////////////
			if ( wpbm_shortcode_type == 'booking-manager-import' ) {   
				
				wpbm_shortcode += 'booking-manager-import';
				
				////////////////////////////////////////////////////////////////
				// URL
				////////////////////////////////////////////////////////////////
				if ( jQuery( '#wpbm_ics_import_url' ).val().trim() != '' ) {
					var wpbm_shortcode_url_temp = jQuery( '#wpbm_ics_import_url' ).val().trim().replace(/'/gi, '');
					wpbm_shortcode += ' url=\'' + wpbm_shortcode_url_temp + '\'';
				} else {
					wpbm_shortcode = '[ URL is required ' 
					jQuery( '#wpbm_text_put_in_shortcode' ).css( 'color', '#F00' );
					return wpbm_shortcode;	
				}
				
				////////////////////////////////////////////////////////////////
				// Booking resource ID
				////////////////////////////////////////////////////////////////
				if ( jQuery( '#wpbm_ics_import_booking_resource' ).length >0 ){											//FixIn: 2.0.3.1
					var p_br = parseInt( jQuery( '#wpbm_ics_import_booking_resource' ).val().trim() );

					if ( p_br > 0 ){
						wpbm_shortcode += ' resource_id=' + p_br;
					}
				}
				////////////////////////////////////////////////////////////////
				// FROM 
				////////////////////////////////////////////////////////////////
				var p_from		  = jQuery( '#wpbm_ics_import_from' ).val().trim();
				var p_from_offset = jQuery( '#wpbm_ics_import_from_offset' ).val().trim();
				
				// Hide | Show date/offset labels
				if ( p_from == 'date' ) {
					jQuery( '.booking_manager_import .wpbm_from_offset' ).hide();
					jQuery( '.booking_manager_import .wpbm_from_date' ).show();						
				} else {
					jQuery( '.booking_manager_import .wpbm_from_offset' ).show();
					jQuery( '.booking_manager_import .wpbm_from_date' ).hide();						
				}
				if ( p_from == 'any' ) {
					jQuery( '.booking_manager_import .wpbm_from_offset' ).hide();
					jQuery( '.booking_manager_import .wpbm_from_date, #wpbm_ics_import_from_offset' ).hide();	
				} else {
					jQuery( '#wpbm_ics_import_from_offset' ).show();	
				}


				if ( p_from != 'date' ) {					
					
					wpbm_shortcode += ' from=\'' + p_from + '\'';

					if ( ( p_from != 'any' ) && ( p_from_offset != '' ) ){					
						p_from_offset = parseInt( p_from_offset );
						if ( ! isNaN( p_from_offset ) )
							wpbm_shortcode += ' from_offset=\'' + p_from_offset
															+ jQuery( '#wpbm_ics_import_from_offset_type' ).val().trim().charAt(0) 					
													 + '\'';
					}
					
				} else if ( ( p_from == 'date' ) && ( p_from_offset != '' ) ){		// If selected Date
				
					wpbm_shortcode += ' from=\'' + p_from_offset + '\'';
				}
				
				////////////////////////////////////////////////////////////////
				// Until 
				////////////////////////////////////////////////////////////////
				var p_until		  = jQuery( '#wpbm_ics_import_until' ).val().trim();
				var p_until_offset = jQuery( '#wpbm_ics_import_until_offset' ).val().trim();
				
				// Hide | Show date/offset labels
				if ( p_until == 'date' ) {
					jQuery( '.booking_manager_import .wpbm_until_offset' ).hide();
					jQuery( '.booking_manager_import .wpbm_until_date' ).show();					
				} else {
					jQuery( '.booking_manager_import .wpbm_until_offset' ).show();
					jQuery( '.booking_manager_import .wpbm_until_date' ).hide();					
				}
				if ( p_until == 'any' ) {
					jQuery( '.booking_manager_import .wpbm_until_offset' ).hide();
					jQuery( '.booking_manager_import .wpbm_until_date, #wpbm_ics_import_until_offset' ).hide();	
				} else {
					jQuery( '#wpbm_ics_import_until_offset' ).show();	
				}
				
				
				
				
				if ( p_until != 'date' ){					
					
					wpbm_shortcode += ' until=\'' + p_until + '\'';

					if ( ( p_until != 'any' ) && ( p_until_offset != '' ) ) {					
						p_until_offset = parseInt( p_until_offset );
						if ( ! isNaN( p_until_offset ) )
							wpbm_shortcode += ' until_offset=\'' + p_until_offset
															+ jQuery( '#wpbm_ics_import_until_offset_type' ).val().trim().charAt(0) 					
													 + '\'';
					}
					
				} else if ( ( p_until == 'date' ) && ( p_until_offset != '' ) ){	// If selected Date
				
					wpbm_shortcode += ' until=\'' + p_until_offset + '\'';
				}

				////////////////////////////////////////////////////////////////
				// Max
				////////////////////////////////////////////////////////////////
				var p_max = parseInt( jQuery( '#wpbm_ics_import_max' ).val().trim() );	
				
				
				if ( p_max != 0 ) {
					wpbm_shortcode += ' max=' + p_max;
				}
								
				////////////////////////////////////////////////////////////////
				// import_conditions
				////////////////////////////////////////////////////////////////
				var p_import_conditions = jQuery( '#wpbm_ics_import_import_conditions' ).val().trim();	
				
				if ( p_import_conditions != '' ) {
					wpbm_shortcode += ' import_conditions=\'' + p_import_conditions + '\'';
				}
								
				////////////////////////////////////////////////////////////////
				// is_all_dates_in
				////////////////////////////////////////////////////////////////
				var p_is_all_dates_in = parseInt( jQuery( '#wpbm_import_is_all_dates_in' ).val().trim() );	
				
				if ( p_is_all_dates_in != 1 ) {
					wpbm_shortcode += ' is_all_dates_in=' + p_is_all_dates_in;
				}
								
				
			}
			return wpbm_shortcode;	
		}
	
	</script>
	<?php
}



// Replace:
// wpbm_			=> opl
// Booking Manager	=> Plugin Name
// In "wpbm_get_tiny_tabs" => define shortcodes
// Define func content wpbm_shortcode_ [ wpbm_ics_listing ]
