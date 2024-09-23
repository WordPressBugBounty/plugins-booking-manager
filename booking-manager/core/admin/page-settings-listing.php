<?php /**
 * @version 1.0
 * @package Booking Manager 
 * @category Content of item Listing page
 * @author wpdevelop
 *
 * @web-site https://oplugins.com/
 * @email info@oplugins.com 
 * 
 * @modified 2015-11-13
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


/** Show Content
 *  Update Content
 *  Define Slug
 *  Define where to show
 */
class WPBM_Page_SettingsListing extends WPBM_Page_Structure {
    
        
    public function in_page() {
        return 'oplugins';// 'wpbm-settings';
    }

    
    public function tabs() {
        
        $tabs = array();
        $tabs[ 'wpbm-settings' ] = array(
									  'title'			 => __( 'Settings', 'booking-manager' )				// Title of TAB    
									, 'page_title'		 => __( 'Settings', 'booking-manager' )		// Title of Page    
									, 'hint'			 => __( 'Settings', 'booking-manager' )		// Hint    
									, 'link'			 => ''											// Can be skiped,  then generated link based on Page and Tab tags. Or can  be extenral link
									, 'position'		 => ''											// 'left'  ||  'right'  ||  ''
									, 'css_classes'		 => ''											// CSS class(es)
									, 'icon'			 => ''											// Icon - link to the real PNG img
									, 'font_icon'		 => 'glyphicon glyphicon-menu-hamburger'		// CSS definition  of forn Icon
									, 'default'		=> false											// Is this tab activated by default or not: true || false. 
									, 'disabled'	=> false											// Is this tab disbaled: true || false. 
									, 'hided'		=> false											// Is this tab hided: true || false. 
									, 'subtabs'		=> array()            
        );        
        // $subtabs = array();                
        // $tabs[ 'items' ][ 'subtabs' ] = $subtabs;     
		
     $subtabs['listing'] = array( 
                              'type' => 'subtab'                                  // Required| Possible values:  'subtab' | 'separator' | 'button' | 'goto-link' | 'html'
                            , 'title'		=>   __('Listing Template' , 'booking-manager')	// Title of TAB    
                            , 'page_title'	=> __('Listing Template', 'booking-manager')		// Title of Page   
                            , 'hint'		=> __('Listing Template' , 'booking-manager')		// Hint    
                            , 'link' => ''                                      // link
                            , 'position' => ''                                  // 'left'  ||  'right'  ||  ''
                            , 'css_classes' => ''                               // CSS class(es)
                            //, 'icon' => 'http://.../icon.png'                 // Icon - link to the real PNG img
                            //, 'font_icon' => 'glyphicon glyphicon-envelope'   // CSS definition of Font Icon
                            , 'default' =>  !true                                // Is this sub tab activated by default or not: true || false. 
                            , 'disabled' => false                               // Is this sub tab deactivated: true || false. 
                            , 'checkbox'  => false                              // or definition array  for specific checkbox: array( 'checked' => true, 'name' => 'feature1_active_status' )   //, 'checkbox'  => array( 'checked' => $is_checked, 'name' => 'enabled_active_status' )
                            , 'content' => 'content'                            // Function to load as conten of this TAB
                        );
        
        $tabs[ 'wpbm-settings' ]['subtabs'] = $subtabs;		
        return $tabs;        
    }

	    
    public function content() {
        
        do_action( 'wpbm_hook_settings_page_header', array( 'page' => $this->in_page() ) );								// Define Notices Section and show some static messages, if needed.
		
		//////////////////////////////////////////////////////////////////////// 
        // Submit  /////////////////////////////////////////////////////////////
        
        $submit_form_name = 'wpbm_form_listing';                             // Define form name
                
        if ( isset( $_POST['is_form_sbmitted_'. $submit_form_name ] ) ) {

            // Nonce checking    {Return false if invalid, 1 if generated between, 0-12 hours ago, 2 if generated between 12-24 hours ago. }
            $nonce_gen_time = check_admin_referer( 'wpbm_settings_page_' . $submit_form_name  );  // Its stop show anything on submiting, if its not refear to the original page

            // Save Changes 
            $this->update();
        }                

        ////////////////////////////////////////////////////////////////////////
        // Get Data from DB ////////////////////////////////////////////////////                
        $data_list_tmpl       =  get_wpbm_option( 'wpbm_listing_template' );                 
        //$data_list_tmpl      = wpbm_nl_after_br( $data_list_tmpl );
        
         
        ////////////////////////////////////////////////////////////////////////
        // Toolbar /////////////////////////////////////////////////////////////
        wpbm_bs_toolbar_sub_html_container_start();

        ?><span class="wpdevelop"><div class="visibility_container clearfix-height" style="display:block;"><?php

            wpbm_js_for_items_page();                                            // JavaScript functions
			
            $this->toolbar_reset_to_default();                                // Reset to Default Forms
            
            $save_button = array( 'title' => __('Save Changes', 'booking-manager'), 'form' => $submit_form_name );
            $this->toolbar_save_button( $save_button );                         // Save Button 
            
        ?></div></span><?php
        
        wpbm_bs_toolbar_sub_html_container_end();
        
        ?><div class="clear"></div><?php
		
		if ( 0 ) {
			// Scroll links ////////////////////////////////////////////////////////
			?>
			<div class="wpdvlp-sub-tabs" style="background:none;border:none;box-shadow: none;padding:0;"><span class="nav-tabs" style="text-align:right;">
				<a href="javascript:void(0);" onclick="javascript:wpbm_scroll_to('#wpbm_settings_listing_metabox' );" original-title="" class="nav-tab go-to-link"><span><?php echo ucwords( __('Listing', 'booking-manager') ); ?></span></a>            
			</span></div>
			<?php
		}
		
        ////////////////////////////////////////////////////////////////////////
        // Content  ////////////////////////////////////////////////////////////
        ?>
        <div class="clear" style="margin-bottom:10px;"></div>
        <span class="metabox-holder">
            <form  name="<?php echo $submit_form_name; ?>" id="<?php echo $submit_form_name; ?>" action="" method="post">
                <?php 
                   // N o n c e   field, and key for checking   S u b m i t 
                   wp_nonce_field( 'wpbm_settings_page_' . $submit_form_name );
                ?><input type="hidden" name="is_form_sbmitted_<?php echo $submit_form_name; ?>" id="is_form_sbmitted_<?php echo $submit_form_name; ?>" value="1" /><?php 
                
                ?><input type="hidden" name="reset_to_default_form" id="reset_to_default_form" value="" /><?php 
                     
                ?><div class="wpbm_settings_row wpbm_settings_row_left"><?php
                
                    wpbm_open_meta_box_section( 'wpbm_settings_listing', __('Event template - row for event listing', 'booking-manager') );
					
						$this->show_listing_template( $data_list_tmpl );   
						
                    wpbm_close_meta_box_section();
                ?>
                </div>  
                <div class="wpbm_settings_row wpbm_settings_row_right"><?php                
                
                    wpbm_open_meta_box_section( 'wpbm_settings_listing_help', __('Help', 'booking-manager') );
					
						$this->show_content_data_form_help();         
						
                    wpbm_close_meta_box_section();
                ?>
                </div>
                <div class="clear"></div>
                <input type="submit" value="<?php _e('Save Changes','booking-manager'); ?>" class="button button-primary wpbm_submit_button" />  
            </form>
        </span>
        <?php       
						
		$this->css();
		$this->js();
		
        do_action( 'wpbm_hook_settings_page_footer', 'listing_template' );
		
	}

    
    /** Save Chanages */  
    public function update() {
            
		// We can  not use here such code:
		// WPBM_Settings_API::validate_textarea_post_static( 'listing_template' );
		// becuse its will  remove also JavaScript,  which  possible to  use for wizard form  or in some other cases.
		$data_list_tmpl =  trim( stripslashes( $_POST['listing_template'] ) );
		update_wpbm_option(   'wpbm_listing_template' , $data_list_tmpl );

		wpbm_show_changes_saved_message();        
    }

        
    // <editor-fold     defaultstate="collapsed"                        desc=" CSS  /  JS  "  >
    
