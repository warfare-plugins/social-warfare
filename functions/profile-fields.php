<?php 

	// Queue up our profile field functions
	add_action( 'show_user_profile', 'sw_show_user_profile_fields' );
	add_action( 'edit_user_profile', 'sw_show_user_profile_fields' );
	add_action( 'personal_options_update', 'sw_save_user_profile_fields' );
	add_action( 'edit_user_profile_update', 'sw_save_user_profile_fields' );

	// Display the new option on the user profile edit page
	function sw_show_user_profile_fields( $user ) {
        echo '<h3>Social Warfare Fields</h3>';
    	echo '<table class="form-table">';
		echo '<tr>';
		echo '<th><label for="twitter">Twitter Username</label></th>';
		echo '<td>';
		echo '<input type="text" name="sw_twitter" id="sw_twitter" value="'.esc_attr( get_the_author_meta( 'sw_twitter' , $user->ID ) ).'" class="regular-text" />';
		echo '<br /><span class="description">Please enter your Twitter username (without the @ symbol).</span>';
        echo '</td>';
        echo '</tr>';		
		echo '<tr>';
		echo '<th><label for="facebook_author">Facebook Author URL</label></th>';
		echo '<td>';
		echo '<input type="text" name="sw_fb_author" id="sw_fb_author" value="'.esc_attr( get_the_author_meta( 'sw_fb_author' , $user->ID ) ).'" class="regular-text" />';
		echo '<br /><span class="description">Please enter the URL of your Facebok profile.</span>';
        echo '</td>';
        echo '</tr>';

    	echo '</table>';
    }
	
	// Save our fields when the page is udpated
	function sw_save_user_profile_fields( $user_id ) {
	
		if ( !current_user_can( 'edit_user', $user_id ) )
			return false;
	
		/* Copy and paste this line for additional fields. Make sure to change 'twitter' to the field ID. */
		update_usermeta( $user_id, 'sw_twitter', $_POST['sw_twitter'] );
		update_usermeta( $user_id, 'sw_fb_author', $_POST['sw_fb_author'] );
	}
	
	function sw_get_author( $post_id = 0 ){
		$post = get_post( $post_id );
		return $post->post_author;
	}