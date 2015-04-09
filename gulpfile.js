/**
 * Gulp file.
 */

// Get plugings.
var gulp = require('gulp');
var jshint = require('gulp-jshint');
var elixir = require('laravel-elixir');

// ...
elixir.extend('checks', function() {

    // Check javascript scripts for errors using JSHint.
    gulp.task('checks', function() {
        return gulp.src('resources/assets/js/*.js')
            .pipe(jshint())
            .pipe(jshint.reporter('default'));
    });

    // Register a watcher to monitor this task.
    this.registerWatcher('checks', 'resources/assets/js/*.js');

    return this.queueTask('checks');
});

// Use Laravel's Elixir to create unique references to our scripts,
// so we can use them in our templates.
elixir(function(mix) {
    
    // Trigger PHPUnit tests.
    // mix.phpUnit();
    
    // Trigger PHPSpec tests.
    // mix.phpSpec();

    //mix.sass('app.scss');
    
    // Combine stylesheets.
    mix.stylesIn('resources/assets/css', 'public/assets/styles.css');
    
    // Combine scripts
    mix.checks();
    mix.scriptsIn('resources/assets/js', 'public/assets/scripts.js');
    
    // Create a unique filename for each script version.
    mix.version(['public/assets/styles.css', 'public/assets/scripts.js']);

});
