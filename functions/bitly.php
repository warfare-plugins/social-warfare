<?php

/*****************************************************************
*                                                                *
*          Link Shortening & Analytics Functions	             *
*                                                                *
******************************************************************/

// Enqueue the Bitly Shortener Hook
add_filter('sw_link_shortening'  	, 'sw_bitly_shortener' );
add_filter('sw_analytics' 			, 'sw_google_analytics' );


/*****************************************************************
*                                                                *
*          The Google Analytics Filter Function		             *
*                                                                *
******************************************************************/

function sw_google_analytics( $array ) {

	// Fetch the user options
	$options = sw_get_user_options();
	$url = $array['url'];
	$network = $array['network'];

	// Check if Analytics have been enabled or not
	if($options['googleAnalytics'] == true):
		if (strpos($url,'?') !== false) :
			$url = $url.urlencode('&utm_source='.$network.'&utm_medium='.$options['analyticsMedium'].'&utm_campaign='.$options['analyticsCampaign'].'');
		else:
			$url = $url.urlencode('?utm_source='.$network.'&utm_medium='.$options['analyticsMedium'].'&utm_campaign='.$options['analyticsCampaign'].'');
		endif;
		return $url;
	else:
		return $url;
	endif;
}

/*****************************************************************
*                                                                *
*          The Bitly Link Shortener	Function		             *
*                                                                *
******************************************************************/

// The Bitly Shortener Function called by the filter hook
function sw_bitly_shortener( $array ) {

	$url = $array['url'];
	$network = $array['network'];
	$postID = $array['postID'];

	// Fetch the User's Options
	$options = sw_get_user_options();
	
	// If Link shortening is activated....
	if($options['linkShortening'] == true) :

		// If Bitly is activated and we have all the appropriate credentials....
		if(isset($options['bitly_access_token'])):

			// Collect our bitly login information
			$access_token = $options['bitly_access_token'];

			// If Google Analytics is Activated....
			if($options['googleAnalytics'] == true):

				// If the Cache is still fresh or a previous API request failed....
				if(sw_is_cache_fresh($postID) == true || (isset($_GLOBALS['bitly_status']) && $_GLOBALS['bitly_status'] == 'failure') ):

					// If the link has already been shortened....
					$existingURL 		= get_post_meta($postID,'bitly_link_'.$network,true);
					if($existingURL && sw_is_cache_fresh($postID) == true):
						return $existingURL;

					// If the link has NOT already been shortened
					else:

						// ....Return the normal URL
						return $url;

					endif;

				// If the Cache is NOT fresh....
				else:

					// If the API provides a shortened URL...
					$shortURL = sw_make_bitly_url( urldecode($url) , $network , $access_token );
					if($shortURL):

						// Store the link in the cache and return it to the buttons
						delete_post_meta($postID,'bitly_link_'.$network);
						update_post_meta($postID,'bitly_link_'.$network,$shortURL);
						return $shortURL;

					// If the API does not provide a shortened URL....
					else:

						// Set a variable we'll check to avoid multiple calls to bitly upon the first failure
						$_GLOBALS['sw']['bitly_status'] = 'failure';

						// Return the normal URL
						return $url;

					// End the check for a shortneing link from the API
					endif;

				// End the check for the cache being fresh
				endif;

			// If Google Analytics is NOT activated....
			else:

				// If the cache is fresh or if the API has failed already....
				if(sw_is_cache_fresh($postID) == true || (isset($_GLOBALS['bitly_status']) && $_GLOBALS['bitly_status'] == 'failure')):

					// If we have a shortened URL in the cache....
					$existingURL = get_post_meta($postID,'bitly_link',true);
					if($existingURL):

						// Save the link in a constant for use in other parts of the loops
						$_GLOBALS['sw']['links'][$postID] = $existingURL;

						// Return the shortened URL
						return $existingURL;

					// If we don't have a shortlink in the cache....
					else:

						// Return the normal URL
						return $url;

					endif;

				// If the cache is expired and needs to be rebuilt....
				else:

					// If we've already generated this link....
					if(isset($_GLOBALS['sw']['links'][$postID])):

						return $_GLOBALS['sw']['links'][$postID];

					// If we've don't already have a generated link....
					else:

						// Use the bitly function to construct a shortened link
						$shortURL = sw_make_bitly_url( urldecode($url) , $network , $access_token );

						// If we got a shortened URL from their API....
						if($shortURL):

							// Save the link in a global so we can skip this part next time
							$_GLOBALS['sw']['links'][$postID] = $shortURL;

							// Delete the meta fields and then update to keep the database clean and up to date.
							delete_post_meta($postID,'bitly_link_'.$network);
							delete_post_meta($postID,'bitly_link');
							update_post_meta($postID,'bitly_link',$shortURL);

							// Return the short URL
							return $shortURL;

						// If didn't get a shortened URL from their API....
						else:

							// Set a variable we'll check to avoid multiple calls to bitly upon the first failure
							$_GLOBALS['sw']['bitly_status'] = 'failure';

							// Return the normal URL
							return $url;

						// End check for shorte URL from the API
						endif;

					// End check for link in the Global Variable
					endif;

				// End check for the cache freshness
				endif;

			// End check for Analytics
			endif;

		// If Bitly is not activated or we don't have the credentials provided....
		else:

			// Return the normal URL
			return $url;

		// End the check for bitly activation and credentials
		endif;

	// If link shortening is not activated....
	else:

		// Return the normal URL
		return $url;

	// End the check for link shortening being activated
	endif;
}

