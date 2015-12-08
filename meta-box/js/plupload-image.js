jQuery( function ( $ )
{
	'use strict';

	// Hide "Uploaded files" title if there are no files uploaded after deleting files
	$( '.SW_META-images' ).on( 'click', '.SW_META-delete-file', function ()
	{
		// Check if we need to show drop target
		var $dragndrop = $( this ).parents( '.SW_META-images' ).siblings( '.SW_META-drag-drop' );

		// After delete files, show the Drag & Drop section
		$dragndrop.removeClass( 'hidden' );
	} );

	$( '.SW_META-drag-drop' ).each( function ()
	{
		// Declare vars
		var $dropArea = $( this ),
			$imageList = $dropArea.siblings( '.SW_META-uploaded' ),
			uploaderData = $dropArea.data( 'js_options' ),
			uploader = {};

		// Extend uploaderData
		uploaderData.multipart_params = $.extend(
			{
				_ajax_nonce: $dropArea.data( 'upload_nonce' ),
				post_id    : $( '#post_ID' ).val()
			},
			uploaderData.multipart_params
		);

		// Create uploader
		uploader = new plupload.Uploader( uploaderData );
		uploader.init();

		// Add files
		uploader.bind( 'FilesAdded', function ( up, files )
		{
			var maxFileUploads = $imageList.data( 'max_file_uploads' ),
				uploaded = $imageList.children().length,
				msg = maxFileUploads > 1 ? SW_METAFile.maxFileUploadsPlural : SW_METAFile.maxFileUploadsSingle;

			msg = msg.replace( '%d', maxFileUploads );

			// Remove files from queue if exceed max file uploads
			if ( maxFileUploads > 0 && ( uploaded + files.length ) > maxFileUploads )
			{
				if ( uploaded < maxFileUploads )
				{
					var diff = maxFileUploads - uploaded;
					up.splice( diff - 1, files.length - diff );
					files = up.files;
				}
				alert( msg );
			}

			// Hide drag & drop section if reach max file uploads
			if ( maxFileUploads > 0 && uploaded + files.length >= maxFileUploads )
			{
				$dropArea.addClass( 'hidden' );
			}

			var max = parseInt( up.settings.max_file_size, 10 );

			// Upload files
			plupload.each( files, function ( file )
			{
				addLoading( up, file, $imageList );
				addThrobber( file );
				if ( file.size >= max )
				{
					removeError( file );
				}
			} );
			up.refresh();
			up.start();

		} );

		uploader.bind( 'Error', function ( up, e )
		{
			addLoading( up, e.file, $imageList );
			removeError( e.file );
			up.removeFile( e.file );
		} );

		uploader.bind( 'FileUploaded', function ( up, file, r )
		{
			r = $.parseJSON( r.response );
			if ( r.success )
			{
				$( 'li#' + file.id ).replaceWith( r.data );
			}
			else
			{
				removeError( file );
			}
		} );
	} );

	/**
	 * Helper functions
	 */

	/**
	 * Removes li element if there is an error with the file
	 *
	 * @return void
	 */
	function removeError( file )
	{
		$( 'li#' + file.id )
			.addClass( 'SW_META-image-error' )
			.delay( 1600 )
			.fadeOut( 'slow', function ()
			{
				$( this ).remove();
			}
		);
	}

	/**
	 * Adds loading li element
	 *
	 * @return void
	 */
	function addLoading( up, file, $ul )
	{
		$ul.removeClass( 'hidden' ).append( '<li id="' + file.id + '"><div class="SW_META-image-uploading-bar"></div><div id="' + file.id + '-throbber" class="SW_META-image-uploading-status"></div></li>' );
	}

	/**
	 * Adds loading throbber while waiting for a response
	 *
	 * @return void
	 */
	function addThrobber( file )
	{
		$( '#' + file.id + '-throbber' ).html( '<img class="SW_META-loader" height="64" width="64" src="' + SW_META.url + '"img/loader.gif">' );
	}
} );
