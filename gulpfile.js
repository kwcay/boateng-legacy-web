
// Required gulp plugins
var gulp = require('gulp'),
    del = require('del'),
    combine = require('gulp-concat'),

    jshint = require('gulp-jshint'),
    minifyJS = require('gulp-uglify'),
    templateCache = require('gulp-angular-templatecache'),

    minifyCSS = require('gulp-minify-css'),
    stripCssComments = require('gulp-strip-css-comments'),

    sourcemaps = require('gulp-sourcemaps'),
    rev = require('gulp-rev');

//
// CSS
//////////////////////

// Paths to stylesheets.
var css = {
    dev: ['resources/assets/sass/main.scss'],
    dependencies: [
        'node_modules/bootstrap/dist/css/bootstrap.min.css',
        'node_modules/font-awesome/css/font-awesome.min.css'
    ]
};

// Removes existing stylesheets.
gulp.task('remove-css', function(done) {
    del('public/assets/*.css');
    done();
});

// Compiles SASS files to CSS.
gulp.task('compile-css', function() {

    // TODO: compile 'main.scss' to 'resources/assets/build/compiled.css'

});

// Combines and minifies dev CSS files.
gulp.task('minify-css', ['compile-css'], function() {
    return gulp.src(css.dev)
        .pipe(stripCssComments())
        .pipe(sourcemaps.init())
            .pipe(minifyCSS())
            .pipe(combine('learn.css'))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('assets/css/build'));
});

// Combines all CSS files.
gulp.task('css', ['remove-css', 'minify-css'], function() {
    return gulp.src(css.dependencies.concat('assets/css/build/learn.css'))
        .pipe(sourcemaps.init())
            .pipe(combine('learn.css'))
            .pipe(rev())
            .pipe(gulp.dest('public/assets'))
        .pipe(sourcemaps.write('./'))
        .pipe(rev.manifest())
        .pipe(gulp.dest('assets/css'));
});

//
// JS
//////////////////////

// Paths to javascript files.
var js = {
    dev: ['resources/assets/js/*.js'],
    dependencies: [
        'node_modules/jquery/dist/jquery.min.js',
        'node_modules/bootstrap/dist/js/bootstrap.min.js',
        'node_modules/angular/angular.min.js',
        'node_modules/angular-route/angular-route.min.js',
        'node_modules/ngstorage/ngStorage.min.js'
    ]
};

// Removes existing javascript files.
gulp.task('remove-js', function(done) {
    del('public/assets/js/*.js');
    done();
});

// Checks javascript files for syntax errors.
gulp.task('lint-js', function() {
    return gulp.src(js.dev)
        .pipe(jshint())
        .pipe(jshint.reporter('jshint-stylish'))
        .pipe(jshint.reporter('fail'));
});

// Combines and minifies dev javascript files.
gulp.task('minify-js', ['lint-js'], function() {
    return gulp.src(js.dev)
        .pipe(sourcemaps.init())
            .pipe(minifyJS())
            .pipe(combine('dinkomo.js'))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('resources/assets/build'));
});

// Combines all javascript files.
gulp.task('js', ['remove-js', 'minify-js'], function() {
    return gulp.src(js.dependencies.concat('resources/assets/build/dinkomo.js'))
        .pipe(sourcemaps.init())
            .pipe(combine('build.js'))
            .pipe(rev())
            .pipe(gulp.dest('public/assets/js'))
        .pipe(sourcemaps.write('./'))
        .pipe(rev.manifest())
        .pipe(gulp.dest('resources/assets/js'));
});

//
// Other
//////////////////////

// Copies some assets over to the public folder.
gulp.task('copy', function() {

    // TODO: copy 'bower_components/font-awesome/fonts' to 'public/assets/fonts'
    // TODO: copy 'resources/assets/fonts' to 'public/assets/fonts'
});

// Reruns the tasks when a file changes.
gulp.task('watch', function() {
    gulp.watch(css.dev, ['css']);
    gulp.watch(js.dev, ['js']);
});

gulp.task('default', ['css', 'js']);







// Use Laravel's Elixir to create unique references to our scripts,
// so we can use them in our templates.
elixir(function(mix) {

    // Combine stylesheets. Paths are relative to 'resources/assets/css'.
    mix.styles([
        '../../../bower_components/bootstrap/dist/css/bootstrap.min.css',
        '../../../bower_components/font-awesome/css/font-awesome.min.css',
        'fonts.css',
        '../build/app.css'
    ], 'public/assets/css/dinkomo.css');

    // Compile app scripts into retular javascript.
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
