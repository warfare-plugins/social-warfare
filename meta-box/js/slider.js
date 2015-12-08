jQuery( function( $ )
{
	'use strict';

	function SW_META_update_slider()
	{
		var $input = $( this ),
			$slider = $input.siblings( '.SW_META-slider' ),
			$valueLabel = $slider.siblings( '.SW_META-slider-value-label' ).find( 'span' ),
			value = $input.val(),
			options = $slider.data( 'options' );

		$slider.html( '' );

		if ( !value )
		{
			value = 0;
			$input.val( 0 );
			$valueLabel.text( '0' );
		}
		else
		{
			$valueLabel.text( value );
		}

		// Assign field value and callback function when slide
		options.value = value;
		options.slide = function( event, ui )
		{
			$input.val( ui.value );
			$valueLabel.text( ui.value );
		};

		$slider.slider( options );
	}

	$( ':input.SW_META-slider-value' ).each( SW_META_update_slider );
	$( '.SW_META-input' ).on( 'clone', ':input.SW_META-slider-value', SW_META_update_slider );
} );
