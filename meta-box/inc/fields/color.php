<?php
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'SW_META_Color_Field' ) )
{
	class SW_META_Color_Field extends SW_META_Field
	{
		/**
		 * Enqueue scripts and styles
		 *
		 * @return void
		 */
		static function admin_enqueue_scripts()
		{
			wp_enqueue_style( 'SW_META-color', SW_META_CSS_URL . 'color.css', array( 'wp-color-picker' ), SW_META_VER );
			wp_enqueue_script( 'SW_META-color', SW_META_JS_URL . 'color.js', array( 'wp-color-picker' ), SW_META_VER, true );
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
				'<input class="SW_META-color" type="text" name="%s" id="%s" value="%s" size="%s" />
				<div class="SW_META-color-picker"></div>',
				$field['field_name'],
				empty( $field['clone'] ) ? $field['id'] : '',
				$meta,
				$field['size']
			);
		}

		/**
		 * Don't save '#' when no color is chosen
		 *
		 * @param mixed $new
		 * @param mixed $old
		 * @param int   $post_id
		 * @param array $field
		 *
		 * @return int
		 */
		static function value( $new, $old, $post_id, $field )
		{
			return '#' === $new ? '' : $new;
		}

		/**
		 * Normalize parameters for field
		 *
		 * @param array $field
		 *
		 * @return array
		 */
		static function normalize_field( $field )
		{
			$field = wp_parse_args( $field, array(
				'size' => 7,
			) );

			return $field;
		}
	}
}
