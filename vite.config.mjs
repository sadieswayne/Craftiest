import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import fs from 'fs';
import path, { resolve } from 'path';
import { homedir } from 'os';
import mkcert from 'vite-plugin-mkcert';

const laravelInputs = [];
const themeAppJsFiles = [];
const excludedThemeDirs = [ 'vendor' ];
const plugins = [];

// adding theme files
const themes = fs.readdirSync('resources/views', { withFileTypes: true })
	.filter(dirent => dirent.isDirectory() && !excludedThemeDirs.includes(dirent.name))
	.map(dirent => dirent.name);

themes.forEach(theme => {
	const scssDir = `resources/views/${theme}/scss`;
	const themeDashboardScssPath = `resources/views/${theme}/scss/dashboard.scss`;
	const themeLPScssPath = `resources/views/${theme}/scss/landing-page.scss`;
	const themeAppJsPath = `resources/views/${theme}/js/app.js`;
	const chatbotAppJsPath = `resources/views/${theme}/js/chatbotApp.js`;

	// Check if scss directory exists
	if (fs.existsSync(scssDir)) {
		try {
			// Get all scss files in the directory
			const findAllScssFiles = dir => {
				let results = [];
				const items = fs.readdirSync(dir);

				for (const item of items) {
					const fullPath = path.join(dir, item);
					const stat = fs.statSync(fullPath);

					if (stat.isDirectory()) {
						results = results.concat(findAllScssFiles(fullPath));
					} else if (item.endsWith('.scss')) {
						results.push(fullPath);
					}
				}

				return results;
			};

			const scssFiles = findAllScssFiles(scssDir);

			// Find duplicates (ones with and without leading underscore)
			scssFiles.forEach(file => {
				const filename = path.basename(file);
				if (!filename.startsWith('_')) {
					const fileDir = path.dirname(file);
					const underscoreVersion = `_${filename}`;
					const underscorePath = path.join(fileDir, underscoreVersion);

					// Check if the underscore version exists
					if (scssFiles.includes(underscorePath)) {
						// Remove the version without underscore
						fs.unlinkSync(file);
						console.log(`Removed duplicate file: ${file}`);
					}
				}
			});
		} catch (error) {
			console.error(`Error processing SCSS directory ${scssDir}:`, error);
		}
	}

	fs.existsSync(themeDashboardScssPath) && laravelInputs.push(themeDashboardScssPath);
	fs.existsSync(themeLPScssPath) && laravelInputs.push(themeLPScssPath);
	if (fs.existsSync(themeAppJsPath)) {
		laravelInputs.push(themeAppJsPath);
		themeAppJsFiles.push(themeAppJsPath);
	}
	fs.existsSync(chatbotAppJsPath) && laravelInputs.push(chatbotAppJsPath);
});

// laravelInputs.push('resources/views/default/js/chatbot/index.js');
if (fs.existsSync('resources/views/default/js/chatbotApp.js')) {
	laravelInputs.push('resources/views/default/js/chatbotApp.js');
}

if (fs.existsSync('app/Extensions/Chatbot/resources/assets/scss/external-chatbot.scss')) {
	laravelInputs.push('app/Extensions/Chatbot/resources/assets/scss/external-chatbot.scss');
}

if ( process.env.NODE_ENV === 'development' ) {
	plugins.push(mkcert());
}

plugins.push(
	laravel({
		input: laravelInputs,
		refresh: [ 'app/**/*.php', 'resources/views/**/*.php', 'resources/views/**/*.js' ],
	})
);

export default ({ mode }) => {
	// Load app-level env vars to node-level env vars.
	process.env = { ...process.env, ...loadEnv(mode, process.cwd()) };

	return defineConfig({
		server: detectServerConfig(process.env.VITE_APP_DOMAIN || 'magicai.test'),
		plugins,
		build: {
			rollupOptions: {
				output: {
					entryFileNames: 'assets/[name]-[hash].js',
					chunkFileNames: 'assets/[name]-[hash].js',
					assetFileNames: 'assets/[name]-[hash].[ext]',
					// manualChunks: {
					// All files will be bundled into a single file
					//     'app': themeAppJsFiles
					// }
				}
			}
		},
		resolve: {
			alias: {
				'@': '/resources/js',
				'@public': '/public',
				'@themeAssets': '/public/themes',
				'~nodeModules': path.resolve(__dirname, 'node_modules'),
				'~vendor': path.resolve(__dirname, 'vendor'),
			}
		}
	});
};

function detectServerConfig(domain) {
	if (process.env.NODE_ENV === 'development') {
		return {
			host: domain,
			origin: process.env.APP_URL,
			https: true,
			port: 4443,
			hmr: {
				host: process.env.APP_URL,
			},
		};
	}

	let keyPath = resolve(homedir(), `.config/valet/Certificates/${domain}.key`);
	let certPath = resolve(homedir(), `.config/valet/Certificates/${domain}.crt`);

	if (!fs.existsSync(keyPath)) {
		return {};
	}

	if (!fs.existsSync(certPath)) {
		return {};
	}

	return {
		hmr: {
			host: domain,
		},
		host: domain,
		https: {
			key: fs.readFileSync(keyPath),
			cert: fs.readFileSync(certPath),
		},
	};
}
