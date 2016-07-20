<?php

/*****************************************************************
*                                                                *
*   #1: Add the On / Off Switch	and Sortable Option				 *
*                                                                *
******************************************************************/
	add_filter('swp_button_options', 'swp_hacker_news_options_function',20);
	function swp_hacker_news_options_function($options) {

		// Create the new option in a variable to be inserted
		$options['content']['hacker_news'] = array(
			'type' => 'checkbox',
			'content' => 'Hacker News',
			'default' => false,
			'premium' => true
		);

		return $options;		 

	};
/*****************************************************************
*                                                                *
*   #2: Add it to global network array	         				 *
*                                                                *
******************************************************************/
	// Queue up your filter to be ran on the swp_options hook.
	add_filter('swp_add_networks', 'swp_hacker_news_network');

	// Create the function that will filter the options
	function swp_hacker_news_network($networks) {

		// Add your network to the existing network array
		$networks[] = 'hacker_news';

		// Be sure to return the modified options array or the world will explode
		return $networks;
	};
/*****************************************************************
*                                                                *
*   #3: Generate the API Share Count Request URL	             *
*                                                                *
******************************************************************/
	function swp_hacker_news_request_link($url) {
		$request_url = 'https://hn.algolia.com/api/v1/search?tags=story&restrictSearchableAttributes=url&query='.$url;
		return $request_url;
	}
/*****************************************************************
*                                                                *
*   #4: Parse the Response to get the share count	             *
*                                                                *
******************************************************************/
	function swp_format_hacker_news_response($response) {
		$response = json_decode($response, true);
		return $response['nbHits'];
	}
/*****************************************************************
*                                                                *
*   #5: Create the Button HTML				  		             *
*                                                                *
******************************************************************/
	add_filter('swp_network_buttons', 'swp_hacker_news_button_html',10);
	function swp_hacker_news_button_html($array) {

		if( (isset($array['options']['newOrderOfIcons']['hacker_news']) && !isset($array['buttons'])) || (isset($array['buttons']) && isset($array['buttons']['hacker_news']))  ):

			// Collect the Title
			$title = get_post_meta( $array['postID'] , 'nc_ogTitle' , true );
			if(!$title):
				$title = get_the_title();
			endif;
			++$array['count'];

			$array['resource']['hacker_news'] = '<div class="nc_tweetContainer swp_hacker_news" data-id="'.$array['count'].'" data-network="hacker_news">';
			$link = urlencode(urldecode(swp_process_url( $array['url'] , 'email' , $array['postID'] )));
			$array['resource']['hacker_news'] .= '<a target="_blank" href="http://news.ycombinator.com/submitlink?u='.$link.'&t='.urlencode($title).'" data-link="http://news.ycombinator.com/submitlink?u='.$link.'&t='.urlencode($title).'" class="nc_tweet">';
			if($array['options']['totesEach'] && $array['shares']['totes'] >= $array['options']['minTotes'] && $array['shares']['hacker_news'] > 0):
				$array['resource']['hacker_news'] .= '<span class="iconFiller">';
				$array['resource']['hacker_news'] .= '<span class="spaceManWilly">';
				$array['resource']['hacker_news'] .= '<i class="sw sw-hacker_news"></i>';
				$array['resource']['hacker_news'] .= '<span class="swp_share"> '.__('Vote','social-warfare').'</span>';
				$array['resource']['hacker_news'] .= '</span></span>';
				$array['resource']['hacker_news'] .= '<span class="swp_count">'.swp_kilomega($array['shares']['hacker_news']).'</span>';
			else:
				$array['resource']['hacker_news'] .= '<span class="swp_count swp_hide"><span class="iconFiller"><span class="spaceManWilly"><i class="sw sw-hacker_news"></i><span class="swp_share"> '.__('Vote','social-warfare').'</span></span></span></span>';
			endif;
			$array['resource']['hacker_news'] .= '</a>';
			$array['resource']['hacker_news'] .= '</div>';

		endif;

		return $array;

	};
