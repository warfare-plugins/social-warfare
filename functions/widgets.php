<?php 

/*************************************************************

	POPULAR POSTS WIDGET CLASS

**************************************************************/
class sw_popular_posts_widget extends WP_Widget {

	// Class Constructor
	function sw_popular_posts_widget() {
		parent::__construct(false, $name = 'Social Warfare: Popular Posts' );
	}
	
/*************************************************************

	FUNCTION - CREATE THE WIDGET FORM

**************************************************************/	

	function form($instance) {
	
	// Check For Previously Set Values
	if( $instance ) {
		 $title 		= esc_attr($instance['title']);
		 $count 		= esc_attr($instance['count']);
		 $timeframe 	= esc_textarea($instance['timeframe']);
		 $network 		= esc_textarea($instance['network']);
		 $showCount 	= esc_textarea($instance['showCount']);
		 $countLabel 	= esc_textarea($instance['countLabel']);
		 $style 		= esc_textarea($instance['style']);
		 $thumbnails 	= esc_textarea($instance['thumbnails']);
		 $thumb_size 	= esc_textarea($instance['thumb_size']);
		 $font_size 	= esc_textarea($instance['font_size']);
		 
	// If not previous values, set some by default
	} else {
		 $title 		= 'Popular Posts';
		 $count 		= '10';
		 $timeframe 	= '180';
		 $network 		= 'totes';
		 $showCount 	= 'true';
		 $countLabel 	= 'Total Shares';
		 $style 		= 'style_01';
		 $thumbnails 	= 'true';
		 $thumb_size 	= '100';
		 $font_size		= '100';
	}
	
	// Fetch the Social Warfare Options
	$options = sw_get_user_options();
	
	// Fetch the networks that are active on this blog
	$availableNetworks = $options['newOrderOfIcons'];

	// Build the Widget Form
	$form = '';	
	
	// The Widget Title Field
	$form .= '<p>';
	$form .= '<label for="'.$this->get_field_id('title').'">Widget Title</label>';
	$form .= '<input class="widefat" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" type="text" value="'.$title.'" />';
	$form .= '</p>';
	
	// Number of Posts to Display Field
	$form .= '<p>';
	$form .= '<label for="'.$this->get_field_id('count').'">How many posts would you like to display?</label>';
	$form .= '<input class="widefat" id="'.$this->get_field_id('count').'" name="'.$this->get_field_name('count').'" type="number" value="'.$count.'" min="0" />';
	$form .= '</p>';
	
	// Age of the pots to display field
	$form .= '<p>';
	$form .= '<label for="'.$this->get_field_id('timeframe').'">What is maximum age of a post (in days) that you would like to include (0 = Unlimited)?</label>';
	$form .= '<input class="widefat" id="'.$this->get_field_id('timeframe').'" name="'.$this->get_field_name('timeframe').'" value="'.$timeframe.'" type="number" min="0">';
	$form .= '</p>';
    
	// Which networks to use as the basis field
    $form .= '<p>';
	$form .= '<label for="'.$this->get_field_id('network').'">Which network would you like to base your posts popularity on?</label>';
	$form .= '<select class="widefat" id="'.$this->get_field_id('network').'" name="'.$this->get_field_name('network').'">';
    $form .= '<option value="totes"'. ( $network == 'totes' ? 'selected' : '' ).'>All Networks</option>';
		foreach($availableNetworks as $key => $value):
			if($options[$key]) {
				if($network == $key.'_shares'):
        			$form .= '<option value="'.$key.'_shares" selected>'.$value.'</option>';
				else:
					$form .= '<option value="'.$key.'_shares">'.$value.'</option>';
				endif;
			};
		endforeach;
	$form .= '</select>';
	$form .= '</p>';
    
	// Display the share count toggle field
    $form .= '<p>';
	$form .= '<label for="'.$this->get_field_id('showCount').'">Would you like to show the count?</label>';
	$form .= '<select class="widefat" id="'.$this->get_field_id('showCount').'" name="'.$this->get_field_name('showCount').'">';
    $form .= '<option value="true" '.( $showCount == 'true' ? 'selected' : '').'>Yes</option>';
    $form .= '<option value="false" '.( $showCount == 'false' ? 'selected' : '').'>No</option>';
    $form .= '</select>';
	$form .= '</p>';
    
	// Count Label Field
    $form .= '<p>';
	$form .= '<label for="'.$this->get_field_id('countLabel').'">Count Number Label</label>';
	$form .= '<input class="widefat" id="'.$this->get_field_id('countLabel').'" name="'.$this->get_field_name('countLabel').'" type="text" value="'.$countLabel.'" />';
	$form .= '</p>';
	
	// Post thumbnails toggle field
    $form .= '<p>';
	$form .= '<label for="'.$this->get_field_id('thumbnails').'">Would you like to display thumbnails?</label>';
	$form .= '<select class="widefat" id="'.$this->get_field_id('thumbnails').'" name="'.$this->get_field_name('thumbnails').'">';
    $form .= '<option value="true" '.( $thumbnails == 'true' ? 'selected' : '').'>Yes</option>';
    $form .= '<option value="false" '.( $thumbnails == 'false' ? 'selected' : '').'>No</option>';
    $form .= '</select>';
	$form .= '</p>';

	// Thumbnails size field
    $form .= '<p>';
	$form .= '<label for="'.$this->get_field_id('thumb_size').'">What size would you like your thumbnails?</label>';
	$form .= '<select class="widefat" id="'.$this->get_field_id('thumb_size').'" name="'.$this->get_field_name('thumb_size').'">';
    $form .= '<option value="50" '.( $thumb_size == '50' ? 'selected' : '').'>50px</option>';
    $form .= '<option value="60" '.( $thumb_size == '60' ? 'selected' : '').'>60px</option>';
    $form .= '<option value="70" '.( $thumb_size == '70' ? 'selected' : '').'>70px</option>';
    $form .= '<option value="80" '.( $thumb_size == '80' ? 'selected' : '').'>80px</option>';
    $form .= '<option value="90" '.( $thumb_size == '90' ? 'selected' : '').'>90px</option>';
    $form .= '<option value="100" '.( $thumb_size == '100' ? 'selected' : '').'>100px</option>';
    $form .= '<option value="110" '.( $thumb_size == '110' ? 'selected' : '').'>110px</option>';
    $form .= '<option value="120" '.( $thumb_size == '120' ? 'selected' : '').'>120px</option>';
    $form .= '<option value="130" '.( $thumb_size == '130' ? 'selected' : '').'>130px</option>';
    $form .= '<option value="140" '.( $thumb_size == '140' ? 'selected' : '').'>140px</option>';
    $form .= '<option value="150" '.( $thumb_size == '150' ? 'selected' : '').'>150px</option>';
    $form .= '</select>';
	$form .= '</p>';

	// Font size field
    $form .= '<p>';
	$form .= '<label for="'.$this->get_field_id('font_size').'">What size would you like the font?</label>';
	$form .= '<select class="widefat" id="'.$this->get_field_id('font_size').'" name="'.$this->get_field_name('font_size').'">';
    $form .= '<option value="50" '.( $font_size == '50' ? 'selected' : '').'>50%</option>';
    $form .= '<option value="60" '.( $font_size == '60' ? 'selected' : '').'>60%</option>';
    $form .= '<option value="70" '.( $font_size == '70' ? 'selected' : '').'>70%</option>';
    $form .= '<option value="80" '.( $font_size == '80' ? 'selected' : '').'>80%</option>';
    $form .= '<option value="90" '.( $font_size == '90' ? 'selected' : '').'>90%</option>';
    $form .= '<option value="100" '.( $font_size == '100' ? 'selected' : '').'>100%</option>';
    $form .= '<option value="110" '.( $font_size == '110' ? 'selected' : '').'>110%</option>';
    $form .= '<option value="120" '.( $font_size == '120' ? 'selected' : '').'>120%</option>';
    $form .= '<option value="130" '.( $font_size == '130' ? 'selected' : '').'>130%</option>';
    $form .= '<option value="140" '.( $font_size == '140' ? 'selected' : '').'>140%</option>';
    $form .= '<option value="150" '.( $font_size == '150' ? 'selected' : '').'>150%</option>';
    $form .= '</select>';
	$form .= '</p>';
	
	// Color Scheme Field
	$form .= '<p>';
	$form .= '<label for="'.$this->get_field_id('style').'">Which color scheme would you like to use?</label>';
	$form .= '<select class="widefat" id="'.$this->get_field_id('style').'" name="'.$this->get_field_name('style').'">';
    $form .= '<option value="style_01" '.( $style == 'style_01' ? 'selected' : '' ) .'>Vanilla (No Styling)</option>';
    $form .= '<option value="style_02" '.( $style == 'style_02' ? 'selected' : '' ) .'>Inspired by Twitter</option>';
    $form .= '<option value="style_03" '.( $style == 'style_03' ? 'selected' : '' ) .'>Inspired by Facebook</option>';
    $form .= '<option value="style_04" '.( $style == 'style_04' ? 'selected' : '' ) .'>Inspired by Google Plus</option>';
    $form .= '<option value="style_05" '.( $style == 'style_05' ? 'selected' : '' ) .'>Inspired by LinkedIn</option>';
    $form .= '<option value="style_06" '.( $style == 'style_06' ? 'selected' : '' ) .'>Inspired by Pinterest</option>';
    $form .= '<option value="custom" '.( $style == 'custom' ? 'selected' : '' ) .'>Custom</option>';
    $form .= '</select>';
	$form .= '</p>';
    
	// Output the form fields
	echo $form;
	
	}

/*************************************************************

	FUNCTION - UPDATE VALUES FROM THE FORM

**************************************************************/	
	
