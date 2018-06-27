<?php

/**
 * SWP_Notice_Loader
 *
 * This is where we define all the messages, CTAs, and scheudling for each notice.
 * It is fine to bloat the method with as many notices as necessary.
 *
 * @package   SocialWarfare\Functions\Social-Networks
 * @copyright Copyright (c) 2018, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since  3.0.9  | 09 JUN 2018 | Created the class.
 * @since  3.0.10 | 27 JUN 2018 | Break each notice into it's own method.
 *
 */
class SWP_Notice_Loader {


	/**
	 * Instantiate the class.
	 *
	 * The constructor will call up the methods that create each of the various
	 * notices throughout the plugin.
	 *
	 * @since  3.0.9  | 09 JUN 2018 | Created.
	 * @since  3.0.10 | 27 JUN 2018 | Updated to use separate methods per notice.
	 * @see    SWP_Notice.php
	 * @param  void
	 * @return void
	 *
	 */
    public function __construct() {
        $this->announce_stumble_upon_closing();
		$this->activate_json_notices();
    }


    /**
     * "StumbleUpon is shutting down."
     *
     * The Message announcing that StumbleUpon is shutting down at the end of
     * June, 2018.
     *
     * @since  3.0.9  | 09 JUN 2018 | Created the method.
     * @since  3.0.10 | 27 JUN 2018 | Renamed to allow each method to make one notice.
     * @param  void
     * @return void
     *
     */
    private function announce_stumble_upon_closing() {
        $message = 'As of June 30th, 2018, StumbleUpon will no longer exist as a sharing platform. Instead, they are moving in with Mix. While this is exciting for Mix, <b>share counts will not be transferred, and Mix is not providing a share button or API. </b> You can read more about it <a href="https://help.stumbleupon.com/customer/en/portal/articles/2908172-transitioning-from-stumbleupon-to-mix" target="_blank">here</a>.';

        $notice = new SWP_Notice( 'stumble_upon_closed', $message, 'notice-info' );
    }


	/**
	 * Activate notices created via our remote JSON file.
	 *
	 * @since  3.1.0 | 27 JUN 2018 | Created
	 * @param  void
	 * @return void
	 * 
	 */
	private function activate_json_notices() {

	}
}
