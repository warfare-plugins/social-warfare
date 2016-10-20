<?php

if ( ! class_exists( 'swp_shortcode_generator' ) ) {

	class swp_shortcode_generator {

		public function __construct() {
			$this->swp_shortcode_generator();
		}

		public function swp_shortcode_generator() {
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
				add_filter( 'mce_external_plugins', array( $this, 'tinymce_register_plugin' ) );
				add_filter( 'mce_buttons', array( $this, 'tinymce_register_button' ) );
			}
		}

		public function tinymce_register_button( $buttons ) {
			array_push( $buttons, '|', 'swp_shortcode_generator' );
			return $buttons;
		}

		public function tinymce_register_plugin( $plugin_array ) {
			$plugin_array['swp_shortcode_generator'] = SWP_PLUGIN_URL . '/js/sw-shortcode-generator.js';
			return $plugin_array;
		}

		public function refresh_mce( $ver ) {
			$ver += 3;
			return $ver;
		}
	}

	new swp_shortcode_generator();

}// End if().
