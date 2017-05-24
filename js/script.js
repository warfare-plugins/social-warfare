/* global swpPinIt */

/*!
 * jQuery throttle / debounce - v1.1 - 3/7/2010
 * http://benalman.com/projects/jquery-throttle-debounce-plugin/
 *
 * Copyright (c) 2010 "Cowboy" Ben Alman
 * Dual licensed under the MIT and GPL licenses.
 * http://benalman.com/about/license/
 */

var socialWarfarePlugin = socialWarfarePlugin || {};

(function( window, undefined ) {
	// Internal method reference.
	var jqThrottle;

	var swp = window.socialWarfarePlugin;

	swp.throttle = jqThrottle = function( delay, noTrailing, callback, debounceMode ) {
		// After wrapper has stopped being called, this timeout ensures that
		// `callback` is executed at the proper times in `throttle` and `end`
		// debounce modes.
		var timeoutID,

		// Keep track of the last time `callback` was executed.
		lastExec = 0;

		// `noTrailing` defaults to falsy.
		if ( typeof noTrailing !== 'boolean' ) {
			debounceMode = callback;
			callback = noTrailing;
			noTrailing = undefined;
		}

		// The `wrapper` function encapsulates all of the throttling / debouncing
		// functionality and when executed will limit the rate at which `callback`
		// is executed.
		function wrapper() {
			var that = this,
			elapsed = +new Date() - lastExec,
			args = arguments;

			// Execute `callback` and update the `lastExec` timestamp.
			function exec() {
				lastExec = +new Date();
				callback.apply( that, args );
			}

			// If `debounceMode` is true (atBegin) this is used to clear the flag
			// to allow future `callback` executions.
			function clear() {
				timeoutID = undefined;
			}

			if ( debounceMode && ! timeoutID ) {
				// Since `wrapper` is being called for the first time and
				// `debounceMode` is true (atBegin), execute `callback`.
				exec();
			}

			// Clear any existing timeout.
			timeoutID && clearTimeout( timeoutID );

			if ( debounceMode === undefined && elapsed > delay ) {
				// In throttle mode, if `delay` time has been exceeded, execute
				// `callback`.
				exec();
			} else if ( noTrailing !== true ) {
				// In trailing throttle mode, since `delay` time has not been
				// exceeded, schedule `callback` to execute `delay` ms after most
				// recent execution.
				//
				// If `debounceMode` is true (atBegin), schedule `clear` to execute
				// after `delay` ms.
				//
				// If `debounceMode` is false (at end), schedule `callback` to
				// execute after `delay` ms.
				timeoutID = setTimeout( debounceMode ? clear : exec, debounceMode === undefined ? delay - elapsed : delay );
			}
		}

		// Set the guid of `wrapper` function to the same of original callback, so
		// it can be removed in jQuery 1.4+ .unbind or .die by using the original
		// callback as a reference.
		if ( swp.guid ) {
			wrapper.guid = callback.guid = callback.guid || swp.guid++;
		}

		// Return the wrapper function.
		return wrapper;
	};

	swp.debounce = function( delay, atBegin, callback ) {
		return callback === undefined ? jqThrottle( delay, atBegin, false ) : jqThrottle( delay, callback, atBegin !== false );
	};
})( this );

