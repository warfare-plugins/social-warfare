<?php

/**
 * Register and enqueue plugin scripts and styles.
 *
 * @package   SocialWarfare\Functions
 * @copyright Copyright (c) 2018, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     1.0.0
 */
class SWP_Script {


	/**
	 * The magic method used to instatiate our class and queue up all of the
	 * add_action and add_filter functions as well as fix a known compatibility
	 * issue with LightSpeed cache.
	 *
	 * @since  1.0.0
	 * @since  3.1.0 | 18 JUNE 2018 | Created add_hooks() and fix_compatability().
	 * @access public
	 * @param  none
	 * @return none
	 */
	public function __construct() {
		$this->add_hooks();
		$this->fix_litespeed_compatibility();
	}


	/**
	 * This method queues up the helper methods of this class to run when
	 * WordPress fires off some of their hooks (e.g. wp_footer hook).
	 *
	 * @since  3.1.0 | 18 JUNE 2018 | Created
	 * @param  void
	 * @return void
	 */
	public function add_hooks() {

		// Queue up our footer hook function
		add_filter( 'swp_footer_scripts', array( $this, 'nonce' ) );
		add_filter( 'swp_footer_scripts', array( $this, 'frame_buster' ) );
		add_filter( 'swp_footer_scripts', array( $this, 'float_before_content' ) );
		add_filter( 'swp_footer_scripts', array( $this, 'ajax_url' ) );
		add_filter( 'swp_footer_scripts', array( $this, 'post_id' ) );

		// Queue up our footer hook function
		add_filter( 'swp_footer_scripts', array( $this, 'click_tracking' ) );
		add_filter( 'swp_javascript_variables', array( $this, 'emphasize_buttons' ) );
		add_filter( 'swp_javascript_variables', array( $this, 'powered_by_variables' ) );

		// Queue up the Social Warfare scripts and styles
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Queue up our hook function
		add_action( 'wp_footer', array( $this, 'footer_functions' ), 99 );
	}


	/**
	 * Helper function for getting the script/style `.min` suffix for minified files.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public static function get_suffix() {
		$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;

		if ( SWP_Utility::debug( 'script' ) ) {
			$debug = true;
		}

		$enabled = (bool) apply_filters( 'swp_enable_suffix', ! $debug );

		return $enabled ? '.min' : '';
	}


	/**
	 * Load front end scripts and styles.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  void
	 * @return void
	 */
	public function enqueue_scripts() {
		$suffix = self::get_suffix();

		wp_enqueue_style(
			'social_warfare',
			SWP_PLUGIN_URL . "/assets/css/style{$suffix}.css",
			array(),
			SWP_VERSION
		);

		if ( false === SWP_AMP::is_amp() ) {
			wp_enqueue_script(
				'social_warfare_script',
				SWP_PLUGIN_URL . "/assets/js/script{$suffix}.js",
				array( 'jquery' ),
				SWP_VERSION,
				true
			);
		}

		$this->localize_variables();
	}


	/**
	 * Load admin scripts and styles.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $screen The ID of the current admin screen.
	 * @return void
	 */
	public function enqueue_admin_scripts( $screen ) {
		$this->enqueue_scripts();

		$suffix = self::get_suffix();

		wp_enqueue_style(
			'social_warfare_admin',
			SWP_PLUGIN_URL . "/assets/css/admin{$suffix}.css",
			array(),
			SWP_VERSION
		);

		wp_enqueue_script(
			'social_warfare_admin_script',
			SWP_PLUGIN_URL . "/assets/js/admin{$suffix}.js",
			array( 'jquery' ),
			SWP_VERSION
		);

		wp_localize_script(
			'social_warfare_admin_script',
			'swp_localize_admin',
			array(
				// 'swp_characters_remaining' => esc_html__( 'Characters Remaining', 'social-warfare' ),
				'swp_characters_remaining' => '',
			)
		);

		$this->localize_variables();
	}


