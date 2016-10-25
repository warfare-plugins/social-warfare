<?php
/**
 * File advanced field class which users WordPress media popup to upload and select files.
 */
class SWPMB_File_Upload_Field extends SWPMB_File_Advanced_Field
{
	/**
	 * Add actions
	 *
	 * @return void
	 */
	static function add_actions()
	{
		parent::add_actions();
		// Print attachment templates
		add_action( 'print_media_templates', array( __CLASS__, 'print_templates' ) );
	}

	/**
	 * Enqueue scripts and styles
	 *
	 * @return void
	 */
	static function admin_enqueue_scripts()
	{
		parent::admin_enqueue_scripts();
		wp_enqueue_style( 'swpmb-upload', SWPMB_CSS_URL . 'upload.css', array( 'swpmb-media' ), SWP_VERSION );
		wp_enqueue_script( 'swpmb-file-upload', SWPMB_JS_URL . 'file-upload.js', array( 'swpmb-media' ), SWP_VERSION, true );
	}

	/**
	 * Template for media item
	 * @return void
	 */
	static function print_templates()
	{
		require_once( SWPMB_INC_DIR . 'templates/upload.php' );
	}
}
