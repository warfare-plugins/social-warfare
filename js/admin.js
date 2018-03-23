/**
*
* Functions for widgets and global utility functions.
*
* @since 1.0.0
* @package   SocialWarfare\Admin\Functions
* @copyright Copyright (c) 2018, Warfare Plugins, LLC
* @license   GPL-3.0+
*/

var socialWarfareAdmin = socialWarfareAdmin || {};
var swpWidget, widgetSubmit;

if (typeof $ === 'undefined') {
	$ = jQuery;
}

/**
* Show and hide input fields based on conditional values.
*
* This function iterates over each element with the "dep" data attribute. For each
* such dependant element, its parent element controls whether the dependant is shown or hidden
* if the parent's value matches the condition.
*
* @since 2.4.0 Feb 12 2018 | Brought func in from admin-options-page.js and set to global scope; Updated variable names for semantics, switched to Yoda condietionals.
* @since 2.4.0 Feb 14 2018 | Mapped the required array from variable types to string.
*
* @see admin-options-page.js
* @return none
*/
function swpConditionalFields() {

	function swp_selected(name) {
		return $('select[name="' + name + '"]').val();
	}

	function swp_checked(name) {
		return $( '[name="' + name + '"]' ).prop( 'checked' );
	}

	function string_to_bool(string) {
		if( string === 'true') { string = true };
		if( string === 'false') { string = false };
		return string;
	}

	// Loop through all the fields that have dependancies
	$( '[data-dep]' ).each( function() {

		// Fetch the conditional values
		var condition = $(this).data( 'dep' );
		console.log(condition);
		console.log('[data-swp-name="' + condition + '"]');
		console.log($('[data-swp-name=\'' + condition + '\']'));
		var required = JSON.parse( JSON.stringify( $(this).data( 'dep_val' ) ) );

		// Check if we're on the options page or somewhere else
		if (window.location.href.indexOf("page=social-warfare") === -1) {
			var conditionEl = $(this).parents('.widgets-holder-wrap').find( '[data-swp-name="' + condition + '"]' );
		} else {
			var conditionEl = $( '[name="' + condition + '"]' )[0];
		}

		console.log(typeof conditionEl);

		var value;

		if (typeof conditionEl === 'undefined') {
			console.log("Missed it the first time");
			conditionEl = $( '[name="' + condition + '"]' )[0];

			if (typeof conditionEl === 'undefined') {
				console.log("Still dont' have it");
				console.log(condition);
				conditionEl = $( '[field$=' + condition + ']' )[0];
				console.log(conditionEl);
			}
		}

		// Fetch the value of checkboxes or other input types
		if ( $( conditionEl ).attr( 'type' ) == 'checkbox' ) {
			value = $( conditionEl ).prop( 'checked' );
		} else {
			value = $( conditionEl ).val();
		}
		value = string_to_bool(value);

		if ( $(this).hasClass('custom_thumb_size') ) {
			console.log(conditionEl);
			console.log(typeof required);
			console.log(required);
			console.log(typeof value);
			console.log(value);
		}

        // *Options page uses parent visibilty to check. Widget page does not. This could definiitely look better.
		// Show or hide based on the conditional values (and the dependancy must be visible in case it is dependant)

		if (window.location.href.indexOf("page=social-warfare") !== -1) {

			// If the required value matches and it's parent is also being shown, show this conditional field
			if ($.inArray( value, required ) !== -1 && $( conditionEl ).parent( '.sw-grid' ).is( ':visible' )  ) {
				$(this).show();
			} else {
				$(this).hide();
			}
		} else {

			// If the required value matches, show this conditional field
			if ($.inArray( value, required ) !== -1 || value === required ) {
				$(this).show();
			} else {
				$(this).hide();
			}
		}
	});

	if ( false === swp_checked('floatStyleSource') &&
	       'customColor' === swp_selected('sideDColorSet')
	    || 'ccOutlines'  === swp_selected('sideDColorSet')
	    || 'customColor' === swp_selected('sideIColorSet')
	    || 'ccOutlines'  === swp_selected('sideIColorSet')
	    || 'customColor' === swp_selected('sideOColorSet')
        || 'ccOutlines'  === swp_selected('sideOColorSet') ) {
		$( '.sideCustomColor_wrapper' ).slideDown();

	} else {
		$( '.sideCustomColor_wrapper' ).slideUp();
	}
}

