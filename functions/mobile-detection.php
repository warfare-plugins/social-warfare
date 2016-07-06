<?php

/*******************************************************
*                                                      *
*	Mobile device detection                            *
*                                                      *
********************************************************/
if( !function_exists('swp_mobile_detection') ){
	function swp_mobile_detection(){
		return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
	}
}