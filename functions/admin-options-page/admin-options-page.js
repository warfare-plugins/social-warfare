/*********************************************************

	The Admin Options Page

*********************************************************/

// Document Ready Trigger
jQuery(document).ready(function() {
	jQuery('.nc_socialPanel').animate({opacity:0},0);
/*********************************************************

	Header Menu

*********************************************************/

	jQuery(document).ready(function() {
		offset 	= jQuery('.sw-top-menu').offset();
		width 	= jQuery('.sw-top-menu').width();
		jQuery('.sw-top-menu').css({
			'position':'fixed',
			'left':offset.left,
			'top':offset.top,
			'width':width
		});
		jQuery('.sw-admin-wrapper').css('padding-top', '75px');
		
	});

/*********************************************************

	Tab Navigation

*********************************************************/
	jQuery(document).on('click','.sw-tab-selector',function(event) {
		event.preventDefault ? event.preventDefault() : (event.returnValue = false);
		jQuery("html, body").animate({ scrollTop: 0 }, 0);
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
		sw_conditional_fields();
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
		save_color_toggle();
		sw_conditional_fields();
	});

	jQuery('form.sw-admin-settings-form input, form.sw-admin-settings-form select').on('change' , function() {
		sw_conditional_fields();
		var new_options = sw_fetch_all_options();
		save_color_toggle()
	});
	
	default_options = sw_fetch_all_options();
	
// End the Document Ready Trigger
});

/*********************************************************

	A Function to change the color of the save button

*********************************************************/
function save_color_toggle() {
	var new_options = sw_fetch_all_options();
	if(JSON.stringify(new_options) != JSON.stringify(default_options)) {
		jQuery('.sw-save-settings').removeClass('sw-navy-button').addClass('sw-red-button');
	} else {
		jQuery('.sw-save-settings').removeClass('sw-red-button').addClass('sw-navy-button');	
	}
}

/*********************************************************

	A Function to gather all the settings

*********************************************************/

function sw_fetch_all_options() {
	
	// Create an object
	values = {};
	
	// Loop through all the inputs
	jQuery('form.sw-admin-settings-form input, form.sw-admin-settings-form select').each( function() {
		
		var name = jQuery(this).attr('name');
		if(jQuery(this).attr('type') == 'checkbox') {
			var value = jQuery(this).prop('checked');	
		} else {
			var value = jQuery(this).val();		
		}
		values[name] = value;
	});
	
	// Create the objects
	values.newOrderOfIcons = {};
	
	// Loop through each active network
	jQuery('.sw-active i').each( function() {
		var network = jQuery(this).attr('value');
		values.newOrderOfIcons[network] = network;
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
		
		// The loading screen
		sw_loading_screen();
		
		// Fetch all the settings
		settings = sw_fetch_all_options();
				
		// Prepare date
		var data = {
			action: 'sw_store_settings',
			settings: settings
		};

		// Send the POST request
		jQuery.post(ajaxurl, data, function(response) {
			
			// Clear the loading screen
			sw_clear_loading_screen();
				
			// Reset the default options variable
			default_options = sw_fetch_all_options();
			save_color_toggle();
			
		});
		
	});
});

function sw_loading_screen() {
	jQuery('body').append('<div class="sw-loading-bg"><div class="sw-loading-message">Saving Changes</div></div>');	
}

function sw_clear_loading_screen() {
	jQuery('.sw-loading-message').html('Success!').removeClass('sw-loading-message').addClass('sw-loading-complete');
	jQuery('.sw-loading-bg').delay(1000).fadeOut(1000);
	setTimeout( function() {
		jQuery('.sw-loading-bg').remove();
	} , 2000);	
}

/*********************************************************

	A Function to update the preview buttons

*********************************************************/

