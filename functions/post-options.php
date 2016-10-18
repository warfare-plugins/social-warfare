<?php
/**
 * Add the custom meta boxes to all supported post types.
 *
 * @package   SocialWarfare\Functions
 * @copyright Copyright (c) 2016, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     1.0.0
 */

/**
 * Get the SWP supported post types.
 *
 * @return array $types A list of post type names.
 */
function swp_get_post_types() {
	$types = get_post_types(
		array(
			'public'   => true,
			'_builtin' => false,
		),
		'names'
	);

	$types[] = 'page';
	$types[] = 'post';

	return (array) apply_filters( 'swp_post_types', $types );
}

/**
 * Get the Twitter handle for tweet counts.
 *
 * @param  string $fallback A default value to fall back to.
 * @return string $twitter_handle The twitter handle.
 */
function _swp_get_twitter_handle( $fallback = false ) {
	// Fetch the Twitter handle for the Post Author if it exists.
	if ( isset( $_GET['post'] ) ) {
		$user_id = swp_get_author( absint( $_GET['post'] ) );
	} else {
		$user_id = get_current_user_id();
	}

	$twitter_handle = get_the_author_meta( 'swp_twitter', $user_id );

	if ( ! $twitter_handle ) {
		$twitter_handle = $fallback;
	}

	return $twitter_handle;
}

add_filter( 'swpmb_meta_boxes', 'swp_register_meta_boxes' );
/**
 * Build the options fields.
 *
 * @param  array $meta_boxes The existing meta boxes.
 * @return array $meta_boxes The modified meta boxes.
 */
