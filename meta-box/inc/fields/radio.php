<?php
/**
 * Radio field class.
 */
class SWPMB_Radio_Field extends SWPMB_Input_List_Field
{
	/**
	 * Normalize parameters for field
	 * @param array $field
	 * @return array
	 */
	static function normalize( $field )
	{
		$field['multiple'] = false;
		$field = parent::normalize( $field );

		return $field;
	}
}
