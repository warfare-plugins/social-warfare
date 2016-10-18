<?php
/**
 * Custom HTML field class.
 */
class SWPMB_Custom_Html_Field extends SWPMB_Field
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
		$html = ! empty( $field['std'] ) ? $field['std'] : '';
		if ( ! empty( $field['callback'] ) && is_callable( $field['callback'] ) )
		{
			$html = call_user_func_array( $field['callback'], array( $meta, $field ) );
		}
		return $html;
	}
}
