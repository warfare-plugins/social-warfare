<?php
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'SW_META_Range_Field' ) )
{
	class SW_META_Range_Field extends SW_META_Field
	{
		/**
		 * Enqueue styles
		 *
		 * @return void
		 */
		static function admin_enqueue_scripts()
		{
			wp_enqueue_style( 'SW_META-range', SW_META_CSS_URL . 'range.css', array(), SW_META_VER );
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
				'<input type="range" class="SW_META-range" name="%s" id="%s" value="%s" min="%s" max="%s" step="%s" />',
				$field['field_name'],
				$field['id'],
				$meta,
				$field['min'],
				$field['max'],
				$field['step']
			);
		}

		/**
		 * Normalize parameters for field.
		 *
		 * @param array $field
		 *
		 * @return array
		 */
		static function normalize_field( $field )
		{
			$field = wp_parse_args( $field, array(
				'min'  => 0,
				'max'  => 10,
				'step' => 1,
			) );

			return $field;
		}

		/**
		 * Ensure number in range.
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
			$new = intval( $new );
			$min = intval( $field['min'] );
			$max = intval( $field['max'] );

			if ( $new < $min )
			{
				return $min;
			}
			elseif ( $new > $max )
			{
				return $max;
			}

			return $new;
		}
	}
}
