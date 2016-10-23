/* globals socialWarfareAdmin */

(function() {
	tinymce.PluginManager.add('clickToTweet', function( editor, url ) {
		editor.addButton( 'clickToTweet', {
			title: 'Click to Tweet by Social Warfare',
			icon: 'sw sw sw-twitter',
			onclick: function() {
				editor.windowManager.open( {
					title: 'Build Your "Click to Tweet"',
					class: 'swp_ctt',
					body: [
						{
							type: 'textbox',
							multiline: true,
							style: 'height:50px',
							name: 'tweet',
							label: 'The Tweet that will be sent out on Twitter.',
							onkeyup: function() {
								var value = jQuery( '.mce-first textarea' ).val();
								var strLength = value.length;
								var handle = jQuery( '#socialWarfare .twitterIDWrapper label' ).html();

								if (value.indexOf( 'http' ) > -1 || value.indexOf( 'https' ) > -1) {
									linkSpace = 0;
								} else {
									linkSpace = 23;
								};

								if (typeof handle === 'undefined') {
									var remaining = 140 - socialWarfareAdmin.linkLength( value ) - linkSpace;
								} else {
									var remaining = 140 - socialWarfareAdmin.linkLength( value ) - linkSpace - handle.length - 6;
								}
								if (remaining > 1 || remaining == 0) {
									jQuery( '.tweetCounter' ).css( {'color':'green'} ).text( remaining + ' characters' );
								} else if (remaining == 1) {
									jQuery( '.tweetCounter' ).css( {'color':'green'} ).text( remaining + ' character' );
								} else if (remaining < 0) {
									jQuery( '.tweetCounter' ).css( {'color':'red'} ).text( remaining + ' characters' );
								}
							},
							class: 'tweetCounting'
					},
						{
							type: 'label',
							name: 'someHelpText',
							onPostRender : function() {
								var value = jQuery( '.mce-first textarea' ).val();
								var strLength = value.length;
								var handle = jQuery( '#socialWarfare .twitterIDWrapper label' ).html();

								if (value.indexOf( 'http' ) > -1 || value.indexOf( 'https' ) > -1) {
									linkSpace = 0;
								} else {
									linkSpace = 23;
								};

								if (typeof handle === 'undefined') {
									var remaining = 140 - socialWarfareAdmin.linkLength( value ) - linkSpace;
								} else {
									var remaining = 140 - socialWarfareAdmin.linkLength( value ) - linkSpace - handle.length - 6;
								}

								this.getEl().innerHTML =
								   '<span style="float:right;">You have <span class="tweetCounter" style="color:green">' + remaining + ' characters</span> remaining.</span>';},
							text: ''},
						{
							type: 'textbox',
							multiline: true,
							style: 'height:50px',
							name: 'quote',
							label: 'The quote as it will appear in your article.'
					},{
						type: 'label',
						name: 'someHelpText2',
						onPostRender : function() {
							this.getEl().innerHTML =
							   '<div style="width:650px;">&nbsp;</div>';},
							text: ''},

					{type: 'listbox',
						name: 'theme',
						label: 'Visual Theme',
						'values': [
						{text: 'Default', value: 'default'},
						{text: 'Send Her My Love', value: 'style1'},
						{text: 'Roll With The Changes', value: 'style2'},
						{text: 'Free Bird', value: 'style3'},
						{text: 'Don\'t Stop Believin\'', value: 'style4'},
						{text: 'Thunderstruck', value: 'style5'},
						{text: 'Livin\' On A Prayer', value: 'style6'},
						],
					},
					],
					onsubmit: function( e ) {
						var value = jQuery( '.mce-first textarea' ).val();
						var strLength = value.length;
						var remaining = 117 - strLength;
						if (e.data.tweet === '' || e.data.quote === '') {
							editor.windowManager.alert( 'Please, fill in both fields.' );
							return false;
						} else if (remaining < 0) {
								editor.windowManager.alert( 'You have too many characters in your tweet.' );
							return false;
						}
						if (e.data.theme == 'default') {
							editor.insertContent( '[clickToTweet tweet="' + e.data.tweet.replace( /"/g,'\'' ) + '" quote="' + e.data.quote.replace( /"/g,'\'' ) + '"]' );
						} else {
							editor.insertContent( '[clickToTweet tweet="' + e.data.tweet.replace( /"/g,'\'' ) + '" quote="' + e.data.quote.replace( /"/g,'\'' ) + '" theme="' + e.data.theme + '"]' );
						}
					}
				});
			}
		});
	});
})();
