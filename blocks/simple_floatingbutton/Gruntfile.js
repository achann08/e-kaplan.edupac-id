module.exports = function(grunt) {
  // Project configuration.
  grunt.initConfig({
      pkg: grunt.file.readJSON('package.json'),
      clean: {
          build: ['amd/build', 'css/build']
      },
      jshint: {
          options: {
              esversion: 6,
              asi: true,
              laxcomma: true,
              globals: {
                  jQuery: true,
                  $: true,
                  console: true,
                  module: true
              }
          },
          all: ['amd/src/**/*.js']
      },
      uglify: {
          options: {
              banner: '/*! <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd") %> */\n'
          },
          build: {
              files: [{
                  expand: true,
                  cwd: 'amd/src',
                  src: '**/*.js',
                  dest: 'amd/build',
                  ext: '.min.js'
              }]
          }
      },
      cssmin: {
          target: {
              files: [{
                  expand: true,
                  cwd: 'css/src',
                  src: ['*.css', '!*.min.css'],
                  dest: 'css/build',
                  ext: '.min.css'
              }]
          }
      }
  });

  // Load the plugins that provide the tasks.
  grunt.loadNpmTasks('grunt-contrib-clean');
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-cssmin');

  // Default task(s).
  grunt.registerTask('default', ['clean', 'jshint', 'uglify', 'cssmin']);
};