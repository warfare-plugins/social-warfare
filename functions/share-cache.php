<?php

add_filter( 'query_vars', 'swp_add_query_vars' );
/**
 * Register custom query vars.
 *
 * @since  2.1.0
 * @access public
 * @param  array $vars The current query vars.
 * @return array $vars The modified query vars.
 */
function swp_add_query_vars( $vars ) {
	$vars[] = 'swp_cache';
	return $vars;
}

add_filter( 'swp_meta_tags', 'swp_cache_rebuild_rel_canonical', 7 );
/**
 * Use a rel canonical so search engines know this is not a real page.
 *
 * @since 1.4.0
 * @param array $info Meta tag info.
 * @return array $info Meta tag info.
 */
function swp_cache_rebuild_rel_canonical( $info ) {
	if ( 'rebuild' === get_query_var( 'swp_cache' ) ) {
		$info['header_output'] .= '<link rel="canonical" href="' . get_permalink() . '">';
	}

	return $info;
}

/**
 * **************************************************************
 *                                                                *
 *          CACHe CHECKING FUNCTION         			 			 *
 *                                                                *
 ******************************************************************/
function swp_is_cache_fresh( $post_id, $output = false, $ajax = false ) {
	global $swp_user_options;

	// Bail early if it's a crawl bot. If so, ONLY SERVE CACHED RESULTS FOR MAXIMUM SPEED.
	if ( isset( $_SERVER['HTTP_USER_AGENT'] ) && preg_match( '/bot|crawl|slurp|spider/i',  wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) ) {
		return true;
	}

	$options = $swp_user_options;

	$fresh_cache = false;

	// Bail if output isn't being forced and legacy caching isn't enabled.
	if ( ! $output && 'legacy' !== $options['cacheMethod'] ) {
		if ( 'rebuild' !== get_query_var( 'swp_cache' ) ) {
			$fresh_cache = true;
		}

		return $fresh_cache;
	}

	// Always be TRUE if we're not on a single.php otherwise we could end up
	// Rebuilding multiple page caches which will cost a lot of time.
	if ( ! is_singular() && ! $ajax ) {
		return true;
	}

	$post_age = floor( date( 'U' ) - get_post_time( 'U' , false , $post_id ) );

	if ( $post_age < ( 21 * 86400 ) ) {
		$hours = 1;
	} elseif ( $post_age < ( 60 * 86400 ) ) {
		$hours = 4;
	} else {
		$hours = 12;
	}

	$time = floor( ( ( date( 'U' ) / 60 ) / 60 ) );
	$last_checked = get_post_meta( $post_id, 'swp_cache_timestamp', true );

	if ( $last_checked > ( $time - $hours ) && $last_checked > 390000 ) {
		$fresh_cache = false;
	} else {
		$fresh_cache = false;
	}

	return $fresh_cache;
}

add_action( 'wp_ajax_swp_cache_trigger', 'swp_cache_rebuild' );
add_action( 'wp_ajax_nopriv_swp_cache_trigger', 'swp_cache_rebuild' );
/**
 * Rebuild the share cache.
 *
 * @since  unkown
 * @global $wpdb
 * @return void
 */
function swp_cache_rebuild() {
	global $wpdb;

	$post_id = absint( $_POST['post_id'] );

	// Bail if we already have fresh cache.
	if ( swp_is_cache_fresh( $post_id , true , true ) ) {
		wp_send_json_error();
		die();
	}

	// Force the cache trigger on.
	set_query_var( 'swp_cache', 'rebuild' );

	// Fetch new shares
	$shares = get_social_warfare_shares( $post_id );

	// Update Bitly links
	foreach ( $shares as $key => $value ) :
		swp_process_url( get_permalink( $post_id ) , $key , $post_id );
	endforeach;

	// Update the Pinterest image
	$array['imageID'] = get_post_meta( $post_id , 'nc_pinterestImage' , true );
	if ( $array['imageID'] ) :
		$array['imageURL'] = wp_get_attachment_url( $array['imageID'] );
		delete_post_meta( $post_id,'swp_pinterest_image_url' );
		update_post_meta( $post_id,'swp_pinterest_image_url',$array['imageURL'] );
	endif;

	// Update the Twitter username
	$user_twitter_handle = get_the_author_meta( 'swp_twitter' , swp_get_author( $post_id ) );
	if ( $user_twitter_handle ) :
		delete_post_meta( $post_id,'swp_twitter_username' );
		update_post_meta( $post_id,'swp_twitter_username',$user_twitter_handle );
	else :
		delete_post_meta( $post_id,'swp_twitter_username' );
	endif;

	// Chache the og_image URL.
	$image_id = get_post_meta( $post_id , 'nc_ogImage' , true );

	if ( $image_id ) :
		$image_url = wp_get_attachment_url( $image_id );
		delete_post_meta( $post_id,'swp_open_graph_image_url' );
		update_post_meta( $post_id,'swp_open_graph_image_url',$image_url );
	else :
		$image_url = wp_get_attachment_url( get_post_thumbnail_id( $post_id ) );
		delete_post_meta( $post_id,'swp_open_thumbnail_url' );
		update_post_meta( $post_id,'swp_open_thumbnail_url' , $image_url );
		delete_post_meta( $post_id,'swp_open_graph_image_url' );
	endif;

	// Update the cache timestamp
	delete_post_meta( $post_id , 'swp_cache_timestamp' );
	update_post_meta( $post_id , 'swp_cache_timestamp' , floor( ( ( date( 'U' ) / 60 ) / 60 ) ) );

	// Return the share count
	wp_send_json( $shares );

	// Kill off all the WordPress functions
	wp_die();
}

