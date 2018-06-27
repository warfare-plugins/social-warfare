<?php

/**
 * A Class for Fetching Remote JSON data and caching it in a manner that will
 * easily allow other classes to access the data for the purpose of generating
 * notices, updating the sidebar, etc.
 *
 * @package   SocialWarfare\Functions
 * @copyright Copyright (c) 2018, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     3.1.0 | 27 JUN 2018 | Created
 *
 */
class SWP_JSON_Cache_Handler {


	__construct() {

		if( false === $this->is_cache_fresh() ):
			$this->fetch_json_response();
			$this->cache_json_response();
		endif;
	}

	private function fetch_json_response() {
		$this->response = wp_remote_retreive_body('link_to_our_json_file');
	}

	private function cache_json_response() {
		// Store the response in an options object with an expiration timestamp.
		// If no response, store somethine to make sure that we have a cache to
		// check so that we don't keep loading the json file over and over.
	}

	private function is_cache_fresh() {
		// Check the global options object to see how long its been since the 
		// last time we pinged the JSON file for a response.
	}

}
