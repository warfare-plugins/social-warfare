<?php

/**
 * Control the cache rebuild processes for share counts
 *
 * @package   SocialWarfare\Functions
 * @copyright Copyright (c) 2017, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     1.0.0 | CREATED | UNKNOWN
 * @since     2.2.4 | UPDATED | 02 MAY 2017 | Added a function to remove the cache rebuild URL parameter from WP's query
 * @since     2.2.6 | UPDATED | 11 MAY 2017 | Added a "force" parameter to force cache rebuild when using URL parameters
 */

defined( 'WPINC' ) || die;


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
add_filter( 'query_vars', 'swp_add_query_vars' );

/**
 * Force our swp_cache variable to not be part of the query
 *
 * @since  2.2.4 | CREATED | 02 MAY 2017
 * @access public
 * @param  array $request The current query request.
 * @return array $request The modified query request.
 */
add_filter( 'request', 'swp_alter_the_query' );
function swp_alter_the_query( $request ) {
    $dummy_query = new WP_Query();  // the query isn't run if we don't pass any query vars
    $dummy_query->parse_query( $request );
	if(isset($request['swp_cache'])) {
		unset($request['swp_cache']);
	}
    return $request;
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
	if ( 'rebuild' === $_GET['swp_cache'] ) {
		$info['header_output'] .= '<link rel="canonical" href="' . get_permalink() . '">';
	}

	return $info;
}

