<?php

/**
 * A function to fetch all the social shares
 *
 * @since  1.0.0 | Unknown     | Created
 * @since  3.0.3 | 09 MAY 2018 | Added checks for the network objects (isset) to ensure
 *                                we're not calling those methods from strings or other
 *                                random items that will throw errors.
 * @since  3.0.4 | 09 MAY 2018 | Replaced $network-> with $swp_social_networks[$network]->
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
	$url     = apply_filters( 'swp_url_filter_function', $url );

	/**
	 * Check if the cache is fresh or expired
	 * @var boolean
	 */
	$fresh_cache = swp_is_cache_fresh( $postID );

	/**
	 * Setup the networks array that we'll loop through
	 */

	// Initiate the ShareCount variable
	$shares['total_shares'] = 0;

	// Queue up the networks that are available
	$networks = $options['order_of_icons'];



    if ( !is_array( $networks ) || count ( $networks ) === 0 ) :
        return $shares;
    endif;

	$icons_array = array(
		'type' => 'buttons'
	);

	foreach ( $networks as $network ) :

		if( isset( $swp_social_networks[$network] ) ):

			// Check if we can used the cached share numbers
			if ( $fresh_cache == true ) :
				$shares[$network] = get_post_meta( $postID,'_' . $network . '_shares',true );

			// If cache is expired, fetch new and update the cache
			else :
				$old_shares[$network]  	= get_post_meta( $postID,'_' . $network . '_shares',true );
				$api_links[$network]	= $swp_social_networks[$network]->get_api_link( $url );
			endif;

		endif;

	endforeach;

	// Recover Shares From Previously Used URL Patterns
	if ( true == $options['recover_shares'] && false == $fresh_cache ) :

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

			if( isset( $swp_social_networks[$network] ) ):

				$old_share_links[$network] = $swp_social_networks[$network]->get_api_link( $alternateURL );

				if( !empty($altURLs) ):
					$altURLs_share_links[$network] = $swp_social_networks[$network]->get_api_link( $altURLs );
				endif;

			endif;

		endforeach;
	endif;

	if ( $fresh_cache == true ) :
		if ( get_post_meta( $postID,'_total_shares',true ) ) :
			$shares['total_shares'] = get_post_meta( $postID, '_total_shares', true );

		else :
			$shares['total_shares'] = 0;
		endif;
	else :

		// Fetch all the share counts asyncrounously
		$raw_shares_array = SWP_CURL::fetch_shares_via_curl_multi( $api_links );

		if ( $options['recover_shares'] == true ) :
			$old_raw_shares_array = SWP_CURL::fetch_shares_via_curl_multi( $old_share_links );
		endif;

		foreach ( $networks as $network ) :

			if( isset( $swp_social_networks[$network] ) ):

				if ( ! isset( $raw_shares_array[$network] ) ) {
					$raw_shares_array[$network] = 0;
				}

				if ( ! isset( $old_raw_shares_array[$network] ) ) {
					$old_raw_shares_array[$network] = 0;
				}

	            $shares[$network] = $swp_social_networks[$network]->parse_api_response($raw_shares_array[$network]);

				if ( $options['recover_shares'] == true ) :

					$recovered_shares[$network] = $swp_social_networks[$network]->parse_api_response( $old_raw_shares_array[$network] );

					if( !empty($altURLs) ):
	                    $altURLs_raw_shares_array = SWP_CURL::fetch_shares_via_curl_multi( $altURLs_share_links );
						$altURLs_recovered_shares[$network] = $swp_social_networks[$network]->parse_api_response( $altURLs_raw_shares_array[$network] );
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
			endif;

		endforeach;
	endif;

	/**
	* Update the Cache and Return the Share Counts
	*/
	if ( $fresh_cache != true ) :

		// Clean out the previously used custom meta fields
		delete_post_meta( $postID,'_total_shares' );

		// Add the new data to the custom meta fields
		update_post_meta( $postID,'_total_shares',$shares['total_shares'] );

	endif;

	// Return the share counts
	return $shares;

}
