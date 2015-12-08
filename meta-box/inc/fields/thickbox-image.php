<?php
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'SW_META_Thickbox_Image_Field' ) )
{
	class SW_META_Thickbox_Image_Field extends SW_META_Image_Field
	{
		/**
		 * Enqueue scripts and styles
		 *
		 * @return void
		 */
		static function admin_enqueue_scripts()
		{
			parent::admin_enqueue_scripts();

			add_thickbox();
			wp_enqueue_script( 'media-upload' );

			wp_enqueue_script( 'SW_META-thickbox-image', SW_META_JS_URL . 'thickbox-image.js', array( 'jquery' ), SW_META_VER, true );
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
			$i18n_title = apply_filters( 'SW_META_thickbox_image_upload_string', _x( 'Upload Images', 'image upload', 'meta-box' ), $field );

			// Uploaded images
			$html = self::get_uploaded_images( $meta, $field );

			// Show form upload
			$html .= "<a href='#' class='button SW_META-thickbox-upload' data-field_id='{$field['id']}'>{$i18n_title}</a>";

			return $html;
		}

		/**
		 * Get field value
		 * It's the combination of new (uploaded) images and saved images
		 *
		 * @param array $new
		 * @param array $old
		 * @param int   $post_id
		 * @param array $field
		 *
		 * @return array|mixed
		 */
		static function value( $new, $old, $post_id, $field )
		{
			return array_unique( array_merge( $old, $new ) );
		}
	}
}
