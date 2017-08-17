
// TODO: use yargs to pass arguments to gulp
// TODO: combine with gulpif

// Required gulp plugins
var gulp          = require('gulp'),
    del           = require('del'),
    combine       = require('gulp-concat'),
    jshint        = require('gulp-jshint'),
    eslint        = require('gulp-eslint'),
    babel         = require('gulp-babel'),
    minifyJS      = require('gulp-uglify'),
    sass          = require('gulp-sass'),
    minifyCSS     = require('gulp-clean-css'),
    stripCssComments = require('gulp-strip-css-comments'),
    sourcemaps    = require('gulp-sourcemaps'),
    rev           = require('gulp-rev');

//
// CSS
//////////////////////

// Paths to stylesheets.
var css = {
  src: {
    app: ['resources/assets/sass/app.scss'],
    user: ['resources/assets/sass/user.scss'],
  },
  prodDependencies: {
    app: ['node_modules/font-awesome/css/font-awesome.min.css'],
    user: [],
  }
};

// Removes existing stylesheets.
gulp.task('clear-css', function(done) {
  del('public/assets/css/*.css');
  done();
});

// Compiles app SASS files to CSS.
gulp.task('app-sass', function() {
  return gulp.src(css.src.app)
    .pipe(sourcemaps.init())
      .pipe(sass().on('error', sass.logError))
    .pipe(sourcemaps.write())
    .pipe(gulp.dest('resources/assets/build/css/app'));
});

// Compiles user SASS files to CSS.
gulp.task('user-sass', function() {
  return gulp.src(css.src.user)
    .pipe(sourcemaps.init())
      .pipe(sass().on('error', sass.logError))
    .pipe(sourcemaps.write())
    .pipe(gulp.dest('resources/assets/build/css/user'));
});

// Combines app CSS files for production.
gulp.task('app-css', ['app-sass'], function() {
  return gulp.src(css.prodDependencies.app.concat('resources/assets/build/css/app/app.css'))
    .pipe(stripCssComments())
    .pipe(sourcemaps.init())                    // Minified CSS files
      .pipe(minifyCSS())
      .pipe(combine('app.css'))
      .pipe(rev())
      .pipe(gulp.dest('public/assets/css'))
    .pipe(sourcemaps.write('./'))               // CSS source maps
    .pipe(rev.manifest())
    .pipe(gulp.dest('resources/assets/build/css/app'));
});

// Combines user CSS files for production.
gulp.task('user-css', ['user-sass'], function() {
  return gulp.src(css.prodDependencies.user.concat('resources/assets/build/css/user/user.css'))
    .pipe(stripCssComments())
    .pipe(sourcemaps.init())                    // Minified CSS files
      .pipe(minifyCSS())
      .pipe(combine('user.css'))
      .pipe(rev())
      .pipe(gulp.dest('public/assets/css'))
    .pipe(sourcemaps.write('./'))               // CSS source maps
    .pipe(rev.manifest())
    .pipe(gulp.dest('resources/assets/build/css/user'));
});

//
// JS
// TODO: https://jonsuh.com/blog/integrating-react-with-gulp/
//////////////////////

// Paths to javascript files.
var js = {
  src: {
    app: ['resources/assets/js/app/*.js', 'resources/assets/js/app/**/*.js'],
    user: ['resources/assets/js/user/*.js', 'resources/assets/js/user/**/*.js'],
    admin: ['resources/assets/js/admin/*.js', 'resources/assets/js/admin/**/*.js'],
  },
  devDependencies: {
    app: ['node_modules/vue/dist/vue.js'],
    user: ['node_modules/vue-resource/dist/vue-resource.js'],
    admin: [],
  },
  prodDependencies: {
    app: ['node_modules/vue/dist/vue.min.js'],
    user: ['node_modules/vue-resource/dist/vue-resource.min.js'],
    admin: [],
  }
};

// Removes existing javascript files.
gulp.task('clear-js', function(done) {
  del('public/assets/js/*.js');
  done();
});

