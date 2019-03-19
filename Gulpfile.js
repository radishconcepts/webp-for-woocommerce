'use strict';

const gulp = require('gulp');
const babel = require('gulp-babel');
const uglify = require('gulp-uglify-es').default;
const concat = require('gulp-concat');
const fs = require('fs');
const rename = require('gulp-rename');
const notify = require('gulp-notify');
const plumber = require('gulp-plumber');
const sass = require('gulp-sass');
const cssnano = require('gulp-cssnano');
const sourcemaps = require('gulp-sourcemaps');
const autoprefixer = require('gulp-autoprefixer');

// Hooks Symlink
gulp.task('create-hooks-symlink', function (done) {
    fs.symlink('../../.hooks/pre-commit', './.git/hooks/pre-commit', done);
    fs.chmod('./.git/hooks/pre-commit', '755', done);
});

gulp.task('init', gulp.series(['create-hooks-symlink']));

/* Scripts task */
gulp.task('scripts', function () {
    return gulp.src('_source/js/*.js')
        .pipe(babel({
            presets: ['@babel/env']
        }))
        .pipe(concat('webp-for-woocommerce.js'))
        .pipe(gulp.dest('./assets/js/'))
        .pipe(rename({suffix: '.min'}))
        .pipe(uglify().on('error', handleErrors))
        .pipe(gulp.dest('./assets/js/'));
});

/* Sass task */
gulp.task('sass', () => {
    return gulp.src(['_source/scss/*.scss', '_source/scss/**/*.scss'])
        .pipe(plumber())
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(autoprefixer('last 2 version', 'ie 9', 'ios 6', 'android 4'))
        .pipe(sourcemaps.write())
        .pipe(concat('webp-for-woocommerce.css'))
        .pipe(gulp.dest('./assets/css/'))
        .pipe(rename({suffix: '.min'}))
        .pipe(cssnano({
            zindex: false
        }))
        .pipe(gulp.dest('./assets/css/'))
});

gulp.task('watch', () => {
    gulp.watch('_source/js/*.js', gulp.series(['scripts']));
    gulp.watch(['_source/scss/*.scss', '_source/scss/**/*.scss'], gulp.series(['sass']));
});

gulp.task('default', gulp.series(['scripts', 'sass', 'watch']));

function handleErrors() {
    const args = Array.prototype.slice.call(arguments);

    // Send error to notification center with gulp-notify
    notify.onError({
        title: "Compile Error",
        message: "<%= error.message %>"
    }).apply(this, args);

    this.emit('end');
}