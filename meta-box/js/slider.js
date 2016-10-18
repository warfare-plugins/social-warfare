jQuery( function( $ )
{
	'use strict';	

	function swpmb_update_slider()
	{
		var $input = $( this ),
			$slider = $input.siblings( '.swpmb-slider' ),
			$valueLabel = $slider.siblings( '.swpmb-slider-value-label' ).find( 'span' ),
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

	$( ':input.swpmb-slider-value' ).each( swpmb_update_slider );
	$( '.swpmb-input' ).on( 'clone', ':input.swpmb-slider-value', swpmb_update_slider );
} );
