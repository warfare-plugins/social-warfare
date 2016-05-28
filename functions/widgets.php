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
		 
	// If not previous values, set some by default
	} else {
		 $title 		= 'Popular Posts';
		 $count 		= '10';
		 $timeframe 	= '180';
		 $network 		= 'totes';
		 $showCount 	= 'true';
		 $countLabel 	= 'Total Shares';
		 $style 		= 'Default';
		 $thumbnails 	= 'true';
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
	
	// Color Scheme Field
	$form .= '<p>';
	$form .= '<label for="'.$this->get_field_id('style').'">Which color scheme would you like to use?</label>';
	$form .= '<select class="widefat" id="'.$this->get_field_id('style').'" name="'.$this->get_field_name('style').'">';
    $form .= '<option value="style1" '.( $style == 'style1' ? 'selected' : '' ) .'>First Style</option>';
    $form .= '<option value="style2" '.( $style == 'style2' ? 'selected' : '' ) .'>Second Style</option>';
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
		$countLabel 	= $instance['countLabel'];
		$style		= $instance['style'];
		$thumbnails	= $instance['thumbnails'];
		
		// Begin output of the widget html
		echo $before_widget;
		echo '<div class="widget-text wp_widget_plugin_box sw_pop_'.$style.'">';
		
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
					$thumbnail_html = '<a href="'.get_the_permalink().'"><img class="sw_pop_thumb" src="'.$thumbnail_url[0].'"></a>';
					
				// If we are not supposed to show thumbnails
				else:
					$thumbnail_html = '';
				endif;

				// Generate the HTML for a link
				$link_html = '<a class="sw_popularity" href="'.get_the_permalink().'"><b>'.get_the_title().'</b>'.$share_html.'</a>';
				
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
