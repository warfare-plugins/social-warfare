jQuery( function ( $ )
{
	'use strict';

	/**
	 * Turn select field into beautiful dropdown with select2 library
	 * This function is called when document ready and when clone button is clicked (to update the new cloned field)
	 *
	 * @return void
	 */
	function update()
	{
		var $this = $( this ),
			options = $this.data( 'options' );
		$this.siblings( '.select2-container' ).remove();
		$this.show().select2( options );
	}

	$( ':input.SW_META-select-advanced' ).each( update );
	$( '.SW_META-input' ).on( 'clone', ':input.SW_META-select-advanced', update );
} );
