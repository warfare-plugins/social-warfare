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
				'spacing'	=> 'two-thirds',
				'content'	=> 'Button Counts',
				'default'	=> true
			),
			'totes' => array(
				'type'		=> 'checkbox',
				'spacing'	=> 'two-thirds',
				'content'	=> 'Total Counts',
				'default'	=> true
			),
			'minTotes' => array(
				'type'		=> 'input',
				'spacing'	=> 'two-thirds',
				'content'	=> 'Minimum Shares',
				'default'	=> 0,
				'divider'	=> true
			),
			'sw_twitter_card' => array(
				'type'		=> 'checkbox',
				'spacing'	=> 'two-thirds',
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
		
		var_dump($tags);
		
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
			'visualOptionsTitle' => array(
				'type' 		=> 'title',
				'content' 	=> 'Visual Options'
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
