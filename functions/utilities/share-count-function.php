<?php

/**
 * A function to fetch all the social shares
 *
 * @since 	1.0.0
 * @param  integer $postID The post ID
 * @return array $shares An array of share data
 */
function get_social_warfare_shares( $postID ) {
	global $swp_user_options;

	// Set the initial options
	$options = $swp_user_options;
	$url     = get_permalink( $postID );

	/**
	 * Check if the cache is fresh or expired
	 * @var boolean
	 */
	$freshCache = swp_is_cache_fresh( $postID );

	/**
	 * Setup the networks array that we'll loop through
	 */

	// Initiate the ShareCount variable
	$shares['totes'] = 0;

	// Queue up the networks that are available
	$availableNetworks = $options['newOrderOfIcons'];
	$networks = array();
	foreach ( $availableNetworks as $key => $value ) :
		if ( $options['newOrderOfIcons'][ $key ] ) {
			$networks[] = $key;
		}
	endforeach;

	$icons_array = array(
		'type'		=> 'buttons'
	);

	/**
	* Loop through the Networks and fetch their share counts
	*/
	if(isset($networks['active'])) { unset($networks['active']); }
	foreach ( $networks as $network ) :

		// Check if we can used the cached share numbers
		if ( $freshCache == true ) :
			$shares[ $network ] = get_post_meta( $postID,'_' . $network . '_shares',true );

			// If cache is expired, fetch new and update the cache
			else :
				$old_shares[ $network ]  	= get_post_meta( $postID,'_' . $network . '_shares',true );
				$share_links[ $network ]	= call_user_func( 'swp_' . $network . '_request_link',$url );
			endif;

		endforeach;

		// Recover Shares From Previously Used URL Patterns
	if ( $options['recover_shares'] == true && $freshCache == false ) :

		$alternateURL = swp_get_alt_permalink( $postID );
		$alternateURL = apply_filters( 'swp_recovery_filter',$alternateURL );

		// Debug the Alternate URL being checked
		if ( _swp_is_debug( 'recovery' ) ) {
			echo $alternateURL;
		}

		foreach ( $networks as $network ) :

			$old_share_links[ $network ] = call_user_func( 'swp_' . $network . '_request_link',$alternateURL );

			endforeach;
		endif;

	if ( $freshCache == true ) :
		if ( get_post_meta( $postID,'_totes',true ) ) :
			$shares['totes'] = get_post_meta( $postID,'_totes',true );
			else :
				$shares['totes'] = 0;
			endif;
		else :

			// Fetch all the share counts asyncrounously
			$raw_shares_array = swp_fetch_shares_via_curl_multi( $share_links );
			if ( $options['recover_shares'] == true ) :
				$old_raw_shares_array = swp_fetch_shares_via_curl_multi( $old_share_links );
			endif;

			foreach ( $networks as $network ) :

				if ( ! isset( $raw_shares_array[ $network ] ) ) { $raw_shares_array[ $network ] = 0;
				}
				if ( ! isset( $old_raw_shares_array[ $network ] ) ) { $old_raw_shares_array[ $network ] = 0;
				}

				$shares[ $network ] = call_user_func( 'swp_format_' . $network . '_response',$raw_shares_array[ $network ] );
				if ( $options['recover_shares'] == true ) :
					$recovered_shares[ $network ] = call_user_func( 'swp_format_' . $network . '_response',$old_raw_shares_array[ $network ] );
					if ( $shares[ $network ] != $recovered_shares[ $network ] ) :
						$shares[ $network ] = $shares[ $network ] + $recovered_shares[ $network ];
					endif;
				endif;
				if ( $shares[ $network ] <= $old_shares[ $network ] ) :
					$shares[ $network ] = $old_shares[ $network ];
				else :
					delete_post_meta( $postID,'_' . $network . '_shares' );
					update_post_meta( $postID,'_' . $network . '_shares',$shares[ $network ] );
				endif;
				if (is_numeric( $shares[ $network ] ) ):
					$shares['totes'] += $shares[ $network ];
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
			update_post_meta( $postID,'_totes',$shares['totes'] );

		endif;

		// Return the share counts
		return $shares;

}
