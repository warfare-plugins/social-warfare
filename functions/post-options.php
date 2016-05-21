<?php

/*****************************************************************
                                                                
           SETUP THE POST EDITOR OPTIONS         
                                                                
******************************************************************/

	function sw_get_post_types() {

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
	
		return $postTypes;

	};

	add_filter( 'rwmb_meta_boxes', 'nc_register_meta_boxes' );
	function nc_register_meta_boxes( $meta_boxes )
	{
		
		// Setup the prefix to avoid conflicts
		 $prefix = 'nc_';
		 $options = sw_get_user_options();
		 $postTypes = sw_get_post_types();
		 foreach($postTypes as $key => $value):
			$postType[] = $key;
		 endforeach;
		 $postType[] = 'page';
		 $postType[] = 'post';
		 
		 // Setup our meta box using an array
		 $meta_boxes[0] = array(
			  'id'       => 'socialWarfare',
			  'title'    => __('Social Warfare Custom Options','social-warfare'),
			  'pages'    => $postType,
			  'context'  => 'normal',
			  'priority' => 'high',
			  'fields' => array(
			  
			  		// Setup the social media image
					array(
						 'name'  => '<span class="dashicons dashicons-share"></span> '.__('Social Media Image','social-warfare'),
						 'desc'  => __('Add an image that is optimized for maximum exposure on Facebook, Google+ and LinkedIn. We recommend 1,200px by 628px.','social-warfare'),
						 'id'    => $prefix . 'ogImage',
						 'type'  => 'image_advanced',
						 'clone' => false,
						 'class' => $prefix . 'ogImageWrapper',
						 'max_file_uploads' => 1
					),
					
					// Setup the social media title
					array(
						 'name'  => '<span class="dashicons dashicons-share"></span> '. __('Social Media Title','social-warfare'),
						 'desc'  => __('Add a title that will populate the open graph meta tag which will be used when users share your content onto Facebook, LinkedIn, and Google+. If nothing is provided here, we will use the post title as a backup.','social-warfare'),
						 'id'    => $prefix . 'ogTitle',
						 'type'  => 'textarea',
						 'class' => $prefix . 'ogTitleWrapper',
						 'clone' => false,
					),
					
					// Setup the social media description
					array(
						 'name'  => '<span class="dashicons dashicons-share"></span> '.__('Social Media Description','social-warfare'),
						 'desc'  => __('Add a description that will populate the open graph meta tag which will be used when users share your content onto Facebook, LinkedIn, and Google Plus.','social-warfare'),
						 'id'    => $prefix . 'ogDescription',
						 'class' => $prefix . 'ogDescriptionWrapper',
						 'type'  => 'textarea',
						 'clone' => false,
					),
					
					// Divider
					array(
						 'name'  => 'divider',
						 'id'    => 'divider',
						 'type'  => 'divider'
					),
					
					// Setup the pinterest optimized image
					array(
						 'name'  => '<i class="sw sw-pinterest"></i> '.__('Pinterest Image','social-warfare'),
						 'desc'  => __('Add an image that is optimized for maximum exposure on Pinterest. We recommend using an image that is formatted in a 2:3 aspect ratio like 735x1102.','social-warfare'),
						 'id'    => $prefix . 'pinterestImage',
						 'class' => $prefix . 'pinterestImageWrapper',
						 'type'  => 'image_advanced',
						 'clone' => false,
						 'max_file_uploads' => 1
					),
					
					// Setup the pinterest description
					array(
						 'name'  => '<i class="sw sw-pinterest"></i>'. __('Pinterest Description','social-warfare'),
						 'desc'  => __('Craft a customized description that will be used when this post is shared on Pinterest. Leave this blank to use the title of the post.','social-warfare'),
						 'id'    => $prefix . 'pinterestDescription',
						 'class' => $prefix . 'pinterestDescriptionWrapper',
						 'type'  => 'textarea',
						 'clone' => false,
					),
					
					// Setup the Custom Tweet box
					array(
						 'name'  => '<i class="sw sw-twitter"></i> '.__('Custom Tweet','social-warfare'),
						 'desc'  => ($options['twitterID'] ? sprintf(__('If this is left blank your post title will be used. Based on your username (@%1$s), <span class="tweetLinkSection">a link being added,</span> and the current content above, your tweet has %2$s characters remaining.','social-warfare'),str_replace('@','',$options['twitterID']),'<span class="counterNumber">140</span>') : sprintf(__('If this is left blank your post title will be used. <span ="tweetLinkSection">Based on a link being added, and</span> the current content above, your tweet has %s characters remaining.','social-warfare'),'<span class="counterNumber">140</span>')),
						 'id'    => $prefix . 'customTweet',
						 'class' => $prefix . 'customTweetWrapper',
						 'type'  => 'textarea',
						 'clone' => false,
					),
					
					// Set up the location on post options
					array(
						 'name'  => '<span class="dashicons dashicons-randomize"></span> '.__('Horizontal Buttons Location','social-warfare'),
						 'id'    => $prefix . 'postLocation',
						 'class' => $prefix . 'postLocationWrapper',
						 'type'  => 'select',
						 'options' => array(
						 	'default' => 'Default',
							'above'=>'Above the Content',
							'below' => 'Below the Content', 
							'both' => 'Both Above and Below the Content', 
							'none' => 'None/Manual Placement'),
						 'clone' => false,
						 'std'	 => 'default'
					)
			  )
		 );
		 
		 
		 $meta_boxes[0]['fields'][] = array(
						 'name'  => '<span class="dashicons dashicons-randomize"></span> '.__('Side Floating Buttons Location','social-warfare'),
						 'id'    => $prefix . 'floatLocation',
						 'class' => $prefix . 'floatLocationWrapper',
						 'type'  => 'select',
						 'options' => array(
						 	'default' => 'Default',
						 	'on' => 'On',
							'off'=> 'Off'
						 ),
						 'clone' => false,
						 'std'	=> 'default',
		 
		 );
		 
		 $meta_boxes[0]['fields'][] = array(
						 'name'  => 'divider2',
						 'id'    => 'divider2',
						 'type'  => 'divider'
					);
					
					// Twitter ID
		 $meta_boxes[0]['fields'][] =array(
						 'name'  => $options['twitterID'],
						 'id'    => 'twitterID',
						 'class' => 'twitterIDWrapper',
						 'type'  => 'hidden',
						 'std'   => $options['twitterID']
					);
		 
		 // Return the meta boxes
		 return $meta_boxes;
	}