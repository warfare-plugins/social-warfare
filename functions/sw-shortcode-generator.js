(function() {
    tinymce.PluginManager.add('sw_shortcode_generator', function( editor, url ) {
        editor.addButton( 'sw_shortcode_generator', {
            title: 'Social Warfare Buttons',
            icon: 'sw sw sw-social-warfare',
            onclick: function() {
				editor.windowManager.open( {
					title: 'Insert Social Warfare Buttons',
					body: [
						{
							type: 'listbox',   
							name: 'reflection',
							label: 'Should the buttons reflect this post or another one?',
							'values': [
								{text: 'This Post', value: 'default'},
								{text: 'A Different Post', value: 'alt'}
							],
							onselect: function( v ) {
								if(this.value() == 'alt') {
									jQuery('.mce-postid').parent().parent().slideDown();
								} else {
									jQuery('.mce-postid').parent().parent().slideUp();
								}
							}
						},
						{
							type: 'textbox',
							multiline: false,
							name: 'postID',
							classes: 'postid',
							label: 'The ID of the post or page to reflect:'
						},
					],
					onPostRender : function() {
						jQuery('.mce-postid').parent().parent().slideUp();
						jQuery('.mce-title').prepend('<i class="sw sw-social-warfare"></i>');
					},
					onsubmit: function( e ) {
						if(e.data.reflection == 'alt' && e.data.postID != '') {
							editor.insertContent( '[social_warfare postID="' + e.data.postID + '"]');
						} else {
							editor.insertContent( '[social_warfare]');
						}
					}
				});
			}
        });
    });
})();