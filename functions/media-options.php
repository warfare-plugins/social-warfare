<?php

/*****************************************************************

	Mechanism for Opting an Image Out of Having a Pin Button

******************************************************************/
add_filter( 'attachment_fields_to_edit', 'swp_add_media_options' , null, 2);

function swp_add_media_options($form_fields, $post){
	$checked = get_post_meta( $post->ID, 'swp_pin_button_opt_out', false ) ? 'checked="checked"' : '';
	$form_fields['swp_pin_button_opt_out'] = array(
		'label' => 'Pin Opt Out',
		'input' => 'html',
		'html'  => "<input type=\"checkbox\"
			name=\"attachments[{$post->ID}][swp_pin_button_opt_out]\"
			id=\"attachments[{$post->ID}][swp_pin_button_opt_out]\"
			value=\"1\" {$checked}/><br />");
	return $form_fields;
}

add_filter("attachment_fields_to_save", function($post, $attachment){
	if(isset($attachment['swp_pin_button_opt_out']))
	{
		update_post_meta($post['ID'], 'swp_pin_button_opt_out', 1);
	} else {
		update_post_meta($post['ID'], 'swp_pin_button_opt_out', 0);
	}
}, null , 2);
