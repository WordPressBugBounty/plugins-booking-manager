/**
 * @version 1.0
 * @package Booking Manager 
 * @subpackage BackEnd Main Script Lib
 * @category Scripts
 * 
 * @author wpdevelop
 * @link https://oplugins.com/
 * @email info@oplugins.com
 *
 * @modified 2014.09.10
 */


/** Set item listing row as   R e a d
 * 
 * @param {type} wpbm_id
 * @returns {undefined}
 */
function set_wpbm_row_read(wpbm_id){
    if (wpbm_id == 0) {
        jQuery('.new-label').addClass('hidden_items');
        jQuery('.bk-update-count').html( '0' );
    } else {
        jQuery('#wpbm_mark_'+wpbm_id + '').addClass('hidden_items');
        decrese_new_counter();
    }
}

/** Set item listing row as   U n R e a d
 * 
 * @param {type} wpbm_id
 * @returns {undefined}
 */
function set_wpbm_row_unread(wpbm_id){
    jQuery('#wpbm_mark_'+wpbm_id + '').removeClass('hidden_items');
    increase_new_counter();
}


/** Increase counter about new items
 * 
 * @returns {undefined}
 */
function increase_new_counter () {
    var my_num = parseInt(jQuery('.bk-update-count').html());
    my_num = my_num + 1;
    jQuery('.bk-update-count').html(my_num);
}

/** Decrease counter about new items
 * 
 * @returns {undefined}
 */
function decrese_new_counter () {
    var my_num = parseInt(jQuery('.bk-update-count').html());
    if (my_num>0){
        my_num = my_num - 1;
        jQuery('.bk-update-count').html(my_num);
    }
}


// Set item listing   R O W   Approved
function set_wpbm_row_approved(wpbm_id){
    jQuery('#wpbm_row_'+wpbm_id + ' .wpbm-labels .label-approved').removeClass('hidden_items');
    jQuery('#wpbm_row_'+wpbm_id + ' .wpbm-labels .label-pending').addClass('hidden_items');

    jQuery('#wpbm_row_'+wpbm_id + ' .wpbm-dates .field-wpbm-date').addClass('approved');

    jQuery('#wpbm_row_'+wpbm_id + ' .wpbm-actions .approve_wpbm_link').addClass('hidden_items');
    jQuery('#wpbm_row_'+wpbm_id + ' .wpbm-actions .pending_wpbm_link').removeClass('hidden_items');

}

// Set item listing   R O W   Pending
function set_wpbm_row_pending(wpbm_id){
    jQuery('#wpbm_row_'+wpbm_id + ' .wpbm-labels .label-approved').addClass('hidden_items');
    jQuery('#wpbm_row_'+wpbm_id + ' .wpbm-labels .label-pending').removeClass('hidden_items');

    jQuery('#wpbm_row_'+wpbm_id + ' .wpbm-dates .field-wpbm-date').removeClass('approved');

    jQuery('#wpbm_row_'+wpbm_id + ' .wpbm-actions .approve_wpbm_link').removeClass('hidden_items');
    jQuery('#wpbm_row_'+wpbm_id + ' .wpbm-actions .pending_wpbm_link').addClass('hidden_items');

}

// Remove  item listing   R O W
function set_wpbm_row_deleted(wpbm_id){
    jQuery('#wpbm_row_'+wpbm_id).fadeOut(1000);        
    jQuery('#gcal_imported_events_id_'+wpbm_id).remove();
}

// Set in item listing   R O W   Resource title
function set_wpbm_row_resource_name(wpbm_id, resourcename){
    jQuery('#wpbm_row_'+wpbm_id + ' .wpbm-labels .label-resource').html(resourcename);
}

// Set in item listing   R O W   new Remark in hint
function set_wpbm_row_remark_in_hint( wpbm_id, new_remark ){
    
    jQuery('#wpbm_row_' + wpbm_id + ' .wpbm-actions .remark_wpbm_link').attr( 'data-original-title', new_remark );

    if ( new_remark != '' )
        jQuery('#wpbm_row_' + wpbm_id + ' .wpbm-actions .remark_wpbm_link i.glyphicon-comment').addClass('red_icon_color');
    else
        jQuery('#wpbm_row_' + wpbm_id + ' .wpbm-actions .remark_wpbm_link i.glyphicon-comment').removeClass('red_icon_color');
}

