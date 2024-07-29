( function () {
	tinymce.create( 'tinymce.plugins.TMClickToTweet', {
		init( ed, url ) {
			ed.addButton( 'tmclicktotweet', {
				title: 'tmclicktotweet.quickaddd',
				image:
					url.replace( '/js', '' ) +
					'/img/twitter-little-bird-button.png',
				onclick() {
					const m = prompt( 'Click To Tweet', 'Enter your tweets' );
					if (
						m != null &&
						m != 'undefined' &&
						m != 'Enter your tweets' &&
						m != ''
					) {
						ed.execCommand(
							'mceInsertContent',
							false,
							'[Tweet "' + m + '"]'
						);
					}
				},
			} );
		},
		createControl( n, cm ) {
			return null;
		},
		getInfo() {
			return {
				longname: 'Click To Tweet by Todaymade',
				author: 'Todaymade',
				authorurl: 'http://coschedule.com/',
				infourl: 'http://coschedule.com/click-to-tweet',
				version: '1.0',
			};
		},
	} );
	tinymce.PluginManager.add(
		'tmclicktotweet',
		tinymce.plugins.TMClickToTweet
	);
} )();
