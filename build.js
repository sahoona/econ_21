const fs = require('fs');
const path = require('path');

// Define absolute paths
const basePath = __dirname;

const bundles = {
  core: {
    files: [
      path.resolve(basePath, 'assets/css/components/variables.css'),
      path.resolve(basePath, 'assets/css/components/fonts.css'),
      path.resolve(basePath, 'assets/css/main.css')
    ],
    output: 'core.bundle.css'
  },
  layout: {
    files: [
      path.resolve(basePath, 'assets/css/layout.css'),
      path.resolve(basePath, 'assets/css/components/layout.css'),
      path.resolve(basePath, 'assets/css/components/header.css'),
      path.resolve(basePath, 'assets/css/components/sidebar.css'),
      path.resolve(basePath, 'assets/css/components/responsive.css')
    ],
    output: 'layout.bundle.css'
  },
  components: {
    files: [
      path.resolve(basePath, 'assets/css/components/dark_mode.css'),
      path.resolve(basePath, 'assets/css/components/language-switcher.css'),
      path.resolve(basePath, 'assets/css/components/language-switcher-partial.css'),
      path.resolve(basePath, 'assets/css/components/back-to-top.css'),
      path.resolve(basePath, 'components/ads/ads.css')
    ],
    output: 'components.bundle.css'
  },
  content: {
    files: [
        path.resolve(basePath, 'assets/css/components/content.css'),
        path.resolve(basePath, 'assets/css/components/post-navigation.css')
    ],
    output: 'content.bundle.css'
  },
  conditional: {
    files: [
        path.resolve(basePath, 'assets/css/components/table-of-contents.css'),
        path.resolve(basePath, 'assets/css/components/series.css'),
        path.resolve(basePath, 'assets/css/components/comments.css'),
        path.resolve(basePath, 'yarpp-custom.css')
    ],
    output: 'conditional.bundle.css'
  }
};

const destDir = path.resolve(basePath, 'assets/dist');

// Ensure destination directory exists
if (!fs.existsSync(destDir)) {
  fs.mkdirSync(destDir, { recursive: true });
}

// Generic function to build a CSS bundle
function buildCssBundle(bundleName, bundleConfig) {
  try {
    const concatenatedCss = bundleConfig.files.map(file => {
      if (fs.existsSync(file)) {
        return fs.readFileSync(file, 'utf8');
      }
      // If a file doesn't exist, just return an empty string and log a warning.
      console.warn(`Warning: File not found, skipping: ${file}`);
      return '';
    }).join('\n');

    const destFile = path.join(destDir, bundleConfig.output);
    fs.writeFileSync(destFile, concatenatedCss);
    console.log(`Successfully created ${destFile} (concatenated only).`);
  } catch (error) {
    console.error(`Error building ${bundleName} bundle:`, error);
  }
}

// Run the build process for all defined bundles
function runAllBuilds() {
  console.log('Starting build process...');
  for (const bundleName in bundles) {
    buildCssBundle(bundleName, bundles[bundleName]);
  }
  console.log('Build process finished.');
}

// Execute the builds
runAllBuilds();
