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
 */
function swp_store_the_registration() {
	_deprecated_function( 'swp_store_the_registration', '2.1.0' );
}

/**
 * A Function to delete the registration code
 */
function swp_delete_the_registration() {
	_deprecated_function( 'swp_delete_the_registration', '2.1.0' );
}

/**
 * ADMIN NOTICE
 */
function swp_admin_notice() {
	if ( ! is_swp_registered() ) {
		echo '<div class="notice is-dismissable swp_register_admin_notice"><p>Your copy of Social Warfare is not registered. Navigate to the <a href="/wp-admin/admin.php?page=social-warfare"><b>Social Warfare Settings Page</b></a> and select the "Register" tab to register now! You can view and manage your purchased licences on the <a target="_blank" href="https://warfareplugins.com/my-account/">My Account</a> page of the Warfare Plugins website. If you have any issues, please contact us and we\'ll be happy to help.</p></div>';
	}
}

/**

 * *************************************************************************************
 *																						*
 *	Enqueue the Filters for the Available Languages										*
 *																						*
 *****************************************************************************************/

add_filter( 'swp_languages','swp_en_language',0 );

/**

 * *************************************************************************************
 *																						*
 *	English																				*
 *																						*
 *****************************************************************************************/

function swp_en_language( $language ) {

	$language['googlePlus'] 	= '+1';
	$language['twitter'] 		= 'Tweet';
	$language['facebook']		= 'Share';
	$language['pinterest']		= 'Pin';
	$language['linkedIn']		= 'Share';
	$language['tumblr']			= 'Share';
	$language['stumbleupon']	= 'Stumble';
	$language['reddit']	        = 'Reddit';
	$language['email']			= 'Email';
	$language['yummly']			= 'Yum';
	$language['whatsapp']		= 'WhatsApp';
	$language['pocket']			= 'Pocket';
	$language['buffer']			= 'Buffer';
	$language['total']			= 'Shares';

	// Return the Languages Array or the world will explode
	return $language;
}


/**

**************************************************************************************
*																						*
*	German																				*
*																						*
*/

// Add the terms to the buttons
add_filter( 'swp_languages','swp_de_language' );
function swp_de_language( $language ) {
	if ( swp_get_single_option( 'language' ) == 'de' ) :
		$language['googlePlus'] 	= '+1';
		$language['twitter'] 		= 'Twittern';
		$language['facebook']		= 'Teilen';
		$language['pinterest']		= 'Pin';
		$language['linkedIn']		= 'Teilen';
		$language['total']			= 'Alle Shares';
	endif;
	return $language;
}

/**

**************************************************************************************
*																						*
*	Russian																				*
*																						*
*/

// Add the terms to the buttons
add_filter( 'swp_languages','swp_ru_language' );
function swp_ru_language( $language ) {
	if ( swp_get_single_option( 'language' ) == 'ru' ) :
		$language['googlePlus'] 	= '+1';
		$language['twitter'] 		= 'Tвитнуть';
		$language['facebook']		= 'Поделиться';
		$language['pinterest']		= 'Pin';
		$language['linkedIn']		= 'Поделиться';
		$language['total']			= 'Поделились';
	endif;
	return $language;
}

/**

**************************************************************************************
*																						*
*	Ukrainian																			*
*																						*
*/

// Add the terms to the buttons
add_filter( 'swp_languages','swp_uk_language' );
function swp_uk_language( $language ) {
	if ( swp_get_single_option( 'language' ) == 'uk' ) :
		$language['googlePlus'] 	= '+1';
		$language['twitter'] 		= 'Tвітнути';
		$language['facebook']		= 'Поділитися';
		$language['pinterest']		= 'Pin';
		$language['linkedIn']		= 'Поділитися';
		$language['total']			= 'Поділилися';
	endif;
	return $language;
}

/**

**************************************************************************************
*																						*
*	Dutch																				*
*																						*
*/

