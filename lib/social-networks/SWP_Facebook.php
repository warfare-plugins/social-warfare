<?php

/**
 * Facebook
 *
 * Class to add a Facebook share button to the available buttons
 *
 * @package   SocialWarfare\Functions\Social-Networks
 * @copyright Copyright (c) 2018, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     1.0.0 | Unknown     | CREATED
 * @since     2.2.4 | 02 MAY 2017 | Refactored functions & updated docblocking
 * @since     3.0.0 | 05 APR 2018 | Rebuilt into a class-based system.
 * @since     3.6.0 | 22 APR 2018 | Removed all Javascript related functions for
 *                                  fetching share counts. This includes:
 *                                      register_cache_processes()
 *                                      add_facebook_footer_hook()
 *                                      print_facebook_script()
 *                                      facebook_shares_update()
 *                                 Shares are now fetched using the same two
 *                                 method process that are used by all other
 *                                 social networks in the plugin.
 *
 */
class SWP_Facebook extends SWP_Social_Network {


	/**
	 * The private $Authorization property will contain an instance of the
	 * SWP_Auth_Helper class which will allow us access to things like the
	 * get_access_token() and has_valid_token() methods.
	 *
	 * @see class SWP_Auth_Helper in /lib/utilities/SWP_Auth_Helper.php
	 * @var Object
	 *
	 */
	private $Authentication;


	/**
	 * The Magic __construct Method
	 *
	 * This method is used to instantiate the social network object. It does three things.
	 * First it sets the object properties for each network. Then it adds this object to
	 * the globally accessible swp_social_networks array. Finally, it fetches the active
	 * state (does the user have this button turned on?) so that it can be accessed directly
	 * within the object.
	 *
	 * @since  3.0.0 | 06 APR 2018 | Created
	 * @param  none
	 * @return none
	 * @access public
	 *
	 */
	public function __construct() {

		// Update the class properties for this network
		$this->name           = __( 'Facebook','social-warfare' );
		$this->cta            = __( 'Share','social-warfare' );
		$this->key            = 'facebook';
		$this->default        = 'true';


		/**
		 * This will add the authentication module to the options page so that
		 * we can fetch share counts through the authenticated API.
		 *
		 */
		$this->Authentication = new SWP_Auth_Helper( $this->key );
		add_filter( 'swp_authorizations', array( $this->Authentication, 'add_to_authorizations' ) );


		/**
		 * This will check to see if the user has connected Social Warfare with
		 * Facebook using the oAuth authentication. If so, we'll use the offical
		 * authentication API to fetch share counts. If not, we'll use the open,
		 * unauthenticated API.
		 *
		 */
		$this->access_token = $this->Authentication->get_access_token();

		// This is the link that is clicked on to share an article to their network.
		$this->base_share_url = 'https://www.facebook.com/share.php?u=';

		$this->init_social_network();
		$this->register_ajax_cache_callbacks();
		$this->display_access_token_notices();
	}


	/**
	 * Generate the API Share Count Request URL
	 *
	 * @since  1.0.0 | 06 APR 2018 | Created
	 * @since  3.6.0 | 22 APR 2019 | Updated Facebook API call to v3.2.
	 * @since  4.0.1 | 02 APR 2020 | Added access_token based API call.
	 * @since  4.1.0 | 21 JUL 2020 | Updated Facebook API call to 7.0.
	 * @since  4.1.0 | 23 JUL 2020 | Added use of has_valid_token() method.
	 * @since  4.3.0 | 04 JUN 2021 | Added call for get_og_id().
	 * @access public
	 * @param  string $url The permalink of the page or post for which to fetch share counts
	 * @return string $request_url The complete URL to be used to access share counts via the API
	 *
	 */
	public function get_api_link( $url ) {


		/**
		 * This will check to see if the user has connected Social Warfare with
		 * Facebook using the oAuth authentication. If so, we'll use the offical
		 * authentication API to fetch share counts. If not, we'll use the open,
		 * unauthenticated API, but we'll do so via the frontend JavaScript call
		 * later on via a different function.
		 *
		 */
		if( $this->Authentication->has_valid_token() && false !== $this->get_og_id($url) ) {

			// Organize the necessary URL parameters.
			$paremeters['id']           = $this->get_og_id( $url );
			$paremeters['fields']       = 'engagement';
			$paremeters['access_token'] = $this->Authentication->get_access_token();

			// Compile the API URL
			$api_url = 'https://graph.facebook.com/v10.0/?' . http_build_query( $paremeters );

			// Return the API link to the caller.
			return $api_url;
		}

		// Return 0 as no server side check will be done. We'll check via JS later.
		return 0;
	}