add_action( 'wp_ajax_swp_facebook_shares_update', 'swp_facebook_shares_update' );
add_action( 'wp_ajax_nopriv_swp_facebook_shares_update', 'swp_facebook_shares_update' );
/**
 * Update Facebook share counts.
 *
 * @since  unknown
 * @return void
 */
function swp_facebook_shares_update() {
	$post_id = $_POST['post_id'];
	$activity = $_POST['activity'];

	$previous_activity = get_post_meta( $post_id, '_facebook_shares', true );

	if ( $activity > $previous_activity ) :
		delete_post_meta( $post_id, '_facebook_shares' );
		update_post_meta( $post_id, '_facebook_shares', $activity );
	endif;

	echo $activity;

	wp_die();
}

add_action( 'save_post', 'swp_reset_cache_timestamp' );
/**
 * A function to reset the cache timestamp on a post after the cache is rebuilt.
 *
 * @since  2.0.0
 * @param  integer $post_id The ID of the post to be reset.
 * @return void
 */
function swp_reset_cache_timestamp( $post_id ) {
	delete_post_meta( $post_id,'swp_cache_timestamp' );

	// Chache the og_image URL.
	$image_id = get_post_meta( $post_id , 'nc_ogImage' , true );

	if ( $image_id ) :
		$image_url = wp_get_attachment_url( $image_id );
		delete_post_meta( $post_id,'swp_open_graph_image_url' );
		update_post_meta( $post_id,'swp_open_graph_image_url',$image_url );
	else :
		$image_url = wp_get_attachment_url( get_post_thumbnail_id( $post_id ) );
		delete_post_meta( $post_id,'swp_open_thumbnail_url' );
		update_post_meta( $post_id,'swp_open_thumbnail_url' , $image_url );
		delete_post_meta( $post_id,'swp_open_graph_image_url' );
	endif;

	// Update the Pinterest image
	$array['imageID'] = get_post_meta( $post_id , 'nc_pinterestImage' , true );
	if ( $array['imageID'] ) :
		$array['imageURL'] = wp_get_attachment_url( $array['imageID'] );
		delete_post_meta( $post_id,'swp_pinterest_image_url' );
		update_post_meta( $post_id,'swp_pinterest_image_url',$array['imageURL'] );
	endif;
}

add_filter( 'swp_footer_scripts' , 'swp_output_cache_trigger' );
/**
 * Trigger cache rebuild.
 *
 * @since  1.4.7
 * @access public
 * @param  array $info An array of footer script information.
 * @return array $info A modified array of footer script information.
 */
function swp_output_cache_trigger( $info ) {

	// Bail early if we're not on a single page or we have fresh cache.
	if ( ! is_singular() || swp_is_cache_fresh( get_the_ID(), true ) ) {
		return $info;
	}

	// Bail if we're not using the newer cache method.
	if ( 'legacy' === $info['swp_user_options']['cacheMethod'] ) {
		return $info;
	}

	// Bail if we're on a WooCommerce account page.
	if ( function_exists( 'is_account_page' ) && is_account_page() ) {
		return $info;
	}

	// Trigger the cache rebuild.
	if ( 'rebuild' === get_query_var( 'swp_cache' ) || false === swp_is_cache_fresh( get_the_ID(), true ) || 'legacy' === $info['swp_user_options']['cacheMethod'] ) {
		ob_start();

		if( $info['swp_user_options']['recover_shares'] == true ) {
			$alternateURL = swp_get_alt_permalink( $info['postID'] );
			$alternateURL = apply_filters( 'swp_recovery_filter',$alternateURL );
		} else {
			$alternateURL = false;
		}

		?>
		swp_admin_ajax = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
		var swp_buttons_exist = !!document.getElementsByClassName( 'nc_socialPanel' );
		if ( swp_buttons_exist ) {
			jQuery( document ).ready( function() {
				var swp_cache_data = {
					'action': 'swp_cache_trigger',
					'post_id': <?php echo $info['postID']; ?>
				};
				jQuery.post( swp_admin_ajax, swp_cache_data, function( response ) {
					console.log(response);
				});
			});
		}
		swp_post_id='<?php echo $info['postID']; ?>';
		swp_post_url='<?php echo get_permalink(); ?>';
		swp_post_recovery_url = '<?php echo $alternateURL; ?>';
		socialWarfarePlugin.fetchShares();
		<?php
		$info['footer_output'] = ob_get_clean();
	}

	return $info;
}
