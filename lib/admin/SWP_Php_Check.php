<?php
/**
 * A class to detect if the user is using a compatible version of PHP (5.3+) on their server
 *
 * @since  2.2.4 | Created | 1 MAY 2017
 * @access public
 * @return string The HTML for an error notice if triggered
 */
class SWP_Php_Check extends SWP_Custom_Check {

	public function __construct() {
		$this->name = 'PHP Check';
	}

	public function run() {
		if ( version_compare( PHP_VERSION, '5.2.0' ) >= 0 ) {
			$this->check_passed = true;
		} else {
			$this->check_passed = false;
			$this->whats_wrong  = 'Your server is currently using PHP version ' . PHP_VERSION . '. In order for our plugin to fetch share counts properly, you must be using PHP 5.3 or newer.';
			$this->how_to_fix   = 'To fix this, simply contact your hosting provider and ask them to update your server to the latest stable version of PHP.';
		}

		return $this->check_passed;
	}
}
