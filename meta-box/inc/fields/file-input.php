<?php
if ( ! class_exists( 'SW_META_File_Input_Field' ) )
{
	class SW_META_File_Input_Field extends SW_META_Field
	{
		/**
		 * Enqueue scripts and styles
		 *
		 * @return void
		 */
		static function admin_enqueue_scripts()
		{
			// Make sure scripts for new media uploader in WordPress 3.5 is enqueued
			wp_enqueue_media();
			wp_enqueue_script( 'SW_META-file-input', SW_META_JS_URL . 'file-input.js', array( 'jquery' ), SW_META_VER, true );
			wp_localize_script( 'SW_META-file-input', 'SW_METAFileInput', array(
				'frameTitle' => __( 'Select File', 'meta-box' ),
			) );
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
				'<input type="text" class="SW_META-file-input" name="%s" id="%s" value="%s" placeholder="%s" size="%s">
				<a href="#" class="SW_META-file-input-select button-primary">%s</a>
				<a href="#" class="SW_META-file-input-remove button %s">%s</a>',
				$field['field_name'],
				$field['id'],
				$meta,
				$field['placeholder'],
				$field['size'],
				__( 'Select', 'meta-box' ),
				$meta ? '' : 'hidden',
				__( 'Remove', 'meta-box' )
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
				'size'        => 30,
				'placeholder' => '',
			) );

			return $field;
		}
	}
}
