const fs						= require('fs');
const path					= require('path');
const { execSync }	= require('child_process');

// -----------------------------------------------------------------------------

// Parse command line arguments

function parse_args()
{

	// checks for npm environment variables for the case of
	// `npm run wp_theme_version --type=... --message="..." --deploy`
	// fallback to defaults

	const options = {
		type		: process.env.npm_config_type			|| 'patch',
		message	: process.env.npm_config_message	|| 'Update theme version',
		push		: process.env.npm_config_no_push	 ? false: true,
		deploy	: process.env.npm_config_deploy		 ? true : false,
	};

	// ---------------------------------------------------------------------------

	// this would be the case of
	// `node workflow/version.js  --type=... --message="..." --deploy`

	const args = process.argv.slice(2);

	for ( const arg of args )
	{
		if ( '--deploy' === arg )
		{
			options.deploy = true;

			continue;
		}

		// -------------------------------------------------------------------------

		if ( arg.startsWith('--') )
		{
			let [key, value] = arg.slice(2).split('=');

			if ( value !== undefined )
			{
				value = value.trim();

				if ( value !== '' )
				{
					options[ key ] = value;
				}
			}
		}
	}

	// ---------------------------------------------------------------------------

	// Validate version increment type

	if ( ! ['patch', 'minor', 'major'].includes( options.type ) )
	{
		console.error(`${error_icon} Invalid increment type: "${options.type}". Use: patch, minor, or major\n`);

		process.exit(1);
	}

	// ---------------------------------------------------------------------------

	return options;

}
// parse_args()

const options = parse_args();

// -----------------------------------------------------------------------------

const success_icon	= '\u2714';
const error_icon		= '\u274c';

// -----------------------------------------------------------------------------

// Define paths

function get_repo_root()
{

	try {

		return execSync('git rev-parse --show-toplevel', { encoding: 'utf8' }).trim();

	} catch ( error ) {

		console.error(`${error_icon} Error: This script must be run within a Git repository\n`);

		process.exit(1);

	}

}
// get_repo_root()

const repo_root					= get_repo_root();
const theme_path				= path.join( repo_root, 'app/wordpress/content/themes/menamaps' );
const package_json_path	= path.join( theme_path, 'package.json' );
const files_to_stage		= [];

// -----------------------------------------------------------------------------

// Read the current package.json

let package_json		= '';
let current_version	= '';

try {

	package_json		= JSON.parse( fs.readFileSync( package_json_path, 'utf8' ) );
	current_version	= package_json.version || '0.0.0';

} catch ( err ) {

	console.error(`${error_icon} Error reading package.json:`, err);

	process.exit(1);

}

// -----------------------------------------------------------------------------

// Calculate the new version number

let [major, minor, patch] = current_version.split('.').map(Number);

switch ( options.type )
{
	case 'patch':

		patch += 1;

		break;

	// ---------------------------------------------------------------------------

	case 'minor':

		minor += 1;
		patch = 0;

		break;

	// ---------------------------------------------------------------------------

	case 'major':

		major += 1;
		minor = 0;
		patch = 0;

		break;

	// ---------------------------------------------------------------------------

	default:

		console.error(`${error_icon} Invalid increment type. Use: patch, minor, or major\n`);

		process.exit(1);
}

const new_version = `${major}.${minor}.${patch}`;

// -----------------------------------------------------------------------------

// Update package.json with the new version

try {

	package_json.version = new_version;

	fs.writeFileSync( package_json_path, JSON.stringify( package_json, null, 2 ) + '\n' );

	console.log(`${success_icon} Updated package.json version to ${new_version}`);

	files_to_stage.push( package_json_path );

} catch ( err ) {

	console.error(`${error_icon} Error updating package.json:`, err);

	process.exit(1);

}

// -----------------------------------------------------------------------------

// Update package-lock.json if it exists

const package_lock_path = path.join( theme_path, 'package-lock.json' );

