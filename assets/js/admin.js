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

	function swp_selected(name) {
		return jQuery('select[name="' + name + '"]').val();
	}

	function swp_checked(name) {
		return jQuery( '[name="' + name + '"]' ).prop( 'checked' );
	}

	function string_to_bool(string) {
		if( string === 'true') { string = true };
		if( string === 'false') { string = false };
		return string;
	}

	// Loop through all the fields that have dependancies
	jQuery( '[data-dep]' ).each( function() {

		// Fetch the conditional values
		var condition = jQuery(this).data( 'dep' );
		var required = JSON.parse( JSON.stringify( jQuery(this).data( 'dep_val' ) ) );

		// Check if we're on the options page or somewhere else
		if (window.location.href.indexOf("page=social-warfare") === -1) {
			var conditionEl = jQuery(this).parents('.widgets-holder-wrap').find( '[data-swp-name="' + condition + '"]' );
		} else {
			var conditionEl = jQuery( '[name="' + condition + '"]' )[0];
		}

		var value;

		if (typeof conditionEl === 'undefined') {
			conditionEl = jQuery( '[name="' + condition + '"]' )[0];

			if (typeof conditionEl === 'undefined') {
				conditionEl = jQuery( '[fieldjQuery=' + condition + ']' )[0];
			}
		}

		// Fetch the value of checkboxes or other input types
		if ( jQuery( conditionEl ).attr( 'type' ) == 'checkbox' ) {
			value = jQuery( conditionEl ).prop( 'checked' );
		} else {
			value = jQuery( conditionEl ).val();
		}
		value = string_to_bool(value);

        //* Options page uses parent visibilty to check. Widget page does not. This could definiitely look better.
		// Show or hide based on the conditional values (and the dependancy must be visible in case it is dependant)

		if (window.location.href.indexOf("page=social-warfare") !== -1) {

			// If the required value matches and it's parent is also being shown, show this conditional field
			if (jQuery.inArray( value, required ) !== -1 && jQuery( conditionEl ).parent( '.sw-grid' ).is( ':visible' )  ) {
				jQuery(this).show();
			} else {
				jQuery(this).hide();
			}
		} else {

			// If the required value matches, show this conditional field
			if (jQuery.inArray( value, required ) !== -1 || value === required ) {
				jQuery(this).show();
			} else {
				jQuery(this).hide();
			}
		}
	});

	if ( false === swp_checked('float_style_source') &&
	       'custom_color'              === swp_selected('float_default_colors')
	    || 'custom_color_outlines'     === swp_selected('float_default_colors')
	    || 'custom_color'              === swp_selected('float_single_colors')
	    || 'custom_color_outlines'     === swp_selected('float_single_colors')
	    || 'custom_color'              === swp_selected('float_hover_colors')
        || 'custom_color_outlines'     === swp_selected('float_hover_colors') ) {
		jQuery( '.sideCustomColor_wrapper' ).slideDown();

	} else {
		jQuery( '.sideCustomColor_wrapper' ).slideUp();
	}
}

//* Only run on widgets.php
if (window.location.href.indexOf("widgets.php") > -1 ) {

	//* Make sure the elements exist before trying to read them.
	var widgetFinder = setInterval(function() {
		if (typeof swpWidget !== 'undefined') clearInterval(widgetFinder);

		swpWidget = jQuery("#widgets-right [id*=_swp_popular_posts_widget], [id*=_swp_popular_posts_widget].open")[0];
		widgetSubmit = jQuery(swpWidget).find("[idjQuery=savewidget]")[0];

        //* Force swpConditionalFields to run when the widget is opened or saved.
		jQuery(swpWidget).on("click", swpConditionalFields);
		jQuery(widgetSubmit).on("click", function() {
			setTimeout(swpConditionalFields, 600);
		});

	}, 50);
}

