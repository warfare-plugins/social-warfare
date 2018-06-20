<?php

class SWP_Post_Cache {
    /**
     * @var bool $fresh_cache
     *
     * True if the post has recently been updated, false otherwise.
     */
    public $fresh_cache = false;


    /**
     * @var bool $rebuild
     *
     * If true, forces the cache to be rebuilt.
     *
     */
    public $rebuild = false;

    public function __construct( $post_id ) {
        global $post;

        if ( $post->ID != $post_id ) {
            $post = get_post( $post_id );
        }

        if ( $_POST['swp_cache'] == 'rebuidl' || $_GET['swp_cache'] == 'rebuild' ) {
            $this->rebuild = true;
        }

        $this->id = $post_id;
        $this->post = $post;
        $this->fresh_cache = $this->has_fresh_cache();

        $this->init_hooks();
    }


    /**
     * Reaches into WordPress to interfere with data.
     *
     * @since  3.0.10 | 19 JUN 2018 | Ported from procedural code to method.
     * @access protected This should only ever be called by the constructor.
     * @return void
     */
    protected function init_hooks() {
        add_action( 'save_post', array( $this, 'rebuild_cache' ) );
        add_action( 'publish_post', array( $this, 'rebuild_cache' ) );
        add_action( 'wp_ajax_facebook_shares_update', array( $this, 'facebook_shares_update' ) );
        add_action( 'wp_ajax_nopriv_facebook_shares_update', array( $this, 'facebook_shares_update' ) );

        if ( !$this->fresh_cache ) {
            add_action('wp_footer', array( $this, 'print_ajax_script' ) );
        }
    }

    /**
     * Determines if the data has recently been updated.
     *
     * @since 3.0.10 | 19 JUN 2018 | Ported from function to class method.
     * @access protected Use the $fresh_cache property to determine cache status.
     * @return mixed boolean if the conditions are met, else function $this->calulcate_age.
     *
     */
    protected function has_fresh_cache() {
        global $swp_user_options;

    	// Bail early if it's a crawl bot. If so, ONLY SERVE CACHED RESULTS FOR MAXIMUM SPEED.
    	if ( isset( $_SERVER['HTTP_USER_AGENT'] ) && preg_match( '/bot|crawl|slurp|spider/i',  wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) ) :
        	return true;
        endif;

    	if ( !$output && isset( $swp_user_options['cache_method'] ) && 'advanced' === $swp_user_options['cache_method'] ) {
    		if ( empty( $_GET['swp_cache'] ) && empty( $_POST['swp_cache'] ) ) {
    			return true;
    		}

    		return false;
    	}

        //* TODO Describe why this condition would pass.
    	if( isset( $_POST['swp_cache'] ) && 'rebuild' === $_POST['swp_cache'] ) {
    		return false;
    	}

    	// Always be TRUE if we're not on a single.php otherwise we could end up
    	// Rebuilding multiple page caches which will cost a lot of time.
    	if ( ! is_singular() && !$ajax ) :
    		return true;
    	endif;

        return $this->calculate_age();
    }

    /**
     *  Determines if the page has recently been updated.
     * @since 3.0.10 | 19 JUN 2018 | Created the method.
     * @return bool $fresh_cache True if the post has recently updated, false otherwise.
     */
    protected function calculate_age() {
        $post_age = floor( date( 'U' ) - get_post_time( 'U' , false , $this->id ) );

    	if ( $post_age < ( 21 * 86400 ) ) {
            //* Three weeks
    		$hours = 1;
    	} elseif ( $post_age < ( 60 * 86400 ) ) {
            //* Two months
    		$hours = 4;
    	} else {
    		$hours = 12;
    	}

    	$time = floor( ( ( date( 'U' ) / 60 ) / 60 ) );
        //* $time is a number in hours. Example: 424814 == Number of hourse since Unix epoch.

    	$last_checked = get_post_meta( $post_id, 'swp_cache_timestamp', true );
        //* $last_checked is the number in hours (at time of storage) since the Unix epoch.

    	$fresh_cache =  $last_checked > ( $time - $hours ) && ( $last_checked > 390000 );

    	return $fresh_cache;
    }


