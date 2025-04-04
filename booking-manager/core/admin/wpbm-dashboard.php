<?php /**
 * @version 1.0
 * @package Booking Manager 
 * @category Admin Panel - Dashboard functions
 * @author wpdevelop
 *
 * @web-site https://oplugins.com/
 * @email info@oplugins.com 
 * 
 * @modified 2016-03-16
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit, if accessed directly


////////////////////////////////////////////////////////////////////////////////
// D a s h b o a r d      W i d g e t
////////////////////////////////////////////////////////////////////////////////
    
/** Setup Widget for Dashboard */
function wpbm_dashboard_widget_setup(){            

   // Check, if we have permission  to  show Widget  ///////////////////////////
   $is_user_activated = apply_wpbm_filter('multiuser_is_current_user_active',  true ); //FixIn: 6.0.1.17
   if ( ! $is_user_activated ) 
       return false;

   $user_role = get_wpbm_option( 'wpbm_user_role_master' );
   if ( ! wpbm_is_current_user_have_this_role( $user_role ) )
       return false;
   
   
   // Add Booking Manager Widget  to Dashboard  ///////////////////////////////
   $bk_dashboard_widget_id = 'wpbm_dashboard_widget';
   wp_add_dashboard_widget( $bk_dashboard_widget_id, sprintf( __( 'Item Calendar', 'booking-manager') ), 'wpbm_dashboard_widget_show', null );

   
   // Sort Dashboard. Add Booking Manager widget to top ///////////////////////
   global $wp_meta_boxes;
   $normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
   if ( isset( $normal_dashboard[$bk_dashboard_widget_id] ) ) {
        // Backup and delete our new dashbaord widget from the end of the array
        $example_widget_backup = array( $bk_dashboard_widget_id => $normal_dashboard[$bk_dashboard_widget_id] );
        unset( $normal_dashboard[$bk_dashboard_widget_id] );
    } else
        $example_widget_backup = array();

    // Sometimes, some other plugins can modify this item, so its can be not a array
    if ( is_array( $normal_dashboard ) ) {                                      
        // Merge the two arrays together so our widget is at the beginning
        if ( is_array( $normal_dashboard ) )
            $sorted_dashboard = array_merge( $example_widget_backup, $normal_dashboard );
        else
            $sorted_dashboard = $example_widget_backup;
        // Save the sorted array back into the original metaboxes
        $wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
    }
}
//add_action( 'wp_dashboard_setup', 'wpbm_dashboard_widget_setup' );



/** Show item Dashboard Widget content */
function wpbm_dashboard_widget_show() {    
    
    wpbm_dashboard_widget_css();
                
   ?>        
   <div id="wpbm_dashboard_widget_container" >
	<?php 
              
       wpbm_dashboard_section_version(); 
       
       wpbm_dashboard_section_support();        
       
	?>         
   </div>
   <div style="clear:both;"></div>   
   <?php 
}
    

/** Get Info for Dashboard */
function wpbm_get_dashboard_info() {
    
    ob_start();  
    
    wpbm_dashboard_widget_show();
  
    return ob_get_clean();  
}


