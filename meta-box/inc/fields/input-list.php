<?php
/**
 * Input list field.
 */
class SWPMB_Input_List_Field extends SWPMB_Choice_Field
{
	/**
	 * Enqueue scripts and styles
	 */
	public static function admin_enqueue_scripts()
	{
		wp_enqueue_style( 'swpmb-input-list', SWPMB_CSS_URL . 'input-list.css', array(), SWP_VERSION );
		wp_enqueue_script( 'swpmb-input-list', SWPMB_JS_URL . 'input-list.js', array(), SWP_VERSION, true );
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
		$walker = new SWPMB_Input_List_Walker( $db_fields, $field, $meta );
		$output = sprintf( '<ul class="swpmb-input-list %s %s">',
			$field['collapse'] ? 'collapse' : '',
		 	$field['inline']   ? 'inline'   : ''
		);
		$output .= $walker->walk( $options, $field['flatten'] ? - 1 : 0 );
		$output .= '</ul>';

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
		$field = $field['multiple'] ? SWPMB_Multiple_Values_Field::normalize( $field ) : $field;
		$field = SWPMB_Input_Field::normalize( $field );
		$field = parent::normalize( $field );
		$field = wp_parse_args( $field, array(
			'collapse' => true,
			'inline'   => null,
		) );

		$field['flatten'] = $field['multiple'] ? $field['flatten'] : true;
		$field['inline'] = ! $field['multiple'] && ! isset( $field['inline'] ) ? true : $field['inline'];

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
		$attributes           = SWPMB_Input_Field::get_attributes( $field, $value );
		$attributes['id']     = false;
		$attributes['type']   = $field['multiple'] ? 'checkbox' : 'radio';
		$attributes['value']  = $value;

		return $attributes;
	}

	/**
	 * Output the field value
	 * Display option name instead of option value
	 *
	 * @use self::meta()
	 *
	 * @param  array    $field   Field parameters
	 * @param  array    $args    Additional arguments. Rarely used. See specific fields for details
	 * @param  int|null $post_id Post ID. null for current post. Optional.
	 *
	 * @return mixed Field value
	 */
	public static function the_value( $field, $args = array(), $post_id = null )
	{
		$value = parent::get_value( $field, $args, $post_id );
		return empty( $value ) ? '' : $field['options'][$value];
	}
}
