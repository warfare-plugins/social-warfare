jQuery( function ( $ )
{
	'use strict';

	/**
	 * Update datetime picker element
	 * Used for static & dynamic added elements (when clone)
	 */
	function SW_META_update_datetime_picker()
	{
		var $this = $( this ),
			options = $this.data( 'options' );

		$this.siblings( '.ui-datepicker-append' ).remove();         // Remove appended text
		$this.removeClass( 'hasDatepicker' ).attr( 'id', '' ).datetimepicker( options );

	}

	$( ':input.SW_META-datetime' ).each( SW_META_update_datetime_picker );
	$( '.SW_META-input' ).on( 'clone', ':input.SW_META-datetime', SW_META_update_datetime_picker );
} );