jQuery(document).ready( function() {

	availableOptions = {
		flatFresh: {
			fullColor 		: 'Full Color',
			lightGray 		: 'Light Gray',
			mediumGray		: 'Medium Gray',
			darkGray 		: 'Dark Gray',
			lgOutlines 		: 'Light Gray Outlines',
			mdOutlines		: 'Medium Gray Outlines',
			dgOutlines 		: 'Dark Gray Outlines',
			colorOutlines 	: 'Color Outlines',
			customColor 	: 'Custom Color',
			ccOutlines 		: 'Custom Color Outlines'
		},
		leaf: {
			fullColor 		: 'Full Color',
			lightGray 		: 'Light Gray',
			mediumGray		: 'Medium Gray',
			darkGray 		: 'Dark Gray',
			lgOutlines 		: 'Light Gray Outlines',
			mdOutlines		: 'Medium Gray Outlines',
			dgOutlines 		: 'Dark Gray Outlines',
			colorOutlines 	: 'Color Outlines',
			customColor 	: 'Custom Color',
			ccOutlines 		: 'Custom Color Outlines'
		},
		pill: {	
			fullColor 		: 'Full Color',
			lightGray 		: 'Light Gray',
			mediumGray		: 'Medium Gray',
			darkGray 		: 'Dark Gray',
			lgOutlines 		: 'Light Gray Outlines',
			mdOutlines		: 'Medium Gray Outlines',
			dgOutlines 		: 'Dark Gray Outlines',
			colorOutlines 	: 'Color Outlines',
			customColor 	: 'Custom Color',
			ccOutlines 		: 'Custom Color Outlines'
		},
		threeDee: {
			fullColor 		: 'Full Color',
			lightGray 		: 'Light Gray',
			mediumGray		: 'Medium Gray',
			darkGray 		: 'Dark Gray'
		},
		connected: {
			fullColor 		: 'Full Color',
			lightGray 		: 'Light Gray',
			mediumGray		: 'Medium Gray',
			darkGray 		: 'Dark Gray',
			lgOutlines 		: 'Light Gray Outlines',
			mdOutlines		: 'Medium Gray Outlines',
			dgOutlines 		: 'Dark Gray Outlines',
			colorOutlines 	: 'Color Outlines',
			customColor 	: 'Custom Color',
			ccOutlines 		: 'Custom Color Outlines'
		},
		shift: {
			fullColor 		: 'Full Color',
			lightGray 		: 'Light Gray',
			mediumGray		: 'Medium Gray',
			darkGray 		: 'Dark Gray',
			lgOutlines 		: 'Light Gray Outlines',
			mdOutlines		: 'Medium Gray Outlines',
			dgOutlines 		: 'Dark Gray Outlines',
			colorOutlines 	: 'Color Outlines',
			customColor 	: 'Custom Color',
			ccOutlines 		: 'Custom Color Outlines'
		}
	};
	
	// Check if we are on the admin page
	if(jQuery('select[name="visualTheme"]').length) {
		
		// Update the items and previews on the initial page load
		var visualTheme  = jQuery('select[name="visualTheme"]').val();
		var dColorSet    = jQuery('select[name="dColorSet"]').val();
		var iColorSet    = jQuery('select[name="iColorSet"]').val();
		var oColorSet    = jQuery('select[name="oColorSet"]').val();
		jQuery('select[name="dColorSet"] option, select[name="iColorSet"] option, select[name="oColorSet"] option').remove();
		jQuery.each(availableOptions[visualTheme], function(index, value) {
			if(index == dColorSet) {
				jQuery('select[name="dColorSet"]').append('<option value="'+index+'" selected>'+value+'</option>');
			} else {
				jQuery('select[name="dColorSet"]').append('<option value="'+index+'">'+value+'</option>');
			};
			if(index == iColorSet) {
				jQuery('select[name="iColorSet"]').append('<option value="'+index+'" selected>'+value+'</option>');
			} else {
				jQuery('select[name="iColorSet"]').append('<option value="'+index+'">'+value+'</option>');
			};
			if(index == oColorSet) {
				jQuery('select[name="oColorSet"]').append('<option value="'+index+'" selected>'+value+'</option>');
			} else {
				jQuery('select[name="oColorSet"]').append('<option value="'+index+'">'+value+'</option>');
			};
			
			
			if(	dColorSet == 'customColor' 
				|| dColorSet == 'ccOutlines' 
				|| iColorSet == 'customColor' 
				|| iColorSet == 'ccOutlines' 
				|| oColorSet == 'customColor' 
				|| oColorSet == 'ccOutlines' ) 
				{
				
				jQuery('.customColor_wrapper').slideDown();
				updateCustomColor();
				
			} else {
				jQuery('.customColor_wrapper').slideUp();
			};
		});
		
		// A function for updating the preview
		function updateTheme() {
			var visualTheme  = jQuery('select[name="visualTheme"]').val();
			var dColorSet    = jQuery('select[name="dColorSet"]').val();
			var iColorSet    = jQuery('select[name="iColorSet"]').val();
			var oColorSet    = jQuery('select[name="oColorSet"]').val();
			var buttonsClass = 'sw_'+visualTheme+' sw_d_'+dColorSet+' sw_i_'+iColorSet+' sw_o_'+oColorSet;
			if(typeof lastClass === 'undefined'){
				jQuery('.nc_socialPanel').removeClass('sw_flatFresh sw_d_fullColor sw_i_fullColor sw_o_fullColor').addClass(buttonsClass);
			} else {
				jQuery('.nc_socialPanel').removeClass(lastClass).addClass(buttonsClass);
			};
			lastClass = buttonsClass;
			if(dColorSet == 'customColor' || dColorSet == 'ccOutlines' || iColorSet == 'customColor' || iColorSet == 'ccOutlines' || oColorSet == 'customColor' || oColorSet == 'ccOutlines' ) {
				jQuery('.customColor_wrapper').slideDown();
				
				updateCustomColor();
				
				
			} else {
				jQuery('.customColor_wrapper').slideUp();
			};
		};
		setTimeout( updateTheme , 2000 );
		
		// If the color set changes, update the preview with the function
		jQuery('select[name="dColorSet"], select[name="iColorSet"], select[name="oColorSet"]').on('change', updateTheme);
		
		// If the visual theme is updated, update the preview manually
		jQuery('select[name="visualTheme"]').on('change', function() {
			
			var visualTheme  = jQuery('select[name="visualTheme"]').val();
			var dColorSet    = jQuery('select[name="dColorSet"]').val();
			var iColorSet    = jQuery('select[name="iColorSet"]').val();
			var oColorSet    = jQuery('select[name="oColorSet"]').val();
			
			var i = 0;
			var array = availableOptions[visualTheme];
			
			var dColor = array.hasOwnProperty(dColorSet);
			var iColor = array.hasOwnProperty(iColorSet);
			var oColor = array.hasOwnProperty(oColorSet);
			
			jQuery('select[name="dColorSet"] option, select[name="iColorSet"] option, select[name="oColorSet"] option').remove();
			jQuery.each(availableOptions[visualTheme], function(index, value) {
				if(index == dColorSet || (dColor == false && i == 0)) {
					jQuery('select[name="dColorSet"]').append('<option value="'+index+'" selected>'+value+'</option>');
				} else {
					jQuery('select[name="dColorSet"]').append('<option value="'+index+'">'+value+'</option>');
				};
				if(index == iColorSet || (iColor == false && i == 0)) {
					jQuery('select[name="iColorSet"]').append('<option value="'+index+'" selected>'+value+'</option>');
				} else {
					jQuery('select[name="iColorSet"]').append('<option value="'+index+'">'+value+'</option>');
				};
				if(index == oColorSet || (oColor == false && i == 0)) {
					jQuery('select[name="oColorSet"]').append('<option value="'+index+'" selected>'+value+'</option>');
				} else {
					jQuery('select[name="oColorSet"]').append('<option value="'+index+'">'+value+'</option>');
				};
				++i;
			});

			var buttonsClass = 'sw_'+visualTheme+' sw_d_'+dColorSet+' sw_i_'+iColorSet+' sw_o_'+oColorSet;
			if(typeof lastClass === 'undefined'){
				jQuery('.nc_socialPanel').removeClass('sw_flatFresh sw_d_fullColor sw_i_fullColor sw_o_fullColor').addClass(buttonsClass);
			} else {
				jQuery('.nc_socialPanel').removeClass(lastClass).addClass(buttonsClass);
			};
			lastClass = buttonsClass;
		});
	}

});

	function updateCustomColor() {
		
		var visualTheme  = jQuery('select[name="visualTheme"]').val();
		var dColorSet    = jQuery('select[name="dColorSet"]').val();
		var iColorSet    = jQuery('select[name="iColorSet"]').val();
		var oColorSet    = jQuery('select[name="oColorSet"]').val();
			
		jQuery('style.sw_customColorStuff').remove();
			var colorCode = jQuery('input[name="customColor"]').val();
			customCSS = '';
			if(dColorSet == 'customColor' || iColorSet == 'customColor' || oColorSet == 'customColor') {
				var customCSS = '.nc_socialPanel.sw_d_customColor a, html body .nc_socialPanel.sw_i_customColor .nc_tweetContainer:hover a, body .nc_socialPanel.sw_o_customColor:hover a {color:white} .nc_socialPanel.sw_d_customColor .nc_tweetContainer, html body .nc_socialPanel.sw_i_customColor .nc_tweetContainer:hover, body .nc_socialPanel.sw_o_customColor:hover .nc_tweetContainer {background-color:'+colorCode+';border:1px solid '+colorCode+';}';
			}
			if(dColorSet == 'ccOutlines' || iColorSet == 'ccOutlines' || oColorSet == 'ccOutlines' ) {
				var customCSS = customCSS+' .nc_socialPanel.sw_d_ccOutlines a, html body .nc_socialPanel.sw_i_ccOutlines .nc_tweetContainer:hover a, body .nc_socialPanel.sw_o_ccOutlines:hover a { color:'+colorCode+'; } .nc_socialPanel.sw_d_ccOutlines .nc_tweetContainer, html body .nc_socialPanel.sw_i_ccOutlines .nc_tweetContainer:hover, body .nc_socialPanel.sw_o_ccOutlines:hover .nc_tweetContainer { background:transparent; border:1px solid '+colorCode+'; }';	
			}
			
			jQuery('head').append('<style type="text/css" class="sw_customColorStuff">'+customCSS+'</style>');	
	};

