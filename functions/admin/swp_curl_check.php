<?php

class swp_curl_check extends swp_custom_check
{
	public function __construct()
	{
		$this->name = 'Curl Check';
	}

	public function run()
	{

		if( function_exists( 'curl_version' ) )
		{
			$this->check_passed = true;
		}
		else
		{
			$this->check_passed = false;
			$this->whats_wrong = 'Your server has cURL disabled. In order for our plugin to fetch share counts, you must have cURL enabled on your server.';
			$this->how_to_fix = 'To fix this, simply contact your hosting provider and ask them to activate cURL on your server.';
		}

		return $this->check_passed;
	}
}

?>