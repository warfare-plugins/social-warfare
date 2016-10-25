<?php
/**
 * File field class which uses HTML <input type="file"> to upload file.
 */
class SWPMB_File_Field extends SWPMB_Field
{
	/**
	 * Enqueue scripts and styles
	 */
	static function admin_enqueue_scripts()
	{
		wp_enqueue_style( 'swpmb-file', SWPMB_CSS_URL . 'file.css', array(), SWP_VERSION );
		wp_enqueue_script( 'swpmb-file', SWPMB_JS_URL . 'file.js', array( 'jquery' ), SWP_VERSION, true );
		wp_localize_script( 'swpmb-file', 'swpmbFile', array(
			'maxFileUploadsSingle' => __( 'You may only upload maximum %d file', 'social-warfare' ),
			'maxFileUploadsPlural' => __( 'You may only upload maximum %d files', 'social-warfare' ),
		) );
	}

	/**
	 * Add custom actions
	 */
	static function add_actions()
	{
		// Add data encoding type for file uploading
		add_action( 'post_edit_form_tag', array( __CLASS__, 'post_edit_form_tag' ) );

		// Delete file via Ajax
		add_action( 'wp_ajax_swpmb_delete_file', array( __CLASS__, 'wp_ajax_delete_file' ) );

		// Allow reordering files
		add_action( 'wp_ajax_swpmb_reorder_files', array( __CLASS__, 'wp_ajax_reorder_files' ) );
	}

	/**
	 * Ajax callback for reordering images
	 */
	static function wp_ajax_reorder_files()
	{
		$post_id  = (int) filter_input( INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT );
		$field_id = (string) filter_input( INPUT_POST, 'field_id' );
		$order    = (string) filter_input( INPUT_POST, 'order' );

		check_ajax_referer( "swpmb-reorder-files_{$field_id}" );
		parse_str( $order, $items );
		delete_post_meta( $post_id, $field_id );
		foreach ( $items['item'] as $item )
		{
			add_post_meta( $post_id, $field_id, $item, false );
		}
		wp_send_json_success();
	}

	/**
	 * Add data encoding type for file uploading
	 *
	 * @return void
	 */
	static function post_edit_form_tag()
	{
		echo ' enctype="multipart/form-data"';
	}

	/**
	 * Ajax callback for deleting files.
	 * Modified from a function used by "Verve Meta Boxes" plugin
	 * @link http://goo.gl/LzYSq
	 */
	static function wp_ajax_delete_file()
	{
		$post_id       = (int) filter_input( INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT );
		$field_id      = (string) filter_input( INPUT_POST, 'field_id' );
		$attachment_id = (int) filter_input( INPUT_POST, 'attachment_id', FILTER_SANITIZE_NUMBER_INT );
		$force_delete  = (int) filter_input( INPUT_POST, 'force_delete', FILTER_SANITIZE_NUMBER_INT );

		check_ajax_referer( "swpmb-delete-file_{$field_id}" );
		delete_post_meta( $post_id, $field_id, $attachment_id );
		$success = $force_delete ? wp_delete_attachment( $attachment_id ) : true;

		if ( $success )
			wp_send_json_success();
		else
			wp_send_json_error( __( 'Error: Cannot delete file', 'social-warfare' ) );
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
		$i18n_title = apply_filters( 'swpmb_file_upload_string', _x( 'Upload Files', 'file upload', 'social-warfare' ), $field );
		$i18n_more  = apply_filters( 'swpmb_file_add_string', _x( '+ Add new file', 'file upload', 'social-warfare' ), $field );

		// Uploaded files
		$html             = self::get_uploaded_files( $meta, $field );
		$new_file_classes = array( 'new-files' );
		if ( ! empty( $field['max_file_uploads'] ) && count( $meta ) >= (int) $field['max_file_uploads'] )
			$new_file_classes[] = 'hidden';

		// Show form upload
		$html .= sprintf(
			'<div class="%s">
				<h4>%s</h4>
				<div class="file-input"><input type="file" name="%s[]" /></div>
				<a class="swpmb-add-file" href="#"><strong>%s</strong></a>
			</div>',
			implode( ' ', $new_file_classes ),
			$i18n_title,
			$field['id'],
			$i18n_more
		);

		return $html;
	}

	static function get_uploaded_files( $files, $field )
	{
		$reorder_nonce = wp_create_nonce( "swpmb-reorder-files_{$field['id']}" );
		$delete_nonce  = wp_create_nonce( "swpmb-delete-file_{$field['id']}" );

		$classes = array( 'swpmb-file', 'swpmb-uploaded' );
		if ( count( $files ) <= 0 )
			$classes[] = 'hidden';
		$list = '<ul class="%s" data-field_id="%s" data-delete_nonce="%s" data-reorder_nonce="%s" data-force_delete="%s" data-max_file_uploads="%s" data-mime_type="%s">';
		$html = sprintf(
			$list,
			implode( ' ', $classes ),
			$field['id'],
			$delete_nonce,
			$reorder_nonce,
			$field['force_delete'] ? 1 : 0,
			$field['max_file_uploads'],
			$field['mime_type']
		);

		foreach ( (array) $files as $attachment_id )
		{
			$html .= self::file_html( $attachment_id );
		}

		$html .= '</ul>';

		return $html;
	}

