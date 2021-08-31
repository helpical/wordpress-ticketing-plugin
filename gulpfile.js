var gulp = require("gulp");
var sass = require("gulp-sass");
var autoprefixer = require('gulp-autoprefixer');
var postcss = require('gulp-postcss');
var uglify = require('gulp-uglify');
var babelify = require('babelify');
var browserify = require('browserify');
var source = require('vinyl-source-stream');
var buffer = require('vinyl-buffer');

gulp.task('js', function() {
    return browserify({
            entries: ['src/js/script.js']
        })
        .transform(babelify.configure({ presets: ['@babel/preset-env'] }))
        .bundle()
        .pipe(source('script.js'))
        .pipe(buffer())
        .pipe(uglify())
        .pipe(gulp.dest('./assets/js'));
});
gulp.task('scss', function() {
    return gulp.src('src/scss/style.scss')
        .pipe(sass({ outputStyle: 'compressed' })
            .on("error", sass.logError))
        .pipe(postcss([autoprefixer]))
        .pipe(gulp.dest('./assets/css'));
});

gulp.task('watch', function() {
    gulp.watch("src/**/*.scss", gulp.series('scss', 'reload'));
    gulp.watch("src/**/*.js", gulp.series('js', 'reload'));
});