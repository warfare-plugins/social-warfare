/*
 * This is the primary javascript file used by the Social Warfare plugin. It is 
 * loaded both on the frontend and the backend. It is used to control all client
 * side manipulation of the HTML.
 *
 * Function Categories:
 * Activate/Initialize the Buttons Panels
 * Control the Floating Buttons Panels
 * Control the Pinterest Save Buttons on Images
 * Utility/Helper Functions
 *
 *
 * Javascript variables created on the server:
 *
 * bool   	swpClickTracking (SWP_Script.php)
 * bool   	swpFloatBeforeContent
 *
 * object 	swpPinIt
 *
 * string 	swp_admin_ajax
 * string 	swp_post_url
 * string 	swp_post_recovery_url
 *
 */


/**
 * The first thing we want to do is to declare our socialWarfare object. We are
 * going to use this object to store all functions that our plugin uses. This will
 * allow us to avoid any naming collisions as well as allowing us to keep things
 * more neatly organized.
 *
 */
window.socialWarfare = window.socialWarfare || {};


/**
 * This allows us to scope all variables and functions to within this anonymous
 * function. However, since we are using a global object, socialWarfare, we will
 * still be able to access our functions and variables from anywhere.
 *
 */
(function(window, $) {
	'use strict';

	var $ = jQuery;


	/**
	 * These variables measure the amount of padding at the top and bottom of
	 * the page upon the dom loaded event. We grab these early on and keep them
	 * stored so that we can add 50 pixels of padding whenever the floating
	 * horizontal buttons are displayed. This will allow us to avoid having our
	 * buttons hover over menus or copyright information in the footer.
	 *
	 */
	socialWarfare.paddingTop = parseInt($('body').css('padding-top').replace('px', ''));
	socialWarfare.paddingBottom = parseInt($('body').css('padding-bottom').replace('px', ''));


	/**
	 * The throttle function is used to control how often an event can be fired.
	 * We use this exclusively to control how often scroll events go off. In some
	 * cases, the scroll event which controls when the floating buttons appear
	 * or disappear, was firing so often on scroll that the floating buttons were
	 * rapidly flickering in and out of view. This solves that.
	 *
	 * @param  integer   delay    How often in ms to allow the event to fire.
	 * @param  function  callback The function to run if the timeout period is expired.
	 * @return function           The callback function.
	 *
	 */
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
				timeoutID = setTimeout(exec, delay - elapsed);
			}
		}

		if (socialWarfare.guid) {
			wrapper.guid = callback.guid = callback.guid || socialWarfareguid++;
		}

		return wrapper;
	};


	/**
	 * A simple wrapper for easily triggering DOM events. This will allow us to
	 * fire off our own custom events that our addons can then bind to in order
	 * to run their own functions in sequence with ours here.
	 *
	 * @param  string event The name of the event to trigger.
	 * @return void
	 *
	 */
	socialWarfare.trigger = function(event) {
		$(window).trigger($.Event(event));
	}


	/**
	 * Makes external requsts to fetch Facebook share counts. We fetch Facebook
	 * share counts via the frontened Javascript because their API has harsh
	 * rate limits that are IP Address based. So it's very easy for a website to
	 * hit those limits and recieve temporary bans from accessing the share count
	 * data. By using the front end, the IP Addresses are distributed to users,
	 * are therefore spread out, and don't hit the rate limits.
	 *
	 * @param  void
	 * @return void
	 *
	 */
	socialWarfare.fetchFacebookShares = function() {
		var url1 = 'https://graph.facebook.com/?fields=og_object{likes.summary(true).limit(0)},share&id=' + swp_post_url;
		var url2 = swp_post_recovery_url ? 'https://graph.facebook.com/?fields=og_object{likes.summary(true).limit(0)},share&id=' + swp_post_recovery_url : '';

		$.when(
				$.get(url1),
				$.get(url2)
			)
			.then(function(response1, response2) {
				var shares = 0;
				var data = {
					action: 'swp_facebook_shares_update',
					post_id: swp_post_id
				};

				shares = socialWarfare.parseFacebookShares(response1[0]);

				if (swp_post_recovery_url) {
					shares += socialWarfare.parseFacebookShares(response2[0]);
				}

				data.share_counts = shares;

				$.post(swp_admin_ajax, data);

			});
	}


	/**
	 * Sums the share data from a facebook API response. This is a utility
	 * function used by socialWarfare.fetchFacebookShares to allow easy access
	 * to parsing out the JSON response that we got from Facebook's API and
	 * converting it into an integer that reflects the tally of all activity
	 * on the URl in question including like, comments, and shares.
	 *
	 * @param  object response The API response received from Facebook.
	 * @return number The total shares summed from the request, or 0.
	 *
	 */
	socialWarfare.parseFacebookShares = function(response) {
		var total = 0;

		if ('undefined' !== typeof response.share) {
			total += parseInt(response.share.share_count);
			total += parseInt(response.share.comment_count);
		}

		if (typeof response.og_object != 'undefined') {
			total += parseInt(response.og_object.likes.summary.total_count);
		}

		return total;
	}


	/**
	 * This triggers the hover effect that you see when you hover over the
	 * buttons in the panel. It measures the space needed to expand the button
	 * to reveal the call to action for that network and then uses flex to
	 * expand it and to shrink the other buttons to make room for the expansion.
	 *
	 * @since 2.1.0
	 * @param  void
	 * @return void
	 *
	 */
	socialWarfare.activateHoverStates = function() {
		socialWarfare.trigger('pre_activate_buttons');

		$('.swp_social_panel:not(.swp_social_panelSide) .nc_tweetContainer').on('mouseenter', function() {

			if ($(this).hasClass('swp_nohover')) {
				return;
			}

			socialWarfare.resetStaticPanel();
			var termWidth = $(this).find('.swp_share').outerWidth();
			var iconWidth = $(this).find('i.sw').outerWidth();
			var containerWidth = $(this).width();
			var change = 1 + ((termWidth + 35) / containerWidth);

			$(this).find('.iconFiller').width(termWidth + iconWidth + 25 + 'px');
			$(this).css("flex", change + ' 1 0%');
		});

		$('.swp_social_panel:not(.swp_social_panelSide)').on('mouseleave', socialWarfare.resetStaticPanel);
	}


	/**
	 * Resets the static panels to their default styles. After they've been
	 * expanded by activateHoverStates(), this function returns the buttons to
	 * their normal state once a user is no longer hovering over the buttons.
	 *
	 * @see activateHoverStates().
	 * @param  void
	 * @return void
	 *
	 */
	socialWarfare.resetStaticPanel = function() {
		$(".swp_social_panel:not(.swp_social_panelSide) .nc_tweetContainer:not(.swp_nohover) .iconFiller").removeAttr("style");
		$(".swp_social_panel:not(.swp_social_panelSide) .nc_tweetContainer:not(.swp_nohover)").removeAttr("style");
	}


	/**
	 * Determines if a set of static buttons is currenty visible on the screen.
	 *
	 * We will use this to determine whether or not we should display a set of
	 * floating buttons. Whenever the static buttons are visible, we hide the
	 * floating buttons. Whenever the static buttons are not visible, we show
	 * the floating buttons.
	 *
	 * @param  void
	 * @return bool True if a static set of buttons is visible on the screen, else false.
	 *
	 */
	socialWarfare.staticPanelIsVisible = function() {
		var visible = false;
		var scrollPos = $(window).scrollTop();

		//* Iterate each buttons panel, checking each to see if it is currently visible.
		$(".swp_social_panel").not(".swp_social_panelSide, .nc_floater").each(function(index) {
			var offset = $(this).offset();

			//* Do not display floating buttons before the horizontal panel.
			if (typeof swpFloatBeforeContent != 'undefined' && false === swpFloatBeforeContent) {
				var theContent = jQuery(".swp-content-locator").parent();

				//* We are in sight of an "Above the content" panel.
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
	 * We clone a set of the static horizontal buttons so that when we create
	 * the floating set we can make the position match exactly. This way when
	 * they are showing up and disappearing, it will create the allusion that
	 * the static buttons are just getting glued to the edge of the screen and
	 * following along with the user as they scroll.
	 *
	 * @since  1.0.0 | 01 JAN 2016 | Created
	 * @param  void
	 * @return void
	 *
	 */
	socialWarfare.createFloatHorizontalPanel = function() {

		//* If a horizontal panel does not exist, we can not create a bar.
		if (!socialWarfare.panels.static || !socialWarfare.panels.static.length) {
			return;
		}

		var floatLocation       = socialWarfare.panels.static.data("float");
		var mobileFloatLocation = socialWarfare.panels.static.data("float-mobile");
		var backgroundColor     = socialWarfare.panels.static.data("float-color");
		var left                = socialWarfare.panels.static.data("align") == "center" ? 0 : socialWarfare.panels.static.offset().left;
		var wrapper             = $('<div class="nc_wrapper" style="background-color:' + backgroundColor + '"></div>');
		var barLocation         = '';

		//* .swp_social_panelSide is the side floater.
		if ($(".nc_wrapper").length) {
			$(".nc_wrapper").remove();
		}

		//* No floating bars are used at all.
		if (floatLocation != 'top' && floatLocation != 'bottom' && mobileFloatLocation != "top" && mobileFloatLocation != "bottom") {
			return;
		}

		//* Or we are on desktop and not using top/bottom floaters:
		if (!socialWarfare.isMobile() && floatLocation != 'top' && floatLocation != 'bottom') {
			return;
		}

		//* Set the location (top or bottom) of the bar depending on
		if (socialWarfare.isMobile()) {
			barLocation = mobileFloatLocation;
		} else {
			barLocation = floatLocation;
		}

		//* Assign a CSS class to the wrapper based on the float-mobile location.
		wrapper.addClass(barLocation).hide().appendTo("body");

		//* Save the new buttons panel to our ${panels} object.
		socialWarfare.panels.bar = socialWarfare.panels.static.first().clone();

		//* Give the bar panel the appropriate classname and put it in its wrapper.
		socialWarfare.panels.bar.addClass("nc_floater").css({
			width: socialWarfare.panels.static.outerWidth(true),
			left: left
		}).appendTo(wrapper);

		$(".swp_social_panel .swp_count").css({
			transition: "padding .1s linear"
		});
	}


	/**
	 * Handler to toggle the display of either the side or bar floating buttons.
	 *
	 * We only show the floating buttons when the static horizontal buttons are
	 * not in the visible view port. This function is used to toggle their
	 * visibility when they need to be shown or hidden.
	 *
	 * @since  2.0.0 | 01 JAN 2016 | Created
	 * @param  void
	 * @return void
	 *
	 */
	socialWarfare.toggleFloatingButtons = function() {
		// Adjust the floating bar
		var location = socialWarfare.panels.static.data('float');

		//* There are no floating buttons enabled, hide any that might exist.
		if (location == 'none') {
			return $(".nc_wrapper, .swp_social_panelSide").hide();
		}

		if (socialWarfare.isMobile()) {
			socialWarfare.createFloatHorizontalPanel();
			socialWarfare.toggleMobileButtons();
			socialWarfare.toggleFloatingHorizontalPanel();
		}

		if (location == "right" || location == "left") {
			socialWarfare.toggleFloatingVerticalPanel();
		}

		if (location == "bottom" || location == "top") {
			socialWarfare.toggleFloatingHorizontalPanel();
		}
	}


	/**
	 * Toggle the visibilty of a mobile bar.
	 *
	 * @return void
	 *
	 */
	socialWarfare.toggleMobileButtons = function() {

		//* There are never any left/right floating buttons on mobile, so hide them.
		socialWarfare.panels.side.hide();

		var visibility = socialWarfare.staticPanelIsVisible() ? "collapse" : "visible";
		$(".nc_wrapper").css("visibility", visibility);
	}


	/**
	 * Toggle the display of a side panel, depending on static panel visibility.
	 *
	 * @return void
	 *
	 */
	socialWarfare.toggleFloatingVerticalPanel = function() {
		var location = socialWarfare.panels.side.data("float")
		var visible = socialWarfare.staticPanelIsVisible();
		var direction, style;

		//* This is on mobile and does not use side panels.
		if (socialWarfare.isMobile()) {
			return socialWarfare.panels.side.hide();
		}

		//* No buttons panel! Manually re-define ${visibility}.
		if (!socialWarfare.panels.side || !socialWarfare.panels.side.length) {
			if (!socialWarfare.isMobile()) {
				visible = false;
			} else {
				visible = true;
			}
		}

		if (socialWarfare.panels.side.data("transition") == "slide") {

			direction = (location.indexOf("left") !== -1) ? "left" : "right";
			style = visible ? "-150px" : "5px";

			//* Update the side panel CSS with the direction and amount.
			socialWarfare.panels.side.css(direction, style);
		} else {
			/**
			 * We had problems with the fading buttons flickering rather than having
			 * a smooth fade animation. The workaround was to manually control opacity,
			 * fade, and opacity again.
			 *
			 */
			if (visible) {
				socialWarfare.panels.side.css("opacity", 1)
					.fadeOut(300)
					.css("opacity", 0);
			} else {
				socialWarfare.panels.side.css("opacity", 0)
					.fadeIn(300)
					.css("display", "flex")
					.css("opacity", 1);
			}
		}
	}


	/**
	 * Toggle the display of a floating bar, depending on static panel visibility.
	 *
	 * @return void
	 *
	 */
	socialWarfare.toggleFloatingHorizontalPanel = function() {
		var panel = $(".swp_social_panel").first();
		var paddingProp, location = '';
		var newPadding = 0;

		//* Set the location based on whether we are desktop or mobile.
		if (!socialWarfare.isMobile()) {
			location = $(panel).data("float");
		} else {
			location = $(panel).data("float-mobile")
		}

		if (socialWarfare.staticPanelIsVisible()) {

			$(".nc_wrapper").hide();
			newPadding = (location == "bottom") ? socialWarfare.paddingBottom : socialWarfare.paddingTop;
		} else {

			$(".nc_wrapper").show();

			/**
			 * Show the top/bottom floating bar. Force its opacity to normal.
			 * @see SWP_Buttons_Panel->render_HTML()
			 *
			 */
			$(".swp_social_panel.nc_floater").css("opacity", 1)

			/**
			 * Add some padding to the page so it fits nicely at the top or bottom.
			 *
			 */
			if (location == 'bottom') {

				newPadding = socialWarfare.paddingBottom + 50;
			} else {

				if (panel.offset().top > $(window).scrollTop() + $(window).height()) {

					newPadding = socialWarfare.paddingTop + 50;
					$('body').animate({
						'padding-top': newPadding + 'px'
					}, 0);
				}
			}
		}

		//* Create the CSS property name.
		paddingProp = "padding-" + location;
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
	socialWarfare.centerFloatSidePanel = function() {
		var panelHeight, windowHeight, offset;

		/**
		 * If no such element exists, we obviously just need to bail out and
		 * not try to center anything.
		 *
		 */
		if (!socialWarfare.panels.side || !socialWarfare.panels.side.length) {
			return;
		}


		/**
		 * We'll need the height of the panel itself and the height of the
		 * actual browser window in order to calculate how to center it.
		 *
		 */
		panelHeight = socialWarfare.panels.side.outerHeight();
		windowHeight = window.innerHeight;


		/**
		 * If for some reason the panel is actually taller than the window
		 * itself, just stick it to the top of the window and the bottom will
		 * just have to overflow past the bottom of the screen.
		 *
		 */
		if (panelHeight > windowHeight) {
			return socialWarfare.panels.side.css("top", 0);
		}


		/**
		 * Calculate the center position of panel and then apply the relevant
		 * CSS to the panel.
		 *
		 */
		offset = (windowHeight - panelHeight) / 2;
		socialWarfare.panels.side.css("top", offset);
	}


	/**
	 * Initializes the buttons provided that they exist.
	 *
	 * This function will activate the hover effects for the buttons, it will
	 * create the floting buttons, center vertically the side panel, handle
	 * and set up the button clicks, and monitor the scroll activity in order to
	 * show and hide any floating buttons.
	 *
	 * @param  void
	 * @return void
	 *
	 */
	socialWarfare.initShareButtons = function() {

		// Bail out if no buttons panels exist.
		if (!socialWarfare.panels.static && !socialWarfare.panels.side && !socialWarfare.panels.bar) {
			return;
		}

		socialWarfare.createFloatHorizontalPanel();
		socialWarfare.centerFloatSidePanel();
		socialWarfare.activateHoverStates();
		socialWarfare.handleButtonClicks();


		/**
		 * This will allow us to monitor whether or not the static horizontal
		 * buttons are inside the viewport as a user is scrolling the page. If
		 * they are not in the viewport, we will display the floating buttons.
		 * The throttle is to prevent it from firing non stop and causing the
		 * floating buttons to flicker.
		 *
		 */
		$(window).scroll(socialWarfare.throttle(50, function() {
			socialWarfare.toggleFloatingButtons();
		}));


		/**
		 * We trigger the scroll event once when the page is loaded so that way
		 * the floating buttons will be toggled on/off to the appropriate state
		 * as soon as the page is loaded even prior to the user actually scrolling.
		 *
		 */
		$(window).trigger('scroll');
	}


	/**
	 * Adds the "Save" button to images when the option is enabled.
	 *
	 * This method will search and destroy any Pinterest save buttons that have
	 * been added by the Pinterest browser extension and then render the html
	 * needed to add our own proprietary Pinterest buttons on top of images.
	 *
	 * @param  void
	 * @return void
	 *
	 */
	socialWarfare.enablePinterestSaveButtons = function() {


		/**
		 * Search and Destroy: This will find any Pinterest buttons that were
		 * added via their browser extension and then destroy them so that only
		 * ours are on the page.
		 *
		 */
		var pinterestBrowserButtons = socialWarfare.findPinterestBrowserSaveButtons();
		if (typeof pinterestBrowserButtons != 'undefined' && pinterestBrowserButtons) {
			socialWarfare.removePinterestBrowserSaveButtons(pinterestBrowserButtons);
		}


		/**
		 * Find all images of the images that are in the content area by looking
		 * for the .swp-content-locator div which is an empty div that we add via
		 * the_content() hook just so that we can target it here. Then iterate
		 * through them and determine if we should add a Pinterest save button.
		 *
		 */
		$('.swp-content-locator').parent().find('img').each(socialWarfare.renderPinterestSaveButton);


		/**
		 * Attach a click handler to each of the newly created "Save" buttons,
		 * and trigger the click tracking function.
		 *
		 */
		$('.sw-pinit .sw-pinit-button').on('click', function() {
			window.open($(this).attr('href'), 'Pinterest', 'width=632,height=253,status=0,toolbar=0,menubar=0,location=1,scrollbars=1');
			socialWarfare.trackClick('pin_image');
		});
	}


	/**
	 * This function renders the HTML needed to print the save buttons on the images.
	 *
	 * @param  void
	 * @since  void
	 *
	 */
	socialWarfare.renderPinterestSaveButton = function() {
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

		var bookmark = 'http://pinterest.com/pin/create/bookmarklet/?media=' + encodeURI(pinMedia) + '&url=' + encodeURI(document.URL) + '&is_video=false' + '&description=' + encodeURIComponent(pinDesc);
		var imageClasses = image.attr('class');
		var imageStyle = image.attr('style');

		image.removeClass().attr('style', '').wrap('<div class="sw-pinit" />');
		image.after('<a href="' + bookmark + '" class="sw-pinit-button sw-pinit-' + swpPinIt.vLocation + ' sw-pinit-' + swpPinIt.hLocation + '">Save</a>');
		image.parent('.sw-pinit').addClass(imageClasses).attr('style', imageStyle);
	}


	/**
	 * Fire an event for Google Analytics and GTM.
	 *
	 * @since  2.3.0 | 01 JAN 2018 | Created
	 * @param  string event A string identifying the button being clicked.
	 * @return void
	 *
	 */
	socialWarfare.trackClick = function(event) {


		/**
		 * If click tracking has been enabled in the user settings, we'll
		 * need to send the event via Googel Analytics. The swpClickTracking
		 * variable will be dynamically generated via PHP and output in the
		 * footer of the page.
		 *
		 */
		if (true === swpClickTracking) {

			/**
			 * If Google Analytics is present on the page, we'll send the
			 * event via their object and methods.
			 *
			 */
			if ("function" == typeof ga) {
				ga("send", "event", "social_media", "swp_" + event + "_share");
			}

			/**
			 * If Google Tag Manager is present on the page, we'll send the
			 * event via their object and methods.
			 *
			 */
			if ("object" == typeof dataLayer) {
				dataLayer.push({
					'event': 'swp_' + event + '_share'
				});
			}
		}
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
			 * This needs to run after all of the bail out conditions above have
			 * been run. We don't want to preventDefault if a condition exists
			 * wherein we don't want to take over the event.
			 *
			 */
			event.preventDefault();

			/**
			 * Fetch the share link that we'll use to call the popout share
			 * windows and then declare the variables that we'll be using later.
			 *
			 */
			var href = $(this).data('link').replace('’', '\'');
			var height, width, top, left, instance, windowAttributes, network;

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

			socialWarfare.trackClick(network);
		});
	}


	/**
	 * Looks for a "Save" button created by Pinterest addons.
	 *
	 * @param  void
	 * @return HTMLNode if the Pinterest button is found, else NULL.
	 *
	 */
	socialWarfare.findPinterestBrowserSaveButtons = function() {

		//* Known constants used by Pinterest.
		var pinterestRed            = "rgb(189, 8, 28)";
		var pinterestZIndex         = "8675309";
		var pinterestBackgroundSize = "14px 14px";
		var button                  = null;

		//* The Pinterest button is a <span/>, so check each span for a match.
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


	/**
	 * Removes the "save" button created by Pinterest Browser Extension.
	 *
	 */
	socialWarfare.removePinterestBrowserSaveButtons = function(button) {
		var pinterestSquare = button.nextSibling;

		//* The sibling to the Pinterest button is always a span.
		if (typeof pinterestSquare != 'undefined' && pinterestSquare.nodeName == 'SPAN') {
			var style = window.getComputedStyle(pinterestSquare);
			var size = "24px";

			//* If the sibling is indeed the correct Pinterest sibling, destory it all.
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
			socialWarfare.checkListeners(count++, limit)
		}, 2000);
	}


	/**
	 * Initializes the vertical position for top/bottom floating buttons.
	 *
	 * @return void
	 */
	socialWarfare.initSidePosition = function() {
		if (!socialWarfare.panels.side || !socialWarfare.panels.side.length) {
			return;
		}

		var buttonsHeight = $(socialWarfare.panels.side).height();
		var windowHeight  = $(window).height();
		var newPosition   = parseInt((windowHeight / 2) - (buttonsHeight / 2));

		setTimeout(function() {
			$(socialWarfare.panels.side).animate({
				top: newPosition
			}, 0);
		}, 105);
	}


	/**
	 * Stores the user-defined mobile breakpoint in the socialWarfare object.
	 *
	 */
	socialWarfare.establishBreakpoint = function() {
		var panel = $(".swp_social_panel");
		socialWarfare.breakpoint = 1100;

		if (panel.length && panel.data("min-width") || panel.data("min-width") == 0) {
			socialWarfare.breakpoint = parseInt(panel.data("min-width"));
		}
	}


	/**
	 * Checks to see if the current viewport is within the defined mobile boundary.
	 *
	 */
	socialWarfare.isMobile = function() {
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
	socialWarfare.establishPanels = function() {
		//* Initialize the panels object with the three known panel types.
		socialWarfare.panels = {
			static: null,
			side:   null,
			bar:    null
		};

		//* TODO create the data-position attribute in PHP and print it on each set of buttons panel.
		var staticPanel = $(".swp_social_panel").not(".swp_social_panelSide");
		var sidePanel   = $(".swp_social_panelSide");
		// var barPanel = $(".swp_social_panelSide").find("'[data-position]=bar'").first();

		if (staticPanel) {
			socialWarfare.panels.static = staticPanel;
		}

		if (sidePanel) {
			socialWarfare.panels.side = sidePanel;
		}
		//
		// if (barPanel) {
		// 	socialWarfare.panels.bar = barPanel
		// }

		return socialWarfare.panels;
	}


	/**
	 * Runs the initialization callbacks for button handlers and placement.
	 *
	 * @return void
	 *
	 */
	socialWarfare.initPlugin = function() {

		socialWarfare.establishPanels();
		socialWarfare.establishBreakpoint();
		socialWarfare.handleButtonClicks();
		socialWarfare.initShareButtons();

		if (socialWarfare.panels.side) {
			socialWarfare.initSidePosition();
		}
	}

	$(window).on('load', function() {

		if ('undefined' !== typeof swpPinIt && swpPinIt.enabled) {
			socialWarfare.enablePinterestSaveButtons();
		}
		window.clearCheckID = 0;
	});

	$(document).ready(function() {
		socialWarfare.initPlugin();

		//* Check every 2 seconds for buttons panels, in case they still need click handlers.
		setTimeout(function() {
			socialWarfare.checkListeners(0, 5);
		}, 2000);

	});
})(this, jQuery);
