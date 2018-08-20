<?php

/**
 * The Global SWP_Post_Caches Object
 *
 * This class allows for the creation of a global $SWP_Post_Caches object. This
 * will be called and instantiated from the main loader class. It will then be
 * made available to classes like the buttons_panel class which can then use it
 * to fetch share counts for specific posts via their post_cache objects.
 *
 * This class is essentially a loader class for the post_cache objects.
 *
 * @package   SocialWarfare\Functions\Utilities
 * @copyright Copyright (c) 2018, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     3.1.0 | 20 JUN 2018 | Created
 * @access    public
 *
 */
class SWP_Post_Cache_Loader {


    /**
    * Array of the currently loaded SWP_Post_Cache objects, indexed by post_id.
    * These are meant to be accessed by the Buttons Panel, for example.
    *
    * @var array
    *
    */
    public $post_caches = array();


	/**
	 * Load the class and queue up the admin hooks.
	 *
	 * @since  3.1.0 | 25 JUN 2018 | Created
	 * @param  void
	 * @return void
	 *
	 */
	public function __construct() {
        add_action( 'wp_ajax_swp_rebuild_cache', array( $this, 'rebuild_post_cache_data' ) );
        add_action( 'wp_ajax_nopriv_swp_rebuild_cache', array( $this, 'rebuild_post_cache_data' ) );

		/**
		 * Reset the cache timestamp when a post is updated. This will cause the
		 * cache to rebuild on the next page load.
		 *
		 */
		add_action( 'save_post', array( $this, 'update_post' ) );
		add_action( 'publish_post', array( $this, 'update_post' ) );
	}


	/**
	 * Gets the post_cache object for a specific post.
	 *
	 * Since all requests for post_cache objects should be called via this
	 * method, we shouldn't have to worry about a post_cache object being
	 * instantiated more than once for any given post. As such, we can use the
	 * instantiation of that object to call functions that we want to make sure
	 * only ever get run once, like updating the cached data.
	 *
	 * @since  3.1.0 | 20 JUNE 2018 | Created
	 * @param  integer $post_id The ID of the post being requested.
	 * @return object           The post_cache object for the post.
	 *
	 */
    public function get_post_cache( $post_id ) {

		if ( !array_key_exists( $post_id, $this->post_caches ) ) :
			$this->post_caches[$post_id] = new SWP_Post_Cache( $post_id );
		endif;

		return $this->post_caches[$post_id];
	}


	/**
	 * Rebuild the cached data for a post cache.
	 *
	 * Since this class is loaded glboally, it can be made available for use by
	 * admin ajax calls. This method will intercept/recieve the admin-ajax
	 * request, instantiate a post cache object, and then instruct that object
	 * to rebuild the post cache data.
	 *
	 * @todo   Add the wp-die() or whatever command is needed to close a wp-ajax
	 *         handler method.
	 * @since  3.1.0 | 25 JUN 2018 | Created
	 * @param  void
	 * @return void
	 *
	 */
	public function rebuild_post_cache_data() {
		echo 'test';
		if( isset( $_POST['post_id'] ) ):
			$Post_Cache = new SWP_Post_Cache( $_POST['post_id'] );
			$Post_Cache->rebuild_cached_data();
		endif;
		wp_die();
	}


	/**
	 * Resets the cache timestamp so that it will rebuild during the next page load.
	 *
	 * @since  3.1.0 | 26 JUN 2018 | Created the method.
	 * @param  void
	 * @return void
	 *
	 */
	public function update_post( $post_id ) {
		$Post_Cache = new SWP_Post_Cache( $post_id );
		$Post_Cache->delete_timestamp();
	}

}