	/**
	 * Queue up our javscript for options and whatnot
	 *
	 * @since 1.4.0
	 * @since 4.4.0 Update script output to convert HTML entities back to characters.
	 * @param Void
	 * @return Void. Echo results directly to the screen.
	 */
	public function footer_functions() {

		if ( SWP_AMP::is_amp() ) {
			return;
		}

		// Fetch a few variables.
		$info['postID']        = get_the_ID();
		$info['footer_output'] = '';

		// Pass the array through our custom filters.
		$info = apply_filters( 'swp_footer_scripts', $info );

		// Clean up and minifiy the output.
		$info['footer_output'] = preg_replace( "/\r|\n/", '', $info['footer_output'] );
		$info['footer_output'] = preg_replace( "/[ ]{2,}|[\t]/", ' ', $info['footer_output'] );
		$info['footer_output'] = preg_replace( '!\s+!', ' ', $info['footer_output'] );

		// If we have output, output it.
		if ( $info['footer_output'] ) {
			$html  = '<script type="text/javascript">';
			$html .= $info['footer_output'];
			$html .= '</script>';

			// Convert special HTML entities back to Characters.
			echo wp_kses( $html, SWP_Section_HTML::get_allowable_html() );
		}
	}


	/**
	 * Enable click tracking in Google Analytics.
	 *
	 * @since  1.4
	 * @access public
	 * @param  array $info An array of footer script information.
	 * @return array $info A modified array of footer script information.
	 */
	public function click_tracking( $info ) {

		// Output the JS variable for click tracking if it is turned on.
		if ( true === SWP_Utility::get_option( 'click_tracking' ) ) {
			$info['footer_output'] .= 'var swpClickTracking = true;';
		} else {
			$info['footer_output'] .= 'var swpClickTracking = false;';
		}

		return $info;
	}


	/**
	 * The Frame Buster Option
	 *
	 * @since  1.4.0
	 * @access public
	 * @param  array $info An array of footer script information.
	 * @return array $info A modified array of footer script information.
	 */
	public function frame_buster( $info ) {
		global $swp_user_options;
		$options = $swp_user_options;

		if ( true === $options['frame_buster'] ) :
			$info['footer_output'] .= PHP_EOL . 'function parentIsEvil() { var html = null; try { var doc = top.location.pathname; } catch(err){ }; if(typeof doc === "undefined") { return true } else { return false }; }; if (parentIsEvil()) { top.location = self.location.href; };var url = "' . get_permalink() . '";if(url.indexOf("stfi.re") != -1) { var canonical = ""; var links = document.getElementsByTagName("link"); for (var i = 0; i < links.length; i ++) { if (links[i].getAttribute("rel") === "canonical") { canonical = links[i].getAttribute("href")}}; canonical = canonical.replace("?sfr=1", "");top.location = canonical; console.log(canonical);};';
		endif;

		return $info;
	}


	/**
	 * A method to fix compatibility with LiteSpeed Cache plugin.
	 *
	 * @since  3.1.0 | 18 JUN 2018 | Created
	 * @param  void
	 * @return void
	 */
	public function fix_litespeed_compatibility() {
		if ( method_exists( 'LiteSpeed_Cache_API', 'esi_enabled' ) && LiteSpeed_Cache_API::esi_enabled() ) :
			LiteSpeed_Cache_API::hook_tpl_esi( 'swp_esi', array( $this, 'hook_esi' ) );
		endif;
	}


	/**
	 * Create a nonce for added security
	 *
	 * @since  2.1.4
	 * @access public
	 * @param  array $info An array of footer script information.
	 * @return array $info A modified array of footer script information.
	 */
	public function nonce( $info ) {

		// To make sure LSCWP ESI is on
		if ( method_exists( 'LiteSpeed_Cache_API', 'esi_enabled' ) && LiteSpeed_Cache_API::esi_enabled() ) {
			// To make sure is using the compatible API version
			if ( method_exists( 'LiteSpeed_Cache_API', 'v' ) && LiteSpeed_Cache_API::v( '1.3' ) ) {
				// Let's turn this block to ESI and return
				$info['footer_output'] .= LiteSpeed_Cache_API::esi_url( 'swp_esi', 'Social Warfare', array(), 'default', true );
				return $info;
			}
		}

		// Create a nonce
		$info['footer_output'] .= ' var swp_nonce = "' . wp_create_nonce() . '";';
		return $info;
	}


