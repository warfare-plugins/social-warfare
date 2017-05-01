module.exports = function( grunt ) {
	'use strict';

	require( 'load-grunt-tasks' )( grunt );
	var autoprefixer = require( 'autoprefixer' );

	grunt.initConfig( {
		pkg: grunt.file.readJSON( 'package.json' ),

		makepot: {
			options: {
				exclude: [ 'node_modules/.*' ],
				domainPath: '/languages',
				type: 'wp-plugin',
				processPot: function( pot ) {
					pot.headers['report-msgid-bugs-to'] = 'https://warfareplugins.com/';
					pot.headers['plural-forms'] = 'nplurals=2; plural=n != 1;';
					pot.headers['last-translator'] = 'Warfare Plugins (https://warfareplugins.com)\n';
					pot.headers['language-team'] = 'Warfare Plugins (https://warfareplugins.com)\n';
					pot.headers['x-poedit-basepath'] = '.\n';
					pot.headers['x-poedit-language'] = 'English\n';
					pot.headers['x-poedit-country'] = 'UNITED STATES\n';
					pot.headers['x-poedit-sourcecharset'] = 'utf-8\n';
					pot.headers['x-poedit-keywordslist'] = '__;_e;__ngettext:1,2;_n:1,2;__ngettext_  noop:1,2;_n_noop:1,2;_c,_nc:4c,1,2;_x:1,2c;_ex:1,2c;_nx:4c,1,2;_nx_noop:4c,1,2;\n';
					pot.headers['x-textdomain-support'] = 'yes\n';
					return pot;
				}
			},
			files: {
				src: [
					'**/*.php'
				]
			}
		},

		addtextdomain: {
			options: {
				textdomain: 'social-warfare',
				updateDomains: [ 'all' ]
			},
			files: {
				src: [
					'**/*.php',
					'!node_modules/**/*.php'
				]
			}
		},

		version: {
			project: {
				src: [
					'package.json'
				]
			},
			functions: {
				options: {
					prefix: 'swp_VERSION\'\,\\s+\''
				},
				src: [
					'social-warfare.php'
				]
			}
		},

		wpcss: {
			style: {
				options: {
					commentSpacing: true,
					config: 'alphabetical'
				},
				files: [
					{
						src: 'css/style.css',
						dest: 'css/style.css'
					},
					{
						src: 'css/style.css',
						dest: 'css/style.css'
					}
				]
			}
		},

		cssmin: {
			options: {
				report: 'gzip',
				sourceMap: false
			},
			style: {
				expand: true,
				cwd: 'css/',
				src: [
					'*.css',
					'**/*.css',
					'!*.min.css',
					'!**/*.min.css'
				],
				dest: 'css/',
				ext: '.min.css',
				extDot: 'last'
			}
		},

		postcss: {
			options: {
				processors: [
					autoprefixer( {
						browsers: [
							'Android >= 2.1',
							'Chrome >= 21',
							'Explorer >= 8',
							'Firefox >= 17',
							'Opera >= 12.1',
							'Safari >= 6.0'
						]
					} )
				]
			},
			plugin: {
				files: [
					{
						expand: true,
						cwd: 'css/',
						src: [
							'**/*.css',
							'!**/*.min.css'
						],
						dest: 'css/',
						ext: '.css',
						extDot: 'last'
					}
				]
			}
		},

		uglify: {
			plugin: {
				options: {
					sourceMap: false,
					mangle: true,
					compress: true,
					report: 'gzip'
				},
				files: [
					{
						expand: true,
						cwd: 'js/',
						src: [
							'*.js',
							'!*.min.js'
						],
						dest: 'js/',
						ext: '.min.js',
						extDot: 'last'
					}
				]
			}
		},
		replace: {
			metabox: {
				options: {
					patterns: [
						{
							match: /RWMB/g,
							replacement: 'SWPMB'
						},
						{
							match: /Rwmb/g,
							replacement: 'Swpmb'
						},
						{
							match: /RW_/g,
							replacement: 'SWP_'
						},
						{
							match: /rwmb/g,
							replacement: 'swpmb'
						}
					]
				},
				files: [
					{
						expand: true,
						src: [
							'meta-box/**',
							'meta-box/.*',
							'!meta-box/img/*',
							'!meta-box/lang/*'
						]
					}
				]
			}
		},
		watch: {
			js: {
				files: [
					'js/*.js',
					'!js/*.min.js'
				],
				tasks: [
					'uglify'
				]
			},
			css: {
				files: [
					'css/*.css',
					'!css/*.min.css'
				],
				tasks: [
					'postcss',
					'wpcss',
					'cssmin'
				]
			}
		}
	});

	grunt.registerTask( 'default', [ 'watch' ] );
	grunt.registerTask( 'build', [
		//'addtextdomain',
		//'makepot',
		'postcss',
		'wpcss',
		'cssmin',
		'uglify'
	]);
};
