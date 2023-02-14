<?php

/**
 * Plugin Name: Social Warfare
 * Plugin URI:  https://warfareplugins.com
 * Description: A plugin to maximize social shares and drive more traffic using the fastest and most intelligent share buttons on the market, calls to action via in-post click-to-tweets, popular posts widgets based on share popularity, link-shortening, Google Analytics and much, much more!
 * Version:     4.4.1
 * Author:      Warfare Plugins
 * Author URI:  https://warfareplugins.com
 * Text Domain: social-warfare
 *
 */
defined( 'WPINC' ) || die;


/**
 * We create these constants here so that we can use them throughout the plugin
 * for things like includes and requires.
 *
 * @since 4.2.0 | 19 NOV 2020 | The str_replace() removes any linebreaks in the string.
 *
 */
define( 'SWP_VERSION', '4.4.1' );
define( 'SWP_DEV_VERSION', '2023.01.20 MASTER' );
define( 'SWP_PLUGIN_FILE', __FILE__ );
define( 'SWP_PLUGIN_URL', str_replace( array("\r", "\n") , '', untrailingslashit( plugin_dir_url( __FILE__ ) ) ) );
define( 'SWP_PLUGIN_DIR', dirname( __FILE__ ) );
define( 'SWP_STORE_URL', 'https://warfareplugins.com' );


/**
 * This will allow shortcodes to be processed in the excerpts. Ours is set up
 * to essentially remove the [shortcode] from being visible in the excerpts so
 * that they don't show up as plain text.
 *
 * @todo This needs to be moved into the Social_Warfare class.
 *
 */
add_filter('the_excerpt', 'do_shortcode', 1);


/**
 * Social Warfare is entirely a class-based, object oriented system. As such, the
 * main function of this file (the main plugin file loaded by WordPress) is to
 * simply load the main Social_Warfare class and then instantiate it. This will,
 * in turn, fire up all the functionality of the plugin.
 *
 */
require_once SWP_PLUGIN_DIR . '/lib/Social_Warfare.php';
new Social_Warfare();
