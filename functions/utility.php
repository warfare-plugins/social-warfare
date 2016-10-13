<?php

/**

 * **************************************************************
 *                                                                *
 *          ROUND TO THE APPROPRATE THOUSANDS                     *
 *                                                                *
 ******************************************************************/
function swp_kilomega( $val ) {

	// Fetch the user assigned options
	$options = swp_get_user_options();

	// Check if we even have a value to format
	if ( $val ) :

		// Check if the value is less than 1,000....
		if ( $val < 1000 ) :

			// If less than 1,000 just format and kick it back
			return number_format( $val );

			// Check if the value is greater than 1,000 and less than 1,000,000....
		elseif ( $val < 1000000 ) :

			// Start by deviding the value by 1,000
			$val = intval( $val ) / 1000;

			// If the decimal separator is a period
			if ( $options['swp_decimal_separator'] == 'period' ) :

				// Then format the number to the appropriate number of decimals
				return number_format( $val,$options['swDecimals'],'.',',' ) . 'K';

				// If the decimal separator is a comma
			else :

				// Then format the number to the appropriate number of decimals
				return number_format( $val,$options['swDecimals'],',','.' ) . 'K';

			endif;

			// Check if the value is greater than 1,000,000....
		else :

			// Start by deviding the value by 1,000,000
			$val = intval( $val ) / 1000000;

			// If the decimal separator is a period
			if ( $options['swp_decimal_separator'] == 'period' ) :

				// Then format the number to the appropriate number of decimals
				return number_format( $val,$options['swDecimals'],'.',',' ) . 'M';

				// If the decimal separator is a comma
			else :

				// Then format the number to the appropriate number of decimals
				return number_format( $val,$options['swDecimals'],',','.' ) . 'M';

			endif;

		endif;

		// If there is no value, return a zero
	else :

		return 0;

	endif;
}

/**

 * **************************************************************
 *                                                                *
 *          AN EXCERPT FUNCTION							         *
 *                                                                *
 ******************************************************************/

	// A function to process the excerpts for descriptions
function swp_get_excerpt_by_id( $post_id ) {

	// Check if the post has an excerpt
	if ( has_excerpt() ) :
		$the_post = get_post( $post_id ); // Gets post ID
		$the_excerpt = $the_post->post_excerpt;

		// If not, let's create an excerpt
		else :
			$the_post = get_post( $post_id ); // Gets post ID
			$the_excerpt = $the_post->post_content; // Gets post_content to be used as a basis for the excerpt
		endif;

		$excerpt_length = 100; // Sets excerpt length by word count
		$the_excerpt = strip_tags( strip_shortcodes( $the_excerpt ) ); // Strips tags and images

		$the_excerpt = str_replace( ']]>', ']]&gt;', $the_excerpt );
		$the_excerpt = strip_tags( $the_excerpt );
		$excerpt_length = apply_filters( 'excerpt_length', 100 );
		$excerpt_more = apply_filters( 'excerpt_more', ' ' . '[...]' );
		$words = preg_split( "/[\n\r\t ]+/", $the_excerpt, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY );

		if ( count( $words ) > $excerpt_length ) :
			array_pop( $words );
			// array_push($words, '…');
			$the_excerpt = implode( ' ', $words );
		endif;

		$the_excerpt = preg_replace( "/\r|\n/", '', $the_excerpt );

		return $the_excerpt;
}

/**

 * ****************************************************
 *                                                      *
 *	Mobile device detection                            *
 *                                                      *
 ********************************************************/
if ( ! function_exists( 'swp_mobile_detection' ) ) {
	function swp_mobile_detection() {
		return preg_match( '/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i', $_SERVER['HTTP_USER_AGENT'] );
	}
}
