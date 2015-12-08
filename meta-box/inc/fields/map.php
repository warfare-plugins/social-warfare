<?php
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'SW_META_Map_Field' ) )
{
	class SW_META_Map_Field extends SW_META_Field
	{
		/**
		 * Enqueue scripts and styles
		 *
		 * @return void
		 */
		static function admin_enqueue_scripts()
		{
			wp_enqueue_script( 'googlemap', 'https://maps.google.com/maps/api/js?sensor=false', array(), '', true );
			wp_enqueue_script( 'SW_META-map', SW_META_JS_URL . 'map.js', array( 'jquery', 'jquery-ui-autocomplete', 'googlemap' ), SW_META_VER, true );
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
			$address = isset( $field['address_field'] ) ? $field['address_field'] : false;

			$html = '<div class="SW_META-map-field">';

			$html .= sprintf(
				'<div class="SW_META-map-canvas" style="%s"%s></div>
				<input type="hidden" name="%s" class="SW_META-map-coordinate" value="%s">',
				isset( $field['style'] ) ? $field['style'] : '',
				isset( $field['std'] ) ? " data-default-loc=\"{$field['std']}\"" : '',
				$field['field_name'],
				$meta
			);

			if ( $address )
			{
				$html .= sprintf(
					'<button class="button SW_META-map-goto-address-button" value="%s">%s</button>',
					is_array( $address ) ? implode( ',', $address ) : $address,
					__( 'Find Address', 'meta-box' )
				);
			}

			$html .= '</div>';

			return $html;
		}
	}
}