	/**
	 * @review: I read recently that if you're using an "else" without any qualifiers
	 * (i.e. elsif) then you should simplify the logic into smaller bits. As such,
	 * I think that breaking this logic out of the method above makes sense and makes
	 * this method very, very simple. However, I want to come up with a better name
	 * for this method. Please review and advise. If you like it, then please replace
	 * the logic above with a call to this method.
	 *
	 * Regarding the name, I like methods to be in a verb - noun format. Obviously,
	 * in this case the noun has some modifiers which is fine.
	 *
	 * @var [type]
	 */
	public function get_time_between_expirations() {

		// Current age of the post
		$post_age = floor( date( 'U' ) - get_post_time( 'U' , false , $this->id ) );

		// If it's less than 21 days old.
		if ( $post_age < ( 21 * 86400 ) ) {
			return 1;

		// If it's less than 60 days old.
		} elseif ( $post_age < ( 60 * 86400 ) ) {
			return 4;
		}

		// If it's really old.
		return 12;
	}

    /**
     * Get either the cached or updated post meta.
     *
     * @return mixed $meta An array of post_meta, or void if not found.
     *
     */
    public function get_post_meta() {
        if ( $this->fresh_cache ) :
            return get_post_meta( $this->id );
        endif;

        //* Cache is not fresh. Do the requests.
        $data = do_requests();

        if ( !$data ) {
          //* Output the ajax JS.
          return;
        }

        $meta = cleanup_data($data);

        update_post_meta( $this->id, $meta );
        $this->post->post_meta = $meta;

        return $meta;
    }

    public function print_ajax_script() {
		?>
        <script type="text/javascript">
        var ticker = 0;
		var swpButtonsExist = (document.getElementsByClassName( 'swp_social_panel' ).length > 0);

		if (swpButtonsExist) {
			document.addEventListener('DOMContentLoaded', function() {
				var swpCheck = setInterval(function() {
                    ticker++;

                    if (ticker > 40) {
                        //* Twenty seconds have passed. We can close this.
                        clearInterval(swpCheck);
                    }

					if('undefined' !== typeof socialWarfarePlugin) {
						clearInterval(swpCheck);

                        var swpCacheData  = {
                            action: 'swp_cache_trigger',
                            post_id: <?= (int) $this->id ?>,
                            timestamp: <?= time() ?>
                        }
                        var browserDate = Math.floor(Date.now() / 1000);
                        var elapsedTime = ( browserDate - swpCacheData.timestamp);

    	                if ( elapsedTime < 60 ) {
                            jQuery.post('<?= admin_url( 'admin-ajax.php' ) ?>',
                                        swp_cache_data,
                                        function( response ) {
                                            console.log("SWP: Response from server");
                                            console.log(response);
                                        });
                            socialWarfarePlugin.fetchFacebookShares();
    	                }
					}
				} , 250 );
			});
		}
        </script>
		<?php
    }

    public function rebuild_cache() {
        $this->rebuild = true;

        $shares = get_social_warfare_shares( $this->id );

        //* TODO What is $value, the network object? We can have better names.
        foreach($shares as $network => $value ) {
            SWP_URL_Management::process_url( get_permalink( $this->id ), $network, $this->id );
        }

        $this->rebuild_pin_image();
        $this->rebuild_og_image();
        $this->reset_timestamp();

        wp_send_json( $shares );
        wp_die();
    }


    /**
     * Pinterest Image
     *
     * Convert the pinterest image ID to a URL and store it in a meta field
     * because then the URL will be autoloaded with the post preventing the
     * need for an additional database query during page loads.
     *
     * @since 3.0.10 | 19 JUN 2018 | Ported from function to class method.
     * @access protected Nothing outside this class should manage this data.
     * @return void
     */
    public function rebuild_pin_image() {
        // Check if a custom pinterest image has been declared
    	$pin_image_id = get_post_meta( $this->id , 'swp_pinterest_image' , true );

    	if ( false !== $pin_image_id ) :
    		$pin_image_url = wp_get_attachment_url( $pin_image_id );
    		$cur_image_url = get_post_meta( $this->id , 'swp_pinterest_image_url' , true );

    		// No need to update the database if the image URL has not changed
    		if($pin_image_url !== $cur_image_url):
    			delete_post_meta( $this->id,'swp_pinterest_image_url' );
    			update_post_meta( $this->id,'swp_pinterest_image_url' , $pin_image_url );
    		endif;

    	else:
    		delete_post_meta( $this->id , 'swp_pinterest_image_url' );
    	endif;
    }

