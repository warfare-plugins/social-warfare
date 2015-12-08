<?php

/*****************************************************************
                                                                
          CHECK FOR ALTERNATE VERSION OF THE PERMALINK       
                                                                
******************************************************************/
	function get_alternate_permalink($format) {

		// Setup the Default Permalink Structure
		if($format == 'Default'):
			$domain = get_site_url();
			$id = get_the_ID();
			$url = $domain.'/?p='.$id;

		// Setup the "Day and name" Permalink Structure
		elseif($format == 'Day and name'):
			$domain = get_site_url();
			$date = get_the_date('Y/m/d');
			$slug = basename(get_permalink());
			$url = $domain.'/'.$date.'/'.$slug.'/';

		// Setup the "Month and name" Permalink Structure
		elseif($format == 'Month and name'):
			$domain = get_site_url();
			$date = get_the_date('Y/m');
			$slug = basename(get_permalink());
			$url = $domain.'/'.$date.'/'.$slug.'/';

		// Setup the "Numeric" Permalink Structure
		elseif($format == 'Numeric'):
			$domain = get_site_url();
			$id = get_the_ID();
			$url = $domain.'/archives/'.$id.'/';

		// Setup the "Post name" Permalink Structure
		elseif($format == 'Post Name'):
			$domain = get_site_url();
			$slug = basename(get_permalink());
			$url = $domain.'/'.$slug.'/';

		endif;

		return $url;

	}
	
	function sw_get_alternate_permalink($format,$protocol,$id) {

		// Setup the Default Permalink Structure
		if($format == 'default'):
			$domain = get_site_url();
			$url = $domain.'/?p='.$id;

		// Setup the "Day and name" Permalink Structure
		elseif($format == 'day_and_name'):
			$domain = get_site_url();
			$date = get_the_date('Y/m/d',$id);
			$slug = basename(get_permalink($id));
			$url = $domain.'/'.$date.'/'.$slug.'/';

		// Setup the "Month and name" Permalink Structure
		elseif($format == 'month_and_name'):
			$domain = get_site_url();
			$date = get_the_date('Y/m',$id);
			$slug = basename(get_permalink($id));
			$url = $domain.'/'.$date.'/'.$slug.'/';

		// Setup the "Numeric" Permalink Structure
		elseif($format == 'numeric'):
			$domain = get_site_url();
			$url = $domain.'/archives/'.$id.'/';

		// Setup the "Post name" Permalink Structure
		elseif($format == 'post_name'):
			$domain = get_site_url();
			$post_data = get_post($id, ARRAY_A);
    		$slug = $post_data['post_name'];
			$url = $domain.'/'.$slug.'/';
		elseif($format == 'unchanged'):
			$url = get_permalink($id);
		endif;

		// Check and Adjust the Protocol setting
		if($protocol == 'https' && strpos($url,'https') === false):
			$url = str_replace('http','https',$url);
		elseif($protocol == 'http' && strpos($url,'https') !== false):
			$url = str_replace('https','http',$url);
		endif;
		
		return $url;

	}