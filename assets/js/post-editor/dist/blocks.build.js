! ( function ( e ) {
	function t( n ) {
		if ( a[ n ] ) {
			return a[ n ].exports;
		}
		const l = ( a[ n ] = { i: n, l: ! 1, exports: {} } );
		return (
			e[ n ].call( l.exports, l, l.exports, t ), ( l.l = ! 0 ), l.exports
		);
	}
	var a = {};
	( t.m = e ),
		( t.c = a ),
		( t.d = function ( e, a, n ) {
			t.o( e, a ) ||
				Object.defineProperty( e, a, {
					configurable: ! 1,
					enumerable: ! 0,
					get: n,
				} );
		} ),
		( t.n = function ( e ) {
			const a =
				e && e.__esModule
					? function () {
							return e.default;
					  }
					: function () {
							return e;
					  };
			return t.d( a, 'a', a ), a;
		} ),
		( t.o = function ( e, t ) {
			return Object.prototype.hasOwnProperty.call( e, t );
		} ),
		( t.p = '' ),
		t( ( t.s = 2 ) );
} )( [
	function ( e, t ) {},
	function ( e, t ) {},
	function ( e, t, a ) {
		'use strict';
		Object.defineProperty( t, '__esModule', { value: ! 0 } );
		a( 3 ), a( 4 ), a( 5 );
	},
	function ( e, t, a ) {
		'use strict';
		function n( e, t, a ) {
			return (
				t in e
					? Object.defineProperty( e, t, {
							value: a,
							enumerable: ! 0,
							configurable: ! 0,
							writable: ! 0,
					  } )
					: ( e[ t ] = a ),
				e
			);
		}
		const l = a( 0 ),
			r = ( a.n( l ), wp.i18n.__ ),
			s = wp.blocks.registerBlockType,
			i = wp.components.Dashicon,
			c = wp.element.createElement(
				'div',
				{ className: 'swp-block-icon' },
				wp.element.createElement(
					'svg',
					{
						version: '1.1',
						id: 'Layer_1',
						x: '0px',
						y: '0px',
						viewBox: '0 0 32 32',
						'enable-background': 'new 0 0 32 32',
					},
					wp.element.createElement( 'path', {
						fill: '#ee464f',
						d: 'M8.6,9.9c0.2-0.8,1.8-4.2,5.5-6.3C8.1,4.5,3.5,9.7,3.5,15.9c0,1.6,0.3,3.2,0.9,4.6c0.2-0.2,0.5-0.3,0.8-0.3 l4.6-0.9c0.8-0.2,1.1,0.2,0.9,1c-0.5,1.8,0.5,2.9,2.3,2.9c1.8,0,3.6-1.1,3.7-2.1C17.1,17.8,5.5,18.5,8.6,9.9z M27.2,10.4 c-0.3,0.3-0.6,0.6-1.1,0.7L21.4,12c-0.8,0.2-1.1-0.2-0.9-0.9c0.3-1.5-0.6-2.5-2.4-2.5c-1.5,0-2.7,0.9-2.8,1.7 c-0.5,2.9,11.4,2.9,8.4,11.5c-0.3,0.8-2.3,4.6-6.8,6.6c6.5-0.4,11.7-5.8,11.7-12.4C28.5,14,28,12.1,27.2,10.4z',
					} )
				)
			);
		s( 'social-warfare/social-warfare', {
			title: r( 'Social Warfare' ),
			icon: c,
			category: 'social-warfare',
			keywords: [ r( 'share' ), r( 'button' ), r( 'tweet' ) ],
			attributes: {
				hasFocus: { type: 'boolean', defualt: ! 1 },
				useThisPost: { type: 'string', default: 'this' },
				postID: { type: 'number', default: '' },
				buttons: { type: 'string', default: '' },
			},
			edit( e ) {
				const t = e.attributes,
					a =
						( t.useThisPost,
						t.buttons,
						t.postID,
						function ( t ) {
							e.setAttributes( {
								hasFocus: ! e.attributes.hasFocus,
							} );
						} ),
					l = function ( t ) {
						e.setAttributes(
							n( {}, t.target.name, t.target.value )
						);
					},
					r = function ( t ) {
						e.setAttributes( { buttons: t.target.value } );
					},
					s = function ( t ) {
						const a =
							( wp.data
								.select( 'core/editor' )
								.getCurrentPostId(),
							t.target.value );
						if ( '' == a ) {
							return void e.setAttributes( { postID: '' } );
						}
						isNaN( parseInt( a ) ) ||
							e.setAttributes( { postID: parseInt( a ) } );
					};
				if ( ! e.attributes.hasFocus ) {
					const o =
							e.attributes.buttons && e.attributes.buttons.length
								? 'buttons="' + e.attributes.buttons + '"'
								: '',
						u =
							'other' == e.attributes.useThisPost
								? 'id="' + e.attributes.postID + '"'
								: '';
					return wp.element.createElement(
						'div',
						{ className: 'social-warfare-admin-block' },
						wp.element.createElement(
							'div',
							{
								className:
									e.className +
									' social-warfare-block-wrap swp-inactive-block',
							},
							wp.element.createElement(
								'div',
								{ className: 'head', onClick: a },
								c,
								wp.element.createElement(
									'div',
									{ className: 'swp-preview' },
									'[social_warfare ',
									o,
									' ',
									u,
									']'
								),
								wp.element.createElement( i, {
									className: 'swp-dashicon',
									icon: 'arrow-down',
								} )
							)
						)
					);
				}
				return wp.element.createElement(
					'div',
					{ className: 'social-warfare-admin-block' },
					wp.element.createElement(
						'div',
						{
							className:
								e.className +
								' social-warfare-block-wrap swp-active-block',
						},
						wp.element.createElement(
							'div',
							{ className: 'head', onClick: a },
							wp.element.createElement(
								'div',
								null,
								c,
								wp.element.createElement(
									'p',
									{ className: 'swp-block-title' },
									'Social Warfare Shortcode'
								)
							),
							wp.element.createElement( i, {
								className: 'swp-dashicon',
								icon: 'arrow-down',
							} )
						),
						wp.element.createElement(
							'p',
							null,
							'Inserts a ',
							wp.element.createElement(
								'pre',
								{ style: { display: 'inline' } },
								'[social_warfare]'
							),
							' shortcode. Leave a field blank to use values based on your global settings. ',
							wp.element.createElement(
								'a',
								{
									href: 'https://warfareplugins.com/support/using-shortcodes-and-php-snippets/',
								},
								'Learn more'
							)
						),
						wp.element.createElement(
							'p',
							null,
							'Should the buttons reflect this post, or a different post?'
						),
						wp.element.createElement(
							'select',
							{
								name: 'useThisPost',
								value:
									'other' == e.attributes.useThisPost
										? 'other'
										: 'this',
								onChange: l,
							},
							wp.element.createElement(
								'option',
								{ value: 'this' },
								'This post'
							),
							wp.element.createElement(
								'option',
								{ value: 'other' },
								'Another post'
							)
						),
						'other' == e.attributes.useThisPost &&
							wp.element.createElement(
								'div',
								null,
								wp.element.createElement(
									'p',
									null,
									'Which post should we fetch SW settings and shares from?'
								),
								wp.element.createElement( 'input', {
									type: 'text',
									onChange: s,
									value: e.attributes.postID,
								} )
							),
						wp.element.createElement(
							'p',
							null,
							'Which networks should we display? Leave blank to use your global settings. '
						),
						wp.element.createElement( 'input', {
							value: e.attributes.buttons,
							type: 'text',
							onChange: r,
						} )
					)
				);
			},
			save( e ) {
				const t =
						e.attributes.buttons && e.attributes.buttons.length
							? 'buttons="' + e.attributes.buttons + '"'
							: '',
					a =
						'other' == e.attributes.useThisPost
							? 'id="' + e.attributes.postID + '"'
							: '';
				return wp.element.createElement(
					'div',
					null,
					'[social_warfare ',
					t,
					' ',
					a,
					']'
				);
			},
		} );
	},
	function ( e, t, a ) {
		'use strict';
		const n = a( 1 ),
			l = ( a.n( n ), a( 0 ) ),
			r = ( a.n( l ), wp.i18n.__ ),
			s = wp.blocks.registerBlockType,
			i = wp.data.select( 'core/editor' ),
			c = ( i.getCurrentPostId, wp.components.Dashicon ),
			o = wp.element.createElement(
				'div',
				{ className: 'swp-block-icon', style: { color: '#429cd6' } },
				wp.element.createElement( c, { icon: 'twitter' } )
			);
		s( 'social-warfare/click-to-tweet', {
			title: r( 'Click To Tweet' ),
			icon: o,
			category: 'social-warfare',
			keywords: [ r( 'twitter' ), r( 'quote' ), r( 'share' ) ],
			attributes: {
				hasFocus: { type: 'boolean', defualt: ! 1 },
				tweetText: { type: 'string', default: '' },
				displayText: { type: 'string', default: '' },
				overLimit: { type: 'boolean', default: ! 1 },
			},
			edit( e ) {
				window.onetwothree = 123;
				const t = e.attributes,
					a = t.tweetText,
					n = t.displayText,
					l = t.theme,
					r = [
						'Default',
						'Send Her My Love',
						'Roll With The Changes',
						'Free Bird',
						"Don't Stop Believin'",
						'Thunderstruck',
						"Livin' On A Prayer",
					],
					s =
						( e.attributes.overLimit,
						e.attributes.overLimit ? 'over-limit' : '' ),
					i = function ( t ) {
						const a = t.target.value.replace(/"/g, "'" );
						if ( ! a || ! a.length ) {
							return e.setAttributes( {
								tweetText: '',
								overLimit: ! 1,
							} );
						}
						const n = a.length > 280;
						e.setAttributes( { overLimit: n, tweetText: a } );
					},
					u = function ( t ) {
						const a = t.target.value.replace(/"/g, "'" );
						e.setAttributes( { displayText: a } );
					},
					m = function ( t ) {
						const a = t.target.value;
						0 == parseInt( a )
							? e.setAttributes( { theme: '' } )
							: e.setAttributes( { theme: a } );
					},
					p = function ( t ) {
						e.setAttributes( {
							hasFocus: ! e.attributes.hasFocus,
						} );
					};
				if ( ! e.attributes.hasFocus ) {
					const w = e.attributes.displayText
						? e.attributes.displayText
						: e.attributes.tweetText
						? e.attributes.tweetText
						: 'No Click To Tweet text is provided.';
					return wp.element.createElement(
						'div',
						{ className: 'social-warfare-admin-block' },
						wp.element.createElement(
							'div',
							{
								className:
									e.className +
									' click-to-tweet-block-wrap swp-inactive-block',
							},
							wp.element.createElement(
								'div',
								{ className: 'head', onClick: p },
								o,
								wp.element.createElement(
									'div',
									{ className: 'swp-preview' },
									w
								),
								wp.element.createElement( c, {
									className: 'swp-dashicon',
									icon: 'arrow-down',
								} )
							)
						)
					);
				}
				return wp.element.createElement(
					'div',
					{ className: 'social-warfare-admin-block' },
					wp.element.createElement(
						'div',
						{
							className:
								e.className +
								' click-to-tweet-block-wrap swp-active-block',
						},
						wp.element.createElement(
							'div',
							{ className: 'head', onClick: p },
							wp.element.createElement(
								'div',
								null,
								o,
								wp.element.createElement(
									'p',
									{ className: 'swp-block-title' },
									'Click to Tweet'
								)
							),
							wp.element.createElement( c, {
								className: 'swp-dashicon',
								icon: 'arrow-up',
							} )
						),
						wp.element.createElement(
							'p',
							null,
							'Inserts a ',
							wp.element.createElement(
								'pre',
								{ style: { display: 'inline' } },
								'[click_to_tweet]'
							),
							' shortcode. ',
							wp.element.createElement(
								'a',
								{
									href: 'https://warfareplugins.com/support/click-to-tweet/',
								},
								'Learn more'
							)
						),
						wp.element.createElement(
							'p',
							null,
							'Type your tweet as you want it to display ',
							wp.element.createElement(
								'b',
								null,
								wp.element.createElement(
									'em',
									null,
									'on Twitter'
								)
							),
							':'
						),
						wp.element.createElement(
							'div',
							{ style: { width: '100%' } },
							wp.element.createElement(
								'p',
								{
									className:
										'block-characters-remaining ' + s,
									style: { marginTop: -33 },
								},
								280 - a.length
							),
							wp.element.createElement( 'textarea', {
								name: 'tweetText',
								placeholder: 'Type your tweet. . . ',
								onChange: i,
								value: a,
							} )
						),
						wp.element.createElement(
							'p',
							null,
							'Type your quote as you want it to display ',
							wp.element.createElement(
								'b',
								null,
								wp.element.createElement(
									'em',
									null,
									'on the page'
								)
							),
							':'
						),
						wp.element.createElement( 'textarea', {
							name: 'displayText',
							placeholder: 'Type your quote. . . ',
							onChange: u,
							value: n,
						} ),
						wp.element.createElement(
							'p',
							null,
							'Which theme would you like to use for this CTT?'
						),
						wp.element.createElement(
							'select',
							{ name: 'theme', value: l, onChange: m },
							r.map( function ( e, t ) {
								return wp.element.createElement(
									'option',
									{ value: t },
									e
								);
							} )
						)
					)
				);
			},
			save( e ) {
				let t = e.attributes.tweetText,
					a = e.attributes.displayText;
				if ( t ) {
					a || ( a = t );
					const n = e.attributes.theme
						? 'style' + e.attributes.theme
						: '';
					return wp.element.createElement(
						'div',
						{ className: 'social-warfare-admin-block' },
						'[click_to_tweet tweet="',
						t,
						'" quote="',
						a,
						'" theme="',
						n,
						'"]'
					);
				}
			},
		} );
	},
	function ( e, t, a ) {
		'use strict';
		function n( e, t, a ) {
			return (
				t in e
					? Object.defineProperty( e, t, {
							value: a,
							enumerable: ! 0,
							configurable: ! 0,
							writable: ! 0,
					  } )
					: ( e[ t ] = a ),
				e
			);
		}
		var l = a( 1 ),
			r = ( a.n( l ), a( 0 ) ),
			s =
				( a.n( r ),
				( function () {
					function e( e, t ) {
						let a = [],
							n = ! 0,
							l = ! 1,
							r = void 0;
						try {
							for (
								var s, i = e[ Symbol.iterator ]();
								! ( n = ( s = i.next() ).done ) &&
								( a.push( s.value ), ! t || a.length !== t );
								n = ! 0
							) {}
						} catch ( e ) {
							( l = ! 0 ), ( r = e );
						} finally {
							try {
								! n && i.return && i.return();
							} finally {
								if ( l ) {
									throw r;
								}
							}
						}
						return a;
					}
					return function ( t, a ) {
						if ( Array.isArray( t ) ) {
							return t;
						}
						if ( Symbol.iterator in Object( t ) ) {
							return e( t, a );
						}
						throw new TypeError(
							'Invalid attempt to destructure non-iterable instance'
						);
					};
				} )() ),
			i = +new Date() + 1e4,
			c = setInterval( function () {
				if (
					( +new Date() > i && clearTimeout( c ),
					'undefined' !== typeof socialWarfare &&
						( clearInterval( c ),
						socialWarfare.addons &&
							socialWarfare.addons.includes( 'pro' ) ) )
				) {
					const e = wp.i18n.__,
						t = wp.blocks.registerBlockType,
						a = wp.data.select( 'core/editor' ),
						l = ( a.getCurrentPostId, wp.components.Dashicon ),
						r = wp.element.createElement(
							'div',
							{ className: 'swp-block-icon' },
							wp.element.createElement(
								'svg',
								{
									version: '1.1',
									id: 'Layer_1',
									x: '0px',
									y: '0px',
									viewBox: '0 0 32 32',
									'enable-background': 'new 0 0 32 32',
								},
								wp.element.createElement(
									'g',
									null,
									wp.element.createElement( 'path', {
										fill: '#cd2029',
										d: 'M16,3.9C9.3,3.9,3.9,9.3,3.9,16c0,4.9,3,9.2,7.2,11.1c0-0.8,0-1.9,0.2-2.8c0.2-1,1.6-6.6,1.6-6.6 s-0.4-0.8-0.4-1.9c0-1.8,1-3.1,2.3-3.1c1.1,0,1.6,0.8,1.6,1.8c0,1.1-0.7,2.8-1.1,4.3c-0.3,1.3,0.6,2.3,1.9,2.3 c2.3,0,3.8-2.9,3.8-6.4c0-2.6-1.8-4.6-5-4.6c-3.7,0-5.9,2.7-5.9,5.8c0,1.1,0.3,1.8,0.8,2.4c0.2,0.3,0.3,0.4,0.2,0.7 c-0.1,0.2-0.2,0.8-0.2,1c-0.1,0.3-0.3,0.4-0.6,0.3c-1.7-0.7-2.5-2.5-2.5-4.6c0-3.4,2.9-7.5,8.6-7.5c4.6,0,7.6,3.3,7.6,6.9 c0,4.7-2.6,8.3-6.5,8.3c-1.3,0-2.5-0.7-2.9-1.5c0,0-0.7,2.8-0.9,3.3c-0.3,0.9-0.8,1.9-1.2,2.6c1.1,0.3,2.2,0.5,3.4,0.5 c6.7,0,12.1-5.4,12.1-12.1C28.1,9.3,22.7,3.9,16,3.9z',
									} )
								)
							)
						);
					t( 'social-warfare/pinterest', {
						title: e( 'Pinterest Image' ),
						icon: r,
						category: 'social-warfare',
						keywords: [ e( 'share' ), e( 'pin' ), e( 'tailwind' ) ],
						attributes: {
							hasFocus: { type: 'boolean', defualt: ! 1 },
							id: { type: 'number', default: 0 },
							width: { type: 'number', default: 0 },
							height: { type: 'number', default: 0 },
							className: { type: 'string', default: '' },
							alignment: { type: 'string', default: '' },
						},
						edit( e ) {
							const t = function ( t ) {
									e.setAttributes( {
										hasFocus: ! e.attributes.hasFocus,
									} );
								},
								a = {
									id: 'Post ID or Image ID',
									width: 'Width (in pixels)',
									height: 'Height (in pixels)',
									className: 'Custom CSS class',
									alignment:
										'Alignment. You may enter one of: left, right, center',
								},
								i = function ( t ) {
									e.setAttributes(
										n( {}, t.target.name, t.target.value )
									);
								};
							if ( ! e.attributes.hasFocus ) {
								const c = Object.entries( e.attributes ).reduce(
									function ( e, t ) {
										const a = s( t, 2 ),
											n = a[ 0 ],
											l = a[ 1 ];
										return l.length &&
											'undefined' !== typeof l
											? ( e += ' ' + n + '="' + l + '"' )
											: e;
									},
									''
								);
								return wp.element.createElement(
									'div',
									{
										className:
											e.className +
											' pinterest-block-wrap swp-inactive-block',
									},
									wp.element.createElement(
										'div',
										{ className: 'head', onClick: t },
										r,
										wp.element.createElement(
											'div',
											{ className: 'swp-preview' },
											'[pinterest_image',
											c,
											']'
										),
										wp.element.createElement( l, {
											className: 'swp-dashicon',
											icon: 'arrow-down',
										} )
									)
								);
							}
							return wp.element.createElement(
								'div',
								{ className: 'social-warfare-admin-block' },
								wp.element.createElement(
									'div',
									{
										className:
											e.className +
											' pinterest-block-wrap swp-active-block',
									},
									wp.element.createElement(
										'div',
										{ className: 'head', onClick: t },
										wp.element.createElement(
											'div',
											null,
											r,
											wp.element.createElement(
												'p',
												{
													className:
														'swp-block-title',
												},
												'Pinterest Image'
											)
										),
										wp.element.createElement( l, {
											className: 'swp-dashicon',
											icon: 'arrow-up',
										} )
									),
									wp.element.createElement(
										'p',
										null,
										'Inserts a ',
										wp.element.createElement(
											'pre',
											{ style: { display: 'inline' } },
											'[pinterest_image]'
										),
										' shortcode. Leave a field blank to use values based on your global settings.'
									),
									Object.entries( a ).map( function ( t ) {
										const a = s( t, 2 ),
											n = a[ 0 ],
											l = a[ 1 ];
										if ( 'alignment' != n ) {
											const r =
												'width' == n || 'height' == n
													? 'swp-inner-block-50'
													: '';
											return wp.element.createElement(
												'div',
												{ className: r },
												wp.element.createElement(
													'p',
													null,
													l
												),
												wp.element.createElement(
													'input',
													{
														name: n,
														type: 'text',
														onChange: i,
														value:
															e.attributes[ n ] ||
															'',
													}
												)
											);
										}
									} ),
									wp.element.createElement(
										'div',
										null,
										wp.element.createElement(
											'p',
											null,
											'Alignment'
										),
										wp.element.createElement(
											'select',
											{
												name: 'alignment',
												value: e.attributes.alignment
													? e.attributes.alignment
													: '',
												onChange: i,
											},
											wp.element.createElement(
												'option',
												{ value: '' },
												'Default'
											),
											wp.element.createElement(
												'option',
												{ value: 'left' },
												'Left'
											),
											wp.element.createElement(
												'option',
												{ value: 'center' },
												'Center'
											),
											wp.element.createElement(
												'option',
												{ value: 'right' },
												'Right'
											)
										)
									)
								)
							);
						},
						save( e ) {
							return wp.element.createElement(
								'div',
								null,
								'[pinterest_image]'
							);
						},
					} );
				}
			}, 100 );
	},
] );