	/**
	 * Thie method exists to fetch and store the Facebook Open Graph ID of a
	 * particular URL. For some reason, when we request "?fields=engagement" with
	 * a URL in the ID parameter, it returns much different results than when
	 * requesting it with the OG ID in the ID parameter.
	 *
	 * Example: URL is https://beta.warfareplugins.com/index.php/2020/10/21/hello-world/
	 *          OG ID is 4578410412187144
	 *
	 * @since 4.3.0 | 04 JUN 2021 | Created
	 * @param  String $url The URL of the post being checked.
	 * @return Integer/Bool The ID number (integer) or false on failure (bool)
	 *
	 */
	private function get_og_id( $url ) {

		// Fetch the post ID
		$post_id     = get_the_id();


		/**
		 * Check if the Facebook ID is already stored in a post meta field. If
		 * we succeed in fetching the POST ID, we'll store it in this field so
		 * that we don't have to waste an API call on future uses in order to
		 * use this ID. So the first thing we do is check to see if we already
		 * have one. If so, we'll return it instead of continuing on.
		 *
		 */
		$facebook_id = get_post_meta( $post_id, '_facebook_og_id', true );
		if( false !== $facebook_id && !empty( $facebook_id ) ) {
			return $facebook_id;
		}


		// Bail if the user doesn't have Facebook authenticated.
		if( false == $this->Authentication->has_valid_token() ) {
			return false;
		}


		/**
		 * Check to make sure that we haven't already attempted to fetch this ID
		 * within the past 10 minutes. We only get so many API requests per URL
		 * per app so we need to throttle our attempts to reach the API here. As
		 * such, if the attempt fails, we won't try again for a minimum of 10
		 * minutes between calls.
		 *
		 */
		$previous_check_timestamp = get_post_meta( $post_id, '_facebook_og_id_timestamp', true );
		if( $previous_check_timestamp > time() - 600 ) {
			return false;
		}


		/**
		 * If everything checks out above, we'll proceed to make an API request.
		 * The basic goal here is to give Facebook the URL and then use the
		 * response to have the Facebook OG ID.
		 *
		 */

		// Organize the necessary URL parameters.
		$query['access_token'] = $this->Authentication->get_access_token();
		$query['fields']       = 'engagement,og_object';
		$query['id']           = $url;

		// Compile the API link.
		$api_url = 'https://graph.facebook.com/v11.0/?' . http_build_query( $query );

		// Make and parse the API call.
		$response = SWP_CURL::file_get_contents_curl( $api_url );
		// var_dump($response);	
		$response = json_decode( $response );

		// If the object we need exists, then store the ID in post meta and return it.
		if( !empty( $response->og_object ) ) {
			update_post_meta( $post_id, '_facebook_og_id', $response->og_object->id );
			return $response->og_object->id;
		}

		// If the call failed, store a timestamp and return false.
		update_post_meta( $post_id, '_facebook_og_id_timestamp', time() );
		return false;
	}


