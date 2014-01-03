module.exports = function(grunt) {
  // Project configuration.
  grunt.initConfig({
    sass: {
      dist: {
        files: {
          'style.css': 'style.scss',
        }
      }
    },

    watch: {
      scripts: {
        files: ['**/*.scss'],
        tasks: ['sass'],
        options: {
          spawn: false,
          livereload: true
        },
      },
    },
  });

  grunt.loadNpmTasks('grunt-contrib-sass');
  grunt.loadNpmTasks('grunt-contrib-watch');

  // Default task(s).
  grunt.registerTask('default', ['sass', 'watch']);

};