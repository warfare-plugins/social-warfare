/* global ajaxurl, swpAdminOptionsData, socialWarfarePlugin, wp */
(function( window, $, undefined ) {
	'use strict';

    window.onload = function() {
        /*********************************************************
    		Temporary patch for the custom color selects.
    	*********************************************************/
        /*
        *  Temp patch on the Visual Options colors.
        *  This makes the custom colors appear/disappear as necessary.
        */
        var panelSelector = "[name=default_colors],[name=hover_colors], [name=single_colors]";
        var floatSelector = "[name=float_default_colors], [name=float_hover_colors], [name=float_single_colors]";

        //* Hide the custom color inputs by default.
        $("[name=custom_color],[name=custom_color_outlines],[name=float_custom_color],[name=float_custom_color_outlines]").parent().parent().hide();

        //* Show custom fields if they have already been selected.
        $(panelSelector).each(function(index, select) {
            var value = $(select).val();
            var customColor = $("[name=custom_color]").parent().parent();
            var customOutlines = $("[name=custom_color_outlines]").parent().parent();

            if ( value.indexOf("custom") !== -1) {
                //* A custom value is set for this input.
                if (value.indexOf('outlines') > 0 ) {
                    customOutlines.show();
                } else {
                    customColor.show();
                }
            }
        });


        //* Same, for floating button options.
        $(floatSelector).each(function(index, select) {
            var value = $(select).val();
            var customColor = $("[name=float_custom_color]").parent().parent();
            var customOutlines = $("[name=float_custom_color_outlines]").parent().parent();

            if ( value.indexOf("custom") !== -1) {
                //* A custom value is set for this input.
                if (value.indexOf('outlines') > 0 ) {
                    customOutlines.show();
                } else {
                    customColor.show();
                }
            }
        });


        //* Change handlers for style.
        $(panelSelector).on("change", function(e) {
            var value = e.target.value;
            var customColor = $("[name=custom_color]").parent().parent();
            var customOutlines = $("[name=custom_color_outlines]").parent().parent();

            handleCustomColors(e, panelSelector, customColor, customOutlines, value);
        });


        //* Same, for floating button options.
        $(floatSelector).on("change", function(e) {
            var value = e.target.value;
            var customColor = $("[name=float_custom_color]").parent().parent();
            var customOutlines = $("[name=float_custom_color_outlines]").parent().parent();

            customColor.hide();
            customOutlines.hide();

            handleCustomColors(e, floatSelector, customColor, customOutlines, value);
        });
    }

    function handleCustomColors(event, selector, customColor, customOutlines) {
        //* Create a notice about the custom colors.
        var colorNotice = '<div id="color-notice"><p><span class="color-dismiss"></span><b>Note:</b> Custom colors will not show up in the preview, but will on your site.</p></div>';
        var visibility = {
            customColor: false,
            customOutlines: false
        };

        $(selector).each(function(index, select) {
            var val = $(select).val();
            //* Check to see if this or a sibling input has a custom_color selected.
            if (val.indexOf("custom") !== -1) {
                if (val.indexOf("outlines") > 0) {
                    visibility.customOutlines = true;
                } else {
                    visibility.customColor = true;
                }
            }
        });

        //* Hide or show the inputs based on results from above.
        visibility.customColor ? customColor.slideDown() : customColor.slideUp();
        visibility.customOutlines ? customOutlines.slideDown() : customOutlines.slideUp();

        if (visibility.customColor || visibility.customOutlines) {
            $("body").append(colorNotice);
            $(".color-dismiss").on("click", function() {
                $("#color-notice").fadeOut("slow");
            });
        } else {
            if ($("#color-notice").length) {
                $("#color-notice").fadeOut("slow");
            }
        }

    }


	/*********************************************************
		A Function send the array of setting to ajax.php
	*********************************************************/
	function selectText( element ) {
		var	range, selection;

		if ( document.body.createTextRange ) {
			range = document.body.createTextRange();

			range.moveToElementText( element );

			range.select();
		} else if ( window.getSelection ) {
			selection = window.getSelection();

			range = document.createRange();

			range.selectNodeContents( element );

			selection.removeAllRanges();

			selection.addRange( range );
		}
	}

	/*********************************************************
		A Function to gather all the settings
	 *********************************************************/
	function fetchAllOptions() {
		// Create an object
		var values = {};

		// Loop through all the inputs
		$( 'form.sw-admin-settings-form input, form.sw-admin-settings-form select, form.sw-admin-settings-form textarea' ).each( function() {
			var $field = $( this );

			var name = $field.attr( 'name' );
			var value;

			if ( 'checkbox' === $field.attr( 'type' ) ) {
				value = $field.prop( 'checked' );
			} else {
				value = $field.val();
			}


			values[name] = value;
		});

		// Create the objects
		values.order_of_icons = {};

		// Loop through each active network
		$( '.sw-active i' ).each( function() {
			var network = $( this ).data( 'network' );
			values.order_of_icons[network] = network;
		});

		return values;
	}

	/*********************************************************
		Header Menu
	*********************************************************/
	function headerMenuInit() {
		var offset = $( '.sw-top-menu' ).offset();

		var width = $( '.sw-top-menu' ).width();

		$( '.sw-top-menu' ).css({
			position: 'fixed',
			left: offset.left,
			top: offset.top,
			width: width
		});

		$( '.sw-admin-wrapper' ).css( 'padding-top', '75px' );
	}

	/*********************************************************
		Tab Navigation
	*********************************************************/
	function tabNavInit() {
		$( '.sw-tab-selector' ).on( 'click', function( event ) {
			event.preventDefault();

			$( 'html, body' ).animate( { scrollTop: 0 }, 300 );

			var tab = $( this ).attr( 'data-link' );

			$( '.sw-admin-tab' ).hide();

			$( '#' + tab ).show();

			$( '.sw-header-menu li' ).removeClass( 'sw-active-tab' );

			$( this ).parents( 'li' ).addClass( 'sw-active-tab' );

			if ( 'swp_styles' === tab ) {
				socialWarfarePlugin.activateHoverStates();
			}

			swpConditionalFields();

		});
	}

	/*********************************************************
		Checkboxes
	*********************************************************/
	function checkboxesInit() {

		$( '.sw-checkbox-toggle' ).on( 'click', function() {
			var status = $( this ).attr( 'status' );

			var elem = $( this ).attr( 'field' );

			if ( 'on' === status ) {
				$( this ).attr( 'status', 'off' );

				$( elem ).prop( 'checked', false );
			} else {
				$( this ).attr( 'status', 'on' );

				$( elem ).prop( 'checked', true );
			}

			saveColorToggle();

			swpConditionalFields();
		});
	}

	function populateOptions() {
		$( 'form.sw-admin-settings-form input, form.sw-admin-settings-form select' ).on( 'change', function() {
			swpConditionalFields();

			socialWarfarePlugin.newOptions = fetchAllOptions();

			saveColorToggle();
		});

		socialWarfarePlugin.defaultOptions = fetchAllOptions();
	}

	/*********************************************************
		A Function to change the color of the save button
	 *********************************************************/
	function saveColorToggle() {
		socialWarfarePlugin.newOptions = fetchAllOptions();

		if ( JSON.stringify( socialWarfarePlugin.newOptions ) !== JSON.stringify( socialWarfarePlugin.defaultOptions ) ) {
			$( '.sw-save-settings' ).removeClass( 'sw-navy-button' ).addClass( 'sw-red-button' );
		} else {
			$( '.sw-save-settings' ).removeClass( 'sw-red-button' ).addClass( 'sw-navy-button' );
		}
	}

	/*********************************************************
		A Function send the array of setting to ajax.php
	*********************************************************/
	function handleSettingSave() {
		$( '.sw-save-settings' ).on( 'click', function( event ) {
			// Block the default action
			event.preventDefault ? event.preventDefault() : ( event.returnValue = false );

			// The loading screen
			loadingScreen();

			// Fetch all the settings
			var settings = fetchAllOptions();

			// Prepare date
			var data = {
				action: 'swp_store_settings',
				security: swpAdminOptionsData.optionsNonce,
				settings: settings
			};

			// Send the POST request
			$.post({
                url: ajaxurl,
				data: data,
				success: function(response) {
					// Clear the loading screen
					clearLoadingScreen();

					// Reset the default options variable
					socialWarfarePlugin.defaultOptions = fetchAllOptions();

					saveColorToggle();
				}

			});

		});
	}

	function loadingScreen() {
		$( 'body' ).append( '<div class="sw-loading-bg"><div class="sw-loading-message">Saving Changes</div></div>' );
	}

	function clearLoadingScreen() {
		$( '.sw-loading-message' ).html( 'Success!' ).removeClass( 'sw-loading-message' ).addClass( 'sw-loading-complete' );

		$( '.sw-loading-bg' ).delay( 1000 ).fadeOut( 1000 );

		setTimeout( function() {
			$( '.sw-loading-bg' ).remove();
		}, 2000 );


	}

	function updateCustomColor() {
		var visualTheme  = $( 'select[name="button_shape"]' ).val();
		var dColorSet    = $( 'select[name="default_colors"]' ).val();
		var iColorSet    = $( 'select[name="single_colors"]' ).val();
		var oColorSet    = $( 'select[name="hover_colors"]' ).val();

		$( 'style.swp_customColorStuff' ).remove();

		var colorCode = $( 'input[name="custom_color"]' ).val();

		var customCSS = '';

		if ( dColorSet == 'custom_color' || iColorSet == 'custom_color' || oColorSet == 'custom_color' ) {
			customCSS = '.swp_social_panel.swp_default_customColor a, html body .swp_social_panel.swp_individual_customColor .nc_tweetContainer:hover a, body .swp_social_panel.swp_other_customColor:hover a {color:white} .swp_social_panel.swp_default_customColor .nc_tweetContainer, html body .swp_social_panel.swp_individual_customColor .nc_tweetContainer:hover, body .swp_social_panel.swp_other_customColor:hover .nc_tweetContainer {background-color:' + colorCode + ';border:1px solid ' + colorCode + ';}';
		}

		if ( dColorSet == 'custom_color_outlines' || iColorSet == 'custom_color_outlines' || oColorSet == 'custom_color_outlines' ) {
			customCSS = customCSS + ' .swp_social_panel.swp_default_custom_color_outlines a, html body .swp_social_panel.swp_individual_custom_color_outlines .nc_tweetContainer:hover a, body .swp_social_panel.swp_other_custom_color_outlines:hover a { color:' + colorCode + '; } .swp_social_panel.swp_default_custom_color_outlines .nc_tweetContainer, html body .swp_social_panel.swp_individual_custom_color_outlines .nc_tweetContainer:hover, body .swp_social_panel.swp_other_custom_color_outlines:hover .nc_tweetContainer { background:transparent; border:1px solid ' + colorCode + '; }';
		}

		$( 'head' ).append( '<style type="text/css" class="swp_customColorStuff">' + customCSS + '</style>' );
	}

	// A function for updating the preview
	function updateTheme() {
		var visualTheme  = getParsedValue("button_shape");
		var dColorSet    = getParsedValue("default_colors");
        var iColorSet    = getParsedValue("single_colors");
		var oColorSet    = getParsedValue("hover_colors");

        function getParsedValue(selector) {
            var value = $( 'select[name="' + selector + '"]' ).val();

            if (value.indexOf("custom") === 0) {
                var prefix = selector.slice(0, selector.indexOf("_"));
                return prefix + "_full_color";
            }

            return value;
        }

		var buttonsClass = 'swp_' + visualTheme + ' swp_default_' + dColorSet + ' swp_individual_' + iColorSet + ' swp_other_' + oColorSet;

		// Declare a default lastClass based on the default HTML if we haven't declared one
		if ('undefined' === typeof socialWarfarePlugin.lastClass) {
			socialWarfarePlugin.lastClass = 'swp_flat_fresh swp_default_full_color swp_individual_full_color swp_other_full_color';
		}

		// Put together the new classes, remove the old ones, add the new ones, store the new ones for removal next time.
		var buttonsClass = 'swp_' + visualTheme + ' swp_default_' + dColorSet + ' swp_individual_' + iColorSet + ' swp_other_' + oColorSet;


        $( '.swp_social_panel' ).removeClass("swp_other_medium_gray");
        $( '.swp_social_panel' ).removeClass( socialWarfarePlugin.lastClass ).addClass( buttonsClass );

		socialWarfarePlugin.lastClass = buttonsClass;

		// if ( dColorSet == 'custom_color' || dColorSet == 'custom_color_outlines' || iColorSet == 'custom_color' || iColorSet == 'custom_color_outlines' || oColorSet == 'custom_color' || oColorSet == 'custom_color_outlines' ) {
		// 	$( '.customColor_wrapper' ).slideDown();
        //
		// 	updateCustomColor();
		// } else {
		// 	$( '.customColor_wrapper' ).slideUp();
		// }
	}

	/*********************************************************
		A Function to update the preview buttons
	*********************************************************/

	function updateButtonPreviews() {

        //* Maps out the button themes.
		var defaults = {
			full_color: 'Full Color',
			light_gray: 'Light Gray',
			medium_gray: 'Medium Gray',
			dark_gray: 'Dark Gray',
			light_gray_outlines: 'Light Gray Outlines',
			medium_gray_outlines: 'Medium Gray Outlines',
			dark_gray_outlines: 'Dark Gray Outlines',
			color_outlines: 'Color Outlines',
			custom_color: 'Custom Color',
			custom_color_outlines: 'Custom Color Outlines'
		};

        //* Defines which themes are available per style.
		var availableOptions = {
			flat_fresh: defaults,
			leaf: defaults,
			pill: defaults,
			three_dee: {
				full_color: 'Full Color',
				light_gray: 'Light Gray',
				medium_gray: 'Medium Gray',
				dark_gray: 'Dark Gray'
			},
			connected: defaults,
			shift: defaults,
			boxed: defaults
		};

		// Check if we are on the admin page
		if ( 0 === $( 'select[name="button_shape"]' ).length ) {
			return;
		}

		// Update the items and previews on the initial page load
		var visualTheme = $( 'select[name="button_shape"]' ).val();
		var dColorSet   = $( 'select[name="default_colors"]' ).val();
		var iColorSet   = $( 'select[name="single_colors"]' ).val();
		var oColorSet   = $( 'select[name="hover_colors"]' ).val();

		$( 'select[name="default_colors"] option, select[name="single_colors"] option, select[name="hover_colors"] option' ).remove();

		$.each( availableOptions[visualTheme], function( index, value ) {
			if ( index === dColorSet ) {
				$( 'select[name="default_colors"]' ).append( '<option value="' + index + '" selected>' + value + '</option>' );
			} else {
				$( 'select[name="default_colors"]' ).append( '<option value="' + index + '">' + value + '</option>' );
			}

			if ( index === iColorSet ) {
				$( 'select[name="single_colors"]' ).append( '<option value="' + index + '" selected>' + value + '</option>' );
			} else {
				$( 'select[name="single_colors"]' ).append( '<option value="' + index + '">' + value + '</option>' );
			}

			if ( index === oColorSet ) {
				$( 'select[name="hover_colors"]' ).append( '<option value="' + index + '" selected>' + value + '</option>' );
			} else {
				$( 'select[name="hover_colors"]' ).append( '<option value="' + index + '">' + value + '</option>' );
			}

			if ( dColorSet == 'custom_color' || dColorSet == 'custom_color_outlines' || iColorSet == 'custom_color' || iColorSet == 'custom_color_outlines' || oColorSet == 'custom_color' || oColorSet == 'custom_color_outlines' ) {
				$( '.customColor_wrapper' ).slideDown();

				updateCustomColor();
			} else {
				$( '.customColor_wrapper' ).slideUp();
			}
		});

		// If the color set changes, update the preview with the function
		$( 'select[name="default_colors"], select[name="single_colors"], select[name="hover_colors"]' ).on( 'change', updateTheme );

		// If the visual theme is updated, update the preview manually
		$( 'select[name="button_shape"]' ).on( 'change', function() {
			var visualTheme  = $( 'select[name="button_shape"]' ).val();
			var dColorSet    = $( 'select[name="default_colors"]' ).val();
			var iColorSet    = $( 'select[name="single_colors"]' ).val();
			var oColorSet    = $( 'select[name="hover_colors"]' ).val();
			var i = 0;
			var array = availableOptions[visualTheme];
			var dColor = array.hasOwnProperty( dColorSet );
			var iColor = array.hasOwnProperty( iColorSet );
			var oColor = array.hasOwnProperty( oColorSet );

			$( 'select[name="default_colors"] option, select[name="single_colors"] option, select[name="hover_colors"] option' ).remove();

			$.each( availableOptions[visualTheme], function( index, value ) {
				if ( index === dColorSet || ( dColor == false && i == 0 ) ) {
					$( 'select[name="default_colors"]' ).append( '<option value="' + index + '" selected>' + value + '</option>' );
				} else {
					$( 'select[name="default_colors"]' ).append( '<option value="' + index + '">' + value + '</option>' );
				}

				if ( index === iColorSet || ( iColor == false && i == 0 ) ) {
					$( 'select[name="single_colors"]' ).append( '<option value="' + index + '" selected>' + value + '</option>' );
				} else {
					$( 'select[name="single_colors"]' ).append( '<option value="' + index + '">' + value + '</option>' );
				}

				if ( index === oColorSet || ( oColor == false && i == 0 ) ) {
					$( 'select[name="hover_colors"]' ).append( '<option value="' + index + '" selected>' + value + '</option>' );
				} else {
					$( 'select[name="hover_colors"]' ).append( '<option value="' + index + '">' + value + '</option>' );
				}

				++i;
			});
			// Declare a default lastClass based on the default HTML if we haven't declared one
			if('undefined' === typeof socialWarfarePlugin.lastClass){
				socialWarfarePlugin.lastClass = 'swp_flat_fresh swp_default_full_color swp_individual_full_color swp_other_full_color';
			}
			// Put together the new classes, remove the old ones, add the new ones, store the new ones for removal next time.
			var buttonsClass = 'swp_' + visualTheme + ' swp_default_' + dColorSet + ' swp_individual_' + iColorSet + ' swp_other_' + oColorSet;

			$( '.swp_social_panel' ).removeClass( socialWarfarePlugin.lastClass ).addClass( buttonsClass );
			socialWarfarePlugin.lastClass = buttonsClass;
		});
	}

	/*********************************************************
		A Function to update the button sizing options
	 *********************************************************/
	function updateScale() {
		$( 'select[name="button_size"],select[name="button_alignment"]' ).on( 'change', function() {
			$( '.swp_social_panel' ).css( { width: '100%' } );

			var width = $( '.swp_social_panel' ).width();
			var scale = $( 'select[name="button_size"]' ).val();
			var align = $( 'select[name="button_alignment"]' ).val();

			var newWidth;

			if ( ( align == 'full_width' && scale != 1 ) || scale >= 1 ) {
				newWidth = width / scale;

				$( '.swp_social_panel' ).css( 'cssText', 'width:' + newWidth + 'px!important;' );

				$( '.swp_social_panel' ).css({
					transform: 'scale(' + scale + ')',
					'transform-origin': 'left'
				});
			} else if ( align != 'full_width' && scale < 1 ) {
				newWidth = width / scale;

				$( '.swp_social_panel' ).css({
					transform: 'scale(' + scale + ')',
					'transform-origin': align
				});
			}

			socialWarfarePlugin.activateHoverStates();
		});
	}

	/*********************************************************
		Update the Click To Tweet Demo
	 *********************************************************/
	function updateCttDemo() {
		var $cttOptions = $( 'select[name="ctt_theme"]' );

		$cttOptions.on( 'change', function() {
			var newStyle = $( 'select[name="ctt_theme"]' ).val();

			$( '.swp_CTT' ).attr( 'class', 'swp_CTT' ).addClass( newStyle );
		});

		$cttOptions.trigger( 'change' );
	}

	function toggleRegistration( status , key ) {
        var adminWrapper = $(".sw-admin-wrapper");
        var addons = adminWrapper.attr("swp-addons");
        var registeredAddons = adminWrapper.attr("swp-registrations");

        //* Toggle visibility of the registration input field for {key}.
		$('.registration-wrapper.' + key).attr('registration', status);

        if (1 === parseInt(status)) {
            adminWrapper.attr('sw-registered', status);
            $('.sw-top-menu').attr('sw-registered', status);
            addAttrValue(adminWrapper, "swp-registrations", key);
        } else {
            removeAttrValue(adminWrapper, "swp-registrations", key);
        }
	}

    //* Removes a string from a given attribute.
    function removeAttrValue(el, attribute, removal) {
        var value = $(el).attr(attribute);
        var startIndex = value.indexOf(removal);
        if (startIndex === -1) return;

        var stopIndex = startIndex + removal.length;
        var newValue = value.slice(0, startIndex) + value.slice(stopIndex);

        $(el).attr(attribute, newValue);
    }

    //* Adds a string to a given attribute.
    function addAttrValue(el, attribute, addition) {
        var value = $(el).attr(attribute);
        if (value.includes(addition)) return;

        $(el).attr(attribute, value + addition);
    }

	/*******************************************************
		Register the Plugin
	*******************************************************/
	function registerPlugin(key,item_id) {
		var registered = false;
		var data = {
			action: 'swp_register_plugin',
			security: swpAdminOptionsData.registerNonce,
			activity: 'register',
			name_key: key,
			item_id: item_id,
			license_key: $( 'input[name="' + key + '_license_key"]' ).val()
		};

		loadingScreen();

		$.post( ajaxurl, data, function( response ) {
			// If the response was a failure...
			response = JSON.parse(response);

			if ( !response.success ) {
				alert( 'Failure: ' + response.data );
			} else {
				toggleRegistration( '1' , key);
				registered = true;
			}

            //* Passing in true forces reload from the server rather than cache.
            window.location.reload(true);
			clearLoadingScreen();

		});

		return registered;
	}

	/*******************************************************
		Unregister the Plugin
	*******************************************************/
	function unregisterPlugin(key,item_id) {
		var unregistered = false;
		var ajaxData = {
			action: 'swp_unregister_plugin',
			security: swpAdminOptionsData.registerNonce,
			activity: 'unregister',
			name_key: key,
			item_id: item_id,
		};

		loadingScreen();

		// Ping the home server to create a registration log
		$.post( ajaxurl, ajaxData, function( response ) {
			// If the response was a failure...
			//
			response = JSON.parse(response);
			if ( !response.success ) {
				alert( 'Failure: ' + response.data );
			} else {
				// If the response was a success
				$( 'input[name="'+key+'_license_key"]' ).val( '' );
				toggleRegistration( '0' , key );
				unregistered = true;
			}

            //* Passing in true forces reload from the server rather than cache.
            window.location.reload(true);
			clearLoadingScreen();
		});


		return unregistered;
	}

	function handleRegistration() {
		$( '.register-plugin' ).on( 'click', function() {
			var key = $(this).attr('swp-addon');
			var item_id = $(this).attr('swp-item-id');
			registerPlugin(key,item_id);
			return false;
		});

		$( '.unregister-plugin' ).on( 'click', function() {
			var key = $(this).attr('swp-addon');
			var item_id = $(this).attr('swp-item-id');
			unregisterPlugin(key,item_id);
			return false;
		});
	}

	/*******************************************************
		Make the buttons sortable
	*******************************************************/
	function sortableInit() {
		$( '.sw-buttons-sort.sw-active' ).sortable({
			connectWith: '.sw-buttons-sort.sw-inactive',
			update: function() {
				saveColorToggle();
			}
		});

		$( '.sw-buttons-sort.sw-inactive' ).sortable({
			connectWith: '.sw-buttons-sort.sw-active',
			update: function() {
				saveColorToggle();
			}
		});
	}

	function getSystemStatus() {
		$( '.sw-system-status' ).on( 'click', function( event ) {
			// Block the default action
			event.preventDefault ? event.preventDefault() : ( event.returnValue = false );

			$( '.system-status-wrapper' ).slideToggle();

			selectText( $( '.system-status-container' ).get( 0 ) );
		});
	}

	/*********************************************************
		A Function for image upload buttons
	*********************************************************/
	function customUploaderInit() {
		var customUploader;

		$( '.swp_upload_image_button' ).click(function( e ) {
			e.preventDefault();

			var inputField = $( this ).attr( 'for' );

			// If the uploader object has already been created, reopen the dialog
			if ( customUploader ) {
				customUploader.open();

				return;
			}

			// Extend the wp.media object
			customUploader = wp.media.frames.file_frame = wp.media({
				title: 'Choose Image',
				button: {
					text: 'Choose Image'
				},
				multiple: false
			});

			// When a file is selected, grab the URL and set it as the text field's value
			customUploader.on( 'select', function() {
				var attachment = customUploader.state().get( 'selection' ).first().toJSON();

				$( 'input[name="' + inputField + '"' ).val( attachment.url );
			});

			// Open the uploader dialog
			customUploader.open();
		});
	}

	function set_ctt_preview() {
        var preview = $("#ctt_preview");
        var select = $("select[name=ctt_theme]");

        if (!preview.length) {
        	preview = $('<style id="ctt_preview"></style>');
        	$("head").append(preview);
        }

        if ($(select).val() === "none") {
        	update_ctt_preview();
        }

        $(select).on("change", function(e) {
        	if (e.target.value === 'none') {
        		update_ctt_preview();
        	}
        });

        $("textarea[name=ctt_css]").on("keyup", update_ctt_preview);
	}

	function update_ctt_preview() {
		var preview = $("#ctt_preview");
        var textarea = $("textarea[name=ctt_css]");

        $(preview).text($(textarea).val());
	}

	$( document ).ready(function() {
		handleSettingSave();
		populateOptions();
		headerMenuInit();
		tabNavInit();
		checkboxesInit();
		updateButtonPreviews();
		swpConditionalFields();
		updateCttDemo();
		updateScale();
		handleRegistration();
		sortableInit();
		getSystemStatus();
		customUploaderInit();
		set_ctt_preview();
	});


})( this, jQuery );
