/*********************************************************

	The Admin Options Page

*********************************************************/

// Document Ready Trigger
jQuery(document).ready(function() {
	
/*********************************************************

	Tab Navigation

*********************************************************/
	jQuery(document).on('click','.sw-tab-selector',function(event) {
		event.preventDefault ? event.preventDefault() : (event.returnValue = false);
		var tab = jQuery(this).attr('data-link');
		jQuery('.sw-admin-tab').hide();
		jQuery('#'+tab).show();
		jQuery('.sw-header-menu li').removeClass('sw-active-tab');
		jQuery(this).parents('li').addClass('sw-active-tab');
		swSetWidths(true);
		activateHoverStates();
	});

/*********************************************************

	Checkboxes

*********************************************************/
	jQuery(document).on('click','.sw-checkbox-toggle',function(event) {
		var status = jQuery(this).attr('status');
		var elem = jQuery(this).attr('field');
		console.log(elem);
		if( status == 'on' ) {
			jQuery(this).attr('status','off');
			jQuery(elem).prop('checked', false);
		} else {
			jQuery(this).attr('status','on');
			jQuery(elem).prop('checked', true);
		}
	});
	
// End the Document Ready Trigger
});

