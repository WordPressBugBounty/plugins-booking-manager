<?php
/**
 * @version 1.1
 * @package Booking Manager 
 * @category Send Emails
 * @author wpdevelop
 *
 * @web-site https://oplugins.com/
 * @email info@oplugins.com 
 * 
 * @modified 15.09.2015
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


////////////////////////////////////////////////////////////////////////////////
// S u p p o r t    E m a i l    F u n c t i o n s   -   Modification    Hooks
////////////////////////////////////////////////////////////////////////////////

/**
 * Check email and format  it
 * 
 * @param string $emails
 * @return string
 */
function wpbm_validate_emails( $emails ) {

    $emails = str_replace(';', ',', $emails);

    if ( !is_array( $emails ) )
            $emails = explode( ',', $emails );

    $emails_list = array();
    foreach ( (array) $emails as $recipient ) {

        // Break $recipient into name and address parts if in the format "Foo <bar@baz.com>"
        $recipient_name = '';
        if( preg_match( '/(.*)<(.+)>/', $recipient, $matches ) ) {
            if ( count( $matches ) == 3 ) {
                $recipient_name = $matches[1];
                $recipient = $matches[2];                 
            }
        } else {                
            // Check about correct  format  of email
            if( preg_match( '/([\w\.\-_]+)?\w+@[\w\-_]+(\.\w+){1,}/im', $recipient, $matches ) ) {                        //FixIn: 2.0.15.1
                $recipient = $matches[0];
            }             
        }

        $recipient_name = str_replace('"', '', $recipient_name);
        $recipient_name = trim( wp_specialchars_decode( esc_html( stripslashes( $recipient_name ) ), ENT_QUOTES ) );

        $emails_list[] =   ( empty( $recipient_name ) ? '' : $recipient_name . ' '  )
                           . '<' . sanitize_email( $recipient ) . '>';		
    }

    $emails_list = implode( ',', $emails_list );

    return $emails_list;
}    


/** Check  Email  subject  about Language sections
 * 
 * @param string $subject
 * @param string $email_id
 * @return string
 */
function wpbm_email_api_get_subject_before( $subject, $email_id ) {
            
    $subject =  apply_wpbm_filter('wpbm_check_for_active_language', $subject );

    return  $subject;
}
add_filter( 'wpbm_email_api_get_subject_before', 'wpbm_email_api_get_subject_before', 10, 2 );    // Hook fire in api-email.php


/** Check  Email  sections content  about Language sections
 * 
 * @param array $fields_values - list  of params to  parse: 'content', 'header_content', 'footer_content' for different languges, etc ....
 * @param string $email_id - Email ID
 * @param string $email_type - 'plain' | 'html'
 */
function wpbm_email_api_get_content_before( $fields_values, $email_id , $email_type ) {
    
    if ( isset( $fields_values['content'] ) ) {
        $fields_values['content'] =  apply_wpbm_filter('wpbm_check_for_active_language', $fields_values['content'] );
        if ($email_type == 'html')
            $fields_values['content'] = make_clickable( $fields_values['content'] );
    }
    
    if ( isset( $fields_values['header_content'] ) )
        $fields_values['header_content'] =  apply_wpbm_filter('wpbm_check_for_active_language', $fields_values['header_content'] );
    
    if ( isset( $fields_values['footer_content'] ) )
        $fields_values['footer_content'] =  apply_wpbm_filter('wpbm_check_for_active_language', $fields_values['footer_content'] );
    
    return $fields_values;
}
add_filter( 'wpbm_email_api_get_content_before', 'wpbm_email_api_get_content_before', 10, 3 );    // Hook fire in api-email.php


/** Modify email  content,  if needed. - In HTML mail content,  make links clickable.
 * 
 * @param array $email_content - content of Email
 * @param string $email_id - Email ID
 * @param string $email_type - 'plain' | 'html'
 */
function wpbm_email_api_get_content_after( $email_content, $email_id , $email_type ) {
    
    if (  ( $email_type == 'html' ) || ( $email_type == 'multipart' )  )
       $email_content = make_clickable( $email_content );
     
    return $email_content;
}
add_filter( 'wpbm_email_api_get_content_after', 'wpbm_email_api_get_content_after', 10, 3 );    // Hook fire in api-email.php


