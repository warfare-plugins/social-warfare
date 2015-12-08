jQuery( function ( $ )
{
	'use strict';

	var frame;

	$( 'body' ).on( 'click', '.SW_META-file-input-select', function ( e )
	{
		e.preventDefault();
		var $el = $( this );

		// Create a frame only if needed
		if ( !frame )
		{
			frame = wp.media( {
				className: 'media-frame SW_META-file-frame',
				multiple : false,
				title    : SW_METAFileInput.frameTitle
			} );
		}

		// Open media uploader
		frame.open();

		// Remove all attached 'select' event
		frame.off( 'select' );

		// Handle selection
		frame.on( 'select', function ()
		{
			var url = frame.state().get( 'selection' ).first().toJSON().url;
			$el.siblings( 'input' ).val( url ).siblings( 'a' ).removeClass( 'hidden' );
		} );
	} );

	// Clear selected images
	$( 'body' ).on( 'click', '.SW_META-file-input-remove', function ( e )
	{
		e.preventDefault();
		$( this ).addClass( 'hidden' ).siblings( 'input' ).val( '' );
	} );
} );