	/**
	 * Ensure the ajaxurl gets output to the screen.
	 *
	 * @since  4.0.0 | 24 FEB 2020 | Created
	 * @access public
	 * @param  array $info An array of footer script information.
	 * @return array $info A modified array of footer script information.
	 */
	public function ajax_url( $info ) {

		// Create a variable containing the AJAX url.
		$info['footer_output'] .= ' var swp_ajax_url = "' . admin_url( 'admin-ajax.php' ) . '";';
		return $info;
	}


	/**
	 * Ensure the ajax requests have a valid post_id to work with.
	 *
	 * @since  4.0.0 | 24 FEB 2020 | Created
	 * @access public
	 * @param  array $info An array of footer script information.
	 * @return array $info A modified array of footer script information.
	 */
	public function post_id( $info ) {

		// Create a variable containing the AJAX url.
		if ( true === is_singular() ) {
			$info['footer_output'] .= ' var swp_post_id = "' . get_the_ID() . '";';
		}
		return $info;
	}


	/**
	 * Echoes selected admin settings from the database to javascript.
	 *
	 * @since  3.1.0 | 27 JUN 2018 | Created the method.
	 * @access public
	 * @return void
	 */
	public function float_before_content( $vars ) {
		global $swp_user_options;
		$options = $swp_user_options;

		$float_before_content = $options['float_before_content'];

		$vars['footer_output'] .= 'var swpFloatBeforeContent = ' . wp_json_encode( $float_before_content ) . ';';

		return $vars;
	}


	/**
	 * Creates the `socialWarfare` object and initializes it with server-side data.
	 *
	 * We'll use this to also add a little bit of data that the JS file can use
	 * to accomplish its functionality like the post ID, for example.
	 *
	 * @since 3.4.0 | 20 NOV 2018 | Created
	 * @since 3.6.1 | 17 MAY 2019 | Changed $addons to $installed_addons to avoid
	 *                              using the parent variable within its own loop.
	 * @param  void
	 * @return void
	 */
	public function localize_variables() {
		global $post;

		/**
		 * The post ID will be null/unset if we are on the plugin's admin
		 * settings page. As such, we'll just use 0.
		 */
		$id = isset( $post ) ? $post->ID : 0;

		/**
		 * We'll fetch all the registered addons so that we can list the key of
		 * each one in the socialWarfare.addons variable.
		 */
		$installed_addons = apply_filters( 'swp_registrations', array() );
		$js_variables     = apply_filters( 'swp_javascript_variables', array() );

		/**
		 * Loop through all of the addons that we found and fetch the key for
		 * each one. The key should be the only information we need on the
		 * front-end. Example keys: "pro", "affiliatewp", etc.
		 */
		$addons = array();
		foreach ( $installed_addons as $addon ) {
			$addons[] = $addon->key;
		}

		/**
		 * Once all the data has been collected, we'll organize it into a single
		 * variable for output.
		 */
		$data = array(
			'addons'             => $addons,
			'post_id'            => $id,
			'variables'          => $js_variables,
			'floatBeforeContent' => SWP_Utility::get_option( 'float_before_content' ),
		);

		wp_localize_script( 'social_warfare_script', 'socialWarfare', $data );
	}


	/**
	 * Add LiteSpeed ESI hook for nonce cache
	 *
	 * @access public
	 * @param  void
	 * @return void
	 */
	public function hook_esi() {
		echo ' var swp_nonce = "' . esc_js( wp_create_nonce() ) . '";';
		exit;
	}


	/**
	 * A method for outputting the "Emphasize Buttons" server variable.
	 *
	 * @since  4.0.0 | 13 JUL 2019 | Created
	 * @param  array $variables An array of server variables to be sent to the JS
	 * @return array The modified array of server variables to be sent to the JS
	 */
	public function emphasize_buttons( $variables ) {
		$variables['emphasizeIcons'] = SWP_Utility::get_option( 'emphasized_icon' );
		return $variables;
	}

	public function powered_by_variables( $variables ) {
		$variables['powered_by_toggle'] = SWP_Utility::get_option( 'powered_by_toggle' );

		$affiliate_link = SWP_Utility::get_option( 'affiliate_link' );
		if ( false === $affiliate_link || empty( $affiliate_link ) || '#' === $affiliate_link ) {
			$affiliate_link = 'https://warfareplugins.com';
		}

		$variables['affiliate_link'] = $affiliate_link;
		return $variables;
	}
}
