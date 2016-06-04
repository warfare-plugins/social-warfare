<?php

// Hook into the admin-ajax request
add_action( 'wp_ajax_sw_cache_trigger', 'sw_cache_rebuild' );
add_action( 'wp_ajax_nopriv_sw_cache_trigger', 'sw_cache_rebuild' );

// A function to rebuild the cache
function sw_cache_rebuild() {

	// Gain access to the database
	global $wpdb;

	// Fetch the Post ID
	$post_id = $_POST['post_id'];

	var_dump( sw_is_cache_fresh( $post_id , true , true ) );

	// Ensure that the cache for this post is actually expired
	if( sw_is_cache_fresh( $post_id , true , true ) == false ):

		// Force the cache trigger on
		$_GET['sw_cache'] = 'rebuild';

		// Fetch new shares
		$shares = get_social_warfare_shares( $post_id );

		// Update the cache timestamp
		delete_post_meta( $post_id , 'sw_cache_timestamp' );
		update_post_meta( $post_id , 'sw_cache_timestamp' , floor( ( ( date( 'U' ) / 60 ) / 60 ) ) );

		// Encode and return the new share counts
		$shares = json_encode($shares);
		echo $shares;

	endif;

	// Kill off all the WordPress functions
	wp_die();
}