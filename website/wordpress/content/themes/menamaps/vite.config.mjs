import { defineConfig }	from 'vite';
import liveReload				from 'vite-plugin-live-reload';
import { resolve }			from 'path';

// -----------------------------------------------------------------------------

// ref: https://vitejs.dev/config

export default defineConfig({

	plugins: [
		liveReload([
			__dirname + '/*.php',
			__dirname + '/template_parts/**/*.php',
			'!' + __dirname + '/includes/DotAim/**/node_modules/**',
		]),
	],

	// ---------------------------------------------------------------------------

	root: '',
	base: process.env.NODE_ENV === 'development' ? '/' : '/assets/build/',

	// ---------------------------------------------------------------------------

	build: {
		outDir				: resolve(__dirname, 'assets/build'),
		emptyOutDir		: true,
		manifest			: true,
		minify				: true,
		rollupOptions	: {
			input: {
				app: resolve(__dirname, 'assets/src/js/app.js'),
			},
		},
	},

	// ---------------------------------------------------------------------------

	server: {

		// required to load scripts from custom host

		cors: true,

		// -------------------------------------------------------------------------

		// we need a strict port to match on PHP side
		// change freely, but update in your functions.php to match the same port

		strictPort: true,
		port			: 3000,

		// -------------------------------------------------------------------------

		proxy: {
			'/website/wp': {
				 target				: 'http://menamaps.localhost/',
				 changeOrigin	: true,
				 rewrite			: path => path.replace(/^\/website\/wp/, '/website/wp'),
			}
		},

		// -------------------------------------------------------------------------

		https: false,

		// serve over httpS
		// to generate localhost certificate follow the link:
		// https://github.com/FiloSottile/mkcert - Windows, MacOS and Linux supported - Browsers Chrome, Chromium and Firefox (FF MacOS and Linux only)
		// installation example on Windows 10:
		// > choco install mkcert (this will install mkcert)
		// > mkcert -install (global one time install)
		// > mkcert localhost (in project folder files localhost-key.pem & localhost.pem will be created)
		// uncomment below to enable https
		//https: {
		//  key: fs.readFileSync('localhost-key.pem'),
		//  cert: fs.readFileSync('localhost.pem'),
		//},

		// -------------------------------------------------------------------------

		hmr: {
			host: 'localhost',
			//port: 443
		},

	},

});
