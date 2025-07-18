const { styles } = require('./gulpfile');
const { series } = require('gulp');

// This is a workaround to run the gulp task programmatically

const buildTask = series(styles);

buildTask((err) => {
  if (err) {
    console.error('Build failed:', err);
    process.exit(1);
  }
  console.log('Build completed successfully.');
});
