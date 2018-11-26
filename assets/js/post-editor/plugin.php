<?php
/**
 * Plugin Name: post-editor — CGB Gutenberg Block Plugin
 * Plugin URI: https://github.com/ahmadawais/create-guten-block/
 * Description: post-editor — is a Gutenberg plugin created via create-guten-block.
 * Author: mrahmadawais, maedahbatool
 * Author URI: https://AhmadAwais.com/
 * Version: 1.0.0
 * License: GPL2+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package CGB
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Block Initializer.
 */

 function register_gutenberg_blocks() {
	 $scripts = array(
 		'editor_script' => 'social-warfare-block-js',
 		'block-script'	=> 'social-warfare-block-js'
 	);

 	wp_register_script(
 		'social-warfare-block-css',
 		plugins_url( '/post-editor/dist/blocks.style.build.css', dirname( __FILE__ ) ),
 		array( 'wp-blocks' ),
		true
 	);

	wp_register_script(
		'social-warfare-block-js',
		plugins_url( '/post-editor/dist/blocks.build.js', dirname( __FILE__ ) ),
		array( 'wp-blocks', 'wp-i18n', 'wp-element' ),
		true
	);

	register_block_type( 'social-warfare/social-warfare', $scripts);
	register_block_type( 'social-warfare/click-to-tweet', $scripts);
	register_block_type( 'social-warfare/pinterest-image', $scripts);
 }


 add_action( 'init', 'register_gutenberg_blocks' );

 // add_action( 'enqueue_block_assets', 'register_gutenberg_blocks' );

 /**
  * Enqueue Gutenberg block assets for backend editor.
  *
  * `wp-blocks`: includes block type registration and related functions.
  * `wp-element`: includes the WordPress Element abstraction for describing the structure of your blocks.
  * `wp-i18n`: To internationalize the block's text.
  *
  * @since 1.0.0
  */
 function post_editor_cgb_editor_assets() {
 	// Scripts.

 }

 add_action( 'enqueue_block_editor_assets', 'post_editor_cgb_editor_assets' );

 function add_block_category( $categories, $post ) {
     return array_merge(
         $categories,
         array(
             array(
                 'slug' => 'social-warfare',
                 'title' => __( 'Social Warfare', 'social-warfare' ),
                 'icon'  => '<i className="mce-ico mce-i-sw sw sw-social-warfare" />',
             ),
         )
     );
 }
 add_filter( 'block_categories', 'add_block_category', 10, 2 );