// Set in item listing   R O W   new Remark in hint
function set_wpbm_row_payment_status(wpbm_id, payment_status, payment_status_show){

    jQuery('#wpbm_row_'+wpbm_id + ' .wpbm-labels .label-payment-status').removeClass('label-danger');
    jQuery('#wpbm_row_'+wpbm_id + ' .wpbm-labels .label-payment-status').removeClass('label-success');

    jQuery('#wpbm_row_'+wpbm_id + ' .wpbm-labels .label-payment-status').html(payment_status_show);

    if (payment_status == 'OK') {
        jQuery('#wpbm_row_'+wpbm_id + ' .wpbm-labels .label-payment-status').addClass('label-success');
    } else if (payment_status == '') {
        jQuery('#wpbm_row_'+wpbm_id + ' .wpbm-labels .label-payment-status').addClass('label-danger');
    } else {
        jQuery('#wpbm_row_'+wpbm_id + ' .wpbm-labels .label-payment-status').addClass('label-danger');
    }
}



// Interface Element
function showSelectedInDropdown(selector_id, title, value){
    jQuery('#' + selector_id + '_selector .wpbm_selected_in_dropdown').html( title );
    jQuery('#' + selector_id ).val( value );
    jQuery('#' + selector_id + '_container').hide();
}

//Admin function s for checking all checkbos in one time
function setCheckBoxInTable(el_stutus, el_class){
     jQuery('.'+el_class).attr('checked', el_stutus);

     if ( el_stutus ) {
         jQuery('.'+el_class).parent().parent().addClass('row_selected_color');
     } else {
         jQuery('.'+el_class).parent().parent().removeClass('row_selected_color');
     }
}


// FixIn: 5.4.5
function wpbm_get_selected_locale( wpbm_id, wpbm_active_locale ) {
    
    var id_to_check = "" + wpbm_id;
    if ( id_to_check.indexOf('|') == -1 ) {
        var selected_locale = jQuery('#locale_for_item' + wpbm_id).val();

        if (  ( selected_locale != '' ) && ( typeof(selected_locale) !== 'undefined' )  ) {
            wpbm_active_locale = selected_locale;
        } 
    }
    return wpbm_active_locale;
}



 
//FixIn: 6.1.1.10 
// Set item listing   R O W   Trash
function set_wpbm_row_trash( wpbm_id ){
    jQuery('#wpbm_row_'+wpbm_id + ' .wpbm-labels .label-trash').removeClass('hidden_items');    
    jQuery('#wpbm_row_'+wpbm_id + ' .wpbm-actions .trash_wpbm_link').addClass('hidden_items');
    jQuery('#wpbm_row_'+wpbm_id + ' .wpbm-actions .restore_wpbm_link').removeClass('hidden_items');
    jQuery('#wpbm_row_'+wpbm_id + ' .wpbm-actions .delete_wpbm_link').removeClass('hidden_items');
    
    
    jQuery('#wpbm-id-'+wpbm_id + ' .label-trash').removeClass('hidden_items');
}

//FixIn: 6.1.1.10 
// Set item listing   R O W   Restore
function set_wpbm_row_restore( wpbm_id ){    
    jQuery('#wpbm_row_'+wpbm_id + ' .wpbm-labels .label-trash').addClass('hidden_items');    
    jQuery('#wpbm_row_'+wpbm_id + ' .wpbm-actions .trash_wpbm_link').removeClass('hidden_items');
    jQuery('#wpbm_row_'+wpbm_id + ' .wpbm-actions .restore_wpbm_link').addClass('hidden_items');
    jQuery('#wpbm_row_'+wpbm_id + ' .wpbm-actions .delete_wpbm_link').addClass('hidden_items');

    jQuery('#wpbm-id-'+wpbm_id + ' .label-trash').addClass('hidden_items');
}
 