/*********************************************************

	A Function to update the button sizing options

*********************************************************/

	function swUpdateScale() {
		jQuery('.nc_socialPanel').css({width:'100%'});
		var width = jQuery('.nc_socialPanel').width();
		scale = jQuery('select[name="buttonSize"]').val();
		align = jQuery('select[name="buttonFloat"]').val();
		if((align == 'fullWidth' && scale != 1) || scale >= 1) {
			newWidth = width / scale;
			jQuery('.nc_socialPanel').css('cssText', 'width:'+newWidth+'px!important;');
			jQuery('.nc_socialPanel').css({
				'transform':'scale('+scale+')',
				'transform-origin':'left'	
			});
		} else if(align != 'fullWidth' && scale < 1) {
			newWidth = width / scale;
			jQuery('.nc_socialPanel').css({
				'transform':'scale('+scale+')',
				'transform-origin':align	
			});
		}
		swSetWidths(true);
		activateHoverStates();
	}
	
jQuery(document).ready( function() {
	
	scale = jQuery('select[name="buttonSize"]').val();
	jQuery('select[name="buttonSize"],select[name="buttonFloat"]').on('change',function() {
		swUpdateScale();
	});

});

/*********************************************************

	Update the Click To Tweet Demo

*********************************************************/
function update_ctt_demo() {
	var current_style 	= jQuery('.sw_CTT').attr('data-style');
	var new_style		= jQuery('select[name="cttTheme"]').val();
	jQuery('.sw_CTT').removeClass(current_style).addClass(new_style).attr('data-style',new_style);
}

