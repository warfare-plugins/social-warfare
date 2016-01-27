<?php

/*******************************************************
*                                                      *
*	Mobile device detection                            *
*                                                      *
********************************************************/
if( !function_exists('sw_mobile_detection') ){
	function sw_mobile_detection(){
		return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
	}
}