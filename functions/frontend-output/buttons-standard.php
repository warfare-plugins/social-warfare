<?php

/**
 * Register and output header meta tags
 *
 * @package   SocialWarfare\Functions
 * @copyright Copyright (c) 2016, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     1.0.0
 */

defined( 'WPINC' ) || die;

/**
 * A wrapper for the legacy version of the function
 *
 * This version accepted 3 parameters, but was scrapped for a
 * new version that now accepts an array of unlimited parameters
 *
 * @since  1.4.0
 * @access public
 * @param  boolean $content The content to which the buttons will be added
 * @param  string  $where   Where the buttons should appear (above, below, both, none)
 * @param  boolean $echo    Echo the content or return it
 * @return string 			Returns the modified content
 */
function socialWarfare( $content = false, $where = 'default', $echo = true ) {

	// Collect the deprecated fields and place them into an array
	$array['content'] 	= $content;
	$array['where'] 	= $where;
	$array['echo'] 		= $echo;
	$array['devs']		= true;

	// Pass the array into the new function
	return social_warfare( $array );
}

/**
 * THE SHARE BUTTONS FUNCTION:
 *
 * This function accepts an array of parameters resulting in the outputting of
 * the Social Warfare Buttons.
 *
 *
 * ACCEPTED PARAMETERS :
 *
 * content : The post content to which we append the buttons
 *         : (string)
 *
 * where   : Used to overwrite the default location in relation to the content
 *         : ( above | below | both | none )
 *
 * echo    : Used to print or store the variables.
 *         : ( true | false )
 *
 * @since 1.0.0
 * @access public
 * @return string $content The modified content
 */
