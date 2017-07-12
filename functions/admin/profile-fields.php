<?php

// Queue up our profile field functions
add_action( 'show_user_profile', 'swp_show_user_profile_fields' );
add_action( 'edit_user_profile', 'swp_show_user_profile_fields' );
add_action( 'personal_options_update', 'swp_save_user_profile_fields' );
add_action( 'edit_user_profile_update', 'swp_save_user_profile_fields' );

/**
 * swp_show_user_profile_fields( $user )   | Display the new options on the user profile edit page
 * @param  object $user The user object
 * @since  Unknown | Created | Unknown     | A function to output fields on each user's profile page
 * @since  2.2.4   | Updated | 07 MAR 2017 | Added translation gettext calls to each title and description
 * @access public
 * @return none
 */
function swp_show_user_profile_fields( $user ) {
	echo '<h3>Social Warfare Fields</h3>';
	echo '<table class="form-table">';
	echo '<tr>';
	echo '<th><label for="twitter">' . __( 'Twitter Username','social-warfare' ) . '</label></th>';
	echo '<td>';
	echo '<input type="text" name="swp_twitter" id="swp_twitter" value="' . esc_attr( get_the_author_meta( 'swp_twitter' , $user->ID ) ) . '" class="regular-text" />';
	echo '<br /><span class="description">' . __( 'Please enter your Twitter username.','social-warfare' ) . '</span>';
	echo '</td>';
	echo '</tr>';
	echo '<tr>';
	echo '<th><label for="facebook_author">' . __( 'Facebook Author URL','social-warfare' ) . '</label></th>';
	echo '<td>';
	echo '<input type="text" name="swp_fb_author" id="swp_fb_author" value="' . esc_attr( get_the_author_meta( 'swp_fb_author' , $user->ID ) ) . '" class="regular-text" />';
	echo '<br /><span class="description">' . __( 'Please enter the URL of your Facebok profile.','social-warfare' ) . '</span>';

	echo '</td>';
	echo '</tr>';

	echo '</table>';
}

// Save our fields when the page is udpated
/**
 * swp_save_user_profile_fields( $user_id )| Save our fields when the page is updated
 * @param  integer $user_id The user ID
 * @since  Unknown | Created | Unknown | A function to save the profile fields
 * @access public
 * @return none
 */
function swp_save_user_profile_fields( $user_id ) {

	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		return false;
	}

	/* Copy and paste this line for additional fields. Make sure to change 'twitter' to the field ID. */
	update_user_meta( $user_id, 'swp_twitter', $_POST['swp_twitter'] );
	update_user_meta( $user_id, 'swp_fb_author', $_POST['swp_fb_author'] );
}

function swp_get_author( $post_id = 0 ) {
	$post = get_post( $post_id );
	return $post->post_author;
}
