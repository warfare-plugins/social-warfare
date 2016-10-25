<?php
/**
 * Select field class.
 */
class SWPMB_Select_Field extends SWPMB_Choice_Field
{
	/**
	 * Enqueue scripts and styles
	 */
	public static function admin_enqueue_scripts()
	{
		wp_enqueue_style( 'swpmb-select', SWPMB_CSS_URL . 'select.css', array(), SWP_VERSION );
		wp_enqueue_script( 'swpmb-select', SWPMB_JS_URL . 'select.js', array(), SWP_VERSION, true );
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
		if ( false === $field['multiple'] )
		{
			if( isset( $field['placeholder'] ) && $field['placeholder'] != '') {
				$output .= "<option value=''>{$field['placeholder']}</option>";
			}
		}
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
		$field = parent::normalize( $field );
		$field = $field['multiple'] ? SWPMB_Multiple_Values_Field::normalize( $field ) : $field;
		$field = wp_parse_args( $field, array(
			'size'            => $field['multiple'] ? 5 : 0,
			'select_all_none' => false,
		) );

		return $field;
	}

	/**
	 * Get the attributes for a field
	 *
	 * @param array $field
	 * @param mixed $value
	 *
	 * @return array
	 */
	public static function get_attributes( $field, $value = null )
	{
		$attributes = parent::get_attributes( $field, $value );
		$attributes = wp_parse_args( $attributes, array(
			'multiple' => $field['multiple'],
			'size'     => $field['size'],
		) );

		return $attributes;
	}

	/**
	 * Get html for select all|none for multiple select
	 *
	 * @param array $field
	 * @return string
	 */
	public static function get_select_all_html( $field )
	{
		if ( $field['multiple'] && $field['select_all_none'] )
		{
			return '<div class="swpmb-select-all-none">' . __( 'Select', 'social-warfare' ) . ': <a data-type="all" href="#">' . __( 'All', 'social-warfare' ) . '</a> | <a data-type="none" href="#">' . __( 'None', 'social-warfare' ) . '</a></div>';
		}
		return '';
	}
}