	static function file_html( $attachment_id )
	{
		$i18n_delete = apply_filters( 'swpmb_file_delete_string', _x( 'Delete', 'file upload', 'social-warfare' ) );
		$i18n_edit   = apply_filters( 'swpmb_file_edit_string', _x( 'Edit', 'file upload', 'social-warfare' ) );
		$item        = '
		<li id="item_%s">
			<div class="swpmb-icon">%s</div>
			<div class="swpmb-info">
				<a href="%s" target="_blank">%s</a>
				<p>%s</p>
				<a title="%s" href="%s" target="_blank">%s</a> |
				<a title="%s" class="swpmb-delete-file" href="#" data-attachment_id="%s">%s</a>
			</div>
		</li>';

		$mime_type = get_post_mime_type( $attachment_id );

		return sprintf(
			$item,
			$attachment_id,
			wp_get_attachment_image( $attachment_id, array( 60, 60 ), true ),
			wp_get_attachment_url( $attachment_id ),
			get_the_title( $attachment_id ),
			$mime_type,
			$i18n_edit,
			get_edit_post_link( $attachment_id ),
			$i18n_edit,
			$i18n_delete,
			$attachment_id,
			$i18n_delete
		);
	}

	/**
	 * Get meta values to save
	 *
	 * @param mixed $new
	 * @param mixed $old
	 * @param int   $post_id
	 * @param array $field
	 *
	 * @return array|mixed
	 */
	static function value( $new, $old, $post_id, $field )
	{
		$name = $field['id'];
		if ( empty( $_FILES[$name] ) )
			return $new;

		$new   = array();
		$files = self::fix_file_array( $_FILES[$name] );

		foreach ( $files as $file_item )
		{
			$file = wp_handle_upload( $file_item, array( 'test_form' => false ) );

			if ( ! isset( $file['file'] ) )
				continue;

			$file_name = $file['file'];

			$attachment = array(
				'post_mime_type' => $file['type'],
				'guid'           => $file['url'],
				'post_parent'    => $post_id,
				'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $file_name ) ),
				'post_content'   => '',
			);
			$id         = wp_insert_attachment( $attachment, $file_name, $post_id );

			if ( ! is_wp_error( $id ) )
			{
				wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $file_name ) );

				// Save file ID in meta field
				$new[] = $id;
			}
		}

		return array_unique( array_merge( $old, $new ) );
	}

	/**
	 * Fixes the odd indexing of multiple file uploads from the format:
	 *     $_FILES['field']['key']['index']
	 * To the more standard and appropriate:
	 *     $_FILES['field']['index']['key']
	 *
	 * @param array $files
	 *
	 * @return array
	 */
	static function fix_file_array( $files )
	{
		$output = array();
		foreach ( $files as $key => $list )
		{
			foreach ( $list as $index => $value )
			{
				$output[$index][$key] = $value;
			}
		}

		return $output;
	}

	/**
	 * Normalize parameters for field
	 *
	 * @param array $field
	 *
	 * @return array
	 */
	static function normalize( $field )
	{
		$field             = parent::normalize( $field );
		$field             = wp_parse_args( $field, array(
			'std'              => array(),
			'force_delete'     => false,
			'max_file_uploads' => 0,
			'mime_type'        => '',
		) );
		$field['multiple'] = true;

		return $field;
	}

	/**
	 * Get the field value
	 * The difference between this function and 'meta' function is 'meta' function always returns the escaped value
	 * of the field saved in the database, while this function returns more meaningful value of the field
	 *
	 * @param  array    $field   Field parameters
	 * @param  array    $args    Not used for this field
	 * @param  int|null $post_id Post ID. null for current post. Optional.
	 *
	 * @return mixed Full info of uploaded files
	 */
	static function get_value( $field, $args = array(), $post_id = null )
	{
		if ( ! $post_id )
			$post_id = get_the_ID();

		/**
		 * Get raw meta value in the database, no escape
		 * Very similar to self::meta() function
		 */
		$file_ids = get_post_meta( $post_id, $field['id'], false );

		// For each file, get full file info
		$value = array();
		foreach ( (array) $file_ids as $file_id )
		{
			if ( $file_info = call_user_func( array( SWP_Meta_Box::get_class_name( $field ), 'file_info' ), $file_id, $args ) )
			{
				$value[$file_id] = $file_info;
			}
		}

		return $value;
	}

	/**
	 * Output the field value
	 * Display unordered list of files
	 *
	 * @param  array    $field   Field parameters
	 * @param  array    $args    Additional arguments. Not used for these fields.
	 * @param  int|null $post_id Post ID. null for current post. Optional.
	 *
	 * @return mixed Field value
	 */
	static function the_value( $field, $args = array(), $post_id = null )
	{
		$value = self::get_value( $field, $args, $post_id );
		if ( ! $value )
			return '';

		$output = '<ul>';
		foreach ( $value as $file_id => $file_info )
		{
			$output .= sprintf(
				'<li><a href="%s" target="_blank">%s</a></li>',
				wp_get_attachment_url( $file_id ),
				get_the_title( $file_id )
			);
		}
		$output .= '</ul>';

		return $output;
	}

	/**
	 * Get uploaded file information
	 *
	 * @param int   $file_id Attachment file ID (post ID). Required.
	 * @param array $args    Array of arguments (for size).
	 *
	 * @return array|bool False if file not found. Array of (id, name, path, url) on success
	 */
	static function file_info( $file_id, $args = array() )
	{
		$path = get_attached_file( $file_id );
		if ( ! $path )
		{
			return false;
		}

		$info = array(
			'ID'    => $file_id,
			'name'  => basename( $path ),
			'path'  => $path,
			'url'   => wp_get_attachment_url( $file_id ),
			'title' => get_the_title( $file_id ),
		);

		return wp_parse_args( $info, wp_get_attachment_metadata( $file_id ) );
	}
}
