<?php

	// Queue up our hook function
	add_action( 'wp_head' , 'swp_add_header_meta' , 1 );

/*****************************************************************
*                                                                *
*          Curly Quote Converter						         *
*                                                                *
******************************************************************/
	function convert_smart_quotes($content) {
		 $content = str_replace('"', '\'', $content);
		 $content = str_replace('&#8220;', '\'', $content);
		 $content = str_replace('&#8221;', '\'', $content);
		 $content = str_replace('&#8216;', '\'', $content);
		 $content = str_replace('&#8217;', '\'', $content);
		 return $content;
	}
/*****************************************************************
*                                                                *
*          Easy Hook Remover							         *
*                                                                *
******************************************************************/
	function swp_remove_filter($hook_name = '', $method_name = '', $priority = 0 ) {
		global $wp_filter;

		// Take only filters on right hook name and priority
		if ( !isset($wp_filter[$hook_name][$priority]) || !is_array($wp_filter[$hook_name][$priority]) )
			return false;

		// Loop on filters registered
		foreach( (array) $wp_filter[$hook_name][$priority] as $unique_id => $filter_array ) {
			// Test if filter is an array ! (always for class/method)
			if ( isset($filter_array['function']) && is_array($filter_array['function']) ) {
				// Test if object is a class and method is equal to param !
				if ( is_object($filter_array['function'][0]) && get_class($filter_array['function'][0]) && $filter_array['function'][1] == $method_name ) {
					unset($wp_filter[$hook_name][$priority][$unique_id]);
				}
			}

		}

		return false;
	}

/*****************************************************************
*                                                                *
*          HEADER META DATA								         *
*                                                                *
******************************************************************/

	// This is the hook function we're adding the header
	function swp_add_header_meta() {

		$info['postID'] = get_the_ID();

		// Cache some resource for fewer queries on subsequent page loads
		if(swp_is_cache_fresh($info['postID'] , true) == false):

			// Check if an image ID has been provided
			$info['imageID'] = get_post_meta( $info['postID'] , 'nc_ogImage' , true );
			if($info['imageID']):
				
				// Cache the image URL
				$info['imageURL'] = wp_get_attachment_url( $info['imageID'] );
				delete_post_meta($info['postID'],'swp_open_graph_image_url');
				update_post_meta($info['postID'],'swp_open_graph_image_url',$info['imageURL']);
				
				// Cache the height and width
				$info['image_data'] = wp_get_attachment_image_src( $info['imageID'] , 'full' );
				delete_post_meta($info['postID'],'swp_open_graph_image_data');
				update_post_meta($info['postID'],'swp_open_graph_image_data',json_encode($info['image_data']));
				
			else:
				$info['imageURL'] = wp_get_attachment_url( get_post_thumbnail_id( $info['postID'] ) );
				delete_post_meta($info['postID'],'swp_open_thumbnail_url');
				update_post_meta($info['postID'],'swp_open_thumbnail_url' , $info['imageURL']);
				delete_post_meta($info['postID'],'swp_open_graph_image_url');
				
				// Cache the height and width
				$info['image_data'] = wp_get_attachment_image_src( get_post_thumbnail_id( $info['postID'] ) , 'full' );
				delete_post_meta($info['postID'],'swp_open_graph_image_data');
				update_post_meta($info['postID'],'swp_open_graph_image_data',json_encode($info['image_data']));
			endif;

			// Cache the Twitter Handle
			$user_twitter_handle 	= get_the_author_meta( 'swp_twitter' , swp_get_author($info['postID']));
			if($user_twitter_handle):
				delete_post_meta($info['postID'],'swp_twitter_username');
				update_post_meta($info['postID'],'swp_twitter_username',$user_twitter_handle);
			else:
				delete_post_meta($info['postID'],'swp_twitter_username');
			endif;

		else:

			// Check if we have a cached Open Graph Image URL
			$info['imageURL'] = get_post_meta( $info['postID'] , 'swp_open_graph_image_url' , true );

			// If not, let's check to see if we have an ID to generate one
			if(!$info['imageURL']):

				// Check for an Open Graph Image ID
				$info['imageID'] = get_post_meta( $info['postID'] , 'nc_ogImage' , true );
				if($info['imageID']):

					// If we find one, let's convert it to a link and cache it for next time
					$info['imageURL'] = wp_get_attachment_url( $info['imageID'] );
					delete_post_meta($info['postID'],'swp_open_graph_image_url');
					update_post_meta($info['postID'],'swp_open_graph_image_url',$info['imageURL']);

				else:

					// If we don't find one, let's save the URL of the thumbnail in case we need it
					$thumbnail_image = get_post_meta($info['postID'],'swp_open_thumbnail_url' , true);
				endif;
			endif;


			$user_twitter_handle = get_post_meta( $info['postID'] , 'swp_twitter_username' , true );

		endif;

		// Create the image Open Graph Meta Tag
		$info['postID'] 				= get_the_ID();
		$info['title'] 					= htmlspecialchars( get_post_meta( $info['postID'] , 'nc_ogTitle' , true ) );
		$info['description'] 			= htmlspecialchars( get_post_meta( $info['postID'] , 'nc_ogDescription' , true ) );
		$info['swp_fb_author'] 			= htmlspecialchars( get_post_meta( $info['postID'] , 'swp_fb_author' , true ) );
		$info['swp_user_options'] 		= swp_get_user_options();
		$info['user_twitter_handle'] 	= $user_twitter_handle;
		$info['header_output']			= '';

		$info = apply_filters( 'swp_meta_tags' , $info );

		if($info['header_output']):
			echo PHP_EOL .'<!-- Open Graph Meta Tags & Twitter Card generated by Social Warfare v'.swp_VERSION.' http://warfareplugins.com -->';
			echo $info['header_output'];
			echo PHP_EOL .'<!-- Open Graph Meta Tags & Twitter Card generated by Social Warfare v'.swp_VERSION.' http://warfareplugins.com -->'. PHP_EOL . PHP_EOL;
		endif;
	}

