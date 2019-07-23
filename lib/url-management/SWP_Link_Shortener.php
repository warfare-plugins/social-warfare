<?php

/**
 * SWP_Link_Shortener
 *
 * This class will serve as the parent class for link shortening integrations.
 * These integrations can extend this class to get easy access to it's methods
 * and properties.
 *
 * @since 4.0.0 | 19 JUL 2019 | Created
 *
 */
class SWP_Link_Shortener {


	/**
	 * This trait gives us access to the following debugging methods:
	 *
	 * debug()  Outputs all class properties to the screen.
	 * record_exit_status()  Stores bail conditions a dumpable array.
	 *
	 */
	use SWP_Debug_Trait;


	/**
	 * The unique key for each child class that extends this link shortening
	 * class. Keys should be snake_cased.
	 *
	 * @var string
	 *
	 */
	public $key;


	/**
	 * The pretty name of the link shortener that will be used when printing the
	 * name to the screen for the users to see.
	 *
	 * @var string
	 *
	 */
	public $name;


	/**
	 * A local property to let us know if this link shortening API has been
	 * activated. For bitly, for example this will reflect whether or not we
	 * have an Access Token.
	 *
	 * @var boolean
	 *
	 */
	public $active = false;


	/**
	 * The properties that will be used to generate the authentication button
	 * on the options page. This should contain, at a minimum, the following
	 * indices in the array.
	 *
	 * text: The text that will appear on the button.
	 * classes: The css classes that will be added to the button.
	 * target: An empty string or _blank for the anchor target.
	 * link: The URL to which the button should link.
	 *
	 * @var array
	 *
	 */
	public $button_properties = array();


	/**
	 * A string representing the name of the hook and the name of the
	 * corresponding function that will handle removing the token, login or
	 * whatever else information may be necessary to remove the apps authorization
	 * credentials from the plugin.
	 *
	 * @var string
	 *
	 */
	public $deactivation_hook = '';


	/**
	 * The Magic Constructor
	 *
	 * This will queue up the register_self() method which will, in turn, add
	 * this link shortener to the available drop-down list.
	 *
	 * @since  4.0.0 | 19 JUL 2019 | Created
	 * @param  void
	 * @return void
	 *
	 */
	public function __construct() {
		$this->establish_button_properties();
		add_filter( 'swp_available_link_shorteners', array( $this, 'register_self' ) );
		add_action( 'wp_ajax_swp_' .$this->deactivation_hook, array( $this, $this->deactivation_hook ) );
		add_action( 'wp_footer', array( $this, 'debug' ) );
		add_filter( 'swp_link_shortening', array( $this, 'provide_shortlink' ) );
		add_action( 'wp_ajax_nopriv_swp_' . $this->activation_hook, array( $this , $this->activation_hook ) );
	}


	/**
	 * A function to register this link shortening integration with the
	 * 'swp_register_link_shortners' filter so that it will show up and become
	 * an option on the options page.
	 *
	 * @since  4.0.0 | 18 JUL 2019 | Created
	 * @param  array $array An array of link shortening integrations.
	 * @return array        The modified array with our integration added.
	 *
	 */
	public function register_self( $array ) {
		$array[$this->key] = $this;
		return $array;
	}


	/**
	 * generate_authentication_button_data()
	 *
	 * A method to generate an array of information that can be used to generate
	 * the authentication button for this network on the options page.
	 *
	 * @since  4.0.0 | 18 JUL 2019 | Created
	 * @param  void
	 * @return array The array of button data including the text, color_css,
	 *               target, and link.
	 *
	 */
	public function establish_button_properties() {

		if ( true == $this->active ) {
			$this->button_properties['text']              = __( 'Connected', 'social-warfare' );
			$this->button_properties['classes']           = 'button sw-green-button';
			$this->button_properties['new_tab']           = true;
			$this->button_properties['link']              = '#';
			$this->button_properties['deactivation_hook'] = $this->deactivation_hook;
		} else {
			$this->button_properties['text']              = __( 'Authenticate', 'social-warfare' );
			$this->button_properties['classes']           = 'button sw-navy-button';
			$this->button_properties['new_tab']           = false;
			$this->button_properties['link']              = $this->authorization_link;
			$this->button_properties['deactivation_hook'] = '';
		}
	}


