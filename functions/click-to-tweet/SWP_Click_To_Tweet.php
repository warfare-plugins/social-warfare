<?php

/**
* Functions for creating click to tweets
*
* @package   SocialWarfare\Functions
* @copyright Copyright (c) 2018, Warfare Plugins, LLC
* @license   GPL-3.0+
* @since     1.0.0
* @since     2.4.0 | Feb 23 2018 | Updated class to fit our style guide.
*
*/
class SWP_Click_To_Tweet {

	public function __construct() {
		$this->click_to_tweet();

	}

	public function click_to_tweet() {
		register_activation_hook( __FILE__, array( __CLASS__, 'activation' ) );
		register_deactivation_hook( __FILE__, array( __CLASS__, 'deactivation' ) );

		if ( is_admin() ) {
			$this->register_admin_hooks();
		}
	}

	public function debug( $array ) {
		echo '<pre>';
		print_r( $array );
		echo '</pre>';
	}

	public function activation() {
		register_uninstall_hook( __FILE__, array( __CLASS__, 'uninstall' ) );
	}

	public function deactivation() {

	}

	public function register_admin_hooks() {
		add_filter( 'tiny_mce_version', array( $this, 'refresh_mce' ) );
		add_action( 'init', array( $this, 'tinymce_button' ) );
	}

	public function tinymce_button() {
		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
			return;
		}

		if ( get_user_option( 'rich_editing' ) == 'true' ) {
            die("tinymce_register_button()");
            
			add_filter( 'mce_external_plugins', array( $this, 'tinymce_register_plugin' ) );
			add_filter( 'mce_buttons', array( $this, 'tinymce_register_button' ) );
		}
	}

	public function tinymce_register_button( $buttons ) {
		array_push( $buttons, '|', 'click_to_tweet' );
		return $buttons;
	}

	public function tinymce_register_plugin( $plugin_array ) {
		$plugin_array['click_to_tweet'] = plugins_url( '/assets/js/clickToTweet.js', __FILE__ );
		return $plugin_array;
	}

	public function register_settings() {
		register_setting( 'tmclicktotweet-options', 'twitter-handle', array( $this, 'validate_settings' ) );
	}

	public function validate_settings( $input ) {
		return str_replace( '@', '', strip_tags( stripslashes( $input ) ) );
	}

	public function refresh_mce( $ver ) {
		$ver += 3;
		return $ver;
	}

}
