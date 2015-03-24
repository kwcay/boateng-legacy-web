/**
 * Gulp file.
 */

// Get plugings.
var gulp = require('gulp');
var jshint = require('gulp-jshint');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var rename = require('gulp-rename');
var elixir = require('laravel-elixir');

// ...
elixir.extend('checks', function() {
    
    // Check javascript scripts for errors using Lint,
    // then concatenate & minify them.
    gulp.task('checks', function() {
        return gulp.src('resources/assets/js/*.js')
            .pipe(jshint())
            .pipe(jshint.reporter('default'));
    });
    
    // Register a watcher to monitor this task.
    this.registerWatcher('checks', 'resources/assets/js/*.js');

    return this.queueTask('checks');
});

// Check javascript scripts for errors using Lint.
elixir.extend('mini', function() {
    
    gulp.task('mini', function() {
        return gulp.src('resources/assets/js/*.js')
            .pipe(concat('s-ucp.js'))
            .pipe(gulp.dest('public/assets'))
            .pipe(rename('s.js'))
            .pipe(uglify())
            .pipe(gulp.dest('public/assets'));
    });

    return this.queueTask('mini');
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
    // mix.styles([
        // 'base.css',
        // 'dialogs.css',
        // 'footer.css',
        // 'forms.css',
        // 'selectize.css',
    // ], 'public/assets/everything.css', 'resources/assets/css/');
    mix.stylesIn('resources/assets/css', 'public/assets/styles.css');
    
    // Combine scripts
    mix.scriptsIn('resources/assets/js', 'public/assets/scripts.js');
    //mix.mini();
    
    // Create a unique filename for each script version.
    // mix.version([
        // 'public/assets/styles.css',
        // 'public/assets/scripts.js'
    // ]);
    // mix.version('public/assets/s.js');
    mix.version(['public/assets/styles.css', 'public/assets/scripts.js']);

});
