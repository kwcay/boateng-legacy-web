
// Required gulp plugins
var gulp = require('gulp'),
    del = require('del'),
    combine = require('gulp-concat'),

    jshint = require('gulp-jshint'),
    eslint = require('gulp-eslint'),
    babel = require('gulp-babel'),
    minifyJS = require('gulp-uglify'),
    templateCache = require('gulp-angular-templatecache'),

    sass = require('gulp-sass'),
    minifyCSS = require('gulp-minify-css'),
    stripCssComments = require('gulp-strip-css-comments'),

    sourcemaps = require('gulp-sourcemaps'),
    rev = require('gulp-rev');

//
// CSS
//////////////////////

// Paths to stylesheets.
var css = {
    dev: 'resources/assets/sass/dinkomo.scss',
    dependencies: [
        'bower_components/bootstrap/dist/css/bootstrap.min.css',
        'bower_components/font-awesome/css/font-awesome.min.css',
        'resources/assets/css/fonts.min.css'
    ]
};

// Removes existing stylesheets.
gulp.task('clear-css', function(done) {
    del('public/assets/css/*.css');
    done();
});

// Compiles SASS files to CSS.
gulp.task('sass', function() {
    return gulp.src(css.dev)
        .pipe(sourcemaps.init())
            // .pipe(sass().on('error', sass.logError))
            .pipe(sass())
        .pipe(sourcemaps.write())
        .pipe(gulp.dest('resources/assets/build/css'));

});

// Combines all CSS files.
gulp.task('css', ['clear-css', 'sass'], function() {
    return gulp.src(css.dependencies.concat('resources/assets/build/css/dinkomo.css'))
        .pipe(stripCssComments())
        .pipe(sourcemaps.init())
            .pipe(minifyCSS())
            .pipe(combine('all.css'))
            .pipe(rev())
            .pipe(gulp.dest('public/assets/css'))
        .pipe(sourcemaps.write('./'))
        .pipe(rev.manifest())
        .pipe(gulp.dest('resources/assets/build/css'));
});

//
// JS
//////////////////////

// Paths to javascript files.
var js = {
    dev: ['resources/assets/js/*.js'],
    dependencies: [
        'bower_components/microplugin/src/microplugin.js',
        'bower_components/jquery/dist/jquery.min.js',
        'bower_components/jquery-ui/jquery-ui.min.js',
        'bower_components/bootstrap/dist/js/bootstrap.min.js',
        'bower_components/sifter/sifter.min.js',
        // 'bower_components/selectize/dist/js/selectize.min.js'
        'bower_components/selectize/dist/js/selectize.js'
    ]
};

// Removes existing javascript files.
gulp.task('clear-js', function(done) {
    del('public/assets/js/*.js');
    done();
});

// Checks javascript files for syntax errors.
gulp.task('jshint', function() {
    return gulp.src(js.dev)
        .pipe(jshint())
        .pipe(jshint.reporter('jshint-stylish'))
        .pipe(jshint.reporter('fail'));
});

// Checks javascript files for syntax errors.
gulp.task('eslint', function() {
    return gulp.src(js.dev)
        .pipe(eslint())
        .pipe(eslint.format())
        .pipe(eslint.failAfterError());
});

// Combines and minifies dev javascript files.
gulp.task('minify-js', ['jshint'], function() {
    return gulp.src(js.dev)
        .pipe(sourcemaps.init())
            .pipe(minifyJS())
            .pipe(combine('dinkomo.js'))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('resources/assets/build/js'));
});

// Converts ES6 javascript to regular javascript.
gulp.task('babel', ['eslint'], function() {
    return gulp.src(js.dev)
        .pipe(sourcemaps.init())
            .pipe(babel({presets: ['es2015']}))
            .pipe(combine('dinkomo.js'))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('resources/assets/build/js'));
});

// Combines and minifies all javascript files.
gulp.task('js', ['clear-js', 'minify-js'], function() {
    return gulp.src(js.dependencies.concat('resources/assets/build/js/dinkomo.js'))
        .pipe(sourcemaps.init())
            .pipe(combine('all.js'))
            .pipe(rev())
            .pipe(gulp.dest('public/assets/js'))
        .pipe(sourcemaps.write('./'))
        .pipe(rev.manifest())
        .pipe(gulp.dest('resources/assets/build/js'));
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
    gulp.watch(['resources/assets/sass/**/*.scss'].concat('gulpfile.js'), ['css']);
    gulp.watch(js.dev.concat('gulpfile.js'), ['js']);
});

// Default task.
gulp.task('default', ['css', 'js']);
