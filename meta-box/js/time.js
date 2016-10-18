jQuery( function ( $ )
{
	'use strict';

	/**
	 * Update datetime picker element
	 * Used for static & dynamic added elements (when clone)
	 */
	function update()
	{
		var $this = $( this ),
			options = $this.data( 'options' ),
			$inline = $this.siblings( '.swpmb-datetime-inline' ),
			current = $this.val();

		$this.siblings( '.ui-datepicker-append' ).remove();  // Remove appended text

		if( $inline.length )
		{
			options.altField = '#' + $this.attr( 'id' );
			$inline
				.removeClass( 'hasDatepicker' )
				.empty()
				.prop( 'id', '' )
				.timepicker( options )
				.timepicker( "setTime", current );
		}
		else
		{
			$this.removeClass( 'hasDatepicker' ).timepicker( options );
		}
	}

	// Set language if available
	if ( $.timepicker.regional.hasOwnProperty( SWPMB_Timepicker.locale ) )
	{
		$.timepicker.setDefaults( $.timepicker.regional[SWPMB_Timepicker.locale] );
	}
	else if ( $.timepicker.regional.hasOwnProperty( SWPMB_Timepicker.localeShort ) )
	{
		$.timepicker.setDefaults( $.timepicker.regional[SWPMB_Timepicker.localeShort] );
	}

	$( '.swpmb-time' ).each( update );
	$( '.swpmb-input' ).on( 'clone', '.swpmb-time', update );
} );
