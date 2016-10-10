<?php

/*****************************************************************
*                                                                *
*          REBUILD THE CACHE						             *
*                                                                *
******************************************************************/

// Hook into the admin-ajax request
add_action( 'wp_ajax_swp_cache_trigger', 'swp_cache_rebuild' );
add_action( 'wp_ajax_nopriv_swp_cache_trigger', 'swp_cache_rebuild' );

// A function to rebuild the cache
function swp_cache_rebuild() {

	// Gain access to the database
	global $wpdb;

	// Fetch the Post ID
	$post_id = $_POST['post_id'];

	// Ensure that the cache for this post is actually expired
	if( swp_is_cache_fresh( $post_id , true , true ) == false ):

		// Force the cache trigger on
		$_GET['swp_cache'] = 'rebuild';

		// Fetch new shares
		$shares = get_social_warfare_shares( $post_id );

		// Update Bitly links
		foreach ( $shares as $key => $value):
			swp_process_url( get_permalink($post_id) , $key , $post_id );
		endforeach;

		// Update the Pinterest image
		$array['imageID'] = get_post_meta( $post_id , 'nc_pinterestImage' , true );
		if($array['imageID']):
			$array['imageURL'] = wp_get_attachment_url( $array['imageID'] );
			delete_post_meta($post_id,'swp_pinterest_image_url');
			update_post_meta($post_id,'swp_pinterest_image_url',$array['imageURL']);
		endif;

		// Update the Twitter username
		$user_twitter_handle 	= get_the_author_meta( 'swp_twitter' , swp_get_author($post_id));
		if($user_twitter_handle):
			delete_post_meta($post_id,'swp_twitter_username');
			update_post_meta($post_id,'swp_twitter_username',$user_twitter_handle);
		else:
			delete_post_meta($post_id,'swp_twitter_username');
		endif;

		// Update the cache timestamp
		delete_post_meta( $post_id , 'swp_cache_timestamp' );
		update_post_meta( $post_id , 'swp_cache_timestamp' , floor( ( ( date( 'U' ) / 60 ) / 60 ) ) );

		// Return the share count
		wp_send_json( $shares );

	endif;

	// Kill off all the WordPress functions
	wp_die();
}
/*****************************************************************
*                                                                *
*         UPDATE FACEBOOK SHARES					             *
*                                                                *
******************************************************************/
// Hook into the admin-ajax request
add_action( 'wp_ajax_swp_facebook_shares_update', 'swp_facebook_shares_update' );
add_action( 'wp_ajax_nopriv_swp_facebook_shares_update', 'swp_facebook_shares_update' );

// A function to rebuild the cache
function swp_facebook_shares_update() {
	$post_id = $_POST['post_id'];
	$activity = $_POST['activity'];

	$previous_activity = get_post_meta($post_id,'_facebook_shares',true);
	if($activity > $previous_activity):
		delete_post_meta($post_id,'_facebook_shares');
		update_post_meta($post_id,'_facebook_shares',$activity);
	endif;
	echo $activity;
	wp_die();
}
