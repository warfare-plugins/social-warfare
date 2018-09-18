<?php

/**
* SWP_URL_Management
*
* A class engineered to manage the links that are shared out to the various social
* networks. This class will shorten them via Bitly or add Google Analytics tracking
* parameters if the user has either of these options enabled and configured.
*
* @since 3.0.0 | 14 FEB 2018 | Added check for is_attachment() to swp_google_analytics
* @since 3.0.0 | 04 APR 2018 | Converted to class-based, object-oriented system.
*
*/
class SWP_URL_Management {


	/**
	 * The magic __construct method.
	 *
	 * This method instantiates the SWP_URL_Management object. It's primary function
	 * is to add the various methods to their approprate hooks for use later on in
	 * modifying the links.
	 *
	 * @since 3.0.0 | 04 APR 2018 | Created
	 * @param none
	 * @return none
	 * @access public
	 *
	 */
	public function __construct() {

		add_filter( 'swp_link_shortening'  	, array( __CLASS__ , 'link_shortener' ) );
		add_filter( 'swp_analytics' 		, array( $this , 'google_analytics' ) );
		add_action( 'wp_ajax_nopriv_swp_bitly_oauth', array( $this , 'bitly_oauth_callback' ) );

	}


	/**
	 * Google Analytics UTM Tracking Parameters
	 *
	 * This is the method used to add Google analytics UTM parameters to the links
	 * that are being shared on social media.
	 *
	 * @since  3.0.0 | 04 APR 2018 | Created
	 * @param  array $array An array of arguments and data used in processing the URL.
	 * @return array $array The modified array.
	 * @access public
	 *
	 */
	public function google_analytics( $array ) {
		global $swp_user_options;

	    // Fetch the user options
	    $options = $swp_user_options;
	    $url = $array['url'];
	    $network = $array['network'];

	    if( ( 'pinterest' === $network && isset( $swp_user_options['utm_on_pins']) && true === $swp_user_options['utm_on_pins']) || $network !== 'pinterest' ) :

	    	if ( true === is_attachment() ) :
	    		return $array;
	    	endif;

	    	// Check if Analytics have been enabled or not
	    	if ( true == SWP_Utility::get_option('google_analytics') ) :
	            $url_string = 'utm_source=' . $network . '&utm_medium=' . $options['analytics_medium'] . '&utm_campaign=' . $options['analytics_campaign'] . '';

	    		if ( strpos( $url,'?' ) !== false ) :
	    			$array['url'] = $url . urlencode( '&' . $url_string );
	    		else :
	    			$array['url'] = $url . urlencode( '?' . $url_string );
	    		endif;
	    	endif;

	    	return $array;
	    endif;

	    return $array;
	}


	/**
	 * Fetch the bitly link that is cached in the local database.
	 *
	 * When the cache is fresh, we just pull the existing bitly link from the
	 * database rather than making an API call on every single page load.
	 *
	 * @since  3.3.2 | 12 SEP 2018 | Created
	 * @param  int $post_id The post ID
	 * @param  string $network The key for the current social network
	 * @return mixed           string: The short url; false on failure.
	 *
	 */
    public static function fetch_local_bitly_link( $post_id, $network ) {

		// If analytics are on, get a different link for each network.
		if ( true == SWP_Utility::get_option('google_analytics') ) {
        	$short_url = get_post_meta( $post_id, 'bitly_link_' . $network, true);

	        if ( is_string( $short_url ) && strlen( $short_url ) ) {
	            return $short_url;
			}
		}

		// If analytics are off, just pull the general short link.
        $short_url = get_post_meta( $post_id, 'bitly_link', true );

        if ( is_string( $short_url ) && strlen( $short_url ) ) {
            return $short_url;
        }

        return false;
    }


