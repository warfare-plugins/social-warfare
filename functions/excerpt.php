<?php

/*****************************************************************
*                                                                *
*          AN EXCERPT FUNCTION							         *
*                                                                *
******************************************************************/

	// A function to process the excerpts for descriptions		
	function sw_get_excerpt_by_id($post_id){
		
		// Check if the post has an excerpt
		if(has_excerpt()):
			$the_post = get_post($post_id); //Gets post ID
			$the_excerpt = $the_post->post_excerpt;
			
		// If not, let's create an excerpt
		else:
			$the_post = get_post($post_id); //Gets post ID
			$the_excerpt = $the_post->post_content; //Gets post_content to be used as a basis for the excerpt
		endif;
		
		$excerpt_length = 100; //Sets excerpt length by word count
		$the_excerpt = strip_tags(strip_shortcodes($the_excerpt)); //Strips tags and images
		
		$the_excerpt = str_replace(']]>', ']]&gt;', $the_excerpt);
		$the_excerpt = strip_tags($the_excerpt);
		$excerpt_length = apply_filters('excerpt_length', 100);
		$excerpt_more = apply_filters('excerpt_more', ' ' . '[...]');
		$words = preg_split("/[\n\r\t ]+/", $the_excerpt, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
	
		if(count($words) > $excerpt_length) :
			array_pop($words);
			// array_push($words, '…');
			$the_excerpt = implode(' ', $words);
		endif;
		
		$the_excerpt = preg_replace( "/\r|\n/", "", $the_excerpt );
	
		return $the_excerpt;
	}