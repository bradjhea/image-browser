module.exports = function (grunt) {

    // Project configuration.
    grunt.initConfig({
        concat: {
            dist: {
                src: ['standard/js/jquery.scrollview.js', 'standard/js/imageBrowser.js'],
                dest: 'min/js/script.js',
            },
        },
        uglify: {
            my_target: {
                files: {
                    'min/js/script.js': ['standard/bootstrap/javascripts/jquery.min.js', 'standard/bootstrap/javascripts/bootstrap.min.js', 'min/js/script.js'],
                }
            }
        },
        cssmin: {
            target: {
                files: {
                    'standard/bootstrap/stylesheets/style.min.css': ['standard/bootstrap/stylesheets/bootstrap.css']
                }
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.registerTask('default', ['concat', 'uglify', 'cssmin']);

};