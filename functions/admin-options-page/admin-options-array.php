<?php

/****************************************************************************************
*																						*
*	The Social Warfare Display Settings													*
*																						*
*****************************************************************************************/
	function sw_options_display($sw_options) {

		// Declare the Options Tab and Tab Name
		$sw_options['tabs']['links']['sw_display'] = 'Display';

		// Declare the content that goes on this options page
		$sw_options['options']['sw_display'] = array(
			'social_networks_title' => array(
				'type' 		=> 'title',
				'content' 	=> 'Social Networks'
			),
			'social_networks_subtitle' => array(
				'type'		=> 'paragraph',
				'content'	=> 'Drag & Drop to activate and order your share buttons.'
			),
			'button_order_placeholder' => array(
				'type'		=> 'image',
				'content'	=> SW_PLUGIN_DIR.'/functions/admin-options-page/images/social-networks.png',
				'divider'	=> true
			),
			'share_counts_title' => array(
				'type' 		=> 'title',
				'content' 	=> 'Share Counts'
			),
			'share_counts_subtitle' => array(
				'type'		=> 'paragraph',
				'content'	=> 'Use the toggles below to determine how to display your social proof.'
			),
			'totesEach' => array(
				'type'		=> 'checkbox',
				'size'		=> 'two-thirds',
				'content'	=> 'Button Counts',
				'default'	=> true
			),
			'totes' => array(
				'type'		=> 'checkbox',
				'size'		=> 'two-thirds',
				'content'	=> 'Total Counts',
				'default'	=> true
			),
			'minTotes' => array(
				'type'		=> 'input',
				'size'		=> 'two-thirds',
				'name'	=> 'Minimum Shares',
				'default'	=> 0,
				'divider'	=> true
			),
			'sw_twitter_card' => array(
				'type'		=> 'checkbox',
				'size'		=> 'two-thirds',
				'content'	=> 'Twitter Cards',
				'header'	=> true,
				'divider'	=> true,
				'default'	=> true
			),
			'position_title' => array(
				'type' 		=> 'title',
				'content' 	=> 'Position Share Buttons'
			),
			'position_subtitle' => array(
				'type'		=> 'paragraph',
				'content'	=> 'The options below will allow you to customize the positioning of share buttons for each post type.'
			),
			'location_column_labels' => array(
				'type'		=> 'column_labels',
				'columns'	=> 3,
				'column_1'	=> '',
				'column_2'	=> 'Static Buttons',
				'column_3'	=> 'Side Floating Buttons (If Activated)'
			)
		);
		
		// Create the content locations
		$contentLocations = array(
			'above'=>'Above the Content',
			'below' => 'Below the Content',
			'both' => 'Both Above and Below the Content',
			'none' => 'None/Manual Placement'
		);
		
		// Get the post Types
		$postTypes = get_post_types();

		// Unset the post types that don't matter
		// if(isset($postTypes['post'])) 				unset($postTypes['post']);
		// if(isset($postTypes['page'])) 				unset($postTypes['page']);
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
				$sw_options['options']['sw_display']['location_'.$postType] = array(
					'type'		=> 'select',
					'name'		=> 'Location on '.$postType.' Posts',
					'primary'	=> 'location_'.$postType,
					'secondary' => 'float_location_'.$postType,
					'content'	=> $contentLocations,
					'content_2'	=> $contentLocations, 
					'default'	=> 'both',
					'default_2' => 'both'
				);
			endforeach;
		endif;

		$sw_options['options']['sw_display']['yummly_divider'] = array(
			'type'		=> 'divider'			
		);

		$sw_options['options']['sw_display']['yummly_cat_title'] = array(
			'type' 		=> 'title',
			'content' 	=> 'Yummly Display Control'
		);

		$sw_options['options']['sw_display']['yummly_cat_description'] = array(
			'type' 		=> 'paragraph',
			'content' 	=> 'If you would like the Yummly to display on a specific category or tag, choose it below.'
		);
		
		$sw_options['options']['sw_display']['yummly_column_labels'] = array(
			'type'		=> 'column_labels',
			'columns'	=> 3,
			'column_1'	=> '',
			'column_2'	=> 'Choose Category',
			'column_3'	=> 'Choose Tag'
		);
		
		$raw_tags = get_terms( array('taxonomy' => 'post_tag' , 'hide_empty' => false ) );
		foreach ( $raw_tags as $tag ):
			$tags[$tag->slug] = $tag->name;
		endforeach;
		
		$raw_cats = get_terms( array('taxonomy' => 'category' , 'hide_empty' => false ) );
		foreach ( $raw_cats as $cat ):
			$cats[$cat->slug] = $cat->name;
		endforeach;
		
		$sw_options['options']['sw_display']['yummly_terms'] = array(
			'type'		=> 'select',
			'name'		=> 'Yummly Terms',
			'primary'	=> 'yummly_categories',
			'secondary' => 'yummly_tags',
			'content'	=> $cats,
			'content_2'	=> $tags
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
		$sw_options['tabs']['links']['sw_styles'] = 'Styles';

		$sw_options['options']['sw_styles'] = array(
			'visual_options_title' => array(
				'type' 		=> 'title',
				'content' 	=> 'Visual Options'
			),
			'visual_options_description' => array(
				'type' 		=> 'paragraph',
				'content' 	=> 'Use the settings below to customize the look of your share buttons.'
			),
			'visualTheme' => array(
				'type'		=> 'select',
				'size'		=> 'two-fourths',
				'name'		=> 'Button Shape',
				'content'	=> array(
					'flatFresh' 	=> 'Flat & Fresh',
					'leaf' 			=> 'A Leaf on the Wind',
					'shift' 		=> 'Shift',
					'pill' 			=> 'Pills',
					'threeDee' 		=> 'Three-Dee',
					'connected' 	=> 'Connected'
				),
				'default' => 'flatFresh'
			),
			'buttonSize' => array(
				'type' => 'select',
				'size' => 'two-fourths',
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
			'dColorSet' => array(
				'type' => 'select',
				'size' => 'two-fourths',
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
			'oColorSet' => array(
				'type' => 'select',
				'size' => 'two-fourths',
				'name' => 'Hover Color Set',
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
				'size' => 'two-fourths',
				'name' => 'Single Button Hover',
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
			'maximum_width' => array(
				'type'		=> 'input',
				'size'		=> 'two-fourths',
				'name'		=> 'Maximum Width'
			),
			'buttons_preview' => array(
				'type' => 'html',
				'divider' => true,
				'content' => '<div class="nc_socialPanel sw_flatFresh sw_d_fullColor sw_i_fullColor sw_o_fullColor" data-position="both" data-float="floatNone" data-count="6" data-floatColor="#ffffff" data-scale="1" data-align="fullWidth"><div class="nc_tweetContainer googlePlus" data-id="2"><a target="_blank" href="https://plus.google.com/share?url=http%3A%2F%2Fwfa.re%2F1W28voz" data-link="https://plus.google.com/share?url=http%3A%2F%2Fwfa.re%2F1W28voz" class="nc_tweet"><span class="iconFiller"><span class="spaceManWilly"><i class="sw sw-google-plus"></i><span class="sw_share"> +1</span></span></span><span class="sw_count">1.2K</span></a></div><div class="nc_tweetContainer twitter" data-id="3"><a href="https://twitter.com/share?original_referer=/&text=Ultimate+Social+Share+%23WordPress+plugin%21+Beautiful%2C+super+fast+%26+more+http%3A%2F%2Fwarfareplugins.com+pic.twitter.com%2FA2zcCJwZtO&url=/&via=WarfarePlugins" data-link="https://twitter.com/share?original_referer=/&text=Ultimate+Social+Share+%23WordPress+plugin%21+Beautiful%2C+super+fast+%26+more+http%3A%2F%2Fwarfareplugins.com+pic.twitter.com%2FA2zcCJwZtO&url=/&via=WarfarePlugins" class="nc_tweet"><span class="iconFiller"><span class="spaceManWilly"><i class="sw sw-twitter"></i><span class="sw_share"> Tweet</span></span></span><span class="sw_count">280</span></a></div><div class="nc_tweetContainer nc_pinterest" data-id="6"><a data-link="https://pinterest.com/pin/create/button/?url=https://warfareplugins.com/&media=https%3A%2F%2Fwarfareplugins.com%2Fwp-content%2Fuploads%2Fget-content-shared-735x1102.jpg&description=Customize+your+Pinterest+sharing+options%2C+create+easy+%22click+to+tweet%22+buttons+within+your+blog+posts%2C+beautiful+sharing+buttons+and+more.+Social+Warfare+is+the+ultimate+social+sharing+arsenal+for+WordPress%21" class="nc_tweet" data-count="0"><span class="iconFiller"><span class="spaceManWilly" style="width:55px;"><i class="sw sw-pinterest"></i><span class="sw_share"> Pin</span></span></span><span class="sw_count">104</span></a></div><div class="nc_tweetContainer fb" data-id="4"><a target="_blank" href="http://www.facebook.com/share.php?u=http%3A%2F%2Fwfa.re%2F1W28vov" data-link="http://www.facebook.com/share.php?u=http%3A%2F%2Fwfa.re%2F1W28vov" class="nc_tweet"><span class="iconFiller"><span class="spaceManWilly"><i class="sw sw-facebook"></i><span class="sw_share"> Share</span></span></span><span class="sw_count">157</span></a></div><div class="nc_tweetContainer linkedIn" data-id="5"><a target="_blank" href="https://www.linkedin.com/cws/share?url=http%3A%2F%2Fwfa.re%2F1W28twH" data-link="https://www.linkedin.com/cws/share?url=http%3A%2F%2Fwfa.re%2F1W28twH" class="nc_tweet"><span class="iconFiller"><span class="spaceManWilly"><i class="sw sw-linkedin"></i><span class="sw_share"> Share</span></span></span><span class="sw_count">51</span></a></div><div class="nc_tweetContainer totes totesalt" data-id="6" ><span class="sw_count"><span class="sw_label">Shares</span> 1.8K</span></div></div>'
			),
			'total_counts_title' => array(
				'type'	=> 'title',
				'content' => 'Total Counts'
			),
			'total_counts_description' => array(
				'type' 		=> 'paragraph',
				'content' 	=> 'Customize how the "Total Shares" section of your share buttons look.'
			),
			'swDecimals' => array(
				'type' => 'select',
				'name' => 'Decimal Places',
				'size' => 'two-fourths',
				'content' => array(
					'0' => 'Zero',
					'1' => 'One',
					'2' => 'Two'
				),
				'default' => '0'
			 ),
			'sw_decimal_separator' => array(
				'type' => 'select',
				'size' => 'two-fourths',
				'name' => 'Decimal Separator',
				'content' => array(
					'period' => 'Period',
					'comma' => 'Comma'
				),
				'default' => 'period'
			 ),
			 'swTotesFormat' => array(
				'type' => 'select',
				'size' => 'two-fourths',
				'name' => 'Alignment',
				'content' => array(
					'totesAlt'		=>	'Right',
					'totesAltLeft'	=>	'Left'
				),
				'default' => 'totesAlt'
			),
			'total_counts_divider' => array(
				'type' => 'divider'
			),
			'float' => array(
				'type'			=> 'checkbox',
				'title' 		=> 'Floating Share Buttons',
				'description' 	=> 'If you would like to activate floating share buttons, turn this on.',
				'size'			=> 'four-fourths'
			),
			'total_counts_description' => array(
				'type' 		=> 'paragraph',
				'content' 	=> 'If you would like to activate floating share buttons, turn this on.'
			),
			'floatOption' => array(
				'type' => 'select',
				'size' => 'two-fourths',
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
				'size' => 'two-fourths',
				'content' => array(
					'default' => 'Buttons',
					'boxed' => 'Boxes'
				),
				'default' => 'default'
			),
			'floatStyleSource' => array(
				'type' => 'checkbox',
				'size' => 'two-fourths',
				'content' => 'Inherit Visual Options',
				'default' => '1'
			),
			'sideDColorSet' => array(
				'type' => 'select',
				'size' => 'two-fourths',
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
			'sideOColorSet' => array(
				'type' => 'select',
				'size' => 'two-fourths',
				'name' => 'Hover Color Set',
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
				'size' => 'two-fourths',
				'name' => 'Single Button Hover',
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
			'sideReveal' => array(
				'type' => 'select',
				'size' => 'two-fourths',
				'name' => 'Hide & Seek Transition',
				'content' => array(
					'slide' 			=> 'Slide In / Slide Out',
					'fade' 				=> 'Fade In / Fade Out'
				),
				'default' => 'slide'
			),
			'floatBgColor' => array(
				'type' => 'input',
				'size' => 'two-fourths',
				'name' => 'Background Color',
				'default' => '#ffffff'
			),
			'sw_float_scr_sz' => array(
				'type' => 'input',
				'size' => 'two-fourths',
				'name' => 'Minimum Screen Width',
				'default' => '1100'
			),
			'click_to_tweet_divider' => array(
				'type' => 'divider'
			),
			'click_to_tweet_title' => array(
				'type' => 'title',
				'content' => 'Click-to-Tweet Style'
			),
			'click_to_tweet_description' => array(
				'type' => 'paragraph',
				'content' => 'Select the default visual style for Click-to-Tweets on your site.'
			),
			'cttTheme' => array(
				'type' => 'select',
				'size' => 'two-fourths',
				'name' => 'Visual Theme',
				'content' => array(
					'style1' => 'Send Her My Love',
					'style2' => 'Roll With The Changes',
					'style3' => 'Free Bird',
					'style4' => 'Don\'t Stop Believin\'',
					'style5' => 'Thunderstruck',
					'style6' => 'Livin\' On A Prayer',
					'none' => 'None - Create Your Own CSS In Your Theme'),
				'default' => 'style1'
			),
			'cttPreview' => array(
				'type' => 'html',
				'content' => '<a class="sw_CTT style1"  data-style="style1" href="https://twitter.com/share?text=We+couldn%27t+find+one+social+sharing+plugin+that+met+all+of+our+needs%2C+so+we+built+it+ourselves.&amp;url=http://warfareplugins.com&amp;via=warfareplugins" data-link="https://twitter.com/share?text=We+couldn%27t+find+one+social+sharing+plugin+that+met+all+of+our+needs%2C+so+we+built+it+ourselves.&amp;url=http://wfa.re/1PtqdNM&amp;via=WarfarePlugins" target="_blank"><span class="sw-click-to-tweet"><span class="sw-ctt-text">We couldn\'t find one social sharing plugin that met all of our needs, so we built it ourselves.</span><span class="sw-ctt-btn">Click To Tweet<i class="sw sw-twitter"></i></span></span></a>'
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

	function sw_options_social_identity($sw_options) {

		// Declare the Display Settings tab and tab name
		$sw_options['tabs']['links']['sw_social_identity'] = 'Social Identity';

		$sw_options['options']['sw_social_identity'] = array(
			'social_identity_title' => array(
				'type' 		=> 'title',
				'content' 	=> 'Sitewide Identity'
			),
			'social_identity_description' => array(
				'type' 		=> 'paragraph',
				'content' 	=> 'If you would like to set sitewide defaults for your social identity, add them below.'
			),
			'twitterID' => array(
				'type'		=> 'input',
				'size'		=> 'two-thirds',
				'name'		=> 'Twitter Username'
			),
			'pinterestID' => array(
				'type'		=> 'input',
				'size'		=> 'two-thirds',
				'name'		=> 'Pinterest Username'
			),
			'facebookPublisherUrl' => array(
				'type'		=> 'input',
				'size'		=> 'two-thirds',
				'name'		=> 'Facebook Page URL'
			),
			'facebookAppID' => array(
				'type'		=> 'input',
				'size'		=> 'two-thirds',
				'name'		=> 'Facebook App ID'
			),
			'social_identity_divider' => array(
				'type'		=> 'divider'
			),
			'social_authentication_title' => array(
				'type' 		=> 'title',
				'content'	=> 'Social Authentication'
			),
			'social_authentication_description' => array(
				'type' 		=> 'paragraph',
				'content'	=> 'In order to have some of the advanced functions like uploading images to Twitter for the custom tweet or fetching your follower counts for the follow widget, we need you to login with your social accounts.'
			),
			'twitter_authentication' => array(
				'type'		=> 'authentication',
				'link'		=> '#',
				'name'		=> 'Connect Your Twitter Account'
			),
			'facebook_authentication' => array(
				'type'		=> 'authentication',
				'link'		=> '#',
				'name'		=> 'Connect Your Facebook Account'
			),
			'google_authentication' => array(
				'type'		=> 'authentication',
				'link'		=> '#',
				'name'		=> 'Connect Your Google Account'
			)
			
		);
		
		return $sw_options;
		
	}

/****************************************************************************************
*																						*
*	Queue up the Options Filters														*
*																						*
*****************************************************************************************/

	add_filter('sw_options_page', 'sw_options_display' 			, 1 );
	add_filter('sw_options_page', 'sw_options_styles' 			, 2 );
	add_filter('sw_options_page', 'sw_options_social_identity'	, 3 );
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
