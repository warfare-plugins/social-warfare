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

    /**
     * Generate Share Link
     *
     * This function generates a share link for the Mastodon social network. It takes the post data as input,
     * including the post title, excerpt, permalink, and ID. It constructs the share link by appending the
     * text parameter with the encoded share text, which includes the post title, excerpt, permalink, and tags (if any).
     * 
     * @since                   4.2.0 | 30 JUN 2023 | CREATED
     * @param  array $post_data The post data containing the post title, excerpt, permalink, and ID.
     * @return string           The generated share link for Mastodon.
     * @access public
     */

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

    /**
     * Get Excerpt
     *
     * This function retrieves the excerpt for a post. If the post does not have an excerpt,
     * it retrieves the first few words from the post content and returns them as the excerpt.
     *
     * @since                   4.2.0 | 30 JUN 2023 | CREATED
     * @param  array $post_data The post data containing the post excerpt and content.
     * @return string           The retrieved excerpt.
     * @access protected
     */

    protected function get_excerpt( $post_data ) {
        $excerpt = $post_data['post_excerpt'];

        if ( empty( $excerpt ) ) {
            $content = $post_data['post_content'];
            $excerpt = $this->get_first_words( $content, 25 );
        }

        return $excerpt;
    }

    /**
     * Get First Words
     *
     * This function retrieves the first few words from a given content. It strips the HTML tags,
     * splits the content into an array of words, selects the specified number of words, and
     * then joins them back into a string to form the excerpt.
     * 
     * @since                      4.2.0 | 30 JUN 2023 | CREATED
     * @param  string $content     The content from which to extract the first words.
     * @param  int    $word_count  The number of words to retrieve.
     * @return string              The extracted first words as the excerpt.
     * @access protected
     */

    protected function get_first_words( $content, $word_count ) {
        $words = explode( ' ', strip_tags( $content ) );
        $words = array_slice( $words, 0, $word_count );
        $excerpt = implode( ' ', $words );

        return $excerpt;
    }

    /**
     * Get Tags
     *
     * This function retrieves the tags associated with a post using the WordPress function wp_get_post_terms.
     * It fetches the post tags and constructs a string of hashtags by appending each tag preceded by '#'.
     * The resulting tag list is returned.
     *
     * @since                  4.2.0 | 30 JUN 2023 | CREATED
     * @param  int    $post_id The ID of the post for which to retrieve the tags.
     * @return string          The constructed tag list as a string.
     * @access protected
     */

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