// Add the terms to the buttons
add_filter( 'swp_languages','swp_nl_language' );
function swp_nl_language( $language ) {
	if ( swp_get_single_option( 'language' ) == 'nl' ) :
		$language['googlePlus'] 	= '+1';
		$language['twitter'] 		= 'Twitteren';
		$language['facebook']		= 'Delen';
		$language['pinterest']		= 'Pin';
		$language['linkedIn']		= 'Delen';
		$language['total']			= 'Alle Shares';
	endif;
	return $language;
}

/**

**************************************************************************************
*																						*
*	French																				*
*																						*
*/

// Add the terms to the buttons
add_filter( 'swp_languages','swp_fr_language' );
function swp_fr_language( $language ) {
	if ( swp_get_single_option( 'language' ) == 'fr' ) :
		$language['googlePlus'] 	= '+1';
		$language['twitter'] 		= 'Tweetez';
		$language['facebook']		= 'Partagez';
		$language['pinterest']		= 'Pin';
		$language['linkedIn']		= 'Partagez';
		$language['total']			= 'Partages';
	endif;
	return $language;
}

/**

**************************************************************************************
*																						*
*	Portuguese																			*
*																						*
*/

// Add the terms to the buttons
add_filter( 'swp_languages','swp_pt_language' );
function swp_pt_language( $language ) {
	if ( swp_get_single_option( 'language' ) == 'pt' ) :
		$language['googlePlus'] 	= '+1';
		$language['twitter'] 		= 'Tweetar';
		$language['facebook']		= 'Partilhar';
		$language['pinterest']		= 'Pin';
		$language['linkedIn']		= 'Partilhar';
		$language['tumblr']			= 'Partilhar';
		$language['stumbleupon']	= 'Stumble';
		$language['reddit']	        = 'Reddit';
		$language['email']			= 'Enviar e-mail';
		$language['yummly']			= 'Yum';
		$language['whatsapp']		= 'WhatsApp';
		$language['total']			= 'Total de partilhas';
	endif;
	return $language;
}

/**

**************************************************************************************
*																						*
*	Danish																				*
*																						*
*/
// Add the terms to the buttons
add_filter( 'swp_languages','swp_da_language' );
function swp_da_language( $language ) {
	if ( swp_get_single_option( 'language' ) == 'da' ) :
		$language['googlePlus'] 	= '+1';
		$language['twitter'] 		= 'Tweet';
		$language['facebook']		= 'Del';
		$language['pinterest']		= 'Pin';
		$language['linkedIn']		= 'Del';
		$language['tumblr']			= 'Del';
		$language['stumbleupon']	= 'Stumble';
		$language['reddit']	        = 'Reddit';
		$language['email']			= 'E-mail';
		$language['yummly']			= 'Yum';
		$language['whatsapp']		= 'WhatsApp';
		$language['total']			= 'Delinger i alt';
	endif;
	return $language;
}

/**

**************************************************************************************
*																						*
*	Italian 																			*
*																						*
*/
// Add the terms to the buttons
add_filter( 'swp_languages','swp_it_language' );
function swp_it_language( $language ) {
	if ( swp_get_single_option( 'language' ) == 'it' ) :
		$language['googlePlus'] 	= '+1';
		$language['twitter'] 		= 'Twitta';
		$language['facebook']		= 'Condividi';
		$language['pinterest']		= 'Pin';
		$language['linkedIn']		= 'Condividi';
		$language['tumblr']			= 'Condividi';
		$language['stumbleupon']	= 'Stumble';
		$language['reddit']			= 'Reddit';
		$language['email']			= 'Email';
		$language['yummly']			= 'Yum';
		$language['whatsapp']		= 'WhatsApp';
		$language['pocket']			= 'Pocket';
		$language['buffer']			= 'Buffer';
		$language['total']			= 'Condivisioni';
	endif;
	return $language;
}
