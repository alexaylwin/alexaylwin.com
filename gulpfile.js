// Sass configuration
var gulp = require('gulp');
var compass = require('gulp-compass');
//var livereload = require('gulp-livereload');
var ts = require('gulp-typescript');
var tsProject = ts.createProject('./tsconfig.json');
var browserSync = require('browser-sync').create();

gulp.task('compass', function() {
    gulp.src('./sass/*.scss')
    .pipe(compass({
        config_file: './config.rb',
        css: 'stylesheets',
        sass: 'sass'
        }))
        .pipe(gulp.dest('/'));
});

gulp.task('typescript', function() {
    return gulp.src([
      "./typescript/**/*.ts"  
    ])
    .pipe(ts(tsProject))
    .js.pipe(gulp.dest("./javascript"));
});

gulp.task('watch', function() {
    browserSync.init({
        server: "."
    });
    //livereload.listen();
    gulp.watch('./sass/*.scss', ['compass']);
    gulp.watch('./typescript/**/*.ts', ['typescript']);
    gulp.watch("*.htm").on('change', browserSync.reload);
    gulp.watch("./stylesheets/*").on('change', browserSync.reload);
    gulp.watch("./javascript/*").on('change', browserSync.reload);
});