    /** CSS for this page */
    private function css() {
        ?>
        <style type="text/css">  
            .wpbm-help-message {
                border:none;
            }
            /* toolbar fix */
            .wpdevelop .visibility_container .control-group {
                margin: 0 8px 5px 0;
            }
            /* Selectbox element in toolbar */
            .visibility_container select optgroup{                            
                color:#999;
                vertical-align: middle;
                font-style: italic;
                font-weight: 400;
            }
            .visibility_container select option {
                padding:5px;
                font-weight: 600;
            }
            .visibility_container select optgroup option{
                padding: 5px 20px;       
                color:#555;
                font-weight: 600;
            }
            @media (max-width: 399px) {
                #wpbm_create_new_custom_form_name_fields {
                    width: 100%;
                }                
            }
			/* iPad mini and all iPhones  and other Mobile Devices */
			@media (max-width: 782px) { 
				.wpbm_page .wpbm_send_button {                															
					padding: 2px;										
					margin-top: 1px;
				}
			}
        </style>
        <?php
    }
    
    
    public function js() {
        ?>
        <script type="text/javascript">
			
			/** Reset form to default template
			 * 
			 * @param string template_name
			 */
			function wpbm_reset_from_to_template( template_name ) {
				
				var editor_textarea_id = 'listing_template';
				
				var editor_textarea_content = wpbm_get_standard_template( template_name );

				if( typeof tinymce != "undefined" ) {
					var editor = tinymce.get( editor_textarea_id );
					if( editor && editor instanceof tinymce.Editor ) {
						editor.setContent( editor_textarea_content );
						editor.save( { no_events: true } );
					} else {
						jQuery( '#' + editor_textarea_id ).val( editor_textarea_content );
					}
				} else {
					jQuery( '#' + editor_textarea_id ).val( editor_textarea_content );
				}
			}

			/** Get Standard Template
			 * 
			 * @param string form_type
			 * @returns string
			 */
			function wpbm_get_standard_template( template_name ) {
				
				var form_content = '';

				if ( ( 'standard' == template_name ) ){
					   form_content = '';
					   form_content +='<div class="wpbm-event"> \n'; 
					   form_content +='     <div style="width:35%;float:left;margin-right:5%;">[DATES]</div> \n';
					   form_content +='     <div style="width:60%;float:left;"> \n';
					   form_content +='          <h2>[SUMMARY]</h2> <span style="display:none;">[UID]</span> \n';
					   form_content +='          <p class="desciption">[DESCRIPTION]</p> \n';
					   form_content +='     </div> \n';
					   form_content +='     <div style="clear:both;"></div> \n';
					   form_content +='</div> \n';        
					   form_content +='<hr />';        
				}
				return form_content;
			}

        </script>
        <?php
    }
    
	
	
    // </editor-fold>
    
    
    // <editor-fold     defaultstate="collapsed"                        desc=" Toolbar "  >
    
    /** Show Save button  in toolbar  for saving form */
    private function toolbar_save_button( $save_button ) {
                
        ?>
        <div class="clear-for-mobile"></div><input 
                                type="button" 
                                class="button button-primary wpbm_submit_button" 
                                value="<?php echo $save_button['title']; ?>" 
                                onclick="if (typeof document.forms['<?php echo $save_button['form']; ?>'] !== 'undefined'){ 
                                            document.forms['<?php echo $save_button['form']; ?>'].submit(); 
                                         } else { 
                                             wpbm_admin_show_message( '<?php echo  ' <strong>Error!</strong> Form <strong>' , $save_button['form'] , '</strong> does not exist.'; ?>.', 'error', 10000 );   //FixIn: 7.0.1.56
                                         }" 
                                />
        <?php
    }
    
    
    /** Selection  of default Template and Button for Reseting  */
    private function toolbar_reset_to_default() {
                
        $templates = array();
        
        $templates['selector_hint'] = array(  
                                                'title' => __('Select', 'booking-manager') . ' ' .  __('Form Template', 'booking-manager')
                                                , 'id' => ''   
                                                , 'name' => ''  
                                                , 'style' => 'font-weight: 400;border-bottom:1px dashed #ccc;'    
                                                , 'class' => ''     
                                                , 'disabled' => false
                                                , 'selected' => false
                                                , 'attr' => array()   
                                            );       
        
        $templates[ 'optgroup_sf_s' ] = array( 
                                                'optgroup' => true
                                                , 'close'  => false
                                                , 'title'  => '&nbsp;' . __('Standard Templates' ,'booking-manager') 
                                            );
        $templates[ 'standard' ] = array(  
                                                'title' => __('Standard', 'booking-manager')
                                                , 'id' => ''   
                                                , 'name' => ''  
                                                , 'style' => ''
                                                , 'class' => ''     
                                                , 'disabled' => false
                                                , 'selected' => false
                                                , 'attr' => array()   
                                            );        
		/*
        $templates[ '2collumns' ] = array(  
                                                'title' => '2 ' . __('Columns', 'booking-manager')
                                                , 'id' => ''   
                                                , 'name' => ''  
                                                , 'style' => ''
                                                , 'class' => ''     
                                                , 'disabled' => false
                                                , 'selected' => false
                                                , 'attr' => array()   
                                            );
		*/
        $templates[ 'optgroup_sf_e' ] = array( 'optgroup' => true, 'close'  => true );
                                        
		/*
        $templates[ 'optgroup_af_s' ] = array(  
                                                'optgroup' => true
                                                , 'close'  => false
                                                , 'title'  => '&nbsp;' . __('Advanced Templates' ,'booking-manager') 
                                            );
        $templates[ 'wizard' ] = array(  
                                                'title' => __('Wizard (several steps)', 'booking-manager')
                                                , 'id' => ''   
                                                , 'name' => ''  
                                                , 'style' => ''
                                                , 'class' => ''     
                                                , 'disabled' => false
                                                , 'selected' => false
                                                , 'attr' => array()   
                                            );

		
        $templates[ 'optgroup_af_e' ] = array( 'optgroup' => true, 'close'  => true );
		*/
                                                                
        $params = array(  
                          'label_for' => 'select_form_help_shortcode'           // "For" parameter  of label element
                        , 'label' => '' //__('Add New Field', 'booking-manager')        // Label above the input group
                        , 'style' => ''                                         // CSS Style of entire div element
                        , 'items' => array(
                                array(      
                                    'type' => 'addon' 
                                    , 'element' => 'text'           // text | radio | checkbox
                                    , 'text' => __('Reset Form', 'booking-manager') . ':'
                                    , 'class' => ''                 // Any CSS class here
                                    , 'style' => 'font-weight:600;' // CSS Style of entire div element
                                )  
                                // Warning! Can be text or selectbox, not both  OR you need to define width                     
                                , array(                                            
                                      'type' => 'select'                              
                                    , 'id' => 'select_default_form_template'  
                                    , 'name' => 'select_default_form_template'  
                                    , 'style' => ''                            
                                    , 'class' => ''   
                                    , 'multiple' => false
                                    , 'disabled' => false
                                    , 'disabled_options' => array()             // If some options disbaled,  then its must list  here                                
                                    , 'attr' => array()                         // Any  additional attributes, if this radio | checkbox element                                                   
                                    , 'options' => $templates                   // Associated array  of titles and values                                                       
                                    , 'value' => ''                             // Some Value from optins array that selected by default                                                                              
                                    , 'onfocus' => ''
                                    //, 'onchange' => "wpbm_show_fields_generator( this.options[this.selectedIndex].value );"
                                )              
                        )
                    );

            
            
            
        ?><div class="control-group wpbm-no-padding"><?php 
                wpbm_bs_input_group( $params );                   
        ?></div><?php
        
        
        $params = array(  
                      'label_for' => 'min_cost'                             // "For" parameter  of label element
                    , 'label' => '' //__('Add New Field', 'booking-manager')        // Label above the input group
                    , 'style' => ''                                         // CSS Style of entire div element
                    , 'items' => array(     
                                        array( 
                                            'type' => 'button'
                                            , 'title' => __('Reset', 'booking-manager')  // __('Reset', 'booking-manager')
                                            , 'hint' => array( 'title' => __('Reset Form' ,'booking-manager') , 'position' => 'top' )
                                            , 'class' => 'button tooltip_top' 
                                            , 'font_icon' => 'glyphicon glyphicon-repeat'
                                            , 'icon_position' => 'right'
                                            , 'action' => " var sel_res_val = document.getElementById('select_default_form_template').options[ document.getElementById('select_default_form_template').selectedIndex ].value;"
                                                        . " if   ( sel_res_val == 'selector_hint') { "
                                                        . "    wpbm_field_highlight( '#select_default_form_template' ); return;"          //. "  jQuery('#wpbm_form_field').trigger( 'submit' );"
                                                        . " }"  
                                                        //. " if ( wpbm_are_you_sure('" . esc_js(__('Do you really want to do this ?' ,'booking-manager')) . "') ) {"
                                                        . "    wpbm_reset_from_to_template( sel_res_val ); "          //. "  jQuery('#wpbm_form_field').trigger( 'submit' );"
                                                        //. " }"  
                                        )                            
                            )
                    );

        ?><div class="control-group wpbm-no-padding"><?php 
                wpbm_bs_input_group( $params );                   
        ?></div><?php
        
    }    
    
    // </editor-fold>
    
    
    // <editor-fold     defaultstate="collapsed"                        desc=" C O N T E N T   F o r m s "  >

    
    /** Show Booking Form  - in Settings page */
    private function show_listing_template( $data ) {
        
        wp_editor( $data, 
           'listing_template',  
           array(
                 'wpautop'       => false
               , 'media_buttons' => false
               , 'textarea_name' => 'listing_template'
               , 'textarea_rows' => 10
               , 'tinymce' => !false                                 // Remove Visual Mode from the Editor        
			   , 'default_editor'   => 'html'                    // 'tinymce' | 'html'     // 'html' is used for the "Text" editor tab.
               , 'editor_class'  => 'wpbm-textarea-tinymce'         // Any extra CSS Classes to append to the Editor textarea 
               , 'teeny' => true                                    // Whether to output the minimal editor configuration used in PressThis 
               , 'drag_drop_upload' => false                        // Enable Drag & Drop Upload Support (since WordPress 3.9) 			   
               )
         );                     
        //echo '<textarea id="listing_template" name="listing_template" class="darker-border" style="width:100%;" rows="33">' . htmlspecialchars($listing_template, ENT_NOQUOTES ) . '</textarea>';
        ?><div class="clear"></div><?php
    }
    
	
    
    /** Show Help section for Content Fields Data Form  - in Settings page */
    private function show_content_data_form_help() {
        
        ?>
        <div  class="wpbm-help-message">
            <span class="description"><strong><?php printf(__('Use these shortcodes for customization: ' ,'booking-manager'));?></strong></span><br/>
            <?php  
			?><span class="description"><?php 
				printf( __( '%s - date(s) of event', 'booking-manager' )		, '<code>[DATES]</code>' );
			?></span><br/>	
			<span class="description"><?php 
				printf( __( '%s - summary info of event', 'booking-manager' )	, '<code>[SUMMARY]</code>' );
			?></span><br/>	
			<span class="description"><?php 
				printf( __( '%s - ID of event', 'booking-manager' )			, '<code>[UID]</code>' );
			?></span><br/>	
			<span class="description"><?php 
				printf( __( '%s - description of event', 'booking-manager' )	, '<code>[DESCRIPTION]</code>' );
			?></span><br/>	
			<span class="description"><?php 
				printf( __( '%s - date of event editing, creation', 'booking-manager' ), '<code>[MODIFIED]</code>' );
			?></span><br/>	
			<span class="description"><?php 
				printf( __( '%s - location of event', 'booking-manager' )		, '<code>[LOCATION]</code>' );
			?></span><br/>
			<hr/>
			<span class="description"><?php //FixIn: 2.0.11.4
				printf( __( '%s - ID of booking (in Booking Calendar plugin), if event has been imported', 'booking-manager' )		, '<code>[BOOKING_ID]</code>' );
			?></span><br/>
			<span class="description"><?php //FixIn: 2.0.11.5
				printf( __( '%s - link to booking (in Booking Calendar plugin), if event has been imported', 'booking-manager' )		, '<code>[BOOKING_LINK]</code>' );
			?></span><br/>
			<span class="description"><?php //FixIn: 2.0.11.5
				echo '<strong>' . __('Example' ,'booking-manager') . ':</strong><br/>';
				echo '<em>' . '&lt;a href="[BOOKING_LINK]" target="_blank"&gt;[BOOKING_ID]&lt;/a&gt;' . '</em>'
			?></span><br/>

			<hr/>
            <span class="description"><?php printf(__('%s - inserting new line' ,'booking-manager'),'<code>&lt;br/&gt;</code>');?></span><br/>
            <span class="description">
                <?php
                echo '<strong>' . __('HTML' ,'booking-manager') . '.</strong> ' 
                     . sprintf(__('You can use any %sHTML tags%s in the booking form. Please use the HTML tags carefully. Be sure, that all "open" tags (like %s) are closed (like this %s).' ,'booking-manager')
                                   ,'<strong>','</strong>'
                                   ,'<code>&lt;div&gt;</code>'
                                   ,'<code>&lt;/div&gt;</code>'
                                );
                ?>
            </span>
        </div>        
        <?php 
        //echo '<hr />';    
        
    }
    
    // </editor-fold>
    
	
}
add_action('wpbm_menu_created', array( new WPBM_Page_SettingsListing() , '__construct') );    // Executed after creation of Menu

