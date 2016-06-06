<?php

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
*	The Social Warfare Display Settings													*
*																						*
*****************************************************************************************/
	function sw_options_display($sw_options) {

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
			'sw_twitter_card' => array(
				'type'		=> 'checkbox',
				'content'	=> 'Activate Twitter Cards?',
				'default'	=> true
			),
			'iconDisplayInfo' => array(
				'type'		=> 'paragraph',
				'content'	=> '<h3>Which social media buttons would you like to display?</h3>'
			),
			'googlePlus' => array(
				'type'		=> 'checkbox',
				'content'	=> 'Google+',
				'default'	=> true
			),
			'twitter' => array(
				'type'		=> 'checkbox',
				'content'	=> 'Twitter',
				'default'	=> true
			),
			'facebook' => array(
				'type'		=> 'checkbox',
				'content'	=> 'Facebook',
				'default'	=> true
			),
			'pinterest' => array(
				'type'		=> 'checkbox',
				'content'	=> 'Pinterest',
				'default'	=> true
			),
			'linkedIn' => array(
				'type'		=> 'checkbox',
				'content'	=> 'LinkedIn',
				'default'	=> true
			),
			'totes' => array(
				'type'		=> 'checkbox',
				'content'	=> 'Show Total Shares',
				'default'	=> true
			),
			'totesEach' => array(
				'type'		=> 'checkbox',
				'content'	=> 'Show Individual Button Counts',
				'default'	=> true
			),
			'minTotes' => array(
				'type'		=> 'textbox',
				'content'	=> 'Minimum Number of shares required before showing counts?',
				'default'	=> true
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
*	The Styles Tab																		*
*																						*
*****************************************************************************************/

	function sw_options_styles($sw_options) {

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
				'content' => '<div class="nc_socialPanel sw_flatFresh sw_d_fullColor sw_i_fullColor sw_o_fullColor" data-position="both" data-float="floatNone" data-count="6" data-floatColor="#ffffff" data-scale="1" data-align="fullWidth"><div class="nc_tweetContainer googlePlus" data-id="2"><a target="_blank" href="https://plus.google.com/share?url=http%3A%2F%2Fwfa.re%2F1W28voz" data-link="https://plus.google.com/share?url=http%3A%2F%2Fwfa.re%2F1W28voz" class="nc_tweet"><span class="iconFiller"><span class="spaceManWilly"><i class="sw sw-google-plus"></i><span class="sw_share"> +1</span></span></span><span class="sw_count">1.2K</span></a></div><div class="nc_tweetContainer twitter" data-id="3"><a href="https://twitter.com/share?original_referer=/&text=Ultimate+Social+Share+%23WordPress+plugin%21+Beautiful%2C+super+fast+%26+more+http%3A%2F%2Fwarfareplugins.com+pic.twitter.com%2FA2zcCJwZtO&url=/&via=WarfarePlugins" data-link="https://twitter.com/share?original_referer=/&text=Ultimate+Social+Share+%23WordPress+plugin%21+Beautiful%2C+super+fast+%26+more+http%3A%2F%2Fwarfareplugins.com+pic.twitter.com%2FA2zcCJwZtO&url=/&via=WarfarePlugins" class="nc_tweet"><span class="iconFiller"><span class="spaceManWilly"><i class="sw sw-twitter"></i><span class="sw_share"> Tweet</span></span></span><span class="sw_count">280</span></a></div><div class="nc_tweetContainer nc_pinterest" data-id="6"><a data-link="https://pinterest.com/pin/create/button/?url=https://warfareplugins.com/&media=https%3A%2F%2Fwarfareplugins.com%2Fwp-content%2Fuploads%2Fget-content-shared-735x1102.jpg&description=Customize+your+Pinterest+sharing+options%2C+create+easy+%22click+to+tweet%22+buttons+within+your+blog+posts%2C+beautiful+sharing+buttons+and+more.+Social+Warfare+is+the+ultimate+social+sharing+arsenal+for+WordPress%21" class="nc_tweet" data-count="0"><span class="iconFiller"><span class="spaceManWilly" style="width:55px;"><i class="sw sw-pinterest"></i><span class="sw_share"> Pin</span></span></span><span class="sw_count">104</span></a></div><div class="nc_tweetContainer fb" data-id="4"><a target="_blank" href="http://www.facebook.com/share.php?u=http%3A%2F%2Fwfa.re%2F1W28vov" data-link="http://www.facebook.com/share.php?u=http%3A%2F%2Fwfa.re%2F1W28vov" class="nc_tweet"><span class="iconFiller"><span class="spaceManWilly"><i class="sw sw-facebook"></i><span class="sw_share"> Share</span></span></span><span class="sw_count">157</span></a></div><div class="nc_tweetContainer linkedIn" data-id="5"><a target="_blank" href="https://www.linkedin.com/cws/share?url=http%3A%2F%2Fwfa.re%2F1W28twH" data-link="https://www.linkedin.com/cws/share?url=http%3A%2F%2Fwfa.re%2F1W28twH" class="nc_tweet"><span class="iconFiller"><span class="spaceManWilly"><i class="sw sw-linkedin"></i><span class="sw_share"> Share</span></span></span><span class="sw_count">51</span></a></div><div class="nc_tweetContainer totes totesalt" data-id="6" ><span class="sw_count"><span class="sw_label">Shares</span> 1.8K</span></div></div>'
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
			'sw_decimal_separator' => array(
				'type' => 'select',
				'name' => 'Decimal Separator',
				'content' => array(
					'period' => 'Period',
					'comma' => 'Comma'
				),
				'default' => 'period'
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
*	Queue up the Options Filters														*
*																						*
*****************************************************************************************/

	add_filter('sw_options_page', 'sw_options_display' 			, 1 );
	add_filter('sw_options_page', 'sw_options_styles' 			, 2 );
//	add_filter('sw_options', 'sw_options_display_locations' , 3 );
//	add_filter('sw_options', 'sw_options_floating_buttons' 	, 4 );
//	add_filter('sw_options', 'sw_options_clicktotweet' 		, 5 );
//	add_filter('sw_options', 'sw_options_tweet_counts' 		, 6 );
//	add_filter('sw_options', 'sw_options_social_identity' 	, 7 );
//	add_filter('sw_options', 'sw_options_link_shortening' 	, 8 );
//	add_filter('sw_options', 'sw_options_analytics' 		, 9 );
//	add_filter('sw_options', 'sw_options_frame_buster' 		, 10);
//	add_filter('sw_options', 'sw_options_recover_shares' 	, 11);
//	add_filter('sw_options', 'sw_options_rare_bug_fixes' 	, 12);
//	add_filter('sw_options', 'sw_options_registration' 		, 99);
//	add_filter('sw_options', 'sw_options_system_status'		, 100);

/****************************************************************************************
*																						*
*	The Social Warfare Options Function	- Process the Options Array!					*
*																						*
*****************************************************************************************

	// Queue up the Social Warfare options hook
	// add_action('init' , 'sw_optionsClass' , 20);

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
*****************************************************************************************

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