    /**
     * Open Graph Image
     *
     * Convert the open graph image ID to a URL and store it in a meta field
     * because then the URL will be autoloaded with the post preventing the
     * need for an additional database query during page loads.
     *
     * @since 3.0.10 | 19 JUN 2018 | Ported from function to class method.
     * @access protected Nothing outside this class should manage this data.
     * @return void
     */
    public function rebuild_og_image() {
        $image_id = get_post_meta( $this->id , 'swp_og_image' , true );

        if ( $image_id ):

            $cur_image_url = get_post_meta( $this->id , 'swp_open_graph_image_url' , true );
            $new_image_url = wp_get_attachment_url( $image_id );

            // No need to update the DB if the url hasn't changed
            if( $cur_image_url !== $new_image_url ):

                $image_data = wp_get_attachment_image_src( $image_id , 'full' );
                delete_post_meta( $this->id , 'swp_open_graph_image_data' );
                update_post_meta( $this->id , 'swp_open_graph_image_data' , json_encode( $image_data ) );

                delete_post_meta( $this->id,'swp_open_graph_image_url' );
                update_post_meta( $this->id,'swp_open_graph_image_url' , $new_image_url );

            endif;
        else:
            delete_post_meta( $this->id,'swp_open_graph_image_url' );
        endif;
    }


    /**
     * Resets the cache timestamp to the current time in hours since Unix epoch.
     *
     * @since 3.0.10 | 19 JUN 2018 | Ported from function to class method.
     * @access protected Nothing outside this class should manage this data.
     * @return void
     */
    public function reset_timestamp() {
        delete_post_meta( $this->id, 'swp_cache_timestamp' );
    	update_post_meta( $this->id, 'swp_cache_timestamp', floor( ( ( date( 'U' ) / 60 ) / 60 ) ) );
    }


    public function facebook_shares_update() {
        $post_id = $_POST['post_id'];
    	$activity = $_POST['share_counts'];

    	$previous_activity = get_post_meta( $post_id, '_facebook_shares', true );

    	if ( $activity > $previous_activity || (isset($options['force_new_shares']) && true === $options['force_new_shares']) ) :
    		delete_post_meta( $post_id, '_facebook_shares' );
    		update_post_meta( $post_id, '_facebook_shares', $activity );
    	endif;

    	echo true;

    	wp_die();
    }


    /**
     * Removes the timestamp on certain hooks.
     *
     * @since 3.0.10 | 19 JUN 2018 | Ported from function to class method.
     * @return void
     */
    public function delete_timestamp() {
        delete_post_meta( $this->id, 'swp_cache_timestamp' );
    }


    /**
     * Gets the computed share data.
     *
     * @since  3.0.10 | 20 JUN 2018 | Created the method.
     * @return array $this->share_data if it exists, or an empty array.
     */
    public function get_shares() {
        if ( !empty( $this->share_data ) ) :
            return $this->share_data;
        endif;

        return array();
    }


