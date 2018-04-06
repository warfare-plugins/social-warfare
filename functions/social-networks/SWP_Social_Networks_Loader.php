<?php

/**
 * A class to load up all of this plugin's social networks.
 *
 * The purpose of this class is to create a global social networks array and
 * then to load up and instantiate each of the social networks as objects into
 * that array.
 *
 * @since 3.0.0 | 05 APR 2018 | Created
 *
 */
class SWP_Social_Networks_Loader {

	public function __construct() {

		// Create a global array to contain our social network objects.
		global $swp_social_networks;
		$swp_social_networks = array();

		// Create the social network objects.
		new SWP_Google_Plus();
		new SWP_Facebook();

	}

}
