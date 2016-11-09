<?php
/**
 * Functions for creating click to tweets
 *
 * @package   SocialWarfare\Functions
 * @copyright Copyright (c) 2016, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     1.0.0
 */

defined( 'WPINC' ) || die;

/**
 * clickToTweet class
 */
if ( ! class_exists( 'clickToTweet' ) ) {

	class clickToTweet {

		public function __construct() {
			$this->clickToTweet();
		}

		public function clickToTweet() {
			register_activation_hook( __FILE__, array( __CLASS__, 'activation' ) );
			register_deactivation_hook( __FILE__, array( __CLASS__, 'deactivation' ) );

			if ( is_admin() ) {
				$this->register_admin_hooks();
			}
		}

		public function debug( $array ) {
			echo '<pre>';
			print_r( $array );
			echo '</pre>';
		}

		public function activation() {
			register_uninstall_hook( __FILE__, array( __CLASS__, 'uninstall' ) );
		}

		public function deactivation() {

		}

		public function register_admin_hooks() {
			add_filter( 'tiny_mce_version', array( $this, 'refresh_mce' ) );
			add_action( 'init', array( $this, 'tinymce_button' ) );
		}

		public function tinymce_button() {
			if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
				return;
			}

			if ( get_user_option( 'rich_editing' ) == 'true' ) {
				add_filter( 'mce_external_plugins', array( $this, 'tinymce_register_plugin' ) );
				add_filter( 'mce_buttons', array( $this, 'tinymce_register_button' ) );
			}
		}

		public function tinymce_register_button( $buttons ) {
			array_push( $buttons, '|', 'clickToTweet' );
			return $buttons;
		}

		public function tinymce_register_plugin( $plugin_array ) {
			$plugin_array['clickToTweet'] = plugins_url( '/assets/js/clickToTweet.js', __FILE__ );
			return $plugin_array;
		}

		public function register_settings() {
			register_setting( 'tmclicktotweet-options', 'twitter-handle', array( $this, 'validate_settings' ) );
		}

		public function validate_settings( $input ) {
			return str_replace( '@', '', strip_tags( stripslashes( $input ) ) );
		}

		public function refresh_mce( $ver ) {
			$ver += 3;
			return $ver;
		}
	}

	new clickToTweet();

}// End if().

/**
 * The function to build the click to tweets
 * @param  array $atts An array of attributes
 * @return string The html of a click to tweet
 */
function clickToTweetShortcode( $atts ) {
	global $swp_user_options;

	$url = swp_process_url( get_permalink() , 'twitter' , get_the_ID() );
	(strpos( $atts['tweet'],'http' ) !== false ? $urlParam = '&url=/' : $urlParam = '&url=' . $url );
	$atts['tweet'] = rtrim( $atts['tweet'] );

	$options = $swp_user_options;
	$user_twitter_handle = get_post_meta( get_the_ID() , 'swp_twitter_username' , true );
	if ( ! $user_twitter_handle ) :
		$user_twitter_handle = $options['twitterID'];
	endif;

	if ( isset( $atts['theme'] ) && $atts['theme'] != 'default' ) :
		$theme = $atts['theme'];
	else :
		$theme = $options['cttTheme'];
	endif;

	return '
		<div class="sw-tweet-clear"></div>
		<a class="swp_CTT ' . $theme . '" href="https://twitter.com/share?text=' . urlencode( html_entity_decode( $atts['tweet'], ENT_COMPAT, 'UTF-8' ) ) . $urlParam . '' . ($user_twitter_handle ? '&via=' . str_replace( '@','',$user_twitter_handle ) : '') . '" data-link="https://twitter.com/share?text=' . urlencode( html_entity_decode( $atts['tweet'], ENT_COMPAT, 'UTF-8' ) ) . $urlParam . '' . ($user_twitter_handle ? '&via=' . str_replace( '@','',$user_twitter_handle ) : '') . '" rel="nofollow" target="_blank"><span class="sw-click-to-tweet"><span class="sw-ctt-text">' . $atts['quote'] . '</span><span class="sw-ctt-btn">' . __( 'Click To Tweet','social-warfare' ) . '<i class="sw sw-twitter"></i></span></span></a>';
}

add_shortcode( 'clickToTweet', 'clickToTweetShortcode' );
