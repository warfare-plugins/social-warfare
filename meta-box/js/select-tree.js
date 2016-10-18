jQuery( function( $ )
{
	'use strict';

	function update()
	{
		var $this = $( this ),
			val = $this.val(),
			$selected = $this.siblings( "[data-parent-id='" + val + "']" ),
			$notSelected = $this.parent().find( '.swpmb-select-tree' ).not( $selected );

		$selected.removeClass( 'hidden' );
		$notSelected
			.addClass( 'hidden' )
			.find( 'select' )
			.prop( 'selectedIndex', 0 );
	}

	$( '.swpmb-input' )
		.on( 'change', '.swpmb-select-tree select', update )
		.on( 'clone', '.swpmb-select-tree select', update );
} );
