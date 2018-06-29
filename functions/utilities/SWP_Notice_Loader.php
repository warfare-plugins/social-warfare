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
		$this->activate_json_notices();
        $this->announce_stumble_upon_closing();
		$this->debug();
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
		$cache_data = get_option('swp_json_cache');

		if( false === $cache_data ):
			return;
		endif;

		if( !is_array( $cache_data ) || empty($cache_data['notices']) ):
			return;
		endif;

		foreach( $cache_data['notices'] as $notice ) :
            if ( empty( $notice['key'] ) || empty( $notice['message'] ) ) {
                continue;
            }

			$key     = $notice['key'];
			$message = $notice['message'];

            $n = new SWP_Notice( $key, $message );

            if ( !empty ( $notice['ctas'] ) ) {
                foreach( $notice['ctas'] as $cta) {
                    $action = $cta[0];
                    $link = '';
                    $class = '';
                    $timeframe = 0;

                    if ( isset( $cta[1] ) ) {
                        $link = $cta[1];
                    }

                    if ( isset( $cta[2] ) ) {
                        $class = $cta[2];
                    }

                    if ( isset( $cta[3] ) ) {
                        $timeframe = $cta[3];
                    }
                    $n->add_cta( $action, $link, $class, $timeframe );
                }
            }

			$this->notices[] = $n;

		endforeach;
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

        $this->notices[] = new SWP_Notice( 'stumble_upon_closed', $message, 'notice-info' );
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
		if( true === _swp_is_debug( 'notices' ) ):
			var_dump($this);
		endif;
	}

}