	function update($new_instance, $old_instance) {
		  $instance = $old_instance;
		  
		  // Fetch the values from the form
		  $instance['title'] 		= strip_tags($new_instance['title']);
		  $instance['count'] 		= strip_tags($new_instance['count']);
		  $instance['timeframe'] 	= strip_tags($new_instance['timeframe']);
		  $instance['network'] 		= strip_tags($new_instance['network']);
		  $instance['showCount'] 	= strip_tags($new_instance['showCount']);
		  $instance['countLabel'] 	= strip_tags($new_instance['countLabel']);
		  $instance['style'] 		= strip_tags($new_instance['style']);
		  $instance['thumbnails'] 	= strip_tags($new_instance['thumbnails']);
		  $instance['thumb_size'] 	= strip_tags($new_instance['thumb_size']);
		  $instance['font_size'] 	= strip_tags($new_instance['font_size']);
		 return $instance;
	}
/*************************************************************

	FUNCTION - OUTPUT THE WIDGET TO THE SITE

**************************************************************/	

	function widget($args, $instance) {
		extract( $args );
	   
		// Fetch the field values from the form
		$title 		= apply_filters('widget_title', $instance['title']);
		$count 		= $instance['count'];
		$timeframe 	= $instance['timeframe'];
		$network 	= $instance['network'];
		$showCount 	= $instance['showCount'];
		$countLabel = $instance['countLabel'];
		$style		= $instance['style'];
		$thumbnails	= $instance['thumbnails'];
		$thumb_size	= $instance['thumb_size'];
		$font_size	= $instance['font_size'];
		
		/*************************************************************

			STYLES - CREATE AN ARRAY OF BACKGROUNDS AND LINK COLORS

		**************************************************************/	
		
		// Vanilla (No Styling)
		$styles['style_01']['wrapper'] 	= 'background:transparent;';
		$styles['style_01']['links']	= '';
		
		// Inspired by Twitter
		$styles['style_02']['wrapper'] 	= 'padding:15px;background:#429cd6;';
		$styles['style_02']['links']	= 'color:#ffffff;';
		
		// Inspired by Facebook
		$styles['style_03']['wrapper'] 	= 'padding:15px;background:#3a589e;';
		$styles['style_03']['links']	= 'color:#ffffff;';
		
		// Inspired by Google Plus
		$styles['style_04']['wrapper'] 	= 'padding:15px;background:#df4b37;';
		$styles['style_04']['links']	= 'color:#ffffff;';
		
		// Inspired by LinkedIn
		$styles['style_05']['wrapper'] 	= 'padding:15px;background:#0d77b7;';
		$styles['style_05']['links']	= 'color:#ffffff;';
		
		// Inspired by Pinterest
		$styles['style_06']['wrapper'] 	= 'padding:15px;background:#cd2029;';
		$styles['style_06']['links']	= 'color:#ffffff;';
		
		/*************************************************************

			BUILD OUT THE WIDGET

		**************************************************************/	
		
		// Begin output of the widget html
		echo $before_widget;
		echo '<div class="widget-text sw_widget_box" style="'.$styles[$style]['wrapper'].'">';
		
		// Check if title is set
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}
	
