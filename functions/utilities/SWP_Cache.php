<?php

class SWP_Cache {
    /**
    * Array of the currently loaded SWP_Post_Cache objects, indexed by post_id.
    * These are meant to be accessed by the Buttons Panel, for example.
    *
    */
    public $post_caches = array();

    public function get_post_cache( $post_id ) {
        if ( array_key_exists( $post_id, $this->post_caches ) ) :
            return $this->post_caches[$post_id];
        endif;

        $post_cache = new SWP_Post_Cache( $post_id );
        $this->post_caches[$post_id] = $post_cache;

        return $post_cache;
    }
}
