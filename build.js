const fs = require('fs');
const path = require('path');

// Define absolute paths
const basePath = __dirname;
const coreFiles = [
  path.resolve(basePath, 'assets/css/components/variables.css'),
  path.resolve(basePath, 'assets/css/components/fonts.css'),
  path.resolve(basePath, 'assets/css/main.css')
];
const destDir = path.resolve(basePath, 'assets/dist');
const destFile = path.join(destDir, 'core.bundle.css');

// Ensure destination directory exists
if (!fs.existsSync(destDir)) {
  fs.mkdirSync(destDir, { recursive: true });
}

// Function to build the core CSS bundle
function buildCoreCss() {
  try {
    // Concatenate files using native fs module
    const concatenatedCss = coreFiles.map(file => {
      if (fs.existsSync(file)) {
        return fs.readFileSync(file, 'utf8');
      }
      throw new Error(`File not found: ${file}`);
    }).join('\n');

    // Write the final file
    fs.writeFileSync(destFile, concatenatedCss);
    console.log(`Successfully created ${destFile} (concatenated only).`);

  } catch (error) {
    console.error('Error during build process:', error);
  }
}

// Run the build process
buildCoreCss();
