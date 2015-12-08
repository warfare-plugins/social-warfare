<?php
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

require_once SW_META_FIELDS_DIR . 'image.php';
if ( ! class_exists( 'SW_META_Image_Advanced_Field' ) )
{
	class SW_META_Image_Advanced_Field extends SW_META_Image_Field
	{
		/**
		 * Enqueue scripts and styles
		 *
		 * @return void
		 */
		static function admin_enqueue_scripts()
		{
			parent::admin_enqueue_scripts();

			// Make sure scripts for new media uploader in WordPress 3.5 is enqueued
			wp_enqueue_media();
			wp_enqueue_script( 'SW_META-image-advanced', SW_META_JS_URL . 'image-advanced.js', array( 'jquery', 'underscore' ), SW_META_VER, true );
			wp_localize_script( 'SW_META-image-advanced', 'SW_METAImageAdvanced', array(
				'frameTitle' => __( 'Select Images', 'meta-box' ),
			) );
		}

		/**
		 * Add actions
		 *
		 * @return void
		 */
		static function add_actions()
		{
			// Do same actions as file field
			parent::add_actions();

			// Attach images via Ajax
			add_action( 'wp_ajax_SW_META_attach_media', array( __CLASS__, 'wp_ajax_attach_media' ) );
			add_action( 'print_media_templates', array( __CLASS__, 'print_templates' ) );
		}

		/**
		 * Ajax callback for attaching media to field
		 *
		 * @return void
		 */
		static function wp_ajax_attach_media()
		{
			$post_id        = isset( $_REQUEST['post_id'] ) ? intval( $_REQUEST['post_id'] ) : 0;
			$field_id       = isset( $_POST['field_id'] ) ? $_POST['field_id'] : 0;
			$attachment_ids = isset( $_POST['attachment_ids'] ) ? (array) $_POST['attachment_ids'] : array();

			check_ajax_referer( "SW_META-attach-media_{$field_id}" );
			foreach ( $attachment_ids as $attachment_id )
			{
				add_post_meta( $post_id, $field_id, $attachment_id, false );
			}
			wp_send_json_success();
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
			$i18n_title   = apply_filters( 'SW_META_image_advanced_select_string', _x( 'Select or Upload Images', 'image upload', 'meta-box' ), $field );
			$attach_nonce = wp_create_nonce( "SW_META-attach-media_{$field['id']}" );

			// Uploaded images
			$html = self::get_uploaded_images( $meta, $field );

			// Show form upload
			$classes = array( 'button', 'SW_META-image-advanced-upload', 'hide-if-no-js', 'new-files' );
			if ( ! empty( $field['max_file_uploads'] ) && count( $meta ) >= (int) $field['max_file_uploads'] )
				$classes[] = 'hidden';

			$classes = implode( ' ', $classes );
			$html .= "<a href='#' class='{$classes}' data-attach_media_nonce={$attach_nonce}>{$i18n_title}</a>";

			return $html;
		}

		/**
		 * Get field value
		 * It's the combination of new (uploaded) images and saved images
		 *
		 * @param array $new
		 * @param array $old
		 * @param int   $post_id
		 * @param array $field
		 *
		 * @return array|mixed
		 */
		static function value( $new, $old, $post_id, $field )
		{
			$new = (array) $new;

			return array_unique( array_merge( $old, $new ) );
		}

		static function print_templates()
		{
			$i18n_delete = apply_filters( 'SW_META_image_delete_string', _x( 'Delete', 'image upload', 'meta-box' ) );
			$i18n_edit   = apply_filters( 'SW_META_image_edit_string', _x( 'Edit', 'image upload', 'meta-box' ) );
			?>
			<script id="tmpl-SW_META-image-advanced" type="text/html">
				<# _.each( attachments, function( attachment ) { #>
					<li id="item_{{{ attachment.id }}}">
						<# if ( attachment.sizes.hasOwnProperty( 'thumbnail' ) ) { #>
							<img src="{{{ attachment.sizes.full.url }}}">
							<# } else { #>
								<img src="{{{ attachment.sizes.full.url }}}">
								<# } #>
									<div class="SW_META-image-bar">
										<a title="<?php echo esc_attr( $i18n_edit ); ?>" class="SW_META-edit-file" href="{{{ attachment.editLink }}}" target="_blank"><?php echo esc_html( $i18n_edit ); ?></a> |
										<a title="<?php echo esc_attr( $i18n_delete ); ?>" class="SW_META-delete-file" href="#" data-attachment_id="{{{ attachment.id }}}">&times;</a>
									</div>
					</li>
					<# } ); #>
			</script>
		<?php
		}

	}
}