function social_warfare_buttons( $array = array() ) {
	global $swp_user_options;

	// Setup the default Array parameters
	if ( ! isset( $array['where'] ) ) { $array['where'] = 'default'; }
	if ( ! isset( $array['echo'] ) ) { $array['echo'] = true; }
	if ( ! isset( $array['content'] ) ) { $array['content'] = false; }

	// Get the options...or create them if they don't exist
	if ( isset( $array['post_id'] ) ) :
		$postID = $array['post_id'];
	else :
		$postID = get_the_ID();
	endif;

	$options = $swp_user_options;

	// Check to see if display location was specifically defined for this post
	$specWhere = get_post_meta( $postID,'nc_postLocation',true );
	if ( ! $specWhere ) { $specWhere = 'default';
	};

	if ( $array['where'] == 'default' ) :

		// If we are on the home page
		if( is_front_page() ):
			$array['where'] = $options['locationHome'];

		// If we are on a singular page
		elseif ( is_singular() && ! is_home() && ! is_archive() && ! is_front_page() ) :
			if ( $specWhere == 'default' || $specWhere == '' ) :
				$postType = get_post_type( $postID );
				if ( isset( $options[ 'location_' . $postType ] ) ) :
					$array['where'] = $options[ 'location_' . $postType ];
				else :
					$array['where'] = 'none';
				endif;
			else :
				$array['where'] = $specWhere;
			endif;

		// If we are anywhere else besides the home page or a singular
		else :
			$array['where'] = $options['locationSite'];
		endif;
	endif;

	// Disable the buttons on Buddy Press pages
	if ( function_exists( 'is_buddypress' ) && is_buddypress() ) :
		return $array['content'];

		// Disable the buttons if the location is set to "None / Manual"
	elseif ( $array['where'] == 'none' && ! isset( $array['devs'] ) ) :
		return $array['content'];

		// Disable the button if we're not in the loop, unless there is no content which means the function was called by a developer.
	elseif ( ( ! is_main_query() || ! in_the_loop()) && ! isset( $array['devs'] ) ) :
		return $array['content'];

		// Don't do anything if we're in the admin section
	elseif ( is_admin() ) :
		return $array['content'];

		// If all the checks pass, let's make us some buttons!
	else :

		// Set the options for the horizontal floating bar
		$postType = get_post_type( $postID );
		$spec_float_where = get_post_meta( $postID , 'nc_floatLocation' , true );
		if ( isset( $array['float'] ) && $array['float'] == 'ignore' ) :
			$floatOption = 'float_ignore';
		elseif ( $spec_float_where == 'off' && $options['buttonFloat'] != 'float_ignore' ) :
				$floatOption = 'floatNone';
		elseif ( $options['float'] && is_singular() && $options[ 'float_location_' . $postType ] == 'on' ) :
			$floatOption = 'float' . ucfirst( $options['floatOption'] );
		else :
			$floatOption = 'floatNone';
		endif;

		// Disable the plugin on feeds, search results, and non-published content
		if ( ! is_feed() && ! is_search() && get_post_status( $postID ) == 'publish' ) :

			// Acquire the social stats from the networks
			if ( isset( $array['url'] ) ) :
				$buttonsArray['url'] = $array['url'];
			else :
				$buttonsArray['url'] = get_permalink( $postID );
			endif;

			if ( isset( $array['scale'] ) ) :
				$scale = $array['scale'];
			else :
				$scale = $options['buttonSize'];
			endif;

			// Fetch the share counts
			$buttonsArray['shares'] = get_social_warfare_shares( $postID );

			// Pass the swp_options into the array so we can pass it into the filter
			$buttonsArray['options'] = $options;

			// Customize which buttosn we're going to display
			if ( isset( $array['buttons'] ) ) :

				// Fetch the global names and keys
				$swp_options = array();
				$swp_available_options = apply_filters( 'swp_options',$swp_options );
				$available_buttons = $swp_available_options['options']['swp_display']['buttons']['content'];

				// Split the comma separated list into an array
				$button_set_array = explode( ',', $array['buttons'] );

				// Match the names in the list to their appropriate system-wide keys
				$i = 0;
				foreach ( $button_set_array as $button ) :

					// Trim the network name in case of white space
					$button = trim( $button );

					// Convert the names to their systme-wide keys
					if ( swp_recursive_array_search( $button , $available_buttons ) ) :
						$key = swp_recursive_array_search( $button , $available_buttons );

						// Store the result in the array that gets passed to the HTML generator
						$buttonsArray['buttons'][ $key ] = $button;

						// Declare a default share count of zero. This will be overriden later
						if ( ! isset( $buttonsArray['shares'][ $key ] ) ) :
							$buttonsArray['shares'][ $key ] = 0;
						endif;

					endif;

					$button_set_array[ $i ] = $button;
					++$i;
				endforeach;

				// Manually turn the total shares on or off
				if ( array_search( 'Total',$button_set_array ) ) { $buttonsArray['buttons']['totes'] = 'Total' ;}

			endif;

			// Setup the buttons array to pass into the 'swp_network_buttons' hook
			$buttonsArray['count'] = 0;
			$buttonsArray['totes'] = 0;
			if ( 	( $buttonsArray['options']['totes'] && $buttonsArray['shares']['totes'] >= $buttonsArray['options']['minTotes'] && ! isset( $array['buttons'] ) )
				|| 	( isset( $buttonsArray['buttons'] ) && isset( $buttonsArray['buttons']['totes'] ) && $buttonsArray['totes'] >= $options['minTotes'] ) ) :
				++$buttonsArray['count'];
			endif;
			$buttonsArray['resource'] = array();
			$buttonsArray['postID'] = $postID;

			// Disable the subtitles plugin to avoid letting them inject their subtitle into our share titles
			if ( is_plugin_active( 'subtitles/subtitles.php' ) && class_exists( 'Subtitles' ) ) :
				remove_filter( 'the_title', array( Subtitles::getinstance(), 'the_subtitle' ), 10, 2 );
			endif;

			// This array will contain the HTML for all of the individual buttons
			$buttonsArray = apply_filters( 'swp_network_buttons' , $buttonsArray );

			// Create the social panel
			$assets = '<div class="nc_socialPanel swp_' . $options['visualTheme'] . ' swp_d_' . $options['dColorSet'] . ' swp_i_' . $options['iColorSet'] . ' swp_o_' . $options['oColorSet'] . ' scale-' . $scale*100 .' scale-' . $options['buttonFloat'] . '" data-position="' . $options['location_post'] . '" data-float="' . $floatOption . '" data-count="' . $buttonsArray['count'] . '" data-floatColor="' . $options['floatBgColor'] . '" data-emphasize="'.$options['emphasize_icons'].'">';

			// Setup the total shares count if it's on the left
			if ( ( $options['totes'] && $options['swTotesFormat'] == 'totesAltLeft' && $buttonsArray['totes'] >= $options['minTotes'] && ! isset( $array['buttons'] ) || ( $options['swTotesFormat'] == 'totesAltLeft' && isset( $buttonsArray['buttons'] ) && isset( $buttonsArray['buttons']['totes'] ) && $buttonsArray['totes'] >= $options['minTotes'] ))
			|| 	($options['swTotesFormat'] == 'totesAltLeft' && isset( $array['buttons'] ) && isset( $array['buttons']['totes'] ) && $buttonsArray['totes'] >= $options['minTotes'] ) ) :
				++$buttonsArray['count'];
				$assets .= '<div class="nc_tweetContainer totes totesalt" data-id="' . $buttonsArray['count'] . '" >';
				$assets .= '<span class="swp_count">' . swp_kilomega( $buttonsArray['totes'] ) . ' <span class="swp_label">' . __( 'Shares','social-warfare' ) . '</span></span>';
				$assets .= '</div>';
			endif;

			// Sort the buttons according to the user's preferences
			if ( isset( $buttonsArray ) && isset( $buttonsArray['buttons'] ) ) :
				foreach ( $buttonsArray['buttons'] as $key => $value ) :
					if ( isset( $buttonsArray['resource'][ $key ] ) ) :
						$assets .= $buttonsArray['resource'][ $key ];
					endif;
				endforeach;
			elseif ( $options['orderOfIconsSelect'] == 'manual' ) :
				foreach ( $options['newOrderOfIcons'] as $key => $value ) :
					if ( isset( $buttonsArray['resource'][ $key ] ) ) :
						$assets .= $buttonsArray['resource'][ $key ];
					endif;
				endforeach;
			elseif ( $options['orderOfIconsSelect'] == 'dynamic' ) :
				arsort( $buttonsArray['shares'] );
				foreach ( $buttonsArray['shares'] as $thisIcon => $status ) :
					if ( isset( $buttonsArray['resource'][ $thisIcon ] ) ) :
						$assets .= $buttonsArray['resource'][ $thisIcon ];
					endif;
				endforeach;
			endif;

			// Create the Total Shares Box if it's on the right
			if ( ( $options['totes'] && $options['swTotesFormat'] != 'totesAltLeft' && $buttonsArray['totes'] >= $options['minTotes'] && ! isset( $buttonsArray['buttons'] ) )
			|| 	( $options['swTotesFormat'] != 'totesAltLeft' && isset( $buttonsArray['buttons'] ) && isset( $buttonsArray['buttons']['totes'] ) && $buttonsArray['totes'] >= $options['minTotes'] ) ) :
				++$buttonsArray['count'];
				if ( $options['swTotesFormat'] == 'totes' ) :
					$assets .= '<div class="nc_tweetContainer totes" data-id="' . $buttonsArray['count'] . '" >';
					$assets .= '<span class="swp_count">' . swp_kilomega( $buttonsArray['totes'] ) . ' <span class="swp_label">' . __( 'Shares','social-warfare' ) . '</span></span>';
					$assets .= '</div>';
				else :
					$assets .= '<div class="nc_tweetContainer totes totesalt" data-id="' . $buttonsArray['count'] . '" >';
					$assets .= '<span class="swp_count"><span class="swp_label">' . __( 'Shares','social-warfare' ) . '</span> ' . swp_kilomega( $buttonsArray['totes'] ) . '</span>';
					$assets .= '</div>';
				endif;
			endif;

			// Close the Social Panel
			$assets .= '</div>';

			// Reset the cache timestamp if needed
			if ( swp_is_cache_fresh( $postID ) == false  && isset($options['cacheMethod']) && 'legacy' === $options['cacheMethod'] ) :
				delete_post_meta( $postID,'swp_cache_timestamp' );
				update_post_meta( $postID,'swp_cache_timestamp',floor( ((date( 'U' ) / 60) / 60) ) );
			endif;

			// Add this post ID to the array so we don't process it again.
			// global $post, $swp_already_print;
			// array_push( $swp_already_print, $postID);

			if ( isset( $array['genesis'] ) ) :
				if ( $array['where'] == 'below' && $array['genesis'] == 'below' ) :
					return $assets;
				elseif ( $array['where'] == 'above' && $array['genesis'] == 'above' ) :
					return $assets;
				elseif ( $array['where'] == 'both' ) :
					return $assets;
				elseif ( $array['where'] == 'none' ) :
					return false;
				endif;
			else :
				if ( $array['echo'] == false && $array['where'] != 'none' ) :
					return $assets;
				elseif ( $array['content'] === false ) :
					echo $assets;
				elseif ( $array['where'] == 'below' ) :
					$content = $array['content'] . '' . $assets;
					return $content;
				elseif ( $array['where'] == 'above' ) :
					$content = $assets . '' . $array['content'];
					return $content;
				elseif ( $array['where'] == 'both' ) :
					$content = $assets . '' . $array['content'] . '' . $assets;
					return $content;
				elseif ( $array['where'] == 'none' ) :
					return $array['content'];
				endif;
			endif;
		else :
			return $array['content'];
		endif;

	endif;
}