	/**
	 * The Bitly Link Shortener Method
	 *
	 * This is the function used to manage shortened links via the Bitly link
	 * shortening service.
	 *
	 * @since  3.0.0 | 04 APR 2018 | Created
	 * @param  array $array An array of arguments and information.
	 * @return array $array The modified array.
	 * @access public
	 *
	 */
	public static function link_shortener( $array ) {
        global $post;

        $post_id = $array['postID'];
        $google_analytics = SWP_Utility::get_option('google_analytics');
        $access_token = SWP_Utility::get_option( 'bitly_access_token' );
        $cached_bitly_link = SWP_URL_Management::fetch_local_bitly_link( $post_id, $array['network'] );
		$start_date = SWP_Utility::get_option( 'bitly_start_date' );



        // Recently done.
        if ( true == $array['fresh_cache'] ) {
			if( false !== $cached_bitly_link ) {
				$array['url'] = $cached_bitly_link;
			}
            return $array;
        }

        // We need this information to make a bitly request.
        if ( false == $access_token || true !== SWP_Utility::get_option( 'bitly_authentication' ) ) {
			return $array;
        }

        // These can not have bitly urls created.
        if ( $array['network'] == 'total_shares' || $array['network'] == 'pinterest' ) {
            return $array;
        }

        $links_enabled = SWP_Utility::get_option( "bitly_links_{$post->post_type}" );

        //* They have disabled bitly links on this post type.
        if ( false == $links_enabled || 'off' == $links_enabled ) :
            return $array;
        endif;

        //* They have decided to only allow posts after a certain date.
        if ( $start_date ) {
            if ( !is_object( $post ) || empty( $post->post_date ) ) :
                return $array;
            endif;

            $start_date = DateTime::createFromFormat( 'Y-m-d', $start_date );
            $post_date = new DateTime( $post->post_date );

            //* The post is too new for $start_date.
            if ( $start_date > $post_date ) :
                return $array;
            endif;
        }

        $network = $array['network'];
        $url = urldecode( $array['url'] );
        $new_bitly_url = SWP_URL_Management::make_bitly_url( $url, $access_token );

        if ( $new_bitly_url ) {
			$meta_key = 'bitly_link';

			if ( $google_analytics ) {
				$meta_key .= "_$network";
			}

            delete_post_meta( $post_id, $meta_key );
            update_post_meta( $post_id, $meta_key, $new_bitly_url );
            $array['url'] = $new_bitly_url;
        }

        // // Delete the meta fields and then update to keep the database clean and up to date.
        // if ( false == $google_analytics ) {
        //     delete_post_meta( $post_id, 'bitly_link_' . $network );
        // }

	    return $array;
	}


	/**
	 * Create a new Bitly short URL
	 *
	 * This is the method used to interface with the Bitly API with regard to creating
	 * new shortened URL's via their service.
	 *
	 * @since  3.0.0 | 04 APR 2018 | Created
	 * @param  string $url          The URL to be shortened
	 * @param  string $network      The social network on which this URL is being shared.
	 * @param  string $access_token The user's Bitly access token.
	 * @return string               The shortened URL.
	 * @access public
	 *
	 */
	public static function make_bitly_url( $url, $access_token ) {
		// Create a link to reach the Bitly API
		$api_request_url = 'https://api-ssl.bitly.com/v3/shorten';
		$api_request_url .= "?access_token=$access_token";
		$api_request_url .= "&longUrl=" . urlencode( $url );
		$api_request_url .= "&format=json";

		// echo __METHOD__, "<pre>", var_dump($api_request_url), die;

		// Fetch a response from the Bitly Shortening API
		$response = SWP_CURL::file_get_contents_curl( $api_request_url );

		// Parse the JSON formated response into an array
		$result = json_decode( $response , true );

		// If the shortening succeeded....
		if ( isset( $result['data']['url'] ) ) {
			// Store the short URL to return to the plugin
			return $result['data']['url'];
		}

		// If the shortening failed....
		return false;
	}


	/**
	 * The function that processes a URL
	 *
	 * This method is used throughout the plugin to fetch URL's that have been processed
	 * with the link shorteners and UTM parameters. It processes an array of information
	 * through the swp_analytics filter and the swp_link_shortening filter.
	 *
	 * @since  3.0.0 | 04 APR 2018 | Created
	 * @param  string $url     The URL to be modified.
	 * @param  string $network The network on which the URL is being shared.
	 * @param  int    $postID  The post ID.
	 * @return string          The modified URL.
	 * @access public static
	 */
	public static function process_url( $url, $network, $postID, $is_cache_fresh = true ) {
		// Fetch the parameters into an array for use by the filters
		$array['url'] = $url;
		$array['network'] = $network;
		$array['postID'] = $postID;
        $array['fresh_cache'] = $is_cache_fresh;

		if( !is_attachment() ):

			// Run the anaylitcs hook filters

			$array = apply_filters( 'swp_analytics' , $array );

			// Run the link shortening hook filters, but not on Pinterest
			// $array = apply_filters( 'swp_link_shortening' , $array );
			$array = SWP_URL_Management::link_shortener($array);
			// echo "<pre>Array after link_shortener(): <br>", var_dump($array), "</pre>";
		endif;

		return $array['url'];
	}


	/**
	 * The Bitly OAuth Callback Function
	 *
	 * When authenticating Bitly to the plugin, Bitly uses a back-and-forth handshake
	 * system. This function will intercept the ping from Bitly's server, process the
	 * information and provide a response to Bitly.
	 *
	 * @since  3.0.0 | 04 APR 2018 | Created
	 * @param  none
	 * @return none A response is echoed to the screen for Bitly to read.
	 * @access public
	 *
	 */
	public function bitly_oauth_callback() {
		$access_token = isset( $_GET['access_token'] ) ? $_GET['access_token'] : '';
		$login = isset( $_GET['login'] ) ? $_GET['login'] : '';

		SWP_Utility::update_option( 'bitly_access_token', $access_token );
		SWP_Utility::update_option( 'bitly_access_login', $login);

		echo admin_url( 'admin.php?page=social-warfare' );
	}
}
