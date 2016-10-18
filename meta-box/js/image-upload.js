window.swpmb = window.swpmb || {};

jQuery( function ( $ )
{
	'use strict';

	var views = swpmb.views = swpmb.views || {},
		ImageField = views.ImageField,
		ImageUploadField,
		UploadButton = views.UploadButton;

	ImageUploadField = views.ImageUploadField = ImageField.extend( {
		createAddButton: function ()
		{
			this.addButton = new UploadButton( { collection: this.collection, props: this.props } );
		}
	} );

	/**
	 * Initialize fields
	 * @return void
	 */
	function init()
	{
		new ImageUploadField( { input: this, el: $( this ).siblings( 'div.swpmb-media-view' ) } );
		console.log('win');
	}
	$( ':input.swpmb-image_upload, :input.swpmb-plupload_image' ).each( init );
	$( '.swpmb-input' )
		.on( 'clone', ':input.swpmb-image_upload, :input.swpmb-plupload_image', init )
} );
