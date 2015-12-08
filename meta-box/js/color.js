jQuery( function ( $ )
{
	'use strict';

	function SW_META_update_color_picker()
	{
		var $this = $( this ),
			$clone_container = $this.closest( '.SW_META-clone' ),
			$color_picker = $this.siblings( '.SW_META-color-picker' );

		// Make sure the value is displayed
		if ( !$this.val() )
		{
			$this.val( '#' );
		}

		if ( typeof $.wp === 'object' && typeof $.wp.wpColorPicker === 'function' )
		{
			if ( $clone_container.length > 0 )
			{
				$this.appendTo( $clone_container ).siblings( 'div.wp-picker-container' ).remove();
			}
			$this.wpColorPicker();
		}
		else
		{
			//We use farbtastic if the WordPress color picker widget doesn't exist
			$color_picker.farbtastic( $this );
		}
	}

	$( ':input.SW_META-color' ).each( SW_META_update_color_picker );
	$( '.SW_META-input' )
		.on( 'clone', ':input.SW_META-color', SW_META_update_color_picker )
		.on( 'focus', '.SW_META-color', function ()
		{
			$( this ).siblings( '.SW_META-color-picker' ).show();
			return false;
		} ).on( 'blur', '.SW_META-color', function ()
		{
			$( this ).siblings( '.SW_META-color-picker' ).hide();
			return false;
		} );
} );
