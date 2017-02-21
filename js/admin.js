var socialWarfareAdmin = socialWarfareAdmin || {};

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
			remaining = 140 - socialWarfareAdmin.linkLength( smTwitter ) - linkSpace;
		} else {
			remaining = 140 - socialWarfareAdmin.linkLength( smTwitter ) - handle.length - linkSpace - 6;
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

		// Show and Hide the Count Label based on if we're showing counts
		$( '.swp_popular_post_options .showCount select' ).on( 'change', function() {
			var value = $( this ).val();
			if ( value = true ) {
				$( '.swp_popular_post_options .countLabel' ).slideDown( 'slow' );
			} else {
				$( '.swp_popular_post_options .countLabel' ).slideUp( 'slow' );
			}
		});

		// Show and Hide the Thumbnail size based on if we're showing thmbnails
		$( '.swp_popular_post_options .thumbnails select' ).on( 'change', function() {
			var value = $( this ).val();
			if ( value = true ) {
				$( '.swp_popular_post_options .thumb_size' ).slideDown( 'slow' );
			} else {
				$( '.swp_popular_post_options .thumb_size' ).slideUp( 'slow' );
			}
		});

		// Show and Hide the Custom fields based on if we're using a custom color scheme
		$( '.swp_popular_post_options .style select' ).on( 'change', function() {
			var value = $( this ).val();
			if ( value = 'custom' ) {
				$( '.swp_popular_post_options .custom_bg, .swp_popular_post_options .custom_link' ).slideDown( 'slow' );
			} else {
				$( '.swp_popular_post_options .custom_bg, .swp_popular_post_options .custom_link' ).slideUp( 'slow' );
			}
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
	});
})( this, jQuery );
