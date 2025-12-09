/**
 * Pilom Asset Minification Script
 * 
 * Minifies CSS and JS files using clean-css and terser.
 * 
 * Usage:
 *   node tools/minify.js           # Build all
 *   node tools/minify.js --css-only # Build CSS only
 *   node tools/minify.js --js-only  # Build JS only
 *   node tools/minify.js --watch    # Watch mode
 */

const fs = require('fs');
const path = require('path');
const CleanCSS = require('clean-css');
const { minify } = require('terser');

// Configuration
const config = {
    publicDir: path.join(__dirname, '..', 'public'),
    cssDir: 'css',
    jsDir: 'js',
    distDir: 'dist',

    // CleanCSS options
    cssOptions: {
        level: 2,
        sourceMap: false,
        format: 'beautify' // Use 'keep-breaks' for readable output
    },

    // Terser options
    jsOptions: {
        compress: {
            dead_code: true,
            drop_console: false, // Keep console for debugging
            drop_debugger: true
        },
        mangle: true,
        format: {
            comments: false
        }
    }
};

// Parse arguments
const args = process.argv.slice(2);
const cssOnly = args.includes('--css-only');
const jsOnly = args.includes('--js-only');
const watchMode = args.includes('--watch');

// Ensure dist directories exist
function ensureDistDirs() {
    const distCss = path.join(config.publicDir, config.distDir, 'css');
    const distJs = path.join(config.publicDir, config.distDir, 'js');

    if (!fs.existsSync(distCss)) {
        fs.mkdirSync(distCss, { recursive: true });
    }
    if (!fs.existsSync(distJs)) {
        fs.mkdirSync(distJs, { recursive: true });
    }
}

// Minify CSS files
async function minifyCss() {
    const cssDir = path.join(config.publicDir, config.cssDir);
    const distDir = path.join(config.publicDir, config.distDir, 'css');

    console.log('\n📦 Minifying CSS files...');

    const files = fs.readdirSync(cssDir).filter(f => f.endsWith('.css'));
    let totalOriginal = 0;
    let totalMinified = 0;

    for (const file of files) {
        const sourcePath = path.join(cssDir, file);
        const destPath = path.join(distDir, file.replace('.css', '.min.css'));

        try {
            const source = fs.readFileSync(sourcePath, 'utf8');
            const result = new CleanCSS(config.cssOptions).minify(source);

            if (result.errors.length > 0) {
                console.error(`  ❌ ${file}: ${result.errors.join(', ')}`);
                continue;
            }

            fs.writeFileSync(destPath, result.styles);

            const originalSize = Buffer.byteLength(source, 'utf8');
            const minifiedSize = Buffer.byteLength(result.styles, 'utf8');
            const savings = ((1 - minifiedSize / originalSize) * 100).toFixed(1);

            totalOriginal += originalSize;
            totalMinified += minifiedSize;

            console.log(`  ✅ ${file} → ${file.replace('.css', '.min.css')} (${savings}% smaller)`);
        } catch (err) {
            console.error(`  ❌ ${file}: ${err.message}`);
        }
    }

    const totalSavings = ((1 - totalMinified / totalOriginal) * 100).toFixed(1);
    console.log(`\n  📊 Total CSS: ${(totalOriginal / 1024).toFixed(1)}KB → ${(totalMinified / 1024).toFixed(1)}KB (${totalSavings}% reduction)`);
}

// Minify JS files
async function minifyJs() {
    const jsDir = path.join(config.publicDir, config.jsDir);
    const distDir = path.join(config.publicDir, config.distDir, 'js');

    console.log('\n📦 Minifying JavaScript files...');

    const files = fs.readdirSync(jsDir).filter(f => f.endsWith('.js'));
    let totalOriginal = 0;
    let totalMinified = 0;

    for (const file of files) {
        const sourcePath = path.join(jsDir, file);
        const destPath = path.join(distDir, file.replace('.js', '.min.js'));

        try {
            const source = fs.readFileSync(sourcePath, 'utf8');
            const result = await minify(source, config.jsOptions);

            if (result.code === undefined) {
                console.error(`  ❌ ${file}: Minification returned undefined`);
                continue;
            }

            fs.writeFileSync(destPath, result.code);

            const originalSize = Buffer.byteLength(source, 'utf8');
            const minifiedSize = Buffer.byteLength(result.code, 'utf8');
            const savings = ((1 - minifiedSize / originalSize) * 100).toFixed(1);

            totalOriginal += originalSize;
            totalMinified += minifiedSize;

            console.log(`  ✅ ${file} → ${file.replace('.js', '.min.js')} (${savings}% smaller)`);
        } catch (err) {
            console.error(`  ❌ ${file}: ${err.message}`);
        }
    }

    const totalSavings = totalOriginal > 0 ? ((1 - totalMinified / totalOriginal) * 100).toFixed(1) : 0;
    console.log(`\n  📊 Total JS: ${(totalOriginal / 1024).toFixed(1)}KB → ${(totalMinified / 1024).toFixed(1)}KB (${totalSavings}% reduction)`);
}

// Main build function
async function build() {
    console.log('🚀 Pilom Asset Builder\n');
    console.log('='.repeat(50));

    ensureDistDirs();

    if (!jsOnly) {
        await minifyCss();
    }

    if (!cssOnly) {
        await minifyJs();
    }

    console.log('\n' + '='.repeat(50));
    console.log('✨ Build complete!\n');
}

// Watch mode
async function watch() {
    const chokidar = require('chokidar');

    console.log('👀 Watching for file changes...\n');

    const cssWatcher = chokidar.watch(path.join(config.publicDir, config.cssDir, '*.css'));
    const jsWatcher = chokidar.watch(path.join(config.publicDir, config.jsDir, '*.js'));

    cssWatcher.on('change', async (filePath) => {
        console.log(`\n📝 CSS changed: ${path.basename(filePath)}`);
        await minifyCss();
    });

    jsWatcher.on('change', async (filePath) => {
        console.log(`\n📝 JS changed: ${path.basename(filePath)}`);
        await minifyJs();
    });

    // Initial build
    await build();
}

// Run
if (watchMode) {
    watch().catch(console.error);
} else {
    build().catch(console.error);
}
