jQuery( function ( $ )
{
	'use strict';

	$( 'body' ).on( 'change', '.swpmb-image-select input', function ()
	{
		var $this = $( this ),
			type = $this.attr( 'type' ),
			selected = $this.is( ':checked' ),
			$parent = $this.parent(),
			$others = $parent.siblings();
		if ( selected )
		{
			$parent.addClass( 'swpmb-active' );
			if ( type === 'radio' )
			{
				$others.removeClass( 'swpmb-active' );
			}
		}
		else
		{
			$parent.removeClass( 'swpmb-active' );
		}
	} );
	$( '.swpmb-image-select input' ).trigger( 'change' );
} );
