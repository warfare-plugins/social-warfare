<?php

/**
 * A function to fetch all the social shares
 *
 * @since 	1.0.0
 * @param  integer $postID The post ID
 * @return array $shares An array of share data
 */
function get_social_warfare_shares( $postID ) {
	global $swp_user_options, $swp_social_networks;

	if ( true === is_attachment() ) :
		return false;
	endif;

	// Set the initial options
	$options = $swp_user_options;
	$url     = get_permalink( $postID );
	$url     = apply_filters( 'swp_url_filter_function', $url );

	/**
	 * Check if the cache is fresh or expired
	 * @var boolean
	 */
	$freshCache = swp_is_cache_fresh( $postID );

	/**
	 * Setup the networks array that we'll loop through
	 */

	// Initiate the ShareCount variable
	$shares['total_shares'] = 0;

	// Queue up the networks that are available
	$networks = $options['order_of_icons'];

	foreach ( $networks as $network ):
		$temp_array[] = swp_snake_case ( $network );;
	endforeach;
	$networks = $temp_array;

	$icons_array = array(
		'type'		=> 'buttons'
	);

	foreach ( $networks as $network ) :

		// Check if we can used the cached share numbers
		if ( $freshCache == true ) :
			$shares[$network] = get_post_meta( $postID,'_' . $network . '_shares',true );

		// If cache is expired, fetch new and update the cache
		else :
			if( isset( $swp_social_networks[$network] ) ):
				$old_shares[$network]  	= get_post_meta( $postID,'_' . $network . '_shares',true );
				$api_responses[$network]	= $swp_social_networks[$network]->get_api_link( $url );
			endif;
		endif;

	endforeach;

	// Recover Shares From Previously Used URL Patterns
	if ( true == $options['recover_shares'] && false == $freshCache ) :

		$alternateURL = SWP_Permalink::get_alt_permalink( $postID );
		$alternateURL = apply_filters( 'swp_recovery_filter', $alternateURL );

		$altURLs = '';
		$altURLs = apply_filters('swp_additional_url_to_check', $altURLs );

		// Debug the Alternate URL being checked
		if ( _swp_is_debug( 'recovery' ) ) {
			echo $alternateURL;
			echo $altURLs;
		}

		foreach ( $networks as $network ) :

			$old_share_links[$network] = call_user_func( 'swp_' . $network . '_request_link',$alternateURL );

			if( !empty($altURLs) ):
				$altURLs_share_links[$network] = call_user_func( 'swp_' . $network . '_request_link' , $altURLs );
			endif;

		endforeach;
	endif;

	if ( $freshCache == true ) :
		if ( get_post_meta( $postID,'_totes',true ) ) :
			$shares['total_shares'] = get_post_meta( $postID, '_totes', true );

		else :
			$shares['total_shares'] = 0;
		endif;
	else :

		// Fetch all the share counts asyncrounously
		$raw_shares_array = SWP_CURL::fetch_shares_via_curl_multi( $api_responses );

		foreach ( $networks as $network ) :

			if ( ! isset( $raw_shares_array[$network] ) ) {
				$raw_shares_array[$network] = 0;
			}

			if ( ! isset( $old_raw_shares_array[$network] ) ) {
				$old_raw_shares_array[$network] = 0;
			}

            $shares[$network] = $swp_social_networks[$network]->parse_api_response($api_responses[$network]);

			if ( $options['recover_shares'] == true ) :
                $old_raw_shares_array = SWP_CURL::fetch_shares_via_curl_multi( $old_share_links );

				$recovered_shares[$network] = call_user_func( 'swp_format_' . $network . '_response', $old_raw_shares_array[$network] );

				if( !empty($altURLs) ):
                    $altURLs_raw_shares_array = SWP_CURL::fetch_shares_via_curl_multi( $altURLs_share_links );
					$altURLs_recovered_shares[$network] = call_user_func( 'swp_format_' . $network . '_response', $altURLs_raw_shares_array[$network] );
				endif;

				if ( $shares[$network] != $recovered_shares[$network] ) :
					$shares[$network] = $shares[$network] + $recovered_shares[$network];
				endif;

				if( !empty($altURLs) ):
					$shares[$network] = $shares[$network] + $altURLs_recovered_shares[$network];
				endif;
			endif;

			if ( $shares[$network] < $old_shares[$network] && false === _swp_is_debug('force_new_shares') ) :
				$shares[$network] = $old_shares[$network];

			elseif($shares[$network] > 0) :
				delete_post_meta( $postID,'_' . $network . '_shares' );
				update_post_meta( $postID,'_' . $network . '_shares',$shares[$network] );

			endif;

			if (is_numeric( $shares[$network] ) ):
				$shares['total_shares'] += $shares[$network];

			endif;

		endforeach;
	endif;

	/**
	* Update the Cache and Return the Share Counts
	*/
	if ( $freshCache != true ) :

		// Clean out the previously used custom meta fields
		delete_post_meta( $postID,'_totes' );

		// Add the new data to the custom meta fields
		update_post_meta( $postID,'_totes',$shares['total_shares'] );

	endif;

	// Return the share counts
	return $shares;

}
