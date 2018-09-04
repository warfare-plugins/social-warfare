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

/**
* Show and hide input fields based on conditional values.
*
* This function iterates over each element with the "dep" data attribute. For each
* such dependant element, its parent element controls whether the dependant is shown or hidden
* if the parent's value matches the condition.
*
* @since 3.0.0 Feb 12 2018 | Brought func in from admin-options-page.js and set to global scope; Updated variable names for semantics, switched to Yoda condietionals.
* @since 3.0.0 Feb 14 2018 | Mapped the required array from variable types to string.
*
* @see admin-options-page.js
* @return none
*/
function swpConditionalFields() {
    if (typeof $ == 'undefined') {
      $ = jQuery;
    }

  	function swp_selected(name) {
    		return $('select[name="' + name + '"]').val();
  	}

  	function swp_checked(name) {
    		return $('[name="' + name + '"]').prop('checked');
  	}

  	function string_to_bool(string) {
    		if (string === 'true') { string = true };
    		if (string === 'false'){ string = false };
    		return string;
  	}

  	// Loop through all the fields that have dependancies
  	$("[data-dep]").each(function() {

  		// Fetch the conditional values
  		var condition = $(this).data('dep');
  		var required = JSON.parse(JSON.stringify($(this).data('dep_val')));

  		// Check if we're on the options page or somewhere else
  		if (window.location.href.indexOf("page=social-warfare") === -1) {
    			var conditionEl = $(this).parents('.widgets-holder-wrap').find('[data-swp-name="' + condition + '"]');
  		} else {
    			var conditionEl = $('[name="' + condition + '"]')[0];
  		}

  		var value;

  		if (typeof conditionEl === 'undefined') {
    			conditionEl = $('[name="' + condition + '"]')[0];

    			if (typeof conditionEl === 'undefined') {
    				conditionEl = $('[field$=' + condition + ']')[0];
    			}
  		}

  		// Fetch the value of checkboxes or other input types
  		if ($(conditionEl).attr('type') == 'checkbox') {
    			value = $(conditionEl).prop('checked');
  		} else {
    			value = $(conditionEl).val();
  		}

  		value = string_to_bool(value);

      //* Options page uses parent visibilty to check. Widget page does not. This could definiitely look better.
  		// Show or hide based on the conditional values (and the dependancy must be visible in case it is dependant)

  		if (window.location.href.indexOf("page=social-warfare") !== -1) {
    			// If the required value matches and it's parent is also being shown, show this conditional field
    			if ($.inArray(value, required) !== -1 && $(conditionEl).parent('.sw-grid').is(':visible') ) {
      				$(this).show();
    			} else {
      				$(this).hide();
    			}
  		} else {
    			// If the required value matches, show this conditional field
    			if ($.inArray(value, required) !== -1 || value === required) {
      				$(this).show();
    			} else {
      				$(this).hide();
    			}
  		}
  	});

  	if (false === swp_checked('float_style_source') &&
  	       'custom_color'              === swp_selected('float_default_colors')
  	    || 'custom_color_outlines'     === swp_selected('float_default_colors')
  	    || 'custom_color'              === swp_selected('float_single_colors')
  	    || 'custom_color_outlines'     === swp_selected('float_single_colors')
  	    || 'custom_color'              === swp_selected('float_hover_colors')
          || 'custom_color_outlines'     === swp_selected('float_hover_colors')) {
  		$('.sideCustomColor_wrapper').slideDown();

  	} else {
  		$('.sideCustomColor_wrapper').slideUp();
  	}
}

//* Only run on widgets.php
if (window.location.href.indexOf("widgets.php") > -1) {
  	//* Make sure the elements exist before trying to read them.
  	//*
  	var widgetFinder = setInterval(function() {
    		if (typeof swpWidget !== 'undefined') clearInterval(widgetFinder);

    		swpWidget = $("#widgets-right [id*=_swp_popular_posts_widget], [id*=_swp_popular_posts_widget].open")[0];
    		widgetSubmit = $(swpWidget).find("[id$=savewidget]")[0];

        //* Force swpConditionalFields to run when the widget is opened or saved.
    		$(swpWidget).on("click", swpConditionalFields);

    		$(widgetSubmit).on("click", function() {
    		  	setTimeout(swpConditionalFields, 600);
    		});

  	}, 50);
}