/*****************************************************************
*                                                                *
*          Queue Up our Open Graph Hooks				         *
*                                                                *
******************************************************************/

			// Queue up our header hook function
			if( is_swp_registered() ):
				add_filter( 'swp_meta_tags' , 'swp_open_graph_tags' , 1 );
				add_filter( 'swp_meta_tags' , 'swp_add_twitter_card' , 2 );
			endif;
			add_filter( 'swp_meta_tags' , 'swp_frame_buster' , 3 );
			add_filter( 'swp_meta_tags' , 'swp_output_custom_color' , 4 );
			add_filter( 'swp_meta_tags' , 'swp_output_font_css' , 5 );
			// add_filter( 'swp_meta_tags' , 'swp_output_cache_trigger' , 6 );
			add_filter( 'swp_meta_tags' , 'swp_cache_rebuild_rel_canonical' , 7 );
			add_action( 'admin_head'   , 'swp_output_font_css' , 10);

			// Disable Simple Podcast Press Open Graph tags
			if ( is_plugin_active( 'simple-podcast-press/simple-podcast-press.php' ) ) {
				global $ob_wp_simplepodcastpress;
				remove_action( 'wp_head' , array( $ob_wp_simplepodcastpress , 'spp_open_graph') , 1);
			}

/*****************************************************************
*                                                                *
*   Open Graph Tags										         *
*                                                                *
* 	Dev Notes: If the user specifies an Open Graph tag,			 *
*	we're going to develop a complete set of tags. Order		 *
*	of preference for each tag is as follows:					 *
*	1. Did they fill out our open graph field?					 *
*	2. Did they fill out Yoast's social field?					 *
*	3. Did they fill out Yoast's SEO field?						 *
*	4. We'll just auto-generate the field from the post.		 *
******************************************************************/

			function swp_open_graph_tags($info) {

				// We only modify the Open Graph tags on single blog post pages
				if(is_singular()):

					// If Yoast Open Graph is activated, we only output Open Graph tags if the user has filled out at least one field
					// Then we'll work along with Yoast to make sure all fields get filled properly
					if(defined('WPSEO_VERSION')):
						global $wpseo_og;
						$yoast_og_setting = has_action( 'wpseo_head', array( $wpseo_og, 'opengraph' ));
					else:
						$yoast_og_setting = false;
					endif;

					if(
						(isset($info['title']) && $info['title']) ||
						(isset($info['description']) && $info['description']) ||
						(isset($info['imageURL']) && $info['imageURL']) ||
						!$yoast_og_setting
					):

						/*****************************************************************
						*                                                                *
						*     YOAST SEO: It rocks, so let's coordinate with it	         *
						*                                                                *
						******************************************************************/

						// Check if Yoast Exists so we can coordinate output with their plugin accordingly
						if (defined('WPSEO_VERSION')):

							// Collect their Social Descriptions as backups if they're not defined in ours
							$yoast_og_title 		= get_post_meta( $info['postID'] , '_yoast_wpseo_opengraph-title' , true );
							$yoast_og_description 	= get_post_meta( $info['postID'] , '_yoast_wpseo_opengraph-description' , true );
							$yoast_og_image 		= get_post_meta( $info['postID'] , '_yoast_wpseo_opengraph-image' , true );

							// Collect their SEO fields as 3rd string backups in case we need them
							$yoast_seo_title		= get_post_meta( $info['postID'] , '_yoast_wpseo_title' , true );
							$yoast_seo_description	= get_post_meta( $info['postID'] , '_yoast_wpseo_metadesc' , true );

							// Cancel their output if ours have been defined so we don't have two sets of tags
							global $wpseo_og;
							remove_action( 'wpseo_head', array( $wpseo_og, 'opengraph' ), 30 );

							// Fetch the WPSEO_SOCIAL Values
							$wpseo_social = get_option( 'wpseo_social' );

						endif;

						// Add all our Open Graph Tags to the Return Header Output
						$info['header_output'] .= PHP_EOL .'<meta property="og:type" content="article" /> ';

						/*****************************************************************
						*                                                                *
						*     JETPACK: If ours are enabled, disable theirs		         *
						*                                                                *
						******************************************************************/

						if ( class_exists( 'JetPack' ) ) :
							add_filter( 'jetpack_enable_opengraph', '__return_false', 99 );
							add_filter( 'jetpack_enable_open_graph', '__return_false', 99 );
						endif;

						/*****************************************************************
						*                                                                *
						*     OPEN GRAPH TITLE									         *
						*                                                                *
						******************************************************************/

						// Open Graph Title: Create an open graph title meta tag
						if($info['title']):

							// If the user defined an social media title, let's use it.
							$info['header_output'] .= PHP_EOL .'<meta property="og:title" content="'.$info['title'].'" />';

						elseif(isset($yoast_og_title) && $yoast_og_title):

							// If the user defined an title over in Yoast, let's use it.
							$info['header_output'] .= PHP_EOL .'<meta property="og:title" content="'.trim($yoast_og_title).'" />';

						elseif(isset($yoast_seo_title) && $yoast_seo_title):

							// If the user defined an title over in Yoast, let's use it.
							$info['header_output'] .= PHP_EOL .'<meta property="og:title" content="'.trim($yoast_seo_title).'" />';

						else:

							// If nothing else is defined, let's use the post title
							$info['header_output'] .= PHP_EOL .'<meta property="og:title" content="'.trim(convert_smart_quotes(htmlspecialchars_decode(get_the_title()))).'" />';

						endif;

						/*****************************************************************
						*                                                                *
						*     OPEN GRAPH DESCRIPTION							         *
						*                                                                *
						******************************************************************/

						// Open Graph Description: Create an open graph description meta tag
						if($info['description']):

							// If the user defined an social media description, let's use it.
							$info['header_output'] .= PHP_EOL .'<meta property="og:description" content="'.$info['description'].'" />';

						elseif(isset($yoast_og_description) && $yoast_og_description):

							// If the user defined an description over in Yoast, let's use it.
							$info['header_output'] .= PHP_EOL .'<meta property="og:description" content="'.$yoast_og_description.'" />';

						elseif(isset($yoast_seo_description) && $yoast_seo_description):

							// If the user defined an description over in Yoast, let's use it.
							$info['header_output'] .= PHP_EOL .'<meta property="og:description" content="'.$yoast_seo_description.'" />';

						else:

							// If nothing else is defined, let's use the post excerpt
							$info['header_output'] .= PHP_EOL .'<meta property="og:description" content="'.convert_smart_quotes(htmlspecialchars_decode(swp_get_excerpt_by_id($info['postID']))).'" />';

						endif;

						/*****************************************************************
						*                                                                *
						*     OPEN GRAPH IMAGE									         *
						*                                                                *
						******************************************************************/

						// Open Graph Image: Create an open graph image meta tag
						if($info['imageURL']):

							// If the user defined an image, let's use it.
							$info['header_output'] .= PHP_EOL .'<meta property="og:image" content="'.$info['imageURL'].'" />';
							$image_output = true;

						elseif(isset($yoast_og_image) && $yoast_og_image):

							// If the user defined an image over in Yoast, let's use it.
							$info['header_output'] .= PHP_EOL .'<meta property="og:image" content="'.$yoast_og_image.'" />';
							$image_output = true;
							
						else:

							// If nothing else is defined, let's use the post Thumbnail as long as we have the URL cached
							$og_image = get_post_meta( $info['postID'] , 'swp_open_thumbnail_url' , true );
							if($og_image):
								$image_output = true;
								$info['header_output'] .= PHP_EOL .'<meta property="og:image" content="'.$og_image.'" />';
							endif;

						endif;
						/*****************************************************************
						*                                                                *
						*     OPEN GRAPH IMAGE DIMENSIONS						         *
						*                                                                *
						******************************************************************/
						if(isset($info['image_data']) && $info['image_data'] && isset($image_output) && $image_output == true):
						
							$info['header_output'] .= PHP_EOL .'<meta property="og:image:width" content="'.$info['image_data'][1].'" />';
							$info['header_output'] .= PHP_EOL .'<meta property="og:image:height" content="'.$info['image_data'][2].'" />';
						
						elseif(get_post_meta( $info['postID'] , 'swp_open_graph_image_data' , true ) && isset($image_output) && $image_output == true):
						
							$info['image_data'] = json_decode(get_post_meta( $info['postID'] , 'swp_open_graph_image_data' , true ));
							$info['header_output'] .= PHP_EOL .'<meta property="og:image:width" content="'.$info['image_data'][1].'" />';
							$info['header_output'] .= PHP_EOL .'<meta property="og:image:height" content="'.$info['image_data'][2].'" />';
						
						endif;
						/*****************************************************************
						*                                                                *
						*     OPEN GRAPH URL & Site Name						         *
						*                                                                *
						******************************************************************/

						$info['header_output'] .= PHP_EOL .'<meta property="og:url" content="'.get_permalink().'" />';
						$info['header_output'] .= PHP_EOL .'<meta property="og:site_name" content="'.get_bloginfo('name').'" />';

						/*****************************************************************
						*                                                                *
						*     OPEN GRAPH AUTHOR									         *
						*                                                                *
						******************************************************************/

						// Add the Facebook Author URL
						if( get_the_author_meta ( 'swp_fb_author' , swp_get_author($info['postID'])) ):

							// Output the Facebook Author URL
							$facebook_author = get_the_author_meta ( 'swp_fb_author' , swp_get_author($info['postID']));
							$info['header_output'] .= PHP_EOL .'<meta property="article:author" content="'.$facebook_author.'" />';

						elseif( get_the_author_meta ( 'facebook' , swp_get_author($info['postID'])) && defined('WPSEO_VERSION')):

							// Output the Facebook Author URL
							$facebook_author = get_the_author_meta ( 'facebook' , swp_get_author($info['postID']));
							$info['header_output'] .= PHP_EOL .'<meta property="article:author" content="'.$facebook_author.'" />';

						endif;

						/*****************************************************************
						*                                                                *
						*     OPEN GRAPH PUBLISHER								         *
						*                                                                *
						******************************************************************/

						// If they have a Facebook Publisher URL in our settings...
						if(isset($info['swp_user_options']['facebookPublisherUrl']) && $info['swp_user_options']['facebookPublisherUrl'] != ''):

							// Output the Publisher URL
							$info['header_output'] .= PHP_EOL .'<meta property="article:publisher" content="'.$info['swp_user_options']['facebookPublisherUrl'].'" />';

						// If they have a Facebook Publisher URL in Yoast's settings...
						elseif(isset($wpseo_social) && isset($wpseo_social['facebook_site']) && $wpseo_social['facebook_site'] != ''):

							// Output the Publisher URL
							$info['header_output'] .= PHP_EOL .'<meta property="article:publisher" content="'.$wpseo_social['facebook_site'].'" />';
						endif;

						$info['header_output'] .= PHP_EOL .'<meta property="article:published_time" content="'.get_post_time('c').'" />';
						$info['header_output'] .= PHP_EOL .'<meta property="article:modified_time" content="'.get_post_modified_time('c').'" />';
						$info['header_output'] .= PHP_EOL .'<meta property="og:updated_time" content="'.get_post_modified_time('c').'" />';

						/*****************************************************************
						*                                                                *
						*     OPEN GRAPH APP ID									         *
						*                                                                *
						******************************************************************/

						// If the Facebook APP ID is in our settings...
						if(isset($info['swp_user_options']['facebookAppID']) && $info['swp_user_options']['facebookAppID'] != ''):

							// Output the Facebook APP ID
							$info['header_output'] .= PHP_EOL .'<meta property="fb:app_id" content="'.$info['swp_user_options']['facebookAppID'].'" />';

						// If the Facebook APP ID is set in Yoast's settings...
						elseif(isset($wpseo_social) && isset($wpseo_social['fbadminapp']) && $wpseo_social['fbadminapp'] != ''):

							// Output the Facebook APP ID
							$info['header_output'] .= PHP_EOL .'<meta property="fb:app_id" content="'.$wpseo_social['fbadminapp'].'" />';

						else:

							// Output the Facebook APP ID
							$info['header_output'] .= PHP_EOL .'<meta property="fb:app_id" content="529576650555031" />';

						endif;

					endif;
				endif;

				// Return the variable containing our information for the meta tags
				return $info;

			}

