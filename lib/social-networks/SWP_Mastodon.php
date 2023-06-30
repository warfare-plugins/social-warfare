<?php

/**
 * Mastodon
 *
 * Class to add a Mastodon share button to the available buttons
 *
 * @package   SocialWarfare\Functions\Social-Networks
 * @copyright Copyright (c) 2023, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     4.2.0 | 30 JUN 2023 | CREATED
 *
 */
 
class SWP_Mastodon extends SWP_Social_Network {
    /**
	 * The Magic __construct Method
	 *
	 * This method is used to instantiate the social network object. It does three things.
	 * First it sets the object properties for each network. Then it adds this object to
	 * the globally accessible swp_social_networks array. Finally, it fetches the active
	 * state (does the user have this button turned on?) so that it can be accessed directly
	 * within the object.
	 *
	 * @since  4.2.0 | 30 JUN 2023 | CREATED
	 * @param  none
	 * @return none
	 * @access public
	 *
	 */

    public function __construct() {
        $this->name    = __( 'Mastodon', 'social-warfare' );
        $this->cta     = __( 'Toot', 'social-warfare' );
        $this->key     = 'mastodon';
        $this->default = 'true';

        $this->init_social_network();
    }

    public function generate_share_link( $post_data ) {
		$title = $post_data['post_title'];
		$excerpt = $this->get_excerpt( $post_data );
		$permalink = urldecode( $this->get_shareable_permalink( $post_data ) );
		$tags = $this->get_tags( $post_data['ID'] );
	
		$share_link = 'https://mastodon.social/share?';
	
		$share_text = $title . PHP_EOL . $excerpt . PHP_EOL . $permalink;
		if ( ! empty( $tags ) ) {
			$share_text .= PHP_EOL . PHP_EOL . $tags;
		}
	
		$share_link .= 'text=' . urlencode( $share_text );
	
		return $share_link;
	}	

    protected function get_excerpt( $post_data ) {
        $excerpt = $post_data['post_excerpt'];

        if ( empty( $excerpt ) ) {
            $content = $post_data['post_content'];
            $excerpt = $this->get_first_words( $content, 25 );
        }

        return $excerpt;
    }

    protected function get_first_words( $content, $word_count ) {
        $words = explode( ' ', strip_tags( $content ) );
        $words = array_slice( $words, 0, $word_count );
        $excerpt = implode( ' ', $words );

        return $excerpt;
    }

    protected function get_tags( $post_id ) {
		$tags = wp_get_post_terms( $post_id, 'post_tag', array( 'fields' => 'names' ) );
	
		if ( is_array( $tags ) && ! empty( $tags ) ) {
			$tag_list = "\n\n";
			foreach ( $tags as $tag ) {
				$tag_list .= '#' . $tag . ' ';
			}
			return trim( $tag_list );
		}
	
		return '';
	}
}