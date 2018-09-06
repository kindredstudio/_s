/* eslint-disable */
var gulp = require('gulp');
var browserSync = require('browser-sync').create();
var reload = browserSync.reload;
var babel = require('gulp-babel');
var autoprefixer = require('autoprefixer');
var concat = require('gulp-concat');
var imageMin = require('gulp-imagemin');
var postCSS = require('gulp-postcss');
var cssnano = require('cssnano');
var notify = require('gulp-notify');
var plumber = require('gulp-plumber');
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');
var uglify = require('gulp-uglify');
var mqpacker = require('css-mqpacker');
var sortCSSmq = require('sort-css-media-queries');

var paths = {
	domain: 'found-design.local',
	styles: './assets/sass/**/*.scss',
	scripts: './assets/js/scripts.js',
	dist: './assets/dist',
};

var prefixSettings = ['last 2 versions', '>1%', 'safari 5', 'ie 8', 'ie 9', 'opera 12.1'];

gulp.task('bs', function () {
	browserSync.init({
		proxy: paths.domain,
	});
});

gulp.task('styles', function () {
	var plugins = [
		autoprefixer({
			browsers: prefixSettings
		}),
		cssnano(),
		mqpacker({
			sort: sortCSSmq.desktopFirst
		})
	];
	return gulp
		.src(paths.styles)
		.pipe(
			plumber({
				errorHandler: notify.onError('Error: <%= error.message %>'),
			})
		)
		.pipe(sourcemaps.init())
		.pipe(sass())
		.pipe(postCSS(plugins))
		.pipe(concat('style.css'))
		.pipe(sourcemaps.write())
		.pipe(gulp.dest(paths.dist))
		.pipe(reload({
			stream: true
		}));
});

gulp.task('scripts', function () {
	return gulp
		.src(paths.scripts)
		.pipe(
			plumber({
				errorHandler: notify.onError('Error: <%= error.message %>'),
			})
		)
		.pipe(
			babel({
				presets: [
					[
						'env',
					],
				],
			})
		)
		.pipe(concat('scripts.min.js'))
		.pipe(uglify())
		.pipe(gulp.dest(paths.dist))
		.pipe(reload({
			stream: true
		}));
});

gulp.task('images', function () {
	return gulp
		.src('./assets/img/**/*')
		.pipe(imageMin({
			progressive: true,
			optimizationLevel: 3, // 0-7 low-high
			interlaced: true,
		}))
		.pipe(gulp.dest('./assets/img'));
});

// configure which files to watch and what tasks to use on file changes
gulp.task('watch', function () {
	gulp.watch(paths.styles, ['styles']);
	gulp.watch(paths.scripts, ['scripts']);
	gulp.watch('./**/*.php', reload);
	gulp.watch(paths.views, reload);
});

gulp.task('default', ['styles', 'scripts', 'images', 'bs', 'watch']);