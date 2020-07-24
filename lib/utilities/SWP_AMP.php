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
 *
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
	 * Example Usage:
	 *
	 * if( true === SWP_AMP::is_amp() ) {
	 *     // Do something.
	 * }
	 *
	 * @since  4.0.2 | 23 JUL 2020 | Created
	 * @param  void
	 * @return boolean True if AMP endpoint; False if not.
	 *
	 */
	public static function is_amp() {


		/**
		 * This is used by the AMP for WP plugin.
		 *
		 * Name: AMP for WP - Accelerated Mobile Pages
		 * Link: https://wordpress.org/plugins/accelerated-mobile-pages/
		 * Documentation: https://ampforwp.com/tutorials/article/detect-amp-page-function/
		 *
		 */
		if( function_exists( 'ampforwp_is_amp_endpoint' ) && ampforwp_is_amp_endpoint() ) {
			return true;
		}


		/**
		 * This is used by the official AMP plugin.
		 *
		 * Name: AMP - Official AMP plugin for WordPress
		 * Link: https://wordpress.org/plugins/amp/
		 * Documention: https://amp-wp.org/documentation/developing-wordpress-amp-sites/how-to-develop-with-the-amp-plugin/
		 *
		 */
		if( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) {
		    return true;
		}

		return false;
	}


	/**
	 * The hide_if_amp() method will return the supplied string if the current
	 * page being rendered is not AMP. It will return an empty string (hence
	 * it will hide it) if it is being accessed via an AMP endpoint.
	 *
	 * Example Usage:
	 *
	 * $attribute = SWP_AMP::hide_if_amp( 'some_attribute' );
	 * $div = '<div ' . $attribute . '>';
	 *
	 * @since  4.0.2 | 24 JUL 2020 | Created
	 * @param  string $text     The text to be shown or hidden.
	 * @param  string $fallback An optional fallback to return instead of an empty string.
	 * @return string           The original text, an empty string, or a fallback string.
	 *
	 */
	public static function hide_if_amp( $text, $fallback = '' ) {

		// If this is an AMP endpoint, return the fallback text.
		if ( self::is_amp() ) {
			return $fallback;
		}

		// If this is NOT an AMP endpoint, return the supplied text.
		return $text;
	}


	/**
	 * The display_if_amp() method will return the supplied string if the current
	 * page being rendered is AMP. It will return an empty string if it is being
	 * accessed via a non-AMP endpoint.
	 *
	 * Example Usage:
	 *
	 * $attribute = SWP_AMP::display_if_amp( 'some_attribute' );
	 * $div = '<div ' . $attribute . '>';
	 *
	 * @since  4.0.2 | 24 JUL 2020 | Created
	 * @param  string $text     The text to be shown or hidden.
	 * @param  string $fallback An optional fallback to return instead of an empty string.
	 * @return string           The original text, an empty string, or a fallback string.
	 *
	 */
	public static function display_if_amp( $text, $fallback = '' ) {

		// If this is NOT an AMP endpoint, return the fallback text.
		if ( false === self::is_amp() ) {
			return $fallback;
		}

		// If this is an AMP endpoint, return the supplied text.
		return $text;
	}
}
