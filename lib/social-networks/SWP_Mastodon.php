<?php

class SWP_Mastodon extends SWP_Social_Network {

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