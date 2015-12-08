<?php
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'SW_META_Number_Field' ) )
{
	class SW_META_Number_Field extends SW_META_Field
	{
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
				'<input type="number" class="SW_META-number" name="%s" id="%s" value="%s" step="%s" min="%s" placeholder="%s"/>',
				$field['field_name'],
				empty( $field['clone'] ) ? $field['id'] : '',
				$meta,
				$field['step'],
				$field['min'],
				$field['placeholder']
			);
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
				'step' => 1,
				'min'  => 0,
			) );

			return $field;
		}
	}
}
