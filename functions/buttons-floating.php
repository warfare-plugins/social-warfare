<?php

/**

 * **************************************************************
 *                                                                *
 *          Side Fixed Floater Function				             *
 *                                                                *
 ******************************************************************/

function socialWarfareSideFloat() {
	global $swp_user_options;

	// Get the options...or create them if they don't exist
	wp_reset_query();

	$postID = get_the_ID();
	$options = $swp_user_options;
	$postType = get_post_type( $postID );

	if ( is_singular() ) :
		$postType = get_post_type( $postID );
		if ( isset( $options[ 'float_location_' . $postType ] ) ) :
			$visibility = $options[ 'float_location_' . $postType ];
			else :
				$visibility = 'on';
			endif;
		else :
			$visibility = 'on';
		endif;

		if ( is_singular() && get_post_status( $postID ) == 'publish' && get_post_meta( $postID , 'nc_floatLocation' , true ) != 'off' && $visibility == 'on' && ! is_home() ) :

			// Acquire the social stats from the networks
			// Acquire the social stats from the networks
			if ( isset( $array['url'] ) ) :
				$buttonsArray['url'] = $array['url'];
			else :
				$buttonsArray['url'] = get_permalink( $postID );
			endif;

			if ( $options['float'] && is_singular() ) :
				$floatOption = 'float' . ucfirst( $options['floatOption'] );
			else :
				$floatOption = 'floatNone';
			endif;

			if ( $options['floatStyleSource'] == true ) :
				$options['sideDColorSet'] = $options['dColorSet'];
				$options['sideIColorSet'] = $options['iColorSet'];
				$options['sideOColorSet'] = $options['oColorSet'];
			endif;

			// Setup the buttons array to pass into the 'swp_network_buttons' hook
			$buttonsArray['shares'] = get_social_warfare_shares( $postID );
			$buttonsArray['count'] = 0;
			$buttonsArray['totes'] = 0;
			$buttonsArray['options'] = $options;
			if ( $buttonsArray['options']['totes'] && $buttonsArray['shares']['totes'] >= $buttonsArray['options']['minTotes'] ) { ++$buttonsArray['count'];
			}
			$buttonsArray['resource'] = array();
			$buttonsArray['postID'] = $postID;

			$buttonsArray = apply_filters( 'swp_network_buttons' , $buttonsArray );

			// Create the social panel
			$assets 		= '<div class="nc_socialPanelSide nc_socialPanel swp_' . $options['floatStyle'] . ' swp_d_' . $options['sideDColorSet'] . ' swp_i_' . $options['sideIColorSet'] . ' swp_o_' . $options['sideOColorSet'] . ' ' . $options['sideReveal'] . '" data-position="' . $options['location_post'] . '" data-float="' . $floatOption . '" data-count="' . $buttonsArray['count'] . '" data-floatColor="' . $options['floatBgColor'] . '" data-screen-width="' . $options['swp_float_scr_sz'] . '" data-transition="' . $options['sideReveal'] . '" data-mobileFloat="'.$options['floatLeftMobile'].'">';

			// Display Total Shares if the Threshold has been met
			if ( $options['totes'] && $buttonsArray['totes'] >= $options['minTotes'] ) :
				$assets .= '<div class="nc_tweetContainer totes totesalt" data-id="6" >';
				$assets .= '<span class="swp_count">' . swp_kilomega( $buttonsArray['totes'] ) . '</span><span class="swp_label"> ' . __( 'Shares','social-warfare' ) . '</span>';
				$assets .= '</div>';
			endif;

			$i = 0;
			// Sort the buttons according to the user's preferences
			if ( isset( $buttonsArray ) && isset( $buttonsArray['buttons'] ) ) :
				foreach ( $buttonsArray['buttons'] as $key => $value ) :
					if ( isset( $buttonsArray['resource'][ $key ] ) && $i <= 5 ) :
						$assets .= $buttonsArray['resource'][ $key ];
					endif;
					++$i;
				endforeach;
			elseif ( $options['orderOfIconsSelect'] == 'manual' ) :
				foreach ( $options['newOrderOfIcons'] as $key => $value ) :
					if ( isset( $buttonsArray['resource'][ $key ] ) && $i <= 5 ) :
						$assets .= $buttonsArray['resource'][ $key ];
					endif;
					++$i;
				endforeach;
			elseif ( $options['orderOfIconsSelect'] == 'dynamic' ) :
				arsort( $buttonsArray['shares'] );
				foreach ( $buttonsArray['shares'] as $thisIcon => $status ) :
					if ( isset( $buttonsArray['resource'][ $thisIcon ] ) && $i <= 5 ) :
						$assets .= $buttonsArray['resource'][ $thisIcon ];
					endif;
					++$i;
				endforeach;
			endif;

			// Close the Social Panel
			$assets .= '</div>';

			echo $assets;

		endif;
}
