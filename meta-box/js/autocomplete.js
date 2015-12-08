jQuery( function ( $ )
{
	'use strict';

	/**
	 * Update date picker element
	 * Used for static & dynamic added elements (when clone)
	 */
	function updateAutocomplete( e )
	{
		var $this = $( this ),
			$result = $this.next(),
			name = $this.data( 'name' );

		// If the function is called on cloning, then change the field name and clear all results
		// @see clone.js
		if ( e.hasOwnProperty( 'type' ) && 'clone' == e.type )
		{
			name = name.replace( /\[(\d+)\]/, function ( match, p1 )
			{
				return '[' + ( parseInt( p1, 10 ) + 1 ) + ']';
			} );

			// Update the "data-name" attribute for further cloning
			$this.attr( 'data-name', name );

			// Clear all results
			$result.html( '' );
		}

		$this.removeClass( 'ui-autocomplete-input' ).attr( 'id', '' )
			.autocomplete( {
			minLength: 0,
			source   : $this.data( 'options' ),
			select   : function ( event, ui )
			{
				$result.append(
					'<div class="SW_META-autocomplete-result">' +
					'<div class="label">' + ui.item.label + '</div>' +
					'<div class="actions">' + SW_META_Autocomplete.delete + '</div>' +
					'<input type="hidden" class="SW_META-autocomplete-value" name="' + name + '" value="' + ui.item.value + '">' +
					'</div>'
				);

				// Reinitialize value
				this.value = '';

				return false;
			}
		} );
	}

	$( '.SW_META-autocomplete-wrapper input[type="text"]' ).each( updateAutocomplete );
	$( '.SW_META-input' ).on( 'clone', ':input.SW_META-autocomplete', updateAutocomplete );

	// Handle remove action
	$( document ).on( 'click', '.SW_META-autocomplete-result .actions', function ()
	{
		// remove result
		$( this ).parent().remove();
	} );
} );