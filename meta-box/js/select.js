jQuery( function ( $ )
{
	'use strict';

	/**
	 * Object stores all necessary methods for select All/None actions
	 * Assign to global variable so we can access to this object from select advanced field
	 */
	var select = window.swpmbSelect = {
		/**
		 * Select all/none for select tag
		 *
		 * @param $input jQuery selector for input wrapper
		 *
		 * @return void
		 */
		selectAllNone: function ( $input )
		{
			var $element = $input.find( 'select' );

			$input.on( 'click', '.swpmb-select-all-none a', function ( e )
			{
				e.preventDefault();
				if ( 'all' == $( this ).data( 'type' ) )
				{
					var selected = [];
					$element.find( 'option' ).each( function ( i, e )
					{
						var $value = $( e ).attr( 'value' );

						if ( $value != '' )
						{
							selected.push( $value );
						}
					} );
					$element.val( selected ).trigger( 'change' );
				}
				else
				{
					$element.val( '' );
				}
			} );
		},

		/**
		 * Add event listener for select all/none links when click
		 *
		 * @param $el jQuery element
		 *
		 * @return void
		 */
		bindEvents: function ( $el )
		{
			var $input = $el.closest( '.swpmb-input' ),
				$clone = $input.find( '.swpmb-clone' );

			if ( $clone.length )
			{
				$clone.each( function ()
				{
					select.selectAllNone( $( this ) );
				} );
			}
			else
			{
				select.selectAllNone( $input );
			}
		}
	};

	/**
	 * Update select field when clicking clone button
	 *
	 * @return void
	 */
	function update()
	{
		select.bindEvents( $( this ) );
	}

	// Run for select field
	$( ':input.swpmb-select' ).each( update );
	$( '.swpmb-input' ).on( 'clone', ':input.swpmb-select', update );
} );