    /**
     * Process the existing share data, or update it.
     *
     *
     */
    public function establish_shares() {
        global $swp_user_options, $swp_social_networks;

    	$options = $swp_user_options;
    	$url     = get_permalink( $postID );
        $url     = apply_filters( 'swp_url_filter_function', get_permalink( $this->ID ) );

    	$fresh_cache = swp_is_cache_fresh( $postID );
        $share_data = array();

    	$share_data['total_shares'] = 0;

    	// Queue up the networks that are available
    	$networks = $options['order_of_icons'];

        if ( !is_array( $networks ) || count ( $networks ) === 0 ) :
            return $share_data;
        endif;

    	foreach ( $networks as $network ) :
            if( isset( $swp_social_networks[$network] ) ):
        		// Check if we can used the cached share numbers
        		if ( $fresh_cache == true ) :
    				$share_data[$network] = get_post_meta( $postID, '_' . $network . '_shares', true );

        		// If cache is expired, fetch new and update the cache
        		else :
    				$old_shares[$network]  	= get_post_meta( $postID, '_' . $network . '_shares', true );
    				$api_links[$network]	= $swp_social_networks[$network]->get_api_link( $url );
        		endif;
    		endif;
    	endforeach;

    	// Recover Shares From Previously Used URL Patterns
    	if ( true == $options['recover_shares'] && false == $fresh_cache ) :
    		$alternateURL = SWP_Permalink::get_alt_permalink( $postID );
    		$alternateURL = apply_filters( 'swp_recovery_filter', $alternateURL );

    		foreach ( $networks as $network ) :
    			if( isset( $swp_social_networks[$network] ) ):
    				$old_share_links[$network] = $swp_social_networks[$network]->get_api_link( $alternateURL );
    			endif;
    		endforeach;
    	endif;

    	if ( $fresh_cache == true ) :
    		if ( get_post_meta( $postID, '_total_shares', true ) ) :
    			$share_data['total_shares'] = get_post_meta( $postID, '_total_shares', true );
    		else :
    			$share_data['total_shares'] = 0;
    		endif;
    	else :

    		// Fetch all the share counts asyncrounously
    		$raw_shares_array = SWP_CURL::fetch_shares_via_curl_multi( $api_links );

    		if ( $options['recover_shares'] == true ) :
    			$old_raw_shares_array = SWP_CURL::fetch_shares_via_curl_multi( $old_share_links );
    		endif;

            //* Need to reset the timestamp so we don't fetch shares again on the same request.
    		swp_cache_reset_timestamp( $postID );

    		foreach ( $networks as $network ) :

    			if( isset( $swp_social_networks[$network] ) ):
    				if ( ! isset( $raw_shares_array[$network] ) ) :
    					$raw_shares_array[$network] = 0;
    				endif;

    				if ( ! isset( $old_raw_shares_array[$network] ) ) :
    					$old_raw_shares_array[$network] = 0;
    				endif;

    				$share_data[$network] = $swp_social_networks[$network]->parse_api_response($raw_shares_array[$network]);

    				if ( $options['recover_shares'] == true ) :

    					$recovered_shares[$network] = $swp_social_networks[$network]->parse_api_response( $old_raw_shares_array[$network] );

    					if( !empty($altURLs) ) :
                            $altURLs_raw_shares_array = SWP_CURL::fetch_shares_via_curl_multi( $altURLs_share_links );
    						$altURLs_recovered_shares[$network] = $swp_social_networks[$network]->parse_api_response( $altURLs_raw_shares_array[$network] );
    					endif;

    					if ( $share_data[$network] != $recovered_shares[$network] ) :
    						$share_data[$network] = $share_data[$network] + $recovered_shares[$network];
    					endif;

    					if( !empty($altURLs) ):
    						$share_data[$network] = $share_data[$network] + $altURLs_recovered_shares[$network];
    					endif;

    				endif; //* End 'recover_shares'

    				if ( $share_data[$network] < $old_shares[$network] && false === _swp_is_debug('force_new_shares') ) :
    						$share_data[$network] = $old_shares[$network];

    				elseif($share_data[$network] > 0) :
    						delete_post_meta( $postID,'_' . $network . '_shares' );
    						update_post_meta( $postID,'_' . $network . '_shares',$share_data[$network] );
    				endif;

    				if (is_numeric( $share_data[$network] ) ):
    						$share_data['total_shares'] += $share_data[$network];
    				endif;

            		delete_post_meta( $postID,'_total_shares' );
            		update_post_meta( $postID,'_total_shares',$share_data['total_shares'] );

    			endif;
    		 endforeach;
    	endif;


    	// Return the share counts
    	return $share_data;
    }
}
