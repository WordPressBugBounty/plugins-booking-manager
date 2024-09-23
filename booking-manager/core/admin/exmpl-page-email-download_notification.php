<?php
/**
 * @version 1.0
 * @package Content
 * @category Menu
 * @author wpdevelop
 *
 * @web-site https://oplugins.com/
 * @email info@oplugins.com 
 * 
 * @modified 2015-04-09
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly

/** Replace:
 1.  DownloadNotification  -> DownloadNotification
 2.  DOWNLOAD_NOTIFICATION -> DOWNLOAD_NOTIFICATION
 3.  download_notification -> download_notification
 4.  Check in api-emails.php 'db_prefix_option' => '...' option,  have to be the same as WPBM_EMAIL_DOWNLOAD_NOTIFICATION_PREFIX here
 5.  Configure Fields in init_settings_fields.
 */
                                                                                            
if ( ! defined( 'WPBM_EMAIL_DOWNLOAD_NOTIFICATION_PREFIX' ) )   define( 'WPBM_EMAIL_DOWNLOAD_NOTIFICATION_PREFIX',  'wpbm_email_' ); // Its defined in api-emails.php file & its same for all emails, here its used only for easy coding...

if ( ! defined( 'WPBM_EMAIL_DOWNLOAD_NOTIFICATION_ID' ) )       define( 'WPBM_EMAIL_DOWNLOAD_NOTIFICATION_ID',      'download_notification' );      /* Define Name of Email Template.   
                                                                                                                   Note. Prefix "wpbm_email_" defined in api-emails.php file. 
                                                                                                                   Full name of option is - "wpbm_email_download_notification"
                                                                                                                   Other email templates names:
                                                                                                                                            - 'download_notification'       - send email with download link to user
                                                                                                                                            - 'link_admin'      - send copy of email to admin with download link
                                                                                                                                            - 'download_admin'  - send email  about downloads happend    
                                                                                                                */

require_once( WPBM_PLUGIN_DIR . '/core/any/api-emails.php' );           // API


/** Email   F i e l d s  */
class WPBM_Emails_API_DownloadNotification extends WPBM_Emails_API  {                       // O v e r r i d i n g     "WPBM_Emails_API"     ClASS
    
