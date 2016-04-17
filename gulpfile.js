// Sass configuration
var gulp = require('gulp');
var compass = require('gulp-compass');
var livereload = require('gulp-livereload');
var ts = require('gulp-typescript');
var tsProject = ts.createProject('./tsconfig.json');

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
    livereload.listen();
    gulp.watch('./sass/*.scss', ['compass']);
    gulp.watch('./typescript/**/*.ts', ['typescript']);
});