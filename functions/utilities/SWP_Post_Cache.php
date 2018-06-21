<?php

/**
 * The Post_Cache Object
 *
 * This class will control the cached data for each individual post across a
 * WordPress website. Direct calls for data such as share counts, will pull and
 * return cached data.
 *
 * Since all Post_Cache objects should be loaded via the Post_Cache_Loader class,
 * we will use the instantiation method (__construct) to queue up asyncronous
 * methods for rebuilding cached data. This should allow us to run that subset
 * of functions only once per page load, and then the cache will once again be
 * fresh for a few hours before we need to do it again.
 *
 * @package   SocialWarfare\Functions\Utilities
 * @copyright Copyright (c) 2018, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     3.0.10 | 20 JUN 2018 | Created
 * @access    public
 *
 */
class SWP_Post_Cache {


	/**
	 * The WordPress Post Object
	 *
	 * @see $this->establish_post_data() method.
	 * @var object
	 *
	 */
	public $post;


	/**
	 * The ID of the Current Post Being Processed
	 *
	 * @see $this->establish_post_data() method.
	 * @var integer
	 *
	 */
	public $id;

    public $old_shares = array();
    public $api_links = array();
    public $share_data = array();


	/**
	 * The Magic Construct Method
	 *
	 * This method 1.) instantiates the object
	 * making the public methods available for use by the plugin, and
     * 2.) Determine if the cache is fresh, and if not, trigger an
	 * asyncronous request to rebuild the cached data.
	 *
	 * @since  3.0.10 | 20 JUN 2018 | Created
	 * @param  integer $post_id The ID of the post
	 * @return void
	 *
	 */
    public function __construct( $post_id ) {

		// Set up the post data into local properties.
		$this->establish_post_data( $post_id );

		// If the cache is expired, trigger the rebuild processes.
        if ( false === $this->is_cache_fresh() ):
			$this->rebuild_cached_data();
		endif;

		// This may not work here and may need moved to the loader class.
        $this->init_post_publish_hooks();

    }


	/**
	 * A method to rebuild all cached data
	 *
	 * This is the method that will be called in the constructor. This is also
	 * the method that we want to run asyncronously. This method will call all
	 * other methods and run the action filter to allow third-party functions
	 * to run during the cache rebuild process.
	 *
	 * @since  3.0.10 | 20 JUN 2018 | Created
	 * @todo   Move all calls to cache rebuild methods into this method. This
	 *         will become the one and only method that is used to rebuild this
	 *         particular cache of data.
	 * @param  void
	 * @return void
	 *
	 */
	protected function rebuild_cached_data() {
        $this->rebuild_shares();
        $this->rebuild_pin_image();
        $this->rebuild_og_image();
        $this->reset_timestamp();

        //* @note: I removed init_cache_update_hooks and just copied its
        //*        contents here sine there was no logic involved.

        //* If we can find a way to spoof IP addresses, we can also remove the
        //* Facebook ajax methods.
        add_action( 'wp_ajax_facebook_shares_update', array( $this, 'facebook_shares_update' ) );
		add_action( 'wp_ajax_nopriv_facebook_shares_update', array( $this, 'facebook_shares_update' ) );
        add_action( 'wp_footer', array( $this, 'print_ajax_script' ) );

		// A hook to run allowing third-party functions to run.
		do_action('swp_cache_rebuild');

	}


	/**
	 * Establish the Post Data
	 *
	 *
	 *
	 * @since  3.0.10 | 20 JUN 2018 | Created
	 * @param  integer $post_id The post id.
	 * @return void             All processed data are stored in local properties.
	 *
	 */
	protected function establish_post_data( $post_id ) {

		// Retrieve the global post object.
		global $post;

		// If there is a glitch, use get_post() to fix it.
        if ( !is_object( $post ) || $post->ID != $post_id ) {
            $post = get_post( $post_id );
        }

        $this->id = $post_id;
        $this->post = $post;
	}


    /**
     * Determines if the data has recently been updated.
     *
     * This is the determining method to decide if a cache is fresh or if it
     * needs to be rebuilt.
     *
     * @since  3.0.10 | 19 JUN 2018 | Ported from function to class method.
     * @todo   Review: Specifically look at how I broke the logic out into
     *         get_cache_age() and get_freshness_duration() methods. I'm trying
     *         really hard to keep all pieces of logic broken down into very
     *         simple, bite-sized amounts that take care of very specific tasks.
     * @access protected
     * @param  void
     * @return boolean True if fresh, false if expired and needs rebuilt.
     *
     */
    protected function is_cache_fresh() {

    	// Bail early if it's a crawl bot. If so, ONLY SERVE CACHED RESULTS FOR MAXIMUM SPEED.
    	if ( isset( $_SERVER['HTTP_USER_AGENT'] ) && preg_match( '/bot|crawl|slurp|spider/i',  wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) ) :
        	return true;
        endif;

		// Always be true if we're not a single post.
    	if ( !is_singular() ) :
    		return true;
    	endif;

		// If a URL parameter is specifically telling it to rebuild.
		if ( isset( $_GET['swp_cache'] ) && 'rebuild' === $_GET['swp_cache'] ) {
			return false;
		}

		// If a POST request (AJAX) is specifically telling it to rebuild.
    	if( isset( $_POST['swp_cache'] ) && 'rebuild' === $_POST['swp_cache'] ) {
    		return false;
    	}

		if( get_cache_age() >= get_freshness_duration() ):
			return false;
		endif;

		return true;

    }


