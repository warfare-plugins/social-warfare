<?php

class SW_META_Loader
{
	static function load( $url, $dir )
	{
		define( 'SW_META_VER', '4.4.1' );

		define( 'SW_META_URL', $url );
		define( 'SW_META_DIR', $dir );

		define( 'SW_META_JS_URL', trailingslashit( SW_META_URL . 'js' ) );
		define( 'SW_META_CSS_URL', trailingslashit( SW_META_URL . 'css' ) );

		define( 'SW_META_INC_DIR', trailingslashit( SW_META_DIR . 'inc' ) );
		define( 'SW_META_FIELDS_DIR', trailingslashit( SW_META_INC_DIR . 'fields' ) );
	}
}