    /**  Overrided functions - define Email Fields & Values  */
    public function init_settings_fields() {
        
        $this->fields = array();

        $this->fields['enabled'] = array(   
                                      'type'        => 'checkbox'
                                    , 'default'     => 'On'            
                                    , 'title'       => __('Enable / Disable', 'booking-manager')
                                    , 'label'       => __('Enable this email notification', 'booking-manager')   
                                    , 'description' => ''
                                    , 'group'       => 'general'

                                );
		/*
        $this->fields['copy_to_admin'] = array(   
                                      'type'        => 'checkbox'
                                    , 'default'     => 'On'            
                                    , 'title'       => __('Copy to admin', 'booking-manager')
                                    , 'label'       => __('Enable / disable sending copy of this email notification to admin', 'booking-manager')
                                    , 'description' => ''
                                    , 'group'       => 'general'

                                );
        */
        $this->fields['enabled_hr'] = array( 'type' => 'hr' );            

        $this->fields['to_html_prefix'] = array(   
                                    'type'          => 'pure_html'
                                    , 'group'       => 'general'
                                    , 'html'        => '<tr valign="top">
                                                        <th scope="row">
                                                            <label class="wpbm-form-email" for="' 
                                                                             . esc_attr( 'download_notification_to' ) 
                                                            . '">' . wp_kses_post(  __('To' , 'booking-manager') ) 
                                                            . '</label>
                                                        </th>
                                                        <td><fieldset style="float:left;width:50%;margin-right:5%;">'
                                );        
        $this->fields['to'] = array(  
                                      'type'        => 'text'               // We are using here 'text'  and not 'email',  for ability to  save several comma seperated emails.
                                    , 'default'     => get_option( 'admin_email' )
                                    //, 'placeholder' => ''
                                    , 'title'       => '' 
                                    , 'description' => __('Email Address', 'booking-manager') . '. ' . __('Required', 'booking-manager') . '.'
                                    , 'description_tag' => ''
                                    , 'css'         => 'width:100%'
                                    , 'group'       => 'general'
                                    , 'tr_class'    => ''
                                    , 'only_field'  => true
                                    , 'validate_as' => array( 'required' )
                                );            
        $this->fields['to_html_middle'] = array(   
                                    'type'          => 'pure_html'
                                    , 'group'       => 'general'
                                    , 'html'        => '</fieldset><fieldset style="float:left;width:45%;">'
                                );  
		
		$user_info = array( 'name' => '' );
		if ( is_user_logged_in() ) {			
			$user_data         = get_userdata( get_current_user_id() );
			$user_info['name'] = ( $user_data ) ? $user_data->display_name : '';
		}

        $this->fields['to_name'] = array(  
                                      'type'        => 'text'
                                    , 'default'     => $user_info['name']
                                    //, 'placeholder' => ''
                                    , 'title'       => '' 
                                    , 'description' => __('Title', 'booking-manager') . '  (' . __('optional', 'booking-manager') . ').' //. ' ' . __('If empty then title defined as WordPress', 'booking-manager') 
                                    , 'description_tag' => ''
                                    , 'css'         => 'width:100%'
                                    , 'group'       => 'general'
                                    , 'tr_class'    => ''
                                    , 'only_field' => true
                                );
        $this->fields['to_html_sufix'] = array(   
                                'type'          => 'pure_html'
                                , 'group'       => 'general'
                                , 'html'        => '    </fieldset>
                                                        </td>
                                                    </tr>'            
                        );        



        $this->fields['from_html_prefix'] = array(   
                                    'type'          => 'pure_html'
                                    , 'group'       => 'general'
                                    , 'html'        => '<tr valign="top">
                                                        <th scope="row">
                                                            <label class="wpbm-form-email" for="' 
                                                                             . esc_attr( 'download_notification_from' ) 
                                                            . '">' . wp_kses_post(  __('From' , 'booking-manager') ) 
                                                            . '</label>
                                                        </th>
                                                        <td><fieldset style="float:left;width:50%;margin-right:5%;">'
                                );        
        $this->fields['from'] = array(  
                                      'type'        => 'email'              // Its can  be only 1 email,  so check  it as Email  field.
                                    , 'default'     => get_option( 'admin_email' )
                                    //, 'placeholder' => ''
                                    , 'title'       => ''
                                    , 'description' => __('Email Address', 'booking-manager') . '. ' . __('Required', 'booking-manager') . '.' 
                                    , 'description_tag' => ''
                                    , 'css'         => 'width:100%'
                                    , 'group'       => 'general'
                                    , 'tr_class'    => ''
                                    , 'only_field' => true
                                    , 'validate_as' => array( 'required' )
                                );            
        $this->fields['from_html_middle'] = array(   
                                    'type'          => 'pure_html'
                                    , 'group'       => 'general'
                                    , 'html'        => '</fieldset><fieldset style="float:left;width:45%;">'
                                );                
        $this->fields['from_name'] = array(  
                                      'type'        => 'text'
                                    , 'default'     => $user_info['name']
                                    //, 'placeholder' => ''
                                    , 'title'       => ''
                                    , 'description' => __('Title', 'booking-manager') . '  (' . __('optional', 'booking-manager') . ').' //. ' ' . __('If empty then title defined as WordPress', 'booking-manager') 
                                    , 'description_tag' => ''
                                    , 'css'         => 'width:100%'
                                    , 'group'       => 'general'
                                    , 'tr_class'    => ''
                                    , 'only_field' => true
                                );
        $this->fields['from_html_sufix'] = array(   
                                'type'          => 'pure_html'
                                , 'group'       => 'general'
                                , 'html'        => '    </fieldset>
                                                        </td>
                                                    </tr>'            
                        );                    

        $this->fields['from_hr'] = array( 'type' => 'hr' );            


        $this->fields['subject'] = array(   
                                      'type'        => 'text'
                                    , 'default'     => sprintf( __( 'Download notification of %s', 'booking-manager'), '[product_title] [ [product_id] ]' )
                                    //, 'placeholder' => ''
                                    , 'title'       => __('Subject', 'booking-manager')
                                    , 'description' => sprintf(__('Type your email %ssubject%s.' , 'booking-manager'),'<b>','</b>') . ' ' . __('Required', 'booking-manager') . '.'
                                    , 'description_tag' => ''
                                    , 'css'         => 'width:100%'
                                    , 'group'       => 'general'
                                    , 'tr_class'    => ''
                                    , 'validate_as' => array( 'required' )
                            );

        $blg_title = get_option( 'blogname' );
        $blg_title = str_replace( array( '"', "'" ), '', $blg_title );
        
        $this->fields['content'] = array(   
                                      'type'        => 'wp_textarea'
                                    , 'default'     => 
//													sprintf( __( 'Hello.%sThe %s have been downloaded at %s from IP: %s~ Download link of %s will expire in %s === User info ===%s User agent: %s IP: %s Request url: %s ======================%s === Product Summary ===%s %s ======================%s Thank you, %s', 'booking-manager' )
//														, '<br/><br/>'
//														, '[product_title] [ [product_id] ]'
//														, '[current_date] [current_time]'
//														, '[remote_ip]' . '<br/><br/>'
//														, '[product_filename]  ([product_size])'
//														, '[product_expire_after] - [product_expire_date]' . '<br/><br/>'
//														,  '<br/>'
//														, '[user_agent]' . '<br/>', '[remote_ip]' . '<br/>', '[request_url]' . '<br/>'
//														,  '<br/><br/>'
//														,  '<br/>'
//														, '[product_summary]<br/>[product_description]<br/>[product_link]<br/>'
//														,  '<br/><br/>'
//														, $blg_title . '<br/>' . '[siteurl]'									
//														)
													sprintf( __( 'Hi. %s The %s have been downloaded. %s ===== User Info ===== %s To: %s IP:  %s User agent: %s Request url: %s ====================== %s ===== Product Summary ===== %s Expire at %s ====================== %s', 'booking-manager' )
															, '<br/><br/>'
															, '<strong>[product_title] [product_version]</strong> [ID=[product_id]]'
															, '<br/><br/>'
															, '<br/>'
															, '[link_sent_to]' . '<br/>'
															, '[remote_ip]' . '<br/>'
															, '[user_agent]' . '<br/>'
															, '[request_url]' . '<br/>'
															, '<br/><br/>'
															, '<br/>[product_summary] <br/>[product_description] <br/>'
															, '<strong>[product_expire_date]</strong> '. '<br/>'
															, '<br/> <br/><strong>[current_date] [current_time]</strong>'
														)
                                    //, 'placeholder' => ''
                                    , 'title'       => __('Content', 'booking-manager')
                                    , 'description' => __('Type your email message content.', 'booking-manager') 
                                    , 'description_tag' => ''
                                    , 'css'         => ''
                                    , 'group'       => 'general'
                                    , 'tr_class'    => ''
                                    , 'rows'        => 10
                                    , 'show_in_2_cols' => true
                            );
//        $this->fields['content'] = htmlspecialchars( $this->fields['content'] );// Convert > to &gt;
//        $this->fields['content'] = html_entity_decode( $this->fields['content'] );// Convert &gt; to >
        


        ////////////////////////////////////////////////////////////////////
        // Style
        ////////////////////////////////////////////////////////////////////


        $this->fields['header_content'] = array(   
                                    'type' => 'textarea'
                                    , 'default' => ''
                                    , 'title' => __('Email Heading', 'booking-manager')
                                    , 'description'  => __('Enter main heading contained within the email notification.', 'booking-manager') 
                                    //, 'placeholder' => ''
                                    , 'rows'  => 2
                                    , 'css' => "width:100%;"
                                    , 'group' => 'parts'                        
                            );
        $this->fields['footer_content'] = array(   
                                    'type' => 'textarea'
                                    , 'default' => ''
                                    , 'title' => __('Email Footer Text', 'booking-manager')
                                    , 'description'  => __('Enter text contained within footer of the email notification', 'booking-manager') 
                                    //, 'placeholder' => ''
                                    , 'rows'  => 2
                                    , 'css' => 'width:100%;'
                                    , 'group' => 'parts'                        
                            );

        $this->fields['template_file'] = array(   
                                    'type' => 'select'
                                    , 'default' => 'plain'
                                    , 'title' => __('Email template', 'booking-manager')
                                    , 'description' => __('Choose email template.', 'booking-manager')  
                                    , 'description_tag' => 'span'
                                    , 'css' => ''
                                    , 'options' => array(
                                                            'plain'     => __('Plain (without styles)', 'booking-manager')  
                                                          , 'standard'  => __('Standard 1 column', 'booking-manager')                                                              
                                                    )      
                                    , 'group' => 'style'
                            );

        $this->fields['template_file_help'] = array(   
                                    'type' => 'help'                                        
                                    , 'value' => array( sprintf( __('You can override this email template in this folder %s', 'booking-manager')                                                
                                                                , '<code>' . realpath( dirname(__FILE__) . '/../any/emails_tpl/' ) . '</code>' ) 
                                                      )
                                    , 'cols' => 2
                                    , 'group' => 'style'
                            );

        $this->fields['base_color'] = array(   
                                    'type'      => 'color'
                                    , 'default'   => '#557da1'
                                    , 'title'   => __('Base Color', 'booking-manager')
                                    , 'description'  => __('The base color for email templates.', 'booking-manager') 
                                                        . ' ' . __('Default color', 'booking-manager') .': <code>#557da1</code>.'
                                    , 'group'   => 'style'
                                    , 'tr_class'    => 'template_colors'
                            );                
        $this->fields['background_color'] = array(   
                                    'type'      => 'color'
                                    , 'default'   => '#f5f5f5'
                                    , 'title'   => __('Background Color', 'booking-manager')
                                    , 'description' => __('The background color for email templates.', 'booking-manager') 
                                                       . ' ' . __('Default color', 'booking-manager') .': <code>#f5f5f5</code>.'
                                    , 'group'   => 'style'
                                    , 'tr_class'    => 'template_colors'
                            );
        $this->fields['body_color'] = array(   
                                    'type'      => 'color'
                                    , 'default'   => '#fdfdfd'
                                    , 'title'   => __('Email Body Background Color', 'booking-manager')
                                    , 'description' =>  __('The main body background color for email templates.', 'booking-manager') 
                                                        . ' ' . __('Default color', 'booking-manager') .': <code>#fdfdfd</code>.'
                                    , 'group'   => 'style'
                                    , 'tr_class'    => 'template_colors'
                            );
        $this->fields['text_color'] = array(   
                                    'type'      => 'color'
                                    , 'default'   => '#505050'
                                    , 'title'   => __('Email Body Text Colour', 'booking-manager')
                                    , 'description' =>  __('The main body text color for email templates.', 'booking-manager') 
                                                        . ' ' . __('Default color', 'booking-manager') .': <code>#505050</code>.'
                                    , 'group'   => 'style'
                                    , 'tr_class'    => 'template_colors'
                            );


        ////////////////////////////////////////////////////////////////////
        // Email format: Plain, HTML, MultiPart
        ////////////////////////////////////////////////////////////////////


        $this->fields['email_content_type'] = array(   
                                    'type' => 'select'
                                    , 'default' => 'plain'
                                    , 'title' => __('Email format', 'booking-manager')
                                    , 'description' => __('Choose which format of email to send.', 'booking-manager')  
                                    , 'description_tag' => 'p'
                                    , 'css' => 'width:100%;'
                                    , 'options' => array(
                                                            'plain' => __('Plain text', 'booking-manager')  
                                                        //  , 'html' => __('HTML', 'booking-manager')  
                                                        //  , 'multipart' => __('Multipart', 'booking-manager')  
                                                    )      
                                    , 'group' => 'email_content_type'
                            );
        if ( class_exists( 'DOMDocument' ) ) {
            $this->fields['email_content_type']['options']['html']        = __('HTML', 'booking-manager');
            $this->fields['email_content_type']['options']['multipart']   = __('Multipart', 'booking-manager');

            $this->fields['email_content_type']['default'] = 'html';
        }



        ////////////////////////////////////////////////////////////////////
        // Help
        ////////////////////////////////////////////////////////////////////

        $this->fields['content_help'] = array(   
                                    'type' => 'help'                                        
                                    , 'value' => array()
                                    , 'cols' => 2
                                    , 'group' => 'help'
                            );

        $skip_shortcodes = array(
                                'denyreason'
                              , 'paymentreason'
                              , 'visitorediturl'
                              , 'visitorcancelurl'
                              , 'visitorpayurl'
                          );
        $email_example = sprintf(__('For example: "You have a new reservation %s on the following date(s): %s Contact information: %s You can approve or cancel this item at: %s Thank you, Reservation service."' , 'booking-manager'),'','[dates]&lt;br/&gt;&lt;br/&gt;','&lt;br/&gt; [content]&lt;br/&gt;&lt;br/&gt;', htmlentities( ' <a href="[moderatelink]">'.__('here' , 'booking-manager').'</a> ') . '&lt;br/&gt;&lt;br/&gt; ');

//        $help_fields = wpbm_get_email_help_shortcodes( $skip_shortcodes, $email_example );
//
//        foreach ( $help_fields as $help_fields_key => $help_fields_value ) {
//            $this->fields['content_help']['value'][] = $help_fields_value;
//        }
            
    }    
        
}