    /**
     * Determines how recently, in hours, the cache has been updated.
     *
     * @since  3.0.10 | 19 JUN 2018 | Created the method.
     * @todo   Review
     * @param  void
     * @return int  The current age of the cache in hours.
     *
     */
    protected function get_cache_age() {

        // $time is a number in hours. Example: 424814 == Number of hourse since Unix epoch.
    	$current_time = floor( ( ( date( 'U' ) / 60 ) / 60 ) );

        // $last_checked is the number in hours (at time of storage) since the Unix epoch.
    	$last_checked_time = get_post_meta( $post_id, 'swp_cache_timestamp', true );

		// How many hours has it been since the cache was rebuilt?
    	$age = $current_time - $last_checked_time;

    	return $age;
    }


	/**
	 * Get the duration during which this cache can be considered fresh.
	 *
	 * A cache is fresh for the following durations:
	 *     1 Hour   - New Posts less than 21 days old.
	 *     4 Hours  - Medium Posts less than 60 days old.
	 *     12 Hours - Old Posts Older than 60 days old.
	 *
	 * @since  3.0.10 | 20 JUN 2018 | Created
	 * @todo   Review
	 * @param  void
	 * @return integer The duration in hours that applies to this cache.
	 *
	 */
	public function get_freshness_duration() {

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


/*******************************************************************************
 *
 *
 * WARNING! WARNING! WARNING! WARNING! WARNING! WARNING! WARNING! WARNING!
 *
 * This is where I stopped refactoring for the day. This ends the requested
 * review sections. Delete this when you get to this point.
 *
 * For the methods below this, continue working on breaking things out into very
 * tiny, bite-sized pieces of straightforward logic in each method.
 *
 *
 ******************************************************************************/





	protected function init_post_publish_hooks() {
		add_action( 'save_post', array( $this, 'rebuild_cache' ) );
		add_action( 'publish_post', array( $this, 'rebuild_cache' ) );
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
        global $swp_user_options;

        $post_id = $_POST['post_id'];
    	$activity = $_POST['share_counts'];

    	$previous_activity = get_post_meta( $post_id, '_facebook_shares', true );

    	if ( $activity > $previous_activity || (isset($swp_user_options['force_new_shares']) && true === $swp_user_options['force_new_shares']) ) :
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
        global $swp_social_networks;

        foreach( $swp_social_networks as $network ) {
            if ( !isset( $swp_social_networks[$network] ) ) :
                continue;
            endif;

            $this->fresh_cache ? $this->set_cached_shares( $network ) : $this->prepare_network( $network );
        }

        $this->fresh_cache ? $this->set_total_shares() : $this->rebuild_shares();
    }

    protected function set_total_shares() {
        if ( get_post_meta( $this->id, '_total_shares', true ) ) :
            $this->share_data['total_shares'] = get_post_meta( $this->id, '_total_shares', true );
        else :
            $this->share_data['total_shares'] = 0;
        endif;
    }

    protected function set_cached_shares( $network ) {
        $this->share_data[$network] = get_post_meta( $this->id, '_' . $network . '_shares', true );
    }

    /**
     *  Finishes processing the share data after the network links have been set up.
     *
     *
     *
     */
    protected function rebuild_shares() {
        global $swp_social_networks, $swp_user_options;

        $raw_shares = SWP_CURL::fetch_shares_via_curl_multi( $api_links );

        if ( true == $swp_user_options['recover_shares'] ) :
            $recovered_shares = array();

            $old_raw_shares = SWP_CURL::fetch_shares_via_curl_multi( $this->old_share_links );
            $recovered_shares[$network] = $swp_social_networks[$network]->parse_api_response( $old_raw_shares[$network] );
        endif;

        foreach( $networks as $network ) {
            $this->share_data[$network] = $swp_social_networks[$network]->parse_api_response($raw_shares[$network]);

            if ( true == $swp_user_options['recover_shares'] &&  $this->share_data[$network] != $recovered_shares[$network] ) {
                $this->share_data[$network] = $this->share_data[$network] + $recovered_shares[$network];
            }

            if ( $this->share_data[$network] < $this->old_shares[$network] && false === _swp_is_debug('force_new_shares') ) :
                $this->share_data[$network] = $this->old_shares[$network];
            endif;

            //* TODO We do want to update post meta for network_shares, right?
            //* Previously this was in a chain of conditionals
            if ( $this->share_data[$network] > 0 ) :
                delete_post_meta( $this->id, '_' . $network . '_shares' );
                update_post_meta( $this->id, '_' . $network . '_shares', $this->share_data[$network] );
                $this->share_data['total_shares'] += $this->share_data[$network];
            endif;
        }

        delete_post_meta( $this->id, '_total_shares' );
        update_post_meta( $this->id, '_total_shares', $this->share_data['total_shares'] );
    }


    /**
     *  Prepares the API link(s) and old share data for a network.
     *
     */
    protected function prepare_network( $network ) {
        global $swp_social_networks, $swp_user_options;

        $this->old_shares[$network] = get_post_meta( $this->id, '_' . $network . '_shares', true );
        $this->api_links[$network]	= $swp_social_networks[$network]->get_api_link( $url );

        if ( $swp_user_options['recover_shares'] == true ) :
            $alternateURL = SWP_Permalink::get_alt_permalink( $this->id );
            $alternateURL = apply_filters( 'swp_recovery_filter', $alternateURL );
            $this->old_share_links[$network] = $swp_social_networks[$network]->get_api_link( $alternateURL );
        endif;
    }
}
