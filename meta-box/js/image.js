jQuery( function ( $ )
{
	'use strict';

	// Reorder images
	$( '.SW_META-images' ).each( function ()
	{
		var $this = $( this ),
			data = {
				action     : 'SW_META_reorder_images',
				_ajax_nonce: $this.data( 'reorder_nonce' ),
				post_id    : $( '#post_ID' ).val(),
				field_id   : $this.data( 'field_id' )
			};
		$this.sortable( {
			placeholder: 'ui-state-highlight',
			items      : 'li',
			update     : function ()
			{
				data.order = $this.sortable( 'serialize' );
				$.post( ajaxurl, data );
			}
		} );
	} );
} );

// Function for SM Title Counting
function smTitleRemaining() {
	var smTitle = jQuery('#socialWarfare .nc_ogTitle textarea').val();
	var remaining = 60 - smTitle.length;
	if(smTitle.length > 0 && remaining >= 0) {
		jQuery('#socialWarfare .nc_ogTitle .sw_CountDown').removeClass('sw_red').addClass('sw_blue');	
	} else if (smTitle.length > 0 && remaining < 0 ) {
		jQuery('#socialWarfare .nc_ogTitle .sw_CountDown').removeClass('sw_blue').addClass('sw_red');
	} else {
		jQuery('#socialWarfare .nc_ogTitle .sw_CountDown').removeClass('sw_blue').removeClass('sw_red');
	}
	jQuery('#socialWarfare .nc_ogTitle .counterNumber').html(remaining);
}

// Function for SM Description Counting
function smDescriptionRemaining() {
	var smDescription = jQuery('#socialWarfare .nc_ogDescription textarea').val();
	var remaining = 160 - smDescription.length;
	if(smDescription.length > 0 && remaining >= 0) {
		jQuery('#socialWarfare .nc_ogDescription .sw_CountDown').removeClass('sw_red').addClass('sw_blue');	
	} else if (smDescription.length > 0 && remaining < 0 ) {
		jQuery('#socialWarfare .nc_ogDescription .sw_CountDown').removeClass('sw_blue').addClass('sw_red');
	} else {
		jQuery('#socialWarfare .nc_ogDescription .sw_CountDown').removeClass('sw_blue').removeClass('sw_red');
	}
	jQuery('#socialWarfare .nc_ogDescription .counterNumber').html(remaining);
}

// Function for Twitter Box Counting
function twitterRemaining() {
	var smTwitter = jQuery('#socialWarfare .nc_customTweet textarea').val();
	var handle = jQuery('#socialWarfare .twitterID label').html();
	if(smTwitter.indexOf('http') > -1) { 
		linkSpace = 0;
		jQuery('.tweetLinkSection').css({'text-decoration':'line-through'});
	} else { 
		linkSpace = 23; 
		jQuery('.tweetLinkSection').css({'text-decoration':'none'});
	};
	if(typeof handle === 'undefined') {
		var remaining = 140 - getTweetLength(smTwitter) - linkSpace;
	} else {
		var remaining = 140 - getTweetLength(smTwitter) - handle.length - linkSpace - 6;
	}
	if(smTwitter.length > 0 && remaining >= 0) { jQuery('#socialWarfare .nc_customTweet .sw_CountDown').removeClass('sw_red').addClass('sw_blue');	
	} else if (smTwitter.length > 0 && remaining < 0 ) { jQuery('#socialWarfare .nc_customTweet .sw_CountDown').removeClass('sw_blue').addClass('sw_red');
	} else { jQuery('#socialWarfare .nc_customTweet .sw_CountDown').removeClass('sw_blue').removeClass('sw_red'); }
	jQuery('#socialWarfare .nc_customTweet .counterNumber').html(remaining);
}

function getTweetLength(input) {
  var tmp = "";
  for(var i = 0; i < 22; i++){tmp+="o"}
  return input.replace(/(http:\/\/[\S]*)/g, tmp).length;
};

jQuery(document).ready( function() {
	if(jQuery('#socialWarfare.postbox').length) {
		
		// Add the CountDown Box for the Social Media Title
		jQuery('#socialWarfare .nc_ogTitle .SW_META-input').prepend('<div class="sw_CountDown"><span class="counterNumber">60</span> Characters Remaining</div>');
		
		// Add the CountDown Box for the Social Media Description
		jQuery('#socialWarfare .nc_ogDescription .SW_META-input').prepend('<div class="sw_CountDown"><span class="counterNumber">150</span> Characters Remaining</div>');
		
		// Add the CountDown Box for the Twitter Box
		jQuery('#socialWarfare .nc_customTweet .SW_META-input').prepend('<div class="sw_CountDown"><span class="counterNumber">118</span> Characters Remaining</div>');
		
		smTitleRemaining();
		jQuery('#socialWarfare .nc_ogTitle textarea').on('input', function() { smTitleRemaining(); });
		
		smDescriptionRemaining();
		jQuery('#socialWarfare .nc_ogDescription textarea').on('input', function() { smDescriptionRemaining(); });
		
		twitterRemaining();
		jQuery('#socialWarfare .nc_customTweet textarea').on('input', function() { twitterRemaining(); });
		

		var smWidth = jQuery('.nc_ogImage ul.SW_META-images').width(); 
		var smHeight = smWidth * (9/16);

		var pinWidth = jQuery('.nc_pinterestImage ul.SW_META-images').width(); 
		var pinHeight = pinWidth * (3/2);
		
		jQuery('#socialWarfare').prepend('<style>.nc_ogImage ul.SW_META-images.hidden { height: '+smHeight+'px!important } .nc_pinterestImage ul.SW_META-images.hidden { height: '+pinHeight+'px!important }</style>');
	
		
	};
	

	
});