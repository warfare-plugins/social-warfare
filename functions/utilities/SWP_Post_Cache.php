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
    }

    /**
     * Determines if the data has recently been updated.
     *
     * @access Protected Use the $fresh_cache property to determine cache status.
     * @return bool true if the cache has recently been updated, else false.
     *
     */
    protected function has_fresh_cache() {
        //* Do checks based on publish date.
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
}