/** Settings Emails   P a g e  */
class WPBM_Settings_Page_Email_DownloadNotification extends WPBM_Page_Structure {

    public $email_settings_api = false;
    
    
    /** Define interface for  Email API
     * 
     * @param string $selected_email_name - name of Email template
     * @param array $init_fields_values - array of init form  fields data
     * @return object Email API
     */
    public function mail_api( $selected_email_name ='',  $init_fields_values = array() ){
        
        if ( $this->email_settings_api === false ) {
            $this->email_settings_api = new WPBM_Emails_API_DownloadNotification( $selected_email_name , $init_fields_values );    
        }
        
        return $this->email_settings_api;
    }
    
    
    public function in_page() {                                                 // P a g e    t a g
        return 'wpbm-settings';
    }
    
    
    public function tabs() {                                                    // T a b s      A r r a y
        
        $tabs = array();
                
        $tabs[ 'email' ] = array(
                              'title'     => __( 'Emails', 'booking-manager')               // Title of TAB    
                            , 'page_title'=> __( 'Emails Settings', 'booking-manager')      // Title of Page    
                            , 'hint'      => __( 'Emails Settings', 'booking-manager')      // Hint                
                            //, 'link'      => ''                                   // Can be skiped,  then generated link based on Page and Tab tags. Or can  be extenral link
                            //, 'position'  => ''                                   // 'left'  ||  'right'  ||  ''
                            //, 'css_classes'=> ''                                  // CSS class(es)
                            //, 'icon'      => ''                                   // Icon - link to the real PNG img
                            , 'font_icon' => 'glyphicon glyphicon-envelope'         // CSS definition  of forn Icon
                            //, 'default'   => false                                // Is this tab activated by default or not: true || false. 
                            //, 'disabled'  => false                                // Is this tab disbaled: true || false. 
                            //, 'hided'     => false                                // Is this tab hided: true || false. 
                            , 'subtabs'   => array()   
                    );

        $subtabs = array();
        

        $is_data_exist = get_wpbm_option( WPBM_EMAIL_DOWNLOAD_NOTIFICATION_PREFIX . WPBM_EMAIL_DOWNLOAD_NOTIFICATION_ID );           // ''wpbm_email_' - defined in api-emails.php  file.
        if (  ( ! empty( $is_data_exist ) ) && ( isset( $is_data_exist['enabled'] ) ) && ( $is_data_exist['enabled'] == 'On' )  )     
            $icon = '<i class="menu_icon icon-1x glyphicon glyphicon-check"></i> &nbsp; ';
        else 
            $icon = '<i class="menu_icon icon-1x glyphicon glyphicon-unchecked"></i> &nbsp; ';
        
        $subtabs['download-notification'] = array( 
                            'type' => 'subtab'                                  // Required| Possible values:  'subtab' | 'separator' | 'button' | 'goto-link' | 'html'
                            , 'title' =>  $icon . __('Download Notification' , 'booking-manager')      // Title of TAB    
                            , 'page_title' => __('Emails Settings', 'booking-manager')  // Title of Page   
                            , 'hint' => __('Email about download notification, which is sending to admin' , 'booking-manager')   // Hint    
                            , 'link' => ''                                      // link
                            , 'position' => ''                                  // 'left'  ||  'right'  ||  ''
                            , 'css_classes' => ''                               // CSS class(es)
                            //, 'icon' => 'http://.../icon.png'                 // Icon - link to the real PNG img
                            //, 'font_icon' => 'glyphicon glyphicon-envelope'   // CSS definition of Font Icon
                            , 'default' =>  ! true                                // Is this sub tab activated by default or not: true || false. 
                            , 'disabled' => false                               // Is this sub tab deactivated: true || false. 
                            , 'checkbox'  => false                              // or definition array  for specific checkbox: array( 'checked' => true, 'name' => 'feature1_active_status' )   //, 'checkbox'  => array( 'checked' => $is_checked, 'name' => 'enabled_active_status' )
                            , 'content' => 'content'                            // Function to load as conten of this TAB
                        );
        
        $tabs[ 'email' ]['subtabs'] = $subtabs;
                        
        return $tabs;
    }
    
    
    /** Show Content of Settings page */
    public function content() {
//debuge( 'WPBM_EMAIL_DOWNLOAD_NOTIFICATION_PREFIX . WPBM_EMAIL_DOWNLOAD_NOTIFICATION_ID, get_wpbm_option( WPBM_EMAIL_DOWNLOAD_NOTIFICATION_PREFIX . WPBM_EMAIL_DOWNLOAD_NOTIFICATION_ID )', WPBM_EMAIL_DOWNLOAD_NOTIFICATION_PREFIX . WPBM_EMAIL_DOWNLOAD_NOTIFICATION_ID, get_wpbm_option( WPBM_EMAIL_DOWNLOAD_NOTIFICATION_PREFIX . WPBM_EMAIL_DOWNLOAD_NOTIFICATION_ID ) );

        $this->css();
        
        ////////////////////////////////////////////////////////////////////////
        // Checking 
        ////////////////////////////////////////////////////////////////////////
        
        do_action( 'wpbm_hook_settings_page_header', array( 'page' => $this->in_page(), 'subpage' => 'emails_settings' ) );	// Define Notices Section and show some static messages, if needed.
        

        
        ////////////////////////////////////////////////////////////////////////
        // Load Data 
        ////////////////////////////////////////////////////////////////////////
        
        /**             Its will  load DATA from DB,  during creattion mail_api CLASS
         *              during initial activation  of the API  its try  to get option  from DB
         *              We need to define this API before checking POST, to know all available fields
         *              Define Email Name & define field values from DB, if not exist, then default values. 
            Array ( 
                    [wpbm_email_download_notification] => Array
                                                (
                                                    [enabled] => On
                                                    [to] => beta@oplugins.com
                                                    [to_name] => Some Name
                                                    [from] => admin@oplugins.com
                                                    [from_name] => 
                                                    [subject] => New item
                                                    [content] => You need to approve  [shortcodetype] for:  ...
                                                    [header_content] => 
                                                    [footer_content] => 
                                                    [template_file] => plain
                                                    [base_color] => #557da1
                                                    [background_color] => #f5f5f5
                                                    [body_color] => #fdfdfd
                                                    [text_color] => #505050
                                                    [email_content_type] => html
                                                )
        )

        // $mail_api->save_to_db( $fields_values );
        */    
        $init_fields_values = array();

        $this->mail_api( WPBM_EMAIL_DOWNLOAD_NOTIFICATION_ID, $init_fields_values );
        
        
        ////////////////////////////////////////////////////////////////////////
        //  S u b m i t   Actions  -  S e n d   
        ////////////////////////////////////////////////////////////////////////
        
        $submit_form_name_action = 'wpbm_form_action';                                      // Define form name
        if ( isset( $_POST['is_form_sbmitted_'. $submit_form_name_action ] ) ) {

            // Nonce checking    {Return false if invalid, 1 if generated between, 0-12 hours ago, 2 if generated between 12-24 hours ago. }
            $nonce_gen_time = check_admin_referer( 'wpbm_settings_page_' . $submit_form_name_action );  // Its stop show anything on submiting, if its not refear to the original page

            // Save Changes 
            $this->update_actions();
        }                        
        ?>
        <form  name="<?php echo $submit_form_name_action; ?>" id="<?php echo $submit_form_name_action; ?>" action="" method="post" autocomplete="off">
           <?php 
              // N o n c e   field, and key for checking   S u b m i t 
              wp_nonce_field( 'wpbm_settings_page_' . $submit_form_name_action );
           ?><input type="hidden" name="is_form_sbmitted_<?php echo $submit_form_name_action; ?>" id="is_form_sbmitted_<?php echo $submit_form_name_action; ?>" value="1" />
             <input type="hidden" name="form_action" id="form_action" value="" />
        </form>
        <?php

        
        ////////////////////////////////////////////////////////////////////////
        //  S u b m i t   Main Form  
        ////////////////////////////////////////////////////////////////////////
        
        $submit_form_name = 'wpbm_emails_template';                             // Define form name
        
        $this->mail_api()->validated_form_id = $submit_form_name;               // Define ID of Form for ability to  validate fields before submit.
        
        if ( isset( $_POST['is_form_sbmitted_'. $submit_form_name ] ) ) {

            // Nonce checking    {Return false if invalid, 1 if generated between, 0-12 hours ago, 2 if generated between 12-24 hours ago. }
            $nonce_gen_time = check_admin_referer( 'wpbm_settings_page_' . $submit_form_name );  // Its stop show anything on submiting, if its not refear to the original page

            // Save Changes 
            $this->update();
        }                
        
        
        ////////////////////////////////////////////////////////////////////////
        // JavaScript: Tooltips, Popover, Datepick (js & css) 
        ////////////////////////////////////////////////////////////////////////
        
        echo '<span class="wpdevelop">';
        
        wpbm_js_for_items_page();                                        
        
        echo '</span>';

        
        ////////////////////////////////////////////////////////////////////////
        // Content
        ////////////////////////////////////////////////////////////////////////
        ?>         
        <div class="clear" style="margin-bottom:10px;"></div>                        
                
        <span class="metabox-holder">
            
            <form  name="<?php echo $submit_form_name; ?>" id="<?php echo $submit_form_name; ?>" action="" method="post" autocomplete="off">
                <?php 
                   // N o n c e   field, and key for checking   S u b m i t 
                   wp_nonce_field( 'wpbm_settings_page_' . $submit_form_name );
                ?><input type="hidden" name="is_form_sbmitted_<?php echo $submit_form_name; ?>" id="is_form_sbmitted_<?php echo $submit_form_name; ?>" value="1" />


                <div class="clear"></div>    
                <div class="metabox-holder">

                    <div class="wpbm_settings_row wpbm_settings_row_left" >
                    <?php 
                            
                        wpbm_open_meta_box_section( $submit_form_name . 'general', __('Email about download notification, which is sending to admin', 'booking-manager')   );
                            
                            $this->mail_api()->show( 'general' ); 
                            
                        wpbm_close_meta_box_section(); 
                            
                        
                        wpbm_open_meta_box_section( $submit_form_name . 'parts' , __('Header / Footer', 'booking-manager') ); 
                            
                            $this->mail_api()->show( 'parts' );
                        
                        wpbm_close_meta_box_section();
                            
                        
                        wpbm_open_meta_box_section( $submit_form_name . 'style' , __('Email Styles', 'booking-manager') ); 
                            
                            $this->mail_api()->show( 'style' );
                        
                        wpbm_close_meta_box_section();
                        
                    ?>    
                    </div>

                    <div class="wpbm_settings_row wpbm_settings_row_right">
                    <?php 
                    
                        wpbm_open_meta_box_section( $submit_form_name . 'actions', __('Actions', 'booking-manager') ); 

                            ?><a class="button button-secondary" style="margin:0 0 5px;" href="javascript:void(0)" 
                                 onclick="javascript: jQuery('#form_action').val('test_send'); jQuery('form#<?php echo $submit_form_name_action; ?>').trigger( 'submit' );"
                                ><?php _e('Send Test Email', 'booking-manager'); ?></a><?php  
                                
                            ?><input type="submit" value="<?php _e('Save Changes', 'booking-manager'); ?>" class="button button-primary right" style="margin:0 0 5px 5px;" /><?php 
                            
                            /* ?>
                            <a class="button button-secondary" href="javascript:void(0)" ><?php _e('Preview Email', 'booking-manager'); ?></a>
                            <hr />
                            <a  class="button button-secondary right"   
                                href="javascript:void(0)" 
                                onclick="javascript: if ( wpbm_are_you_sure('<?php echo esc_js(__('Do you really want to delete this item?', 'booking-manager')); ?>') ){ 
                                                         jQuery('#form_action').val('delete');
                                                         jQuery('form#<?php echo $submit_form_name_action; ?>').trigger( 'submit' );
                                                     }"
                                ><?php _e('Delete Email', 'booking-manager'); ?></a>
                             <?php */ 
                            
                            ?><div class="clear"></div><?php   
                        
                        wpbm_close_meta_box_section(); 
                        
                        wpbm_open_meta_box_section( $submit_form_name . 'email_content_type', __('Type', 'booking-manager') );
                            
                            $this->mail_api()->show( 'email_content_type' );
                            
                        wpbm_close_meta_box_section(); 
                        
                        
                        wpbm_open_meta_box_section( $submit_form_name . 'help', __('Help', 'booking-manager') );
                            
                            $this->mail_api()->show( 'help' );
                            
                        wpbm_close_meta_box_section(); 
                        
                    ?>
                    </div>
                    <div class="clear"></div>
                </div>
                
                <input type="submit" value="<?php _e('Save Changes', 'booking-manager'); ?>" class="button button-primary" />  
            </form>
        </span>
        <?php
        
        $this->enqueue_js();
    }
    
    
    /**
     * Update form  from Toolbar - create / delete/ load email templates
     * 
     * @return boolean
     */
    public function update_actions(  ) {
    
             
        if ( $_POST['form_action'] == 'test_send' ) {                           // Sending test  email
            
            /*
            $this->email_settings_api = false;    
            $selected_email_name = 'standard';    
            $email_fields = get_wpbm_option( 'wpbm_email_' . $selected_email_name );
            $this->mail_api( $selected_email_name, $email_fields );                
            */

            
            //$to = sanitize_email( $this->mail_api()->fields_values['to'] );
            $replace = array ();
			$replace[ 'product_id' ] = '<strong>99</strong>';
			$replace[ 'product_title' ] = '<strong>Product ZZZ</strong>';
			$replace[ 'product_version' ] = '<strong>1.0</strong>';
			$replace[ 'product_description' ] = 'Product ZZZ Info';
			$replace[ 'product_filename' ] = 'zzz_product.zip';
			$replace[ 'product_link' ] = home_url();
			$replace[ 'product_size' ] = '3 Mb';
			$replace[ 'product_expire_after' ] = '1 day';
			$replace[ 'product_expire_date' ] = date_i18n( get_wpbm_option( 'wpbm_date_format' ) . ' ' . get_wpbm_option( 'wpbm_time_format' ), strtotime( '+1 day' ) );
			$replace[ 'product_summary' ] = '<a href="">' . $replace[ 'product_filename' ] . '</a> (' . $replace[ 'product_size' ] . ')  ~ expire in ' . $replace[ 'product_expire_after' ];

			$replace[ 'link_sent_to' ] = $this->mail_api()->get_from__email_address();

			$replace[ 'siteurl' ] = htmlspecialchars_decode( '<a href="' . home_url() . '">' . home_url() . '</a>' );
			$replace[ 'remote_ip' ] = wpbm_get_user_ip();												// The IP address from which the user is viewing the current page. 
			$replace[ 'user_agent' ] = $_SERVER[ 'HTTP_USER_AGENT' ];									// Contents of the User-Agent: header from the current request, if there is one. 
			$replace[ 'request_url' ] = $_SERVER[ 'HTTP_REFERER' ];										// The address of the page (if any) where action was occured. Because we are sending it in Ajax request, we need to use the REFERER HTTP
			$replace[ 'current_date' ] = date_i18n( get_wpbm_option( 'wpbm_date_format' ) );
			$replace[ 'current_time' ] = date_i18n( get_wpbm_option( 'wpbm_time_format' ) );
        
            
            $to = $this->mail_api()->get_from__email_address();
            $to_name = $this->mail_api()->get_from__name();
            $to = trim(  $to_name ) . ' <' .  $to . '> ';
        
            $email_result = $this->mail_api()->send( $to , $replace );

            if ( $email_result ) 
                wpbm_show_message ( __('Email sent to ', 'booking-manager') . ' ' . $this->mail_api()->get_from__email_address() , 5 );             
            else 
                wpbm_show_message ( __('Email was not sent. An error occurred.', 'booking-manager'), 5 ,'error' );    
        }

        /*
        if ( $_POST['form_action'] == 'create' ) {                              // Create
            
            $email_title = esc_attr( $_POST['create_email_template'] );            
            $email_name = wpbm_get_slug_format_4_option_name( $email_title );
            
            $wpbm_email_tpl_names = get_wpbm_option( 'wpbm_email_tpl_names' );
            if ( empty( $wpbm_email_tpl_names ) )  $wpbm_email_tpl_names = array();
            
            
            if ( empty($email_name) || isset( $wpbm_email_tpl_names[ $email_name ] ) ) {      // Error
                wpbm_show_message ( __('Email template has not added.', 'booking-manager'), 5 , 'error' );  
                return false;                
            }
            
            $wpbm_email_tpl_names[ $email_name ]= stripslashes( $email_title );
            
            update_wpbm_option( 'wpbm_email_tpl_names', $wpbm_email_tpl_names );
            
            wpbm_show_message ( __('Email template added successfully', 'booking-manager'), 5 );                                               // Show Save message
            
            $redir = esc_url( add_query_arg( array('email_template' => $email_name ), html_entity_decode( $this->getUrl() ) ) );       
            
            wpbm_reload_page_by_js( $redir );
            
            return true;            
        }
        
        if ( $_POST['form_action'] == 'delete' ) {                              // Delete
            $email_name = esc_attr( $_POST['select_email_template'] );
            
            $wpbm_email_tpl_names = get_wpbm_option( 'wpbm_email_tpl_names' );
            if ( empty( $wpbm_email_tpl_names ) )  $wpbm_email_tpl_names = array();
            
            if ( ! isset( $wpbm_email_tpl_names[ $email_name ] ) ) {            // Error
                wpbm_show_message ( __('Email template does not exist.', 'booking-manager'), 5 , 'error' );  
                return false;                
            } 
            
            unset($wpbm_email_tpl_names[ $email_name ]);                        // Remove Email  name from list of email names
            update_wpbm_option( 'wpbm_email_tpl_names', $wpbm_email_tpl_names );
            
            delete_wpbm_option( 'wpbm_email_' . $email_name );                  // Delete Email Template
            
            wpbm_show_message ( __('Email template deleted successfully', 'booking-manager'), 5 );                                     // Show Save message
            
                        
            $redir = esc_url( remove_query_arg( array( 'email_template' ), html_entity_decode( $this->getUrl() ) ) );       // Load standard email template
            
            wpbm_reload_page_by_js( $redir );
            
            return true;            
            
        }
        
        if ( $_POST['form_action'] == 'load' ) {                                // Load
            $email_name = $_POST['select_email_template'];
            
            $wpbm_email_tpl_names = get_wpbm_option( 'wpbm_email_tpl_names' );
            if ( empty( $wpbm_email_tpl_names ) )  $wpbm_email_tpl_names = array();
            
            if ( ! isset( $wpbm_email_tpl_names[ $email_name ] ) ) {             // Error
                wpbm_show_message ( __('Email template does not exist.', 'booking-manager'), 5 , 'error' );  
                return false;                
            }
            
        }
        */
    }
    
    
    /** Update Email template to DB */
    public function update() {

        // Get Validated Email fields
        $validated_fields = $this->mail_api()->validate_post();
        
//debuge($validated_fields);        
		// Remove <p> at begining and </p> at END of email template.
		if (
				( substr( $validated_fields['content'], 0, 3) === '<p>' ) 
			&&  ( substr( $validated_fields['content'], -4 ) === '</p>' ) 
			) {
			$validated_fields['content'] = substr ( $validated_fields['content'], 3, ( strlen ( $validated_fields['content'] ) - 7 ) );
		}
        
        $this->mail_api()->save_to_db( $validated_fields );
                
        wpbm_show_message ( __('Settings saved.', 'booking-manager'), 5 );              // Show Save message
    }

