/** @type {import('tailwindcss').Config} */
const defaultTheme	= require('tailwindcss/defaultTheme');
const colors				= require('tailwindcss/colors');

// -----------------------------------------------------------------------------

module.exports = {

	content: {
		relative: true,
		files		: [
			'./*.php',
			'./template_parts/**/*.php',
			'./includes/DotAim/**/*.php',
			'!./includes/DotAim/**/node_modules/**',
		],
	},

	// ---------------------------------------------------------------------------

	// classes that need to be added but are not in content
	// for example classes added using customizer or in post content
	//
	// ref: https://tailwindcss.com/docs/content-configuration#safelisting-classes

	/*
	safelist: [
		'text-center',
		{
			pattern: /bg-(red|green|blue)-(100|200|300)/,
		},
	],
	*/

	// ---------------------------------------------------------------------------

	theme: {
		extend: {

			colors: {

				//primary: colors.neutral,
				primary: {
					 50: 'rgb(var(--color-primary-50)  / <alpha-value>)',
					100: 'rgb(var(--color-primary-100) / <alpha-value>)',
					200: 'rgb(var(--color-primary-200) / <alpha-value>)',
					300: 'rgb(var(--color-primary-300) / <alpha-value>)',
					400: 'rgb(var(--color-primary-400) / <alpha-value>)',
					500: 'rgb(var(--color-primary-500) / <alpha-value>)',
					600: 'rgb(var(--color-primary-600) / <alpha-value>)',
					700: 'rgb(var(--color-primary-700) / <alpha-value>)',
					800: 'rgb(var(--color-primary-800) / <alpha-value>)',
					900: 'rgb(var(--color-primary-900) / <alpha-value>)',
					950: 'rgb(var(--color-primary-950) / <alpha-value>)',
				},

				//secondary: colors.blue,
				secondary: {
					 50: 'rgb(var(--color-secondary-50)  / <alpha-value>)',
					100: 'rgb(var(--color-secondary-100) / <alpha-value>)',
					200: 'rgb(var(--color-secondary-200) / <alpha-value>)',
					300: 'rgb(var(--color-secondary-300) / <alpha-value>)',
					400: 'rgb(var(--color-secondary-400) / <alpha-value>)',
					500: 'rgb(var(--color-secondary-500) / <alpha-value>)',
					600: 'rgb(var(--color-secondary-600) / <alpha-value>)',
					700: 'rgb(var(--color-secondary-700) / <alpha-value>)',
					800: 'rgb(var(--color-secondary-800) / <alpha-value>)',
					900: 'rgb(var(--color-secondary-900) / <alpha-value>)',
					950: 'rgb(var(--color-secondary-950) / <alpha-value>)',
				},

			},

			// -----------------------------------------------------------------------

			typography: ({ theme }) => ({
				primary: {
					css: {

						// light color scheme
						'--tw-format-body'								: 'rgb(var(--color-primary-800))',
						'--tw-format-headings'						: 'rgb(var(--color-primary-900))',
						'--tw-format-lead'								: 'rgb(var(--color-primary-800))',
						'--tw-format-links'								: 'rgb(var(--color-secondary-600))',
						'--tw-format-bold'								: 'rgb(var(--color-primary-900))',
						'--tw-format-counters'						: 'rgb(var(--color-primary-800))',
						'--tw-format-bullets'							: 'rgb(var(--color-primary-800))',
						'--tw-format-hr'									: 'rgb(var(--color-primary-200))',
						'--tw-format-quotes'							: 'rgb(var(--color-primary-900))',
						'--tw-format-quote-borders'				: 'rgb(var(--color-primary-200))',
						'--tw-format-captions'						: 'rgb(var(--color-primary-800))',
						'--tw-format-code'								: 'rgb(var(--color-primary-900))',
						'--tw-format-code-bg'							: 'rgb(var(--color-primary-100))',
						'--tw-format-pre-code'						: 'rgb(var(--color-primary-800))',
						'--tw-format-pre-bg'							: 'rgb(var(--color-primary-100))',
						'--tw-format-th-borders'					: 'rgb(var(--color-primary-200))',
						'--tw-format-th-bg'								: 'rgb(var(--color-primary-50))',
						'--tw-format-td-borders'					: 'rgb(var(--color-primary-200))',

						// dark color scheme
						'--tw-format-invert-body'					: 'rgb(var(--color-primary-200))',
						'--tw-format-invert-headings'			: 'rgb(var(--color-primary-50))',
						'--tw-format-invert-lead'					: 'rgb(var(--color-primary-200))',
						'--tw-format-invert-links'				: 'rgb(var(--color-secondary-500))',
						'--tw-format-invert-bold'					: 'rgb(var(--color-primary-50))',
						'--tw-format-invert-counters'			: 'rgb(var(--color-primary-200))',
						'--tw-format-invert-bullets'			: 'rgb(var(--color-primary-200))',
						'--tw-format-invert-hr'						: 'rgb(var(--color-primary-700))',
						'--tw-format-invert-quotes'				: 'rgb(var(--color-primary-100))',
						'--tw-format-invert-quote-borders': 'rgb(var(--color-primary-700))',
						'--tw-format-invert-captions'			: 'rgb(var(--color-primary-200))',
						'--tw-format-invert-code'					: 'rgb(var(--color-primary-50))',
						'--tw-format-invert-code-bg'			: 'rgb(var(--color-primary-800))',
						'--tw-format-invert-pre-code'			: 'rgb(var(--color-primary-50))',
						'--tw-format-invert-pre-bg'				: 'rgb(var(--color-primary-800))',
						'--tw-format-invert-th-borders'		: 'rgb(var(--color-primary-600))',
						'--tw-format-invert-td-borders'		: 'rgb(var(--color-primary-700))',
						'--tw-format-invert-th-bg'				: 'rgb(var(--color-primary-700))',

						// for ref of previous way:
						/*
						'--tw-format-body'								: theme('colors.neutral[500]'),
						'--tw-format-headings'						: theme('colors.neutral[900]'),
						'--tw-format-lead'								: theme('colors.neutral[500]'),
						//'--tw-format-links'							: theme('colors.neutral[600]'),
						//'--tw-format-links'							: theme('colors.primary[600]'),
						//'--tw-format-links'							: theme('colors.secondary[600]'),
						'--tw-format-links'								: 'rgb(var(--color-secondary-600))',
						'--tw-format-bold'								: theme('colors.neutral[900]'),
						'--tw-format-counters'						: theme('colors.neutral[500]'),
						'--tw-format-bullets'							: theme('colors.neutral[500]'),
						'--tw-format-hr'									: theme('colors.neutral[200]'),
						'--tw-format-quotes'							: theme('colors.neutral[900]'),
						'--tw-format-quote-borders'				: theme('colors.neutral[200]'),
						'--tw-format-captions'						: theme('colors.neutral[500]'),
						'--tw-format-code'								: theme('colors.neutral[900]'),
						'--tw-format-code-bg'							: theme('colors.neutral[100]'),
						'--tw-format-pre-code'						: theme('colors.neutral[600]'),
						'--tw-format-pre-bg'							: theme('colors.neutral[100]'),
						'--tw-format-th-borders'					: theme('colors.neutral[200]'),
						'--tw-format-th-bg'								: theme('colors.neutral[50]'),
						'--tw-format-td-borders'					: theme('colors.neutral[200]'),
						'--tw-format-invert-body'					: theme('colors.neutral[400]'),
						'--tw-format-invert-headings'			: theme('colors.white'),
						'--tw-format-invert-lead'					: theme('colors.neutral[400]'),
						//'--tw-format-invert-links'			: theme('colors.white'),
						//'--tw-format-invert-links'			: theme('colors.primary[500]'),
						//'--tw-format-invert-links'			: theme('colors.secondary[500]'),
						'--tw-format-invert-links'				: 'rgb(var(--color-secondary-500))',
						'--tw-format-invert-bold'					: theme('colors.white'),
						'--tw-format-invert-counters'			: theme('colors.neutral[400]'),
						'--tw-format-invert-bullets'			: theme('colors.neutral[600]'),
						'--tw-format-invert-hr'						: theme('colors.neutral[700]'),
						'--tw-format-invert-quotes'				: theme('colors.neutral[100]'),
						'--tw-format-invert-quote-borders': theme('colors.neutral[700]'),
						'--tw-format-invert-captions'			: theme('colors.neutral[400]'),
						'--tw-format-invert-code'					: theme('colors.white'),
						'--tw-format-invert-code-bg'			: theme('colors.neutral[800]'),
						'--tw-format-invert-pre-code'			: theme('colors.neutral[300]'),
						'--tw-format-invert-pre-bg'				: theme('colors.neutral[700]'),
						'--tw-format-invert-th-borders'		: theme('colors.neutral[600]'),
						'--tw-format-invert-td-borders'		: theme('colors.neutral[700]'),
						'--tw-format-invert-th-bg'				: theme('colors.neutral[700]'),
						*/
					},
				},
			}),

		},
	},

	// ---------------------------------------------------------------------------

	plugins: [
		require('flowbite/plugin'),
		require('flowbite-typography'),
	],

	// ---------------------------------------------------------------------------

	//darkMode	: 'class', // media | class
	//important	: true, // or '#element_id',

}