/*****************************************************************
*                                                                *
*   TWITTER CARDS		 							             *
*                                                                *
*	Dev Notes: If the user has Twitter cards turned on, we		 *
*	need to generate them, but we also like Yoast so we'll		 *
*	pay attention to their settings as well. Here's the order	 *
*	of preference for each field:								 *
*	1. Did the user fill out the Social Media field?			 *
*	2. Did the user fill out the Yoast Twitter Field?			 *
*	3. Did the user fill out the Yoast SEO field?				 *
*	4. We'll auto generate something logical from the post.		 *
*																 *
******************************************************************/

			function swp_add_twitter_card($info) {
				if(is_singular()):
					// Check if Twitter Cards are Activated
					if($info['swp_user_options']['swp_twitter_card']):

						/*****************************************************************
						*                                                                *
						*     YOAST SEO: It rocks, so let's coordinate with it	         *
						*                                                                *
						******************************************************************/

						// Check if Yoast Exists so we can coordinate output with their plugin accordingly
						if (defined('WPSEO_VERSION')):

							// Collect their Social Descriptions as backups if they're not defined in ours
							$yoast_twitter_title 		= get_post_meta( $info['postID'] , '_yoast_wpseo_twitter-title' , true );
							$yoast_twitter_description 	= get_post_meta( $info['postID'] , '_yoast_wpseo_twitter-description' , true );
							$yoast_twitter_image 		= get_post_meta( $info['postID'] , '_yoast_wpseo_twitter-image' , true );

							// Collect their SEO fields as 3rd string backups in case we need them
							$yoast_seo_title			= get_post_meta( $info['postID'] , '_yoast_wpseo_title' , true );
							$yoast_seo_description		= get_post_meta( $info['postID'] , '_yoast_wpseo_metadesc' , true );

							// Cancel their output if ours have been defined so we don't have two sets of tags
							remove_action( 'wpseo_head' , array( 'WPSEO_Twitter' , 'get_instance' ) , 40 );

						endif;

						/*****************************************************************
						*                                                                *
						*     JET PACK: If ours are activated, disable theirs	         *
						*                                                                *
						******************************************************************/

						if ( class_exists( 'JetPack' ) ) :

							add_filter( 'jetpack_disable_twitter_cards', '__return_true', 99 );

						endif;

						/*****************************************************************
						*                                                                *
						*     TWITTER TITLE										         *
						*                                                                *
						******************************************************************/

						// If the user defined a Social Media title, use it, otherwise check for Yoast's
						if(!$info['title'] && isset($yoast_twitter_title) && $yoast_twitter_title):

							$info['title'] = $yoast_twitter_title;

						// If not title has been defined, let's check the SEO description as a 3rd string option
						elseif(!$info['title'] && isset($yoast_seo_title) && $yoast_seo_title):

							$info['title'] = $yoast_seo_title;

						// If not title has been defined, let's use the post title
						elseif(!$info['title']):

							$info['title'] = convert_smart_quotes(htmlspecialchars_decode( get_the_title() ));

						endif;

						/*****************************************************************
						*                                                                *
						*     TWITTER DESCRIPTION								         *
						*                                                                *
						******************************************************************/

						// Open Graph Description
						if(!$info['description'] && isset($yoast_twitter_description) && $yoast_twitter_description):

							$info['description'] = $yoast_twitter_description;

						// If not title has been defined, let's check the SEO description as a 3rd string option
						elseif(!$info['description'] && isset($yoast_seo_description) && $yoast_seo_description):

							$info['description'] = $yoast_seo_description;

						// If not, then let's use the excerpt
						elseif(!$info['description']):

							$info['description'] = convert_smart_quotes(htmlspecialchars_decode( swp_get_excerpt_by_id( $info['postID'] )) );

						endif;

						/*****************************************************************
						*                                                                *
						*     TWITTER IMAGE								         *
						*                                                                *
						******************************************************************/

						// Open Graph Description
						if(!$info['imageURL'] && isset($yoast_twitter_image) && $yoast_twitter_image):

							$info['imageURL'] = $yoast_twitter_image;

						else:

						// If nothing else is defined, let's use the post Thumbnail as long as we have the URL cached
							$twitter_image = get_post_meta( $info['postID'] , 'swp_open_thumbnail_url' , true );
							if($twitter_image):
								$info['imageURL'] = $twitter_image;
							endif;

						endif;

						/*****************************************************************
						*                                                                *
						*     PUT IT ALL TOGETHER						         		 *
						*                                                                *
						******************************************************************/

						// Check if we have everything we need for a large image summary card
						if($info['imageURL']):
							$info['header_output'] .= PHP_EOL .'<meta name="twitter:card" content="summary_large_image">';
							$info['header_output'] .= PHP_EOL .'<meta name="twitter:title" content="'.trim($info['title']).'">';
							$info['header_output'] .= PHP_EOL .'<meta name="twitter:description" content="'.$info['description'].'">';
							$info['header_output'] .= PHP_EOL .'<meta name="twitter:image" content="'.$info['imageURL'].'">';
							if($info['swp_user_options']['twitterID']):
								$info['header_output'] .= PHP_EOL .'<meta name="twitter:site" content="@'.str_replace('@','',$info['swp_user_options']['twitterID']).'">';
							endif;
							if($info['user_twitter_handle']):
								$info['header_output'] .= PHP_EOL .'<meta name="twitter:creator" content="@'.str_replace('@','',$info['user_twitter_handle']).'">';
							endif;

						// Otherwise create a small summary card
						else:
							$info['header_output'] .= PHP_EOL .'<meta name="twitter:card" content="summary">';
							$info['header_output'] .= PHP_EOL .'<meta name="twitter:title" content="'.str_replace('"','\'',$info['title']).'">';
							$info['header_output'] .= PHP_EOL .'<meta name="twitter:description" content="'.str_replace('"','\'',$info['description']).'">';
							if($info['swp_user_options']['twitterID']):
								$info['header_output'] .= PHP_EOL .'<meta name="twitter:site" content="@'.str_replace('@','',$info['swp_user_options']['twitterID']).'">';
							endif;
							if($info['user_twitter_handle']):
								$info['header_output'] .= PHP_EOL .'<meta name="twitter:creator" content="@'.str_replace('@','',$info['user_twitter_handle']).'">';
							endif;
						endif;

					endif;
				endif;
				return $info;
			}

