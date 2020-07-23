<?php

/**
 * The SWP_AMP class is designed to do all of the heavy lifting in allowing us
 * to quickly show or hide things depending on whether or not a post/page is
 * currently being served via an AMP endpoint.
 *
 * Since there are multiple AMP plugins out there, there are also multiple
 * conditionals that exist in the wild. As such, this will house all of those
 * conditionals so that when a new one is discovered, it can be added here
 * instead of having to be added everywhere throughout the plugin where the
 * conditional is actually needed.
 *
 * @since 4.0.2 | 23 JUL 2020 | Created
 */
class SWP_AMP {


	/**
	 * The is_amp() method is a public static method that will house all of the
	 * conditionals needed to determine whether or not a page/post is being
	 * served up via an AMP endpoint. This method will return a simple true/false
	 * based on that.
	 *
	 * Each of the conditionals below are used by different plugins to determine
	 * if their plugin is currently on an AMP endpoint or not.
	 *
	 * @since  4.0.2 | 23 JUL 2020 | Created
	 * @param  void
	 * @return boolean True if AMP endpoint; False if not.
	 *
	 */
	public static function is_amp() {
		if( function_exists( 'ampforwp_is_amp_endpoint' ) && ampforwp_is_amp_endpoint() ) {
			return true;
		}

		if( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) {
		    return true;
		}

		return false;
	}

	public static function hide_if_amp( $text, $fallback = '' ) {
		if ( self::is_amp() ) {
			return $fallback;
		}
		return $text;
	}

	public static function display_if_amp( $text, $fallback = '' ) {
		if ( false === self::is_amp() ) {
			return $fallback;
		}
		return $text;
	}

}