(function( window, $, undefined ) {
	'use strict';

	var swp = window.socialWarfarePlugin;

	function absint( $int ) {
		return parseInt( $int, 10 );
	}

	function swp_trigger_events(event) {
		var evt = $.Event(event);
		$(window).trigger(evt);
	}

	/****************************************************************************

		Fetch and Store Facebook Counts

	****************************************************************************/
	var swpPostData = {};
	socialWarfarePlugin.fetchShares = function() {
		/**
		 * Run all the API calls
		 */
		$.when(
			$.get('https://graph.facebook.com/?id=' + swp_post_url) ,
			$.get('https://graph.facebook.com/?id=' + swp_post_url + '&fields=og_object{likes.summary(true),comments.summary(true)}') ,
			( swp_post_recovery_url ? $.get('https://graph.facebook.com/?id=' + swp_post_recovery_url) : ''),
			( swp_post_recovery_url ? $.get('https://graph.facebook.com/?id=' + swp_post_recovery_url + '&fields=og_object{likes.summary(true),comments.summary(true)}') : '')
		)
		.then( function(a, b, c, d) {
			/**
			 * Parse the responses, add up the activity, send the results to admin_ajax
			 */
			if( 'undefined' !== typeof a[0].share && 'undefined' !== typeof b[0].og_object ) {
				var f1 = absint( a[0].share.share_count);
				var f2 = absint( b[0].og_object.likes.summary.total_count );
				var f3 = absint( b[0].og_object.comments.summary.total_count );
				var fShares = f1 + f2 + f3;
				if(swp_post_recovery_url) {
					if (typeof c[0].share !== 'undefined') {
						var f4 = absint( c[0].share.share_count);
					} else {
						var f4 = 0;
					}
					if (typeof d[0].og_object !== 'undefined') {
						var f5 = absint( d[0].og_object.likes.summary.total_count );
						var f6 = absint( d[0].og_object.comments.summary.total_count );
					} else {
						var f5 = 0, f6 = 0;
					}
					var fShares2 = f4 + f5 + f6;
					if (fShares !== fShares2) {
						fShares = fShares + fShares2;
					}
				}
				swpPostData = {
					action: 'swp_facebook_shares_update',
					post_id: swp_post_id,
					activity: fShares
				};

				$.post( swp_admin_ajax, swpPostData, function( response ) {
					console.log( 'Facebook Shares Response: ' + response );
				});
			}
		});
	}

	/**
	 * Activate Hover States: Trigger the resizes to the proper widths for the expansion on hover effect
	 * @since 2.1.0
	 * @param none
	 * @return none
	 */
	swp.activateHoverStates = function() {
		swp_trigger_events('pre_activate_buttons');
		$('.nc_socialPanel:not(.nc_socialPanelSide) .nc_tweetContainer:not(.swp_nohover)').on('mouseenter',function(){
			swpRestoreSizes();
			var term_width = $(this).find('.swp_share').outerWidth();
			var icon_width = $(this).find('i.sw').outerWidth();
			var container_width = $(this).width();
			var percentage_change = 1 + ((term_width + 35) / container_width);
			$(this).find('.iconFiller').width(term_width + icon_width + 25 + 'px');
			$(this).css({flex:percentage_change + ' 1 0%'});
		});
		$('.nc_socialPanel:not(.nc_socialPanelSide)').on('mouseleave',function() {
			swpRestoreSizes();
		});
	}
	function swpRestoreSizes() {
		$('.nc_socialPanel:not(.nc_socialPanelSide) .nc_tweetContainer:not(.swp_nohover) .iconFiller').removeAttr('style');
		$('.nc_socialPanel:not(.nc_socialPanelSide) .nc_tweetContainer:not(.swp_nohover)').removeAttr('style');
	}
	function createFloatBar() {
		//if ( ! $( '.nc_socialPanelSide' ).length ) {
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
				$( '.nc_wrapper' ).hide().addClass( (position == 'floatLeft' ? 'floatBottom' : position) );
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
		//}
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
		if ( floatOption == 'floatLeft' ) {
			var floatLeftMobile = $( '.nc_socialPanelSide' ).attr( 'data-mobileFloat' );
			if ( $( '.nc_socialPanel' ).not( '.nc_socialPanelSide' ).length ) {
				$( '.nc_socialPanel' ).not( '.nc_socialPanelSide, .nc_floater' ).each(function() {
						var thisOffset = $( this ).offset();
						var thisHeight = $( this ).height();
						if ( thisOffset.top + thisHeight > scrollPos && thisOffset.top < scrollPos + windowHeight ) {
							visible = true;
						}
					});
				if ( offsetOne.left < 100 || $( window ).width() < minWidth ) {
					visible = true;
					if ( floatLeftMobile == 'bottom' ) {
						floatOption = 'floatBottom';
					}
				} else if (visible) {
					visible == true;
				} else {
					visible = false;
				}
			} else {
				if ( $( window ).width() > minWidth ) {
					visible = false;
				} else {
					visible = true;
					if(floatLeftMobile == 'bottom') {
						floatOption = 'floatBottom';
					}
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
		if ( floatOption == 'floatBottom' || floatOption == 'floatTop' ) {
			visible = false;
			$( '.nc_socialPanel' ).not( '.nc_socialPanelSide, .nc_floater' ).each(function() {
					var thisOffset = $( this ).offset();
					var thisHeight = $( this ).height();
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
				swp_trigger_events('floating_bar_revealed');

				// Add some padding to the page so it fits nicely at the top or bottom
				if ( floatOption == 'floatBottom' ) {
					newPadding = window.bodyPaddingBottom + 50;
					$( 'body' ).animate({ 'padding-bottom': newPadding + 'px' }, 0 );
				} else if ( floatOption == 'floatTop' ) {
					firstOffset = $( '.nc_socialPanel' ).not( '.nc_socialPanelSide, .nc_wrapper .nc_socialPanel' ).first().offset();
					if ( firstOffset.top > scrollPos + windowHeight ) {
						newPadding = window.bodyPaddingTop + 50;
						$( 'body' ).animate({ 'padding-top': newPadding + 'px' }, 0 );
					}
				}
			}
		}
	}

	function initShareButtons() {
		if ( 0 !== $( '.nc_socialPanel' ).length ) {
			createFloatBar();
			swp.activateHoverStates();
			handleWindowOpens();
			$( window ).scrollTop();
			$( window ).scroll( swp.throttle( 50, function() {
				floatingBarReveal();
			}));
			$( window ).trigger( 'scroll' );
			$( '.nc_socialPanel' ).css( {'opacity':1} );
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
			} else if ( $(this).attr('data-lazy-src') ) {
			    pinMedia = $(this).attr('data-lazy-src');
			} else if ( $image[0].src ) {
				pinMedia = $image[0].src;
			}

			// Bail if we don't have any media to pin.
			if ( false === pinMedia ) {
				return;
			}

			if ( $image.hasClass('no_pin')) {
				return;
			}

			var pinDesc = '';

			if ( $image.attr( 'title' ) ) {
				pinDesc = $image.attr( 'title' );
			} else if ( $image.attr( 'alt' ) ) {
				pinDesc = $image.attr( 'alt' );
			}

			var bookmark = 'http://pinterest.com/pin/create/bookmarklet/?media=' + encodeURI( pinMedia ) + '&url=' + encodeURI( options.pageURL ) + '&is_video=false' + '&description=' +  encodeURIComponent( pinDesc );
			var imageClasses = $image.attr( 'class' );
			var imageStyle = $image.attr( 'style' );

			$image.removeClass().attr( 'style', '' ).wrap( options.wrap );

			$image.after( '<a href="' + bookmark + '" class="sw-pinit-button sw-pinit-' + swpPinIt.vLocation + ' sw-pinit-' + swpPinIt.hLocation + '">Save</a>' );

			$image.parent( '.sw-pinit' ).addClass( imageClasses ).attr( 'style', imageStyle );

			$( '.sw-pinit .sw-pinit-button' ).on( 'click', function() {
				window.open( $( this ).attr( 'href' ), 'Pinterest', 'width=632,height=253,status=0,toolbar=0,menubar=0,location=1,scrollbars=1' );
				// Record the event if Google Analytics Click tracking is enabled
				if (typeof ga == "function" && true === swpClickTracking) {
					var network = 'pin_image';
					console.log(network + " Button Clicked");
					ga("send", "event", "social_media", "swp_" + network + "_share" );
				}
				return false;
			});
		});
	}

	function handleWindowOpens() {
		$( '.nc_tweet, a.swp_CTT' ).off( 'click' );
		$( '.nc_tweet, a.swp_CTT' ).on( 'click', function( event ) {
			if ( $( this ).hasClass( 'noPop' ) ) {
				return false;
			}

			console.log($(this));

			if( $( this ).attr( 'data-link' ) ) {
				event.preventDefault ? event.preventDefault() : ( event.returnValue = false );

				var href = $( this ).attr( 'data-link' );
				console.log(href);
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

				if (typeof ga == "function" && true === swpClickTracking) {
					if($(this).hasClass('nc_tweet')) {
						var network = $(this).parents(".nc_tweetContainer").attr("data-network");
					} else if ($(this).hasClass('swp_CTT') ) {
						var network = 'ctt';
					}
					console.log(network + " Button Clicked");
					ga("send", "event", "social_media", "swp_" + network + "_share" );
				}

				return false;
			}
		});
	}

	$( window ).on('load' , function() {
		if ( 'undefined' !== typeof swpPinIt && swpPinIt.enabled ) {
			pinitButton();
		}
	});

	$( document ).ready( function() {
		handleWindowOpens();
		initShareButtons();

		// Fetch the padding amount to make space later for the floating bars
		window.bodyPaddingTop = absint( $( 'body' ).css( 'padding-top' ).replace( 'px', '' ) );
		window.bodyPaddingBottom = absint( $( 'body' ).css( 'padding-bottom' ).replace( 'px', '' ) );

		var swp_hover = false;
		$( '.nc_socialPanel' ).hover(
		    function () {
		        swp_hover = true;
		    },
		    function () {
		        swp_hover = false;
		    }
		);
		$( window ).resize( swp.debounce( 250, function() {
			if ( $( '.nc_socialPanel' ).length && false !== swp_hover ) { } else {
				window.swpAdjust = 1;
				initShareButtons();
			}
		}));

		// $( window ).trigger( 'resize' );

		$( document.body ).on( 'post-load', function() {
			initShareButtons();
		});

		if ( 0 !==  $( '.nc_socialPanelSide' ).length ) {
			var buttonsHeight = $( '.nc_socialPanelSide' ).height();
			var windowHeight = $( window ).height();
			var newPosition = absint( ( windowHeight / 2 ) - ( buttonsHeight / 2 ) );
			setTimeout( function() {
				$( '.nc_socialPanelSide' ).animate({ top: newPosition }, 0 );
			}, 105 );
		}

		if( swp_isMobile.phone ) {
			$('.swp_whatsapp').addClass('mobile');
		}

		// Hide empty containers
	    if( 1 === $('.swp-content-locator').parent().children().length ) {
	        $('.swp-content-locator').parent().hide();
	    }

	});
})( this, jQuery );

(function (global) {

    var apple_phone         = /iPhone/i,
        apple_ipod          = /iPod/i,
        apple_tablet        = /iPad/i,
        android_phone       = /(?=.*\bAndroid\b)(?=.*\bMobile\b)/i, // Match 'Android' AND 'Mobile'
        android_tablet      = /Android/i,
        amazon_phone        = /(?=.*\bAndroid\b)(?=.*\bSD4930UR\b)/i,
        amazon_tablet       = /(?=.*\bAndroid\b)(?=.*\b(?:KFOT|KFTT|KFJWI|KFJWA|KFSOWI|KFTHWI|KFTHWA|KFAPWI|KFAPWA|KFARWI|KFASWI|KFSAWI|KFSAWA)\b)/i,
        windows_phone       = /Windows Phone/i,
        windows_tablet      = /(?=.*\bWindows\b)(?=.*\bARM\b)/i, // Match 'Windows' AND 'ARM'
        other_blackberry    = /BlackBerry/i,
        other_blackberry_10 = /BB10/i,
        other_opera         = /Opera Mini/i,
        other_chrome        = /(CriOS|Chrome)(?=.*\bMobile\b)/i,
        other_firefox       = /(?=.*\bFirefox\b)(?=.*\bMobile\b)/i, // Match 'Firefox' AND 'Mobile'
        seven_inch = new RegExp(
            '(?:' +         // Non-capturing group

            'Nexus 7' +     // Nexus 7

            '|' +           // OR

            'BNTV250' +     // B&N Nook Tablet 7 inch

            '|' +           // OR

            'Kindle Fire' + // Kindle Fire

            '|' +           // OR

            'Silk' +        // Kindle Fire, Silk Accelerated

            '|' +           // OR

            'GT-P1000' +    // Galaxy Tab 7 inch

            ')',            // End non-capturing group

            'i');           // Case-insensitive matching

    var match = function(regex, userAgent) {
        return regex.test(userAgent);
    };

    var IsMobileClass = function(userAgent) {
        var ua = userAgent || navigator.userAgent;

        // Facebook mobile app's integrated browser adds a bunch of strings that
        // match everything. Strip it out if it exists.
        var tmp = ua.split('[FBAN');
        if (typeof tmp[1] !== 'undefined') {
            ua = tmp[0];
        }

        // Twitter mobile app's integrated browser on iPad adds a "Twitter for
        // iPhone" string. Same probable happens on other tablet platforms.
        // This will confuse detection so strip it out if it exists.
        tmp = ua.split('Twitter');
        if (typeof tmp[1] !== 'undefined') {
            ua = tmp[0];
        }

        this.apple = {
            phone:  match(apple_phone, ua),
            ipod:   match(apple_ipod, ua),
            tablet: !match(apple_phone, ua) && match(apple_tablet, ua),
            device: match(apple_phone, ua) || match(apple_ipod, ua) || match(apple_tablet, ua)
        };
        this.amazon = {
            phone:  match(amazon_phone, ua),
            tablet: !match(amazon_phone, ua) && match(amazon_tablet, ua),
            device: match(amazon_phone, ua) || match(amazon_tablet, ua)
        };
        this.android = {
            phone:  match(amazon_phone, ua) || match(android_phone, ua),
            tablet: !match(amazon_phone, ua) && !match(android_phone, ua) && (match(amazon_tablet, ua) || match(android_tablet, ua)),
            device: match(amazon_phone, ua) || match(amazon_tablet, ua) || match(android_phone, ua) || match(android_tablet, ua)
        };
        this.windows = {
            phone:  match(windows_phone, ua),
            tablet: match(windows_tablet, ua),
            device: match(windows_phone, ua) || match(windows_tablet, ua)
        };
        this.other = {
            blackberry:   match(other_blackberry, ua),
            blackberry10: match(other_blackberry_10, ua),
            opera:        match(other_opera, ua),
            firefox:      match(other_firefox, ua),
            chrome:       match(other_chrome, ua),
            device:       match(other_blackberry, ua) || match(other_blackberry_10, ua) || match(other_opera, ua) || match(other_firefox, ua) || match(other_chrome, ua)
        };
        this.seven_inch = match(seven_inch, ua);
        this.any = this.apple.device || this.android.device || this.windows.device || this.other.device || this.seven_inch;

        // excludes 'other' devices and ipods, targeting touchscreen phones
        this.phone = this.apple.phone || this.android.phone || this.windows.phone;

        // excludes 7 inch devices, classifying as phone or tablet is left to the user
        this.tablet = this.apple.tablet || this.android.tablet || this.windows.tablet;

        if (typeof window === 'undefined') {
            return this;
        }
    };

    var instantiate = function() {
        var IM = new IsMobileClass();
        IM.Class = IsMobileClass;
        return IM;
    };

    if (typeof module !== 'undefined' && module.exports && typeof window === 'undefined') {
        //node
        module.exports = IsMobileClass;
    } else if (typeof module !== 'undefined' && module.exports && typeof window !== 'undefined') {
        //browserify
        module.exports = instantiate();
    } else if (typeof define === 'function' && define.amd) {
        //AMD
        define('swp_isMobile', [], global.swp_isMobile = instantiate());
    } else {
        global.swp_isMobile = instantiate();
    }

})(this);
