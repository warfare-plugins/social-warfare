<?php
/**
 * Validation module.
 * @package Meta Box
 */

/**
 * Validation class.
 */
class SWPMB_Validation
{
	/**
	 * Add hooks when module is loaded.
	 */
	public function __construct()
	{
		add_action( 'swpmb_after', array( $this, 'rules' ) );
		add_action( 'swpmb_enqueue_scripts', array( $this, 'scripts' ) );
	}

	/**
	 * Output validation rules of each meta box.
	 * The rules are outputted in [data-rules] attribute of an hidden <script> and will be converted into JSON by JS.
	 * @param SWP_Meta_Box $object Meta Box object
	 */
	public function rules( SWP_Meta_Box $object )
	{
		if ( ! empty( $object->meta_box['validation'] ) )
		{
			echo '<script type="text/html" class="swpmb-validation-rules" data-rules="' . esc_attr( json_encode( $object->meta_box['validation'] ) ) . '"></script>';
		}
	}

	/**
	 * Enqueue scripts for validation.
	 */
	public function scripts()
	{
		wp_enqueue_script( 'jquery-validate', SWPMB_JS_URL . 'jquery.validate.min.js', array( 'jquery' ), SWP_VERSION, true );
		wp_enqueue_script( 'swpmb-validate', SWPMB_JS_URL . 'validate.js', array( 'jquery-validate' ), SWP_VERSION, true );
		wp_localize_script( 'swpmb-validate', 'swpmbValidate', array(
			'summaryMessage' => __( 'Please correct the errors highlighted below and try again.', 'social-warfare' ),
		) );
	}
}
