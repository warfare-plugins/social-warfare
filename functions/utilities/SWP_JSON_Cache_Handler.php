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


	/**
	 * Instantiate the class object.
	 *
	 * Check if the cache is fresh, if not, ping the JSON file on our server,
	 * parse the results, and store them in an options field in the database.
	 *
	 * @since  3.1.0 | 28 JUN 2018 | Created
	 * @param  void
	 * @return void
	 *
	 */
	__construct() {

		if( false === $this->is_cache_fresh() ):
			$this->fetch_new_json_data();
		endif;
	}


	/**
	 * Fetch new JSON data.
	 *
	 * @since  3.1.0 | 28 JUN 2018 | Created
	 * @param  void
	 * @return void
	 *
	 */
	private function fetch_new_json_data() {

		// Fetch the response.
		$response = wp_remote_retreive_body('https://warfareplugins.com/json_updates.php');

		// Create the cache data array.
		$cache_data = array();

		if( !empty($response) ):
			$cache_data = json_decode( $this->response , true );
		endif;

		$cache_data['timestamp'] = time();

		// Store the data in the database.
		update_option('swp_json_cache' , $cache_data , true );

	}


	/**
	 * A method to determin if the cached data is still fresh.
	 *
	 * @since  3.1.0 | 28 JUN 2018 | Created
	 * @param  void
	 * @return boolean true if fresh, false if expired.
	 *
	 */
	private function is_cache_fresh() {

		$cache_data = get_option('swp_json_cache');

		// If no cached data, the cache is not fresh.
		if( false === $cache_data):
			return false;
		endif;

		// Forumlate the timestamps.
		$timestamp = $cache_data['timestamp'];
		$current_time = time();
		$time_between_checks = ( 6 * 60 * 60 )

		// Compare the timestamps.
		if($current_time > $timestamp + $time_between_checks ):
			return false;
		endif;

		return true;

	}

}
