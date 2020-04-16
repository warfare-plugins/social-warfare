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
		 * This will check to see if the user has connected Social Warfare with
		 * Facebook using the oAuth authentication. If so, we'll use the offical
		 * authentication API to fetch share counts. If not, we'll use the open,
		 * unauthenticated API.
		 *
		 */
		$auth_helper = new SWP_Auth_Helper( $this->key );
		$this->access_token = $auth_helper->get_access_token();

		// This is the link that is clicked on to share an article to their network.
		$this->base_share_url = 'https://www.facebook.com/share.php?u=';

		$this->init_social_network();
		$this->register_ajax_cache_callbacks();
	}


	/**
	 * Generate the API Share Count Request URL
	 *
	 * @since  1.0.0 | 06 APR 2018 | Created
	 * @since  3.6.0 | 22 APR 2019 | Updated API to v3.2.
	 * @since  4.0.1 | 02 APR 2020 | Added access_token based API call.
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
		 * unauthenticated API.
		 *
		 */
		$auth_helper = new SWP_Auth_Helper( $this->key );
		$access_token = $auth_helper->get_access_token();

		if( $this->access_token ) {
			return 'https://graph.facebook.com/v6.0/?id='.$url.'&fields=og_object{engagement}&access_token='.$access_token;
		}
		return 0;
	}


	/**
	 * Parse the response to get the share count
	 *
	 * @since  1.0.0 | 06 APR 2018 | Created
	 * @since  3.6.0 | 22 APR 2019 | Updated to parse API v.3.2.
	 * @since  4.0.0 | 03 DEC 2019 | Updated to parse API v.3.2 without token.
	 * @access public
	 * @param  string  $response The raw response returned from the API request
	 * @return integer The number of shares reported from the API
	 *
	 */
	public function parse_api_response( $response ) {

		// Parse the response into a generic PHP object.
		$response = json_decode( $response );

		// Parse the response to get integers.
		if( !empty( $response->og_object ) && !empty( $response->og_object->engagement ) ) {
			return $response->og_object->engagement->count;
		}

		// Return 0 if no valid counts were able to be extracted.
		return 0;
	}


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
		if( false === $this->is_active() || $this->access_token ) {
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

		if (!is_numeric( $_POST['share_counts'] ) || !is_numeric( $_POST['post_id'] ) ) {
			echo 'Invalid data types sent to the server. No information processed.';
			wp_die();
		}

		$activity = (int) $_POST['share_counts'];
		$post_id  = (int) $_POST['post_id'];

		$previous_activity = get_post_meta( $post_id, '_facebook_shares', true );

		if ( $activity >= $previous_activity || true === SWP_Utility::debug('force_new_shares') ) :
			echo 'Facebook Shares Updated: ' . $activity;

			delete_post_meta( $post_id, '_facebook_shares' );
			update_post_meta( $post_id, '_facebook_shares', $activity );
		endif;

		wp_die();
	}

}
