<?php


/****************************************************************************************
*																						*
*	Enqueue the Filters for the Available Languages										*
*																						*
*****************************************************************************************/

add_filter('sw_languages','sw_en_language',0);

/****************************************************************************************
*																						*
*	English																				*
*																						*
*****************************************************************************************/

function sw_en_language($language) {
		
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
	$language['total']			= 'Total Shares';
	
	// Return the Languages Array or the world will explode
	return $language;
}


/****************************************************************************************
*																						*
*	German																				*
*																						*
*****************************************************************************************/

// Add the terms to the buttons
add_filter('sw_languages','sw_de_language');
function sw_de_language($language) {
	if(sw_get_single_option('language') == 'de'):
		$language['googlePlus'] 	= '+1';
		$language['twitter'] 		= 'Twittern';
		$language['facebook']		= 'Teilen';
		$language['pinterest']		= 'Pin';
		$language['linkedIn']		= 'Teilen';
		$language['total']			= 'Alle Shares';
	endif;
	return $language;
}

/****************************************************************************************
*																						*
*	Russian																				*
*																						*
*****************************************************************************************/

// Add the terms to the buttons
add_filter('sw_languages','sw_ru_language');
function sw_ru_language($language) {
	if(sw_get_single_option('language') == 'ru'):
		$language['googlePlus'] 	= '+1';
		$language['twitter'] 		= 'Tвитнуть';
		$language['facebook']		= 'Поделиться';
		$language['pinterest']		= 'Pin';
		$language['linkedIn']		= 'Поделиться';
		$language['total']			= 'Поделились';
	endif;
	return $language;
}

/****************************************************************************************
*																						*
*	Ukrainian																			*
*																						*
*****************************************************************************************/

// Add the terms to the buttons
add_filter('sw_languages','sw_uk_language');
function sw_uk_language($language) {
	if(sw_get_single_option('language') == 'uk'):
		$language['googlePlus'] 	= '+1';
		$language['twitter'] 		= 'Tвітнути';
		$language['facebook']		= 'Поділитися';
		$language['pinterest']		= 'Pin';
		$language['linkedIn']		= 'Поділитися';
		$language['total']			= 'Поділилися';
	endif;
	return $language;
}

/****************************************************************************************
*																						*
*	Dutch																				*
*																						*
*****************************************************************************************/

// Add the terms to the buttons
add_filter('sw_languages','sw_nl_language');
function sw_nl_language($language) {
	if(sw_get_single_option('language') == 'nl'):
		$language['googlePlus'] 	= '+1';
		$language['twitter'] 		= 'Twitteren';
		$language['facebook']		= 'Delen';
		$language['pinterest']		= 'Pin';
		$language['linkedIn']		= 'Delen';
		$language['total']			= 'Alle Shares';
	endif;
	return $language;
}

/****************************************************************************************
*																						*
*	French																				*
*																						*
*****************************************************************************************/

// Add the terms to the buttons
add_filter('sw_languages','sw_fr_language');
function sw_fr_language($language) {
	if(sw_get_single_option('language') == 'fr'):
		$language['googlePlus'] 	= '+1';
		$language['twitter'] 		= 'Tweetez';
		$language['facebook']		= 'Partagez';
		$language['pinterest']		= 'Pin';
		$language['linkedIn']		= 'Partagez';
		$language['total']			= 'Partages';
	endif;
	return $language;
}

/****************************************************************************************
*																						*
*	Portuguese																				*
*																						*
*****************************************************************************************/

// Add the terms to the buttons
add_filter('sw_languages','sw_pt_language');
function sw_pt_language($language) {
	if(sw_get_single_option('language') == 'pt'):
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