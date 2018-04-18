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
    $side_panel = new SWP_Buttons_Panel( ['content' => "" ]);
    $side_panel->render_floating_HTML();
    return;
	global $swp_user_options;

	// Get the options...or create them if they don't exist
	wp_reset_query();


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
				foreach ( $options['order_of_icons'] as $key => $value ) :
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

	

			// echo $assets;

		endif;
}