jQuery(document).ready(function($) {

	update_ctt_demo();
	jQuery('select[name="cttTheme"]').on('change', function() {
		update_ctt_demo();
	});
	
});

/*********************************************************

	A function to show/hide conditionals

*********************************************************/

jQuery(document).ready(function() {
	sw_conditional_fields();
	jQuery('[name="float"]')
	
});

function sw_conditional_fields() {
	
	// Loop through all the fields that have dependancies
	jQuery('div[dep]').each( function() {
		
		// Fetch the conditional values
		var con_dep 	= jQuery(this).attr('dep');
		var con_dep_val = jQuery.parseJSON(jQuery(this).attr('dep_val'));
		
		// Fetch the value of checkboxes or other input types
		if(jQuery('[name="'+con_dep+'"]').attr('type') == 'checkbox') {
			var value = jQuery('[name="'+con_dep+'"]').prop('checked');	
		} else {
			var value = jQuery('[name="'+con_dep+'"]').val();		
		}

		// Show or hide based on the conditional values (and the dependancy must be visible in case it is dependant)
		if(jQuery.inArray(value,con_dep_val) !== -1 && jQuery('[name="'+con_dep+'"]').parent('.sw-grid').is(':visible')) {
			jQuery(this).show();
		} else {
			jQuery(this).hide();			
		}		
	});
}

