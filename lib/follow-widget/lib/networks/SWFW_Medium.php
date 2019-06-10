<?php

/**
 * SWFW_Medium
 *
 * This provides an interface for creating a follow button for Medium.
 *
 * @package   social-follow-widget
 * @copyright Copyright (c) 2019, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     1.0.0 | Created
 *
 */
class SWFW_Medium extends SWFW_Follow_Network {


	/**
	 * Applies network-specific data to the SWFW_Follow_Network
	 *
	 * @since 1.0.0 | 03 DEC 2018 | Created.
	 * @see SWFW_Follow_Network
	 * @param void
	 * @return void
	 */
	public function __construct() {
		$network = array(
			'key' => 'medium',
			'name' => 'Medium',
			'cta' => 'Follow',
			'follow_description' => 'Followers',
			'color_primary' => '#00ab6c',
			'color_accent' => '#fff',
			'url'	=> 'https://medium.com/swfw_username',
			'needs_authorization' => false
		);

		parent::__construct( $network );
	}


	/**
	 * This network does not have a follow API, so it must return false.
	 *
	 * @since 1.0.0 | 12 FEB 2019 | Created.
	 * @param void
	 * @return void;
	 *
	 */
	public function do_api_request() {
		return false;
	}


	/**
	 * This network does not have a follow API, so it must return 0.
	 *
	 * @since 1.0.0 | 12 FEB 2019 | Created.
	 * @param void
	 * @return string 0
	 *
	 */
	public function parse_api_response() {
		return '0';
	}
}
