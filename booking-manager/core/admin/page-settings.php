<?php /**
 * @version 1.0
 * @package Booking Manager 
 * @category Content of Settings page 
 * @author wpdevelop
 *
 * @web-site https://oplugins.com/
 * @email info@oplugins.com 
 * 
 * @modified 2015-11-02
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


/** Show Content
 *  Update Content
 *  Define Slug
 *  Define where to show
 */
class WPBM_Page_SettingsGeneral extends WPBM_Page_Structure {
    
    private $settings_api = false;
     
    public function in_page() {
        
        return 'oplugins';// 'wpbm-settings';
    }        
    
    /** Get Settings API class - define, show, update "Fields".
     * 
     * @return object Settings API
     */    
    public function settings_api(){
        
        if ( $this->settings_api === false )             
             $this->settings_api = new WPBM_Settings_API_General(); 
        
        return $this->settings_api;
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
									, 'font_icon'		 => 'glyphicon glyphicon-cog'					// CSS definition  of forn Icon
									, 'default'			 => ! true										// Is this tab activated by default or not: true || false. 
								);

		$subtabs = array();
        
		/*
        $subtabs['wpbm-settings-listing'] = array(  'type' => 'goto-link'                               
                                                    , 'title' => __('Misc', 'booking-manager')            
                                                    , 'show_section' => 'wpbm_general_settings_wpbm_misc_metabox'
                                                );
        
        $subtabs['wpbm-settings-menu-access'] = array(  'type' => 'goto-link'                               
                                                    , 'title' => __('Plugin Menu', 'booking-manager')            
                                                    , 'show_section' => 'wpbm_general_settings_permissions_metabox'
                                                );
                
        $subtabs['wpbm-settings-uninstall'] = array(  'type' => 'goto-link'                               
                                                    , 'title' => __('Uninstall', 'booking-manager')            
                                                    , 'show_section' => 'wpbm_general_settings_uninstall_metabox'
                                                );
		
        $subtabs['wpbm-settings-advanced'] = array(  'type' => 'goto-link'                               
                                                    , 'title' => __('Advanced', 'booking-manager')            
                                                    , 'show_section' => 'wpbm_general_settings_advanced_metabox'
                                                );
        
                
        
        $subtabs['form-save'] = array( 
                                        'type' => 'button'                                  
                                        , 'title' => __('Save Changes', 'booking-manager')        
                                        , 'form' => 'wpbm_general_settings_form'                
                                    );
		*/
		
		$subtabs['general'] = array( 
                              'type' => 'subtab'                                  // Required| Possible values:  'subtab' | 'separator' | 'button' | 'goto-link' | 'html'
                            , 'title'		=> __('General Settings' , 'booking-manager')     // Title of TAB    
                            , 'page_title'	=> __('General Settings', 'booking-manager')		// Title of Page   
                            , 'hint'		=> __('General Settings' , 'booking-manager')		// Hint    
                            , 'link'		=> ''								// link
                            , 'position'	=> ''								// 'left'  ||  'right'  ||  ''
                            , 'css_classes' => ''                               // CSS class(es)
                            //, 'icon' => 'http://.../icon.png'                 // Icon - link to the real PNG img
                            //, 'font_icon' => 'glyphicon glyphicon-envelope'   // CSS definition of Font Icon
                            , 'default'		=> true                             // Is this sub tab activated by default or not: true || false. 
                            , 'disabled'	=> false                            // Is this sub tab deactivated: true || false. 
                            , 'checkbox'	=> false                            // or definition array  for specific checkbox: array( 'checked' => true, 'name' => 'feature1_active_status' )   //, 'checkbox'  => array( 'checked' => $is_checked, 'name' => 'enabled_active_status' )
                            , 'content'		=> 'content'                        // Function to load as conten of this TAB
                        );
        
        $tabs[ 'wpbm-settings' ]['subtabs'] = $subtabs;		
        