if ( fs.existsSync( package_lock_path ) )
{
	try {

		const package_lock = JSON.parse( fs.readFileSync( package_lock_path, 'utf8' ) );

		package_lock.version = new_version;

		// Update the root package in the packages object if it exists
		if ( package_lock.packages && package_lock.packages[''] )
		{
			package_lock.packages[''].version = new_version;
		}

		fs.writeFileSync( package_lock_path, JSON.stringify( package_lock, null, 2 ) + '\n' );

		console.log(`${success_icon} Updated package-lock.json version to ${new_version}`);

		files_to_stage.push( package_lock_path );

	} catch ( err ) {

		console.error(`${error_icon} Error updating package-lock.json:`, err);

	}
}

// -----------------------------------------------------------------------------

// Update theme style.css

const style_css_path = path.join( theme_path, 'style.css' );

if ( fs.existsSync( style_css_path ) )
{
	try {

		// Read the style.css file

		const style_css = fs.readFileSync( style_css_path, 'utf8' );

		// -------------------------------------------------------------------------

		// Update version in style.css

		const version_line_match	= style_css.match(/(Version:\s*)/);
		const version_spacing			= version_line_match ? version_line_match[1].replace('Version:', '') : ' ';
		let style_css_updated			= style_css.replace(/Version:\s*.*/, `Version:${version_spacing}${new_version}`);

		// -------------------------------------------------------------------------

		// Update dates

		const date						= new Date();
		const current_date		= date.toISOString().split('T')[0];
		const date_line_match	= style_css_updated.match(/(Date:\s*)/);
		const date_spacing		= date_line_match ? date_line_match[1].replace('Date:', '') : ' ';
		style_css_updated			= style_css_updated.replace(/Date:\s*.*/, `Date:${date_spacing}${current_date}`);
		style_css_updated			= style_css_updated.replace(/Copyright \(c\) \d{4}/, `Copyright (c) ${date.getFullYear()}`);

		// -------------------------------------------------------------------------

		// Write the updated content back to style.css

		fs.writeFileSync( style_css_path, style_css_updated, 'utf8' );

		console.log(`${success_icon} Updated style.css to version ${new_version}`);

		files_to_stage.push( style_css_path );

	} catch ( err ) {

		console.error(`${error_icon} Error updating style.css:`, err);

		process.exit(1);

	}
}

// -----------------------------------------------------------------------------

// Git operations

try {

	// Stage the changes

	if ( files_to_stage.length )
	{
		execSync(`git add ${files_to_stage.join(' ')}`);

		console.log(`${success_icon} Staged changes to Git`);
	}

	// ---------------------------------------------------------------------------

	// Commit

	execSync(`git commit -m "WordPress Theme version ${new_version}"`);

	console.log(`${success_icon} Committed changes to Git`);

	// ---------------------------------------------------------------------------

	// Create tag

	execSync(`git tag -a wp_theme-v${new_version} -m "${options.message}"`);

	console.log(`${success_icon} Created Git tag wp_theme-v${new_version}`);

	// ---------------------------------------------------------------------------

	// Push

	if ( options.push )
	{
		execSync('git push && git push --tags');

		console.log(`${success_icon} Pushed changes and tags to remote`);
	}
	else
	{
		console.log('Changes not pushed. Remove "--no_push" to push automatically.');
	}

} catch ( err ) {

	console.error(`${error_icon} Error during Git operations:`, err + '\n');

	process.exit(1);

}

console.log(`\n${success_icon} Theme version update completed: ${current_version} → ${new_version}\n`);

// -----------------------------------------------------------------------------

// Deploy

if ( options.deploy )
{
	try {

		const deploy_script_path = path.join( theme_path, 'workflow/deploy.sh' );

		if ( ! fs.existsSync( deploy_script_path ) )
		{
			console.error(`${error_icon} Error: Deployment script not found at ${deploy_script_path}\n`);

			process.exit(1);
		}

		// -------------------------------------------------------------------------

		console.log(`\n${success_icon} Starting theme deployment...`);

		execSync(`bash ${deploy_script_path}`);

		console.log(`${success_icon} Theme deployed successfully\n`);

	} catch ( err ) {

		console.error(`${error_icon} Error during deployment:`, err + '\n');

		process.exit(1);

	}
}
