<?php
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'SW_META_Wysiwyg_Field' ) )
{
	class SW_META_Wysiwyg_Field extends SW_META_Field
	{

		static $cloneable_editors = array();

		/**
		 * Enqueue scripts and styles
		 *
		 * @return void
		 */
		static function admin_enqueue_scripts()
		{
			wp_enqueue_style( 'SW_META-meta-box-wysiwyg', SW_META_CSS_URL . 'wysiwyg.css', array(), SW_META_VER );
		}

		/**
		 * Change field value on save
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
			if ( $field['raw'] )
			{
				$meta = $new;
			}
			else if ( $field['clone'] )
			{
				$meta = array_map( 'wpautop', $new );
			}
			else
			{
				$meta = wpautop( $new );
			}

			return $meta;
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
			// Using output buffering because wp_editor() echos directly
			ob_start();

			$field['options']['textarea_name'] = $field['field_name'];

			// Use new wp_editor() since WP 3.3
			wp_editor( $meta, $field['id'], $field['options'] );

			$editor = ob_get_clean();
			if ( $field['clone'] )
			{
				self::$cloneable_editors[$field['id']] = $editor;
				add_action( 'admin_print_footer_scripts', array( __CLASS__, 'footer_scripts' ), 51 );
			}

			return $editor;
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
				'raw'     => false,
				'options' => array(),
			) );

			$field['options'] = wp_parse_args( $field['options'], array(
				'editor_class' => 'SW_META-wysiwyg',
				'dfw'          => true, // Use default WordPress full screen UI
			) );

			// Keep the filter to be compatible with previous versions
			$field['options'] = apply_filters( 'SW_META_wysiwyg_settings', $field['options'] );

			return $field;
		}

		static function footer_scripts()
		{
			echo '<script> var SW_META_cloneable_editors = ' . json_encode( self::$cloneable_editors ) . ';</script>';
		}
	}
}