	/**
	 * The Bitly Link Shortener Method
	 *
	 * This is the function used to manage shortened links via the Bitly link
	 * shortening service.
	 *
	 * @since  3.0.0 | 04 APR 2018 | Created
	 * @since  3.4.0 | 16 OCT 2018 | Modified order of conditionals, docblocked.
	 * @since  4.0.0 | 17 JUL 2019 | Migrated into this standalone Bitly class.
	 * @since  4.0.0 | 23 JUL 2019 | Migrated into the parent Link_Shortener class.
	 * @param  array $array An array of arguments and information.
	 * @return array $array The modified array.
	 *
	 */
	public function provide_shortlink( $array ) {


		/**
		 * Pull together the information that we'll need to generate bitly links.
		 *
		 */
		global $post;
		$network           = $array['network'];
		$post_id           = $array['post_id'];
		$fresh_cache       = $array['fresh_cache'];
		$google_analytics  = SWP_Utility::get_option('google_analytics');


		/**
		 * Check if any of the bail conditions are met, in which case we'll exit
		 * the function without returning any kind of shortlinks.
		 *
		 */
		if( false === $this->should_link_be_shortened( $network ) ) {
			return $array;
		}


		/**
		 * Bail if we don't have a valid Bitly token.
		 *
		 */
		if ( false == $this->active ) {
			$this->record_exit_status( 'Is this link shortener authenticated?' );
			return $array;
		}


		/**
		 * If the chache is fresh and we have a valid bitly link stored in the
		 * database, then let's use our cached link.
		 *
		 * If the cache is fresh and we don't have a valid bitly link, we just
		 * return the unmodified array. This will prevent it from running non-stop
		 * API requests if one failed.
		 *
		 */
		if ( true == $fresh_cache ) {
			$this->record_exit_status( 'fresh_cache' );
			if( $this->fetch_cached_shortlink( $post_id, $network ) ) {
				$array['url'] = $this->fetch_cached_shortlink( $post_id, $network );
			}
			return $array;
		}


		/**
		 * If all checks have passed, let's generate a new bitly URL. If an
		 * existing link exists for the link passed to the API, it won't generate
		 * a new one, but will instead return the existing one.
		 *
		 */
		$url           = urldecode( $array['url'] );
		$new_bitly_url = $this->generate_new_shortlink( $url );


		/**
		 * If a link was successfully created, let's store it in the database,
		 * let's store it in the url indice of the array, and then let's wrap up.
		 *
		 */
		if ( $new_shortlink ) {
			$meta_key = $this->key . '_link';

			if ( $google_analytics ) {
				$meta_key .= "_$network";
			}

			delete_post_meta( $post_id, $meta_key );
			update_post_meta( $post_id, $meta_key, $new_shortlink );
			$array['url'] = $new_shorlink;
		}

		return $array;
	}


