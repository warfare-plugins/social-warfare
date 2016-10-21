<?php

	/**
	 * swp_options_display An array of options for the display tab of the options page
	 * @since 	2.0.0
	 * @param  	array $swp_options The array of options
	 * @return 	array $swp_options The modified array of options
	 */
	function swp_options_display($swp_options) {

		$icons_array = array(
			'type'		=> 'buttons'
		);
		$icons_array = apply_filters( 'swp_button_options' , $icons_array );

		// Declare the Options Tab and Tab Name
		$swp_options['tabs']['links']['swp_display'] = __( 'Display' );

		// Declare the content that goes on this options page
		$swp_options['options']['swp_display'] = array(
			'social_networks_title' => array(
				'type' 		=> 'title',
				'content' 	=> __( 'Social Networks' )
			),
			'social_networks_subtitle' => array(
				'type'		=> 'paragraph',
				'content'	=> __( 'Drag & Drop to activate and order your share buttons.' )
			),
			'buttons' => $icons_array,
			'orderOfIconsSelect' => array(
				'type'		=> 'select',
				'size'		=> 'two-thirds',
				'content'	=> array(
					'manual' 	=> __( 'Sort Manually Using Drag & Drop Above' ),
					'dynamic' 	=> __( 'Sort Dynamically By Order Of Most Shares' )
				),
				'default'	=> 'manual',
				'name'		=> __( 'Button Ordering' ),
				'divider'	=> false,
				'premium'	=> true
			),
			'emphasize_icons' => array(
				'type'		=> 'select',
				'size'		=> 'two-thirds',
				'content'	=> array(
					'0' 	=> __( 'Don\'t Emphasize Any Buttons' ),
					'1' 	=> __( 'Emphasize the First Button' ),
					'2' 	=> __( 'Emphasize the First Two Buttons' )
				),
				'default'	=> '0',
				'name'		=> __( 'Emphasize Buttons' ),
				'divider'	=> true,
				'premium'	=> true
			),
			'share_counts_title' => array(
				'type' 		=> 'title',
				'content' 	=> __( 'Share Counts' )
			),
			'share_counts_subtitle' => array(
				'type'		=> 'paragraph',
				'content'	=> __( 'Use the toggles below to determine how to display your social proof.' )
			),
			'totesEach' => array(
				'type'		=> 'checkbox',
				'size'		=> 'two-thirds',
				'content'	=> __( 'Button Counts' ),
				'default'	=> true,
				'premium'	=> false
			),
			'totes' => array(
				'type'		=> 'checkbox',
				'size'		=> 'two-thirds',
				'content'	=> __( 'Total Counts' ),
				'default'	=> true,
				'premium'	=> false
			),
			'minTotes' => array(
				'type'		=> 'input',
				'size'		=> 'two-thirds',
				'name'		=> __( 'Minimum Shares' ),
				'default'	=> 0,
				'divider'	=> true,
				'premium'	=> true
			),
			'swp_twitter_card' => array(
				'type'		=> 'checkbox',
				'size'		=> 'two-thirds',
				'content'	=> __( 'Twitter Cards' ),
				'header'	=> true,
				'divider'	=> true,
				'default'	=> true,
				'premium'	=> true
			),
			'position_title' => array(
				'type' 		=> 'title',
				'content' 	=> __( 'Position Share Buttons' )
			),
			'position_subtitle' => array(
				'type'		=> 'paragraph',
				'content'	=> __( 'The options below will allow you to customize the positioning of share buttons for each post type.' )
			),
			'location_column_labels' => array(
				'type'		=> 'column_labels',
				'columns'	=> 3,
				'column_1'	=> __( 'Post Type' ),
				'column_2'	=> __( 'Static Buttons' ),
				'column_3'	=> __( 'Floating Buttons (If Activated)' )
			)
		);

		// Create the content locations
		$contentLocations = array(
			'above'=> __( 'Above the Content' ),
			'below' => __( 'Below the Content' ),
			'both' => __( 'Both Above and Below the Content' ),
			'none' => __( 'None/Manual Placement' )
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
				$swp_options['options']['swp_display']['location_'.$postType] = array(
					'type'		=> 'select',
					'name'		=> ucfirst($postType),
					'primary'	=> 'location_'.$postType,
					'secondary' => 'float_location_'.$postType,
					'content'	=> $contentLocations,
					'content_2'	=> array(
						'on'	=> __( 'On' ),
						'off'	=> __( 'Off' ),
					),
					'default'	=> 'both',
					'default_2' => 'on'
				);
			endforeach;
		endif;

		$swp_options['options']['swp_display']['locationSite'] = array(
			'type'		=> 'select',
			'name'		=> __( 'Archive & Categories' ),
			'content'	=> $contentLocations,
			'default'	=> 'below',
			'size'		=> 'two-thirds'
		);

		$swp_options['options']['swp_display']['pinit_divider'] = array(
			'type'		=> 'divider'
		);

		$swp_options['options']['swp_display']['pinit_title'] = array(
			'type' 		=> 'title',
			'content' 	=> __( 'Image Hover Pin Button' ),
			'premium'	=> true
		);

		$swp_options['options']['swp_display']['pinit_description'] = array(
			'type' 		=> 'paragraph',
			'content' 	=> __( 'If you would like a "Pin" button to appear on images when users hover over them, activate this.' ),
			'premium'	=> true
		);

		$swp_options['options']['swp_display']['pinit_toggle'] = array(
			'type'		=> 'checkbox',
			'size'		=> 'two-thirds',
			'content'	=> __( 'Pinit Button' ),
			'default'	=> true,
			'premium'	=> true
		);

		$swp_options['options']['swp_display']['pinit_location_horizontal'] = array(
			'type'		=> 'select',
			'name'		=> __( 'Horizontal Location' ),
			'content'	=> array(
				'left' 		=> __( 'Left' ),
				'center'	=> __( 'Center' ),
				'right' 	=> __( 'Right' )
			),
			'default'	=> 'center',
			'size'		=> 'two-fourths',
			'premium'	=> true,
			'dep' 		=> 'pinit_toggle',
			'dep_val'	=> array(true)
		);

		$swp_options['options']['swp_display']['pinit_min_width'] = array(
			'type'		=> 'input',
			'name'		=> __( 'Min Width' ),
			'default'	=> '200',
			'size'		=> 'two-fourths',
			'premium'	=> true,
			'dep' 		=> 'pinit_toggle',
			'dep_val'	=> array(true)
		);

		$swp_options['options']['swp_display']['pinit_location_vertical'] = array(
			'type'		=> 'select',
			'name'		=> __( 'Vertical Location' ),
			'content'	=> array(
				'top' 		=> __( 'Top' ),
				'middle'	=> __( 'Middle' ),
				'bottom' 	=> __( 'Bottom' )
			),
			'default'	=> 'top',
			'size'		=> 'two-fourths',
			'premium'	=> true,
			'dep' 		=> 'pinit_toggle',
			'dep_val'	=> array(true)
		);

		$swp_options['options']['swp_display']['pinit_min_height'] = array(
			'type'		=> 'input',
			'name'		=> __( 'Min Height' ),
			'default'	=> '200',
			'size'		=> 'two-fourths',
			'premium'	=> true,
			'dep' 		=> 'pinit_toggle',
			'dep_val'	=> array(true)
		);

		//$swp_options['options']['swp_display']['pinit_custom_image'] = array(
		//	'type'		=> 'image_upload',
		//	'name'		=> 'Custom Image',
		//	'default'	=> 'top_left',
		//	'premium'	=> true,
		//	'dep' 		=> 'pinit_toggle',
		//	'dep_val'	=> array(true)
		//);

		$swp_options['options']['swp_display']['yummly_divider'] = array(
			'type'		=> 'divider'
		);

		$swp_options['options']['swp_display']['yummly_cat_title'] = array(
			'type' 		=> 'title',
			'content' 	=> __( 'Yummly Display Control' ),
			'premium'	=> true
		);

		$swp_options['options']['swp_display']['yummly_cat_description'] = array(
			'type' 		=> 'paragraph',
			'content' 	=> __( 'If you would like the Yummly button to only display on posts of a specific category or tag, enter the category or tag name below (e.g "Recipe"). Leave blank to display the button on all posts.' ),
			'premium'	=> true
		);

		$swp_options['options']['swp_display']['yummly_column_labels'] = array(
			'type'		=> 'column_labels',
			'columns'	=> 3,
			'column_1'	=> '',
			'column_2'	=> __( 'Choose Category' ),
			'column_3'	=> __( 'Choose Tag' ),
			'premium'	=> true
		);

		$swp_options['options']['swp_display']['yummly_terms'] = array(
			'type'		=> 'input',
			'name'		=> __( 'Yummly Terms' ),
			'primary'	=> 'yummly_categories',
			'secondary' => 'yummly_tags',
			'premium'	=> true
		);

		return $swp_options;
	}

	/**
	 * swp_options_styles An array of options for the styles tab of the options page
	 * @since 	2.0.0
	 * @param  	array $swp_options The array of options
	 * @return 	array $swp_options The modified array of options
	 */
	function swp_options_styles($swp_options) {

		// Declare the Display Settings tab and tab name
		$swp_options['tabs']['links']['swp_styles'] = __( 'Styles' );

		$swp_options['options']['swp_styles'] = array(
			'visual_options_title' => array(
				'type' 		=> 'title',
				'content' 	=> __( 'Visual Options' ),
				'premium'	=> true
			),
			'visual_options_description' => array(
				'type' 		=> 'paragraph',
				'content' 	=> __( 'Use the settings below to customize the look of your share buttons.' ),
				'premium'	=> true
			),
			'visualTheme' => array(
				'type'		=> 'select',
				'size'		=> 'two-fourths',
				'name'		=> __( 'Button Shape' ),
				'content'	=> array(
					'flatFresh' 	=> __( 'Flat & Fresh' ),
					'leaf' 			=> __( 'A Leaf on the Wind' ),
					'shift' 		=> __( 'Shift' ),
					'pill' 			=> __( 'Pills' ),
					'threeDee' 		=> __( 'Three-Dee' ),
					'connected' 	=> __( 'Connected' )
				),
				'default' => 'flatFresh',
				'premium'	=> true
			),
			'buttonSize' => array(
				'type' => 'select',
				'size' => 'two-fourths',
				'name' => __( 'Button Size' ),
				'content' => array(
					'1.4' => __( '140%' ),
					'1.3' => __( '130%' ),
					'1.2' => __( '120%' ),
					'1.1' => __( '110%' ),
					'1'   => __( '100%' ),
					'0.9' => __( '90%' ),
					'0.8' => __( '80%' ),
					'0.7' => __( '70%' )
				),
				'default' => '1',
				'premium'	=> true
			 ),
			'dColorSet' => array(
				'type' => 'select',
				'size' => 'two-fourths',
				'name' => __( 'Default Color Set' ),
				'content' => array(
					'fullColor' 		=> __( 'Full Color' ),
					'lightGray' 		=> __( 'Light Gray' ),
					'mediumGray'		=> __( 'Medium Gray' ),
					'darkGray' 			=> __( 'Dark Gray' ),
					'lgOutlines' 		=> __( 'Light Gray Outlines' ),
					'mdOutlines'		=> __( 'Medium Gray Outlines' ),
					'dgOutlines' 		=> __( 'Dark Gray Outlines' ),
					'colorOutlines' 	=> __( 'Color Outlines' ),
					'customColor' 		=> __( 'Custom Color' ),
					'ccOutlines' 		=> __( 'Custom Color Outlines' )
				),
				'default' => 'fullColor',
				'premium'	=> true
			),
			'oColorSet' => array(
				'type' => 'select',
				'size' => 'two-fourths',
				'name' => __( 'Hover Color Set' ),
				'content' => array(
					'fullColor' 		=> __( 'Full Color' ),
					'lightGray' 		=> __( 'Light Gray' ),
					'mediumGray'		=> __( 'Medium Gray' ),
					'darkGray' 			=> __( 'Dark Gray' ),
					'lgOutlines' 		=> __( 'Light Gray Outlines' ),
					'mdOutlines'		=> __( 'Medium Gray Outlines' ),
					'dgOutlines' 		=> __( 'Dark Gray Outlines' ),
					'colorOutlines' 	=> __( 'Color Outlines' ),
					'customColor' 		=> __( 'Custom Color' ),
					'ccOutlines' 		=> __( 'Custom Color Outlines' )
				),
				'default' => 'fullColor',
				'premium'	=> true
			),
			'iColorSet' => array(
				'type' => 'select',
				'size' => 'two-fourths',
				'name' => __( 'Single Button Hover' ),
				'content' => array(
					'fullColor' 		=> __( 'Full Color' ),
					'lightGray' 		=> __( 'Light Gray' ),
					'mediumGray'		=> __( 'Medium Gray' ),
					'darkGray' 			=> __( 'Dark Gray' ),
					'lgOutlines' 		=> __( 'Light Gray Outlines' ),
					'mdOutlines'		=> __( 'Medium Gray Outlines' ),
					'dgOutlines' 		=> __( 'Dark Gray Outlines' ),
					'colorOutlines' 	=> __( 'Color Outlines' ),
					'customColor' 		=> __( 'Custom Color' ),
					'ccOutlines' 		=> __( 'Custom Color Outlines' )
				),
				'default' => 'fullColor',
				'premium'	=> true
			),
			//'maximum_width' => array(
			//	'type'		=> 'input',
			//	'size'		=> 'two-fourths',
			//	'name'		=> 'Maximum Width',
			//	'premium'	=> true
			//),
			'customColor' => array(
				'type'		=> 'input',
				'size'		=> 'two-fourths',
				'name'		=> __( 'Custom Color' ),
				'default'	=> '#FFFFFF',
				'premium'	=> true
			),
			'buttonFloat' 	=> array(
				'type' 		=> 'select',
				'size'		=> 'two-fourths',
				'name' 		=> __( 'Button Alignment' ),
				'content' 	=> array(
					'fullWidth' => __( 'Full Width' ),
					'left' 		=> __( 'Left' ),
					'right'   	=> __( 'Right' ),
					'center' 	=> __( 'Center' )
				),
				'default' 	=> 'fullWidth',
				'dep' 		=> 'buttonSize',
				'dep_val'	=> array('0.9','0.8','0.7'),
				'premium'	=> true
			 ),
			'buttons_preview' => array(
				'type' => 'html',
				'divider' => true,
				'content' => '<div class="nc_socialPanel swp_flatFresh swp_d_fullColor swp_i_fullColor swp_o_fullColor" data-position="both" data-float="floatNone" data-count="6" data-floatColor="#ffffff" data-scale="1" data-align="fullWidth"><div class="nc_tweetContainer googlePlus" data-id="2"><a target="_blank" href="https://plus.google.com/share?url=http%3A%2F%2Fwfa.re%2F1W28voz" data-link="https://plus.google.com/share?url=http%3A%2F%2Fwfa.re%2F1W28voz" class="nc_tweet"><span class="iconFiller"><span class="spaceManWilly"><i class="sw sw-google-plus"></i><span class="swp_share"> ' . __( '+1','social-warfare' ) . '</span></span></span><span class="swp_count">1.2K</span></a></div><div class="nc_tweetContainer twitter" data-id="3"><a href="https://twitter.com/share?original_referer=/&text=Ultimate+Social+Share+%23WordPress+plugin%21+Beautiful%2C+super+fast+%26+more+http%3A%2F%2Fwarfareplugins.com+pic.twitter.com%2FA2zcCJwZtO&url=/&via=WarfarePlugins" data-link="https://twitter.com/share?original_referer=/&text=Ultimate+Social+Share+%23WordPress+plugin%21+Beautiful%2C+super+fast+%26+more+http%3A%2F%2Fwarfareplugins.com+pic.twitter.com%2FA2zcCJwZtO&url=/&via=WarfarePlugins" class="nc_tweet"><span class="iconFiller"><span class="spaceManWilly"><i class="sw sw-twitter"></i><span class="swp_share"> ' . __( 'Tweet','social-warfare' ) . '</span></span></span><span class="swp_count">280</span></a></div><div class="nc_tweetContainer nc_pinterest" data-id="6"><a data-link="https://pinterest.com/pin/create/button/?url=https://warfareplugins.com/&media=https%3A%2F%2Fwarfareplugins.com%2Fwp-content%2Fuploads%2Fget-content-shared-735x1102.jpg&description=Customize+your+Pinterest+sharing+options%2C+create+easy+%22click+to+tweet%22+buttons+within+your+blog+posts%2C+beautiful+sharing+buttons+and+more.+Social+Warfare+is+the+ultimate+social+sharing+arsenal+for+WordPress%21" class="nc_tweet" data-count="0"><span class="iconFiller"><span class="spaceManWilly"><i class="sw sw-pinterest"></i><span class="swp_share"> ' . __( 'Pin','social-warfare' ) . '</span></span></span><span class="swp_count">104</span></a></div><div class="nc_tweetContainer swp_fb" data-id="4"><a target="_blank" href="http://www.facebook.com/share.php?u=http%3A%2F%2Fwfa.re%2F1W28vov" data-link="http://www.facebook.com/share.php?u=http%3A%2F%2Fwfa.re%2F1W28vov" class="nc_tweet"><span class="iconFiller"><span class="spaceManWilly"><i class="sw sw-facebook"></i><span class="swp_share"> ' . __( 'Share','social-warfare' ) . '</span></span></span><span class="swp_count">157</span></a></div><div class="nc_tweetContainer linkedIn" data-id="5"><a target="_blank" href="https://www.linkedin.com/cws/share?url=http%3A%2F%2Fwfa.re%2F1W28twH" data-link="https://www.linkedin.com/cws/share?url=http%3A%2F%2Fwfa.re%2F1W28twH" class="nc_tweet"><span class="iconFiller"><span class="spaceManWilly"><i class="sw sw-linkedin"></i><span class="swp_share"> ' . __( 'Share','social-warfare' ) . '</span></span></span><span class="swp_count">51</span></a></div><div class="nc_tweetContainer totes totesalt" data-id="6" ><span class="swp_count"><span class="swp_label">Shares</span> 1.8K</span></div></div>'
			),
			'total_counts_title' => array(
				'type'	=> 'title',
				'content' => __( 'Total Counts' )
			),
			'total_counts_description' => array(
				'type' 		=> 'paragraph',
				'content' 	=> __( 'Customize how the "Total Shares" section of your share buttons look.' )
			),
			'swDecimals' => array(
				'type' => 'select',
				'name' => __( 'Decimal Places' ),
				'size' => 'two-fourths',
				'content' => array(
					'0' => __( 'Zero' ),
					'1' => __( 'One' ),
					'2' => __( 'Two' )
				),
				'default' => '0'
			 ),
			'swp_decimal_separator' => array(
				'type' => 'select',
				'size' => 'two-fourths',
				'name' => __( 'Decimal Separator' ),
				'content' => array(
					'period' => __( 'Period' ),
					'comma' => __( 'Comma' )
				),
				'default' => 'period'
			 ),
			 'swTotesFormat' => array(
				'type' => 'select',
				'size' => 'two-fourths',
				'name' => __( 'Alignment' ),
				'content' => array(
					'totesAlt'		=>	__( 'Right' ),
					'totesAltLeft'	=>	__( 'Left' )
				),
				'default' => 'totesAlt'
			),
			'total_counts_divider' => array(
				'type' => 'divider'
			),
			'float' => array(
				'type'			=> 'checkbox',
				'title' 		=> __( 'Floating Share Buttons' ),
				'description' 	=> __( 'If you would like to activate floating share buttons, turn this on.' ),
				'size'			=> 'four-fourths'
			),
			'total_floating_description' => array(
				'type' 		=> 'paragraph',
				'content' 	=> __( 'If you would like to activate floating share buttons, turn this on.' )
			),
			'floatOption' => array(
				'type' => 'select',
				'size' => 'two-fourths',
				'name' => __( 'Float Position' ),
				'content' => array(
					'top' => __( 'Top of the Page' ),
					'bottom' => __( 'Bottom of the Page' ),
					'left' => __( 'On the left side of the page' )
				),
				'default' => 'bottom',
				'dep' 		=> 'float',
				'dep_val'	=> array(true)
			),
			'swp_float_scr_sz' => array(
				'type' => 'input',
				'size' => 'two-fourths',
				'name' => __( 'Minimum Screen Width' ),
				'default' => '1100',
				'dep' 		=> 'floatOption',
				'dep_val'	=> array('left')
			),
			'sideReveal' => array(
				'type' => 'select',
				'size' => 'two-fourths',
				'name' => __( 'Transition' ),
				'content' => array(
					'slide' 			=> __( 'Slide In / Slide Out' ),
					'fade' 				=> __( 'Fade In / Fade Out' )
				),
				'default' => 'slide',
				'dep' 		=> 'floatOption',
				'dep_val'	=> array('left')
			),
			'floatLeftMobile' => array(
				'type' => 'select',
				'name' => __( 'On Mobile' ),
				'size' => 'two-fourths',
				'content' => array(
					'bottom' 	=> __( 'Bottom of Screen' ),
					'off' 		=> __( 'Off' )
				),
				'default' => 'bottom',
				'dep' 		=> 'floatOption',
				'dep_val'	=> array('left'),
				'premium'	=> true
			),
			'floatStyle' => array(
				'type' => 'select',
				'name' => __( 'Button Shape' ),
				'size' => 'two-fourths',
				'content' => array(
					'default' => __( 'Buttons' ),
					'boxed' => __( 'Boxes' )
				),
				'default' => 'boxed',
				'dep' 		=> 'floatOption',
				'dep_val'	=> array('left'),
				'premium'	=> true
			),
			'floatStyleSource' => array(
				'type' => 'checkbox',
				'size' => 'two-fourths',
				'content' => __( 'Inherit Visual Options' ),
				'default' => '1',
				'dep' 		=> 'floatOption',
				'dep_val'	=> array('left'),
				'premium'	=> true
			),
			'sideDColorSet' => array(
				'type' => 'select',
				'size' => 'two-fourths',
				'name' => __( 'Default Color Set' ),
				'content' => array(
					'fullColor' 		=> __( 'Full Color' ),
					'lightGray' 		=> __( 'Light Gray' ),
					'mediumGray'		=> __( 'Medium Gray' ),
					'darkGray' 			=> __( 'Dark Gray' ),
					'lgOutlines' 		=> __( 'Light Gray Outlines' ),
					'mdOutlines'		=> __( 'Medium Gray Outlines' ),
					'dgOutlines' 		=> __( 'Dark Gray Outlines' ),
					'colorOutlines' 	=> __( 'Color Outlines' ),
					'customColor' 		=> __( 'Custom Color' ),
					'ccOutlines' 		=> __( 'Custom Color Outlines' )
				),
				'default' => 'fullColor',
				'dep' 		=> 'floatStyleSource',
				'dep_val'	=> array(false)
			),
			'sideOColorSet' => array(
				'type' => 'select',
				'size' => 'two-fourths',
				'name' => __( 'Hover Color Set' ),
				'content' => array(
					'fullColor' 		=> __( 'Full Color' ),
					'lightGray' 		=> __( 'Light Gray' ),
					'mediumGray'		=> __( 'Medium Gray' ),
					'darkGray' 			=> __( 'Dark Gray' ),
					'lgOutlines' 		=> __( 'Light Gray Outlines' ),
					'mdOutlines'		=> __( 'Medium Gray Outlines' ),
					'dgOutlines' 		=> __( 'Dark Gray Outlines' ),
					'colorOutlines' 	=> __( 'Color Outlines' ),
					'customColor' 		=> __( 'Custom Color' ),
					'ccOutlines' 		=> __( 'Custom Color Outlines' )
				),
				'default' => 'fullColor',
				'dep' 		=> 'floatStyleSource',
				'dep_val'	=> array(false)
			),
			'sideIColorSet' => array(
				'type' => 'select',
				'size' => 'two-fourths',
				'name' => __( 'Single Button Hover' ),
				'content' => array(
					'fullColor' 		=> __( 'Full Color' ),
					'lightGray' 		=> __( 'Light Gray' ),
					'mediumGray'		=> __( 'Medium Gray' ),
					'darkGray' 			=> __( 'Dark Gray' ),
					'lgOutlines' 		=> __( 'Light Gray Outlines' ),
					'mdOutlines'		=> __( 'Medium Gray Outlines' ),
					'dgOutlines' 		=> __( 'Dark Gray Outlines' ),
					'colorOutlines' 	=> __( 'Color Outlines' ),
					'customColor' 		=> __( 'Custom Color' ),
					'ccOutlines' 		=> __( 'Custom Color Outlines' )
				),
				'default' => 'fullColor',
				'dep' 		=> 'floatStyleSource',
				'dep_val'	=> array(false)
			),
			'floatBgColor' => array(
				'type' => 'input',
				'size' => 'two-fourths',
				'name' => __( 'Background Color' ),
				'default' => '#ffffff',
				'dep' 		=> 'floatOption',
				'dep_val'	=> array('top','bottom')
			),
			'click_to_tweet_divider' => array(
				'type' => 'divider'
			),
			'click_to_tweet_title' => array(
				'type' => 'title',
				'content' => __( 'Click-to-Tweet Style' ),
				'premium' => true
			),
			'click_to_tweet_description' => array(
				'type' => 'paragraph',
				'content' => __( 'Select the default visual style for Click-to-Tweets on your site.' ),
				'premium' => true
			),
			'cttTheme' => array(
				'type' => 'select',
				'size' => 'two-thirds',
				'name' => __( 'Visual Theme' ),
				'content' => array(
					'style1' => __( 'Send Her My Love' ),
					'style2' => __( 'Roll With The Changes' ),
					'style3' => __( 'Free Bird' ),
					'style4' => __( 'Don\'t Stop Believin\'' ),
					'style5' => __( 'Thunderstruck' ),
					'style6' => __( 'Livin\' On A Prayer' ),
					'none' => __( 'None - Create Your Own CSS In Your Theme' )
				),
				'default' => 'style1',
				'premium'	=> true
			),
			'cttPreview' => array(
				'type' => 'html',
				'content' => '<a class="swp_CTT style1"  data-style="style1" href="https://twitter.com/share?text=We+couldn%27t+find+one+social+sharing+plugin+that+met+all+of+our+needs%2C+so+we+built+it+ourselves.&amp;url=http://warfareplugins.com&amp;via=warfareplugins" data-link="https://twitter.com/share?text=We+couldn%27t+find+one+social+sharing+plugin+that+met+all+of+our+needs%2C+so+we+built+it+ourselves.&amp;url=http://wfa.re/1PtqdNM&amp;via=WarfarePlugins" target="_blank"><span class="sw-click-to-tweet"><span class="sw-ctt-text">We couldn\'t find one social sharing plugin that met all of our needs, so we built it ourselves.</span><span class="sw-ctt-btn">Click To Tweet<i class="sw sw-twitter"></i></span></span></a>',
				'premium' => true
			)
		);

		// Return the options value
		return $swp_options;

	}


	/**
	 * swp_options_social_identity An array of options for the social identity tab of the options page
	 * @since 	2.0.0
	 * @param  	array $swp_options The array of options
	 * @return 	array $swp_options The modified array of options
	 */

	function swp_options_social_identity($swp_options) {

		// Declare the Display Settings tab and tab name
		$swp_options['tabs']['links']['swp_social_identity'] = 'Social Identity';

		$swp_options['options']['swp_social_identity'] = array(
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
			/*
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
			) */

		);

		return $swp_options;

	}

	/**
	 * swp_options_advanced An array of options for the advanced tab of the options page
	 * @since 	2.0.0
	 * @param  	array $swp_options The array of options
	 * @return 	array $swp_options The modified array of options
	 */

	function swp_options_advanced($swp_options) {

		// Declare the Display Settings tab and tab name
		$swp_options['tabs']['links']['swp_advanced'] = 'Advanced';

		$swp_options['options']['swp_advanced'] = array(
			'sniplyBuster' => array(
				'type'			=> 'checkbox',
				'title' 		=> 'Frame Buster',
				'description' 	=> 'If you want to stop content pirates from framing your content, turn this on.',
				'size'			=> 'two-thirds-advanced',
				'default'		=> true,
				'divider'		=> true
			),
			'linkShortening' => array(
				'type'			=> 'checkbox',
				'title' 		=> 'Bitly Link Shortening',
				'description' 	=> 'If you like to have all of your links automatically shortened, turn this on.',
				'size'			=> 'two-thirds-advanced',
				'default'		=> false,
				'premium'		=> true
			),
			'bitly_authentication' => array(
				'type'		=> 'authentication',
				'link'		=> 'https://bitly.com/oauth/authorize?client_id=96c9b292c5503211b68cf4ab53f6e2f4b6d0defb&state='.admin_url( 'admin-ajax.php' ).'&redirect_uri=https://warfareplugins.com/bitly_oauth.php',
				'name'		=> 'Connect Your Bitly Account',
				'divider'	=> true,
				'dependant'	=> 'bitly_access_token',
				'premium'	=> true
			),
			'analytics_title'	=> array(
				'type'		=> 'title',
				'content'	=> 'Analytics Tracking',
				'premium'	=> true
			),
			'analtycis_description' => array(
				'type'		=> 'paragraph',
				'content'	=> 'If you want to activate UTM tracking for shared URL, turn this on.',
				'premium'	=> true
			),
			'swp_click_tracking' => array(
				'type'			=> 'checkbox',
				'content' 		=> 'Button Click Tracking',
				'size'			=> 'two-thirds',
				'default'		=> false,
				'premium'		=> true
			),
			'googleAnalytics' => array(
				'type'			=> 'checkbox',
				'content' 		=> 'UTM Link Tracking',
				'size'			=> 'two-thirds',
				'default'		=> false,
				'premium'		=> true
			),
			'analyticsMedium' => array(
				'type'		=> 'input',
				'size'		=> 'two-thirds',
				'name'		=> 'UTM Medium',
				'default'	=> 'Social',
				'dep'		=> 'googleAnalytics',
				'dep_val'	=> array(true),
				'premium'	=> true
			),
			'analyticsCampaign' => array(
				'type'		=> 'input',
				'size'		=> 'two-thirds',
				'name'		=> 'UTM Campaign',
				'divider' 	=> true,
				'default'	=> 'SocialWarfare',
				'dep'		=> 'googleAnalytics',
				'dep_val'	=> array(true),
				'premium'	=> true
			),
			'share_recovery_title' => array(
				'type'		=> 'title',
				'content'	=> 'Share Recovery',
				'premium'	=> true
			),
			'share_recovery_description' => array(
				'type'		=> 'paragraph',
				'content'	=> 'If at any point you have changed permalink structures or have gone from http to https (SSL) then you will have undoubtedly lost all of your share counts. This tool allows you to recover them. See <a target="_blank" href="https://warfareplugins.com/support/recover-social-share-counts-after-changing-permalink-settings/">this guide</a> for more detailed instructions on how to use this feature.',
				'premium'	=> true
			),
			'recover_shares' => array(
				'type'		=> 'checkbox',
				'size'		=> 'two-thirds',
				'content'	=> 'Activate Share Recovery',
				'default'	=> false,
				'premium'	=> true
			),
			'recovery_format' 	=> array(
				'type' 				=> 'select',
				'name' 				=> 'Previous URL Format',
				'size'				=> 'two-thirds',
				'content' 			=> array(
					'unchanged'			=> 'Unchanged',
					'default' 			=> 'Plain',
					'day_and_name' 		=> 'Day and Name',
					'month_and_name' 	=> 'Month and Name',
					'numeric' 			=> 'Numeric',
					'post_name' 		=> 'Post Name',
					'custom'			=> 'Custom'
				),
				'default' 			=> 'unchanged',
				'dep'				=> 'recover_shares',
				'dep_val'			=> array(true),
				'premium'			=> true
			),
			'recovery_custom_format' => array(
				'type'		=> 'input',
				'size'		=> 'two-thirds',
				'name'		=> 'Custom Permalink Format',
				'dep'				=> 'recover_shares',
				'dep_val'			=> array(true),
				'premium'	=> true
			),
			'recovery_protocol'	=> array(
				'type'				=> 'select',
				'size'				=> 'two-thirds',
				'name'				=> 'Previous Connection Protocol',
				'content'			=> array(
					'unchanged'			=> 'Unchanged',
					'http'				=> 'http',
					'https'				=> 'https'
				),
				'default'			=> 'unchanged',
				'dep'				=> 'recover_shares',
				'dep_val'			=> array(true),
				'premium'	=> true
			),
			'recovery_prefix'	=> array(
				'type'				=> 'select',
				'size'				=> 'two-thirds',
				'name'				=> 'Previous Domain Prefix',
				'content'			=> array(
					'unchanged'			=> 'Unchanged',
					'www'				=> 'www',
					'nonwww'			=> 'non-www'
				),
				'default'			=> 'unchanged',
				'dep'				=> 'recover_shares',
				'dep_val'			=> array(true),
				'premium'	=> true
			),
			'recovery_subdomain' => array(
				'type' 		=> 'input',
				'size'		=> 'two-thirds',
				'name' 		=> 'Subdomain',
				'default' 	=> '',
				'divider'	=> false,
				'dep'		=> 'recover_shares',
				'dep_val'	=> array(true),
				'premium'	=> true
			),
			'cross_domain_recovery_description' => array(
				'type'		=> 'paragraph',
				'content'	=> 'If you\'ve migrated your website from one domain to another, fill in these two fields to activate cross-domain share recovery.',
				'premium'	=> true,
				'dep'		=> 'recover_shares',
				'dep_val'	=> array(true)
			),
			'former_domain' => array(
				'type' 		=> 'input',
				'size'		=> 'two-thirds',
				'name' 		=> 'Former Domain',
				'default' 	=> '',
				'divider'	=> false,
				'dep'		=> 'recover_shares',
				'dep_val'	=> array(true),
				'premium'	=> true
			),
			'current_domain' => array(
				'type' 		=> 'input',
				'size'		=> 'two-thirds',
				'name' 		=> 'Current Domain',
				'default' 	=> '',
				'divider'	=> true,
				'dep'		=> 'recover_shares',
				'dep_val'	=> array(true),
				'premium'	=> true
			),
			'caching_method_title' => array(
				'type'		=> 'title',
				'content'	=> 'Caching Method'
			),
			'caching_method_description' => array(
				'type'		=> 'paragraph',
				'content'	=> 'If you have advanced knowledge of caching configurations, you may want to choose your own below.'
			),
			'cacheMethod'		=> array(
				'type'				=> 'select',
				'size'				=> 'two-thirds',
				'name'				=> 'Cache Rebuild Method',
				'content'			=> array(
					'advanced'			=> 'Advanced Cache Triggering',
					'legacy'			=> 'Legacy Cache Rebuilding during Page Loads'
				),
				'default'			=> 'advanced'
			)
		);

		return $swp_options;
	};

	/**
	 * swp_options_registration An array of options for the registration tab of the options page
	 * @since 	2.0.0
	 * @param  	array $swp_options The array of options
	 * @return 	array $swp_options The modified array of options
	 */

	function swp_options_registration($swp_options) {

		// Declare the Display Settings tab and tab name
		$swp_options['tabs']['links']['swp_registration'] = 'Registration';

		$swp_options['options']['swp_registration'] = array(
			'plugin_registration' => array(
				'type'			=> 'plugin_registration',
				'divider'		=> true,
				'premium'	=> true
			),
			'activate_tweet_counts' => array(
				'type'			=> 'tweet_counts',
				'premium'		=> true
			)
		);

		return $swp_options;

	}

	/**
	 * Queue up the options filter functions
	 * @since 2.0.0
	 */

	add_filter('swp_options', 'swp_options_display' 			, 1 );
	add_filter('swp_options', 'swp_options_styles' 				, 2 );
	add_filter('swp_options', 'swp_options_social_identity'		, 3 );
	add_filter('swp_options', 'swp_options_advanced'			, 4 );
	add_filter('swp_options', 'swp_options_registration'		, 5 );

/**
 * swp_add_network_option A function for easily adding networks to the avialable options
 * @since 2.0.0
 * @param array $swp_options 	The array of available options
 * @param array $newOptionArray An array containing information about the new option we're adding
 * @return array $swp_options 	The modified array of available options
 */
function swp_add_network_option($swp_options,$newOptionArray) {

	$swp_options['options']['swp_display']['newOrderOfIcons']['content'][$key] = $newOptionArray[$key]['content'];

	// Return the option array or the world will explode
	return $swp_options;
}
