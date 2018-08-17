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
 * This class contains four major sections of methods:
 *     1. Set up the cache object and necessary properties.
 *     2. Check if the cache is fresh or not.
 *     3. Update the cached data when the cache is expired.
 *     4. Allow a publicly accessable method for fetching cached counts.
 *
 * @package   SocialWarfare\Functions\Utilities
 * @copyright Copyright (c) 2018, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     3.1.0 | 20 JUN 2018 | Created
 * @access    public
 *
 */
class SWP_Post_Cache {


	/**
	 * SECTION #1: SETTING UP THE CACHE OBJECT
	 *
	 * The methods in this section are used to set up the cache object by
	 * initializing the object, setting up local properties, and pulling in the
	 * global $post object that will be used throughout the class.
	 *
	 */

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


	/**
	 * The Magic Construct Method
	 *
	 * This method 1.) instantiates the object
	 * making the public methods available for use by the plugin, and
     * 2.) Determine if the cache is fresh, and if not, trigger an
	 * asyncronous request to rebuild the cached data.
	 *
	 * @todo   Can we eliminate all post data except for the post_id?
	 * @since  3.1.0 | 20 JUN 2018 | Created
	 * @param  integer $post_id The ID of the post
	 * @return void
	 *
	 */
    public function __construct( $post_id ) {
        global $swp_user_options;

		// Set up the post data into local properties.
		$this->id = $post_id;
        $this->establish_share_counts();

		// If the cache is expired, trigger the rebuild processes.
        if ( false === $this->is_cache_fresh() ):
			$this->rebuild_cached_data();
        endif;

		// Debugging
		$this->debug();
    }


	/**
	 * A method for debugging and outputting the class object.
	 *
	 * @since  3.1.0 | 25 JUN 2018 | Created
	 * @param  void
	 * @return void
	 *
	 */
	private function debug() {
		if( true === _swp_is_debug('swp_share_cache') ):
            echo "<pre>", var_dump( $this ), "</pre>";
		endif;
	}


	/**
	 * A method for outputting debug notices when cache rebuild parameters are present.
	 *
	 * @since  3.2.0 | 31 JUL 2018 | Created
	 * @param  string $string The message to be displayed.
	 * @return void
	 *
	 */
	private function debug_message( $string ) {
		if( isset( $_GET['swp_cache'] ) && 'rebuild' === $_GET['swp_cache'] ) {
			echo $string;
		}
	}


	/**
	 * SECTION #2: CHECKING IF THE CACHE IS FRESH
	 *
	 * The methods in this section are used to determine whether or not the
	 * cached data needs to be rebuilt or not.
	 *
	 */


	/**
	* Determines if the data has recently been updated.
	*
	* This is the determining method to decide if a cache is fresh or if it
	* needs to be rebuilt.
	*
	* @since  3.1.0 | 19 JUN 2018 | Ported from function to class method.
	* @access public
	* @param  void
	* @return boolean True if fresh, false if expired and needs rebuilt.
	*
	*/
	public function is_cache_fresh() {
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

 		// Check if the cache is older than is allowable for this post.
 		if( $this->get_cache_age() >= $this->get_allowable_age() ):
 			return false;
 		endif;

 		return true;

     }


