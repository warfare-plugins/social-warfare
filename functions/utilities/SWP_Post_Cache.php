<?php

class SWP_Post_Cache {
    /**
     * @var bool $fresh_cache
     *
     * True if the post has recently been updated, false otherwise.
     */
    public $fresh_cache = false;

    public function __construct( $post_id ) {
        global $post;

        if ( $post->ID != $post_id ) {
            $post = get_post( $post_id );
        }

        $this->post_id = $post_id;
        $this->post = $post;
        $this->fresh_cache = $this->has_fresh_cache();

        if ( !$this->fresh_cache ) {
            add_filter( 'swp_ajax_script', array( $this, 'print_ajax_script') );
        }
    }

    /**
     * Determines if the data has recently been updated.
     *
     * @access Protected Use the $fresh_cache property to determine cache status.
     * @return mixed boolean if the conditions are met, else function $this->calulcate_age.
     *
     */
    protected function has_fresh_cache() {
        global $swp_user_options;

    	// Bail early if it's a crawl bot. If so, ONLY SERVE CACHED RESULTS FOR MAXIMUM SPEED.
    	if ( isset( $_SERVER['HTTP_USER_AGENT'] ) && preg_match( '/bot|crawl|slurp|spider/i',  wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) ) :
        	return true;
        endif;

    	if ( !$output && isset( $swp_user_options['cache_method'] ) && 'advanced' === $swp_user_options['cache_method'] ) {
    		if ( empty( $_GET['swp_cache'] ) && empty( $_POST['swp_cache'] ) ) {
    			return true;
    		}

    		return false;
    	}

        //* TODO Describe why this condition would pass.
    	if( isset( $_POST['swp_cache'] ) && 'rebuild' === $_POST['swp_cache'] ) {
    		return false;
    	}

    	// Always be TRUE if we're not on a single.php otherwise we could end up
    	// Rebuilding multiple page caches which will cost a lot of time.
    	if ( ! is_singular() && !$ajax ) :
    		return true;
    	endif;

        return $this->calculate_age();
    }

    /**
     *  Determines if the page has recently been updated.
     * @since 3.0.10 | 19 JUN 2018 | Created the method.
     * @return bool $fresh_cache True if the post has recently updated, false otherwise.
     */
    protected function calculate_age() {
        $post_age = floor( date( 'U' ) - get_post_time( 'U' , false , $this->post_id ) );

    	if ( $post_age < ( 21 * 86400 ) ) {
            //* Three weeks
    		$hours = 1;
    	} elseif ( $post_age < ( 60 * 86400 ) ) {
            //* Two months
    		$hours = 4;
    	} else {
    		$hours = 12;
    	}

    	$time = floor( ( ( date( 'U' ) / 60 ) / 60 ) );
        //* $time is a number in hours. Example: 424814 == Number of hourse since Unix epoch.

    	$last_checked = get_post_meta( $post_id, 'swp_cache_timestamp', true );
        //* $last_checked is the number in hours (at time of storage) since the Unix epoch.

    	$fresh_cache =  $last_checked > ( $time - $hours ) && ( $last_checked > 390000 );

    	return $fresh_cache;
    }

    /**
     * Get either the cached or updated post meta.
     *
     * @return mixed $meta An array of post_meta, or void if not found.
     *
     */
    public function get_post_meta() {
        if ( $this->fresh_cache ) :
            return get_post_meta( $this->id );
        endif;

        //* Cache is not fresh. Do the requests.
        $data = do_requests();

        if ( !$data ) {
          //* Output the ajax JS.
          return;
        }

        $meta = cleanup_data($data);

        update_post_meta( $this->id, $meta );
        $this->post->post_meta = $meta;

        return $meta;
    }

    public function print_ajax_script() {
        global $Cache;

        if ( !$Cache->will_print_script ) {
            
        }
    }
}