// *Only run on widgets.php
if (window.location.href.indexOf("widgets.php")) {

	// *Make sure the elements exist before trying to read them.
	var widgetFinder = setInterval(function() {
		if (typeof swpWidget !== 'undefined') clearInterval(widgetFinder);

		swpWidget = $("#widgets-right [id*=_swp_popular_posts_widget], [id*=_swp_popular_posts_widget].open")[0];
		widgetSubmit = $(swpWidget).find("[id$=savewidget]")[0];

        // *Force swpConditionalFields to run when the widget is opened or saved.
		$(swpWidget).on("click", swpConditionalFields);
		$(widgetSubmit).on("click", function() {
			setTimeout(swpConditionalFields, 600);
		});

	}, 50);
}

(function( window, $, undefined ) {
	'use strict';

	socialWarfareAdmin.linkLength = function( input ) {
		var tmp = '';

		for ( var i = 0; i < 23; i++ ) {
			tmp += 'o';
		}

		return input.replace( /(http:\/\/[\S]*)/g, tmp ).length;
	};

	// Function for SM Title Counting
	function smTitleRemaining() {
		var smTitle = $( '#socialWarfare textarea#nc_ogTitle' ).val();
		var remaining = 60 - smTitle.length;
		if ( smTitle.length > 0 && remaining >= 0 ) {
			$( '#socialWarfare .nc_ogTitleWrapper .swp_CountDown' ).removeClass( 'swp_red' ).addClass( 'swp_blue' );
		} else if ( smTitle.length > 0 && remaining < 0 ) {
			$( '#socialWarfare .nc_ogTitleWrapper .swp_CountDown' ).removeClass( 'swp_blue' ).addClass( 'swp_red' );
		} else {
			$( '#socialWarfare .nc_ogTitleWrapper .swp_CountDown' ).removeClass( 'swp_blue' ).removeClass( 'swp_red' );
		}
		$( '#socialWarfare .nc_ogTitleWrapper .counterNumber' ).html( remaining );
	}

	// Function for SM Description Counting
	function smDescriptionRemaining() {
		var smDescription = $( '#socialWarfare textarea#nc_ogDescription' ).val();
		var remaining = 160 - smDescription.length;
		if ( smDescription.length > 0 && remaining >= 0 ) {
			$( '#socialWarfare .nc_ogDescriptionWrapper .swp_CountDown' ).removeClass( 'swp_red' ).addClass( 'swp_blue' );
		} else if ( smDescription.length > 0 && remaining < 0 ) {
			$( '#socialWarfare .nc_ogDescriptionWrapper .swp_CountDown' ).removeClass( 'swp_blue' ).addClass( 'swp_red' );
		} else {
			$( '#socialWarfare .nc_ogDescriptionWrapper .swp_CountDown' ).removeClass( 'swp_blue' ).removeClass( 'swp_red' );
		}
		$( '#socialWarfare .nc_ogDescriptionWrapper .counterNumber' ).html( remaining );
	}

	// Function for Twitter Box Counting
	function twitterRemaining() {
		var smTwitter = $( '#socialWarfare textarea#nc_customTweet' ).val();
		var handle = $( '#socialWarfare .twitterIDWrapper label' ).html();
		var linkSpace;

		if ( smTwitter.indexOf( 'http' ) > -1 || smTwitter.indexOf( 'https' ) > -1 ) {
			linkSpace = 0;
			$( '.tweetLinkSection' ).css({ 'text-decoration': 'line-through' });
		} else {
			linkSpace = 23;
			$( '.tweetLinkSection' ).css({ 'text-decoration': 'none' });
		}

		var remaining;
		if ( 'undefined' === typeof handle ) {
			remaining = 280 - socialWarfareAdmin.linkLength( smTwitter ) - linkSpace;
		} else {
			remaining = 280 - socialWarfareAdmin.linkLength( smTwitter ) - linkSpace - handle.length - 6;
		}

		if ( smTwitter.length > 0 && remaining >= 0 ) {
			$( '#socialWarfare .nc_customTweetWrapper .swp_CountDown' ).removeClass( 'swp_red' ).addClass( 'swp_blue' );
		} else if ( smTwitter.length > 0 && remaining < 0 ) {
			$( '#socialWarfare .nc_customTweetWrapper .swp_CountDown' ).removeClass( 'swp_blue' ).addClass( 'swp_red' );
		} else {
			$( '#socialWarfare .nc_customTweetWrapper .swp_CountDown' ).removeClass( 'swp_blue' ).removeClass( 'swp_red' );
		}

		$( '#socialWarfare .nc_customTweetWrapper .counterNumber' ).html( remaining );
	}

	function toggleCustomThumbnailFields(show) {
		if (typeof show === 'undefined') show = true;

		if (show) {
			$(".custom_thumb_size").show();
		}
        else {
        	$(".custom_thumb_size").hide();
        }
	}

	$( document ).ready( function() {
		if ( $( '#socialWarfare.postbox' ).length ) {

			// Add the CountDown Box for the Social Media Title
			$( '#socialWarfare #nc_ogTitle' ).parent().prepend( '<div class="swp_CountDown"><span class="counterNumber">60</span> ' + swp_localize_admin.swp_characters_remaining + '</div>' );

			// Add the CountDown Box for the Social Media Description
			$( '#socialWarfare #nc_ogDescription' ).parent().prepend( '<div class="swp_CountDown"><span class="counterNumber">150</span> ' + swp_localize_admin.swp_characters_remaining + '</div>' );

			// Add the CountDown Box for the Twitter Box
			$( '#socialWarfare #nc_customTweet' ).parent().prepend( '<div class="swp_CountDown"><span class="counterNumber">118</span> ' + swp_localize_admin.swp_characters_remaining + '</div>' );

			smTitleRemaining();
			$( '#socialWarfare textarea#nc_ogTitle' ).on( 'input', function() {
				smTitleRemaining();
			});

			smDescriptionRemaining();
			$( '#socialWarfare textarea#nc_ogDescription' ).on( 'input', function() {
				smDescriptionRemaining();
			});

			twitterRemaining();
			$( '#socialWarfare textarea#nc_customTweet' ).on( 'input', function() {
				twitterRemaining();
			});

			// Setup an initilazation loop
			var swpPostInit = setInterval( function() {
				var swpOgImage  = $( '.nc_ogImageWrapper ul.swpmb-media-list' );
				var swpPinImage = $( '.nc_pinterestImageWrapper ul.swpmb-media-list' );

				var smWidth, smHeight;

				// Check if the media list has been created yet
				if ( swpOgImage.length && swpOgImage.is( ':empty' ) ) {
					// Setup the Open Graph Image Placeholder
					smWidth = swpOgImage.width();
					smHeight = smWidth * ( 9 / 16 );
					swpOgImage.css({ height: smHeight + 'px' });
				} else {
					smHeight = swpOgImage.find( 'img' ).height();
					swpOgImage.css({ height: smHeight + 'px' });
				}

				var pinWidth, pinHeight;

				if ( swpPinImage.length && swpPinImage.is( ':empty' ) ) {
					// Setup the Open Graph Image Placeholder
					pinWidth = swpPinImage.width();
					pinHeight = pinWidth * ( 3 / 2 );
					swpPinImage.css({ height: pinHeight + 'px' });
				} else {
					pinHeight = swpPinImage.find( 'img' ).height();
					swpPinImage.css({
						height: pinHeight + 'px'
					});
				}
			}, 1000 );
		}

		swpConditionalFields();
		$( '.swp_popular_post_options select' ).on( 'change', function() {
			swpConditionalFields();
		});

		if ( $( '.postbox#socialWarfare' ).length ) {
			var registrationStatus = $( '#socialWarfare .registrationWrapper input' ).attr( 'id' );
			if ( registrationStatus == 'false' ) {
				$( '.postbox#socialWarfare' )
					.css({ position: 'relative',opacity: '0.3' })
					.append( '<div class="sw-premium-blocker"></div>' );

				$( '#socialWarfare .sw-premium-blocker' ).tooltip({
					items: '#socialWarfare .sw-premium-blocker',
					content: '<i></i>Unlock these features by registering your license.',
					position: {
						my: 'center top',
						at: 'center top'
					},
					tooltipClass: 'sw-admin-hover-notice',
					open: function( event, ui ) {
						if ( 'undefined' === typeof ( event.originalEvent ) ) {
							return false;
						}

						var $id = $( ui.tooltip ).attr( 'id' );

						// close any lingering tooltips
						$( 'div.ui-tooltip' ).not( '#' + $id ).remove();

						// ajax function to pull in data and add it to the tooltip goes here
					},
					close: function( event, ui ) {
						ui.tooltip.hover(function() {
							$(this).stop( true ).fadeTo( 400, 1 );
						},
						function() {
							$(this).fadeOut( '400', function() {
								$(this).remove();
							});
						});
					}
				});
			}
		}

		/*
		var customThumbnailSelect = $("#widget-swp_popular_posts_widget-2-thumb_size");

		if (customThumbnailSelect.value === 'custom') {
			toggleCustomThumbnailFields();
		}

		$(customThumbnailSelect).on("change", function(e) {
			console.log("changing");
			console.log(e.target.value);
            if (e.target.value === 'custom') {
            	toggleCustomThumbnailFields();
            } else {
            	toggleCustomThumbnailFields(false);
            }
		});
		*/
	});
})( this, jQuery );