     /**
      * Determines how recently, in hours, the cache has been updated.
      *
      * @since  3.1.0 | 19 JUN 2018 | Created the method.
      * @todo   Review
      * @param  void
      * @return int  The current age of the cache in hours.
      *
      */
     protected function get_cache_age() {

         // An integer in hours. Example: 424814 == Number of hourse since Unix epoch.
     	$current_time = floor( ( ( date( 'U' ) / 60 ) / 60 ) );

         // The integer in hours at time of storage since the Unix epoch.
     	$last_checked_time = get_post_meta( $this->id, 'swp_cache_timestamp', true );

        if ( !is_numeric( $last_checked_time ) ) :
            $last_checked_time = 0;
        endif;


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
 	 * @since  3.1.0 | 20 JUN 2018 | Created
 	 * @todo   Review
 	 * @param  void
 	 * @return integer The duration in hours that applies to this cache.
 	 *
 	 */
 	public function get_allowable_age() {

 		// Integer in hours of the current age of the post.
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
	 * SECTION #3: REBUILDING THE CACHED DATA
	 *
	 * The methods in this section are used to rebuild all of the cached data.
	 *
	 */



	/**
	 * Trigger the Cache Rebuild
	 *
	 * This method will trigger a non-blocking request to admin-ajax. This
	 * request will be intercepted/recieved by the Post_Cache_Loader class
	 * during which it will load this post_cache object and call the
	 * rebuild_cached_data() method below. This way the rebuilding of the cache
	 * is conducted in an asyncronous, non-blocking fashion.
	 *
	 * @TODO   Add the wp_remote_post to ping admin-ajax.php.
	 * @since  3.1.0 | 25 JUN 2018 | Created
	 * @param  void
	 * @return void
	 *
	 */
	protected function trigger_cache_rebuild() {
        //* Ping ajax to Post_Cache_Loader->
        $data = array(
            'method'    => 'POST',
            'action'    => 'swp_rebuild_cache',
            'post_id'   => $this->id
        );

        $args = array(
            // 'timeout'   => 0.01,
            'blocking'  => false,
            'body'      => $data,
            'cookies'   => $_COOKIE,
            'sslverify' => false,
        );

        $var = wp_remote_post( admin_url( 'admin-ajax.php', $args ) );
		var_dump($var);
	}


	/**
	 * A method to rebuild all cached data
	 *
	 * This is the method that will be called during the rebuild. This is also
	 * the method that we want to run asyncronously. This method will call all
	 * other methods and run the action filter to allow third-party functions
	 * to run during the cache rebuild process.
	 *
	 * @since  3.1.0 | 20 JUN 2018 | Created
	 * @todo   Move all calls to cache rebuild methods into this method. This
	 *         will become the one and only method that is used to rebuild this
	 *         particular cache of data.
	 * @param  void
	 * @return void
	 *
	 */
    public function rebuild_cached_data() {

		if( true === $this->should_shares_be_fetched() ):
			$this->rebuild_share_counts();
		endif;

        $this->rebuild_pinterest_image();
        $this->rebuild_open_graph_image();
		$this->process_urls();
        $this->reset_timestamp();

		// A hook to allow third-party functions to run.
		do_action( 'swp_cache_rebuild', $this->id );
	}


	/**
	 * Should we fetch share counts for this post?
	 *
	 * This method controls which instances we should be fetching share counts
	 * and which instances whe shouldn't.
	 *
	 * @since  3.2.0 | 24 JUL 2018 | Created
	 * @param  void
	 * @return bool True: fetch share counts; False: don't fetch counts.
	 *
	 */
	private function should_shares_be_fetched() {

        // Don't fetch if all share counts are disabled.
		if( false == swp_get_option( 'network_shares' ) && false == swp_get_option( 'total_shares' ) ) {
			$this->debug_message( 'No Shares Fetched. Share counts are disabled in the settings.' );
			return false;
		}

		// Only fetch on published posts
		if( 'publish' !== get_post_status( $this->id ) ) {
			$this->debug_message( 'No Shares Fetched. This post is not yet published.' );
			return false;
		}

		return true;
	}


	/**
	 * Process the URLs for shortlinks, UTM, etc.
	 *
	 * @since  3.1.0 | 20 JUN 2018 | Created
	 * @param  void
	 * @return void
	 *
	 */
	public function process_urls() {
    	global $swp_social_networks;
    	$permalink = get_permalink( $this->id );
        foreach( $swp_social_networks as $network ):
            if( $network->is_active() ):
                SWP_URL_Management::process_url( $permalink, $network->key, $this->id, false );
            endif;
        endforeach;
	}


    /**
     * Pinterest Image
     *
     * Convert the pinterest image ID to a URL and store it in a meta field
     * because then the URL will be autoloaded with the post preventing the
     * need for an additional database query during page loads.
     *
     * @since  3.1.0 | 19 JUN 2018 | Ported from function to class method.
     * @access protected
     * @param  void
     * @return void
     *
     */
    public function rebuild_pinterest_image() {

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
     * @since 3.1.0 | 19 JUN 2018 | Ported from function to class method.
     * @access protected
     * @param  void
     * @return void
     *
     */
    public function rebuild_open_graph_image() {
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
     * @since 3.1.0 | 19 JUN 2018 | Ported from function to class method.
     * @access protected
     * @param  void
     * @return void
     *
     */
    public function reset_timestamp() {
        delete_post_meta( $this->id, 'swp_cache_timestamp' );
    	update_post_meta( $this->id, 'swp_cache_timestamp', floor( ( ( date( 'U' ) / 60 ) / 60 ) ) );
    }


	/**
	 * Removes the timestamp on certain hooks like when a post is updated.
	 *
	 * @since  3.1.0 | 19 JUN 2018 | Ported from function to class method.
	 * @param  void
	 * @return void
	 *
	 */
	public function delete_timestamp() {
		delete_post_meta( $this->id, 'swp_cache_timestamp' );
	}


	/**
     * Finishes processing the share data after the network links have been set up.
     *
     * The flow of logic should look something like this:
     * establish_permalinks();                    $this->permalinks;
     * establish_api_request_urls();              $this->api_urls;
     * fetch_api_responses();                     $this->raw_api_responses;
     * parse_api_responses();                     $this->parsed_api_responses;
     * calculate_network_shares();                $this->share_counts;
     * calculate_total_shares();                  $this->share_counts['total_shares'];
     * cache_share_counts();                      Stored in DB post meta.
     *
     * @since  3.1.0 | 21 JUN 2018 | Created
     * @access protected
     * @param  void
     * @return void
     *
     */
    protected function rebuild_share_counts() {
        global $swp_social_networks, $swp_user_options;

		$this->establish_permalinks();
		$this->establish_api_request_urls();
		$this->fetch_api_responses();
		$this->parse_api_responses();
		$this->calculate_network_shares();
		$this->cache_share_counts();
    }


	/**
	 * Establish the Permalinks to be checked for shares.
	 *
	 * The word Permalink here specifically refers to URL's of blog posts which
	 * we want to fetch share counts for. We want a system that allows us to
	 * create permalinks for the primary permalink, the share recovery permalink,
	 * allow a filter for programatic adding of others, and so on.
	 *
	 * The processed results will be stored in $this->permalinks.
     * @var permalinks Links to be checked for share counts during the
     *                 share count update process.
	 *
	 * @since  3.1.0 | 21 JUN 2018 | Created
	 * @access private
	 * @param  void
	 * @return void
	 *
	 */
    private function establish_permalinks() {
        global $swp_social_networks, $swp_user_options;
        $this->permalinks = array();

        foreach( $swp_social_networks as $key => $object):
            if ( !$object->active ) :
                continue;
            endif;

            $this->permalinks[$key][] = get_permalink( $this->id );

            if( isset( $swp_user_options['recover_shares'] ) && true === $swp_user_options['recover_shares'] ) :
                $this->permalinks[$key][] = SWP_Permalink::get_alt_permalink( $this->id );
            endif;

            $this->permalinks = apply_filters( 'swp_recovery_filter', $this->permalinks );
        endforeach;

    }


    /**
     * Prepares outbound API links per network.
     *
     * @since  3.1.0 | 25 JUN 2018 | Created the method.
     * @var    api_urls The array of outbound API request destinations.
     * @param  void
     * @return void
     *
     */
    private function establish_api_request_urls() {
        global $swp_social_networks;

        $this->api_urls = array();

        foreach ( $this->permalinks as $network => $links ) {
			$current_request = 0;
            foreach( $links as $url ) {
                $this->api_urls[$current_request][$network] = $swp_social_networks[$network]->get_api_link( $url );
				++$current_request;
			}
        }
    }


	/**
	 * Fetch responses from the network API's.
	 *
	 * This method will use the $this->api_urls array, loop through them, and
	 * using curl_multi will fetch raw responses from the network API's. The
	 * results will be stored in $this->raw_api_responses array.
	 *
	 * @since  3.1.0 | 25 JUN 2018 | Created
	 * @var    raw_api_responses An array of responses from the API's.
	 * @param  void
	 * @return void All data is stored in local properties.
	 *
	 */
    private function fetch_api_responses() {
		$current_request = 0;
        foreach ( $this->api_urls as $request => $networks ) {
            $this->raw_api_responses[$current_request] = SWP_CURL::fetch_shares_via_curl_multi( $networks );
            $current_request++;
        }
    }


	/**
	 * Parse the API responses
	 *
	 * This method will take the array of raw responses stored inside the
	 * $this->raw_api_responses property and use each network's parse method
	 * to convert them into integers that we can use to tally up our share counts.
	 *
	 * @since  3.1.0 | 25 JUN 2018 | Created
	 * @var    parsed_api_responses An array of integers from parsing the responses.
	 * @param  void
	 * @return void Processed data is stored in local properties.
	 *
	 */
    private function parse_api_responses() {
        global $swp_social_networks;

        if ( empty( $this->raw_api_responses ) ) :
            return;
        endif;

        $this->parsed_api_responses = array();

        foreach( $this->raw_api_responses as $request => $responses ) {
            $current_request = 0;

            foreach ( $responses as $key => $response ) {
                $this->parsed_api_responses[$current_request][$key][] = $swp_social_networks[$key]->parse_api_response( $response );
                $current_request++;
            }
        }
    }


	/**
	 * Calculate the network shares.
	 *
	 * This method is used to calculate the shares for each network based on
	 * what we have just retrieved from the API responses. Another method,
	 * establish_share_counts will be used to create this data from the cached
	 * database data. This one is ONLY used when the cache is not fresh and the
	 * data is being rebuilt.
	 *
	 * @since  3.1.0 | 25 JUN 2018 | Created
	 * @var    share_counts An array of share count numbers.
	 * @param  void
	 * @return void All data stored in local properties.
	 *
	 */
    private function calculate_network_shares() {
        global $swp_social_networks, $swp_user_options;

        $share_counts = array();

        $checked_networks = array();

        foreach ( $this->parsed_api_responses as $request => $networks ) {
            foreach ( $networks as $network => $count_array ) {
                foreach ( $count_array as $count ) {
                    if ( !isset( $share_counts[$network] ) ) {
                        $share_counts[$network] = 0;
                    }

                    $share_counts[$network] += $count;
                }
                $checked_networks[] = $network;
            }
        }

        if ( isset( $swp_user_options['order_of_icons'] ) ) :

            //* For defunct network shares (e.g. Google Plus, LinkedIn, StumbleUpon)
            foreach( $swp_user_options['order_of_icons'] as $network ) {
                if ( !in_array( $network, $checked_networks ) ) :
                    $count = get_post_meta( $this->id, "_${network}_shares", true );
                    $count = isset($count) ? $count : 0;
                    $share_counts[$network] = $count;
                endif;
            }

        endif;

        $this->share_counts = $share_counts;
    }


	/**
	 * Update the meta fields with the new share counts.
	 *
	 * As per the inline docblock below, we only update if larger numbers are
	 * recieved than the previous checks. This is because some networks, like
	 * Pinterest are notorious for randomly resetting some counts all the way
	 * back to zero. This will prevent a post with 10K shares from keeping the
	 * zero response.
	 *
	 * @since  3.1.0 | 25 JUN 2018 | Created
	 * @param  void
	 * @return void
	 *
	 */
    private function cache_share_counts() {
        $this->share_counts['total_shares'] = 0;

        if ( empty( $this->share_counts ) ) :
            return;
        endif;

        foreach( $this->share_counts as $key => $count ) {
            if ( 'total_shares' === $key ) {
                continue;
            }

            $previous_count = get_post_meta( $this->id, "_${key}_shares", true);

            if ( empty( $previous_count ) ) {
                $previous_count = 0;
            }

			// We only update to newly fetched numbers if they're bigger than
			// the old ones unless the url parameter is forcing it to take.
            if ( $count <= $previous_count && false === _swp_is_debug( 'force_new_shares' ) ) {
                $this->share_counts[$key] = $previous_count;
                $this->share_counts['total_shares'] += $previous_count;
            } else {
                $this->share_counts['total_shares'] += $count;
            }

            delete_post_meta( $this->id, "_${key}_shares");
            update_post_meta( $this->id, "_${key}_shares", $this->share_counts[$key] );
        }

        delete_post_meta( $this->id, '_total_shares');
        update_post_meta( $this->id, '_total_shares', $this->share_counts['total_shares'] );
    }


	/**
	 * Gets the computed share data.
	 *
	 * @since  3.1.0 | 20 JUN 2018 | Created the method.
	 * @param  void
	 * @return array $this->share_counts if it exists, or an empty array.
	 *
	 */
	public function get_shares() {
		if ( !empty( $this->share_counts ) ) :
			return $this->share_counts;
		endif;

		return array();
	}


	/**
	 * Process the existing share data, or update it.
	 *
	 * @todo Remove all fresh_cache() checks. This method needs to assume the
	 * cache is always fresh and always return cached data.
	 *
	 * @since 3.1.0 | 21 JUN 2018 | Created the method.
	 * @access protected
	 * @param  void
	 * @return void
	 *
	 */
	protected function establish_share_counts() {
		global $swp_social_networks;

		foreach( $swp_social_networks as $network => $network_object ) {
			if ( !isset( $swp_social_networks[$network] ) ) :
				continue;
			endif;

            $count = get_post_meta( $this->id, '_' . $network . '_shares', true );
			$this->share_counts[$network] = $count ? $count : 0;
		}

        $total = get_post_meta( $this->id, '_total_shares', true );
        $this->share_counts['total_shares'] = $total ? $total : 0;
	}

}
