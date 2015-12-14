<?php

	// Include the Admin Page Class files
	require_once('admin-page-class/admin-page-class.php');

/****************************************************************************************
*																						*
*	One Options Array to Rule Them All													*
*																						*
*****************************************************************************************/

	// Create all of the options in one giant array
	$sw_options = array(
	
		// The Options Page Configuration
		'config' => array(
			'menu'=> array('top' => 'social-warfare'),  	// sub page to settings page
			'page_title' => 'Social Warfare',   			// The name of this page
			'capability' => 'update_plugins',       		// The capability needed to view the page
			'option_group' => 'socialWarfareOptions',		// the name of the option to create in the database
			'id' => 'social-warfare',                		// Page id, unique per page
			'fields' => array(),                 			// list of fields (can be added by field arrays)
			'local_images' => false,             			// Use local or hosted images (meta box images for add/remove)
			'use_with_theme' => false
		),
		
		// A List of Options Page Tabs and Their Titles
		'tabs' => array(
			'links' => array(
			)
		),
		
		// A list of options in each of the options tabs
		'options' => array()
	);

/****************************************************************************************
*																						*
*	The Social Warfare Social Identity Settings												*
*																						*
*****************************************************************************************/
	function sw_options_social_identity($sw_options) {
		
		// Declare the Options Tab and Tab Name
		$sw_options['tabs']['links']['socialIdentity'] = 'Social Identity';
		
		// Declare the content that goes on this options page
		$sw_options['options']['socialIdentity'] = array(
			'twitterHandleDescription' => array(
				'type'		=> 'paragraph',
				'content'	=>	'<h3>Would you like to be mentioned in tweets?</h3><br />If so, please provide your Twitter username WITHOUT the @ symbol.'
			),
			'twitterID' => array(
				'type'		=> 'textbox',
				'content'	=> 'Twitter Username'
			),
			'facebookPublisherDescription' => array(
				'type'		=> 'paragraph',
				'content'	=>	'<h3>Would you like to activate Facebook Authorship?</h3><br />If so, please provide your Facebook page URL.'
			),
			'facebookPublisherUrl' => array(
				'type'		=> 'textbox',
				'content'	=> 'URL'
			),
			'pinterestUserDescription' => array(
				'type'		=> 'paragraph',
				'content'	=>	'<h3>Would you like to be mentioned in Pins?</h3><br />If so, please provide your Pinterest username WITHOUT the @ symbol.'
			),
			'pinterestID' => array(
				'type'		=> 'textbox',
				'content'	=> 'Pinterest Username'
			),

/****************************************************************************************
*																						*
*	The Social Warfare Display Settings													*
*																						*
*****************************************************************************************/
	function sw_options_display_settings($sw_options) {
		
		// Declare the Options Tab and Tab Name
		$sw_options['tabs']['links']['displaySettings'] = 'Display Settings';
		
		// Declare the content that goes on this options page
		$sw_options['options']['displaySettings'] = array(
			'displaySettingsTitle' => array(
				'type' 		=> 'title',
				'content' 	=> 'Display Settings'
			),
			'displaySettingsDescription' => array(
				'type'		=> 'paragraph',
				'content'	=> 'Welcome to Social Warfare! Get ready to dominate the world\'s social networks. This is the page where you\'ll select which social media icons to display, where to display them, and in what order.'
			),
			'language' => array(
				'type' => 'select',
				'name' => 'Language',
				'content' => array(
					'nl'	=> 'Dutch',
					'en' 	=> 'English',
					'fr' 	=> 'French',
					'de'	=> 'German',
					'ru'	=> 'Russian',
					'uk'	=> 'Ukrainian'
				),
				'default' => 'en'
			),
			'sw_twitter_card' => array(
				'type'		=> 'checkbox',
				'content'	=> 'Activate Twitter Cards?',
				'default'	=> 1
			),
			'iconDisplayInfo' => array(
				'type'		=> 'paragraph',
				'content'	=> '<h3>Which social media buttons would you like to display?</h3>'
			),
			'googlePlus' => array(
				'type'		=> 'checkbox',
				'content'	=> 'Google+',
				'default'	=> 1
			),
			'twitter' => array(
				'type'		=> 'checkbox',
				'content'	=> 'Twitter',
				'default'	=> 1
			),
			'facebook' => array(
				'type'		=> 'checkbox',
				'content'	=> 'Facebook',
				'default'	=> 1
			),
			'pinterest' => array(
				'type'		=> 'checkbox',
				'content'	=> 'Pinterest',
				'default'	=> 1
			),
			'linkedIn' => array(
				'type'		=> 'checkbox',
				'content'	=> 'LinkedIn',
				'default'	=> 1
			),
			'totes' => array(
				'type'		=> 'checkbox',
				'content'	=> 'Show Total Shares',
				'default'	=> 1
			),
			'totesEach' => array(
				'type'		=> 'checkbox',
				'content'	=> 'Show Individual Button Counts',
				'default'	=> 1
			),
			'minTotes' => array(
				'type'		=> 'textbox',
				'content'	=> 'Minimum Number of shares required before showing counts?',
				'default'	=> 0
			),
			'orderOfIconsDescription' => array(
				'type'		=> 'paragraph',
				'content'	=> '<h3>In what order should we place your share buttons?</h3><br />Simply drag and drop the social platforms to indicate the order in which you want them displayed.'
			),
			'orderOfIconsSelect' => array(
				'type' => 'select',
				'name' => 'Order of Icons',
				'content' => array(
					'manual'		=> 'Manually Order by My Preference',
					'dynamicCount' 	=> 'Dynamically Order Buttons by Share Counts',
				),
				'default' => 'manual'
			),
			'newOrderOfIcons' => array(
				'type'		=> 'sortable',
				'name'		=> 'Order Your Icons',
				'content'	=> array(
					'googlePlus'	=> 'Google+',
					'twitter'		=> 'Twitter',
					'facebook'		=> 'Facebook',
					'pinterest'		=> 'Pinterest',
					'linkedIn'		=> 'LinkedIn'
				)
			)
		);
		return $sw_options;
	}
/****************************************************************************************
*																						*
*	The Social Warfare Display Locations												*
*																						*
*****************************************************************************************/

	// A function to add display locations to the array
	function sw_options_display_locations($sw_options) {
		
		// Declare the Display Settings tab and tab name
		$sw_options['tabs']['links']['displayLocations'] = 'Display Locations';
		
		// Default locations available (ARRAY)
		$contentLocations = array(
			'above'=>'Above the Content',
			'below' => 'Below the Content', 
			'both' => 'Both Above and Below the Content', 
			'none' => 'None/Manual Placement'
		);
		
		// Add the options to this tab
		$sw_options['options']['displayLocations'] = array(
			'displayLocationsTitle' => array(
				'type' 		=> 'title',
				'content' 	=> 'Display Locations'
			),
			'displayLocationsDescription' => array(
				'type'		=> 'paragraph',
				'content'	=>	'<h3>Where would you like to display your horizontal share buttons?</h3>If you select \'None/Manual Placement\' you can add the social warfare plugin by adding \'social_warfare()\' to your themes files or shortcode [social_warfare] in posts or pages at the specific location that you want it to appear.'
			),
			'locationPost' => array(
				'type'		=> 'select',
				'name'		=> 'Location on Posts',
				'content'	=> $contentLocations,
				'default'	=> 'both'
			),
			'locationPage' => array(
				'type'		=> 'select',
				'name'		=> 'Location on Pages',
				'content'	=> $contentLocations,
				'default'	=> 'both'
			),
			'locationSite' => array(
				'type'		=> 'select',
				'name'		=> 'Location Sitewide',
				'content'	=> $contentLocations,
				'default'	=> 'both'
			)
		);
		
		// Get the post Types
		$postTypes = get_post_types();
		
		// Unset the post types that don't matter
		if(isset($postTypes['post'])) 				unset($postTypes['post']);
		if(isset($postTypes['page'])) 				unset($postTypes['page']);
		if(isset($postTypes['attachment'])) 		unset($postTypes['attachment']);
		if(isset($postTypes['revision'])) 			unset($postTypes['revision']);
		if(isset($postTypes['nav_menu_item'])) 		unset($postTypes['nav_menu_item']);
		if(isset($postTypes['nf_sub'])) 			unset($postTypes['nf_sub']);
		if(isset($postTypes['shop_order'])) 		unset($postTypes['shop_order']);
		if(isset($postTypes['shop_order_refund'])) 	unset($postTypes['shop_order_refund']);
		if(isset($postTypes['shop_coupon'])) 		unset($postTypes['shop_coupon']);
		if(isset($postTypes['shop_webhook'])) 		unset($postTypes['shop_webhook']);
		
		if(!empty($postTypes)):
			
			// Loop through the Custom Post Type Options
			foreach($postTypes as $postType):
				$sw_options['options']['displayLocations']['location'.$postType] = array(
					'type'		=> 'select',
					'name'		=> 'Location on '.$postType.' Posts',
					'content'	=> $contentLocations,
					'default'	=> 'both'					
				);
			endforeach;
		endif;
		
		$sw_options['options']['displayLocations']['floatLocationsDescription'] = array(
			'type'		=> 'paragraph',
			'content'	=>	'<h3>Custom Post Type Placements for Vertical Floating Buttons (If Activated)</h3>These are the the same options as above, but now you get to choose where you want the vertical floating buttons to be turned off or on.'
		);
		
		$sw_options['options']['displayLocations']['floatLocationPost'] = array(
			'type'		=> 'select',
			'name'		=> 'Location on Posts',
			'content'	=> array(
				'on'	=> 'On',
				'off'	=> 'Off',
			),
			'default'	=> 'both'
		);
		$sw_options['options']['displayLocations']['floatlocationPage'] = array(
			'type'		=> 'select',
			'name'		=> 'Location on Pages',
			'content'	=> array(
				'on'	=> 'On',
				'off'	=> 'Off',
			),
			'default'	=> 'both'
		);
		$sw_options['options']['displayLocations']['floatlocationSite'] = array(
			'type'		=> 'select',
			'name'		=> 'Location Sitewide',
			'content'	=> array(
				'on'	=> 'On',
				'off'	=> 'Off',
			),
			'default'	=> 'both'
		);
	
		if(!empty($postTypes)):
			
			// Loop through the Custom Post Type Options
			foreach($postTypes as $postType):
				$sw_options['options']['displayLocations']['floatLocation'.$postType] = array(
					'type'		=> 'select',
					'name'		=> 'Location on '.$postType.' Posts',
					'content'	=> array(
						'on'	=> 'On',
						'off'	=> 'Off',
					),
					'default'	=> 'On'					
				);
			endforeach;
		endif;
		
		// Return the options values
		return $sw_options;
		
	}
/****************************************************************************************
*																						*
*	The Social Warfare Visual Options													*
*																						*
*****************************************************************************************/

	function sw_options_visual_options($sw_options) {
					
		// Declare the Display Settings tab and tab name
		$sw_options['tabs']['links']['visualOptions'] = 'Visual Options';
	
		$sw_options['options']['visualOptions'] = array(
			'visualOptionsTitle' => array(
				'type' 		=> 'title',
				'content' 	=> 'Visual Options'
			),
			'visualTheme' => array(
				'type' => 'select',
				'name' => 'Button Shape',
				'content' => array(
					'flatFresh' 	=> 'Flat & Fresh',
					'leaf' 			=> 'A Leaf on the Wind',
					'shift' 		=> 'Shift',
					'pill' 			=> 'Pills',
					'threeDee' 		=> 'Three-Dee',
					'connected' 	=> 'Connected'
				),
				'default' => 'flatFresh'
			),
			'dColorSet' => array(
				'type' => 'select',
				'name' => 'Default Color Set',
				'content' => array(
					'fullColor' 		=> 'Full Color',
					'lightGray' 		=> 'Light Gray',
					'mediumGray'		=> 'Medium Gray',
					'darkGray' 			=> 'Dark Gray',
					'lgOutlines' 		=> 'Light Gray Outlines',
					'mdOutlines'		=> 'Medium Gray Outlines',
					'dgOutlines' 		=> 'Dark Gray Outlines',
					'colorOutlines' 	=> 'Color Outlines',
					'customColor' 		=> 'Custom Color',
					'ccOutlines' 		=> 'Custom Color Outlines'
				),
				'default' => 'fullColor'
			),
			'iColorSet' => array(
				'type' => 'select',
				'name' => 'Individual Hover Color Set',
				'content' => array(
					'fullColor' 		=> 'Full Color',
					'lightGray' 		=> 'Light Gray',
					'mediumGray'		=> 'Medium Gray',
					'darkGray' 			=> 'Dark Gray',
					'lgOutlines' 		=> 'Light Gray Outlines',
					'mdOutlines'		=> 'Medium Gray Outlines',
					'dgOutlines' 		=> 'Dark Gray Outlines',
					'colorOutlines' 	=> 'Color Outlines',
					'customColor' 		=> 'Custom Color',
					'ccOutlines' 		=> 'Custom Color Outlines'
				),
				'default' => 'fullColor'
			),
			'oColorSet' => array(
				'type' => 'select',
				'name' => 'Other Buttons Hover Color Set',
				'content' => array(
					'fullColor' 		=> 'Full Color',
					'lightGray' 		=> 'Light Gray',
					'mediumGray'		=> 'Medium Gray',
					'darkGray' 			=> 'Dark Gray',
					'lgOutlines' 		=> 'Light Gray Outlines',
					'mdOutlines'		=> 'Medium Gray Outlines',
					'dgOutlines' 		=> 'Dark Gray Outlines',
					'colorOutlines' 	=> 'Color Outlines',
					'customColor' 		=> 'Custom Color',
					'ccOutlines' 		=> 'Custom Color Outlines'
				),
				'default' => 'fullColor'
			),
			'buttonSize' => array(
				'type' => 'select',
				'name' => 'Button Size',
				'content' => array(
					'1.4' => '140%',
					'1.3' => '130%',
					'1.2' => '120%',
					'1.1' => '110%',
					'1'   => '100%',
					'0.9' => '90%',
					'0.8' => '80%',
					'0.7' => '70%'
				),
				'default' => '1'
			 ),
			'buttonFloat' => array(
				'type' => 'select',
				'name' => 'Button Alignment (When Scaled)',
				'content' => array(
					'fullWidth' => 'Full Width',
					'left' 		=> 'Left',
					'right'   	=> 'Right',
					'center' 	=> 'Center'
				),
				'default' => 'fullWidth'
			 ),
			'customColorDescription' => array(
				'type' => 'paragraph',
				'content' => '<h3 class="customColorLabel">Custom Color Selector</h3>Select your own custom color for the sharing buttons. You must have selected one of the "Custom Color" options in the "Color Set" drop down for this setting to have any effect. Please note that there is no dynamic preview above for the custom color setting...yet.'
			),
			'customColor' => array(
				'type' => 'colorselect',
				'name' => 'Custom Buttons Color',
				'default' => '#ffffff'
			),
			'buttonsPreview' => array(
				'type' => 'paragraph',
				'content' => '<div class="nc_socialPanel sw_flatFresh sw_d_fullColor sw_i_fullColor sw_o_fullColor" data-position="above" data-float="floatNone" data-count="6" data-floatColor="#ffffff" data-url="http://beta.warfareplugins.com/"><div class="nc_tweetContainer googlePlus" data-num="18" data-wid="45" data-id="1" data-count="0" ><a target="_blank" data-link="https://plus.google.com/share?url=http://beta.warfareplugins.com/%3Futm_source%3DSocialWarfare%26utm_medium%3Dplus.google.com%26utm_campaign%3DSocialWarfare" class="nc_tweet"><div class="iconFiller"><div class="spaceManWilly" style="width:46px;"><i class="sw sw-google-plus"></i><span class="sw_share"> +1</span></div></div><span class="sw_count">0</span></a></div><div class="nc_tweetContainer fb" data-num="40" data-wid="65" data-id="3" data-count="0" ><a target="_blank" data-link="http://www.facebook.com/share.php?u=http://beta.warfareplugins.com/%3Futm_source%3DSocialWarfare%26utm_medium%3Dfacebook.com%26utm_campaign%3DSocialWarfare" class="nc_tweet"><div class="iconFiller"><div class="spaceManWilly" style="width:65px;"><i class="sw sw-facebook"></i><span class="sw_share"> Share</span></div></div><span class="sw_count">0</span></a></div><div class="nc_tweetContainer nc_pinterest" data-num="25" data-wid="55" data-id="4" ><a data-link="//pinterest.com/pin/create/button/?url=http%3A%2F%2Fbeta.warfareplugins.com%2F%253Futm_source%253DSocialWarfare%2526utm_medium%253Dpinterest.com%2526utm_campaign%253DSocialWarfare&media=http%3A%2F%2Fbeta.warfareplugins.com%2Fwp-content%2Fuploads%2F2015%2F04%2Fsocialwarfare-735x1102.jpg&description=This+is+the+pin+for+%28Beta%29+Warfare+is+a+site+that+is+our+testing+ground+for+the+actual+site%2C+which+is+actually+live%2C+but+always+a+work+in+progress.+" class="nc_tweet" data-count="0"><div class="iconFiller"><div class="spaceManWilly" style="width:55px;"><i class="sw sw-pinterest"></i><span class="sw_share"> Pin</span></div></div><span class="sw_count">0</span></a></div><div class="nc_tweetContainer twitter" data-num="40" data-wid="70" data-id="2" data-count="0" ><a data-link="https://twitter.com/share?original_referer=/&text=Whoa%21+You+just+tweeted+about+the+Beta+site.+Not+cool+bro.+&url=http://beta.warfareplugins.com/%3Futm_source%3DSocialWarfare%26utm_medium%3Dtwitter.com%26utm_campaign%3DSocialWarfare" class="nc_tweet"><div class="iconFiller"><div class="spaceManWilly" style="width:70px;"><i class="sw sw-twitter"></i><span class="sw_share"> Tweet</span></div></div><span class="sw_count">0</span></a></div><div class="nc_tweetContainer linkedIn" data-num="41" data-wid="70" data-id="5" data-count="0" ><a data-link="https://www.linkedin.com/cws/share?url=http://beta.warfareplugins.com/%3Futm_source%3DSocialWarfare%26utm_medium%3Dlinkedin.com%26utm_campaign%3DSocialWarfare" class="nc_tweet"><div class="iconFiller"><div class="spaceManWilly" style="width:70px;"><i class="sw sw-linkedin"></i><span class="sw_share"> Share</span></div></div><span class="sw_count">0</span></a></div><div class="nc_tweetContainer totes totesalt" data-id="6" ><span class="sw_count"><span class="sw_label">TOTAL SHARES</span> 0</span></div></div>'
			),
			'decimalOptionDescription' => array(
				'type' => 'paragraph',
				'content' => 'Select the number of decimals to display on numbers greater than 1,000. Zero will display as 3K shares. One will display as 3.2K shares. Two will display as 3.24K Shares. We recommend one.'
			),
			'swDecimals' => array(
				'type' => 'select',
				'name' => 'Decimals',
				'content' => array(
					'0' => 'Zero',
					'1' => 'One',
					'2' => 'Two'
				),
				'default' => '0'
			 ),
			 'swTotesFormat' => array(
				'type' => 'select',
				'name' => 'Total Count Alignment',
				'content' => array(
					'totesAlt'		=>	'Right',
					'totesAltLeft'	=>	'Left'
				),
				'default' => 'totesAlt'
			)
		);
		
		// Return the options value
		return $sw_options;
	
	}

/****************************************************************************************
*																						*
*	The Social Warfare Floating Buttons													*
*																						*
*****************************************************************************************/

	function sw_options_floating_buttons($sw_options) {
					
		// Declare the Display Settings tab and tab name
		$sw_options['tabs']['links']['floatingButtons'] = 'Floating Buttons';

		$sw_options['options']['floatingButtons'] = array(
			'floatingButtonsTitle' => array(
				'type' 		=> 'title',
				'content' 	=> 'Floating Share Buttons'
			),
			'float' => array(
				'type' => 'checkbox',
				'content' => 'Floating Share Buttons?',
				'default' => '1'
			),
			'floatOption' => array(
				'type' => 'select',
				'name' => 'Float Position',
				'content' => array(
					'top' => 'Top of the Page', 
					'bottom' => 'Bottom of the Page',
					'left' => 'On the left side of the page' 
				),
				'default' => 'bottom'
			),
			'floatStyle' => array(
				'type' => 'select',
				'name' => 'Float Style',
				'content' => array(
					'default' => 'Buttons', 
					'boxed' => 'Boxes'
				),
				'default' => 'default'
			),
			'floatStyleSource' => array(
				'type' => 'checkbox',
				'content' => 'Style the buttons the same as the horizontal ones?',
				'default' => '1'
			),
			'sideDColorSet' => array(
				'type' => 'select',
				'name' => 'Default Color Set',
				'content' => array(
					'fullColor' 		=> 'Full Color',
					'lightGray' 		=> 'Light Gray',
					'mediumGray'		=> 'Medium Gray',
					'darkGray' 			=> 'Dark Gray',
					'lgOutlines' 		=> 'Light Gray Outlines',
					'mdOutlines'		=> 'Medium Gray Outlines',
					'dgOutlines' 		=> 'Dark Gray Outlines',
					'colorOutlines' 	=> 'Color Outlines',
					'customColor' 		=> 'Custom Color',
					'ccOutlines' 		=> 'Custom Color Outlines'
				),
				'default' => 'fullColor'
			),
			'sideIColorSet' => array(
				'type' => 'select',
				'name' => 'Individual Hover Color Set',
				'content' => array(
					'fullColor' 		=> 'Full Color',
					'lightGray' 		=> 'Light Gray',
					'mediumGray'		=> 'Medium Gray',
					'darkGray' 			=> 'Dark Gray',
					'lgOutlines' 		=> 'Light Gray Outlines',
					'mdOutlines'		=> 'Medium Gray Outlines',
					'dgOutlines' 		=> 'Dark Gray Outlines',
					'colorOutlines' 	=> 'Color Outlines',
					'customColor' 		=> 'Custom Color',
					'ccOutlines' 		=> 'Custom Color Outlines'
				),
				'default' => 'fullColor'
			),
			'sideOColorSet' => array(
				'type' => 'select',
				'name' => 'Other Buttons Hover Color Set',
				'content' => array(
					'fullColor' 		=> 'Full Color',
					'lightGray' 		=> 'Light Gray',
					'mediumGray'		=> 'Medium Gray',
					'darkGray' 			=> 'Dark Gray',
					'lgOutlines' 		=> 'Light Gray Outlines',
					'mdOutlines'		=> 'Medium Gray Outlines',
					'dgOutlines' 		=> 'Dark Gray Outlines',
					'colorOutlines' 	=> 'Color Outlines',
					'customColor' 		=> 'Custom Color',
					'ccOutlines' 		=> 'Custom Color Outlines'
				),
				'default' => 'fullColor'
			),
			'floatBgColor' => array(
				'type' => 'colorselect',
				'name' => 'Floating Background Color',
				'default' => '#ffffff'
			)
		);

		return $sw_options;

	}

/****************************************************************************************
*																						*
*	The Social Warfare Link Shortening													*
*																						*
*****************************************************************************************/

	function sw_options_link_shortening($sw_options) {
					
		// Declare the Display Settings tab and tab name
		$sw_options['tabs']['links']['linkShortening'] = 'Link Shortening';
	
		$sw_options['options']['linkShortening'] = array(
			'linkShortening' => array(
				'type' => 'checkbox',
				'content' => 'Activate Link Shortening?',
				'default' => 0
			),
			'shorteningMethod' => array(
				'type' => 'select',
				'name' => 'Shortening Format',
				'content' => array(
					'bitly' => 'bit.ly'
				),
				'default' => 'bitly'
			),
			'linkShorteningDescription' => array(
				'type' => 'paragraph',
				'content' => 'You MUST provide your bitly information below in order for link-shortening to work. This information can be found by logging into Bitly and navigating to the Settings page: <a target="_blank" href="https://bitly.com/a/settings/advanced">Bitly Setting Page</a>'
			),
			'bitlyLogin' => array(
				'type' => 'textbox',
				'content' => 'Bitly Login / Username'
			),
			'bitlyAPI' => array(
				'type' => 'textbox',
				'content' => 'Bitly API Key'
			)
		);
		
		return $sw_options;
		
	};
/****************************************************************************************
*																						*
*	The Social Warfare Analytics														*
*																						*
*****************************************************************************************/

	function sw_options_analytics($sw_options) {
	
		// Add the Analytics Tab and Tab Name
		$sw_options['tabs']['links']['analytics'] = 'Analytics';
	
		// Add the Analytics Options Arrays
		$sw_options['options']['analytics'] = array(
			'analyticsTitle' => array(
				'type' => 'title',
				'content' => 'Analytics'
			),
			'analyticsDescription' => array(
				'type' => 'paragraph',
				'content' => 'This page will allow you to activate analytics tracking for links that are shared via the Social Warfare buttons. This will allow you to see exactly how much traffic is being driven to your site as a direct result of the plugin.'
			),
			'googleAnalytics' => array(
				'type' => 'checkbox',
				'content' => 'Activate Google Analytics Tracking?',
				'default' => 0
			),
			'analyticsMedium' => array(
				'type' => 'textbox',
				'content' => 'Analytics Medium',
				'default' => 'social'
			),
			'analyticsCampaign' => array(
				'type' => 'textbox',
				'content' => 'Analytics Campaign',
				'default' => 'SocialWarfare'
			)
		);
	
		return $sw_options;	
	}
/****************************************************************************************
*																						*
*	The Social Warfare Frame Buster														*
*																						*
*****************************************************************************************/

	function sw_options_frame_buster($sw_options) {
					
		// Declare the Display Settings tab and tab name
		$sw_options['tabs']['links']['frameBuster'] = 'Frame Buster';
	
		$sw_options['options']['frameBuster'] = array(
			'frameBusterTitle' => array(
				'type' => 'title',
				'content' => 'Frame Buster'
			),
			'frameBusterParagraph' => array(
				'type' => 'paragraph',
				'content' => 'Frame Buster checks if your site is being displayed inside of a frame like Sniply. If it is being displayed inside of a frame, the plugin will redirect to the page on your own domain, removing third party ads and calls to action.'
			),
			'sniplyBuster' => array(
				'type' => 'checkbox',
				'content' => 'Activate Frame Buster?',
				'default' => 0
			)
		);
		
		return $sw_options;
		
	};
/****************************************************************************************
*																						*
*	The Social Warfare Click to Tweet													*
*																						*
*****************************************************************************************/

	function sw_options_clicktotweet($sw_options) {
	
		// Add the Click to Tweet Tab and Tab Name
		$sw_options['tabs']['links']['clicktotweet'] = 'Click to Tweet';
	
		// Add the Click to Tweet Options Arrays
		$sw_options['options']['clicktotweet'] = array(
			'clicktotweetTitle' => array(
				'type' => 'title',
				'content' => 'Click to Tweet'
			),
			'clicktotweetDescription' => array(
				'type' => 'paragraph',
				'content' => 'View the available visual styles below and then use this drop down to select the one that you would like to use on your site. If you have suggestions for new styles, please let us know.'
			),
			'cttTheme' => array(
				'type' => 'select',
				'name' => 'Visual Theme',
				'content' => array(
					'style1' => 'Send Her My Love', 
					'style2' => 'Roll With The Changes', 
					'style3' => 'Free Bird',
					'style4' => 'Don\'t Stop Believin\'', 
					'none' => 'None - Create Your Own CSS In Your Theme'),
				'default' => 'style1'
			),
			'style1demo' => array(
				'type' => 'paragraph',
				'content' => '<h4>Send Her My Love</h4><img src="'.PLUGIN_DIR.'/images/cttStyle1-demo.jpg"></p>'
			),
			'style2demo' => array(
				'type' => 'paragraph',
				'content' => '<h4>Roll With The Changes</h4><img src="'.PLUGIN_DIR.'/images/cttStyle2-demo.jpg"></p>'
			),
			'style3demo' => array(
				'type' => 'paragraph',
				'content' => '<h4>Free Bird</h4><img src="'.PLUGIN_DIR.'/images/cttStyle3-demo.jpg"></p>'
			),
			'style4demo' => array(
				'type' => 'paragraph',
				'content' => '<h4>Don\'t Stop Believin\'</h4><img src="'.PLUGIN_DIR.'/images/cttStyle4-demo.jpg"></p>'
			)
		);
		
		return $sw_options;
	}
/****************************************************************************************
*																						*
*	Recover Shares																		*
*																						*
*****************************************************************************************/

	function sw_options_recover_shares($sw_options) {
	
		// Add the Recover Shares Tab and Tab Name
		$sw_options['tabs']['links']['recover_shares'] = 'Share Recovery';
	
		// Add the Recover Shares Options Arrays
		$sw_options['options']['recover_shares'] = array(
			'recover_sharesTitle' => array(
				'type' => 'title',
				'content' => 'Share Counts Recovery (Beta)'
			),
			'recoverSharesDescription' => array(
				'type' => 'paragraph',
				'content' => 'If you\'ve ever used a different URL format than you are currently using, this feature can be used to recover the share counts from those other URLs. Simply turn this features on and then select the URL pattern that you used along with the previous protocol. Once active, the plugin will begin to fetch shares for both the current URL pattern and the former one. It will add them together and display the new total. <br /><br />NOTE: It may take up to 24 hours for all the share counts to update to their combined totals. <br /><br />For information about each type of URL format <a target="_blank" href="https://codex.wordpress.org/Settings_Permalinks_Screen">Click Here</a>'
			),
			'recover_shares' 	=> array(
				'type' 				=> 'checkbox',
				'content' 			=> 'Activate Share Count Recovery?',
				'default' 			=> false
			),
			'recovery_format' 	=> array(
				'type' 				=> 'select',
				'name' 				=> 'Previous URL Format',
				'content' 			=> array(
					'unchanged'			=> 'Unchanged',
					'default' 			=> 'Default',
					'day_and_name' 		=> 'Day and Name',
					'month_and_name' 	=> 'Month and Name',
					'numeric' 			=> 'Numeric',
					'post_name' 		=> 'Post Name'
				),
				'default' 			=> 'unchanged'
			),
			'recovery_protocol'	=> array(
				'type'				=> 'select',
				'name'				=> 'Previous Connection Protocol',
				'content'			=> array(
					'http'				=> 'http',
					'https'				=> 'https'
				),
				'default'			=> 'http'
			)
		);
	
		return $sw_options;
		
	}
/****************************************************************************************
*																						*
*	Rare Bugs & Their Fixes																*
*																						*
*****************************************************************************************/

	function sw_options_rare_bug_fixes($sw_options) {
					
		// Declare the Display Settings tab and tab name
		$sw_options['tabs']['links']['bugFixes'] = 'Rare Configurations';
	 
		$sw_options['options']['bugFixes'] = array(
			'visualOptionsBugFixTitle' => array(
				'type' 		=> 'title',
				'content' 	=> 'Rare Configuration Bugs & Their Fixes'
			),
			'rareBugsDescription' => array(
				'type' => 'paragraph',
				'content' => 'In some very rare cases, we\'ve encountered a few bugs that when fixed, end up creating bugs on other platforms. So instead of creating a universal fix for these issues, we have created the following options to be used on specific sites where the themes or plugins require the fix.'
			),
			'rawNumbersDescription' => array(
				'type' => 'paragraph',
				'content' => '<h3>Raw Share Information in Shares</h3>In some rare instances, raw share numbers and share information show up in front of the description when being shared. If this is happening on your site, simply activate this feature and the issue will go away. Most networks like Facebook will cache the information that they scraped from your page. As such, you can force a rescrape of your page by going to the following URL to see if it is indeed fixed: <a href="https://developers.facebook.com/tools/debug/" target="_blank">Facebook Open Graph Debugger.</a>'
			),
			'rawNumbers' => array(
				'type' => 'checkbox',
				'content' => 'Is raw share information displaying when sharing posts?',
				'default' => '0'
			),
			'visualEditorBugDescription' => array(
				'type' => 'paragraph',
				'content' => '<h3>Visual Editors</h3>If your theme (or a plugin) has you using a Visual Editor rather than the default WordPress post editor and if the buttons are not displaying properly or are doing something unusual then activating this option should fix you right up.'
			),
			'visualEditorBug' => array(
				'type' => 'checkbox',
				'content' => 'Are you using a visual editor that causes buttons to not behave?',
				'default' => '0'
			),
			'loopFixDescription' => array(
				'type' => 'paragraph',
				'content' => '<h3>Shares in Widget Areas</h3>If shares buttons are showing up in sidebar widgets or in related posts where a mini-loop of posts is being displayed and you want them turned off, activating this option should fix you right up.'
			),
			'loopFix' => array(
				'type' => 'checkbox',
				'content' => 'Are shares showing up on related posts or in widget areas?',
				'default' => '0'
			)
		);
		return $sw_options;
	}
/****************************************************************************************
*																						*
*	The Social Warfare Registration														*
*																						*
*****************************************************************************************/

	function sw_options_registration($sw_options) {
	
		// Add the Registration Tab and Tab Name
		$sw_options['tabs']['links']['registration'] = 'Registration';
	
		// Add the Registration Options Arrays
		$sw_options['options']['registration'] = array(
			'registrationTitle' => array(
				'type' => 'title',
				'content' => 'Premium Registration'
			)
		);
		
		$homeURL = get_home_url();
		$regCode = md5($homeURL);
		
		if(is_sw_registered()):
			$sw_options['options']['registration']['registrationNotice'] = array(
				'type' => 'paragraph',
				'content' => '<span style="color:green" class="sw_registration_span sw_registered"><b>This copy of Social Warfare IS registered.</b></span>'
			);
		else:
			$sw_options['options']['registration']['registrationNotice'] = array(
				'type' => 'paragraph',
				'content' => '<span style="color:#ed464f" class="sw_registration_span sw_not_registered"><b>This copy of Social Warfare IS NOT registered.</b></span>'
			);
		endif;
		
		$sw_options['options']['registration']['registrationInstructions'] = array(
			'type' => 'paragraph',
			'content' => '
		1. Enter Your Email.<br />
		2. Click on the "ACTIVATE PLUGIN" button.<br />
		3. Watch the magic.<br /><br />'
		);
		
		$sw_options['options']['registration']['registrationCode'] = array(
			'type' => 'paragraph',
			'content' => '
		<div class="at-label"><label for="regCode">Registration Code</label></div><input type="text" class="at-text" name="regCode" id="regCode" value="'.$regCode.'" size="30" readonly><input type="hidden" class="at-text" name="domain" id="domain" value="'.$homeURL.'" size="30" readonly><div class="clearfix"></div>
		'
		);
		
		$sw_options['options']['registration']['emailAddress'] = array(
			'type' => 'textbox',
			'content' => 'Email Address'
		);
		
		$sw_options['options']['registration']['premiumCode'] = array(
			'type' => 'textbox',
			'content' => 'Premium Code'
		);
		
		$sw_options['options']['registration']['registrationActivateButton'] = array(
			'type' => 'paragraph',
			'content' => '<input type="submit" class="activate btn-info" value="Activate Plugin" />'
		);
		
		$sw_options['options']['registration']['unregisterInstructions'] = array(
			'type' => 'paragraph',
			'content' => 'To unregister the plugin from this site, simply click the "Deactivate Plugin" button below. This will disable it\'s functionality on this site, but will free up your license, allowing you to register this plugin to a different domain.'
		);
		
		$sw_options['options']['registration']['unregisterButtons'] = array(
			'type' => 'paragraph',
			'content' => '<input type="submit" class="deactivate btn-info" value="Deactivate Plugin" />'
		);
		
		return $sw_options;
		
	}

/****************************************************************************************
*																						*
*	The Social Warfare System Status													*
*																						*
*****************************************************************************************/

	function sw_options_system_status($sw_options) {
	
		// Add the System Status Tab and Tab Name
		$sw_options['tabs']['links']['systemstatus'] = 'System Status';
	
		// Add the System Status Options Arrays
		$sw_options['options']['systemstatus'] = array(
			'systemStatusTitle' => array(
				'type' => 'title',
				'content' => 'System Status'
			)
		);
			
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$plugins = get_plugins();
		$pluginList = '';
		foreach ($plugins as $plugin):
			$pluginList .= '<tr><td><b>'.$plugin['Name'].'</b></td><td>'.$plugin['Version'].'</td></tr>';
		endforeach;
		
		if ( function_exists('fsockopen') ) :
			$fsockopen = '<span style="color:green;">Enabled</span>';
		else :
			$fsockopen = '<span style="color:red;">Disabled</span>';
		endif;
		
		if ( function_exists('curl_version') ) :
			$curl = '<span style="color:green;">Enabled</span>';
		else :
			$curl = '<span style="color:red;">Disabled</span>';
		endif;
		
		$theme = wp_get_theme();
		
		$sw_options['options']['systemstatus']['systemStatusOutput'] = array(
			'type' => 'paragraph',
			'content' => '
			<p>Please include all of the information on this page when reporting bugs or glitches. This will help us to more quickly identify any conflicts that may be interfering with the functioning of this plugin.</p>
			<table style="width:100%;">
				<tr><td><h2>Environment Statuses</h2></td><td></td></tr>
				<tr><td><b>Home URL</b></td><td>'.get_home_url().'</td></tr>
				<tr><td><b>Site URL</b></td><td>'.get_site_url().'</td></tr>
				<tr><td><b>WordPress Version</b></td><td>'.get_bloginfo('version').'</td></tr>
				<tr><td><b>PHP Version</b></td><td>'.phpversion().'</td></tr>
				<tr><td><b>WP Memory Limit</b></td><td>'.WP_MEMORY_LIMIT.'</td></tr>
				<tr><td><b>Social Warfare Version</b></td><td>'.SW_VERSION.'</td></tr>
				<tr><td><h2>Connection Statuses</h2></td><td></td></tr>
				<tr><td><b>fsockopen</b></td><td>'.$fsockopen.'</td></tr>
				<tr><td><b>cURL</b></td><td>'.$curl.'</td></tr>
				<tr><td><h2>Plugin Statuses</h2></td><td></td></tr>
				<tr><td><b>Theme Name</b></td><td>'.$theme['Name'].'</td></tr>
				<tr><td><b>Theme Version</b></td><td>'.$theme['Version'].'</td></tr>
				<tr><td><b>Active Plugins</b></td><td></td></tr>
				<tr><td><b>Number of Active Plugins</b></td><td>'.count($plugins).'</td></tr>
				'.$pluginList.'
				
			</table>
			'
		);
		
		return $sw_options;
		
	};

/****************************************************************************************
*																						*
*	Queue up the Options Filters														*
*																						*
*****************************************************************************************/

	add_filter('sw_options', 'sw_options_display_settings' 	, 1);
	add_filter('sw_options', 'sw_options_social_identity' 	, 2);
	add_filter('sw_options', 'sw_options_visual_options' 	, 3);
	add_filter('sw_options', 'sw_options_display_locations' , 4);
	add_filter('sw_options', 'sw_options_floating_buttons' 	, 5);
	add_filter('sw_options', 'sw_options_link_shortening' 	, 6);
	add_filter('sw_options', 'sw_options_analytics' 		, 7);
	add_filter('sw_options', 'sw_options_frame_buster' 		, 8);
	add_filter('sw_options', 'sw_options_clicktotweet' 		, 9);
	add_filter('sw_options', 'sw_options_recover_shares' 	, 10);
	add_filter('sw_options', 'sw_options_rare_bug_fixes' 	, 11);
	add_filter('sw_options', 'sw_options_registration' 		, 99);
	add_filter('sw_options', 'sw_options_system_status'		, 100);

/****************************************************************************************
*																						*
*	The Social Warfare Options Function	- Process the Options Array!					*
*																						*
*****************************************************************************************/

	// Queue up the Social Warfare options hook
	add_action('init' , 'sw_optionsClass' , 20);
	
	// The Options Function which relies on the Options Array above
	function sw_optionsClass() {
	
		// Fetch the Options Array - This is the sw_options filter hook
		global $sw_options;
		$sw_options = apply_filters('sw_options',$sw_options);

		// Initiate the Options Class with the config settings in the array
		$options_panel = new BF_Admin_Page_Class($sw_options['config']);
	
		// Open the Options Tabs Container
		$options_panel->OpenTabs_container('');
		
		// Execute the list of options tabs
		$options_panel->TabsListing($sw_options['tabs']);
		
		// Loop through the options tabs and build the options page
		foreach($sw_options['options'] as $tabName => $tabOptions):
			$options_panel->OpenTab($tabName);
			
			// Loop through and output the options for this tab
			foreach($tabOptions as $key => $option):
			
				// TITLE - Add a Title
				if($option['type'] == 'title'):
					$options_panel->Title($option['content']);
				endif;
				
				// PARAGRAPH - Add a Paragraph of Information
				if($option['type'] == 'paragraph'):
					$options_panel->addParagraph($option['content']);
				endif;
				
				// TEXTBOX - Add a Textbox option
				if($option['type'] == 'textbox'):
					if(isset($option['default'])):
						$options_panel->addText($key,array('name' => $option['content'], 'std' => $option['default']));
					else:
						$options_panel->addText($key,array('name' => $option['content']));
					endif;
				endif;
				
				// CHECKBOX - Add a checkbox option
				if($option['type'] == 'checkbox'):
					$options_panel->addCheckbox($key,array('name' => $option['content'], $key => $key, 'std' => $option['default']));
				endif;
				
				// SORTABLE - Add a sortable option
				if($option['type'] == 'sortable'):
					$options_panel->addSortable(
						$key,
						$option['content'],
						array('name' => $option['name'])
					);
				endif;
				
				// SELECT - Add a select option
				if($option['type'] == 'select'):
					$options_panel->addSelect(
						$key,
						$option['content'],
						array(
							'name' 	=> $option['name'],
							'std'	=> $option['default']
						)
					);
				endif;
				
				// COLOROPTION - Add a color picker
				if($option['type'] == 'colorselect'):
					$options_panel->addColor(
						$key,
						array(
							'name'=> $option['name'], 
							'std' => $option['default']
						)
					);
				endif;
			
			endforeach;
			
			// Close the tab and move on to the next one
			$options_panel->CloseTab();
		endforeach;
	};
	
/****************************************************************************************
*																						*
*	The Social Warfare Add Option(s) After Hook	Function								*
*																						*
*****************************************************************************************/

function sw_add_option_after($sw_options,$tabName,$optionName,$newOptionArray) {

	// Locate the index of the option you want to insert next to
    $keyIndex = array_search( 
        $optionName, 
        array_keys( $sw_options['options'][$tabName] ) 
    );
    
    // Split the array at the location of the option above
    $first_array = array_splice (
        $sw_options['options'][$tabName], 
        0, 
        $keyIndex+1
    ); 
    
    // Merge the two parts of the split array with your option added in the middle
    $sw_options['options'][$tabName] = array_merge (
        $first_array, 
        $newOptionArray, 
        $sw_options['options'][$tabName]
    ); 
 
    // Return the option array or the world will explode
    return $sw_options;
	
}

function sw_add_language_option($sw_options,$langName,$langCode) {
	
	// Add our new language to the options page
	$sw_options['options']['displaySettings']['language']['content'][$langCode] = $langName;

	// Return the option array or the world will explode
	return $sw_options;

}

function sw_add_network_option($sw_options,$newOptionArray) {
		// Locate the index of the option you want to insert next to
    $keyIndex = array_search( 
        'iconDisplayInfo', 
        array_keys( $sw_options['options']['displaySettings'] ) 
    );
    
    // Split the array at the location of the option above
    $first_array = array_splice (
        $sw_options['options']['displaySettings'], 
        0, 
        $keyIndex+1
    ); 
    
    // Merge the two parts of the split array with your option added in the middle
    $sw_options['options']['displaySettings'] = array_merge (
        $first_array, 
        $newOptionArray, 
        $sw_options['options']['displaySettings']
    ); 
	
	$key = key($newOptionArray);
	$sw_options['options']['displaySettings']['newOrderOfIcons']['content'][$key] = $newOptionArray[$key]['content'];
 
    // Return the option array or the world will explode
    return $sw_options;
}