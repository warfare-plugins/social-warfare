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
 * @since     3.0.10 | 20 JUN 2018 | Created
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
	 * Permalinks
	 *
	 * This variable contains an array of permalinks to be checked for share
	 * counts during the share count update process.
	 *
	 * @see $this->establish_permalinks();
	 * @var array
	 *
	 */
	protected $permalinks = array();


    public $share_data = array();
    protected $old_shares = array();
    protected $api_links = array();

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
        if ( true === $this->is_cache_fresh() ):
			$this->rebuild_cached_data();
		endif;

        $this->establish_share_data();

		// Reset portions of the cache when a post is updated.
        $this->init_publish_hooks();

    }


	/**
	 * Establish the Post Data
	 *
	 * @since  3.0.10 | 20 JUN 2018 | Created
	 * @param  integer $post_id The post id.
	 * @return void    All processed data are stored in local properties.
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
	* @since  3.0.10 | 19 JUN 2018 | Ported from function to class method.
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

 		// Check if the cache is older than is allowable for this post.
 		if( $this->get_cache_age() >= $this->get_allowable_age() ):
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

         // An integer in hours. Example: 424814 == Number of hourse since Unix epoch.
     	$current_time = floor( ( ( date( 'U' ) / 60 ) / 60 ) );

         // The integer in hours at time of storage since the Unix epoch.
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

        $this->rebuild_share_counts();
        $this->rebuild_pinterest_image();
        $this->rebuild_open_graph_image();
        $this->reset_timestamp();

        //* If we can find a way to spoof IP addresses, we can also remove the
        //* Facebook ajax methods.
        add_action( 'wp_ajax_facebook_shares_update', array( $this, 'facebook_shares_update' ) );
		add_action( 'wp_ajax_nopriv_facebook_shares_update', array( $this, 'facebook_shares_update' ) );
        add_action( 'wp_footer', array( $this, 'print_ajax_script' ) );

		// A hook to run allowing third-party functions to run.
		do_action('swp_cache_rebuild');

	}


	/**
     * Sets the cache to rebuild itself when a user creates or updates a post.
     *
     * @since  3.0.10 | 21 JUN 2018 | Created the method.
     * @param  void
     * @return void
     *
     */
	protected function init_publish_hooks() {
		add_action( 'save_post', array( $this, 'rebuild_cached_data' ) );
		add_action( 'publish_post', array( $this, 'rebuild_cached_data' ) );
	}


    /**
     * Pinterest Image
     *
     * Convert the pinterest image ID to a URL and store it in a meta field
     * because then the URL will be autoloaded with the post preventing the
     * need for an additional database query during page loads.
     *
     * @since  3.0.10 | 19 JUN 2018 | Ported from function to class method.
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
     * @since 3.0.10 | 19 JUN 2018 | Ported from function to class method.
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
     * @since 3.0.10 | 19 JUN 2018 | Ported from function to class method.
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
	 * @since  3.0.10 | 19 JUN 2018 | Ported from function to class method.
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
     * Note: There should not be any calls to check if the cache is fresh in
     * any of these methods. It should check once in the constructor. If any of
     * these methods are called, then the cache is NOT fresh and can therefore
     * skip any checks for it.
     *
     * The flow of logic should look something like this:
     * establish_permalinks();                    $this->permalinks;
     * establish_api_request_urls();              $this->api_urls;
     * fetch_api_responses();                     $this->raw_api_responses;
     * parse_api_responses();                     $this->parsed_api_responses;
     * calculate_network_shares();                $this->share_counts;
     * calculate_total_shares();                  $this->share_counts;
     * cache_share_counts();                      Stored in DB post meta.
     *
     * @since  3.0.10 | 21 JUN 2018 | Created
     * @access protected
     * @param  void
     * @return void
     *
     */
    protected function rebuild_share_counts() {

		// Pull in necessary globals.
        global $swp_social_networks, $swp_user_options;

		/**
		 * Required flow of Logic
		 *
		 * @todo Uncomment these methods as they are created.
		 *
		 */
		$this->establish_permalinks();
		// $this->establish_api_request_urls();
		// $this->fetch_api_responses();
		// $this->parse_api_responses();
		// $this->calculate_network_shares();
		// $this->calculate_total_shares();
		// $this->cache_share_counts();

        foreach ($this->permalinks as $link ) {
            $unprocessed_share_data = SWP_CURL::fetch_shares_via_curl_multi( $api_links );

            foreach( $swp_social_networks as $network => $network_object ) {
                $share_count = $network_object->parse_api_response($unprocessed_share_data[$network]);
                $this->share_data[$network] += $share_count;
                $this->share_data['total_shares'] += $share_count;
            }
        }

        //* This needs to be a separate loop so all of the share data can be summed.
        foreach( $swp_social_networks as $network => $network_object ) {
            delete_post_meta( $this->id, '_' . $network . '_shares' );
            update_post_meta( $this->id, '_' . $network . '_shares', $this->share_data[$network] );
        }

        delete_post_meta( $this->id, '_total_shares' );
        update_post_meta( $this->id, '_total_shares', $this->share_data['total_shares'] );
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
	 *
	 * @since  3.0.10 | 21 JUN 2018 | Created
	 * @access private
	 * @param  void
	 * @return void
	 *
	 */
	private function establish_permalinks() {

        global $swp_social_networks;
        $this->permalinks = array();

        foreach( $swp_social_networks as $key => $object):
            $this->permalinks[$key][] = get_the_permalink();
            if( $share_recovery_in_the_options == 'blahblah' ):
                $this->permalinks[$key][] = get_the_other_permalink_that_we_need_to_check_for();
            endif;

        endforeach;

        $this->permalinks = apply_filter('swp_recovery_urls', $this->permalinks );

    }


    /**
     *  Prepares the API link(s) and old share data for a network.
     *
     */
    protected function prepare_network( $network ) {
        global $swp_social_networks, $swp_user_options;

        $this->permalinks[$network]	= $swp_social_networks[$network]->get_api_link( $url );

        if ( $swp_user_options['recover_shares'] == true ) :
            array_merge( $this->permalinks, apply_filters( 'swp_recovery_filter' ) );
            $this->permalinks[] = SWP_Permalink::get_alt_permalink( $this->id );
        endif;
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
	 * @todo Remove all fresh_cache() checks. This method needs to assume the
	 * cache is always fresh and always return cached data.
	 *
	 * @since 3.0.10 | 21 JUN 2018 | Created the method.
	 * @access protected
	 * @param  void
	 * @return void
	 * 
	 */
	protected function establish_share_data() {
		global $swp_social_networks;

		foreach( $swp_social_networks as $network => $network_object ) {
			if ( !isset( $swp_social_networks[$network] ) ) :
				continue;
			endif;

			$this->is_cache_fresh() ? $this->establish_cached_shares( $network ) : $this->prepare_network( $network );
		}

		$this->is_cache_fresh() ? $this->establish_total_shares() : $this->rebuild_share_data();
	}

	protected function establish_total_shares() {
		if ( get_post_meta( $this->id, '_total_shares', true ) ) :
			$this->share_data['total_shares'] = get_post_meta( $this->id, '_total_shares', true );
		else :
			$this->share_data['total_shares'] = 0;
		endif;
	}

	protected function establish_cached_shares( $network ) {
		$this->share_data[$network] = get_post_meta( $this->id, '_' . $network . '_shares', true );
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

}
