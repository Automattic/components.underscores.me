var gulp = require('gulp');
var autoprefixer = require('gulp-autoprefixer');
var csscomb = require('gulp-csscomb');
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');

gulp.task('styles', function() {
	return gulp.src('assets/stylesheets/style.scss')
		.pipe(sourcemaps.init())
		.pipe(sass().on('error', sass.logError))
		.pipe(autoprefixer({
      browsers: ['last 2 versions', 'ie >= 9'],
      cascade: false
    }))
		.pipe(sourcemaps.write('./'))
		.pipe(csscomb())
		.on('error', function(err) {
			console.error('Error building styles:', err.message);
		})
		.pipe(gulp.dest('./'));
});

// Watch files for changes
gulp.task('watch', ['build'], function() {
	gulp.watch('assets/stylesheets/**/*.scss', ['styles']);
});

gulp.task('build', ['styles']);
gulp.task('default', ['styles', 'scripts', 'images', 'watch']);
