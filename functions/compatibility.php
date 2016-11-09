<?php
/**
 * Functions to enhance compatibility with other plugins
 *
 * @package   SocialWarfare\Functions
 * @copyright Copyright (c) 2016, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     1.0.0
 */

defined( 'WPINC' ) || die;

/**
 * Disabe Open Graph tags on Simple Podcast Press Pages
 *
 * @since 1.4.0
 * @access public
 */
if ( is_plugin_active( 'simple-podcast-press/simple-podcast-press.php' ) ) {
	global $ob_wp_simplepodcastpress;
	remove_action( 'wp_head' , array( $ob_wp_simplepodcastpress, 'spp_open_graph' ) , 1 );
}