/*******************************************************

	Register the Plugin

*******************************************************/

// Wait for the DOM to load
jQuery(document).ready(function() {

	// Register the plugin
	jQuery('#register-plugin').on('click',function(e) {
		
		// Block the default action
		event.preventDefault ? event.preventDefault() : (event.returnValue = false);

		// The loading screen
		sw_loading_screen();

		// Fetch all the registration values
		var regCode = jQuery('input[name="regCode"]').val();
		var email = jQuery('input[name="emailAddress"]').val();
		var domain = jQuery('input[name="domain"]').val();
		
		// Create the ajax URL
		url = ('https:' == document.location.protocol ? 'https:' : 'http:') + '//warfareplugins.com/registration-api/?activity=register&emailAddress='+email+'&domain='+domain+'&registrationCode='+regCode;
		
		ajax_data = {
			'action':'sw_ajax_passthrough',
			'url':url
		}
		
		// Ping the home server to create a registration log
		jQuery.post( ajaxurl, ajax_data, function( data ) {
			
			// Parse the JSON response
			var object = jQuery.parseJSON(data);
			
			// If the response was a failure...
			if(object['status'] == 'failure') {
				
				// Remove the loading message
				jQuery('.sw-loading-complete').remove();
				
				// Alert the failure status
				alert('Failure: '+object['message']);
				
			// If the response was a success	
			} else {
				jQuery('input[name="premiumCode"]').val(object['premiumCode']);

				// Prepare data
				var data = {
					action: 'sw_store_registration',
					premiumCode: object['premiumCode']
				};
					
				// Send the response to admin-ajax.php
				jQuery.post(ajaxurl, data, function(response) {
					
					// Clear the loading screen
					sw_clear_loading_screen();
					
					// Toggle the registration display
					jQuery('.registration-wrapper').attr('registration','1');
					
				});
			}
		});
	});
	
/*******************************************************

	Unregister the Plugin

*******************************************************/
	jQuery('#unregister-plugin').on('click',function(e) {
		
		// Block the default action
		event.preventDefault ? event.preventDefault() : (event.returnValue = false);

		// The loading screen
		sw_loading_screen();
		
		// Fetch the registration values
		var regCode = jQuery('input[name="regCode"]').val();
		var email = jQuery('input[name="emailAddress"]').val();
		var domain = jQuery('input[name="domain"]').val();
		
		// Assemble the link for the Ajax request
		url = ('https:' == document.location.protocol ? 'https:' : 'http:') + '//warfareplugins.com/registration-api/?activity=unregister&emailAddress='+email+'&domain='+domain+'&registrationCode='+regCode;
		
		// Create the ajax object
		ajax_data = {
			'action':'sw_ajax_passthrough',
			'url':url
		}
		
		// Ping the home server for the registration log
		jQuery.post( ajaxurl, ajax_data, function( data ) {
			
			// Parse the JSON response
			var object = jQuery.parseJSON(data);
			
			// Clear out the premium code and the email address field
			jQuery('input[name="premiumCode"]').val('');
			jQuery('input[name="emailAddress"]').val('');

				// Prepare data
				var data = {
					action: 'sw_delete_registration',
					premiumCode: '',
					emailAddress: ''
				};
					
				// Send the response to admin-ajax.php
				jQuery.post(ajaxurl, data, function(response) {
					
					// Clear the loading screen
					sw_clear_loading_screen();
					
					// Toggle the registration display
					jQuery('.registration-wrapper').attr('registration','0');
					
				});			
			
		});
	});


});

