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

		swpmbSelect.bindEvents( $this );
	}

	$( ':input.swpmb-select_advanced' ).each( update );
	$( '.swpmb-input' ).on( 'clone', ':input.swpmb-select_advanced', update );
} );
