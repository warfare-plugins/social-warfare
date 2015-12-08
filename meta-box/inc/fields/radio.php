<?php
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'SW_META_Radio_Field' ) )
{
	class SW_META_Radio_Field extends SW_META_Field
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
			$html = array();
			$tpl  = '<label><input type="radio" class="SW_META-radio" name="%s" value="%s"%s> %s</label>';

			foreach ( $field['options'] as $value => $label )
			{
				$html[] = sprintf(
					$tpl,
					$field['field_name'],
					$value,
					checked( $value, $meta, false ),
					$label
				);
			}

			return implode( ' ', $html );
		}
	}
}