/**
 * Cache checking function
 *
 * @since  1.0.0
 * @access public
 * @param  integer  $post_id The post ID
 * @param  boolean $output  Does the caller require a response output
 * @param  boolean $ajax    Is this being called from Ajax
 * @return boolean true/false The status of wether the cache is fresh or not
 */
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
		if ( empty( $_GET['swp_cache'] ) ) {
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
		$fresh_cache = true;
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
 * @since  1.0.0
 * @global $wpdb
 * @return void
 */
function swp_cache_rebuild() {
	global $wpdb;

	$post_id = absint( $_POST['post_id'] );

	/**
	 *  Bail if we already have fresh cache and this request is invalid.
	 *
	 */
	if ( swp_is_cache_fresh( $post_id , true , true ) && !isset($_POST['force']) ) {
		wp_send_json_error();
		die();
	}

	/**
	 *  Force the cache trigger on.
	 *
	 */
	set_query_var( 'swp_cache', 'rebuild' );

	/**
	 * Fetch new share counts via the various API's
	 *
	 * @var integer $post_id The ID of the post
	 */
	$shares = get_social_warfare_shares( $post_id );

	/**
	 * Update Bitly links in case anything has changed with the permalink
	 *
	 */
	foreach ( $shares as $key => $value ) :
		swp_process_url( get_permalink( $post_id ) , $key , $post_id );
	endforeach;

	/**
	 * Recheck the image URL's and then store the values
	 * in the meta fields so that they are autoloaded with the postID
	 * to prevent the extra queries during page loads.
	 */
	swp_cache_rebuild_pin_image($post_id);
	swp_cache_rebuild_og_image($post_id);

	// Remove any meta fields that we used to use, but no longer use.
	swp_cache_remove_unused_fields($post_id);

	// Reset the timestamp
	swp_cache_reset_timestamp($post_id);

	// Return the share count
	wp_send_json( $shares );

	// Kill off all the WordPress functions
	wp_die();
}

/**
 * A function to reset the cache timestamp with the current time
 *
 * @since 2.1.4
 * @access public
 * @param integer $post_id The Post's ID
 * @return void
 */
function swp_cache_reset_timestamp($post_id) {
	delete_post_meta( $post_id , 'swp_cache_timestamp' );
	update_post_meta( $post_id , 'swp_cache_timestamp' , floor( ( ( date( 'U' ) / 60 ) / 60 ) ) );
}

/**
 * A function to delete the current timestamp
 *
 * @since 2.1.4
 * @return void
 */
function swp_cache_delete_timestamp() {
	delete_post_meta( get_the_ID() , 'swp_cache_timestamp' );
}
add_action( 'save_post', 'swp_cache_delete_timestamp' );
add_action( 'save_post', 'swp_cache_store_autoloads' );

/**
 * A function to store all the fields for autoloading
 *
 * @since 2.1.4
 * @return void
 */
function swp_cache_store_autoloads() {
	$post_id = get_the_ID();
	if( 'publish' === get_post_status( $post_id ) ):
		swp_cache_rebuild_pin_image($post_id);
		swp_cache_rebuild_og_image($post_id);
	endif;
}
/**
 * Open Graph Image
 *
 * Convert the open graph image ID to a URL and store it in a meta field
 * because then the URL will be autoloaded with the post preventing the
 * need for an additional database query during page loads.
 *
 * @since  2.1.4
 * @access public
 * @param  integer $post_id The ID of the post
 * @return void
 */
function swp_cache_rebuild_og_image($post_id) {

	// Check if an OG image has been declared
	$image_id = get_post_meta( $post_id , 'nc_ogImage' , true );
	if ( $image_id ):

		$cur_image_url = get_post_meta( $post_id , 'swp_open_graph_image_url' , true );
		$new_image_url = wp_get_attachment_url( $image_id );

		// No need to update the DB if the url hasn't changed
		if( $cur_image_url !== $new_image_url ):

			$image_data = wp_get_attachment_image_src( $image_id , 'full' );
			delete_post_meta( $post_id , 'swp_open_graph_image_data' );
			update_post_meta( $post_id , 'swp_open_graph_image_data' , json_encode( $image_data ) );

			delete_post_meta( $post_id,'swp_open_graph_image_url' );
			update_post_meta( $post_id,'swp_open_graph_image_url' , $new_image_url );

		endif;
	else:
		delete_post_meta( $post_id,'swp_open_graph_image_url' );
	endif;
}
/**
 * Pinterest Image
 *
 * Convert the pinterest image ID to a URL and store it in a meta field
 * because then the URL will be autoloaded with the post preventing the
 * need for an additional database query during page loads.
 *
 * @since  2.1.4
 * @access public
 * @param  integer $post_id The ID of the post
 * @return void
 */
function swp_cache_rebuild_pin_image($post_id) {

	// Check if a custom pinterest image has been declared
	$pin_image_id = get_post_meta( $post_id , 'nc_pinterestImage' , true );
	if ( $pin_image_id ) :
		$pin_image_url = wp_get_attachment_url( $pin_image_id );
		$cur_image_url = get_post_meta( $post_id , 'swp_pinterest_image_url' , true );

		// No need to update the database if the image URL has not changed
		if($pin_image_url !== $cur_image_url):
			delete_post_meta( $post_id,'swp_pinterest_image_url' );
			update_post_meta( $post_id,'swp_pinterest_image_url' , $pin_image_url );
		endif;
	endif;
}

/**
 * Update Facebook share counts.
 *
 * @since  2.1.0
 * @return void
 */

add_action( 'wp_ajax_swp_facebook_shares_update', 'swp_facebook_shares_update' );
add_action( 'wp_ajax_nopriv_swp_facebook_shares_update', 'swp_facebook_shares_update' );
function swp_facebook_shares_update() {
	$post_id = $_POST['post_id'];
	$activity = $_POST['activity'];

	$previous_activity = get_post_meta( $post_id, '_facebook_shares', true );

	if ( $activity > $previous_activity ) :
		delete_post_meta( $post_id, '_facebook_shares' );
		update_post_meta( $post_id, '_facebook_shares', $activity );
	endif;

	echo true;

	wp_die();
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

	if( $info['swp_user_options']['recover_shares'] == true ) {
		$alternateURL = swp_get_alt_permalink( $info['postID'] );
		$alternateURL = apply_filters( 'swp_recovery_filter',$alternateURL );
	} else {
		$alternateURL = false;
	}

	// Bail if we're not using the newer cache method.
	if ( 'legacy' === $info['swp_user_options']['cacheMethod'] && is_singular() ) {
		ob_start(); ?>

		var swp_buttons_exist = !!document.getElementsByClassName( 'nc_socialPanel' );
		if ( swp_buttons_exist ) {
			swp_admin_ajax = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
			swp_post_id='<?php echo $info['postID']; ?>';
			swp_post_url='<?php echo get_permalink(); ?>';
			swp_post_recovery_url = '<?php echo $alternateURL; ?>';
			socialWarfarePlugin.fetchShares();
		}

		<?php
		$info['footer_output'] .= ob_get_clean();
		return $info;
	}

	// Bail early if we're not on a single page or we have fresh cache.
	if ( (! is_singular() || swp_is_cache_fresh( get_the_ID(), true )) && empty( $_GET['swp_cache'] ) ) {
		return $info;
	}

	// Bail if we're on a WooCommerce account page.
	if ( function_exists( 'is_account_page' ) && is_account_page() ) {
		return $info;
	}

	// Trigger the cache rebuild.
	if ( ( isset($_GET['swp_cache']) && 'rebuild' === $_GET['swp_cache'] ) || false === swp_is_cache_fresh( get_the_ID(), true ) ) {
		ob_start();

		?>
        var within_timelimit;
		swp_admin_ajax = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
		var swp_buttons_exist = !!document.getElementsByClassName( 'nc_socialPanel' );
		if ( swp_buttons_exist ) {
			jQuery( document ).ready( function() {
				<?php if( isset($_GET['swp_cache']) && 'rebuild' === $_GET['swp_cache'] ): ?>
				var swp_cache_data = {
					'action': 'swp_cache_trigger',
					'post_id': <?php echo $info['postID']; ?>,
                    'timestamp': <?php echo time(); ?>,
					'force':true
				};
				<?php else: ?>
				var swp_cache_data = {
					'action': 'swp_cache_trigger',
					'post_id': <?php echo $info['postID']; ?>,
                    'timestamp': <?php echo time(); ?>
				};
				<?php endif; ?>
                // if( !swp_cache_data.timestamp ){ // error handling}
                console.log( "Server Timestamp is " + swp_cache_data.timestamp );
                var browser_date = Date.now();
                if( !browser_date )
                    browser_date = new Date().getTime();
                browser_date = Math.floor( browser_date / 1000 );
                console.log( "Browser Timestamp is " + browser_date );
                var elapsed_time = ( swp_cache_data.timestamp - browser_date );
                if( elapsed_time > 60 ){
                    console.log( "Elapsed time since server timestamp is greater than 60 seconds -- " + elapsed_time + "seconds" );
                    within_timelimit = false;
                } else {
                    console.log( "Elapsed time since server timestamp is less than 60 seconds -- " + elapsed_time + "seconds"  );
                    within_timelimit = true;
                }

                if( within_timelimit === true ){
				    jQuery.post( swp_admin_ajax, swp_cache_data, function( response ) {
					    console.log(response);
				    });

                    socialWarfarePlugin.fetchShares();
                }
			});
			swp_post_id='<?php echo $info['postID']; ?>';
			swp_post_url='<?php echo get_permalink(); ?>';
			swp_post_recovery_url = '<?php echo $alternateURL; ?>';

			//    socialWarfarePlugin.fetchShares();
		}
		<?php
		$info['footer_output'] .= ob_get_clean();
	}

	return $info;
}

/**
 * A function to remove fields that we no longer use
 *
 * @since  2.1.4
 * @param  integer $post_id The post ID
 * @return void
 */
function swp_cache_remove_unused_fields($post_id){
	delete_post_meta( $post_id, 'sw_fb_author' );
	delete_post_meta( $post_id, 'sw_open_graph_image_url' );
	delete_post_meta( $post_id, 'sw_open_graph_image_data' );
	delete_post_meta( $post_id, 'sw_open_thumbnail_url' );
	delete_post_meta( $post_id, 'swp_open_thumbnail_url' );
	delete_post_meta( $post_id, 'sw_pinterest_image_url' );
	delete_post_meta( $post_id, 'swp_twitter_username' );
}