// Combines app source files.
gulp.task('combine-app-src', function() {
  return gulp.src(js.src.app)

    // ES6 Linter
    .pipe(eslint({
      baseConfig: {
        ecmaFeatures: {
           jsx: true
         }
      }
    }))
    .pipe(eslint.format())
    .pipe(eslint.failAfterError())

    // ES5 Linter
    // .pipe(jshint())
    // .pipe(jshint.reporter('jshint-stylish'))
    // .pipe(jshint.reporter('fail'))

    // Combine & minify
    .pipe(sourcemaps.init())
      .pipe(minifyJS())
      .pipe(combine('source.js'))
    .pipe(sourcemaps.write('./'))

    // Finish build
    .pipe(gulp.dest('resources/assets/build/js/app'));
});

// Combines user source files.
gulp.task('combine-user-src', function() {
  return gulp.src(js.src.user)

    // JS Hint
    .pipe(jshint())
    .pipe(jshint.reporter('jshint-stylish'))
    .pipe(jshint.reporter('fail'))

    // Combine & minify
    .pipe(sourcemaps.init())
      .pipe(minifyJS())
      .pipe(combine('source.js'))
    .pipe(sourcemaps.write('./'))

    // Finish build
    .pipe(gulp.dest('resources/assets/build/js/user'));
});

// Converts ES6 javascript to regular javascript.
gulp.task('babel', ['eslint'], function() {
    return gulp.src(js.src.app)
        .pipe(sourcemaps.init())
            .pipe(babel({presets: ['es2015']}))
            .pipe(combine('app.js'))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('resources/assets/build/js'));
});

// Builds app javascript files for production.
gulp.task('app-js', ['combine-app-src'], function() {
  return gulp.src(js.prodDependencies.app.concat('resources/assets/build/js/app/source.js'))
    .pipe(sourcemaps.init())
      .pipe(combine('app.js'))
      .pipe(rev())
      .pipe(gulp.dest('public/assets/js'))
    .pipe(sourcemaps.write('./'))
    .pipe(rev.manifest())
    .pipe(gulp.dest('resources/assets/build/js/app'));
});

// Builds app javascript files for development.
gulp.task('app-js-dev', ['combine-app-src'], function() {
  return gulp.src(js.devDependencies.app.concat('resources/assets/build/js/app/source.js'))
    .pipe(sourcemaps.init())
      .pipe(combine('app.js'))
      .pipe(rev())
      .pipe(gulp.dest('public/assets/js'))
    .pipe(sourcemaps.write('./'))
    .pipe(rev.manifest())
    .pipe(gulp.dest('resources/assets/build/js/app'));
});

// Builds user javascript files for production.
gulp.task('user-js', ['combine-user-src'], function() {
  return gulp.src(js.prodDependencies.user.concat('resources/assets/build/js/user/source.js'))
    .pipe(sourcemaps.init())
      .pipe(combine('user.js'))
      .pipe(rev())
      .pipe(gulp.dest('public/assets/js'))
    .pipe(sourcemaps.write('./'))
    .pipe(rev.manifest())
    .pipe(gulp.dest('resources/assets/build/js/user'));
});

// Builds user javascript files for development.
gulp.task('user-js-dev', ['combine-user-src'], function() {
  return gulp.src(js.devDependencies.user.concat('resources/assets/build/js/user/source.js'))
    .pipe(sourcemaps.init())
      .pipe(combine('user.js'))
      .pipe(rev())
      .pipe(gulp.dest('public/assets/js'))
    .pipe(sourcemaps.write('./'))
    .pipe(rev.manifest())
    .pipe(gulp.dest('resources/assets/build/js/user'));
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
// gulp.task('watch', function() {
//     gulp.watch(['resources/assets/sass/**/*.scss'].concat('gulpfile.js'), ['css']);
//     gulp.watch(js.dev.concat('gulpfile.js'), ['js']);
// });

// Production build tasks.
gulp.task('css', ['clear-css', 'app-css', 'user-css']);
gulp.task('js', ['clear-js', 'app-js', 'user-js']);
gulp.task('default', ['css', 'js']);

// Development build tasks.
gulp.task('css-dev', ['clear-css', 'app-css', 'user-css']);
gulp.task('js-dev', ['clear-js', 'app-js-dev', 'user-js-dev']);
gulp.task('dev', ['css-dev', 'js-dev']);
