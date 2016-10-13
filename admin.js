// Function for SM Title Counting
function smTitleRemaining() {
	var smTitle = jQuery('#socialWarfare textarea#nc_ogTitle').val();
	var remaining = 60 - smTitle.length;
	if(smTitle.length > 0 && remaining >= 0) {
		jQuery('#socialWarfare .nc_ogTitleWrapper .swp_CountDown').removeClass('swp_red').addClass('swp_blue');
	} else if (smTitle.length > 0 && remaining < 0 ) {
		jQuery('#socialWarfare .nc_ogTitleWrapper .swp_CountDown').removeClass('swp_blue').addClass('swp_red');
	} else {
		jQuery('#socialWarfare .nc_ogTitleWrapper .swp_CountDown').removeClass('swp_blue').removeClass('swp_red');
	}
	jQuery('#socialWarfare .nc_ogTitleWrapper .counterNumber').html(remaining);
}

// Function for SM Description Counting
function smDescriptionRemaining() {
	var smDescription = jQuery('#socialWarfare textarea#nc_ogDescription').val();
	var remaining = 160 - smDescription.length;
	if(smDescription.length > 0 && remaining >= 0) {
		jQuery('#socialWarfare .nc_ogDescriptionWrapper .swp_CountDown').removeClass('swp_red').addClass('swp_blue');
	} else if (smDescription.length > 0 && remaining < 0 ) {
		jQuery('#socialWarfare .nc_ogDescriptionWrapper .swp_CountDown').removeClass('swp_blue').addClass('swp_red');
	} else {
		jQuery('#socialWarfare .nc_ogDescriptionWrapper .swp_CountDown').removeClass('swp_blue').removeClass('swp_red');
	}
	jQuery('#socialWarfare .nc_ogDescriptionWrapper .counterNumber').html(remaining);
}

// Function for Twitter Box Counting
function twitterRemaining() {
	var smTwitter = jQuery('#socialWarfare textarea#nc_customTweet').val();
	var handle = jQuery('#socialWarfare .twitterIDWrapper label').html();
	if(smTwitter.indexOf('http') > -1 || smTwitter.indexOf('https') > -1) {
		linkSpace = 0;
		jQuery('.tweetLinkSection').css({'text-decoration':'line-through'});
	} else {
		linkSpace = 23;
		jQuery('.tweetLinkSection').css({'text-decoration':'none'});
	};
	if(typeof handle === 'undefined') {
		var remaining = 140 - link_length(smTwitter) - linkSpace;
	} else {
		var remaining = 140 - link_length(smTwitter) - handle.length - linkSpace - 6;
	}
	if(smTwitter.length > 0 && remaining >= 0) { jQuery('#socialWarfare .nc_customTweetWrapper .swp_CountDown').removeClass('swp_red').addClass('swp_blue');
	} else if (smTwitter.length > 0 && remaining < 0 ) { jQuery('#socialWarfare .nc_customTweetWrapper .swp_CountDown').removeClass('swp_blue').addClass('swp_red');
	} else { jQuery('#socialWarfare .nc_customTweetWrapper .swp_CountDown').removeClass('swp_blue').removeClass('swp_red'); }
	jQuery('#socialWarfare .nc_customTweetWrapper .counterNumber').html(remaining);
}

function link_length(input) {
  var tmp = "";
  for(var i = 0; i < 23; i++){tmp+="o"}
  return input.replace(/(http:\/\/[\S]*)/g, tmp).length;
};

