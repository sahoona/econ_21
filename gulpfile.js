const gulp = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const concat = require('gulp-concat');

// Define paths
const paths = {
  styles: {
    src: 'assets/css/**/*.scss',
    dest: 'dist/css'
  }
};

// Compile sass and concatenate to a single file
function styles() {
  return gulp.src('assets/css/main.scss') // main.scss를 진입점으로 사용
    .pipe(sass().on('error', sass.logError))
    .pipe(concat('bundle.css'))
    .pipe(gulp.dest(paths.styles.dest));
}

// Watch for changes
function watch() {
  gulp.watch(paths.styles.src, styles);
}

// Define complex tasks
const build = gulp.series(styles);
const dev = gulp.series(styles, watch);

// Export tasks
exports.styles = styles;
exports.watch = watch;
exports.build = build;
exports.default = dev;
