const { exec }	= require('child_process');
const path			= require('path');
const fs				= require('fs');
const pkg				= require('../package.json');

// -----------------------------------------------------------------------------

// flag file

// @notes:
// this is a hack to use a file and delete it when the server is stopped
// because development env is not detected in php

const root			= path.resolve(__dirname, '../');
const flag_file	= path.join(root, pkg.config.vite_dev_flag_file);

fs.writeFileSync( flag_file, '' );

// -----------------------------------------------------------------------------

const vite_dev_server = exec('vite');

vite_dev_server.stderr.on('data', ( data ) => { console.error( data ); });
vite_dev_server.stdout.on('data', ( data ) => {

	// @todo:
	// the same messagge is displayed twice, not sure why and how to fix it
	console.log( data );

	if ( data.includes('Local:') )
	{
		// @consider
		// extract the URL from the Vite output
		//const matches	= data.match(/(http:\/\/[\w\.\-]+:\d+)\/?/);
		//const base		= matches && matches[1] ? `${matches[1]}/` : 'http://menamaps.localhost';

		const base	= 'http://menamaps.localhost';
		const url		= new URL( '/website/wp/', base ).href;

		// display the URL in cli so it can be copied it if needed
		console.log('\nGo To:', url);
	}

});

// -----------------------------------------------------------------------------

function cleanup()
{

	if ( fs.existsSync( flag_file ) )
	{
		console.log('\nRemoving vite dev flag file:', pkg.config.vite_dev_flag_file);

		fs.unlinkSync( flag_file );
	}

}
// cleanup()



// i'm doing the build after stoping dev server
// so that the assets files are updated without the need to run the build command
function build()
{

	console.log('\nStarting the build process...\n');

	exec('npm run build', ( error, stdout, stderr ) => {

		if ( error )
		{
			console.error(`Error during npm run build: ${error}`);

			return;
		}

		// -------------------------------------------------------------------------

		if ( stderr )
		{
			console.error(`Build error output:\n${stderr}`);
		}

		// -------------------------------------------------------------------------

		console.log(`Build output: ${stdout}`);

	});

}
// build()



// handle the process events
['SIGINT', 'SIGTERM'].forEach( ( signal ) => {

	process.on( signal, () => {

		console.log(`\nProcess received signal: "${signal}"`);

		vite_dev_server.kill('SIGINT'); // kill vite dev server process

		cleanup();

		build();

	});

});