/*********************************************************

	Rearm the Registration if the domain has changed

*********************************************************/

// Wait for the DOM to load
jQuery(document).ready(function() {

	jQuery('input[name="premiumCode"]').attr('readonly','readonly');
	jQuery('input[name="regCode"]').parent('.sw_field').hide();
	var premcode = jQuery('input#domain').attr('data-premcode');

	if (jQuery('input[name="premiumCode"]').val() != '' && jQuery('input[name="premiumCode"]').val() != premcode) {
		
		// Fetch our variables
		var regCode = jQuery('input[name="regCode"]').val();
		var email = jQuery('input[name="emailAddress"]').val();
		var domain = jQuery('input[name="domain"]').val();
		
		// Create the unregister url
		url = ('https:' == document.location.protocol ? 'https:' : 'http:') + '//warfareplugins.com/registration-api/?activity=unregister&emailAddress='+email+'&premiumCode='+jQuery('input[name="premiumCode"]').val();
		
		// Create the ajax object
		ajax_data = {
			'action':'sw_ajax_passthrough',
			'url':url
		}
		
		// Pass the URL to the admin-ajax.php passthrough function
		jQuery.get( ajaxurl, ajax_data , function(data) {
			
			// Create the register URL
			url = ('https:' == document.location.protocol ? 'https:' : 'http:') + '//warfareplugins.com/registration-api/?activity=register&emailAddress='+email+'&domain='+domain+'&registrationCode='+regCode;
			
			// Create the ajax object
			ajax_data = {
				'action':'sw_ajax_passthrough',
				'url':url
			}
			
			// Pass the URL to the admin-ajax.php passthrough function
			jQuery.post( ajaxurl, ajax_data , function(subdata) {
				
				// Parse the response
				var info = jQuery.parseJSON(subdata);
				
				// If the rearm was successful
				if(info['status'] == 'success') {
					
					// Prepare data
					var data = {
						action: 'sw_store_registration',
						premiumCode: info['premiumCode']
					};
					
					// Send the response to admin-ajax.php
					jQuery.post(ajaxurl, data, function(response) {
						
						// Toggle the registration display
						jQuery('.registration-wrapper').attr('registration','1');
						
					});
					
					jQuery('input[name="premiumCode"]').val(info['premiumCode']);
					
				} else {
					
					// Prepare data
					var data = {
						action: 'sw_delete_registration',
						premiumCode: '',
						emailAddress: ''
					};
						
					// Send the response to admin-ajax.php
					jQuery.post(ajaxurl, data, function(response) {
												
						// Toggle the registration display
						jQuery('.registration-wrapper').attr('registration','0');
						
					});
					
				}
			});
				
		
		} );
	};

});

/*******************************************************

	Make the buttons sortable

*******************************************************/

jQuery(document).ready( function() {
	jQuery( '.sw-buttons-sort.sw-active' ).sortable({
		connectWith: ".sw-buttons-sort.sw-inactive",
		update: function( event, ui ) { save_color_toggle(); }
	});
	jQuery( '.sw-buttons-sort.sw-inactive' ).sortable({
		connectWith: ".sw-buttons-sort.sw-active",
		update: function( event, ui ) { save_color_toggle(); }
	});
});

/*********************************************************

	A Function send the array of setting to ajax.php

*********************************************************/
jQuery.fn.selectText = function(){
    var doc = document
        , element = this[0]
        , range, selection
    ;
    if (doc.body.createTextRange) {
        range = document.body.createTextRange();
        range.moveToElementText(element);
        range.select();
    } else if (window.getSelection) {
        selection = window.getSelection();        
        range = document.createRange();
        range.selectNodeContents(element);
        selection.removeAllRanges();
        selection.addRange(range);
    }
};

jQuery(document).ready( function() {
	jQuery('.sw-system-status').on('click', function() {
				
		// Block the default action
		event.preventDefault ? event.preventDefault() : (event.returnValue = false);
		
		jQuery('.system-status-wrapper').slideToggle();
		jQuery('.system-status-container').selectText();
		
	});
});