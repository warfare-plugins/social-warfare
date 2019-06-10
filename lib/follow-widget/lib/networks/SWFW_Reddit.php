<?php

/**
 * SWFW_Reddit
 *
 * This provides an interface for creating a follow button for Reddit.
 *
 * @package   social-follow-widget
 * @copyright Copyright (c) 2019, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     1.0.0 | Created
 *
 */
class SWFW_Reddit extends SWFW_Follow_Network {


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
			'key' => 'reddit',
			'name' => 'Reddit',
			'cta' => 'Follow',
			'follow_description' => 'Followers',
			'color_primary' => '#EF4A23',
			'color_accent' => '#D33F27',
			'url'	=> 'https://reddit.com/user/swfw_username'
		);

		parent::__construct( $network );
	}


	public function do_api_request() {
		$this->response = false;
	}


	/**
	 * Reddit-specific response handling.
	 *
	 * @since 1.0.0 | 15 JAN 2019 | Created.
	 * @param void
	 * @return int 0 This network does not offer count data.
	 *
	 */
	public function parse_api_response() {
		$this->follow_count = 0;
	}
}