		// If a custom timeframe is not being used....
		if( $timeframe == 0 ):
		
			// Create the arguments for a query without a timeframe
			$args = array(
				'posts_per_page' 	=> $count,
				'post_type' 		=> 'post',
				'meta_key' 			=> '_'.$network,
				'orderby' 			=> 'meta_value_num',
				'order' 			=> 'DESC',
			);
		
		// If a custom timeframe is being used....
		else:
		
			// Create the arguments for a query with a timeframe
			$args = array(
				'posts_per_page' 	=> $count,
				'post_type' 		=> 'post',
				'meta_key' 			=> '_'.$network,
				'orderby' 			=> 'meta_value_num',
				'order' 			=> 'DESC',
				'date_query'    	=> array(
					'column'  		=> 'post_date',
					'after'   		=> '- 300 days'
				)
			);
		endif;
		
		// Query and fetch the posts
		$q = new WP_Query( $args );
		
		// Begin the loop
		if( $q->have_posts() ) :
			$i = 1;
			while( $q->have_posts() ):
				
				$q->the_post();
				
				// If we are supposed to show count numbers....
				if($showCount == 'true'):
					$postID = get_the_ID();
					$shares = get_post_meta($postID,'_'.$network,true);
					$share_html = '<span class="sw_pop_count"> - '.kilomega($shares).' '.$countLabel.'</span>';
					
