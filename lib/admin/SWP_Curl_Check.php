<?php
/**
 * A class to detect if the user has cURL enabled on their server. cURL is a requirement of the plugin.
 *
 * @since  2.2.4 | Created | 1 MAY 2017
 * @access public
 * @return string The HTML for an error notice if triggered
 */
class SWP_Curl_Check extends SWP_Custom_Check {

	public function __construct() {
		$this->name = 'Curl Check';
	}

	public function run() {

		if ( function_exists( 'curl_version' ) ) {
			$this->check_passed = true;
		} else {
			$this->check_passed = false;
			$this->whats_wrong  = 'Your server has cURL disabled. In order for our plugin to fetch share counts, you must have cURL enabled on your server.';
			$this->how_to_fix   = 'To fix this, simply contact your hosting provider and ask them to activate cURL on your server.';
		}

		return $this->check_passed;
	}
}
