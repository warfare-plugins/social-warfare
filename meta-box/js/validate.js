jQuery( function ( $ )
{
	'use strict';

	var $form = $( '#post' );

	// Required field styling
	$.each( SW_META.validationOptions.rules, function ( k, v )
	{
		if ( v['required'] )
		{
			$( '#' + k ).parent().siblings( '.SW_META-label' ).addClass( 'required' ).append( '<span>*</span>' );
		}
	} );

	SW_META.validationOptions.invalidHandler = function ()
	{
		// Re-enable the submit ( publish/update ) button and hide the ajax indicator
		$( '#publish' ).removeClass( 'button-primary-disabled' );
		$( '#ajax-loading' ).attr( 'style', '' );
		$form.siblings( '#message' ).remove();
		$form.before( '<div id="message" class="error"><p>' + SW_META.summaryMessage + '</p></div>' );
	};

	$form.validate( SW_META.validationOptions );
} );
