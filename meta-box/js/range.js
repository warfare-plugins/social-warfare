jQuery( function ( $ )
{
	'use strict';

	/**
	 * Update color picker element
	 * Used for static & dynamic added elements (when clone)
	 */
	function update()
	{
		var $this = $( this ),
			$output = $this.siblings( '.swpmb-output' );

    $this.on( 'input propertychange change', function( e )
    {
      $output.html( $this.val() );
    } );

	}

	$( ':input.swpmb-range' ).each( update );
	$( '.swpmb-input' ).on( 'clone', 'input.swpmb-range', update );
} );
