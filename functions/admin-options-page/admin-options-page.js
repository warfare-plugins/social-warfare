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

	A Function to update the preview buttons

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
	}
	
	function swShowAlignment() {
		scale = jQuery('select[name="buttonSize"]').val();
		if(scale >= 1) {
			jQuery('select[name="buttonFloat"]').parents('.sw_field').slideUp();
		} else {
			jQuery('select[name="buttonFloat"]').parents('.sw_field').slideDown();	
		}
	}
	
	if(jQuery('select[name="buttonSize"]').length) {
		swUpdateScale();
		swShowAlignment();
		jQuery('select[name="buttonSize"],select[name="buttonFloat"]').on('change',function() {
			swUpdateScale();
			swShowAlignment();
		});
	}

