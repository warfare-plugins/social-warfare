/*********************************************************

	The Admin Options Page

*********************************************************/

// Document Ready Trigger
jQuery(document).ready(function() {
	jQuery('.nc_socialPanel').animate({opacity:0},0);
/*********************************************************

	Tab Navigation

*********************************************************/
	jQuery(document).on('click','.sw-tab-selector',function(event) {
		event.preventDefault ? event.preventDefault() : (event.returnValue = false);
		var tab = jQuery(this).attr('data-link');
		jQuery('.sw-admin-tab').hide();
		jQuery('.nc_socialPanel').animate({opacity:0},0);
		jQuery('#'+tab).show();
		jQuery('.sw-header-menu li').removeClass('sw-active-tab');
		jQuery(this).parents('li').addClass('sw-active-tab');
		if(tab == 'sw_styles') {
			swSetWidths(true);
			activateHoverStates();
		} else {
			jQuery('.nc_socialPanel').animate({opacity:0},0);
		}
	});

/*********************************************************

	Checkboxes

*********************************************************/
	jQuery(document).on('click','.sw-checkbox-toggle',function(event) {
		var status = jQuery(this).attr('status');
		var elem = jQuery(this).attr('field');
		if( status == 'on' ) {
			jQuery(this).attr('status','off');
			jQuery(elem).prop('checked', false);
		} else {
			jQuery(this).attr('status','on');
			jQuery(elem).prop('checked', true);
		};
		sw_fetch_all_options();
	});

	jQuery('form.sw-admin-settings-form input, form.sw-admin-settings-form select').on('change' , function() {
		sw_fetch_all_options();
	});
	
	sw_fetch_all_options();
	
// End the Document Ready Trigger
});

/*********************************************************

	A Function to gather all the settings

*********************************************************/

function sw_fetch_all_options() {
	
	values = {};
	jQuery('form.sw-admin-settings-form input, form.sw-admin-settings-form select').each( function() {
		
		
		var name = jQuery(this).attr('name');
		if(jQuery(this).attr('type') == 'checkbox') {
			var value = jQuery(this).prop('checked');	
		} else {
			var value = jQuery(this).val();		
		}
		values[name] = value;
	});
	
	return values;
	
}

/*********************************************************

	A Function send the array of setting to ajax.php

*********************************************************/

jQuery(document).ready(function() {

	jQuery('.sw-save-settings').on('click',function(event) {
		
		// Block the default action
		event.preventDefault ? event.preventDefault() : (event.returnValue = false);
		
		jQuery('body').append('<div class="sw-loading-message">Saving Changes</div>');
		
		// Fetch all the settings
		settings = sw_fetch_all_options();
				
		// Prepare date
		var data = {
			action: 'sw_store_settings',
			settings: settings
		};

		// Send the POST request
		jQuery.post(ajaxurl, data, function(response) {
			
			jQuery('.sw-loading-message').html('Success!').removeClass('sw-loading-message').addClass('sw-loading-complete').delay(1000).fadeOut(1000);
			setTimeout( function() {
				jQuery('.sw-loading-complete').remove();
			} , 2500);
			console.log('Got this from the server: ' + response);
		});
		
	});
});

/*********************************************************

	A Function to register the plugin

*********************************************************/







