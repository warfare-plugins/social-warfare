<?php
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

// Make sure "text" field is loaded
require_once SW_META_FIELDS_DIR . 'url.php';

if ( ! class_exists( 'SW_META_OEmbed_Field' ) )
{
	class SW_META_OEmbed_Field extends SW_META_URL_Field
	{
		/**
		 * Enqueue scripts and styles
		 *
		 * @return void
		 */
		static function admin_enqueue_scripts()
		{
			wp_enqueue_style( 'SW_META-oembed', SW_META_CSS_URL . 'oembed.css' );
			wp_enqueue_script( 'SW_META-oembed', SW_META_JS_URL . 'oembed.js', array(), SW_META_VER, true );
		}

		/**
		 * Add actions
		 *
		 * @return void
		 */
		static function add_actions()
		{
			add_action( 'wp_ajax_SW_META_get_embed', array( __CLASS__, 'wp_ajax_get_embed' ) );
		}

		/**
		 * Ajax callback for returning oEmbed HTML
		 *
		 * @return void
		 */
		static function wp_ajax_get_embed()
		{
			$url = isset( $_POST['url'] ) ? $_POST['url'] : '';
			wp_send_json_success( self::get_embed( $url ) );
		}

		/**
		 * Get embed html from url
		 *
		 * @param string $url
		 *
		 * @return string
		 */
		static function get_embed( $url )
		{
			$embed = @wp_oembed_get( $url );

			return $embed ? $embed : __( 'Embed HTML not available.', 'meta-box' );
		}

		/**
		 * Get field HTML
		 *
		 * @param mixed $meta
		 * @param array $field
		 *
		 * @return string
		 */
		static function html( $meta, $field )
		{
			return sprintf(
				'<input type="url" class="SW_META-oembed" name="%s" id="%s" value="%s" size="%s">
				<a href="#" class="show-embed button">%s</a>
				<span class="spinner"></span>
				<div class="embed-code">%s</div>',
				$field['field_name'],
				$field['id'],
				$meta,
				$field['size'],
				__( 'Preview', 'meta-box' ),
				$meta ? self::get_embed( $meta ) : ''
			);
		}
	}
}
