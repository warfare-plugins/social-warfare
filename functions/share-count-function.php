<?php

/*****************************************************************
*                                                                *
*       FUNCTION TO FETCH ALL THE NETWORK SHARES                 *
*                                                                *
******************************************************************/
function get_social_warfare_shares($postID) {

		// Set the initial options
		$options 			= swp_get_user_options();
		$url 				= get_permalink( $postID );
		// $url				= 'https://youtu.be/jjK1aUU2Dx4';

/*****************************************************************
*                                                                *
*        Check the Cache		                    			 *
*                                                                *
******************************************************************/

		$freshCache = swp_is_cache_fresh($postID);
		// $freshCache = false;

/*****************************************************************
*                                                                *
*       Setup the Networks Array that we'll loop through		 *
*                                                                *
******************************************************************/

		// Initiate the ShareCount class
		$shares['totes']	= 0;

		// Queue up the networks that are available
		$availableNetworks = $options['newOrderOfIcons'];
		$networks = array();
		foreach($availableNetworks as $key => $value):
			if($options['newOrderOfIcons'][$key]) $networks[] = $key;
		endforeach;

/*****************************************************************
*                                                                *
*       Loop through the Networks                    			 *
*                                                                *
******************************************************************/

		// Loop through the networks and fetch their share counts
		foreach($networks as $network):

			// Check if we can used the cached share numbers
			if($freshCache == true):
				$shares[$network] = get_post_meta($postID,'_'.$network.'_shares',true);

			// If cache is expired, fetch new and update the cache
			else:
				$old_shares[$network]  	= get_post_meta($postID,'_'.$network.'_shares',true);
				$share_links[$network]	= call_user_func('swp_'.$network.'_request_link',$url);
			endif;

		endforeach;

		// Recover Shares From Previously Used URL Patterns
		if($options['recover_shares'] == true && $freshCache == false):

			$alternateURL = swp_get_alt_permalink($postID);
			$alternateURL = apply_filters('swp_recovery_filter',$alternateURL);

			// Debug the Alternate URL being checked
			if(isset($_GET['swp_recovery_debug']) && $_GET['swp_recovery_debug'] == true):
				echo $alternateURL;
			endif;

			foreach($networks as $network):

				$old_share_links[$network] = call_user_func('swp_'.$network.'_request_link',$alternateURL);

			endforeach;
		endif;

		if($freshCache == true):
			if(get_post_meta($postID,'_totes',true)) :
				$shares['totes'] = get_post_meta($postID,'_totes',true);
			else:
				$shares['totes'] = 0;
			endif;
		else:

			// Fetch all the share counts asyncrounously
			$raw_shares_array = swp_fetch_shares_via_curl_multi($share_links);
			if($options['recover_shares'] == true):
				$old_raw_shares_array = swp_fetch_shares_via_curl_multi($old_share_links);
			endif;

			foreach($networks as $network):

				if(!isset($raw_shares_array[$network])) $raw_shares_array[$network] = 0;
				if(!isset($old_raw_shares_array[$network])) $old_raw_shares_array[$network] = 0;

				$shares[$network] = call_user_func('swp_format_'.$network.'_response',$raw_shares_array[$network]);
				if($options['recover_shares'] == true):
					$recovered_shares[$network] = call_user_func('swp_format_'.$network.'_response',$old_raw_shares_array[$network]);
					if($shares[$network] != $recovered_shares[$network]):
						$shares[$network] = $shares[$network] + $recovered_shares[$network];
					endif;
				endif;
				if($shares[$network] <= $old_shares[$network]):
					$shares[$network] = $old_shares[$network];
				else:
					delete_post_meta($postID,'_'.$network.'_shares');
					update_post_meta($postID,'_'.$network.'_shares',$shares[$network]);
				endif;
				$shares['totes'] += $shares[$network];

			endforeach;
		endif;

/*****************************************************************
*                                                                *
*       Update the Cache and Return the Share Counts   			 *
*                                                                *
******************************************************************/

		if($freshCache != true):

			// Clean out the previously used custom meta fields
			delete_post_meta($postID,'_totes');

			// Add the new data to the custom meta fields
			update_post_meta($postID,'_totes',$shares['totes']);

		endif;

		// Return the share counts
		return $shares;

	}
