<?php
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

// Make sure "text" field is loaded
require_once SW_META_FIELDS_DIR . 'text.php';

if ( ! class_exists( 'SW_META_Email_Field' ) )
{
	class SW_META_Email_Field extends SW_META_Text_Field
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
				'<input type="email" class="SW_META-email" name="%s" id="%s" value="%s" size="%s" placeholder="%s"/>',
				$field['field_name'],
				$field['id'],
				$meta,
				$field['size'],
				$field['placeholder']
			);
		}

		/**
		 * Sanitize email
		 *
		 * @param mixed $new
		 * @param mixed $old
		 * @param int   $post_id
		 * @param array $field
		 *
		 * @return string
		 */
		static function value( $new, $old, $post_id, $field )
		{
			if ( $field['clone'] )
			{
				$new = (array) $new;
				$new = array_map( 'sanitize_email', $new );
			}
			else
			{
				$new = sanitize_email( $new );
			}

			return $new;
		}
	}
}
