<?php

/*****************************************************************
*                                                                *
*          cURL - A Function to Process cURL requests            *
*                                                                *
******************************************************************/

	function swp_file_get_contents_curl($url){
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		curl_setopt($ch, CURLOPT_FAILONERROR, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
		curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		$cont = @curl_exec($ch);
		$curl_errno = curl_errno($ch);
		if ($curl_errno > 0) {
			return 0;
		}
		return $cont;
	}

/*****************************************************************
*                                                                *
*          Twitter - A Function to Fetch Twitter Shares          *
*                                                                *
******************************************************************/

	function swp_fetch_twitter_shares($url) {
		$url = rawurlencode($url);
		$json_string = swp_file_get_contents_curl('https://urls.api.twitter.com/1/urls/count.json?url=' . $url);
		$json = json_decode($json_string, true);
		return isset($json['count'])?intval($json['count']):0;
	}

/*****************************************************************
*                                                                *
*          Facebook - A Function to Fetch Facebook Shares        *
*                                                                *
******************************************************************/

	function swp_fetch_facebook_shares($url) {
		$url = rawurlencode($url);
		$json_string = swp_file_get_contents_curl('https://api.facebook.com/restserver.php?method=links.getStats&format=json&urls='.$url);
		$json = json_decode($json_string, true);
		return isset($json[0]['total_count'])?intval($json[0]['total_count']):0;
	}

/*****************************************************************
*                                                                *
*          GooglePlus - A Function to Fetch GooglePlus Shares    *
*                                                                *
******************************************************************/

	function swp_fetch_googlePlus_shares($url)  {
		$url = rawurlencode($url);
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, "https://clients6.google.com/rpc");
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_POSTFIELDS, '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"'.rawurldecode($url).'","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
		$curl_results = curl_exec ($curl);
		curl_close ($curl);
		$json = json_decode($curl_results, true);
		return isset($json[0]['result']['metadata']['globalCounts']['count'])?intval( $json[0]['result']['metadata']['globalCounts']['count'] ):0;
	}

/*****************************************************************
*                                                                *
*          Pinterest - A Function to Fetch Pinterest Shares      *
*                                                                *
******************************************************************/

	function swp_fetch_pinterest_shares($url) {
		$url = rawurlencode($url);
		$return_data = swp_file_get_contents_curl('https://api.pinterest.com/v1/urls/count.json?url='.$url);
		$json_string = preg_replace('/^receiveCount\((.*)\)$/', "\\1", $return_data);
		$json = json_decode($json_string, true);
		return isset($json['count'])?intval($json['count']):0;
	}

/*****************************************************************
*                                                                *
*          LinkedIn - A Function to Fetch LinkedIn Shares        *
*                                                                *
******************************************************************/

	function swp_fetch_linkedIn_shares($url) {
		$url = rawurlencode($url);
		$json_string = swp_file_get_contents_curl('https://www.linkedin.com/countserv/count/share?url='.$url.'&format=json');
		$json = json_decode($json_string, true);
		return isset($json['count'])?intval($json['count']):0;
	}
