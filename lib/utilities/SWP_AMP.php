<?php
// TODO: Document
class SWP_AMP {

	public static function is_amp() {
		if ( function_exists( 'ampforwp_is_amp_endpoint' ) && ampforwp_is_amp_endpoint() ) {
			return true;
		}

		if (function_exists( 'is_amp_endpoint' ) && is_amp_endpoint()) {
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
