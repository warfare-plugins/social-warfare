window.swpmb = window.swpmb || {};

jQuery( function ( $ )
{
	'use strict';

	var views = swpmb.views = swpmb.views || {},
		MediaField = views.MediaField,
		MediaItem = views.MediaItem,
		MediaList = views.MediaList,
		ImageField, ImageList, ImageItem;

	ImageField = views.ImageField = MediaField.extend( {
		createList: function ()
		{
			this.list = new MediaList( { collection: this.collection, props: this.props, itemView: ImageItem } );
		}
	} );

	ImageItem = views.ImageItem = MediaItem.extend( {
		className: 'swpmb-image-item',
		template : wp.template( 'swpmb-image-item' )
	} );

	/**
	 * Initialize image fields
	 * @return void
	 */
	function initImageField()
	{
		new ImageField( { input: this, el: $( this ).siblings( 'div.swpmb-media-view' ) } );
	}
	$( ':input.swpmb-image_advanced' ).each( initImageField );
	$( '.swpmb-input' )
		.on( 'clone', ':input.swpmb-image_advanced', initImageField )
} );
