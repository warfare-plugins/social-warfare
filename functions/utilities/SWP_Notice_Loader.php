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
 * @since  3.1.0 | 27 JUN 2018 | Break each notice into it's own method.
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
	 * @since  3.1.0 | 27 JUN 2018 | Updated to use separate methods per notice.
	 * @see    SWP_Notice.php
	 * @param  void
	 * @return void
	 *
	 */
    public function __construct() {
		$this->activate_json_notices();
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

        $notices = array_merge($cache_data['notices'], apply_filters( 'swp_admin_notices', array() ) );

		foreach( $notices as $data ) :
            if ( empty( $data['key'] ) || empty( $data['message'] ) ) {
                continue;
            }

            $ctas = !empty( $data['ctas'] ) ? $data['ctas'] : [];

            $notice = new SWP_Notice( $data['key'], $data['message'], $ctas );

            if ( isset( $data['start_date'] ) ) {
                $n->set_start_date( $data['start_date'] );
            }

            if ( isset( $data['end_date'] ) ) {
                $n->set_end_date( $data['end_date'] );
            }

            if ( isset( $data['no_cta'] ) ) {
                $n->remove_cta();
            }

			$this->notices[] = $notice;

		endforeach;
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