jQuery(document).ready( function() {
	if(jQuery('#socialWarfare.postbox').length) {

		// Add the CountDown Box for the Social Media Title
		jQuery('#socialWarfare #nc_ogTitle').parent().prepend('<div class="swp_CountDown"><span class="counterNumber">60</span> '+swp_localize_admin.swp_characters_remaining+'</div>');

		// Add the CountDown Box for the Social Media Description
		jQuery('#socialWarfare #nc_ogDescription').parent().prepend('<div class="swp_CountDown"><span class="counterNumber">150</span> '+swp_localize_admin.swp_characters_remaining+'</div>');

		// Add the CountDown Box for the Twitter Box
		jQuery('#socialWarfare #nc_customTweet').parent().prepend('<div class="swp_CountDown"><span class="counterNumber">118</span> '+swp_localize_admin.swp_characters_remaining+'</div>');

		smTitleRemaining();
		jQuery('#socialWarfare textarea#nc_ogTitle').on('input', function() { smTitleRemaining(); });

		smDescriptionRemaining();
		jQuery('#socialWarfare textarea#nc_ogDescription').on('input', function() { smDescriptionRemaining(); });

		twitterRemaining();
		jQuery('#socialWarfare textarea#nc_customTweet').on('input', function() { twitterRemaining(); });

		// Setup an initilazation loop
		var swp_post_initialization = setInterval( function() {

			var swp_og_image 	= jQuery('.nc_ogImageWrapper ul.rwmb-media-list');
			var swp_pin_image 	= jQuery('.nc_pinterestImageWrapper ul.rwmb-media-list');

			// Check if the media list has been created yet
			if(swp_og_image.length && swp_og_image.is(':empty')) {

				// Setup the Open Graph Image Placeholder
				var smWidth = swp_og_image.width();
				var smHeight = smWidth * (9/16);
				swp_og_image.css({height:smHeight+'px'});

			} else {

				var smHeight = swp_og_image.find('img').height();
				swp_og_image.css({height:smHeight+'px'});

			}

			if(swp_pin_image.length && swp_pin_image.is(':empty')) {

				// Setup the Open Graph Image Placeholder
				var pinWidth = swp_pin_image.width();
				var pinHeight = pinWidth * (3/2);
				swp_pin_image.css({height:pinHeight+'px'});

			} else {

				var pinHeight = swp_pin_image.find('img').height();
				swp_pin_image.css({height:pinHeight+'px'});

			}


		} , 1000 );

	};

	// Show and Hide the Count Label based on if we're showing counts
	jQuery('.swp_popular_post_options .showCount select').on('change', function() {
		var value = jQuery(this).val()
		if(value = true) {
			jQuery('.swp_popular_post_options .countLabel').slideDown('slow');
		} else {
			jQuery('.swp_popular_post_options .countLabel').slideUp('slow');
		}
	});

	// Show and Hide the Thumbnail size based on if we're showing thmbnails
	jQuery('.swp_popular_post_options .thumbnails select').on('change', function() {
		var value = jQuery(this).val()
		if(value = true) {
			jQuery('.swp_popular_post_options .thumb_size').slideDown('slow');
		} else {
			jQuery('.swp_popular_post_options .thumb_size').slideUp('slow');
		}
	});

	// Show and Hide the Custom fields based on if we're using a custom color scheme
	jQuery('.swp_popular_post_options .style select').on('change', function() {
		var value = jQuery(this).val()
		if(value = 'custom') {
			jQuery('.swp_popular_post_options .custom_bg, .swp_popular_post_options .custom_link').slideDown('slow');
		} else {
			jQuery('.swp_popular_post_options .custom_bg, .swp_popular_post_options .custom_link').slideUp('slow');
		}
	});

	if(jQuery('.postbox#socialWarfare').length){
		var registration_status = jQuery('#socialWarfare .registrationWrapper input').attr('id');
		if( registration_status == 'false' ) {
			jQuery('.postbox#socialWarfare')
				.css({position:'relative',opacity:'0.3'})
				.append('<div class="sw-premium-blocker"></div>');

			jQuery('#socialWarfare .sw-premium-blocker').tooltip({
				items: '#socialWarfare .sw-premium-blocker',
				content: '<i></i>Unlock these features by registering your license.',
				position: {
					my: 'center top',
					at: 'center top'
				},
				tooltipClass: "sw-admin-hover-notice",
				open: function(event, ui)
				{
					if (typeof(event.originalEvent) === 'undefined')
					{
						return false;
					}

					var $id = jQuery(ui.tooltip).attr('id');

					// close any lingering tooltips
					jQuery('div.ui-tooltip').not('#' + $id).remove();

					// ajax function to pull in data and add it to the tooltip goes here
				},
				close: function(event, ui)
				{
					ui.tooltip.hover(function()
					{
						jQuery(this).stop(true).fadeTo(400, 1);
					},
					function()
					{
						jQuery(this).fadeOut('400', function()
						{
							jQuery(this).remove();
						});
					});
				}
			});

		}
	};



});
