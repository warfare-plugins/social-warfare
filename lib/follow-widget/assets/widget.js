(function(window, jQuery) {
	'use strict';

	if (typeof $ != 'function') {

		if (typeof jQuery == 'function') {
			$ = jQuery;
		}
		else if (typeof window.jQuery == 'function') {
			$ = window.jQuery
		}
		else {
			console.log("Social Warfare requires jQuery, or $ as an alias of jQuery. Please make sure your theme provides access to jQuery before activating Social Warfare.");
			return;
		}
	}

	function updateColor(followField) {
		followField = $(followField);
		var icon = followField.find(".swfw-follow-field-icon")

		// Show them we are updating the link by flashing the background color.
		icon.css('backgroundColor', followField.data('color-accent'));

		// Wait for CSS animatino to finish, then update to final color.
		setTimeout(function() {
			var css = {}
			if (followField.hasClass("swfw-active")) {
				css.backgroundColor = followField.data("color-primary")
				css.stroke = "white";
			}
			else {
				css.stroke = "black";
				css.backgroundColor = "white";
			}
			icon.css(css);
		}, 300);


	}

	function setUpdateListeners(followField) {
		followField = $(followField);
		var currentValue = followField.find('input').val();
		var newValue = currentValue;

		followField.children("input").on("change", function(event) {
			newValue = event.target.value;
			if (newValue.length) {
				followField.addClass('swfw-active');
			}
			else {
				followField.removeClass('swfw-active');
			}
		});

		followField.children("input").on("blur", function(event) {
			if (newValue == currentValue) {
				return;
			}
			updateURL(followField);
			updateColor(followField);
		});

		followField.children("a").on("click", function(event) {
			if (currentValue.length == 0) {
				return event.preventDefault();
			}
		})
	}

	function updateURL(followField) {
		followField = $(followField);
		var username = $(followField).children('input').val();
		var href ="#";

		if (username.length) {
			var url = $(followField).data('url');
			href = url.replace('swfw_username', username)
		}

		return followField.children('a').get(0).href = href;
	}

	function triggerUpdates() {
		document.querySelectorAll(".swfw-follow-field").forEach(updateURL)
		document.querySelectorAll(".swfw-follow-field").forEach(updateColor);
	}

	$(document).ready(function() {

		/*
		  This script is supposed to be a UX enhancement for admin widget area.
		  But WordPress reloads the widget content on save, and we don't have
		  a time budget to work out the kinks in this right now. 
		 */

		// The widget area re-loads by ajax, which resets JS data.
		// $(".widget-control-save").on("click", function() {
		// 	setTimeout(function() {
		// 		triggerUpdates();
		// 	}, 600);
		// });

		// // jQuery objects were weird, just use JS selectors.
		// triggerUpdates();
		// document.querySelectorAll(".swfw-follow-field").forEach(setUpdateListeners);
	});

})(window, jQuery)