function swp_register_meta_boxes( $meta_boxes ) {
	$prefix = 'nc_';
	$options = swp_get_user_options();

	$twitter_id = isset( $options['twitterID'] ) ? $options['twitterID'] : false;

	$twitter_handle = _swp_get_twitter_handle( $twitter_id );

	// Setup our meta box using an array.
	$meta_boxes[0] = array(
		'id'       => 'socialWarfare',
		'title'    => __( 'Social Warfare Custom Options','social-warfare' ),
		'pages'    => swp_get_post_types(),
		'context'  => 'normal',
		'priority' => apply_filters( 'swp_metabox_priority', 'high' ),
		'fields'   => array(
			// Setup the social media image.
			array(
				'name'  => '<span class="dashicons dashicons-share"></span> ' . __( 'Social Media Image','social-warfare' ),
				'desc'  => __( 'Add an image that is optimized for maximum exposure on Facebook, Google+ and LinkedIn. We recommend 1,200px by 628px.','social-warfare' ),
				'id'    => $prefix . 'ogImage',
				'type'  => 'image_advanced',
				'clone' => false,
				'class' => $prefix . 'ogImageWrapper',
				'max_file_uploads' => 1,
			),
			// Setup the social media title.
			array(
				'name'  => '<span class="dashicons dashicons-share"></span> ' . __( 'Social Media Title','social-warfare' ),
				'desc'  => __( 'Add a title that will populate the open graph meta tag which will be used when users share your content onto Facebook, LinkedIn, and Google+. If nothing is provided here, we will use the post title as a backup.','social-warfare' ),
				'id'    => $prefix . 'ogTitle',
				'type'  => 'textarea',
				'class' => $prefix . 'ogTitleWrapper',
				'clone' => false,
			),
			// Setup the social media description.
			array(
				'name'  => '<span class="dashicons dashicons-share"></span> ' . __( 'Social Media Description','social-warfare' ),
				'desc'  => __( 'Add a description that will populate the open graph meta tag which will be used when users share your content onto Facebook, LinkedIn, and Google Plus.','social-warfare' ),
				'id'    => $prefix . 'ogDescription',
				'class' => $prefix . 'ogDescriptionWrapper',
				'type'  => 'textarea',
				'clone' => false,
			),
			// Divider.
			array(
				'name' => 'divider',
				'id'   => 'divider',
				'type' => 'divider',
			),
			// Setup the pinterest optimized image.
			array(
				'name'  => '<i class="sw sw-pinterest"></i> ' . __( 'Pinterest Image','social-warfare' ),
				'desc'  => __( 'Add an image that is optimized for maximum exposure on Pinterest. We recommend using an image that is formatted in a 2:3 aspect ratio like 735x1102.','social-warfare' ),
				'id'    => $prefix . 'pinterestImage',
				'class' => $prefix . 'pinterestImageWrapper',
				'type'  => 'image_advanced',
				'clone' => false,
				'max_file_uploads' => 1,
			),
			// Setup the pinterest description.
			array(
				'name'  => '<i class="sw sw-pinterest"></i>' . __( 'Pinterest Description','social-warfare' ),
				'desc'  => __( 'Craft a customized description that will be used when this post is shared on Pinterest. Leave this blank to use the title of the post.','social-warfare' ),
				'id'    => $prefix . 'pinterestDescription',
				'class' => $prefix . 'pinterestDescriptionWrapper',
				'type'  => 'textarea',
				'clone' => false,
			),
			// Setup the Custom Tweet box.
			array(
				'name'  => '<i class="sw sw-twitter"></i> ' . __( 'Custom Tweet','social-warfare' ),
				'desc'  => ( $twitter_id ? sprintf( __( 'If this is left blank your post title will be used. Based on your username (@%1$s), <span class="tweetLinkSection">a link being added,</span> and the current content above, your tweet has %2$s characters remaining.','social-warfare' ),str_replace( '@','',$twitter_handle ),'<span class="counterNumber">140</span>' ) : sprintf( __( 'If this is left blank your post title will be used. <span ="tweetLinkSection">Based on a link being added, and</span> the current content above, your tweet has %s characters remaining.','social-warfare' ),'<span class="counterNumber">140</span>' )),
				'id'    => $prefix . 'customTweet',
				'class' => $prefix . 'customTweetWrapper',
				'type'  => 'textarea',
				'clone' => false,
			),
			// Set up the location on post options.
			array(
				'name'    => '<span class="dashicons dashicons-randomize"></span> ' . __( 'Horizontal Buttons Location','social-warfare' ),
				'id'      => $prefix . 'postLocation',
				'class'   => $prefix . 'postLocationWrapper',
				'type'    => 'select',
				'options' => array(
					'default' => __( 'Default','social-warfare' ),
					'above'   => __( 'Above the Content','social-warfare' ),
					'below'   => __( 'Below the Content','social-warfare' ),
					'both'    => __( 'Both Above and Below the Content','social-warfare' ),
					'none'    => __( 'None/Manual Placement','social-warfare' ),
				),
				'clone' => false,
				'std'	=> 'default',
			),
			array(
				'name'    => '<span class="dashicons dashicons-randomize"></span> ' . __( 'Floating Buttons','social-warfare' ),
				'id'      => $prefix . 'floatLocation',
				'class'   => $prefix . 'floatLocationWrapper',
				'type'    => 'select',
				'options' => array(
					'default' => __( 'Default','social-warfare' ),
					'on'      => __( 'On','social-warfare' ),
					'off'     => __( 'Off','social-warfare' ),
				),
				'clone' => false,
				'std'   => 'default',
			),
			array(
				'name'  => 'divider2',
				'id'    => 'divider2',
				'type'  => 'divider',
			),
			array(
				'name'  => $twitter_handle,
				'id'    => 'twitterID',
				'class' => 'twitterIDWrapper',
				'type'  => 'hidden',
				'std'   => $twitter_handle,
			),
			array(
				'name'  => (is_swp_registered() ? 'true' : 'false'),
				'id'    => (is_swp_registered() ? 'true' : 'false'),
				'class' => 'registrationWrapper',
				'type'  => 'hidden',
				'std'   => (is_swp_registered() ? 'true' : 'false'),
			),
		),
	);

	return $meta_boxes;
}
