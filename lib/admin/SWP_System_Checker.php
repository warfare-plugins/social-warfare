<?php
/**
 * A series of classes to check the user's system for minimum system requirements
 *
 * @since  2.2.4 | Created | 6 March 2017
 * @access public
 * @return string The HTML for an error notice if triggered
 */
class SWP_System_Checker {

	public static $custom_checks = array();
	public static $has_error     = false;

	public static function full_system_check() {
		SWP_System_Checker::load_all_checks();
		SWP_System_Checker::run_all_checks();
		SWP_System_Checker::print_all_errors();
	}

	public static function load_all_checks() {
		SWP_System_Checker::$custom_checks['SWP_Php_Check']  = new SWP_Php_Check();
		SWP_System_Checker::$custom_checks['SWP_Curl_Check'] = new SWP_Curl_Check();
	}

	public static function run_all_checks() {
		foreach ( SWP_System_Checker::$custom_checks as $custom_check ) {
			if ( method_exists( $custom_check, 'run' ) ) {
				if ( ! $custom_check->run() && ! $custom_check->check_passed ) {
					SWP_System_Checker::$has_error = true;
				}
			}
		}
	}

	public static function print_all_errors() {
		if ( ! isset( SWP_System_Checker::$has_error ) || empty( SWP_System_Checker::$has_error ) ) {
			return false;
		}

		foreach ( SWP_System_Checker::$custom_checks as $custom_check ) {
			if ( $custom_check->check_passed ) {
				continue;
			}

			echo '<div class="sw-red-notice">' . $custom_check->whats_wrong . $custom_check->how_to_fix . '</div>';
		}
	}
}
