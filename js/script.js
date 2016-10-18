/*!
 * jQuery throttle / debounce - v1.1 - 3/7/2010
 * http://benalman.com/projects/jquery-throttle-debounce-plugin/
 *
 * Copyright (c) 2010 "Cowboy" Ben Alman
 * Dual licensed under the MIT and GPL licenses.
 * http://benalman.com/about/license/
 */

(function( window, undefined ) {
	'$:nomunge'; // Used by YUI compressor.

	// Since jQuery really isn't required for this plugin, use `jQuery` as the
	// namespace only if it already exists, otherwise use the `Cowboy` namespace,
	// creating it if necessary.
	var $ = window.jQuery || window.Cowboy || ( window.Cowboy = {} ),

	// Internal method reference.
	jq_throttle;

	$.swpThrottle = jq_throttle = function( delay, no_trailing, callback, debounce_mode ) {
		// After wrapper has stopped being called, this timeout ensures that
		// `callback` is executed at the proper times in `throttle` and `end`
		// debounce modes.
		var timeout_id,

		// Keep track of the last time `callback` was executed.
		last_exec = 0;

		// `no_trailing` defaults to falsy.
		if ( typeof no_trailing !== 'boolean' ) {
			debounce_mode = callback;
			callback = no_trailing;
			no_trailing = undefined;
		}

		// The `wrapper` function encapsulates all of the throttling / debouncing
		// functionality and when executed will limit the rate at which `callback`
		// is executed.
		function wrapper() {
			var that = this,
			elapsed = +new Date() - last_exec,
			args = arguments;

			// Execute `callback` and update the `last_exec` timestamp.
			function exec() {
				last_exec = +new Date();
				callback.apply( that, args );
			}

			// If `debounce_mode` is true (at_begin) this is used to clear the flag
			// to allow future `callback` executions.
			function clear() {
				timeout_id = undefined;
			}

			if ( debounce_mode && ! timeout_id ) {
				// Since `wrapper` is being called for the first time and
				// `debounce_mode` is true (at_begin), execute `callback`.
				exec();
			}

			// Clear any existing timeout.
			timeout_id && clearTimeout( timeout_id );

			if ( debounce_mode === undefined && elapsed > delay ) {
				// In throttle mode, if `delay` time has been exceeded, execute
				// `callback`.
				exec();
			} else if ( no_trailing !== true ) {
				// In trailing throttle mode, since `delay` time has not been
				// exceeded, schedule `callback` to execute `delay` ms after most
				// recent execution.
				//
				// If `debounce_mode` is true (at_begin), schedule `clear` to execute
				// after `delay` ms.
				//
				// If `debounce_mode` is false (at end), schedule `callback` to
				// execute after `delay` ms.
				timeout_id = setTimeout( debounce_mode ? clear : exec, debounce_mode === undefined ? delay - elapsed : delay );
			}
		}

		// Set the guid of `wrapper` function to the same of original callback, so
		// it can be removed in jQuery 1.4+ .unbind or .die by using the original
		// callback as a reference.
		if ( $.guid ) {
			wrapper.guid = callback.guid = callback.guid || $.guid++;
		}

		// Return the wrapper function.
		return wrapper;
	};

	$.swpDebounce = function( delay, at_begin, callback ) {
		return callback === undefined ? jq_throttle( delay, at_begin, false ) : jq_throttle( delay, callback, at_begin !== false );
	};
})( this );

/* global swpPinIt */
var socialWarfarePlugin = socialWarfarePlugin || {};

