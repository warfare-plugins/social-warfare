jQuery( function ( $ )
{
	'use strict';

	$( 'body' ).on( 'change', '.SW_META-image-select input', function ()
	{
		var $this = $( this ),
			type = $this.attr( 'type' ),
			selected = $this.is( ':checked' ),
			$parent = $this.parent(),
			$others = $parent.siblings();
		if ( selected )
		{
			$parent.addClass( 'SW_META-active' );
			if ( type === 'radio' )
			{
				$others.removeClass( 'SW_META-active' );
			}
		}
		else
		{
			$parent.removeClass( 'SW_META-active' );
		}
	} );
	$( '.SW_META-image-select input' ).trigger( 'change' );
} );
