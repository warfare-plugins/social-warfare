<?php
/**
 * Depreacted functions which should no longer be used and will be removed in
 * a later release.
 *
 * @package   SocialWarfare\Functions
 * @copyright Copyright (c) 2016, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     2.1.0
 */

/**
 * Deprecated function. Was originally used to pass variables to the Pinterest
 * button script.
 *
 * @since  unknown
 * @access public
 * @param  array $info An array of footer script information.
 * @return array $info A modified array of footer script information.
 */
function swp_pinit( $info ) {
	_deprecated_function( 'swp_pinit', '2.1.0' );

	return $info;
}
