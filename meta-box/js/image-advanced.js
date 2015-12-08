jQuery( function ( $ )
{
	'use strict';

	// Use only one frame for all upload fields
	var frame,
		template = $( '#tmpl-SW_META-image-advanced' ).html();

	$( 'body' ).on( 'click', '.SW_META-image-advanced-upload', function ( e )
	{
		e.preventDefault();

		var $uploadButton = $( this ),
			$imageList = $uploadButton.siblings( '.SW_META-images' ),
			maxFileUploads = $imageList.data( 'max_file_uploads' ),
			msg = maxFileUploads > 1 ? SW_METAFile.maxFileUploadsPlural : SW_METAFile.maxFileUploadsSingle;

		msg = msg.replace( '%d', maxFileUploads );

		// Create a frame only if needed
		if ( !frame )
		{
			frame = wp.media( {
				className: 'media-frame SW_META-media-frame',
				multiple : true,
				title    : SW_METAImageAdvanced.frameTitle,
				library  : {
					type: 'image'
				}
			} );
		}

		// Open media uploader
		frame.open();

		// Remove all attached 'select' event
		frame.off( 'select' );

		// Handle selection
		frame.on( 'select', function ()
		{
			// Get selections
			var selection = frame.state().get( 'selection' ).toJSON(),
				uploaded = $imageList.children().length,
				ids;

			if ( maxFileUploads > 0 && ( uploaded + selection.length ) > maxFileUploads )
			{
				if ( uploaded < maxFileUploads )
				{
					selection = selection.slice( 0, maxFileUploads - uploaded );
				}
				alert( msg );
			}

			// Get only files that haven't been added to the list
			// Also prevent duplication when send ajax request
			selection = _.filter( selection, function ( attachment )
			{
				return $imageList.children( 'li#item_' + attachment.id ).length === 0;
			} );
			ids = _.pluck( selection, 'id' );

			if ( ids.length > 0 )
			{
				var data = {
					action        : 'SW_META_attach_media',
					post_id       : $( '#post_ID' ).val(),
					field_id      : $imageList.data( 'field_id' ),
					attachment_ids: ids,
					_ajax_nonce   : $uploadButton.data( 'attach_media_nonce' )
				};

				$.post( ajaxurl, data, function ( r )
				{
					if ( r.success )
					{
						$imageList
							.append( _.template( template, { attachments: selection }, {
								evaluate   : /<#([\s\S]+?)#>/g,
								interpolate: /\{\{\{([\s\S]+?)\}\}\}/g,
								escape     : /\{\{([^\}]+?)\}\}(?!\})/g
							} ) )
							.trigger( 'update.SW_METAFile' );
					}
				}, 'json' );
			}
		} );
	} );
} );
