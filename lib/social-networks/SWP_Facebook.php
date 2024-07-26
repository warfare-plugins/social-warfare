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
 */
class SWP_Facebook extends SWP_Social_Network {

	/**
	 * Indicates whether share counts are shown. This is specific to each social network.
	 *
	 * @var boolean
	 */
	protected $are_shares_shown;

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
	 */
	public function __construct() {

		// Update the class properties for this network
		$this->name             = esc_html__( 'Facebook', 'social-warfare' );
		$this->cta              = esc_html__( 'Share', 'social-warfare' );
		$this->key              = 'facebook';
		$this->default          = 'true';
		$this->base_share_url   = 'https://www.facebook.com/share.php?u=';
		$this->are_shares_shown = false;

		$this->init_social_network();
	}
}