(function(window, jQuery, undefined) {
  	'use strict';

    if (typeof $ == 'undefined') {
        $ = jQuery;
    }

  	socialWarfareAdmin.linkLength = function(input) {
    		var tmp = '';

    		for (var i = 0; i < 23; i++) {
    			tmp += 'o';
    		}

    		return input.replace(/(http:\/\/[\S]*)/g, tmp).length;
  	};

    function updateCharactersRemaining(containerSelector, characterLimit) {
        var input = $("#social_warfare #" + containerSelector);
    		var container = $("#social_warfare [class*='" + containerSelector + "'");
        var remaining = characterLimit - input.val().length

        if (containerSelector == "swp_custom_tweet") {
          //* Account for the permalink + whitespace being added to the tweet.
          remaining -= $("#sample-permalink").text().length + 1;
        }

        if (remaining >= 0) {
            container.find(".swp_CountDown").removeClass("swp_red").addClass("swp_blue")
        } else {
            container.find(".swp_CountDown").removeClass("swp_blue").addClass("swp_red")
        }

    		container.find(".counterNumber").text(remaining)
    }

  	function toggleCustomThumbnailFields(show) {
  		if (typeof show === 'undefined') show = true;

  		if (show) {
  		  	$(".custom_thumb_size").show();
  		} else {
        	$(".custom_thumb_size").hide();
      }
  	}

    function noticeClickHandlers() {
        $(".swp-notice-cta").on("click", function(e) {
            e.preventDefault();
            //* Do not use jQuery to get href.
            var link = e.target.getAttribute("href");

            if (typeof link == 'string' && link.length) {
                window.open(link);
            }

            var parent = $(this).parents(".swp-dismiss-notice");

            $.post({
                url: ajaxurl,
                data: {
                    action: 'dismiss',
                    key: parent.data("key"),
                    timeframe: this.dataset.timeframe
                },
                success: function(result) {
                    result = JSON.parse(result)
                    if (result) {
                        parent.slideUp(500);
                    }
                }
            });
        });
    }


    function createCharactersRemaining(selector, textLimit) {
      var div = '<div class="swp_CountDown"><span class="counterNumber">' + textLimit + '</span> ' + swp_localize_admin.swp_characters_remaining + '</div>';

      $("#social_warfare #" + selector).parent().prepend(div);
    }

  	$(document).ready(function() {
        noticeClickHandlers();

    		if ($('#social_warfare.postbox').length) {
            var textCounters = {
                "swp_og_title": 60,
                "swp_og_description": 150,
                "swp_pinterest_description": 140,
                "swp_custom_tweet": 280
              };

              Object.entries(textCounters).map(function(entry) {
                  var selector = entry[0];
                  var textLimit = entry[1];

                  createCharactersRemaining(selector, textLimit);
                  updateCharactersRemaining(selector, textLimit);

                  $("#social_warfare #" + selector).on("input", function() {
                      updateCharactersRemaining(selector, textLimit);
                  });
              });

        			// Setup an initilazation loop
        			var swpPostInit = setInterval(function() {
          				var swpOgImage  = $('.swp_og_imageWrapper ul.swpmb-media-list');
          				var swpPinImage = $('.swp_pinterest_imageWrapper ul.swpmb-media-list');

          				var smWidth, smHeight;

          				// Check if the media list has been created yet
          				if (swpOgImage.length && swpOgImage.is(':empty')) {
          					// Setup the Open Graph Image Placeholder
          					smWidth = swpOgImage.width();
          					smHeight = smWidth * (9 / 16);
          					swpOgImage.css({ height: smHeight + 'px' });
          				} else {
          					smHeight = swpOgImage.find('img').height();
          					swpOgImage.css({ height: smHeight + 'px' });
          				}

          				var pinWidth, pinHeight;

          				if (swpPinImage.length && swpPinImage.is(':empty')) {
          					// Setup the Open Graph Image Placeholder
          					pinWidth = swpPinImage.width();
          					pinHeight = pinWidth * (3 / 2);
          					swpPinImage.css({ height: pinHeight + 'px' });
          				} else {
          					pinHeight = swpPinImage.find('img').height();
          					swpPinImage.css({
          						height: pinHeight + 'px'
          					});
          				}
      			}, 1000);
    		}

    		swpConditionalFields();
    		$('.swp_popular_post_options select').on('change', function() {
    		  	swpConditionalFields();
    		});
  	});
})(this, jQuery);
