<?php

/**
 * Register and output the icon fonts.
 *
 * @package   SocialWarfare\Functions\Frontend-Output
 * @copyright Copyright (c) 2018, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     1.0.0
 * @since     3.0.0 | 21 FEB 2018 | Updated to a class.
 */
class SWP_Header_Output {


	/**
	 * The global user options array.
	 *
	 * @since 3.0.0
	 * @var array $swp_user_options An array of options as set by the WordPress admin.
	 */


	/**
	 * The local user options array.
	 *
	 * @since 3.0.0
	 * @var array $swp_user_options An array of options as set by the WordPress admin.
	 */
	public $options;


	/**
	 *  This is the magic method that instatiates this class.
	 *
	 *  We pull the global user options into a local property so that we don't have to keep
	 *  pulling in the global in each method in which it is needed.
	 *
	 * @since 3.0.0 | 21 FEB 2018 | Created
	 */
	public function __construct() {
		global $swp_user_options;
		$this->options = $swp_user_options;
		$this->init();
	}

	private function init() {
		add_action( 'wp_head', array( $this, 'add_header_output' ), 1 );
		add_filter( 'swp_header_html', array( $this, 'output_font_css' ), 20 );
		add_action( 'admin_head', array( $this, 'output_font_css' ), 20 );
	}


	/**
	 * The function that we're hooking into the header
	 *
	 * All other items being added to the header will be hooked into
	 * swp_meta_tags which we will call and print via this function.
	 *
	 * @since 1.4.0
	 * @access public
	 * @param  none
	 * @return none
	 */
	public function add_header_output() {

		// Get the global options and the post ID
		$info['postID'] = get_the_ID();

		/**
		 * Create and return the values to be used in the header meta tags
		 *
		 * We use our own proprietary header filter so that anything being added to the head
		 * across our plugin or across any of our addons will all be output at the same time.
		 * Once the filter has been run and the string is fully compiled, then we will use
		 * the WordPress head hook to output the compiled string.
		 *
		 * @since 2.1.4
		 * @access public
		 * @var array $info An array of information
		 * @return array $info The modified array with the 'meta_tag_values' index populated
		 */
		$info = apply_filters( 'swp_header_values', $info );

		/**
		 * Assembles the meta tags, CSS, icon font, and other items that go in <head>
		 *
		 * Note: Each meta tag should begin with PHP_EOL for clean structured HTML output
		 *
		 * @since 2.1.4
		 * @since 4.5.0 | 26 JUL 2024 | Added the wp_kses() function to the output.
		 * @access public
		 * @var array $info An array of information
		 * @return array $info The modified array with the 'html_output' index populated.
		 */
		$meta_html = apply_filters( 'swp_header_html', '' );

		if ( $meta_html ) :
			echo PHP_EOL . '<!-- Social Warfare v' . esc_html( SWP_VERSION ) . ' https://warfareplugins.com - BEGINNING OF OUTPUT -->' . PHP_EOL;
			
			$allowed_html = array(
				'meta' => array(
					'name'    => array(),
					'content' => array(),
					'property'=> array(),
					'http-equiv' => array(),
					'charset' => array(),
				),
				'style' => array(
					'type' => array(),
					'media' => array(),
					'scoped' => array(),
				),
			);			
		
			echo wp_kses( $meta_html, $allowed_html );

			echo PHP_EOL . '<!-- Social Warfare v' . esc_html( SWP_VERSION ) . ' https://warfareplugins.com - END OF OUTPUT -->' . PHP_EOL . PHP_EOL;
		endif;
	}


	/**
	 * Output the CSS to include the icon font.
	 *
	 * Note: This is done in the header rather than in a CSS file to
	 * avoid having the fonts called from a CDN, 95% of which do not
	 * support the necessary mime & cross-origin access types to deliver them.
	 *
	 * @since  1.0.0
	 * @since  4.0.0 | 21 FEB 2020 | Added font-display:block
	 * @access public
	 * @param  array $info An array of information about the post
	 * @return array  $info The modified array
	 */
	public function output_font_css( $meta_html ) {

		/**
		 * The var $meta_html is passed to both string and array filters. The
		 * solution is to re-wire those filters appropriately. This is the patch.
		 */
		if ( is_array( $meta_html ) ) {
			return $meta_html;
		}

		/**
		 * This ensures that the icon font CSS that gets compiled below will
		 * only be generated one time. If it's already been generated and exists
		 * in this string, then bail out.
		 */
		if ( ! empty( $meta_html ) && strpos( $meta_html, 'font-family: "sw-icon-font"' ) ) :
			return $meta_html;
		endif;

		/**
		 * If, for some reason, we have something other than a string here,
		 * convert it into a string and then proceed as planned.
		 */
		if ( false === is_string( $meta_html ) ) {
			$meta_html = '';
		}

		$style = '<style>
	@font-face {
		font-family: "sw-icon-font";
		src:url("' . SWP_PLUGIN_URL . '/assets/fonts/sw-icon-font.eot?ver=' . SWP_VERSION . '");
		src:url("' . SWP_PLUGIN_URL . '/assets/fonts/sw-icon-font.eot?ver=' . SWP_VERSION . '#iefix") format("embedded-opentype"),
		url("' . SWP_PLUGIN_URL . '/assets/fonts/sw-icon-font.woff?ver=' . SWP_VERSION . '") format("woff"),
		url("' . SWP_PLUGIN_URL . '/assets/fonts/sw-icon-font.ttf?ver=' . SWP_VERSION . '") format("truetype"),
		url("' . SWP_PLUGIN_URL . '/assets/fonts/sw-icon-font.svg?ver=' . SWP_VERSION . '#1445203416") format("svg");
		font-weight: normal;
		font-style: normal;
		font-display:block;
	}
</style>';

		/**
		 * If we are in the admin area, then we need to echo this string
		 * directly to the screen. Otherwise, we're going to return the string
		 * so that it will get output via the header hook.
		 */
		if ( true === is_admin() ) {
			echo wp_kses( $style, array( 'style' => array() ) );
		} else {
			if ( empty( $meta_html ) ) :
				$meta_html = '';
			endif;

			$meta_html .= $style;
		}

		return $meta_html;
	}
}