(function( window, $, undefined ) {
	'use strict';

	function absint( $int ) {
		return parseInt( $int, 10 );
	}

	/****************************************************************************

		Fetch and Store Facebook Counts

	****************************************************************************/

	socialWarfarePlugin.fetchFacebookShares = function() {
		var requestUrl = 'https://graph.facebook.com/?id=' + swp_post_url;
		$.get( requestUrl, function( response ) {
			//response = $.parseJSON(data);
			var requestUrTwo = 'https://graph.facebook.com/?id=' + swp_post_url + '&fields=og_object{likes.summary(true),comments.summary(true)}';
			$.get( requestUrTwo, function( responseTwo ) {
				var shares, likes, comments, activity;

				//responseTwo = $.parseJSON(data);
				shares = absint( response.share.share_count );
				likes = absint( responseTwo.og_object.likes.summary.total_count );
				comments = absint( responseTwo.og_object.comments.summary.total_count );
				activity = shares + likes + comments;
				console.log( activity );

				swpPostData = {
					action: 'swp_facebook_shares_update',
					post_id: swp_post_id,
					activity: activity
				};

				$.post( swp_admin_ajax, swpPostData, function( response ) {
					console.log( response );
				});
			});
		});
	}

	function createFloatBar() {
		if ( ! $( '.nc_socialPanelSide' ).length ) {
			if( $( '.nc_wrapper' ).length ) {
				$( '.nc_wrapper' ).remove();
			}
			var firstSocialPanel = $( '.nc_socialPanel' ).not( '[data-float="float_ignore"]' ).first();
			var index = $( '.nc_socialPanel' ).index( firstSocialPanel );
			var floatOption = firstSocialPanel.attr( 'data-float' );
			var alignment = firstSocialPanel.attr( 'data-align' );
			if ( floatOption ) {
				var backgroundColor = $( '.nc_socialPanel' ).attr( 'data-floatColor' );
				$( '<div class="nc_wrapper" style="background-color:' + backgroundColor + '"></div>' ).appendTo( 'body' );
				var position = firstSocialPanel.attr( 'data-float' );
				firstSocialPanel.clone().appendTo( '.nc_wrapper' );
				$( '.nc_wrapper' ).hide().addClass( position );
				var width = firstSocialPanel.outerWidth( true );
				var offset = firstSocialPanel.offset();
				$( '.nc_socialPanel' ).last().addClass( 'nc_floater' ).css({
					width: width,
					left: ( alignment == 'center' ? 0 : offset.left )
				});
				$( '.nc_socialPanel .swp_count' ).css({ transition: 'padding .1s linear' });
				$( '.nc_socialPanel' ).eq( 0 ).addClass( 'swp_one' );
				$( '.nc_socialPanel' ).eq( 2 ).addClass( 'swp_two' );
				$( '.nc_socialPanel' ).eq( 1 ).addClass( 'swp_three' );
			}
		}
	}

	function floatingBarReveal() {
		// Adjust the floating bar
		var panels = $( '.nc_socialPanel' );
		var floatOption = panels.not( '[data-float="float_ignore"]' ).eq( 0 ).attr( 'data-float' );
		var windowElement = $( window );
		var windowHeight = windowElement.height();
		var ncWrapper = $( '.nc_wrapper' );
		var ncSideFloater = $( '.nc_socialPanelSide' ).filter( ':not(.mobile)' );
		var position = $( '.nc_socialPanel' ).attr( 'data-position' );
		var minWidth = ncSideFloater.attr( 'data-screen-width' );
		var offsetOne = panels.eq( 0 ).offset();
		var scrollPos = windowElement.scrollTop();
		var st = $( window ).scrollTop();
		if ( typeof window.swpOffsets == 'undefined' ) {
			window.swpOffsets = {};
		}

		var visible = false;

		if ( floatOption == 'floatBottom' || floatOption == 'floatTop' ) {
			$( '.nc_socialPanel' ).not( '.nc_socialPanelSide, .nc_wrapper .nc_socialPanel' ).each(function() {
				var thisOffset, thisHeight, screenBottom;

				var index = $( '.nc_socialPanel' ).index( $( this ) );

				// Fetch our base numbers
				if ( typeof window.swpOffsets[index] == 'undefined' ) {
					thisOffset   = $( this ).offset();
					thisHeight   = $( this ).height();
					screenBottom = thisOffset + thisHeight;
					window.swpOffsets[index] = thisOffset;
				} else {
					thisOffset   = window.swpOffsets[index];
					thisHeight   = $( this ).height();
					screenBottom = thisOffset + thisHeight;
				}

				// Check if it's visible
				if ( thisOffset.top + thisHeight > scrollPos && thisOffset.top < scrollPos + windowHeight ) {
					visible = true;
				}
			});
			if ( visible ) {
				// Hide the Floating bar
				ncWrapper.hide();

				// Add some padding to the page so it fits nicely at the top or bottom
				if ( floatOption == 'floatBottom' ) {
					$( 'body' ).animate({ 'padding-bottom': window.bodyPaddingBottom + 'px' }, 0 );
				} else if ( floatOption == 'floatTop' ) {
					$( 'body' ).animate({ 'padding-top': window.bodyPaddingTop + 'px' }, 0 );
				}
			} else {
				var newPadding, firstOffset;
				// Show the floating bar
				ncWrapper.show();

				// Add some padding to the page so it fits nicely at the top or bottom
				if ( floatOption == 'floatBottom' ) {
					newPadding = window.bodyPaddingBottom + 50;
					$( 'body' ).animate({ 'padding-bottom': newPadding + 'px' }, 0 );
				} else if ( floatOption == 'floatTop' ) {
					firstOffset = $( '.nc_socialPanel' ).not( '.nc_socialPanelSide, .nc_wrapper .nc_socialPanel' ).first().offset();
					console.log( firstOffset );
					if ( firstOffset.top > scrollPos + windowHeight ) {
						newPadding = window.bodyPaddingTop + 50;
						$( 'body' ).animate({ 'padding-top': newPadding + 'px' }, 0 );
					}
				}
			}
		} else if ( floatOption == 'floatLeft' ) {
			visible = false;
			if ( $( '.nc_socialPanel' ).not( '.nc_socialPanelSide' ).length ) {
				$( '.nc_socialPanel' ).not( '.nc_socialPanelSide' ).each(function() {
						var thisOffset = $( this ).offset();
						var thisHeight = $( this ).height();
						if ( thisOffset.top + thisHeight > scrollPos && thisOffset.top < scrollPos + windowHeight ) {
							visible = true;
						}
					});
				if ( visible || $( '.nc_socialPanelSide' ).hasClass( 'mobile' ) ) {
					visible = true;
				} else {
					visible = false;
				}
			} else {
				if ( $( window ).width() > minWidth ) {
					visible = false;
				} else {
					visible = true;
				}
			}

			var transition = ncSideFloater.attr( 'data-transition' );
			if ( transition == 'slide' ) {
				if ( visible == true ) {
					ncSideFloater.css({ left: '-100px' }, 200 );
				} else {
					ncSideFloater.css({ left: '5px' });
				}
			} else if ( transition == 'fade' ) {
				if ( visible == true ) {
					ncSideFloater.fadeOut( 200 );
				} else {
					ncSideFloater.fadeIn( 200 );
				}
			}
		}
	}

	function initShareButtons() {
		if ( 0 !== $( '.nc_socialPanel' ).length ) {
			createFloatBar();
			swp_activate_hover_states();
			$( window ).scrollTop();
			$( window ).scroll( $.swpThrottle( 250, function() {
				floatingBarReveal();
			}));

			$( window ).trigger( 'scroll' );
		}
	}

	/****************************************************************************

		Pin It Hover Effect

	****************************************************************************/

	function pinitButton() {
		var defaults = {
			wrap: '<div class="sw-pinit" />',
			pageURL: document.URL
		};

		var options = $.extend( defaults, options );

		// Iterate over the current set of matched elements.
		$( '.swp-content-locator' ).parent().find( 'img' ).each( function() {
			var $image = $( this );

			if ( $image.outerHeight() < swpPinIt.minHeight || $image.outerWidth() < swpPinIt.minWidth ) {
				return;
			}

			var pinMedia = false;

			if ( $image.data( 'media' ) ) {
				pinMedia = $image.data( 'media' );
			} else if ( $image[0].src ) {
				pinMedia = $image[0].src;
			}

			// Bail if we don't have any media to pin.
			if ( ! pinMedia ) {
				return;
			}

			var pinDesc = '';

			if ( $image.attr( 'title' ) ) {
				pinDesc = $image.attr( 'title' );
			} else if ( $image.attr( 'alt' ) ) {
				pinDesc = $image.attr( 'alt' );
			}

			var bookmark = 'http://pinterest.com/pin/create/bookmarklet/?media=' + pinMedia + '&url=' + options.pageURL + '&is_video=false' + '&description=' + encodeURIComponent( pinDesc );
			var imageClasses = $image.attr( 'class' );
			var imageStyle = $image.attr( 'style' );

			$image.removeClass().attr( 'style', '' ).wrap( options.wrap );

			$image.after( '<a href="' + encodeURI( bookmark ) + '" class="sw-pinit-button sw-pinit-' + swpPinIt.vLocation + ' sw-pinit-' + swpPinIt.hLocation + '">Save</a>' );

			$image.parent( '.sw-pinit' ).addClass( imageClasses ).attr( 'style', imageStyle );

			$( '.sw-pinit .sw-pinit-button' ).on( 'click', function() {
				window.open( $( this ).attr( 'href' ), 'Pinterest', 'width=632,height=253,status=0,toolbar=0,menubar=0,location=1,scrollbars=1' );
				return false;
			});
		});
	}

	function resetCache() {
		// Reset the cache
		if ( 'undefined' !== typeof swpCacheURL ) {
			var urlParams;

			// If the URL Contains a question mark already
			if ( swpCacheURL.indexOf( '?' ) != -1 ) {
				urlParams = '&swp_cache=rebuild';
			} else {
				urlParams = '?swp_cache=rebuild';
			}

			$.get( swpCacheURL + urlParams );
		}
	}

	function handleWindowOpens() {
		$( '.nc_tweet, a.swp_CTT' ).on( 'click', function( event ) {
			if ( $( this ).hasClass( 'noPop' ) ) {
				return false;
			}

			if( $( this ).attr( 'data-link' ) ) {
				event.preventDefault ? event.preventDefault() : ( event.returnValue = false );

				var href = $( this ).attr( 'data-link' );
				var height, width, instance;

				href = href.replace( '’', '\'' );

				if ( $( this ).hasClass( 'pinterest' ) || $( this ).hasClass( 'buffer_link' ) || $( this ).hasClass( 'flipboard' ) ) {
					height = 550;
					width = 775;
				} else {
					height = 270;
					width = 500;
				}

				instance = window.open( href, '_blank', 'height=' + height + ',width=' + width );

				return false;
			}
		});
	}

	/**
	 * Activate Hover States: Trigger the resizes to the proper widths for the expansion on hover effect
	 * @since 2.1.0
	 * @todo Made the mouseenter/mouseleave events detect the hovering of the floating buttons on the bottom
	 * @param none
	 * @return none
	 */
	function swp_activate_hover_states() {
		$('.nc_socialPanel:not(.nc_socialPanelSide) .nc_tweetContainer').on('mouseenter',function(){
			console.log('hello world');
			var term_width = $(this).find('.swp_share').width();
			var icon_width = $(this).find('i.sw').outerWidth();
			var container_width = $(this).width();
			var percentage_change = 1 + ((term_width + 35) / container_width);
			$(this).find('.iconFiller').width(term_width + icon_width + 25 + 'px');
			$(this).css({flex:percentage_change + ' 1 0%'});
		});
		$('.nc_socialPanel:not(.nc_socialPanelSide) .nc_tweetContainer').on('mouseleave',function(){
			$(this).find('.iconFiller').width('30px');
			$(this).css({flex:'1'});
		});
	}

	$( document ).ready( function() {
		handleWindowOpens();

		// Fetch the padding amount to make space later for the floating bars
		window.bodyPaddingTop = absint( $( 'body' ).css( 'padding-top' ).replace( 'px', '' ) );
		window.bodyPaddingBottom = absint( $( 'body' ).css( 'padding-bottom' ).replace( 'px', '' ) );

		$( window ).resize( $.swpDebounce( 250, function() {
			if ( $( '.nc_socialPanel' ).length && $( '.nc_socialPanel:hover' ).length !== 0 ) { } else {
				window.swpAdjust = 1;
				initShareButtons();
			}
		}));

		$( window ).trigger( 'resize' );

		$( document.body ).on( 'post-load', function() {
			initShareButtons();
		});

		if ( 0 !==  $( '.nc_socialPanelSide' ).length ) {
			var buttonsHeight = $( '.nc_socialPanelSide' ).height();
			var windowHeight = $( window ).height();
			var newPosition = absint( ( windowHeight / 2 ) - ( buttonsHeight / 2 ) );
			setTimeout( function() {
				$( '.nc_socialPanelSide' ).animate({ top: newPosition }, 0 );
				console.log( newPosition );
			}, 105 );
		}

		resetCache();

		if ( swpPinIt.enabled ) {
			pinitButton();
		}
	});
})( this, jQuery );
