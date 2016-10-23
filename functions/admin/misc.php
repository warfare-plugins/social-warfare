<?php
/**
 * Miscellaneous admin functions.
 *
 * @package   SocialWarfare\Admin\Functions
 * @copyright Copyright (c) 2016, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     1.0.0
 */

add_filter( 'plugin_action_links_' . plugin_basename( SWP_PLUGIN_FILE ), 'swp_settings_link' );
/**
 * Add a "Settings" link to the listing on the plugins page
 *
 * @since  1.0.0
 * @param  array $links Array of links passed in from WordPress core.
 * @return array $links Array of links modified by the function passed back to WordPress
 */
function swp_settings_link( $links ) {
	$settings_link = sprintf( '<a href="admin.php?page=social-warfare">%s</a>',
		esc_html__( 'Settings', 'social-warfare' )
	);

	array_unshift( $links, $settings_link );

	return $links;
}
