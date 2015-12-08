<?php
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

// Make sure "text" field is loaded
require_once SW_META_FIELDS_DIR . 'text.php';

if ( ! class_exists( 'SW_META_Password_Field' ) )
{
	class SW_META_Password_Field extends SW_META_Text_Field
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
				'<input type="password" class="SW_META-password" name="%s" id="%s" value="%s" size="%s" />',
				$field['field_name'],
				$field['id'],
				$meta,
				$field['size']
			);
		}
	}
}
