<?php
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'SW_META_Heading_Field' ) )
{
	class SW_META_Heading_Field extends SW_META_Field
	{
		/**
		 * Enqueue scripts and styles
		 *
		 * @return void
		 */
		static function admin_enqueue_scripts()
		{
			wp_enqueue_style( 'SW_META-heading', SW_META_CSS_URL . 'heading.css', array(), SW_META_VER );
		}

		/**
		 * Show begin HTML markup for fields
		 *
		 * @param mixed $meta
		 * @param array $field
		 *
		 * @return string
		 */
		static function begin_html( $meta, $field )
		{
			return sprintf( '<h4>%s</h4>', $field['name'] );
		}

		/**
		 * Show end HTML markup for fields
		 *
		 * @param mixed $meta
		 * @param array $field
		 *
		 * @return string
		 */
		static function end_html( $meta, $field )
		{
			$id = $field['id'] ? " id='{$field['id']}-description" : '';

			return $field['desc'] ? "<p{$id} class='description'>{$field['desc']}</p>" : '';
		}
	}
}
