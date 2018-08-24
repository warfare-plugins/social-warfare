<?php

/**
 * SWP_Sidebar_Loader
 *
 * This pulls in the sidebar component JSON from our server and displays it as HTML.
 *
 * We can cachce the HTML so as not to make an excessive number of ajax calls.
 *
 * @package   SocialWarfare\Functions\Social-Networks
 * @copyright Copyright (c) 2018, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since  3.3.0 | 03 AUG 2018 | Created.
 *
 */
class SWP_Sidebar_Loader {


	/**
	 * Instantiate the class.
	 *
	 * @since  3.3.0 | 03 AUG 2018 | Created.
	 * @param  void
	 * @return void
	 *
	 */
    public function __construct() {
		$this->load_components();
    }


	/**
	 * Activate notices created via our remote JSON file.
	 *
	 * @since  3.1.0 | 27 JUN 2018 | Created
	 * @param  void
	 * @return void
	 *
	 */
	private function load_components() {
		$cache_data = get_option('swp_json_cache');

		if( false === $cache_data ):
			return;
		endif;

		if( !is_array( $cache_data ) || empty($cache_data['sidebar']) ):
			return;
		endif;

        add_filter( 'swp_admin_sidebar', function( $components ) {
            return array_merge( $components, $cache_data['sidebar'] );
        });
	}


	/**
	 * A function for debugging this class.
	 *
	 * All notices are stored in the $this->notices as an array of notice
	 * objects. Since this is the last method called, all notices should be
	 * present in the $this object for review.
	 *
	 * @since  3.1.0 | 28 JUN 2018 | Created
	 * @param  void
	 * @return void
	 *
	 */
	private function debug() {
		if( true === SWP_Utility::debug( 'notices' ) ):
			var_dump($this);
		endif;
	}

}
