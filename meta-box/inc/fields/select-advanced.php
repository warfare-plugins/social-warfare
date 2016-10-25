<?php
/**
 * Select advanced field which uses select2 library.
 */
class SWPMB_Select_Advanced_Field extends SWPMB_Select_Field
{
	/**
	 * Enqueue scripts and styles
	 */
	public static function admin_enqueue_scripts()
	{
		parent::admin_enqueue_scripts();
		wp_enqueue_style( 'swpmb-select2', SWPMB_CSS_URL . 'select2/select2.css', array(), '4.0.1' );
		wp_enqueue_style( 'swpmb-select-advanced', SWPMB_CSS_URL . 'select-advanced.css', array(), SWP_VERSION );

		wp_register_script( 'swpmb-select2', SWPMB_JS_URL . 'select2/select2.min.js', array(), '4.0.2', true );

		//Localize
		$deps  = array( 'swpmb-select2', 'swpmb-select' );
		$dir   = SWPMB_JS_URL . 'select2/i18n/';
		$file  = str_replace( '_', '-', get_locale() );
		$parts = explode( '-', $file );
		$file  = file_exists( SWPMB_DIR . 'js/select2/i18n/' . $file . '.js' ) ? $file : $parts[0];

		if ( file_exists( SWPMB_DIR . 'js/select2/i18n/' . $file . '.js' ) )
		{
			wp_register_script( 'swpmb-select2-i18n', $dir . $file . '.js', array( 'swpmb-select2' ), '4.0.2', true );
			$deps[] = 'swpmb-select2-i18n';
		}

		wp_enqueue_script( 'swpmb-select', SWPMB_JS_URL . 'select.js', array(), SWP_VERSION, true );
		wp_enqueue_script( 'swpmb-select-advanced', SWPMB_JS_URL . 'select-advanced.js', $deps, SWP_VERSION, true );

	}

	/**
	 * Walk options
	 *
	 * @param mixed $meta
	 * @param array $field
	 * @param mixed $options
	 * @param mixed $db_fields
	 *
	 * @return string
	 */
	public static function walk( $options, $db_fields, $meta, $field )
	{
		$attributes = call_user_func( array( SWP_Meta_Box::get_class_name( $field ), 'get_attributes' ), $field, $meta );
		$walker     = new SWPMB_Select_Walker( $db_fields, $field, $meta );
		$output     = sprintf(
			'<select %s>',
			self::render_attributes( $attributes )
		);

		$output .= '<option></option>';
		$output .= $walker->walk( $options, $field['flatten'] ? - 1 : 0 );
		$output .= '</select>';
		$output .= self::get_select_all_html( $field );
		return $output;
	}

	/**
	 * Normalize parameters for field
	 *
	 * @param array $field
	 * @return array
	 */
	public static function normalize( $field )
	{
		$field = wp_parse_args( $field, array(
			'js_options'  => array(),
			'placeholder' => 'Select an item',
		) );

		$field = parent::normalize( $field );

		$field['js_options'] = wp_parse_args( $field['js_options'], array(
			'allowClear'  => true,
			'width'       => 'none',
			'placeholder' => $field['placeholder'],
		) );

		return $field;
	}

	/**
	 * Get the attributes for a field
	 *
	 * @param array $field
	 * @param mixed $value
	 * @return array
	 */
	public static function get_attributes( $field, $value = null )
	{
		$attributes = parent::get_attributes( $field, $value );
		$attributes = wp_parse_args( $attributes, array(
			'data-options' => wp_json_encode( $field['js_options'] ),
		) );

		return $attributes;
	}
}