				// If we are not supposed to show count numbers
				else:
					$share_html = '';
				endif;

				// If we are supposed to show thumbnails
				if($thumbnails == true && has_post_thumbnail()):
					$thumbnail_url = wp_get_attachment_image_src( get_post_thumbnail_id() , 'thumbnail' );
					$thumbnail_html = '';
					$thumbnail_html .= '<a href="'.get_the_permalink().'">';
					$thumbnail_html .= '<img style="width:'.$thumb_size.'px;height:'.$thumb_size.'px;" class="sw_pop_thumb" src="'.$thumbnail_url[0].'">';
					$thumbnail_html .= '</a>';
					
				// If we are not supposed to show thumbnails
				else:
					$thumbnail_html = '';
				endif;

				// Generate the HTML for a link
				$link_html = '<a style="font-size:'.$font_size.'%;'.$styles[$style]['links'].'" class="sw_popularity" href="'.get_the_permalink().'"><b>'.get_the_title().'</b>'.$share_html.'</a>';
				
				// Output the post to the site
				echo '<div class="sw_popular_post">'.$thumbnail_html.''.$link_html.'</div>';
				echo '<div class="sw_clearfix"></div>';
				
			// End the loop
			endwhile;
		endif;
		
		// Reset the main query
		wp_reset_postdata();
		echo '</div>';
		echo $after_widget;
	}
}

// register widget
add_action('widgets_init', create_function('', 'return register_widget("sw_popular_posts_widget");'));
