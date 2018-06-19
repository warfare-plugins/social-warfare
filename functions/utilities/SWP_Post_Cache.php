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

        $this->post_id = $post_id;
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
        $post_age = floor( date( 'U' ) - get_post_time( 'U' , false , $this->post_id ) );

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
                            post_id: <?= (int) $this->post_id ?>,
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


    /**
     * Removes the timestamp on certain hooks.
     *
     * @since 3.0.10 | 19 JUN 2018 | Ported from function to class method.
     * @return void
     */
    public function delete_timestamp() {
        delete_post_meta( $this->id, 'swp_cache_timestamp' );
    }
}