    // <editor-fold     defaultstate="collapsed"                        desc=" CSS & JS  "  >
    
    /** CSS for this page */
    private function css() {
        ?>
        <style type="text/css">  
            .wpbm-help-message {
                border:none;
                margin:0 !important;
                padding:0 !important;
            }
            
            @media (max-width: 399px) {
            }
        </style>
        <?php
    }
    
    
        
    /**     Add Custon JavaScript - for some specific settings options
     *      Executed After post content, after initial definition of settings,  and possible definition after POST request.
     * 
     * @param type $menu_slug
     * 
     */
    private function enqueue_js(){                                               // $page_tag, $active_page_tab, $active_page_subtab ) {

    
            
        // Check if this correct  page /////////////////////////////////////////////

//        if ( !(
//                   ( $page_tag == 'wpbm-settings')                              // Load only at 'wpbm-settings' menu
//                && ( $_GET['tab'] == 'email' )                                  // At ''general' tab
//                && (  ( ! isset( $_GET['subtab'] ) ) || ( $_GET['subtab'] == 'new-admin' )  )                                               
//              )
//          ) return;

        // JavaScript //////////////////////////////////////////////////////////////
        
        $js_script = '';
        //Show or hide colors section  in settings page depend form  selected email  template.
        $js_script .= " jQuery('select[name=\"download_notification_template_file\"]').on( 'change', function(){    
                                if ( jQuery('select[name=\"download_notification_template_file\"] option:selected').val() == 'plain' ) {   
                                    jQuery('.template_colors').hide();                                    
                                } else {
                                    jQuery('.template_colors').show();                                    
                                }
                            } ); ";    
        $js_script .= "\n";                                                     //New Line
        $js_script .= " if ( jQuery('select[name=\"download_notification_template_file\"] option:selected').val() == 'plain' ) {   
                            jQuery('.template_colors').hide();                                    
                        } ";    
        
        // Show Warning messages if Title (optional) is empty - title of email  will be "WordPress
        $js_script .= " jQuery(document).ready(function(){ ";
        $js_script .= "     if (  jQuery('#download_notification_to_name').val() == ''  ) {";
        $js_script .= "         jQuery('#download_notification_to_name').parent().append('<div class=\'updated\' style=\'border-left-color:#ffb900;padding:5px 10px;\'>". esc_js(__('If empty then title defined as WordPress', 'booking-manager'))."</div>')";
        $js_script .= "     }";
        $js_script .= "     if (  jQuery('#download_notification_from_name').val() == ''  ) {";
        $js_script .= "         jQuery('#download_notification_from_name').parent().append('<div class=\'updated\' style=\'border-left-color:#ffb900;padding:5px 10px;\'>". esc_js(__('If empty then title defined as WordPress', 'booking-manager'))."</div>')";
        $js_script .= "     }";
        $js_script .= "  }); ";
          // Show Warning messages if "From" Email DNS different from current website DNS
        $js_script .= " jQuery(document).ready(function(){ ";
        
        $js_script .= "     var wpbm_email_from = jQuery('#download_notification_from').val();";    // from@oplugins.com 
        $js_script .= "     wpbm_email_from = wpbm_email_from.split('@');";             // ['from', 'oplugins.com']
        $js_script .= "     wpbm_email_from.shift();";                                  // ['oplugins.com']
        $js_script .= "     wpbm_email_from = wpbm_email_from.join('');";              // 'oplugins.com'        

        $js_script .= "     var wpbm_website_dns = jQuery(location).attr('hostname');"; // server.com
        $js_script .= "     if ( wpbm_email_from != wpbm_website_dns ) {";
        $js_script .= "         jQuery('#download_notification_from').parent().append('<div class=\'updated\' style=\'border-left-color:#ffb900;padding:5px 10px;\'>". esc_js(__('Email different from website DNS, its can be a reason of not delivery emails. Please use the email withing the same domain as your website!', 'booking-manager'))."</div>')";
        $js_script .= "     }";

        $js_script .= "  }); ";
        
        
        
        // Eneque JS to  the footer of the page
        wpbm_enqueue_js( $js_script );                
    }

    
    // </editor-fold>    
}
add_action('wpbm_menu_created',  array( new WPBM_Settings_Page_Email_DownloadNotification() , '__construct') );    // Executed after creation of Menu