function sw_make_bitly_url( $url , $network , $access_token) {

	// Fetch the user's options
	$options = sw_get_user_options();

	/* Create a link to check if the permalink has already been shortened.
	$bitly_lookup_url = 'https://api-ssl.bitly.com/v3/user/link_lookup?url='.urlencode($url).'&access_token='.$access_token;

	// Fetch a response from the Bitly Lookup API
	$bitly_lookup_response = sw_file_get_contents_curl( $bitly_lookup_url );

	// Parse the JSON formatted response from the Bitly Lookup API
	$bitly_lookup_response = json_decode( $bitly_lookup_response , true );

	// If the lookup returned a valid, previously generated short link....
	if( isset( $bitly_lookup_response['data']['link_lookup'][0]['link'] ) && true == false ):

		// Store the short url to return to the plugin
		$short_url = $bitly_lookup_response['data']['link_lookup'][0]['link'];

	// If the lookup did not return a valid short link....
	else:*/

		// Set the format to json
		$format='json';

		// Create a link to reach the Bitly API
		$bitly_api = 'https://api-ssl.bitly.com/v3/shorten?access_token='.$access_token.'&uri='.urlencode($url).'&format='.$format;

		// Fetch a response from the Bitly Shortening API
		$data = sw_file_get_contents_curl($bitly_api);

		// Parse the JSON formated response into an array
		$data = json_decode( $data , true);
		
		// If the shortening succeeded....
		if( isset ( $data['data']['url'] ) ) :

			// Store the short URL to return to the plugin
			$short_url = $data['data']['url'];

		// If the shortening failed....
		else:

			// Return a status of false
			$short_url = false;

		endif;

	// endif;

	return $short_url;

}

/*****************************************************************
*                                                                *
* 	Apply the Link Shortening and Analytics Filters to the URL	 *
*                                                                *
******************************************************************/

function sw_process_url( $url , $network , $postID) {

	// $bitly_api = 'https://api-ssl.bitly.com/v3/link/lookup?url='.urlencode($url).'&login='.$login.'&apiKey='.$appkey;
	// $data = sw_file_get_contents_curl($bitly_api);
	// $data = json_decode($data);
	// var_dump($data);

	// Check to see if we've already shortened this link in another section of the loop.
	// This will only be set if analytics are turned off and bitly is turned on.
	// Since all links will be the same, this will be generated on the first request and
	// then stored for immediate use on subsequent requests.
	if(isset($_GLOBALS['sw']['links'][$postID])):
		return $_GLOBALS['sw']['links'][$postID];
	else:

		// $options = sw_get_user_options();
		$array['url'] = $url;
		$array['network'] = $network;
		$array['postID'] = $postID;
		$array['url'] = apply_filters('sw_analytics' , $array);
		$url = apply_filters('sw_link_shortening' , $array);
		return $url;

	endif;

}

/*****************************************************************
*                                                                *
* 	BITLY OAUTH TRIGGER RESPONSE								 *
*                                                                *
******************************************************************/
add_action( 'wp_ajax_nopriv_sw_bitly_oauth', 'sw_bitly_oauth_callback' );
function sw_bitly_oauth_callback() {

	// Fetch the User's Options Array
	$sw_user_options = sw_get_user_options();

	// Set the premium code to null
	$sw_user_options['bitly_access_token'] = $_GET['access_token'];

	// Update the options array with the premium code nulled
	update_option('socialWarfareOptions',$sw_user_options);

	echo admin_url( 'admin.php?page=social-warfare' );

};