        return $tabs;
    }


    public function content() {
                
        // Checking ////////////////////////////////////////////////////////////
        
        do_action( 'wpbm_hook_settings_page_header', array( 'page' => $this->in_page() ) );					// Define Notices Section and show some static messages, if needed.
                    
        $is_can = apply_wpbm_filter('recheck_version', true); if ( ! $is_can ) { ?><script type="text/javascript"> jQuery(document).ready(function(){ jQuery( '.wpdvlp-sub-tabs').remove(); }); </script><?php return; }
        
        
        // Init Settings API & Get Data from DB ////////////////////////////////
        $this->settings_api();                                                  // Define all fields and get values from DB
        
        // Submit  /////////////////////////////////////////////////////////////
        
        $submit_form_name = 'wpbm_general_settings_form';                       // Define form name
                
        if ( isset( $_POST['is_form_sbmitted_'. $submit_form_name ] ) ) {

            // Nonce checking    {Return false if invalid, 1 if generated between, 0-12 hours ago, 2 if generated between 12-24 hours ago. }
            $nonce_gen_time = check_admin_referer( 'wpbm_settings_page_' . $submit_form_name  );  // Its stop show anything on submiting, if its not refear to the original page

            // Save Changes 
            $this->update();
        }                
        //$wpbm_user_role_master   = get_wpbm_option( 'wpbm_user_role_master' );    // O L D   W A Y:   Get Fields Data
        
        
        // JavaScript: Tooltips, Popover, Datepick (js & css) //////////////////
        echo '<span class="wpdevelop">';
        wpbm_js_for_items_page();                                        
        echo '</span>';

              
        ?><div class="clear"></div><?php
		
		if ( 1 ) {
			// Scroll links ////////////////////////////////////////////////////////
			?>
			<div class="wpdvlp-sub-tabs" style="background:none;border:none;box-shadow: none;padding:0;"><span class="nav-tabs" style="text-align:right;">
				<a onclick="javascript:wpbm_scroll_to('#wpbm_general_settings_wpbm_misc_metabox' );"  href="javascript:void(0);" original-title="" class="nav-tab go-to-link"><span><?php 
					echo ucwords( __('Misc', 'booking-manager') ); ?></span></a>            
			</span>
			<span class="nav-tabs" style="text-align:right;">
				<a onclick="javascript:wpbm_scroll_to('#wpbm_general_settings_permissions_metabox' );"  href="javascript:void(0);" original-title="" class="nav-tab go-to-link"><span><?php 
					echo ucwords( __('Plugin Menu', 'booking-manager') ); ?></span></a>            
			</span><span class="nav-tabs" style="text-align:right;">
				<a onclick="javascript:wpbm_scroll_to('#wpbm_general_settings_uninstall_metabox' );"  href="javascript:void(0);" original-title="" class="nav-tab go-to-link"><span><?php 
					echo ucwords( __('Uninstall', 'booking-manager') ); ?></span></a>            
			</span><span class="nav-tabs" style="text-align:right;">
				<a onclick="javascript:wpbm_scroll_to('#wpbm_general_settings_advanced_metabox' );"  href="javascript:void(0);" original-title="" class="nav-tab go-to-link"><span><?php 
					echo ucwords( __('Advanced', 'booking-manager') ); ?></span></a>            
			</span></div>
			<?php
		}
		
        
        // Content  ////////////////////////////////////////////////////////////
        ?>
        <div class="clear" style="margin-bottom:10px;"></div>
        <span class="metabox-holder">
            <form  name="<?php echo $submit_form_name; ?>" id="<?php echo $submit_form_name; ?>" action="" method="post">
                <?php 
                   // N o n c e   field, and key for checking   S u b m i t 
                   wp_nonce_field( 'wpbm_settings_page_' . $submit_form_name );
                ?><input type="hidden" name="is_form_sbmitted_<?php echo $submit_form_name; ?>" id="is_form_sbmitted_<?php echo $submit_form_name; ?>" value="1" />

                <div class="wpbm_settings_row wpbm_settings_row_left" >

                    <?php // wpbm_open_meta_box_section( 'wpbm_general_settings_calendar', __('General', 'booking-manager') );  ?>

                    <?php // $this->settings_api()->show( 'general' ); ?>                                      
                    
                    <?php // wpbm_close_meta_box_section(); ?>
					
					
                    <?php wpbm_open_meta_box_section( 'wpbm_general_settings_wpbm_misc', __('Miscellaneous', 'booking-manager') );  ?>

                    <?php $this->settings_api()->show( 'wpbm_listing' ); ?>                                      
                    
                    <?php wpbm_close_meta_box_section(); ?>
                    
                     
                    <?php wpbm_open_meta_box_section( 'wpbm_general_settings_permissions', __('Plugin Menu', 'booking-manager') );  ?>

                    <?php $this->settings_api()->show( 'permissions' ); ?>                                      
                    
                    <?php wpbm_close_meta_box_section(); ?>                    

                    
                    <?php wpbm_open_meta_box_section( 'wpbm_general_settings_uninstall', __('Uninstall / deactivation', 'booking-manager') );  ?>

                    <?php $this->settings_api()->show( 'uninstall' ); ?>                                      
                    
                    <?php wpbm_close_meta_box_section(); ?>                    
                    
                </div>  
                <div class="wpbm_settings_row wpbm_settings_row_right">

                    <?php wpbm_open_meta_box_section( 'wpbm_general_settings_information', __('Information', 'booking-manager') );  ?>

                    <?php $this->settings_api()->show( 'information' ); ?>                                      
                    
                    <?php wpbm_close_meta_box_section(); ?>                    



                                        
                    <?php wpbm_open_meta_box_section( 'wpbm_general_settings_advanced', __('Advanced', 'booking-manager') );  ?>

                    <?php $this->settings_api()->show( 'advanced' ); ?>                                      
                    
                    <?php wpbm_close_meta_box_section(); ?>
                    
                </div>                
                <div class="clear"></div>
                <input type="submit" value="<?php _e('Save Changes', 'booking-manager'); ?>" class="button button-primary wpbm_submit_button" />  
            </form>
            <?php if ( ( isset( $_GET['system_info'] ) ) && ( $_GET['system_info'] == 'show' ) ) { ?>
                
                <div class="clear" style="height:30px;"></div>
                
                <?php wpbm_open_meta_box_section( 'wpbm_general_settings_system_info', 'System Info' );  ?>

                <?php wpbm_system_info(); ?>

                <?php wpbm_close_meta_box_section(); ?>                    

            <?php } ?>
            
        </span>
    <?php 

    
    
        do_action( 'wpbm_hook_settings_page_footer', 'general_settings' );
    
//debuge( 'Content <strong>' . basename(__FILE__ ) . '</strong> <span style="font-size:9px;">' . __FILE__  . '</span>');                  
    }


    public function update() {
//debuge($_POST);
        $validated_fields = $this->settings_api()->validate_post();             // Get Validated Settings fields in $_POST request.
        
        $validated_fields = apply_filters( 'wpbm_settings_validate_fields_before_saving', $validated_fields );   //Hook for validated fields.
//debuge($validated_fields);
        // Skip saving specific option, for example in Demo mode.
        // unset($validated_fields['wpbm_start_day_weeek']);

        $this->settings_api()->save_to_db( $validated_fields );                 // Save fields to DB
        wpbm_show_changes_saved_message();
        
//debuge( basename(__FILE__), 'UPDATE',  $_POST, $validated_fields);          
                
        // O L D   W A Y:   Saving Fields Data
        //      update_wpbm_option( 'wpbm_is_delete_if_deactive'
        //                       , WPBM_Settings_API::validate_checkbox_post('wpbm_is_delete_if_deactive') );  
        //      ( (isset( $_POST['wpbm_is_delete_if_deactive'] ))?'On':'Off') );

    }
}



//if ( $is_other_tab ) {  
//    
//    if (  ( ! isset( $_GET['tab'] ) ) || ( $_GET['tab'] == 'general' )  ) {     // If tab  was not selected or selected default,  then  redirect  it to the "form" tab.            
//        $_GET['tab'] = 'form';
//    }
//} else {
//    add_action('wpbm_menu_created', array( new WPBM_Page_SettingsGeneral() , '__construct') );    // Executed after creation of Menu
//}

add_action('wpbm_menu_created', array( new WPBM_Page_SettingsGeneral() , '__construct') );    // Executed after creation of Menu
 