/*****************************************************************
*                                                                *
*          Frame Buster 							             *
*                                                                *
******************************************************************/

		function swp_frame_buster($info) {
			if($info['swp_user_options']['sniplyBuster'] == true):
				$info['header_output'] .= PHP_EOL.'<script type="text/javascript">function parentIsEvil() { var html = null; try { var doc = top.location.pathname; } catch(err){ }; if(typeof doc === "undefined") { return true } else { return false }; }; if (parentIsEvil()) { top.location = self.location.href; };var url = "'.get_permalink().'";if(url.indexOf("stfi.re") != -1) { var canonical = ""; var links = document.getElementsByTagName("link"); for (var i = 0; i < links.length; i ++) { if (links[i].getAttribute("rel") === "canonical") { canonical = links[i].getAttribute("href")}}; canonical = canonical.replace("?sfr=1", "");top.location = canonical; console.log(canonical);};</script>';
			endif;
			return $info;
		}

/*****************************************************************
*                                                                *
*          CUSTOM COLORS 							             *
*                                                                *
******************************************************************/

		function swp_output_custom_color($info) {
			if($info['swp_user_options']['dColorSet'] == 'customColor' || $info['swp_user_options']['iColorSet'] == 'customColor' || $info['swp_user_options']['oColorSet'] == 'customColor'):
				$info['header_output'] .= PHP_EOL.'<style type="text/css">.nc_socialPanel.swp_d_customColor a, html body .nc_socialPanel.swp_i_customColor .nc_tweetContainer:hover a, body .nc_socialPanel.swp_o_customColor:hover a {color:white} .nc_socialPanel.swp_d_customColor .nc_tweetContainer, html body .nc_socialPanel.swp_i_customColor .nc_tweetContainer:hover, body .nc_socialPanel.swp_o_customColor:hover .nc_tweetContainer {background-color:'.$info['swp_user_options']['customColor'].';border:1px solid '.$info['swp_user_options']['customColor'].';} </style>';
			endif;

			if($info['swp_user_options']['dColorSet'] == 'ccOutlines' || $info['swp_user_options']['iColorSet'] == 'ccOutlines' || $info['swp_user_options']['oColorSet'] == 'ccOutlines'):
				$info['header_output'] .= PHP_EOL.'<style type="text/css">.nc_socialPanel.swp_d_ccOutlines a, html body .nc_socialPanel.swp_i_ccOutlines .nc_tweetContainer:hover a, body .nc_socialPanel.swp_o_ccOutlines:hover a { color:'.$info['swp_user_options']['customColor'].'; }
.nc_socialPanel.swp_d_ccOutlines .nc_tweetContainer, html body .nc_socialPanel.swp_i_ccOutlines .nc_tweetContainer:hover, body .nc_socialPanel.swp_o_ccOutlines:hover .nc_tweetContainer { background:transparent; border:1px solid '.$info['swp_user_options']['customColor'].'; } </style>';

			endif;
			return $info;
		}