	/**
	 * The parse_api_response() method parses the raw response from the API and
	 * returns the share count as an integer.
	 *
	 * In the case here for Facebook, it will json_decode the response and then
	 * look for and return the $response->engagement properties.
	 *
	 * @since  1.0.0 | 06 APR 2018 | Created
	 * @since  3.6.0 | 22 APR 2019 | Updated to parse API v.3.2.
	 * @since  4.0.0 | 03 DEC 2019 | Updated to parse API v.3.2 without token.
	 * @since  4.1.0 | 18 APR 2020 | Updated to parse API v.6.0.
	 * @since  4.1.0 | 21 JUL 2020 | Updated to parse API v.7.0.
	 *                               Added authenticated API to core.
	 *                               Added checking for expired tokens.
	 * @access public
	 * @param  string  $response The raw response returned from the API request
	 * @return integer The number of shares reported from the API. 0 on failure.
	 *
	 */
	public function parse_api_response( $response ) {


		/**
		 * This is the response that came back from Facebook's server/API. Since
		 * this response is JSON encoded, we'll decode it into a generic PHP
		 * object so that we can access it's properties.
		 *
		 */
		$response = json_decode( $response );


		/**
		 * This will catch the error code whenever Facebook responds by telling
		 * us that the user's access token has expired. If so, we'll update the
		 * access token to the value "expired" so that the rest of the plugin
		 * can take action to fix it. The plugin will then stop using the access
		 * token and will display a notice to the user telling them to update
		 * their authentication.
		 *
		 */
		if( !empty( $response->error ) && $response->error->code == 190 ) {
			SWP_Credential_Helper::store_data('facebook', 'access_token', 'expired' );
			return 0;
		}


		/**
		 * This will parse the currently formatted responses from the API as of
		 * May 28, 2021. This will grab the engagement counts object which contains
		 * an integer for reaction_count, comment_count, share_count, and
		 * comment_plugin_count. This will simply loop through them and add them
		 * all into a single integer.
		 *
		 * @since  4.3.0 | 28 MAY 2021 | Added this entire block of code.
		 */
		$engagement = 0;

		// Check if the engagement object exists in this response.
		if( !empty( $response->engagement ) ) {

			// Loop through each item in that response.
			foreach( $response->engagement as $this_engagement ) {

				// Ensure that the response is valid.
				if( is_numeric( $this_engagement ) ) {

					// Add the response to our ongoing total.
					$engagement += $this_engagement;
				}
			}

			// Return the total.
			return $engagement;
		}


		/**
		 * I don't think this method is used anymore and it probably needs to be
		 * removed. In the mean time, if it does detect the presence of these
		 * fields, it will know what to do with them.
		 *
		 */
		if( !empty( $response->og_object ) && !empty( $response->og_object->engagement ) ) {
			return $response->og_object->engagement->count;
		}


		/**
		 * This API returns the numbers as their individual parts: reactions,
		 * comments, and shares. We'll add these numbers together before
		 * returning the count to the caller.
		 *
		 */
		if( !empty( $response->engagement ) ) {
			$engagement = $response->engagement;
			$activity = $engagement->reaction_count + $engagement->comment_count + $engagement->share_count;
			return $activity;
		}

		// Return 0 if no valid counts were able to be extracted.
		return 0;
	}


	/**
	 * ATTENTION! ATTENTION! ATTENTION! ATTENTION! ATTENTION! ATTENTION! ATTENTION!
	 *
	 * All of the methods below this point are used for the client-side,
	 * Javascript share count fetching. Since Facebook has implemented some
	 * rather rigerous rate limits on their non-authenticated API, many
	 * server-side use cases are reaching these rate limits somewhat rapidly and
	 * spend as much time "down" as they do "up". This results in huge delays to
	 * getting share count numbers.
	 *
	 * As such, we have moved the share counts to the client side and we fetch
	 * those counts via javascript/jQuery. Now, instead of having 100 API hits
	 * being counted against the server's IP address, it will be counted against
	 * 100 different client/browser IP addresses. This should provide a virtually
	 * unlimited access to the non-authenticated API.
	 *
	 * You will also notice that these processes are conditonal on the plugin not
	 * being connected to Facebook. If the user has connected the plugin to Facebook,
	 * then we will simply use the authenticated API instead from the server.
	 *
	 */


	/**
	 * Register Cache Processes
	 *
	 * This method registered the processes that will need to be run during the
	 * cache rebuild process. The new caching class (codenames neo-advanced cache
	 * method) allows us to hook in functions that will run during the cache
	 * rebuild process by hooking into the swp_cache_rebuild hook.
	 *
	 * @since  3.1.0 | 26 JUN 2018 | Created
	 * @param  void
	 * @return void
	 *
	 */
	private function register_ajax_cache_callbacks() {

		if( false === $this->is_active() || $this->Authentication->has_valid_token() ) {
			return;
		}

		add_action( 'swp_cache_rebuild', array( $this, 'add_facebook_footer_hook' ), 10, 1 );
		add_action( 'wp_ajax_swp_facebook_shares_update', array( $this, 'facebook_shares_update' ) );
		add_action( 'wp_ajax_nopriv_swp_facebook_shares_update', array( $this, 'facebook_shares_update' ) );
	}


	/**
	 * A function to add the Facebook updater to the footer hook.
	 *
	 * This is a standalone method because we only want to hook into the footer
	 * and display the script during the cache rebuild process.
	 *
	 * @since  3.1.0 | 25 JUN 2018 | Created
	 * @param  void
	 * @return void
	 *
	 */
	public function add_facebook_footer_hook( $post_id ) {
        $this->post_id = $post_id;
		add_action( 'swp_footer_scripts', array( $this, 'print_facebook_script' ) );
	}


