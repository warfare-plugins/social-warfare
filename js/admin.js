var socialWarfareAdmin = socialWarfareAdmin || {};
$ = jQuery;

/*********************************************************
	A function to show/hide conditionals
*********************************************************/
function conditionalFields() {

	// Loop through all the fields that have dependancies
	$( 'div[dep],p[dep]' ).each( function() {
		// Fetch the conditional values
		var conDep = $( this ).attr( 'dep' );

		var conDepVal = $.parseJSON( $( this ).attr( 'dep_val' ) );
		var value;

		// Fetch the value of checkboxes or other input types
		if ( $( '[data-swp-name="' + conDep + '"]' ).attr( 'type' ) == 'checkbox' ) {
			value = $( '[data-swp-name="' + conDep + '"]' ).prop( 'checked' );
		} else {
			value = $( '[data-swp-name="' + conDep + '"]' ).val();
		}
		// console.log(value);
		console.log(value+':'+conDepVal);
		// Show or hide based on the conditional values (and the dependancy must be visible in case it is dependant)
		if ( $.inArray( value, conDepVal ) !== -1 && $( '[data-swp-name="' + conDep + '"]' ).parent( '.sw-grid,p' ).is( ':visible' ) ) {
			$( this ).show();
		} else {
			$( this ).hide();
		}
	});

	if ( swp_check_val('floatStyleSource') == false && (swp_select_val('sideDColorSet') == 'customColor' || swp_select_val('sideDColorSet') == 'ccOutlines' || swp_select_val('sideIColorSet') == 'customColor' || swp_select_val('sideIColorSet') == 'ccOutlines' || swp_select_val('sideOColorSet') == 'customColor' || swp_select_val('sideOColorSet') == 'ccOutlines') ) {
		$( '.sideCustomColor_wrapper' ).slideDown();
	} else {
		$( '.sideCustomColor_wrapper' ).slideUp();
	}
}

function swp_select_val(name) {
	return $('select[name="' + name + '"]').val();
}

function swp_check_val(name) {
	return $( '[name="' + name + '"]' ).prop( 'checked' );
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

		conditionalFields();
		$( '.swp_popular_post_options select' ).on( 'change', function() {
			conditionalFields();
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
							$( this ).stop( true ).fadeTo( 400, 1 );
						},
						function() {
							$( this ).fadeOut( '400', function() {
								$( this ).remove();
							});
						});
					}
				});
			}
		}

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
	});
})( this, jQuery );