// <editor-fold     defaultstate="collapsed"                        desc=" Emails Sending After Download action "  >

function wpbm_send_email_download_notification( $replace = array(), $email_to = '' ) {
    
    
    ////////////////////////////////////////////////////////////////////////
    // Load Data 
    ////////////////////////////////////////////////////////////////////////

    /* Check if New Email Template   Exist or NOT
     * Exist     -  return  empty array in format: array( OPTION_NAME => array() ) 
     *              Its will  load DATA from DB,  during creattion mail_api CLASS
     *              during initial activation  of the API  its try  to get option  from DB
     *              We need to define this API before checking POST, to know all available fields
     *              Define Email Name & define field values from DB, if not exist, then default values. 
     * Not Exist -  import Old Data from DB
     *              or get "default" data from settings and return array with  this data
     *              This data its initial  parameters for definition fields in mail_api CLASS 
     * 
     */

    $init_fields_values = array();//wpbm_import6_email__download_notification__get_fields_array_for_activation();

    // Get Value of first element - array of default or imported OLD data,  because need only  array  of values without key - name of options for wp_options table
    //$init_fields_values = array_shift( array_values( $init_fields_values ) );               

    $mail_api = new WPBM_Emails_API_DownloadNotification( WPBM_EMAIL_DOWNLOAD_NOTIFICATION_ID, $init_fields_values );
    
    ////////////////////////////////////////////////////////////////////////////
    
    if ( $mail_api->fields_values['enabled'] == 'Off' )     return false;       // Email  template deactivated - exit.

	add_filter( 'wpbm_email_api_is_allow_send_copy' , 'wpbm_email_api_is_allow_send_copy_block' , 10, 3);

	// Email
    if ( ! empty( $mail_api->fields_values['to'] ) )                            // Admin  email  from  Settings > Emails page 
        $valid_email = sanitize_email( $mail_api->fields_values['to'] );
                                                                                // Overwrite email,  if passed to  function
    if ( ! empty( $to_email ) )        
        $valid_email = sanitize_email( $to_email );
  	
    if ( empty( $valid_email ) ) return $mail_api;        //return false;       
    
	// Name
    if  ( ! empty( $mail_api->fields_values['to_name'] ) )         
        $email_to_name = trim( wp_specialchars_decode( esc_html( stripslashes( $mail_api->fields_values['to_name'] ) ), ENT_QUOTES ) );
    else    
        $email_to_name = '';
    
	
    $to = $email_to_name . ' <' .  $valid_email . '> ';
    $email_result = $mail_api->send( $to , $replace );
    
 //debuge( (int) $email_result, $to , $replace);
    
    return $mail_api;    
    
}

// </editor-fold>