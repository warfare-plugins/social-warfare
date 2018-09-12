<?php

/**
 * A class of functions used to render shortcodes for the user
 *
 * The SWP_Shortcodes Class used to add our shorcodes to WordPress
 * registry of registered functions.
 *
 * @package   SocialWarfare\Frontend-Output
 * @copyright Copyright (c) 2018, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     1.0.0
 * @since     3.0.0 | 19 FEB 2018 | Refactored into a class-based system
 *
 */
class SWP_Shortcode {
	/**
	 * Constructs a new SWP_Shortcodes instance
	 *
	 * This function is used to add our shortcodes to WordPress' registry of
	 * shortcodes and to map our functions to each one.
	 *
	 * @since  3.0.0
	 * @param  none
	 * @return none
	 *
	 */
    public function __construct() {
		add_shortcode( 'social_warfare', array( $this, 'buttons_shortcode' ) );
		add_shortcode( 'total_shares', array ( $this, 'post_total_shares' ) );
		add_shortcode( 'sitewide_shares', array ( $this, 'sitewide_total_shares' ) );
        add_shortcode( 'click_to_tweet', array( $this, 'click_to_tweet' ) );

		/**
		 * These are old legacy shortcodes that have been replaced with the ones seen above.
		 * We're leaving these here to ensure that it won't break for anyone who has used these
		 * ones in the past. The ones above adhere to our code style guide.
		 *
		 */
        add_shortcode( 'clickToTweet', array($this, 'click_to_tweet' ) );
		add_shortcode( 'socialWarfare', array($this, 'buttons_shortcode' ) );
	}


	/**
	 * Processing the shortcodes that populate a
	 * set of social sharing buttons directly in a WordPress post.
	 *
	 * This function will accept an array of arguments which WordPress
	 * will create from the shortcode attributes.
	 *
	 * @since  3.0.0
	 * @param  $atts Array An array converted from shortcode attributes.
	 *
	 * 		content: The content for the Social Warfare function to filter. In the case of
	 * 			shortcodes, this will be blank since this isn't a content filter.
	 *
	 * 		where: The buttons are designed to be appended to the content. This default
	 * 			tells the buttons to append after the content. Since shortcodes don't have
	 * 			any content, they'll just produce and return the HTML without any content.
	 * 			This will likely never actually be set by the shortcode, but is necessary
	 * 			for the HTML generator to know what to do.
	 *
	 * 		echo: True echos the HTML to the screen. False returns the HTML as a string.
	 *
	 * @return string The HTML of the Social Warfare buttons.
	 *
	 */
	public function buttons_shortcode( $args ) {

		if( !is_array($args) ):
			$args = array();
		endif;

		$buttons_panel = new SWP_Buttons_Panel( $args, true );
		return $buttons_panel->render_HTML();
	}


	/**
	 * This is used to process the total shares across all tracked
	 * social networks for any given WordPress post.
	 *
	 * This function will accept an array of arguments which WordPress
	 * will create from the shortcode attributes. However, it doesn't actually
	 * use any parameters. It is only included to prevent throwing an error
	 * in the event that someone tries to input a parameter on it.
	 *
	 * @since  3.0.0
	 * @param  $atts Array An array converted from shortcode attributes.
	 * @return string A string of text representing the total shares for the post.
	 *
	 */
	public function post_total_shares( $settings ) {
			$total_shares = get_post_meta( get_the_ID() , '_total_shares', true );
			$total_shares = SWP_Utility::kilomega( $total_shares );
			return $total_shares;
	}


	/**
	 * This is used to process the total shares across all tracked
	 * social networks for all posts across the site as an aggragate count.
	 *
	 * This function will accept an array of arguments which WordPress
	 * will create from the shortcode attributes. However, it doesn't actually
	 * use any parameters. It is only included to prevent throwing an error
	 * in the event that someone tries to input a parameter on it.
	 *
	 * @since  3.0.0
	 * @param  $atts Array An array converted from shortcode attributes.
	 * @return string A string of text representing the total sitewide shares.
	 *
	 */
	public function sitewide_total_shares( $settings ) {
			global $wpdb;
			$sum = $wpdb->get_results( "SELECT SUM(meta_value) AS total FROM $wpdb->postmeta WHERE meta_key = '_total_shares'" );
			return SWP_Utility::kilomega( $sum[0]->total );
	}

    /**
     * The function to build the click to tweets
     *
     * @param  array $atts The shortcode key/value attributes.
     * @return string The html of a click to tweet
     */
    function click_to_tweet( $atts ) {
        global $post;

        if ( strpos( $atts['tweet'], 'http' ) > -1 ) :
            /**
             * They included a link in the tweet text. Do not pass a &url paramter.
             *
             * Twitter will diregard value if it is: empty, a whitespace, or %20.
             * Instead, give it an invalid URL! It achieves the targeted effect. 
             *
            */
            $url = '&url=x';
        else :
            $url = '&url=' . SWP_URL_Management::process_url( get_permalink() , 'twitter' , get_the_ID() );
        endif;

    	$user_twitter_handle = get_post_meta( get_the_ID() , 'swp_twitter_username' , true );

    	if ( ! $user_twitter_handle ) :
    		$user_twitter_handle = SWP_Utility::get_option( 'twitter_id' );
    	endif;

    	if ( isset( $atts['theme'] ) && $atts['theme'] != 'default' ) :
    		$theme = $atts['theme'];
    	else :
    		$theme = SWP_Utility::get_option( 'ctt_theme' );
    	endif;

        $tweet = $this->get_tweet( $atts );

        $via = ($user_twitter_handle ? '&via=' . str_replace( '@','',$user_twitter_handle ) : '');

        $html = '<div class="sw-tweet-clear"></div>';
        $html .= '<a class="swp_CTT ' . $theme;
        $html .= '" href="https://twitter.com/share?text=' . $tweet . $via . $url;
        $html .= '" data-link="https://twitter.com/share?text=' . $tweet . $via . $url;
        $html .= '" rel="nofollow noreferrer noopener" target="_blank">';
            $html .= '<span class="sw-click-to-tweet">';
                $html .= '<span class="sw-ctt-text">';
                    $html .= $atts['quote'];
                $html .= '</span>';
                $html .= '<span class="sw-ctt-btn">';
                    $html .= __( 'Click To Tweet','social-warfare' );
                    $html .= '<i class="sw swp_twitter_icon"></i>';
            $html .= '</span>';
            $html .= '</span>';
        $html .= '</a>';

    	return $html;
    }

    /**
     *
     * Retrieves tweet from database and converts to UTF-8 for Twitter.
     *
     * @since  3.3.0 | 16 AUG 2018 | Created. Ported code from $this->generate_share_link.
     * @param array $atts Shortcode attributes.
     *
     * @return string $tweet The encoded tweet text.
     *
     */
    protected function get_tweet( $atts ) {
        $max_tweet_length = 240;

        // Check for a custom tweet from the shortcode attributes. .
        $tweet = $atts['tweet'];

		if ( function_exists( 'mb_convert_encoding' ) ) :
	        $tweet = mb_convert_encoding( $tweet, 'UTF-8', get_bloginfo( "charset" ) );
		endif;

        $html_safe_tweet = htmlentities( $tweet, ENT_COMPAT, 'UTF-8' );
        $tweet = utf8_uri_encode( $tweet, $max_tweet_length );
		$tweet = urlencode( $tweet );

        return $tweet;
    }
}
