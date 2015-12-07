/**
 * Gulp file.
 */

// Get plugings.
var gulp = require('gulp');
var jshint = require('gulp-jshint');
var elixir = require('laravel-elixir');


// Checks javascript scripts for errors using JSHint.
gulp.task('jshint', function() {
    return gulp.src('resources/assets/js/*.js')
        .pipe(jshint())
        .pipe(jshint.reporter('default'));
});

// Use Laravel's Elixir to create unique references to our scripts,
// so we can use them in our templates.
elixir(function(mix) {

    // Refreshes the browser when assets are changed (used with 'watch' task).
    // mix.browserSync({
    //     proxy: 'dinkomo.vagrant'
    // });

    // Trigger PHPUnit tests.
    // mix.phpUnit();

    // Trigger PHPSpec tests.
    // mix.phpSpec();

    // Copy over some assets to the public folder.
    mix.copy('bower_components/font-awesome/fonts', 'public/assets/fonts');
    mix.copy('resources/assets/fonts', 'public/assets/fonts');

    // Build app stylesheet. Paths are relative to 'resources/assets/sass'.
    mix.sass('main.scss', 'resources/assets/build/app.css');

    // Combine stylesheets. Paths are relative to 'resources/assets/css'.
    mix.styles([
        '../../../bower_components/bootstrap/dist/css/bootstrap.min.css',
        '../../../bower_components/font-awesome/css/font-awesome.min.css',
        'fonts.css',
        '../build/app.css'
    ], 'public/assets/css/dinkomo.css');

    // Compile app scripts into retular javascript.
    mix.task('jshint');
    mix.babel([
        'app.js',
        'dialogs.js',
        'forms.js',
        'resources.js',
    ], 'resources/assets/build/compiled.js');

    // Combine scripts. Paths are relative to 'resources/assets/js'.
    mix.scripts([
        '../../../bower_components/microplugin/src/microplugin.js',
        '../../../bower_components/jquery/dist/jquery.min.js',
        // '../../../bower_components/jquery-ui/jquery-ui.min.js',
        '../../../bower_components/bootstrap/dist/js/bootstrap.min.js',
        '../../../bower_components/selectize/dist/js/selectize.min.js',
        '../build/compiled.js'
    ], 'public/assets/js/dinkomo.js');

    // Versioning. Fetched paths are relative to 'public', while output paths are relative to
    // fetched path.
    mix.version(['assets/css/dinkomo.css', 'assets/js/dinkomo.js']);
});