/** Check  Email  Headers  -  in New item Email (to admin) set Reply-To header to visitor email.
 * 
 * @param string $headers
 * @param string $email_id - Email ID
 * @param array $fields_values - list  of params to  parse: 'content', 'header_content', 'footer_content' for different languges, etc ....
 * @param array $replace_array - list  of relpaced shortcodes
 * @return string
 */
function wpbm_email_api_get_headers_after( $mail_headers, $email_id , $fields_values , $replace_array, $additional_params = array() ) {
       
/*
// Default in api-emails.php:
//        $mail_headers  = 'From: ' . $this->get_from__name() . ' <' .  $this->get_from__email_address() . '> ' . "\r\n" ;
//        $mail_headers .= 'Content-Type: ' . $this->get_content_type() . "\r\n" ;
//        
//            $mail_headers = "From: $mail_sender\n";
//            preg_match('/<(.*)>/', $mail_sender, $simple_email_matches );
//            $reply_to_email = ( count( $simple_email_matches ) > 1 ) ? $simple_email_matches[1] : $mail_sender;
//            $mail_headers .= 'Reply-To: ' . $reply_to_email . "\n";        
//            $mail_headers .= 'X-Sender: ' . $reply_to_email . "\n";
//            $mail_headers .= 'Return-Path: ' . $reply_to_email . "\n";
*/

//debuge($mail_headers, $email_id , $fields_values , $replace_array);    
    if (
        ( $email_id == 'new_admin' )                                            // Only  for email: "New item to Admin"
       || ( isset( $additional_params['reply'] ) )  
    ) {
        if ( isset( $replace_array['email'] ) ) {                                // Get email from  the item form.
           
            $reply_to_email = sanitize_email( $replace_array['email'] );
            if ( ! empty( $reply_to_email ) )
                $mail_headers .= 'Reply-To: '    . $reply_to_email  . "\r\n" ;
            
           // $mail_headers .= 'X-Sender: '    . $reply_to_email  . "\r\n" ;
           // $mail_headers .= 'Return-Path: ' . $reply_to_email  . "\r\n" ;           
        }
    }

    return  $mail_headers;
}
add_filter( 'wpbm_email_api_get_headers_after', 'wpbm_email_api_get_headers_after', 10, 5 );    // Hook fire in api-email.php


/** Check if we can send Email - block  sending in live demos
 * 
 * @param bool $is_send_email 
 * @param string $email_id
 * @param array $fields_values - list  of params to  parse: 'content', 'header_content', 'footer_content' for different languges, etc ....
 * @return bool
 */
function wpbm_email_api_is_allow_send( $is_send_email, $email_id, $fields_values ) {
//debuge($fields_values);    
    if ( wpbm_is_this_demo() )   
        $is_send_email = false;

    return  $is_send_email;
}
add_filter( 'wpbm_email_api_is_allow_send', 'wpbm_email_api_is_allow_send', 100, 3 );    // Hook fire in api-email.php
add_filter( 'wpbm_email_api_is_allow_send_copy' , 'wpbm_email_api_is_allow_send' , 100, 3);

/** Show warning about not sending emails,  and reason about this.
 * 
 * @param object $wp_error_object     - WP Error object
 * @param string $error_description   - Description
 */
function wpbm_email_sending_error( $wp_error_object, $error_description = '' ) {
    
    if ( empty( $error_description ) ) {
//        $error_description = __( 'Unknown exception', 'booking-manager') . '.';        // Overwrite to  show error, if no description ???    
    }
    
    if ( ! empty( $error_description ) ) {

        $error_description = '' . __('Error', 'booking-manager')  . '! ' . __('Email was not sent. An error occurred.', 'booking-manager') .  ' ' . $error_description;
        
        // Admin side
        if (  function_exists( 'wpbm_show_message' ) ) {
            wpbm_show_message ( $error_description , 15 , 'error');     

        }
        
        // Front-end
        ?>   
        <script type="text/javascript">  
            if (typeof( wpbm_show_message_under_element ) == 'function') {
                wpbm_show_message_under_element( '.wpbm_form' , '<?php echo esc_js( $error_description ) ; ?>', '');
            }
        </script>    
        <?php    
    } else {
        
        // Error that have no description. Its can be Empty Object like this: WP_Error Object(  'errors' => array(), 'error_data' => array() ),  or NOT
        // debuge( $wp_error_object );        
    }
}
add_action('wpbm_email_sending_error', 'wpbm_email_sending_error', 10, 2);