/*****************************************************************
*                                                                *
*          CACHE REBUILD REL CANONICAL				             *
*                                                                *
******************************************************************/
function swp_cache_rebuild_rel_canonical($info) {

	// Fetch the Permalink
	$url = get_permalink();

	// Check to see if the cache is currently being rebuilt
	if(isset($_GET['swp_cache']) && $_GET['swp_cache'] == 'rebuild'):

		// Use a rel canonical so everyone knows this is not a real page
		$info['header_output'] .= '<link rel="canonical" href="'.$url.'">';
	endif;

	// Return the array so the world doesn't explode
	return $info;
}
/*****************************************************************
*                                                                *
*          ICON FONT CSS							             *
*                                                                *
******************************************************************/
function swp_output_font_css($info=array()) {
	if(is_admin()):

		// Echo it if we're using the Admin Head Hook
		echo '<style>@font-face {font-family: "sw-icon-font";src:url("'.swp_PLUGIN_DIR.'/fonts/sw-icon-font.eot?ver='.swp_VERSION.'");src:url("'.swp_PLUGIN_DIR.'/fonts/sw-icon-font.eot?ver='.swp_VERSION.'#iefix") format("embedded-opentype"),url("'.swp_PLUGIN_DIR.'/fonts/sw-icon-font.woff?ver='.swp_VERSION.'") format("woff"),
    url("'.swp_PLUGIN_DIR.'/fonts/sw-icon-font.ttf?ver='.swp_VERSION.'") format("truetype"),url("'.swp_PLUGIN_DIR.'/fonts/sw-icon-font.svg?ver='.swp_VERSION.'#1445203416") format("svg");font-weight: normal;font-style: normal;}</style>';
	else:

		// Add it to our array if we're using the frontend Head Hook
		$info['header_output'] .= PHP_EOL.'<style>@font-face {font-family: "sw-icon-font";src:url("'.swp_PLUGIN_DIR.'/fonts/sw-icon-font.eot?ver='.swp_VERSION.'");src:url("'.swp_PLUGIN_DIR.'/fonts/sw-icon-font.eot?ver='.swp_VERSION.'#iefix") format("embedded-opentype"),url("'.swp_PLUGIN_DIR.'/fonts/sw-icon-font.woff?ver='.swp_VERSION.'") format("woff"), url("'.swp_PLUGIN_DIR.'/fonts/sw-icon-font.ttf?ver='.swp_VERSION.'") format("truetype"),url("'.swp_PLUGIN_DIR.'/fonts/sw-icon-font.svg?ver='.swp_VERSION.'#1445203416") format("svg");font-weight: normal;font-style: normal;}</style>';

		return $info;
	endif;
}
/*****************************************************************
*                                                                *
*          FOOTER HOOKS & SCRIPTS					             *
*                                                                *
******************************************************************/
// Queue up our hook function
add_action( 'wp_footer' , 'swp_footer_functions' , 1 );