	/**
	 * A method to check the bail conditons before fetching, generating or
	 * returning a shortlink.
	 *
	 * @since  4.0.0 | 23 JUL 2019 | Created
	 * @param  string $network The key corresponding to a social network.
	 * @return bool   True if cleared to continue; false if we should bail.
	 *
	 */
	public function should_link_be_shortened( $network ) {
		global $post;


		/**
		 * We don't want bitly links generated for the total shares buttons
		 * (since they don't have any links at all), and Pinterest doesn't allow
		 * shortlinks on their network.
		 *
		 */
		if ( 'total_shares' == $network || 'pinterest' == $network ) {
			return false;
		}


		/**
		 * Bail if link shortening is turned off.
		 *
		 */
		if( false == SWP_Utility::get_option( 'link_shortening_toggle' ) ) {
			$this->record_exit_status( 'link_shortening_toggle' );
			return false;
		}


		/**
		 * Bail if the current shortener is not the selected Link shortener.
		 *
		 */
		if( $this->key !== SWP_Utility::get_option( 'link_shortening_service' ) ) {
			$this->record_exit_status( 'link_shortening_service' );
			return false;
		}


		/**
		 * Bail out if the post is older than the specified minimum publication
		 * date for posts and pages.
		 *
		 */
		if ( false == $this->check_publication_date() ) {
			$this->record_exit_status( 'publication_date' );
			return false;
		}


		/**
		 * Shortlinks can now be turned on or off at the post_type level on the
		 * options page. So if the shortlinks are turned off for our current
		 * post type, let's bail
		 *
		 */
		$post_type_toggle = SWP_Utility::get_option( 'short_link_toggle_' . $post->post_type );
		if ( false === $post_type_toggle ) {
			$this->record_exit_status( 'short_link_toggle_' . $post->post_type );
			return false;
		}


		// If all checks pass, return true. We should shortend the link.
		return true;
	}


	/**
	 * Fetch the bitly link that is cached in the local database.
	 *
	 * When the cache is fresh, we just pull the existing bitly link from the
	 * database rather than making an API call on every single page load.
	 *
	 * @since  3.3.2 | 12 SEP 2018 | Created
	 * @since  3.4.0 | 16 OCT 2018 | Refactored, Simplified, Docblocked.
	 * @since  4.0.0 | 23 JUL 2019 | Moved into this parent class.
	 * @param  int $post_id The post ID
	 * @param  string $network The key for the current social network
	 * @return mixed           string: The short url; false on failure.
	 *
	 */
	public function fetch_cached_shortlink( $post_id, $network ) {


		/**
		 * Fetch the local bitly link. We'll use this one if Google Analytics is
		 * not enabled. Otherwise we'll switch it out below.
		 *
		 */
		$short_url = get_post_meta( $post_id, $this->key . '_link', true );


		/**
		 * If Google analytics are enabled, we'll need to fetch a different
		 * shortlink for each social network. If they are disabled, we just use
		 * the same shortlink for all of them.
		 *
		 */
		if ( true == SWP_Utility::get_option('google_analytics') ) {
			$short_url = get_post_meta( $post_id, $this->key . '_link_' . $network, true);
		}


		/**
		 * We need to make sure that the $short_url returned from get_post_meta()
		 * is not false or an empty string. If so, we'll return false.
		 *
		 */
		if ( !empty( $short_url ) ) {
			return $short_url;
		}

		return false;
	}

	/**
	 * Users can select a date prior to which articles will not get short
	 * links. This is to prevent the case where some users get their quotas
	 * filled up as soon as the option is turned on because it is generating
	 * links for older articles. So this conditional checks the publish date
	 * of an article and ensures that the article is eligible for links.
	 *
	 * @since  4.0.0 | 23 JUL 2019 | Created
	 * @param  void
	 * @return bool True if publication date is valid; false if not.
	 *
	 */
	public function check_publication_date() {
		global $post;

		// Fetch the user-set start date from the options page.
		$start_date = SWP_Utility::get_option( 'link_shortening_start_date' );

		// If the start date is actually set...
		if ( $start_date ) {

			// Bail if we don't have a valid post object or post_date.
			if ( !is_object( $post ) || empty( $post->post_date ) ) {
				return $array;
			}

			// Format the start dates into something we can use.
			$start_date = DateTime::createFromFormat( 'Y-m-d', $start_date );
			$post_date  = new DateTime( $post->post_date );

			// The post is older than the minimum publication date, return false.
			if ( $start_date > $post_date ) {
				return false;
			}
		}
		return true;
	}
}