/** CSS for Dashboard Widget */
function wpbm_dashboard_widget_css() {
    
    ?><style type="text/css">
        #wpbm_dashboard_widget_container {
            width:100%;
        }
        #wpbm_dashboard_widget_container .wpbm_dashboard_section {
            float:left;
            margin:0px;
            padding:0px;
            width:100%;
        }
        #wpbm_dashboard_widget_container .wpbm_dashboard_section h4 {            
            font-size: 14px;
            font-weight: 600;
            margin: 5px 0 15px;
        }     
        #bk_upgrade_section p {
            font-size: 13px;
            line-height: 1.5em;
            margin: 15px 0 0;
            padding: 0;
        }
        #dashboard-widgets-wrap #wpbm_dashboard_widget_container .wpbm_dashboard_section {
           width:49%;
        }
        #dashboard-widgets-wrap #wpbm_dashboard_widget_container .bk_right {
            float:right
        }
        #dashboard-widgets-wrap #wpbm_dashboard_widget_container .border_orrange, 
        #wpbm_dashboard_widget_container .border_orrange {
            background: #fffaf1 none repeat scroll 0 0;
            border-left: 3px solid #eeab26;
            clear: both;
            margin: 5px 5px 20px;
            padding: 10px 0;
            width: 99%;
        }
        #wpbm_dashboard_widget_container .bk_header {
            color: #555555;
            font-size: 13px;
            font-weight: 600;
            line-height: 1em;
        }
        #wpbm_dashboard_widget_container .bk_table {
            background:transparent;
            border-bottom:none;
            border-top:1px solid #ECECEC;
            margin:6px 0 10px 6px;
            padding:2px 10px;
            width:95%;
            -border-radius:4px;
            -moz-border-radius:4px;
            -webkit-border-radius:4px;
            -moz-box-shadow:0 0 2px #C5C3C3;
            -webkit-box-shadow:0 0 2px #C5C3C3;
            -box-shadow:0 0 2px #C5C3C3;
        }
        #wpbm_dashboard_widget_container .bk_table td{
            border-bottom:1px solid #DDDDDD;
            line-height:19px;
            padding:4px 0px 4px 10px;
            font-size:13px;
        }
        #wpbm_dashboard_widget_container .bk_table tr td.first{
           text-align:center;
           padding:4px 0px;
        }
        #wpbm_dashboard_widget_container .bk_table tr td a {
            text-decoration: none;
        }
        #wpbm_dashboard_widget_container .bk_table tr td a span{
            font-size:18px;
            font-family: Georgia,"Times New Roman","Bitstream Charter",Times,serif;
        }
        #wpbm_dashboard_widget_container .bk_table td.bk_spec_font a{
            font-family: Georgia,"Times New Roman","Bitstream Charter",Times,serif;
            font-size:14px;
        }
        #wpbm_dashboard_widget_container .bk_table td.bk_spec_font {
            font-family: Georgia,"Times New Roman","Bitstream Charter",Times,serif;
            font-size:13px;
        }
        #wpbm_dashboard_widget_container .bk_table td.pending a{
            color:#E66F00;
        }
        #wpbm_dashboard_widget_container .bk_table td.new-items a{
            color:red;
        }
        #wpbm_dashboard_widget_container .bk_table td.actual-items a{
            color:green;
        }
        #bk_errror_loading {
             text-align: center;
             font-style: italic;
             font-size:11px;
        }
    </style><?php
}


////////////////////////////////////////////////////////////////////////////////
// S e c t i o n s
////////////////////////////////////////////////////////////////////////////////

/** Dashboard Support Section */
function wpbm_dashboard_section_support() {
    ?>
    <div class="wpbm_dashboard_section bk_right">
        <span class="bk_header"><?php _e('Support' , 'booking-manager');?>:</span>
        <table class="bk_table">
            <tr>
                <td style="text-align:center;" class="bk_spec_font"><a target="_blank" href="https://oplugins.com/plugins/booking-manager/"><?php _e('Help Info' , 'booking-manager');?></a></td>
            </tr>
            <tr>
                <td style="text-align:center;" class="bk_spec_font"><a href="mailto:support@oplugins.com?subject=booking-manager"><?php _e('Contact Support' , 'booking-manager');?></a></td>
            </tr>                                        
            <tr>
                <td style="text-align:center;" class="bk_spec_font"><?php  // mailto:newfeature@oplugins.com?subject=booking-manager
				printf( __( '%sNew feature suggestion%s', 'booking-manager'),
									'<a href="mailto:newfeature@oplugins.com?Subject=booking-manager" target="_blank">',
									'</a>' ); ?></td>
            </tr>                                        
        </table>
    </div>
    <?php 
}


/** Dashboard Version Section */
function wpbm_dashboard_section_version() {
        
    $version = 'free';
    
    ?>
    <div class="wpbm_dashboard_section" >
        <span class="bk_header"><?php _e('Current version' , 'booking-manager');?>:</span>
        <table class="bk_table">
            <tr class="first">
                <td style="width:35%;text-align: right;;" class=""><?php _e('Version' , 'booking-manager');?>:</td>
                <td style="color: #e50;font-size: 13px;font-weight: 600;text-align: left;text-shadow: 0 -1px 0 #eee;;" 
                    class="bk_spec_font"><?php 
                if ( substr( WPBM_VERSION, 0, 2 ) == '9.' ) {
                    $show_version =  substr( WPBM_VERSION , 2 ) ;                            
                    if ( substr($show_version, ( -1 * ( strlen( WPBM_VERSION_NUM ) ) ) ) === WPBM_VERSION_NUM ) {
                        $show_version = substr($show_version, 0, ( -1 * ( strlen( WPBM_VERSION_NUM ) ) - 1 ) );
                        $show_version = str_replace('.', ' ', $show_version) . " <sup><strong style='font-size:12px;'>" . WPBM_VERSION_NUM . "</strong></sup>" ;
                    }                                           
                    echo $show_version ; 
                } else 
                    echo WPBM_VERSION;
                ?></td>
            </tr>
            <tr>
                <td style="width:35%;text-align: right;" class="first b"><?php _e('Release date' , 'booking-manager');?>:</td>
                <td style="text-align: left;  font-weight: 600;" class="bk_spec_font"><?php echo date ("d.m.Y", filemtime(WPBM_FILE)); ?></td>
            </tr>
        </table>
    </div>    
    <?php 
}