(function( window, jQuery, undefined ) {
	'use strict';

  if (typeof $ == 'undefined') {
      $ = jQuery;
  }

	socialWarfareAdmin.linkLength = function( input ) {
		var tmp = '';

		for ( var i = 0; i < 23; i++ ) {
			tmp += 'o';
		}

		return input.replace( /(http:\/\/[\S]*)/g, tmp ).length;
	};

  function updateCharactersRemaining(containerSelector, characterLimit) {
      var input = $("#social_warfare #" + containerSelector );
  		var container = $("#social_warfare ." + containerSelector );
      var remaining = characterLimit - input.val().length

      if (text.length && remaining >= 0) {
          container.find(".swp_CountDown").removeClass("swp_red").addClass("swp_blue")
      } else {
          container.find(".swp_CountDown").removeClass("swp_blue").addClass("swp_red")
      }

  		container.find(".counterNumber").text(remaining)
  }



	// Function for SM Title Counting
	function smTitleRemaining() {
		var smTitle = $( '#social_warfare textarea#swp_og_title' ).val();
		var remaining = 60 - smTitle.length;
		if ( smTitle.length > 0 && remaining >= 0 ) {
			$( '#social_warfare .swp_og_title .swp_CountDown' ).removeClass( 'swp_red' ).addClass( 'swp_blue' );
		} else if ( smTitle.length > 0 && remaining < 0 ) {
			$( '#social_warfare .swp_og_title .swp_CountDown' ).removeClass( 'swp_blue' ).addClass( 'swp_red' );
		} else {
			$( '#social_warfare .swp_og_title .swp_CountDown' ).removeClass( 'swp_blue' ).removeClass( 'swp_red' );
		}
		$( '#social_warfare .swp_og_title .counterNumber' ).html( remaining );
	}

	// Function for SM Description Counting
	function smDescriptionRemaining() {
		var smDescription = $( '#social_warfare textarea#swp_og_description' ).val();
		var remaining = 160 - smDescription.length;
		if ( smDescription.length > 0 && remaining >= 0 ) {
			$( '#social_warfare .swp_og_description .swp_CountDown' ).removeClass( 'swp_red' ).addClass( 'swp_blue' );
		} else if ( smDescription.length > 0 && remaining < 0 ) {
			$( '#social_warfare .swp_og_description .swp_CountDown' ).removeClass( 'swp_blue' ).addClass( 'swp_red' );
		} else {
			$( '#social_warfare .swp_og_description .swp_CountDown' ).removeClass( 'swp_blue' ).removeClass( 'swp_red' );
		}
		$( '#social_warfare .swp_og_description .counterNumber' ).html( remaining );
	}

    // Function for SM Description Counting
	function swpPinterestRemaining() {
		var pinterestDescription = $( '#social_warfare textarea#swp_pinterest_description' ).val();
        if (!pinterestDescription) {
            pinterestDescription = '';
        }
		var remaining = 500 - pinterestDescription.length;
		if ( pinterestDescription.length > 0 && remaining >= 0 ) {
			$( '#social_warfare .swp_pinterest_descriptionWrapper .swp_CountDown' ).removeClass( 'swp_red' ).addClass( 'swp_blue' );
		} else if ( pinterestDescription.length > 0 && remaining < 0 ) {
			$( '#social_warfare .swp_pinterest_descriptionWrapper .swp_CountDown' ).removeClass( 'swp_blue' ).addClass( 'swp_red' );
		} else {
			$( '#social_warfare .swp_pinterest_descriptionWrapper .swp_CountDown' ).removeClass( 'swp_blue' ).removeClass( 'swp_red' );
		}
		$( '#social_warfare .swp_pinterest_descriptionWrapper .counterNumber' ).html( remaining );
    }

	// Function for Twitter Box Counting
	function twitterRemaining() {
		var smTwitter = $( '#social_warfare textarea#swp_custom_tweet' ).val();
		var handle = $( '#social_warfare .twitterIDWrapper label' ).html();
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
			$( '#social_warfare .swp_customTweetWrapper .swp_CountDown' ).removeClass( 'swp_red' ).addClass( 'swp_blue' );
		} else if ( smTwitter.length > 0 && remaining < 0 ) {
			$( '#social_warfare .swp_customTweetWrapper .swp_CountDown' ).removeClass( 'swp_blue' ).addClass( 'swp_red' );
		} else {
			$( '#social_warfare .swp_customTweetWrapper .swp_CountDown' ).removeClass( 'swp_blue' ).removeClass( 'swp_red' );
		}

		$( '#social_warfare .swp_customTweetWrapper .counterNumber' ).html( remaining );
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

    function noticeClickHandlers() {
        $(".swp-notice-cta").on("click", function(e) {
            e.preventDefault();
            //* Do not use $ to get href.
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
    $("#social_warfare #" + selector).parent().prepend('<div class="swp_CountDown"><span class="counterNumber">' + textLimit + '</span> ' + swp_localize_admin.swp_characters_remaining + '</div>');
  }

	$( document ).ready( function() {
      noticeClickHandlers();

		if ( $( '#social_warfare.postbox' ).length ) {
        var textCounters = {
            "swp_og_title": 60,
            "swp_og_description": 150,
            "swp_pinterest_description": 140,
            "swp_custom_tweet": 140
          };

          Object.entries(textCounters).map(function(entry) {
            console.log(entry[0])
              var selector = entry[0];
              var textLimit = entry[1];

              createCharactersRemaining(selector, textLimit);
              updateCharactersRemaining(selector, textLimit);

              $("#social_warfare #" + selector).on("input", function() {
                  updateCharactersRemaining(selector, textLimit);
              });
          });

    			// Setup an initilazation loop
    			var swpPostInit = setInterval( function() {
      				var swpOgImage  = $( '.swp_og_imageWrapper ul.swpmb-media-list' );
      				var swpPinImage = $( '.swp_pinterest_imageWrapper ul.swpmb-media-list' );

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
