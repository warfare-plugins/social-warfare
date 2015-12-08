jQuery( function ( $ )
{
	'use strict';

	/**
	 * Update date picker element
	 * Used for static & dynamic added elements (when clone)
	 */
	function SW_META_update_date_picker()
	{
		var $this = $( this ),
			options = $this.data( 'options' );

		$this.siblings( '.ui-datepicker-append' ).remove();         // Remove appended text
		$this.removeClass( 'hasDatepicker' ).attr( 'id', '' ).datepicker( options );
	}

	$( ':input.SW_META-date' ).each( SW_META_update_date_picker );
	$( '.SW_META-input' ).on( 'clone', ':input.SW_META-date', SW_META_update_date_picker );
} );
