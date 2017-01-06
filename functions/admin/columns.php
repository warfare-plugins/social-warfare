<?php
/**
 * Functions for modifying the default admin columns behavior.
 *
 * @package   SocialWarfare\Admin\Functions
 * @copyright Copyright (c) 2016, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     1.0.0
 */

/**
 * Add a share counts column to the post listing admin pages; make it Sortable.
 *
 * @since 1.4.0
 */
add_filter( 'manage_post_posts_columns', 'createSocialSharesColumn' );
add_filter( 'manage_page_posts_columns', 'createSocialSharesColumn' );
function createSocialSharesColumn( $defaults ) {
	$defaults['swSocialShares'] = 'Social Shares';
	return $defaults;
}

// Populate the new column
add_action( 'manage_posts_custom_column', 'populateSocialSharesColumn', 10, 2 );
add_action( 'manage_page_posts_custom_column', 'populateSocialSharesColumn', 10, 2 );
function populateSocialSharesColumn( $column_name, $post_ID ) {
	if ( $column_name == 'swSocialShares' ) {
		$answer = get_post_meta( $post_ID,'_totes',true );
		echo intval( $answer );
	}
}

// Make the column Sortable
add_filter( 'manage_edit-post_sortable_columns', 'makeSocialSharesSortable' );
add_filter( 'manage_edit-page_sortable_columns', 'makeSocialSharesSortable' );
function makeSocialSharesSortable( $columns ) {
	$columns['swSocialShares'] = 'Social Shares';
	return $columns;
}

add_action( 'pre_get_posts', 'swp_social_shares_orderby' );
function swp_social_shares_orderby( $query ) {
	if ( ! is_admin() ) {
		return;
	}
	$orderby = $query->get( 'orderby' );

	if ( 'Social Shares' === $orderby ) {
		$query->set( 'meta_key','_totes' );
		$query->set( 'orderby','meta_value_num' );
	}
}