// Queue up our footer hook function
add_filter( 'swp_footer_scripts' , 'swp_output_cache_trigger' );
add_filter( 'swp_footer_scripts' , 'swp_click_tracking' );
add_filter( 'swp_footer_scripts' , 'swp_pinit' );

function swp_footer_functions() {

		// Fetch a few variables
		$info['postID'] 				= get_the_ID();
		$info['swp_user_options'] 		= swp_get_user_options();
		$info['footer_output']			= '';

		// Pass the array through our custom filters
		$info = apply_filters( 'swp_footer_scripts' , $info );

		// If we have output, output it
		if($info['footer_output']):
			echo '<script type="text/javascript">';
			echo $info['footer_output'];
			echo '</script>';
		endif;
		
}

/*****************************************************************
*                                                                *
*          PIN IMAGES VARIABLES						             *
*                                                                *
******************************************************************/
function swp_pinit($info) {
	if($info['swp_user_options']['pinit_toggle'] == true && is_swp_registered()):
		$info['footer_output'] .= 'swp_pinit=true; swp_pinit_h_location="'.$info['swp_user_options']['pinit_location_horizontal'].'"; swp_pinit_v_location="'.$info['swp_user_options']['pinit_location_vertical'].'"; sw_pinit_min_width='.str_replace('px','',$info['swp_user_options']['pinit_min_width']).'; sw_pinit_min_height='.str_replace('px','',$info['swp_user_options']['pinit_min_height']).';';
	else:
		$info['footer_output'] .= 'swp_pinit=false;';
	endif;
	return $info;	
}

