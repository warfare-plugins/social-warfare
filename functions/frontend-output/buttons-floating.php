<?php

/**

 * ***************************************************************
 *                                                                *
 *          Side Fixed Floater Function				              *
 *                                                                *
 ******************************************************************/

/**
*
* @since 2.4.0 Added checks and classes for float_verical and float_button_size
*/
function socialWarfareSideFloat() {
	global $swp_user_options;

	// Get the options...or create them if they don't exist
	wp_reset_query();

	$postID = get_the_ID();
	$options = $swp_user_options;
	$postType = get_post_type( $postID );

	if ( is_singular() && ! is_attachment() ) :
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

			$class = "";

			// Acquire the social stats from the networks
			if ( isset( $array['url'] ) ) :
				$buttonsArray['url'] = $array['url'];
			else :
				$buttonsArray['url'] = get_permalink( $postID );
			endif;

			if ( $options['floating_panel'] && is_singular() ) :
				$floatOption = 'floating_panel' . ucfirst( $options['float_position'] );
				$class = "swp_float_" . $options['float_position'];
			else :
				$floatOption = 'floatNone';
			endif;

			if ( $options['float_style_source'] == true ) :
				$options['float_default_colors'] = $options['default_colors'];
				$options['float_single_colors'] = $options['single_colors'];
				$options['float_hover_colors'] = $options['hover_colors'];
			endif;

			// *Get the vertical position
			if ($options['float_vertical'] && $options['float_vertical'] !== 'center' ) :
				$class .= " swp_side_${options['float_vertical']} ";
			endif;

            // *Set button size
            if ( isset($options['float_button_size']) ) :
                $position = $options['float_vertical'];
                $size = $options['float_button_size'] * 100;
                $side = $options['float_position'];

                if ($side === 'right') :

                endif;

                $class .= " scale-${size} trans-origin-${position}-${side}";

            endif;

			// Setup the buttons array to pass into the 'swp_network_buttons' hook
			$buttonsArray['shares'] = get_social_warfare_shares( $postID );
			$buttonsArray['count'] = 0;
			$buttonsArray['total_shares'] = 0;
			$buttonsArray['options'] = $options;
			if ( $buttonsArray['options']['total_shares'] && $buttonsArray['shares']['total_shares'] >= $buttonsArray['options']['minimum_shares'] ) { ++$buttonsArray['count'];
			}
			$buttonsArray['resource'] = array();
			$buttonsArray['postID'] = $postID;

			$buttonsArray = apply_filters( 'swp_network_buttons' , $buttonsArray );

			// Create the social panel
			$assets 		= '<div class="nc_socialPanelSide nc_socialPanel swp_' . $options['float_button_shape'] . ' swp_d_' . $options['float_default_colors'] . ' swp_i_' . $options['float_single_colors'] . ' swp_o_' . $options['float_hover_colors'] . ' ' . $options['transition'] . ' ' . $class . ' ' . '" data-position="' . $options['location_post'] . '" data-float="' . $floatOption . '" data-count="' . $buttonsArray['count'] . '" data-floatColor="' . $options['float_background_color'] . '" data-screen-width="' . $options['float_screen_width'] . '" data-transition="' . $options['transition'] . '" data-mobileFloat="'.$options['float_mobile'].'">';

			// Display Total Shares if the Threshold has been met
			if ( $options['total_shares'] && $buttonsArray['total_shares'] >= $options['minimum_shares'] ) :
				$assets .= '<div class="nc_tweetContainer totes totesalt" data-id="6" >';
				$assets .= '<span class="swp_count">' . swp_kilomega( $buttonsArray['total_shares'] ) . '</span><span class="swp_label"> ' . __( 'Shares','social-warfare' ) . '</span>';
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
			elseif ( $options['order_of_icons'] == 'manual' ) :
				foreach ( $options['newOrderOfIcons'] as $key => $value ) :
					if ( isset( $buttonsArray['resource'][ $key ] ) && $i <= 5 ) :
						$assets .= $buttonsArray['resource'][ $key ];
					endif;
					++$i;
				endforeach;
			elseif ( $options['order_of_icons'] == 'dynamic' ) :
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