// Get Selected rows in imported Events list
function get_selected_items_id_in_this_list( list_tag, skip_id_length ) {

    var checkedd = jQuery( list_tag + ":checked" );
    var id_for_approve = "";

    // get all IDs
    checkedd.each(function(){
        var id_c = jQuery(this).attr('id');
        id_c = id_c.substr(skip_id_length,id_c.length-skip_id_length);
        id_for_approve += id_c + "|";
    });

    if ( id_for_approve.length > 1 )
        id_for_approve = id_for_approve.substr(0,id_for_approve.length-1);      //delete last "|"

    return id_for_approve ;

}

// Get the list of ID in selected items from item listing
function get_selected_items_id_in_wpbm_listing(){

    var checkedd = jQuery(".wpbm_list_item_checkbox:checked");
    var id_for_approve = "";

    // get all IDs
    checkedd.each(function(){
        var id_c = jQuery(this).attr('id');
        id_c = id_c.substr(20,id_c.length-20);
        id_for_approve += id_c + "|";
    });

    if ( id_for_approve.length > 1 )
        id_for_approve = id_for_approve.substr(0,id_for_approve.length-1);      //delete last "|"

    return id_for_approve ;
}





/** Selections of several  checkboxes like in gMail with shift :)
 * Need to  have this structure: 
 * .wpbm_selectable_table
 *      .wpbm_selectable_head
 *              .check-column
 *                  :checkbox
 *      .wpbm_selectable_body
 *          .wpbm_row
 *              .check-column
 *                  :checkbox
 *      .wpbm_selectable_foot             
 *              .check-column
 *                  :checkbox
 */
( function( $ ){            
    $( document ).ready(function(){
            
	var checks, first, last, checked, sliced, lastClicked = false;

	// check all checkboxes
        $('.wpbm_selectable_body').find('.check-column').find(':checkbox').on( 'click', function(e) {                   //FixIn: 2.0.18.4
		if ( 'undefined' == e.shiftKey ) { return true; }
		if ( e.shiftKey ) {
			if ( !lastClicked ) { return true; }
			//checks = $( lastClicked ).closest( 'form' ).find( ':checkbox' ).filter( ':visible:enabled' );
                        checks = $( lastClicked ).closest( '.wpbm_selectable_body' ).find( ':checkbox' ).filter( ':visible:enabled' );
			first = checks.index( lastClicked );
			last = checks.index( this );
			checked = $(this).prop('checked');
			if ( 0 < first && 0 < last && first != last ) {
				sliced = ( last > first ) ? checks.slice( first, last ) : checks.slice( last, first );
				sliced.prop( 'checked', function() {
					if ( $(this).closest('.wpbm_row').is(':visible') )
						return checked;

					return false;
				});
			}
		}
		lastClicked = this;

		// toggle "check all" checkboxes
		var unchecked = $(this).closest('.wpbm_selectable_body').find(':checkbox').filter(':visible:enabled').not(':checked');
		$(this).closest('.wpbm_selectable_table').children('.wpbm_selectable_head, .wpbm_selectable_foot').find(':checkbox').prop('checked', function() {
			return ( 0 === unchecked.length );
		});

		return true;
	});

	$('.wpbm_selectable_head, .wpbm_selectable_foot').find('.check-column :checkbox').on( 'click.wp-toggle-checkboxes', function( event ) {
		var $this = $(this),
			$table = $this.closest( '.wpbm_selectable_table' ),
			controlChecked = $this.prop('checked'),
			toggle = event.shiftKey || $this.data('wp-toggle');

		$table.children( '.wpbm_selectable_body' ).filter(':visible')
                        .find('.check-column').find(':checkbox')
			//.children().children('.check-column').find(':checkbox')
			.prop('checked', function() {
				if ( $(this).is(':hidden,:disabled') ) {
					return false;
				}

				if ( toggle ) {
					return ! $(this).prop( 'checked' );
				} else if ( controlChecked ) {
					return true;
				}

				return false;
			});

		$table.children('.wpbm_selectable_head,  .wpbm_selectable_foot').filter(':visible')
                        .find('.check-column').find(':checkbox')
			//.children().children('.check-column').find(':checkbox')
			.prop('checked', function() {
				if ( toggle ) {
					return false;
				} else if ( controlChecked ) {
					return true;
				}

				return false;
			});
	});
    });    
}( jQuery ) );    