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
	 * SWP_Debug_Trait provides useful tool like error handling and a debug
	 * method which outputs the contents of the current object.
	 *
	 */
	use SWP_Debug_Trait;


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
	 * A collection of permalinks for which to check for share counts.
	 *
	 * @var array
	 *
	 */
	public $permalinks = array();


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
		// Set up the post data into local properties.
		$this->post_id = $post_id;
		$this->establish_share_counts();

		// If the cache is expired, trigger the rebuild processes.
		if ( false === $this->is_cache_fresh() ){
			$this->rebuild_cached_data();
		}

		// Debugging
		add_action( 'wp_footer', array( $this, 'debug' ) );
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
		if ( !is_singular() && !is_admin() ) :
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


		/**
		 * Fetch the current time and the time that the cache was last updated
		 * so that we can compare them to find out how old the cache is.
		 *
		 */
		 $current_time      = floor( ( ( date( 'U' ) / 60 ) / 60 ) );
		 $last_updated_time = get_post_meta( $this->post_id, 'swp_cache_timestamp', true );


		/**
		 * If the meta field is empty or non-existent, get_post_meta() will
		 * return false. If it does, we'll simply convert it to an integer (0)
		 * so that we can use it in the mathematical comparisons.
		 *
		 */
		if ( false == is_numeric( $last_updated_time ) ) {
			$last_updated_time = 0;
		}


		/**
		 * Compare the current time to the time the cache was last updated, and
		 * determine the age of the cache.
		 *
		 */
		 $cache_age = $current_time - $last_updated_time;

		 return $cache_age;
	}


	 /**
	  * Get the duration during which this cache can be considered fresh.
	  *
	  * A cache is fresh for the following durations:
	  *     1 Hour   - New Posts less than 21 days old.
	  *     4 Hours  - Medium Posts less than 60 days old.
	  *     12 Hours - Old Posts Older than 60 days old.
	  *     24 Hours - Share counts are disabled, but we still need to fetch
	  *                periodically for the admin post column and popular posts
	  *                widget to have data to puplate correctly.
	  *
	  * @since  3.1.0 | 20 JUN 2018 | Created
	  * @since  3.4.0 | Added check for share counts being active.
	  * @param  void
	  * @return integer The duration in hours that applies to this cache.
	  *
	  */
	 public function get_allowable_age() {


		/**
		 * Don't fetch share counts very often if share counts are disabled both
		 * as totals and on the buttons. We will only fetch once in a while so
		 * that we can cache the data and use it for things like the popular
		 * posts calculations and the admin posts column.
		 *
		 */
		$network_shares = SWP_Utility::get_option( 'network_shares' );
		$total_shares   = SWP_Utility::get_option( 'total_shares' );
		if( false == ( $network_shares || $total_shares ) ) {
			return 24;
		}

		// Integer in hours of the current age of the post.
		$current_time     = floor( date( 'U' ) );
		$publication_time = get_post_time( 'U' , false , $this->post_id );
		$post_age         = $current_time - $publication_time;


		// If it's less than 3 days old.
		if( $post_age < ( 3 * 86400 ) ) {
			return 1;
		}

		// If it's less than 21 days old.
		if( $post_age < ( 21 * 86400 ) ) {
			return 3;
		}

		// If it's less than 60 days old.
		if( $post_age < ( 60 * 86400 ) ) {
			return 6;
		}

		// If it's really old.
		return 24;
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

		$this->update_image_cache( 'swp_pinterest_image' );
		$this->update_image_cache( 'swp_og_image' );

		// Don't run these methods unless the post is published.
		if( true === $this->is_post_published() ) {
			$this->rebuild_share_counts();
			$this->process_urls();
			$this->reset_timestamp();

			// A hook to allow third-party functions to run.
			do_action( 'swp_cache_rebuild', $this->post_id );
		}
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
	protected function is_post_published() {

		// Only fetch on published posts
		if( 'publish' !== get_post_status( $this->post_id ) ) {
			$this->debug_message( 'No data updated. This post is not yet published.' );
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
		$permalink = get_permalink( $this->post_id );
		foreach( $swp_social_networks as $network ) {
			if( $network->is_active() ) {
				SWP_Link_Manager::process_url( $permalink, $network->key, $this->post_id, false );
			}
		}
	}


	/**
	 * Store image url, id, and metadata in post_meta for quicker access later.
	 *
	 * @since  3.5.0 | 19 DEC 2018 | Merged old methods into this new method.
	 * @since  3.6.0 | 22 APR 2019 | Remvoed calls to delete the original field.
	 * @param  string $meta_key The image field to update. Known examples include
	 *                          swp_og_image, swp_pinterest_image, swp_twitter_image
	 * @param  int    $new_id The attachment ID to update.
	 * @return void
	 *
	 */
	public function update_image_cache( $meta_key ) {


		/**
		 * Fetch the ID of the image in question. We will use this to extrapalate
		 * the information that we need to prepopulate into the other fields.
		 *
		 */
		$new_id   = SWP_Utility::get_meta( $this->post_id, $meta_key );
		$old_data = SWP_Utility::get_meta_array( $this->post_id, $meta_key . '_data' );


		/**
		 * The following two processes are designed to fix and restore the image
		 * from a bug that was either converting the field from and ID to an array
		 * or was deleting the field entirely in which case we restore it from
		 * the cached fields.
		 *
		 * RESTORE FIELD FROM CACHE
		 *
		 * If the field is empty, but we have the correct cached data, then
		 * let's repopulate the field from the cache. The empty field is most
		 * likely caused by the bug from 3.5.x that was deleting or altering
		 * the data in the field. This will restore it.
		 *
		 * RESTORE FIELD FROM ARRAY
		 *
		 * If the meta key was stored as an array, let's find the URL of the
		 * image, convert it back to the correct ID of said image, and store that
		 * back in the original meta field.
		 *
		 * This was caused by a bug in a previous version that was overwriting
		 * the ID in this field with the image_data array. This will fix that
		 * and restore the field to an ID.
		 *
		 */
		$restore_from_cache = empty( $new_id ) && is_array( $old_data ) && false !== filter_var( $old_data[0], FILTER_VALIDATE_URL );
		$restore_from_array = is_array( $new_id ) && false !== filter_var( $new_id[0], FILTER_VALIDATE_URL );


		/**
		 * Filter out requests from the admin so that this "fix" doesn't
		 * override the user's preference whilst they are updating a post.
		 *
		 * This block is for people who are missing a key like `swp_og_image`
		 * between v3.5.0 and v3.5.4. The logic below will create the missing
		 * key based off of data we have previously saved.
		 *
		 */
		if ( ($restore_from_cache || $restore_from_array) && !is_admin() ) {


			// Convert the image URL into a valid WP ID.
			if ( $restore_from_array ) {
				$new_id = SWP_Utility::get_image_id_by_url( $new_id[0] );
			} elseif ( $restore_from_cache ) {
				$new_id = SWP_Utility::get_image_id_by_url( $old_data[0] );
			}

			// Bail if we didn't get an ID from the above function.
			if ( empty( $new_id ) ) {
				return;
			}

			// Delete and update the meta field with the corrected ID.
			delete_post_meta( $this->post_id, $meta_key );
			update_post_meta( $this->post_id, $meta_key, $new_id );
		}


		/**
		 * If there is no image ID from the meta field, we need to delete this
		 * and all related fields just in case there used to be an image but it
		 * was removed. Prior to deleting these fields, the Pinterest image
		 * URL and data generated here would persist after the image was
		 * deleted from the meta field.
		 *
		 */
		if ( empty( $new_id ) ) {
			delete_post_meta( $this->post_id, $meta_key.'_data' );
			delete_post_meta( $this->post_id, $meta_key.'_url' );
			delete_post_meta( $this->post_id, $meta_key );
			return;
		}


		/**
		 * Fetch the data array of the new image and the data array of the old
		 * previously cached image (fetchd above) so that we can see if anything
		 * has changed.
		 *
		 */
		$new_data = wp_get_attachment_image_src( $new_id, 'full_size' );


		/**
		 * If the old data is the same as the new data, then there is no need to
		 * make any new database calls. Just exit and move on with our lives.
		 *
		 */
		if ( false == $new_data || $new_data === $old_data ) {
			return;
		}


		/**
		 * We are not changing the value of the original field which contains
		 * the WordPress attachement ID of the image in question. We are,
		 * however, updating two additional fields (_data and _url) so that this
		 * data will be preloaded with the post load. We will delete them first
		 * to ensure that we never have more than one of the same field.
		 *
		 */
		delete_post_meta( $this->post_id, $meta_key.'_data' );
		delete_post_meta( $this->post_id, $meta_key.'_url' );
		update_post_meta( $this->post_id, $meta_key.'_data', json_encode( $new_data ) );
		update_post_meta( $this->post_id, $meta_key.'_url', $new_data[0] );
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
		delete_post_meta( $this->post_id, 'swp_cache_timestamp' );
		update_post_meta( $this->post_id, 'swp_cache_timestamp', floor( ( ( date( 'U' ) / 60 ) / 60 ) ) );
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
		delete_post_meta( $this->post_id, 'swp_cache_timestamp' );
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
	 * @since  4.0.0 | 20 FEB 2020 | Added call to debug_display_permalinks()
	 * @since  4.0.0 | 21 FEB 2020 | Added call to add_trailing_slashes()
	 * @param  void
	 * @return void
	 *
	 */
	protected function establish_permalinks() {
		global $swp_social_networks;
		$this->permalinks = array();


		/**
		 * Loop through the global social network objects, identify the active
		 * networks, and find the permalinks to check for each one.
		 *
		 */
		foreach( $swp_social_networks as $key => $object) {


			/**
			 * Ensure that the array object is already setup to recieve the
			 * permalinks.
			 *
			 */
			if( !isset( $this->permalinks[$key] ) ) {
				$this->permalinks[$key] = array();
			}


			/**
			 * If this particular network isn't active, we need to skip it and
			 * not fetch any share counts for it.
			 *
			 */
			if ( false == $object->active ) {
				continue;
			}


			/**
			 * This is the standard, current permalink for the post. We use the
			 * standard permalink by default for checking for share counts.
			 *
			 */
			$this->permalinks[$key][] = get_permalink( $this->post_id );


			/**
			 * If share count recovery is activated, we'll add a second permalink
			 * to the array for each network. So now we'll have two permalinks
			 * for which to fetch share counts.
			 *
			 */
			if( true === SWP_Utility::get_option('recover_shares') ) {
				$this->permalinks[$key][] = SWP_Permalink::get_alt_permalink( $this->post_id );
			}


			/**
			 * This filter allows third-parties to enable another permalink for
			 * which to check for share counts.
			 *
			 */
			$this->permalinks = apply_filters( 'swp_recovery_filter', $this->permalinks );


			/**
			 * Duplicates the URL's so that the given network will be check for
			 * a permalink with both a trailing slash and without one.
			 *
			 */
			$this->add_trailing_slashes( $key );

			$this->add_utm_codes( $key );

		}

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
	protected function establish_api_request_urls() {
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
	protected function fetch_api_responses() {
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
	protected function parse_api_responses() {
		global $swp_social_networks;


		/**
		 * If for any reason the $raw_api_responses property failed to get
		 * populated just gracefully bail out and stop processing.
		 *
		 */
		if ( empty( $this->raw_api_responses ) ) {
			return;
		}
		$this->parsed_api_responses = array();

		foreach( $this->raw_api_responses as $request => $responses ) {
			$current_request = 0;
			foreach ( $responses as $key => $response ) {
				$this->parsed_api_responses[$current_request][$key][] = $swp_social_networks[$key]->parse_api_response( $response );
				$current_request++;
			}
		}


		/**
		 * This will output the checked permalinks to the screen when the following
		 * URL parameters are added to the address bar: ?swp_cache=rebuild&swp_debug=recovery.
		 *
		 */
		$this->debug_display_permalinks();
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
	 * @since  3.4.0 | 18 OCT 2018 | Refactored to ensure that force_new_shares
	 *                               works the way that it's supposed to.
	 * @since  3.4.0 | 18 OCT 2018 | Added array_unique to prevent double counts.
	 * @var    share_counts An array of share count numbers.
	 * @param  void
	 * @return void All data stored in local properties.
	 *
	 */
	protected function calculate_network_shares() {
		global $swp_social_networks;


		/**
		 * If for any reason the $parsed_api_responses property failed to get
		 * populated just gracefully bail out and stop processing.
		 *
		 */
		if ( empty( $this->parsed_api_responses ) ) {
			return;
		}

		$share_counts                 = array();
		$share_counts['total_shares'] = 0;
		$checked_networks             = array();


		/**
		 * This loops through all of the parsed API responses and converts them
		 * into share counts. The next loop below will then go through all the
		 * remaining networks that didn't have API requests/responses.
		 *
		 */
		foreach ( $this->parsed_api_responses as $request => $networks ) {
			foreach ( $networks as $network => $count_array ) {


				/**
				 * Added a call to array_unique to eliminate duplicate share
				 * counts when share recovery is active. In some cases, the
				 * social networks detect the change in URL and return the same
				 * share count for the current URL as well as the old, redirected
				 * URL. This prevents the count from being doubled.
				 *
				 */
				$count_array = array_unique( $count_array );
				foreach ( $count_array as $count ) {
					if ( !is_numeric( $count ) ) {
						continue;
					}

					if ( !isset( $share_counts[$network] ) ) {
						$share_counts[$network] = 0;
					}

					$share_counts[$network] += $count;
				}

				$checked_networks[] = $network;
			}
		}


		/**
		 * After we processed the API responses, we'll now go through all active
		 * networks regardless of whether or not they have an API, and process
		 * their share counts. Of course, most of these will be zeroes unless it
		 * is a network that used to have share counts. If so, we will not
		 * override the old share counts unless the user is using the debug
		 * parameter to force it to do so.
		 *
		 */
		foreach( SWP_Utility::get_option( 'order_of_icons' ) as $network ) {
			$count = 0;


			/**
			 * If this is a network that we checked above (that has an API),
			 * then let's start by using the count fetched from the API.
			 *
			 */
			if ( in_array( $network, $checked_networks ) ) {
				$count = $share_counts[$network];
			}


			/**
			 * Let's fetch the previous count that we have stored in the database
			 * from the previous API calls so that we can run a comparison.
			 *
			 */
			$previous_count = get_post_meta( $this->post_id, "_${network}_shares", true );
			if( false === $previous_count ) {
				$previous_count = 0;
			}


			/**
			 * The ?swp_debug=force_new_shares will force it to update to the
			 * newest numbers even if it is a lower number. If this debug
			 * parameter is off, however, then we simply use whichever number is
			 * highest between the current and previously fetched counts.
			 *
			 */
			if ( $count < $previous_count && false == SWP_Utility::debug( 'force_new_shares' ) ) {
				$count = $previous_count;
			}


			/**
			 * Iterate the total shares with our new numbers, and then store
			 * this network's count in the local property for caching and
			 * display later on.
			 *
			 */
			$share_counts['total_shares'] += $count;
			$share_counts[$network]        = $count;

		}

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
	protected function cache_share_counts() {
		global $swp_social_networks;


		/**
		 * If the local property $share_counts is empty, then we won't have any
		 * share counts to cache in the database so just bail out.
		 *
		 */
		if ( empty( $this->share_counts ) ) {
			return;
		}


		/**
		 * Loop through the share counts for each network and store the new
		 * counts in the databse in custom fields.
		 *
		 * @var $key The key corresponding to a social network (e.g. 'twitter')
		 * @var $count The share count for this network.
		 *
		 */
		foreach( $this->share_counts as $key => $count ) {

			// Skip it if this is the total shares. This will be added later.
			if ( empty( $swp_social_networks[$key] ) ) {
				continue;
			}

			if( 0 === $swp_social_networks[$key]->get_api_link('') ) {
				continue;
			}

			// Access the Social_Network object and update its count.
			$Current_Social_Network = $swp_social_networks[$key];
			$Current_Social_Network->update_share_count( $this->post_id, $count );
		}

		// Update the total shares.
		delete_post_meta( $this->post_id, '_total_shares');
		update_post_meta( $this->post_id, '_total_shares', $this->share_counts['total_shares'] );
		$this->cleanup_remnants();
		do_action('swp_analytics_record_shares', $this->post_id, $this->share_counts );
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
		if ( !empty( $this->share_counts ) ) {
			return $this->share_counts;
		}

		return array();
	}


	/**
	 * Fetch and return the cached share data from the database.
	 *
	 * @since 3.1.0 | 21 JUN 2018 | Created the method.
	 * @access protected
	 * @param  void
	 * @return void
	 *
	 */
	protected function establish_share_counts() {
		global $swp_social_networks;
		$this->share_counts['total_shares'] = 0;

		/**
		 * Loop through the social networks and pull their share count from
		 * the custom fields for this post.
		 *
		 */
		foreach( $swp_social_networks as $Network ) {

			// Get the current share count from the cache.
			$this->share_counts[$Network->key] = $Network->get_share_count( $this->post_id );

			// Add up the total shares based on the counts of the active networks.
			if( true === $Network->is_active() ) {
				$this->share_counts['total_shares'] += $this->share_counts[$Network->key];
			}
		}
	}


	/**
	 * A method for outputting debug notices when cache rebuild parameters are present.
	 *
	 * @since  3.2.0 | 31 JUL 2018 | Created
	 * @param  string $string The message to be displayed.
	 * @return void
	 *
	 */
	protected function debug_message( $string ) {
		if( isset( $_GET['swp_cache'] ) && 'rebuild' === $_GET['swp_cache'] ) {
			echo $string;
		}
	}


	/**
	 * A method for displaying the permalinks that are being used to fetch
	 * share counts for this post or page. This will output an array of permalinks
	 * separated by social network.
	 *
	 * To activate: ?swp_cache=rebuild&swp_debug=recovery
	 *
	 * @since  4.0.0 | 19 FEB 2020 | Created
	 * @param  void
	 * @return void All data is output to the screen.
	 *
	 */
	protected function debug_display_permalinks() {

		// Bail if debugging is not currently active.
		if( false === SWP_Utility::debug( 'recovery' ) ) {
			return;
		}


		// Output the preformatted box with the array of permalinks.
		echo '<pre style="background:yellow;">';
		$with_pro = '';
		if( defined( 'SWPP_VERSION' ) && defined( 'SWPP_DEV_VERSION' ) ) {
			$with_pro = '(with Pro ' . SWPP_VERSION .'.'. SWPP_DEV_VERSION .')';
		}
		echo '<p>Social Warfare ' . SWP_VERSION .'.'. SWP_DEV_VERSION .' ' . $with_pro . ' </p>';

		echo '<h1>The URL\'s Being Checked:</h1>';
		var_dump($this->permalinks);
		echo '<h1>The Responses from the API:</h1>';
		var_dump($this->raw_api_responses);
		echo '<h1>This is the share counts array</h1>';
		var_dump($this->share_counts);
		echo '</pre>';
	}


	/**
	 * This function will add or remove the trailing slash so that we check a
	 * network for share counts for both versions of the link. Now we will check
	 * for the following versions of a link:
	 *
	 * Note: This is currently done exclusively for Pinterest, but other networks
	 * can be added instantaneously by adding them to the $eligible_networks array below.
	 *
	 * /my-blog-post/
	 * /my-blog-post
	 *
	 * @since 4.0.0 | 21 FEB 2020 | Created
	 * @param string $key The key corresponding to the current social network.
	 * @return void All data is stored in class properties.
	 *
	 */
	protected function add_trailing_slashes( $key ) {

		// The list of networks that we will check both URL versions for.
		$eligible_networks = array('pinterest');

		// If this isn't one of those networks, bail out early.
		if( false === in_array( $key, $eligible_networks ) ) {
			return false;
		}

		/**
		 * We'll add our newly created permalinks to this now-empty array. Later
		 * we'll merge this back into the original class property array.
		 *
		 */
		$new_links = array();


		/**
		 * We'll loop through every single permalink that is being checked for
		 * this network (both normal and recovery), and create a second version
		 * either by adding a trailing slash or removing it.
		 *
		 */
		foreach( $this->permalinks[$key] as $permalink ) {

			// If it doesn't have a trailing slash, we'll add one.
			if( false === SWP_Utility::ends_with( $permalink, '/' ) ) {
				$new_links[] = $permalink . '/';

			// If it does have a trailing slash, we'll remove it.
			} else {
				$new_links[] = rtrim( $permalink, '/');
			}
		}


		/**
		 * Merge our newly created permalinks array into the class property array
		 * whilst specifically targeting the array that lives in the indice for
		 * this network.
		 */
		$this->permalinks[$key] = array_merge( $this->permalinks[$key], $new_links );

	}


	/**
	 * This function will add a version of the URL with UTM codes so we check a
	 * network for share counts for both versions of the link. Now we will check
	 * for the following versions of a link:
	 *
	 * Note: This is currently done exclusively for Pinterest, but other networks
	 * can be added instantaneously by adding them to the $eligible_networks array below.
	 *
	 * /my-blog-post/
	 * /my-blog-post/?utm=blahblah
	 *
	 * @since 4.2.0 | 30 NOV 2020 | Created
	 * @param string $key The key corresponding to the current social network.
	 * @return void All data is stored in class properties.
	 *
	 */
	protected function add_utm_codes( $key ) {
		global $swp_social_networks;

		// The list of networks that we will check both URL versions for.
		$eligible_networks = array('pinterest');

		// If this isn't one of those networks, bail out early.
		if( false === in_array( $key, $eligible_networks ) ) {
			return false;
		}

		// If Google Analytics are turned off, then just bail out.
		if( false === SWP_Utility::get_option( 'google_analytics' ) ) {
			return;
		}


		// If UTM is turned off on Pinterest, and this is Pinterest, just bail out.
		if( false === SWP_Utility::get_option( 'utm_on_pins' ) && 'pinterest' === $key ) {
			return;
		}


		/**
		 * We'll add our newly created permalinks to this now-empty array. Later
		 * we'll merge this back into the original class property array.
		 *
		 */
		$new_links = array();

		// The data needed by the get_shareable_permalink() method below.
		$post_data = array(
			'ID'        => $this->post_id,
			'permalink' => $this->permalinks[$key][0]
		);

		$new_links[] = urldecode( $swp_social_networks[$key]->get_shareable_permalink( $post_data ) );


		/**
		 * Merge our newly created permalinks array into the class property array
		 * whilst specifically targeting the array that lives in the indice for
		 * this network.
		 */
		$this->permalinks[$key] = array_merge( $this->permalinks[$key], $new_links );
	}


	public function cleanup_remnants() {
		delete_post_meta( $this->post_id, '_totes');
		delete_post_meta( $this->post_id, '_email_shares');
		delete_post_meta( $this->post_id, '_more_shares');
		delete_post_meta( $this->post_id, '_print_shares');
		delete_post_meta( $this->post_id, '_telegram_shares');
	}
}
