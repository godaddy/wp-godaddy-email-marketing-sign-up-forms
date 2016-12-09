/* jshint node:true */
module.exports = function( grunt ) {
	'use strict';

	var pkg = grunt.file.readJSON( 'package.json' ),
	    BUILD_DIR = 'build';

	require( 'matchdep' ).filterDev( 'grunt-*' ).forEach( grunt.loadNpmTasks );

	// Project configuration.
	grunt.initConfig( {
		pkg: pkg,

		keywords: [
			'__',
			'_e',
			'__ngettext:1,2',
			'_n:1,2',
			'__ngettext_noop:1,2',
			'_n_noop:1,2',
			'_c',
			'_nc:4c,1,2',
			'_x:1,2c',
			'_nx:4c,1,2',
			'_nx_noop:4c,1,2',
			'_ex:1,2c',
			'esc_attr__',
			'esc_attr_e',
			'esc_attr_x:1,2c',
			'esc_html__',
			'esc_html_e',
			'esc_html_x:1,2c'
		],

		cssmin: {
			options: {
				shorthandCompacting: false,
				roundingPrecision: -1,
				processImport: false
			},
			target: {
				files: [{
					expand: true,
					cwd: 'css',
					src: ['*.css', '!*.min.css'],
					dest: 'css',
					ext: '.min.css'
				}]
			}
		},

		uglify: {
			options: {
				ASCIIOnly: true
			},
			core: {
				expand: true,
				cwd: 'js',
				dest: 'js',
				ext: '.min.js',
				src: ['*.js', '!*.min.js']
			}
		},

		watch: {
			css: {
				files: ['*.css', '!*.min.css'],
				options: {
					cwd: 'css',
					nospawn: true
				},
				tasks: ['cssmin']
			},
			uglify: {
				files: ['*.js', '!*.js.css'],
				options: {
					cwd: 'js',
					nospawn: true
				},
				tasks: ['uglify']
			}
		},

		pot: {
			options: {
				omit_header: false,
				text_domain: 'godaddy-email-marketing',
				encoding: 'UTF-8',
				dest: 'languages/',
				keywords: '<%= keywords %>',
				msgmerge: true
			},
			files: {
				src: [ 'godaddy-email-marketing.php', 'includes/*.php' ],
				expand: true
			}
		},

		po2mo: {
			files: {
				src: [
					'languages/*.po',
					'!languages/*.po~'
				],
				expand: true
			}
		},

		// Check textdomain errors.
		checktextdomain: {
			options:{
				text_domain: 'godaddy-email-marketing',
				keywords: '<%= keywords %>'
			},
			files: {
				src: [
					'**/*.php',
					'!build/**',
					'!dev-lib/**',
					'!node_modules/**',
					'!tests/**'
				],
				expand: true
			}
		},

		// Build a deployable plugin.
		copy: {
			build: {
				src: [
					'**',
					'!.*',
					'!.*/**',
					'!.DS_Store',
					'!assets/**',
					'!build/**',
					'!composer.json',
					'!dev-lib/**',
					'!Gruntfile.js',
					'!languages/*.po*',
					'!node_modules/**',
					'!npm-debug.log',
					'!package.json',
					'!phpcs.ruleset.xml',
					'!phpunit.xml.dist',
					'!readme.md',
					'!tests/**'
				],
				dest: BUILD_DIR,
				expand: true,
				dot: true
			}
		},

		replace: {
			version_php: {
				src: [
					'**/*.php',
					'!vendor/**',
					'!dev-lib/*'
				],
				overwrite: true,
				replacements: [ {
					from: /Version:(\s*?)[a-zA-Z0-9\.\-\+]+$/m,
					to: 'Version:$1' + pkg.version
				}, {
					from: /@version(\s*?)[a-zA-Z0-9\.\-\+]+$/m,
					to: '@version$1' + pkg.version
				}, {
					from: /@since(.*?)NEXT/mg,
					to: '@since$1' + pkg.version
				}, {
					from: /VERSION(\s*?)=(\s*?['"])[a-zA-Z0-9\.\-\+]+/mg,
					to: 'VERSION$1=$2' + pkg.version
				} ]
			},
			version_readme: {
				src: 'readme.*',
				overwrite: true,
				replacements: [ {
					from: /^(\*\*|)Stable tag:(\*\*|)(\s*?)[a-zA-Z0-9.-]+(\s*?)$/mi,
					to: '$1Stable tag:$2$3<%= pkg.version %>$4'
				} ]
			}
		},

		// Deploys a git Repo to the WordPress SVN repo.
		wp_deploy: {
			deploy: {
				options: {
					plugin_slug: pkg.name,
					build_dir: BUILD_DIR,
					assets_dir: 'assets',
					plugin_main_file: 'godaddy-email-marketing.php'
				}
			}
		},

		// Clean up.
		clean: {
			po: {
				src: [
					'languages/*.po~'
				]
			},
			build: {
				src: [
					'build'
				]
			}
		}

	} );

	grunt.registerTask( 'default', [ 'cssmin', 'uglify'	] );
	grunt.registerTask( 'version', ['replace'] );
	grunt.registerTask( 'update_translation', [ 'checktextdomain', 'pot', 'po2mo', 'clean:po' ] );
	grunt.registerTask( 'develop', [ 'default', 'update_translation' ] );
	grunt.registerTask( 'deploy', [ 'copy', 'wp_deploy', 'clean:build' ] );

};