/*****************************************************************
*                                                                *
*          CACHE REBUILD TRIGGER					             *
*                                                                *
******************************************************************/
function swp_output_cache_trigger($info) {
	// Check if we're on a single post page, the cache is expired, and they're using the updated cache rebuild method
	if((is_singular() && swp_is_cache_fresh( get_the_ID() , true ) == false && $info['swp_user_options']['cacheMethod'] != 'legacy') || (isset($_GET['swp_cache']) && $_GET['swp_cache'] == 'rebuild')):

		// Make sure we're not on a WooCommerce Account Page
		if(is_plugin_active( 'woocommerce/woocommerce.php' ) && is_account_page()):
			return $info;

		// Trigger the cache rebuild
		else:
			$url = get_permalink();
			$admin_ajax = admin_url( 'admin-ajax.php' );
			$info['footer_output'] .= PHP_EOL.'swp_admin_ajax = "'.$admin_ajax.'"; var swp_buttons_exist = !!document.getElementsByClassName("nc_socialPanel");if(swp_buttons_exist) {jQuery(document).ready( function() { var swp_cache_data = {"action":"swp_cache_trigger","post_id":'.$info['postID'].'};jQuery.post(swp_admin_ajax, swp_cache_data, function(response) {console.log(response);});});} swp_post_id="'.$info['postID'].'"; swp_post_url="'.$url.'"; swp_fetch_facebook_shares(); ';
		endif;
	endif;
	// Return the array so the world doesn't explode
	return $info;
}
/*****************************************************************
*                                                                *
*          Click Tracking							             *
*                                                                *
******************************************************************/
function swp_click_tracking($info) {
	$swp_options = swp_get_user_options();
	if( $swp_options['swp_click_tracking'] == 1 ):
    	$info['footer_output'] .= 'if (typeof ga == "function") { jQuery(document).on("click",".nc_tweet",function(event) {var network = jQuery(this).parents(".nc_tweetContainer").attr("data-network");ga("send", "event", "social_media", "swp_" + network + "_share" );});}';
	endif;
	return $info;
}
