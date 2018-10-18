/* global swpPinIt */

/*
 * JS variables created on the server:
 *
 * bool   swpClickTracking (SWP_Script.php)
 * object swpPinIt
 *
*/

window.socialWarfare = window.socialWarfare || {};

(function(window, $) {
'use strict';

if (typeof $ == 'undefined') {
  	var $ = jQuery;
}

var paddingTop = parseInt($('body').css('padding-top').replace('px', ''));
var paddingBottom = parseInt($('body').css('padding-bottom').replace('px', ''));

socialWarfare.throttle = function(delay, callback) {
    var timeoutID = 0;
    var lastExec = 0;

    function wrapper() {
        var that = this;
        var elapsed = +new Date() - lastExec;
        var args = arguments;

        function exec() {
            lastExec = +new Date();
            callback.apply(that, args);
        }

        function clear() {
            timeoutID = undefined;
        }

        timeoutID && clearTimeout(timeoutID);

        if (elapsed > delay) {
            exec();
        } else {
            timeoutID = setTimeout(exec,  delay - elapsed);
        }
    }

    if (socialWarfareguid) {
        wrapper.guid = callback.guid = callback.guid || socialWarfareguid++;
    }

    return wrapper;
};

socialWarfare.trigger = function(event) {
	  $(window).trigger($.Event(event));
}

/****************************************************************************

    Fetch and Store Facebook Counts

****************************************************************************/
/**
 * Adds the share data from a facebook API response.
 *
 * @param  object response The API response received from Facebook.
 * @return number The total shares summed from the request, or 0.
 *
 */
socialWarfare.parseFacebookShares = function(response) {
	return (parseInt(response[0].share.share_count) +
		     parseInt(response[0].share.comment_count) +
	       parseInt(response[0].og_object.likes.summary.total_count)) || 0;
}


socialWarfare.fetchFacebookShares = function() {
	/**
	 * Run all the API calls
	 */
	$.when(
			$.get('https://graph.facebook.com/?fields=og_object{likes.summary(true).limit(0)},share&id=' + swp_post_url),
			(swp_post_recovery_url ? $.get('https://graph.facebook.com/?fields=og_object{likes.summary(true).limit(0)},share&id=' + swp_post_recovery_url) : '')
		)
		.then(function(request1, request2) {
			/**
			 * Parse the responses, add up the activity, send the results to admin_ajax
			 */
			if ('undefined' !== typeof request1[0].share) {
				var shares = socialWarfare.parseFacebookShares(request1[0]);

				if (swp_post_recovery_url) {
					shares += socialWarfare.parseFacebookShares(request2[0]);
				}

				var swpPostData = {
					action: 'swp_facebook_shares_update',
					post_id: swp_post_id,
					share_counts: shares
				};

				$.post(swp_admin_ajax, swpPostData);
			}
		});
}

/**
 * Activate Hover States: Trigger the resizes to the proper widths for the expansion on hover effect
 * @since 2.1.0
 * @param none
 * @return none
 */
socialWarfare.activateHoverStates = function() {
	socialWarfare.trigger('pre_activate_buttons');

	$('.swp_social_panel:not(.swp_social_panelSide) .nc_tweetContainer').on('mouseenter', function() {

		if ($(this).hasClass('swp_nohover')) {
			return;
		}
		// socialWarfare.resetStaticDimensions();
		var termWidth = $(this).find('.swp_share').outerWidth();
		var iconWidth = $(this).find('i.sw').outerWidth();
		var containerWidth = $(this).width();
		var change = 1 + ((term_width + 35) / containerWidth);

		$(this).find('.iconFiller').width(termWidth + iconWidth + 25 + 'px');
		$(this).css({
			flex: change + ' 1 0%'
		});
	});

	$('.swp_social_panel:not(.swp_social_panelSide)').on('mouseleave', socialWarfare.resetStaticDimensions);
}


socialWarfare.resetStaticDimensions = function() {
	$(".swp_social_panel:not(.swp_social_panelSide) .nc_tweetContainer:not(.swp_nohover) .iconFiller").removeAttr("style");
	// socialWarfare.panels.static.removeAttr("style");
	$(".swp_social_panel:not(.swp_social_panelSide) .nc_tweetContainer:not(.swp_nohover)").removeAttr("style");
}


//*  If any horiztonal buttons panel is currently visible on screen,
//*  returns true. Else, returns false.
socialWarefare.panelIsVisible = function() {
	var panel = $(".swp_social_panel").not(".swp_social_panelSide").first();
	var visible = false;
	var scrollPos = $(window).scrollTop();

	$(".swp_social_panel").not(".swp_social_panelSide, .nc_floater").each(function(index) {
		var offset = $(this).offset();

		//* Do not display floating buttons before the horizontal panel.
		if (typeof swpFloatBeforeContent != 'undefined' && false === swpFloatBeforeContent) {
			var theContent = jQuery(".swp-content-locator").parent();

			if (index === 0 && theContent.length && theContent.offset().top > (scrollPos + jQuery(window).height())) {
				visible = true;
			}
		}

		//* Do not display floating buttons if a panel is currently visible.
		if ((offset.top + $(this).height()) > scrollPos && offset.top < (scrollPos + $(window).height())) {
			visible = true;
		}
	});

	return visible;
}

/**
 *  Clones a copy of the static buttons to use as a floating panel.
 *
 */
socialWarfare.createFloatBar = function() {
	//* .swp_social_panelSide is the side floater.
	if ($(".nc_wrapper").length) {
		$(".nc_wrapper").remove();
	}

	var panel = $(".swp_social_panel").not(".swp_social_panelSide");
	var floatLocation = panel.data("float");
	var mobileFloatLocation = panel.data("float-mobile");

	//* If a horizontal panel does not exist,
	if (typeof panel == "undefined") {
		return;
	}

	//* No floating bars are used at all.
	if (floatLocation != 'top' && floatLocation != 'bottom' && mobileFloatLocation != "top" && mobileFloatLocation != "bottom") {
		return;
	}

	//* Or we are on desktop and not using top/bottom floaters:
	if (!isMobile() && floatLocation != 'top' && floatLocation != 'bottom') {
		return;
	}

	var backgroundColor = panel.data("float-color");
	var left = panel.data("align") == "center" ? 0 : panel.offset().left;
	var wrapper = $('<div class="nc_wrapper" style="background-color:' + backgroundColor + '"></div>');

	if (isMobile()) {
		var barLocation = mobileFloatLocation;
	} else {
		var barLocation = floatLocation;
	}

	wrapper.addClass(barLocation).hide().appendTo("body");

	var clone = panel.first().clone();
	clone.addClass("nc_floater").css({
		width: panel.outerWidth(true),
		left: left
	}).appendTo(wrapper);

	$(".swp_social_panel .swp_count").css({
		transition: "padding .1s linear"
	});
}


socialWarfare.toggleFloatingButtons = function() {
	// Adjust the floating bar
	var panel = $(".swp_social_panel").first();
	var location = panel.data('float');

	if (location == 'none') {
		jQuery(".nc_wrapper, .swp_social_panelSide").hide();
		return;
	}

	if (isMobile()) {
		createFloatBar();
		toggleMobileButtons();
		toggleFloatingBar();
	}

	if (location == "right" || location == "left") {
		toggleSideButtons();
	}

	if (location == "bottom" || location == "top") {
		toggleFloatingBar();
	}
}


socialWarfare.toggleMobileButtons = function() {
	var panel = $(".swp_social_panel").first();
	// var direction = (location.indexOf("left") !== -1) ? "left" : "right";
	var visibility = panelIsVisible() ? "collapse" : "visible";

	//* Force side floating panel to be hidden.
	$(".swp_social_panelSide").hide();

	//* Make sure hidden mobile buttons do not block clicks on content underneath.
	$(".nc_wrapper").css("visibility", visibility);
}


socialWarfare.toggleSideButtons = function() {
	var panel = $(".swp_social_panel").not(".swp_social_panelSide").first();
	var sidePanel = $(".swp_social_panelSide");
	var location = sidePanel.data("float")
	var visible = panelIsVisible();

	if (isMobile() && $(".nc_wrapper").length) {
		//* Mobile display with top/bottom mobile bar.
		sidePanel.hide();
		return;
	}

	if (!panel.length) {
		//* No buttons panel!
		if (!isMobile()) {
			visible = false;
		} else {
			visible = true;
		}
	}

	if (sidePanel.data("transition") == "slide") {
		var direction = (location.indexOf("left") !== -1) ? "left" : "right";
		if (visible) {
			sidePanel.css(direction, "-150px");
		} else {
			sidePanel.css(direction, "5px");
		}
	} else {
		if (visible) {
			sidePanel.css("opacity", 1).fadeOut(300).css("opacity", 0);
		} else {
			sidePanel.css("opacity", 0).fadeIn(300).css({
				opacity: 1,
				display: "flex"
			});
		}
	}
}

//* Note: All of the other logic for padding now lives in createFloatBar.
//* Otherwise, it added the padding every time this was called.
socialWarfare.toggleFloatingBar = function() {
	var panel = $(".swp_social_panel").first();
	var newPadding = 0;

	//* Are we on desktop or mobile?
	if (!isMobile()) {
		var location = $(panel).data("float");
	} else {
		var location = $(panel).data("float-mobile")
	}

	if (panelIsVisible()) {
		$(".nc_wrapper").hide();

		newPadding = (location == "bottom") ? paddingBottom : paddingTop;

	} else {
		$(".nc_wrapper").show();

		//* Show the top/bottom floating bar. Force its opacity to normal.
		//* @see SWP_Buttons_Panel->render_HTML()
		jQuery(".swp_social_panel.nc_floater").css("opacity", 1)

		// Add some padding to the page so it fits nicely at the top or bottom
		if (location == 'bottom') {
			newPadding = paddingBottom + 50;
			// $('body').animate({ 'padding-bottom': newPadding + 'px' }, 0);
		} else {
			if (panel.offset().top > $(window).scrollTop() + $(window).height()) {
				newPadding = paddingTop + 50;
				$('body').animate({
					'padding-top': newPadding + 'px'
				}, 0);
			}
		}
	}

	var paddingProp = "padding-" + location;
	$("body").animate({
		paddingProp: newPadding
	}, 0);

}


/**
 * This method is used to vertically center the floating buttons when they
 * are positioned on the left or right of the screen.
 *
 * @since  3.4.0 | 18 OCT 2018 | Created
 * @param  void
 * @param  void All changes are made to the dom.
 *
 */
socialWarfare.centerSidePanel = function() {

	var sidePanel, panelHeight, windowHeight, offset;


	/**
	 * The side panel has unique classes that are used to let us know where
	 * it should display. It can display at the top, the bottom, or centered
	 * vertically. This is the class for centered: float-position-center.
	 *
	 */
	sidePanel = jQuery("[class*=float-position-center]");


	/**
	 * If no such element exists, we obviously just need to bail out and
	 * not try to center anything.
	 *
	 */
	if (false == sidePanel.length) {
		return;
	}


	/**
	 * We'll need the height of the panel itself and the height of the
	 * actual browser window in order to calculate how to center it.
	 *
	 */
	panelHeight = sidePanel.outerHeight();
	windowHeight = window.innerHeight;


	/**
	 * If for some reason the panel is actually taller than the window
	 * itself, just stick it to the top of the window and the bottom will
	 * just have to overflow past the bottom of the screen.
	 *
	 */
	if (panelHeight > windowHeight) {
		sidePanel.css("top", 0);
		return;
	}


	/**
	 * Calculate the center position of panel and then apply the relevant
	 * CSS to the panel.
	 *
	 */
	offset = (windowHeight - panelHeight) / 2;
	sidePanel.css("top", offset);
}


socialWarfare.initShareButtons = function() {
	if (0 !== $('.swp_social_panel').length) {
		createFloatBar();
		centerSidePanel();
		socialWarfareactivateHoverStates();
		handleButtonClicks();
		$(window).scroll(socialWarfarethrottle(50, function() {
			toggleFloatingButtons();
		}));
		$(window).trigger('scroll');
		// $('.swp_social_panel').css({'opacity':1});
	}
}

/****************************************************************************

    Pin It Hover Effect

****************************************************************************/

socialWarfare.pinitButton = function() {
	var defaults = {
		wrap: '<div class="sw-pinit" />',
		pageURL: document.URL
	};

	var options = $.extend(defaults, options);
	var pinterestButton = findPinterestSaveButton();

	if (typeof pinterestButton != 'undefined' && pinterestButton) {
		removePinterestButton(pinterestButton);
	}

	// Iterate over the current set of matched elements.
	$('.swp-content-locator').parent().find('img').each(function() {
		var image = $(this);

		if (typeof swpPinIt.disableOnAnchors != undefined && swpPinIt.disableOnAnchors) {
			if (jQuery(image).parents().filter("a").length) {
				return;
			}
		}

		if (image.outerHeight() < swpPinIt.minHeight || image.outerWidth() < swpPinIt.minWidth) {
			return;
		}

		if (image.hasClass('no_pin') || image.hasClass('no-pin')) {
			return;
		}

		var pinMedia;

		if ('undefined' !== typeof swpPinIt.image_source) {

			//* Create a temp image to force absolute paths via jQuery.
			var i = new Image();
			i.src = swpPinIt.image_source;
			pinMedia = jQuery(i).prop('src');

		} else if (image.data('media')) {
			pinMedia = image.data('media');
		} else if ($(this).data('lazy-src')) {
			pinMedia = $(this).data('lazy-src');
		} else if (image[0].src) {
			pinMedia = image[0].src;
		};

		// Bail if we don't have any media to pin.
		if (!pinMedia || 'undefined' === typeof pinMedia) {
			return;
		}

		var pinDesc = '';

		if (typeof image.data("pin-description") != 'undefined') {
			pinDesc = image.data("pin-description");
		} else if ('undefined' !== typeof swpPinIt.image_description) {
			pinDesc = swpPinIt.image_description;
		} else if (image.attr('title')) {
			pinDesc = image.attr('title');
		} else if (image.attr('alt')) {
			pinDesc = image.attr('alt');
		}

		var bookmark = 'http://pinterest.com/pin/create/bookmarklet/?media=' + encodeURI(pinMedia) + '&url=' + encodeURI(options.pageURL) + '&is_video=false' + '&description=' + encodeURIComponent(pinDesc);
		var imageClasses = image.attr('class');
		var imageStyle = image.attr('style');

		image.removeClass().attr('style', '').wrap(options.wrap);
		image.after('<a href="' + bookmark + '" class="sw-pinit-button sw-pinit-' + swpPinIt.vLocation + ' sw-pinit-' + swpPinIt.hLocation + '">Save</a>');
		image.parent('.sw-pinit').addClass(imageClasses).attr('style', imageStyle);
	});

	$('.sw-pinit .sw-pinit-button').on('click', function() {
		window.open($(this).attr('href'), 'Pinterest', 'width=632,height=253,status=0,toolbar=0,menubar=0,location=1,scrollbars=1');

		// Record the event if Google Analytics Click tracking is enabled
		if (true === swpClickTracking) {
			var network = 'pin_image';

			// If Google Analytics is Present on the page.
			if ('function' == typeof ga) {
				ga("send", "event", "social_media", "swp_" + network + "_share");
			}

			// If Google Tag Manager is Present on the Page
			if ("object" == typeof dataLayer) {
				dataLayer.push({
					'event': 'swp_' + network + '_share'
				});
			}
		}

		return false;
	});
}


/**
 * Handle clicks on the buttons that open share windows. It fetches the
 * share link, it opens the share link into a new window, it sizes the
 * popout window, and makes sure the user is able to share the content.
 *
 * This also handles sending the events to Google Analytics and Google Tag
 * Manager if the user has that feature enabled.
 *
 * @since  1.0.0 | 01 JAN 2018 | Created
 * @param  void
 * @return bool Returns false on failure.
 *
 */
socialWarfare.handleButtonClicks = function() {


	/**
	 * In order to avoid the possibility that this function may be called
	 * more than once, we remove all click handlers from our buttons prior
	 * to activating the new click handler. Prior to this, there were some
	 * unique instances where clicking on a button would cause multiple
	 * share windows to pop out.
	 *
	 */
	$('.nc_tweet, a.swp_CTT').off('click');
	$('.nc_tweet, a.swp_CTT').on('click', function(event) {


		/**
		 * Some buttons that don't have popout share windows can use the
		 * 'nopop' class to disable this click handler. This will then make
		 * that button behave like a standard link and allow the browser's
		 * default click handler to handle it. This is for things like the
		 * email button.
		 *
		 */
		if ($(this).hasClass('noPop')) {
			return false;
		}


		/**
		 * Our click handlers will use the data-link html attribute on the
		 * button as the share URL when opening the share window. Therefore,
		 * we need to make sure that this attribute exists.
		 *
		 */
		if (false == $(this).data('link')) {
			return false;
		}

		/**
		 * Prevent the browser from handling the click.
		 *
		 */
		event.preventDefault();


		/**
		 * Fetch the share link that we'll use to call the popout share
		 * windows and then declare the variables that we'll be using later.
		 *
		 */
		var href = $(this).data('link').replace('’', '\'');
		var height, width, top, left, instance, windowAttributes;


		/**
		 * These are the default dimensions that are used by most of the
		 * popout share windows. Additionally, a few of the windows have
		 * their own javascript that will resize the window dynamically
		 * once loaded.
		 *
		 */
		height = 270;
		width = 500;


		/**
		 * Pinterest, Buffer, and Flipboard use a different size than the
		 * rest so if it's one of those buttons, overwrite the defaults
		 * that we set above.
		 *
		 */
		if ($(this).is('.pinterest, .buffer_link, .flipboard')) {
			height = 550;
			width = 775;
		}


		/**
		 * We'll measure the window and then run some calculations to ensure
		 * that our popout share window opens perfectly centered on the
		 * browser window.
		 *
		 */
		top              = window.screenY + (window.innerHeight - height) / 2;
		left             = window.screenX + (window.innerWidth - width) / 2;
		windowAttributes = 'height=' + height + ',width=' + width + ',top=' + top + ',left=' + left;
		instance         = window.open(href, '_blank', windowAttributes);


		/**
		 * If click tracking has been enabled in the user settings, we'll
		 * need to send the event via Googel Analytics. The swpClickTracking
		 * variable will be dynamically generated via PHP and output in the
		 * footer of the page.
		 *
		 */
		if (false == swpClickTracking) {


			/**
			 * If a button was clicked, use the data-network attribute to
			 * figure out which network is being shared. If it was a click
			 * to tweet that was clicked on, just use ctt as the network.
			 *
			 */
			if ($(this).hasClass('nc_tweet')) {
				network = $(this).parents('.nc_tweetContainer').data('network');
			} else if ($(this).hasClass('swp_CTT')) {
				network = 'ctt';
			}

			/**
			 * If Google Analytics is present on the page, we'll send the
			 * event via their object and methods.
			 *
			 */
			if ('function' == typeof ga) {
				ga('send', 'event', 'social_media', 'swp_' + network + '_share');
			}


			/**
			 * If Google Tag Manager is present on the page, we'll send the
			 * event via their object and methods.
			 *
			 */
			if ('object' == typeof dataLayer) {
				dataLayer.push({
					'event': 'swp_' + network + '_share'
				});
			}
		}

		return false;
	});
}


//* The Pinterest Browser Extension create a single Save button.
//* Let's search and destroy.
socialWarfare.findPinterestSaveButton = function() {
	//* Known constants used by Pinterest.
	var pinterestRed = "rgb(189, 8, 28)";
	var pinterestZIndex = "8675309";
	var pinterestBackgroundSize = "14px 14px";
	var button = null;

	document.querySelectorAll("span").forEach(function(el, index) {
		var style = window.getComputedStyle(el);

		if (style.backgroundColor == pinterestRed) {
			if (style.backgroundSize == pinterestBackgroundSize && style.zIndex == pinterestZIndex) {
				button = el;
			}
		}
	});

	return button;
}

socialWarfare.removePinterestButton = function(button) {
	var pinterestSquare = button.nextSibling;

	if (typeof pinterestSquare != 'undefined' && pinterestSquare.nodeName == 'SPAN') {
		var style = window.getComputedStyle(pinterestSquare);
		var size = "24px";

		if (style.width.indexOf(size) === 0 && style.height.indexOf(size) === 0) {
			pinterestSquare.remove()
		}
	}

	button.remove();
}


/**
 * Checks to see if we have a buttons panel. If so, forces a re-run of the
 * handleButtonClicks callback.
 *
 * @param  number count The current iteration of the loop cycle.
 * @param  number limit The maximum number of iterations for the loop cycle.
 * @return void or function handleButtonClicks().
 *
 */
socialWarfare.checkListeners = function(count, limit) {
	if (count > limit) {
		return;
	}

	var panel = $('.swp_social_panel');

	if (panel.length > 0 && panel.find(".swp_pinterest")) {
		return socialWarfare.handleButtonClicks();
	}

	setTimeout(function() {
		checkListeners(count++, limit)
	}, 2000);
}

/**
 * Initializes the veritcal position for top/bottom floating buttons.
 *
 * @return void
 */
socialWarfare.initSidePosition = function() {
	var sidePanel = $('.swp_social_panelSide');
	// *If using top or bottom vertical positions, let CSS position the element.
	if ($(sidePanel).attr("class").indexOf("swp_side") !== -1) return;

	var buttonsHeight = $(sidePanel).height();
	var windowHeight = $(window).height();
	var newPosition = parseInt((windowHeight / 2) - (buttonsHeight / 2));

	setTimeout(function() {
		$(sidePanel).animate({
			top: newPosition
		}, 0);
	}, 105);
}


//* Stores the user-defined mobile breakpoint in the socialWarfare object.
socialWarfare.establishBreakpoint = function() {
	var panel = $(".swp_social_panel");
	socialWarfare.breakpoint = 1100;

	if (panel.length && panel.data("min-width") || panel.data("min-width") == 0) {
		socialWarfare.breakpoint = parseInt(panel.data("min-width"));
	}
}

//* Checks to see if the current viewport is within the defined mobile boundary.
socialWwarfare.isMobile = function() {
	var currentWidth = $(window).width();
	return currentWidth < socialWarfare.breakpoint;
}

/**
 * Finds each kind of buttons panel, if it exists, and stores it to the
 * socialWarfare object for later reference. This is useful for reading data
 * attributes of buttons panels without needing to fetch the panel every time.
 *
 * @return object The object which holds each of the kinds of buttons panels.
 */
socialWarefare.establishPanels = function() {
	socialWarfare.panels = {
		static: null,
		side: null,
		bar: null
	};

	//* TODO create the data-position attribute in PHP and print it on each set of buttons panel.
	var staticPanel = $(".swp_social_panel").not(".swp_social_panelSide").first();
	var sidePanel = $(".swp_social_panelSide").find("'[data-position]=side'").first();
	var barPanel = $(".swp_social_panelSide").find("'[data-position]=bar'").first();

	if (panel) {
		socialWarfare.panels.static = staticPanel;
	}

	if (sidePanel) {
		socialWarfare.panels.side = sidePanel;
	}

	if (barPanel) {
		socialWarfare.panels.bar = barPanel
	}

	return socialWarfare.panels;
}

/**
 * Runs the initialization callbacks for button handlers and placement.
 *
 * @return void
 *
 */
socialWarefare.initPlugin = function() {
	socialWarefare.establishPanels();
	socialWarefare.establishBreakpoint();
	socialWarefare.handleButtonClicks();
	socialWarefare.initShareButtons();

	if (0 !== $('.swp_social_panelSide').length) {
		socialWarefare.initSidePosition();
	}

	// Hide empty containers
	if (1 === $('.swp-content-locator').parent().children().length) {
		$('.swp-content-locator').parent().hide();
	}
}

$(window).on('load', function() {
	if ('undefined' !== typeof swpPinIt && swpPinIt.enabled) {
		socialWarefare.pinitButton();
	}
	window.clearCheckID = 0;
});

$(document).ready(function() {
	socialWarefare.initPlugin();

	//* Check every 2 seconds for buttons panels, in case they still need click handlers.
	setTimeout(function() {
		checkListeners(0, 5);
	}, 2000);

});
})(this, jQuery);
