jQuery( function( $ )
{
	'use strict';

	$( '.SW_META-oembed-wrapper .spinner' ).hide();

	$( 'body' ).on( 'click', '.SW_META-oembed-wrapper .show-embed', function() {
		var $this = $( this ),
			$spinner = $this.siblings( '.spinner' ),
			data = {
				action: 'SW_META_get_embed',
				url: $this.siblings( 'input' ).val()
			};

		$spinner.show();
		$.post( ajaxurl, data, function( r )
		{
			$spinner.hide();
			$this.siblings( '.embed-code' ).html( r.data );
		}, 'json' );

		return false;
	} );
} );
