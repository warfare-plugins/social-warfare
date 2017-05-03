<?php
/**
 * **************************************************************
 *                                                                *
 *   #1: The Social Warfare Buttons Shortcode [social_warfare]    *
 *                                                                *
 ******************************************************************/

function socialWarfareShortcode( $atts ) {
	return socialWarfare( false,'after',false );
}
function social_warfareShortcode( $array ) {
	if ( ! isset( $array['where'] ) ) { $array['where'] = 'after'; }
	if ( ! isset( $array['echo'] ) ) { $array['echo'] = false; }
	if ( ! isset( $array['content'] ) ) { $array['content'] = false; }
	$array['shortcode'] = true;
	$array['devs'] = true;
	return social_warfare( $array );
}

add_shortcode( 'socialWarfare', 'socialWarfareShortcode' );
add_shortcode( 'social_warfare', 'social_warfareShortcode' );

/**

 * **************************************************************
 *                                                                *
 *   #2: The Post Total Shares Shortcode [total_shares]      *
 *                                                                *
 ******************************************************************/

function swp_post_totes_function( $atts ) {
	$totes = get_post_meta( get_the_ID() , '_totes', true );
	$totes = swp_kilomega( $totes );
	return $totes;
}

add_shortcode( 'total_shares', 'swp_post_totes_function' );

/**

 * **************************************************************
 *                                                                *
 *   #3: The Sitewide Total Shares Shortcode [sitewide_shares]      *
 *                                                                *
 ******************************************************************/
// * Social Warfare Sitewide shares
function swp_sitewide_shares_function( $atts ) {
	global $wpdb;
	$sum = $wpdb->get_results( "SELECT SUM(meta_value) AS total FROM $wpdb->postmeta WHERE meta_key = '_totes'" );
	return swp_kilomega( $sum[0]->total );
}
add_shortcode( 'sitewide_shares', 'swp_sitewide_shares_function' );
