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

/**
 * A Function to store the registration code
 *
 * @return void
 */
function swp_store_the_registration() {
	_deprecated_function( 'swp_store_the_registration', '2.1.0' );
}

/**
 * A Function to delete the registration code
 *
 * @return void
 */
function swp_delete_the_registration() {
	_deprecated_function( 'swp_delete_the_registration', '2.1.0' );
}

/**
 * Unused admin notice.
 *
 * @return void
 */
function swp_admin_notice() {
	_deprecated_function( 'swp_admin_notice', '2.1.0' );

	if ( ! is_swp_registered() ) {
		echo '<div class="notice is-dismissable swp_register_admin_notice"><p>Your copy of Social Warfare is not registered. Navigate to the <a href="/wp-admin/admin.php?page=social-warfare"><b>Social Warfare Settings Page</b></a> and select the "Register" tab to register now! You can view and manage your purchased licences on the <a target="_blank" href="https://warfareplugins.com/my-account/">My Account</a> page of the Warfare Plugins website. If you have any issues, please contact us and we\'ll be happy to help.</p></div>';
	}
}

/**
 * English
 *
 * @param  array $language Deprecated language settings.
 * @return array $language
 */
function swp_en_language( $language ) {
	_deprecated_function( 'swp_en_language', '2.1.0' );
	return $language;
}


/**
 * German
 *
 * @param  array $language Deprecated language settings.
 * @return array $language
 */
function swp_de_language( $language ) {
	_deprecated_function( 'swp_de_language', '2.1.0' );
	return $language;
}

/**
 * Russian
 *
 * @param  array $language Deprecated language settings.
 * @return array $language
 */
function swp_ru_language( $language ) {
	_deprecated_function( 'swp_ru_language', '2.1.0' );
	return $language;
}

/**
 * Ukrainian
 *
 * @param  array $language Deprecated language settings.
 * @return array $language
 */
function swp_uk_language( $language ) {
	_deprecated_function( 'swp_uk_language', '2.1.0' );
	return $language;
}

/**
 * Dutch
 *
 * @param  array $language Deprecated language settings.
 * @return array $language
 */
function swp_nl_language( $language ) {
	_deprecated_function( 'swp_nl_language', '2.1.0' );
	return $language;
}

/**
 * French
 *
 * @param  array $language Deprecated language settings.
 * @return array $language
 */
function swp_fr_language( $language ) {
	_deprecated_function( 'swp_fr_language', '2.1.0' );
	return $language;
}

/**
 * Portuguese
 *
 * @param  array $language Deprecated language settings.
 * @return array $language
 */
function swp_pt_language( $language ) {
	_deprecated_function( 'swp_pt_language', '2.1.0' );
	return $language;
}

/**
 * Danish
 *
 * @param  array $language Deprecated language settings.
 * @return array $language
 */
function swp_da_language( $language ) {
	_deprecated_function( 'swp_da_language', '2.1.0' );
	return $language;
}

/**
 * Italian
 *
 * @param  array $language Deprecated language settings.
 * @return array $language
 */
function swp_it_language( $language ) {
	_deprecated_function( 'swp_it_language', '2.1.0' );
	return $language;
}
