module.exports = function( grunt ) {
	grunt.initConfig({
		pkg: grunt.file.readJSON( 'package.json' ),

		sass: {
			options: {
				implementation: require( 'sass' ), // Use the locally installed 'sass' package
				sourceMap: true,
				style: 'expanded'
			},
			dist: {
				files: {
					'assets/css/style.css': 'assets/scss/style.scss'
				}
			}
		},

		cssmin: {
			target: {
			files: [ {
				expand: true,
				cwd: 'assets/css',
				src: [ '*.css', '!*.min.css' ],
				dest: 'assets/css',
				ext: '.min.css'
			} ]
			}
		},

		uglify: {
			options: {
			mangle: false
			},
			my_target: {
				files: [ {
					expand: true,
					cwd: 'assets/js',
					src: [ '*.js', '!*.min.js' ],
					dest: 'assets/js',
					ext: '.min.js'
				} ]
			}
		},

		watch: {
			css: {
				files: 'assets/scss/**/*.scss',
				tasks: [ 'sass', 'cssmin' ]
			},
			js: {
				files: [ 'assets/js/**/*.js', '!assets/js/**/*.min.js' ],
				tasks: [ 'uglify' ]
			}
		}
	});

	grunt.loadNpmTasks( 'grunt-contrib-sass' );
	grunt.loadNpmTasks( 'grunt-contrib-watch' );
	grunt.loadNpmTasks( 'grunt-contrib-uglify' );
	grunt.loadNpmTasks( 'grunt-contrib-cssmin' );

	grunt.registerTask( 'default', [ 'watch' ]);
};