	/**
	 * Output the AJAX/JS for updating Facebook share counts.
	 *
	 * @since  3.1.0 | 25 JUN 2018 | Created
	 * @param  void
	 * @return void Output is printed directly to the screen.
	 *
	 */
	public function print_facebook_script( $info ) {

		if ( true === SWP_Utility::get_option( 'recover_shares' ) ) {
			$alternateURL = SWP_Permalink::get_alt_permalink( $this->post_id );
		} else {
			$alternateURL = false;
		}

		$info['footer_output'] .= PHP_EOL .  '
			document.addEventListener("DOMContentLoaded", function() {
				var swpButtonsExist = document.getElementsByClassName( "swp_social_panel" ).length > 0;
				if (swpButtonsExist) {
					swp_admin_ajax = "' . admin_url( 'admin-ajax.php' ) . '";
					swp_post_id=' . (int) $this->post_id . ';
					swp_post_url= "' . get_permalink() . '";
					swp_post_recovery_url = "' . $alternateURL . '";
					socialWarfare.fetchFacebookShares();
				}
			});
		';

		return $info;
	}


	/**
	 * Process the Facebook shares response via admin-ajax.php.
	 *
	 * The object will be instantiated by the Cache_Loader class and it will
	 * then call this method from there.
	 *
	 * @since  3.1.0 | 25 JUN 2018 | Created
	 * @param  void
	 * @return void
	 *
	 */
	public function facebook_shares_update() {
		global $swp_user_options;


		/**
		 * Verify that the data being submitted and later sent to the database
		 * are actual numbers. This ensures that it's the data type that we
		 * need, but it also prevents any kind of malicious code from being used.
		 *
		 */
		if (!is_numeric( $_POST['share_counts'] ) || !is_numeric( $_POST['post_id'] ) ) {
			echo 'Invalid data types sent to the server. No information processed.';
			wp_die();
		}

		// Cast them to integers just in case they come in as numeric strings.
		$activity = (int) $_POST['share_counts'];
		$post_id  = (int) $_POST['post_id'];


		/**
		 * We will attempt to update the share counts. If the new numbers are
		 * higher than the old numbers, it will return true. If not, it will
		 * return false. Either way, we'll echo a message so that we can know
		 * what happened.
		 *
		 */
		if ( true === $this->update_share_count( $post_id, $activity ) ) {
			$this->update_total_counts( $post_id );
			echo 'Facebook Shares Updated: ' . $activity;
		} else {
			$previous_activity = get_post_meta( $post_id, '_facebook_shares', true );
			echo "Facebook share counts not updated. New counts ($activity) is not higher than previously saved counts ($previous_activity)";
		}

		wp_die();
	}


	/**
	 * The display_access_token_notices() method is designed to imform the user
	 * as to the status of their Facebook authentication with the plugin.
	 *
	 * 1. If the plugin has not been authenticated with Facebook, it will inform
	 * the user of the benefits and encourage them to do so.
	 *
	 * 2. If the plugin has been authenticated, but it has expired, it will
	 * inform them and encourage them to authenticate it again.
	 *
	 * @since  4.1.0 | 22 JUL 2020 | Created
	 * @param  void
	 * @return void
	 *
	 */
	public function display_access_token_notices() {
		$is_notice_needed      = false;

		// If there is no token.
		if( false === $this->Authentication->get_access_token() ) {
			$is_notice_needed = true;
			$notice_key       = 'facebook_not_authenticated';
			$notice_message   = '<b>Notice: Facebook is not authenticated with Social Warfare.</b> We\'ve added the ability to authenticate and connect Social Warfare with Facebook. This allows us access to their official API which we use for collecting more accurate share counts. Just go to the Social Warfare Option Page, select the "Social Identity" tab, then scoll down to the "Social Network Connections" section and get yourself set up now!';
		}

		// If the token is expired.
		if( 'expired' === $this->Authentication->get_access_token() ) {
			$is_notice_needed = true;
			$notice_key       = 'fb_token_expired_' . date('MY') ;
			$notice_message   = '<b>Notice: Social Warfare\'s connection with Facebook has expired!</b> This happens by Facebook\'s design every couple of months. To give our plugin access to the most accurate, reliable and up-to-date data that we\'ll use to populate your share counts, just go to the Social Warfare Option Page, select the "Social Identity" tab, then scoll down to the "Social Network Collections" section and get yourself set up now!<br /><br />P.S. We do NOT collect any of your data from the API to our servers or share it with any third parties. Absolutely None.';
		}

		// If a message was generated above, send it to the notice class.
		if( true === $is_notice_needed ) {
			new SWP_Notice( $notice_key, $notice_message );
		}
	}

}
