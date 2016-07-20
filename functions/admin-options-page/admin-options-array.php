<?php

/****************************************************************************************
*																						*
*	The Social Warfare Display Settings													*
*																						*
*****************************************************************************************/
	function swp_options_display($swp_options) {

		$icons_array = array(
			'type'		=> 'buttons'
		);
		$icons_array = apply_filters( 'swp_button_options' , $icons_array );

		// Declare the Options Tab and Tab Name
		$swp_options['tabs']['links']['swp_display'] = 'Display';

		// Declare the content that goes on this options page
		$swp_options['options']['swp_display'] = array(
			'social_networks_title' => array(
				'type' 		=> 'title',
				'content' 	=> 'Social Networks'
			),
			'social_networks_subtitle' => array(
				'type'		=> 'paragraph',
				'content'	=> 'Drag & Drop to activate and order your share buttons.'
			),
			'buttons' => $icons_array,
			'orderOfIconsSelect' => array(
				'type'		=> 'select',
				'size'		=> 'two-thirds',
				'content'	=> array(
					'manual' 	=> 'Sort Manually Using Drag & Drop Above',
					'dynamic' 	=> 'Sort Dyanamically By Order Of Most Shares'
				),
				'default'	=> 'manual',
				'name'		=> 'Button Ordering',
				'divider'	=> true,
				'premium'	=> true
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
				'default'	=> true,
				'premium'	=> false
			),
			'totes' => array(
				'type'		=> 'checkbox',
				'size'		=> 'two-thirds',
				'content'	=> 'Total Counts',
				'default'	=> true,
				'premium'	=> false
			),
			'minTotes' => array(
				'type'		=> 'input',
				'size'		=> 'two-thirds',
				'name'	=> 'Minimum Shares',
				'default'	=> 0,
				'divider'	=> true,
				'premium'	=> true
			),
			'swp_twitter_card' => array(
				'type'		=> 'checkbox',
				'size'		=> 'two-thirds',
				'content'	=> 'Twitter Cards',
				'header'	=> true,
				'divider'	=> true,
				'default'	=> true,
				'premium'	=> true
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
				'column_1'	=> 'Post Type',
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
				$swp_options['options']['swp_display']['location_'.$postType] = array(
					'type'		=> 'select',
					'name'		=> ucfirst($postType),
					'primary'	=> 'location_'.$postType,
					'secondary' => 'float_location_'.$postType,
					'content'	=> $contentLocations,
					'content_2'	=> array(
						'on'	=> 'On',
						'off'	=> 'Off',
					),
					'default'	=> 'both',
					'default_2' => 'on'
				);
			endforeach;
		endif;

		$swp_options['options']['swp_display']['locationSite'] = array(
			'type'		=> 'select',
			'name'		=> 'Archive & Categories',
			'content'	=> $contentLocations,
			'default'	=> 'below',
			'size'		=> 'two-thirds'
		);

		$swp_options['options']['swp_display']['pinit_divider'] = array(
			'type'		=> 'divider'			
		);

		$swp_options['options']['swp_display']['pinit_title'] = array(
			'type' 		=> 'title',
			'content' 	=> 'Image Hover Pin Button',
			'premium'	=> true
		);

		$swp_options['options']['swp_display']['pinit_description'] = array(
			'type' 		=> 'paragraph',
			'content' 	=> 'If you would like a "Pin" button to appear on images when users hover over them, activate this.',
			'premium'	=> true
		);
		
		$swp_options['options']['swp_display']['pinit_toggle'] = array(
			'type'		=> 'checkbox',
			'size'		=> 'two-thirds',
			'content'	=> 'Pinit Button',
			'default'	=> true,
			'premium'	=> true
		);
		
		$swp_options['options']['swp_display']['pinit_location_horizontal'] = array(
			'type'		=> 'select',
			'name'		=> 'Horizontal Location',
			'content'	=> array(
				'left' 		=> 'Left',
				'center'	=> 'Center',
				'right' 	=> 'Right'
			),
			'default'	=> 'center',
			'size'		=> 'two-thirds',
			'premium'	=> true,
			'dep' 		=> 'pinit_toggle',
			'dep_val'	=> array(true)
		);
		
		$swp_options['options']['swp_display']['pinit_location_vertical'] = array(
			'type'		=> 'select',
			'name'		=> 'Vertical Location',
			'content'	=> array(
				'top' 		=> 'Top',
				'middle'	=> 'Middle',
				'bottom' 	=> 'Bottom'
			),
			'default'	=> 'top',
			'size'		=> 'two-thirds',
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
			'content' 	=> 'Yummly Display Control',
			'premium'	=> true
		);

		$swp_options['options']['swp_display']['yummly_cat_description'] = array(
			'type' 		=> 'paragraph',
			'content' 	=> 'If you would like the Yummly to display on a specific category or tag, choose it below.',
			'premium'	=> true
		);
		
		$swp_options['options']['swp_display']['yummly_column_labels'] = array(
			'type'		=> 'column_labels',
			'columns'	=> 3,
			'column_1'	=> '',
			'column_2'	=> 'Choose Category',
			'column_3'	=> 'Choose Tag',
			'premium'	=> true
		);
		
		$raw_tags = get_terms( array('taxonomy' => 'post_tag' , 'hide_empty' => false ) );
		if( !empty( $raw_tags ) ):
			foreach ( $raw_tags as $tag ):
				$tags[$tag->slug] = $tag->name;
			endforeach;
		endif;
		
		$raw_cats = get_terms( array('taxonomy' => 'category' , 'hide_empty' => false ) );
		if( !empty ( $raw_cats ) ):
			foreach ( $raw_cats as $cat ):
				$cats[$cat->slug] = $cat->name;
			endforeach;
		endif;
		
		$swp_options['options']['swp_display']['yummly_terms'] = array(
			'type'		=> 'select',
			'name'		=> 'Yummly Terms',
			'primary'	=> 'yummly_categories',
			'secondary' => 'yummly_tags',
			'content'	=> $cats,
			'content_2'	=> $tags,
			'premium'	=> true
		);
		
		return $swp_options;
	}

/****************************************************************************************
*																						*
*	The Styles Tab																		*
*																						*
*****************************************************************************************/

	function swp_options_styles($swp_options) {

		// Declare the Display Settings tab and tab name
		$swp_options['tabs']['links']['swp_styles'] = 'Styles';

		$swp_options['options']['swp_styles'] = array(
			'visual_options_title' => array(
				'type' 		=> 'title',
				'content' 	=> 'Visual Options',
				'premium'	=> true
			),
			'visual_options_description' => array(
				'type' 		=> 'paragraph',
				'content' 	=> 'Use the settings below to customize the look of your share buttons.',
				'premium'	=> true
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
				'default' => 'flatFresh',
				'premium'	=> true
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
				'default' => '1',
				'premium'	=> true
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
				'default' => 'fullColor',
				'premium'	=> true
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
				'default' => 'fullColor',
				'premium'	=> true
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
				'name'		=> 'Custom Color',
				'default'	=> '#FFFFFF',
				'premium'	=> true
			),
			'buttonFloat' 	=> array(
				'type' 		=> 'select',
				'size'		=> 'two-fourths',
				'name' 		=> 'Button Alignment',
				'content' 	=> array(
					'fullWidth' => 'Full Width',
					'left' 		=> 'Left',
					'right'   	=> 'Right',
					'center' 	=> 'Center'
				),
				'default' 	=> 'fullWidth',
				'dep' 		=> 'buttonSize',
				'dep_val'	=> array('0.9','0.8','0.7'),
				'premium'	=> true
			 ),
			'buttons_preview' => array(
				'type' => 'html',
				'divider' => true,
				'content' => '<div class="nc_socialPanel swp_flatFresh swp_d_fullColor swp_i_fullColor swp_o_fullColor" data-position="both" data-float="floatNone" data-count="6" data-floatColor="#ffffff" data-scale="1" data-align="fullWidth"><div class="nc_tweetContainer googlePlus" data-id="2"><a target="_blank" href="https://plus.google.com/share?url=http%3A%2F%2Fwfa.re%2F1W28voz" data-link="https://plus.google.com/share?url=http%3A%2F%2Fwfa.re%2F1W28voz" class="nc_tweet"><span class="iconFiller"><span class="spaceManWilly"><i class="sw sw-google-plus"></i><span class="swp_share"> +1</span></span></span><span class="swp_count">1.2K</span></a></div><div class="nc_tweetContainer twitter" data-id="3"><a href="https://twitter.com/share?original_referer=/&text=Ultimate+Social+Share+%23WordPress+plugin%21+Beautiful%2C+super+fast+%26+more+http%3A%2F%2Fwarfareplugins.com+pic.twitter.com%2FA2zcCJwZtO&url=/&via=WarfarePlugins" data-link="https://twitter.com/share?original_referer=/&text=Ultimate+Social+Share+%23WordPress+plugin%21+Beautiful%2C+super+fast+%26+more+http%3A%2F%2Fwarfareplugins.com+pic.twitter.com%2FA2zcCJwZtO&url=/&via=WarfarePlugins" class="nc_tweet"><span class="iconFiller"><span class="spaceManWilly"><i class="sw sw-twitter"></i><span class="swp_share"> Tweet</span></span></span><span class="swp_count">280</span></a></div><div class="nc_tweetContainer nc_pinterest" data-id="6"><a data-link="https://pinterest.com/pin/create/button/?url=https://warfareplugins.com/&media=https%3A%2F%2Fwarfareplugins.com%2Fwp-content%2Fuploads%2Fget-content-shared-735x1102.jpg&description=Customize+your+Pinterest+sharing+options%2C+create+easy+%22click+to+tweet%22+buttons+within+your+blog+posts%2C+beautiful+sharing+buttons+and+more.+Social+Warfare+is+the+ultimate+social+sharing+arsenal+for+WordPress%21" class="nc_tweet" data-count="0"><span class="iconFiller"><span class="spaceManWilly" style="width:55px;"><i class="sw sw-pinterest"></i><span class="swp_share"> Pin</span></span></span><span class="swp_count">104</span></a></div><div class="nc_tweetContainer swp_fb" data-id="4"><a target="_blank" href="http://www.facebook.com/share.php?u=http%3A%2F%2Fwfa.re%2F1W28vov" data-link="http://www.facebook.com/share.php?u=http%3A%2F%2Fwfa.re%2F1W28vov" class="nc_tweet"><span class="iconFiller"><span class="spaceManWilly"><i class="sw sw-facebook"></i><span class="swp_share"> Share</span></span></span><span class="swp_count">157</span></a></div><div class="nc_tweetContainer linkedIn" data-id="5"><a target="_blank" href="https://www.linkedin.com/cws/share?url=http%3A%2F%2Fwfa.re%2F1W28twH" data-link="https://www.linkedin.com/cws/share?url=http%3A%2F%2Fwfa.re%2F1W28twH" class="nc_tweet"><span class="iconFiller"><span class="spaceManWilly"><i class="sw sw-linkedin"></i><span class="swp_share"> Share</span></span></span><span class="swp_count">51</span></a></div><div class="nc_tweetContainer totes totesalt" data-id="6" ><span class="swp_count"><span class="swp_label">Shares</span> 1.8K</span></div></div>'
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
			'swp_decimal_separator' => array(
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
				'default' => 'bottom',
				'dep' 		=> 'float',
				'dep_val'	=> array(true)
			),
			'swp_float_scr_sz' => array(
				'type' => 'input',
				'size' => 'two-fourths',
				'name' => 'Minimum Screen Width',
				'default' => '1100',
				'dep' 		=> 'floatOption',
				'dep_val'	=> array('left')
			),
			'sideReveal' => array(
				'type' => 'select',
				'size' => 'two-fourths',
				'name' => 'Transition',
				'content' => array(
					'slide' 			=> 'Slide In / Slide Out',
					'fade' 				=> 'Fade In / Fade Out'
				),
				'default' => 'slide',
				'dep' 		=> 'floatOption',
				'dep_val'	=> array('left')
			),
			'floatStyle' => array(
				'type' => 'select',
				'name' => 'Button Shape',
				'size' => 'two-fourths',
				'content' => array(
					'default' => 'Buttons',
					'boxed' => 'Boxes'
				),
				'default' => 'boxed',
				'dep' 		=> 'floatOption',
				'dep_val'	=> array('left'),
				'premium'	=> true
			),
			'floatStyleSource' => array(
				'type' => 'checkbox',
				'size' => 'two-fourths',
				'content' => 'Inherit Visual Options',
				'default' => '1',
				'dep' 		=> 'floatOption',
				'dep_val'	=> array('left'),
				'premium'	=> true
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
				'default' => 'fullColor',
				'dep' 		=> 'floatStyleSource',
				'dep_val'	=> array(false)
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
				'default' => 'fullColor',
				'dep' 		=> 'floatStyleSource',
				'dep_val'	=> array(false)
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
				'default' => 'fullColor',
				'dep' 		=> 'floatStyleSource',
				'dep_val'	=> array(false)
			),
			'floatBgColor' => array(
				'type' => 'input',
				'size' => 'two-fourths',
				'name' => 'Background Color',
				'default' => '#ffffff',
				'dep' 		=> 'floatOption',
				'dep_val'	=> array('top','bottom')
			),
			'click_to_tweet_divider' => array(
				'type' => 'divider'
			),
			'click_to_tweet_title' => array(
				'type' => 'title',
				'content' => 'Click-to-Tweet Style',
				'premium' => true
			),
			'click_to_tweet_description' => array(
				'type' => 'paragraph',
				'content' => 'Select the default visual style for Click-to-Tweets on your site.',
				'premium' => true
			),
			'cttTheme' => array(
				'type' => 'select',
				'size' => 'two-thirds',
				'name' => 'Visual Theme',
				'content' => array(
					'style1' => 'Send Her My Love',
					'style2' => 'Roll With The Changes',
					'style3' => 'Free Bird',
					'style4' => 'Don\'t Stop Believin\'',
					'style5' => 'Thunderstruck',
					'style6' => 'Livin\' On A Prayer',
					'none' => 'None - Create Your Own CSS In Your Theme'),
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


/****************************************************************************************
*																						*
*	Queue up the Options Filters														*
*																						*
*****************************************************************************************/

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

/****************************************************************************************
*																						*
*	The Advanced Tab																	*
*																						*
*****************************************************************************************/

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
				'content'	=> 'If at any point you have changed permalink structures or have gone from http to https (SSL) then you will have undoubtedly lost all of your share counts. This tool allows you to recover them.',
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
				'divider'	=> true,
				'dep'				=> 'recover_shares',
				'dep_val'			=> array(true),
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

/****************************************************************************************
*																						*
*	The Advanced Tab																	*
*																						*
*****************************************************************************************/

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

/****************************************************************************************
*																						*
*	Queue up the Options Filters														*
*																						*
*****************************************************************************************/

	add_filter('swp_options', 'swp_options_display' 			, 1 );
	add_filter('swp_options', 'swp_options_styles' 			, 2 );
	add_filter('swp_options', 'swp_options_social_identity'	, 3 );
	add_filter('swp_options', 'swp_options_advanced'			, 4 );
	add_filter('swp_options', 'swp_options_registration'		, 5 );

/****************************************************************************************
*																						*
*	The Social Warfare Options Function	- Process the Options Array!					*
*																						*
*****************************************************************************************

	// Queue up the Social Warfare options hook
	// add_action('init' , 'swp_optionsClass' , 20);

	// The Options Function which relies on the Options Array above
	function swp_optionsClass() {

		// Fetch the Options Array - This is the swp_options filter hook
		global $swp_options;
		$swp_options = apply_filters('swp_options',$swp_options);

		// Initiate the Options Class with the config settings in the array
		$options_panel = new BF_Admin_Page_Class($swp_options['config']);

		// Open the Options Tabs Container
		$options_panel->OpenTabs_container('');

		// Execute the list of options tabs
		$options_panel->TabsListing($swp_options['tabs']);

		// Loop through the options tabs and build the options page
		foreach($swp_options['options'] as $tabName => $tabOptions):
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

function swp_add_option_after($swp_options,$tabName,$optionName,$newOptionArray) {

	// Locate the index of the option you want to insert next to
    $keyIndex = array_search(
        $optionName,
        array_keys( $swp_options['options'][$tabName] )
    );

    // Split the array at the location of the option above
    $first_array = array_splice (
        $swp_options['options'][$tabName],
        0,
        $keyIndex+1
    );

    // Merge the two parts of the split array with your option added in the middle
    $swp_options['options'][$tabName] = array_merge (
        $first_array,
        $newOptionArray,
        $swp_options['options'][$tabName]
    );

    // Return the option array or the world will explode
    return $swp_options;

}

function swp_add_language_option($swp_options,$langName,$langCode) {

	// Add our new language to the options page
	$swp_options['options']['displaySettings']['language']['content'][$langCode] = $langName;

	// Return the option array or the world will explode
	return $swp_options;

}
*/
function swp_add_network_option($swp_options,$newOptionArray) {
	
	$swp_options['options']['swp_display']['newOrderOfIcons']['content'][$key] = $newOptionArray[$key]['content'];

    // Return the option array or the world will explode
    return $swp_options;
}
