import { toPng } from 'html-to-image';

// -----------------------------------------------------------------------------

// Utility function to generate subtle color variations

function generate_subtle_color( hex_color, lighten = true, percentage = 10 )
{

	// Remove # if present

	hex_color = hex_color.replace('#', '');

	// ---------------------------------------------------------------------------

	// Convert to RGB

	const r = parseInt(hex_color.substr(0, 2), 16);
	const g = parseInt(hex_color.substr(2, 2), 16);
	const b = parseInt(hex_color.substr(4, 2), 16);

	// ---------------------------------------------------------------------------

	// Apply factor

	const factor	= lighten ? 1 + percentage / 100 : 1 - percentage / 100;
	const new_r		= Math.min(255, Math.max(0, Math.round(r * factor)));
	const new_g		= Math.min(255, Math.max(0, Math.round(g * factor)));
	const new_b		= Math.min(255, Math.max(0, Math.round(b * factor)));

	// ---------------------------------------------------------------------------

	// Convert back to hex

	return `#${((1 << 24) + (new_r << 16) + (new_g << 8) + new_b).toString(16).slice(1)}`;

}
// generate_subtle_color()

// -----------------------------------------------------------------------------

// Auto-resize text to fit container

function auto_resize_text( container, text_element, max_font_size )
{

	// Find the actual content wrapper (might be content box or container)

	const content_wrapper = container.querySelector('.content-box') || container;

	// ---------------------------------------------------------------------------

	// Check if there's a logo and calculate its space

	const logo_element	= content_wrapper.querySelector('.content-logo');
	let logo_height			= 0;

	if ( logo_element )
	{
		const logo_styles	= window.getComputedStyle( logo_element );
		logo_height				= logo_element.offsetHeight
											+ parseInt( logo_styles.marginTop )
											+ parseInt( logo_styles.marginBottom );
	}

	// ---------------------------------------------------------------------------

	// Check if there's copyright text and calculate its space

	const copyright_element	= content_wrapper.querySelector('.content-copyright');
	let copyright_height		= 0;

	if ( copyright_element )
	{
		const copyright_styles	= window.getComputedStyle( copyright_element );
		copyright_height				= copyright_element.offsetHeight
														+ parseInt( copyright_styles.marginTop )
														+ parseInt( copyright_styles.marginBottom );
	}

	// ---------------------------------------------------------------------------

	// Get the actual available space

	const container_rect	= container.getBoundingClientRect();
	const wrapper_rect		= content_wrapper.getBoundingClientRect();

	// ---------------------------------------------------------------------------

	// Get computed styles for accurate padding

	const wrapper_styles	= window.getComputedStyle(content_wrapper);
	const padding_left		= parseInt(wrapper_styles.paddingLeft) || 0;
	const padding_right		= parseInt(wrapper_styles.paddingRight) || 0;
	const padding_top			= parseInt(wrapper_styles.paddingTop) || 0;
	const padding_bottom	= parseInt(wrapper_styles.paddingBottom) || 0;

	// ---------------------------------------------------------------------------

	// Calculate available space inside the content wrapper, minus logo space and copyright space

	const available_width		= wrapper_rect.width - padding_left - padding_right;
	const available_height	= wrapper_rect.height - padding_top - padding_bottom - logo_height - copyright_height;

	// ---------------------------------------------------------------------------

	let font_size = max_font_size;

	text_element.style.fontSize = `${font_size}px`;

	// ---------------------------------------------------------------------------

	// Give the text element full available space

	text_element.style.width	= `${available_width}px`;
	text_element.style.height	= `${available_height}px`;

	// ---------------------------------------------------------------------------

	// Reduce font size until text fits

	let attempts = 0;

	while ( font_size > 12 && attempts < 50 ) // Prevent infinite loops
	{
		// Force a reflow

		text_element.offsetHeight;

		// -------------------------------------------------------------------------

		// Check if text overflows

		const text_scroll_height	= text_element.scrollHeight;
		const text_scroll_width		= text_element.scrollWidth;
		const text_client_height	= text_element.clientHeight;
		const text_client_width		= text_element.clientWidth;

		// -------------------------------------------------------------------------

		// If text fits, we're done

		if ( text_scroll_height <= text_client_height && text_scroll_width <= text_client_width )
		{
			break;
		}

		// -------------------------------------------------------------------------

		// Reduce font size and try again

		font_size -= 2;

		text_element.style.fontSize = `${font_size}px`;

		attempts++;
	}

	// ---------------------------------------------------------------------------

	return font_size;

}
// auto_resize_text()

// -----------------------------------------------------------------------------

// Convert Tailwind classes to CSS styles

function get_font_weight_css( tailwind_class )
{

	const weights = {
		'font-thin'				: '100',
		'font-extralight'	: '200',
		'font-light'			: '300',
		'font-normal'			: '400',
		'font-medium'			: '500',
		'font-semibold'		: '600',
		'font-bold'				: '700',
		'font-extrabold'	: '800',
		'font-black'			: '900',
	};

	return weights[tailwind_class] || '400';

}
// get_font_weight_css()

// -----------------------------------------------------------------------------

function get_text_align_css( tailwind_class )
{

	const alignments = {
		'text-start'	: 'start',
		'text-center'	: 'center',
		'text-end'		: 'end',
		'text-left'		: 'left',
		'text-right'	: 'right',
		'text-justify': 'justify',
	};

	return alignments[tailwind_class] || 'start';

}
// get_text_align_css()

// -----------------------------------------------------------------------------

// Convert Tailwind rounded corner classes to CSS

function get_border_radius_css( tailwind_class )
{

	const radius_map = {
		''						: '0',
		'rounded-sm'	: '2px',
		'rounded'			: '4px',
		'rounded-md'	: '6px',
		'rounded-lg'	: '8px',
		'rounded-xl'	: '12px',
		'rounded-2xl'	: '16px',
		'rounded-3xl'	: '24px',
		'rounded-full': '9999px',
	};

	return radius_map[tailwind_class] || '0';

}
// get_border_radius_css()

// -----------------------------------------------------------------------------

// Convert Tailwind shadow classes to CSS

function get_box_shadow_css( tailwind_class )
{

	const shadow_map = {
		''						: 'none',
		'shadow-sm'		: '0 1px 2px 0 rgba(0, 0, 0, 0.05)',
		'shadow'			: '0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1)',
		'shadow-md'		: '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1)',
		'shadow-lg'		: '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1)',
		'shadow-xl'		: '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1)',
		'shadow-2xl'	: '0 25px 50px -12px rgba(0, 0, 0, 0.25)',
		'shadow-inner': 'inset 0 2px 4px 0 rgba(0, 0, 0, 0.05)',
	};

	return shadow_map[tailwind_class] || 'none';

}
// get_box_shadow_css()

// -----------------------------------------------------------------------------

// Generate pattern styles

function get_pattern_style( pattern, background_color, use_gradient_effect, transparency = 1 )
{

	if ( ! pattern )
	{
		return {};
	}

	// ---------------------------------------------------------------------------

	let lighten					= false; // Could be dynamic based on color brightness
	let pattern_color		= generate_subtle_color(background_color, lighten, 5);
	let background_size	= '3em 3em';
	let background;

	// ---------------------------------------------------------------------------

	// Add transparency to pattern color if needed

	if ( transparency < 1 )
	{
		const hex			= pattern_color.replace('#', '');
		const r				= parseInt(hex.substr(0, 2), 16);
		const g				= parseInt(hex.substr(2, 2), 16);
		const b				= parseInt(hex.substr(4, 2), 16);
		pattern_color	= `rgba(${r}, ${g}, ${b}, ${transparency})`;
	}

	// ---------------------------------------------------------------------------

	// Gradient for additional effects

	let gradient;

	if ( use_gradient_effect )
	{
		const gradient_color = generate_subtle_color( background_color, false, 50 );

		if ( transparency < 1 )
		{
			const hex	= gradient_color.replace('#', '');
			const r		= parseInt(hex.substr(0, 2), 16);
			const g		= parseInt(hex.substr(2, 2), 16);
			const b		= parseInt(hex.substr(4, 2), 16);
			gradient	= `radial-gradient(rgba(0,0,0,0), rgba(${r}, ${g}, ${b}, ${transparency * 0.5}))`;
		}
		else
		{
			gradient = `radial-gradient(rgba(0,0,0,0), ${gradient_color})`;
		}
	}

	// ---------------------------------------------------------------------------

	// Helper function for repeating properties

	const repeat_property = (arr, property) => {

		const length = arr.length > 1 ? arr.length - 1 : arr.length;

		return Array(length).fill(property).join(',');

	};

	// ---------------------------------------------------------------------------

	switch ( pattern )
	{
		// source: https://superdesigner.co/tools/css-backgrounds - mixed 5
		case 'Pattern 1':

			background = [
				`linear-gradient(45deg,transparent 15%, ${pattern_color} 15%, ${pattern_color} 20%, transparent 20%, transparent 80%, ${pattern_color}  80%, ${pattern_color} 85%, transparent 85%)`,
				`linear-gradient(135deg, transparent 15%, ${pattern_color} 15%, ${pattern_color} 20%, transparent 20%, transparent 80%, ${pattern_color}  80%, ${pattern_color} 85%, transparent 85%)`,
				`radial-gradient(circle at top,transparent 9%, ${pattern_color} 10% ,${pattern_color} 15% , transparent 16%)`,
				`radial-gradient(circle at bottom,transparent 9%, ${pattern_color} 10% ,${pattern_color} 15% , transparent 16%)`,
				`radial-gradient(circle at right,transparent 9%, ${pattern_color} 10% ,${pattern_color} 15% , transparent 16%)`,
				`radial-gradient(circle at left,transparent 9%, ${pattern_color} 10% ,${pattern_color} 15% , transparent 16%)`,
				gradient,
			].filter( el => el );

			break;

		// -------------------------------------------------------------------------

		// source: https://superdesigner.co/tools/css-backgrounds - line 11
		case 'Pattern 2':

			background = [
				`linear-gradient(45deg,transparent 34%, ${pattern_color} 35%, ${pattern_color} 40%, transparent 41%, transparent 59%, ${pattern_color}  60%, ${pattern_color} 65%, transparent 66%)`,
				`linear-gradient(135deg,transparent 34%, ${pattern_color} 35%, ${pattern_color} 40%, transparent 41%, transparent 59%, ${pattern_color}  60%, ${pattern_color} 65%, transparent 66%)`,
				gradient,
			].filter( el => el );

			break;

		// -------------------------------------------------------------------------

		// source: https://superdesigner.co/tools/css-backgrounds - circle 22
		case 'Pattern 3':

			background = [
				`radial-gradient(${pattern_color} 15%, transparent 16%, transparent 49%, ${pattern_color} 50%, transparent 51%)`,
				`radial-gradient(circle at top left, ${pattern_color} 10%, transparent 10%, transparent 39%, ${pattern_color} 40%, transparent 41%)`,
				`radial-gradient(circle at top right, ${pattern_color} 10%, transparent 10%, transparent 39%, ${pattern_color} 40%, transparent 41%)`,
				`radial-gradient(circle at bottom left, ${pattern_color} 10%, transparent 10%, transparent 39%, ${pattern_color} 40%, transparent 41%)`,
				`radial-gradient(circle at bottom right, ${pattern_color} 10%, transparent 10%, transparent 39%, ${pattern_color} 40%, transparent 41%)`,
				gradient,
			].filter( el => el );

			break;

		// -------------------------------------------------------------------------

		// source: https://10015.io/tools/css-background-pattern-generator - fences
		case 'Pattern 4':

			background = [
				`radial-gradient(27% 29% at right, #0000 83%,${pattern_color} 85% 99%,#0000 101%) calc(32px/2) 32px`,
				`radial-gradient(27% 29% at left, #0000 83%,${pattern_color} 85% 99%,#0000 101%) calc(32px/-2) 32px`,
				`radial-gradient(29% 27% at top, #0000 83%,${pattern_color} 85% 99%,#0000 101%) 0 calc(32px/2)`,
				`radial-gradient(29% 27% at bottom, #0000 83%,${pattern_color} 85% 99%,#0000 101%) 0 calc(32px/-2)`,
				gradient,
			].filter( el => el );

			background_size = '64px 64px';

			break;

		// -------------------------------------------------------------------------

		// source: https://superdesigner.co/tools/css-backgrounds - cross 3
		case 'Diagonal Squares':

			background = [
				`linear-gradient(45deg, transparent 49%, ${pattern_color} 49% 51%, transparent 51%)`,
				`linear-gradient(-45deg, transparent 49%, ${pattern_color} 49% 51%, transparent 51%)`,
				gradient,
			].filter( el => el );

			break;

		// -------------------------------------------------------------------------

		// source: https://superdesigner.co/tools/css-backgrounds - circle14
		case 'Diagonal Squares With Dots':

			background = [
				`radial-gradient(${pattern_color} 15%, transparent 16%)`,
				`linear-gradient(45deg, transparent 49%, ${pattern_color} 49% 51%, transparent 51%)`,
				`linear-gradient(-45deg, transparent 49%, ${pattern_color} 49% 51%, transparent 51%)`,
				gradient,
			].filter( el => el );

			break;

		// -------------------------------------------------------------------------

		// source: https://superdesigner.co/tools/css-backgrounds - diamond2
		case 'Diamonds With Dots':

			background = [
				`radial-gradient(${pattern_color} 20%, transparent 21% ,transparent 79%, ${pattern_color} 80%)`,
				gradient,
			].filter( el => el );

			break;

		// -------------------------------------------------------------------------

		// source: https://superdesigner.co/tools/css-backgrounds - dot4
		case 'Diagonal Dots':

			background = [
				`radial-gradient(circle, ${pattern_color} 10%, transparent 11%)`,
				`radial-gradient(circle at bottom left, ${pattern_color} 5%, transparent 6%)`,
				`radial-gradient(circle at bottom right, ${pattern_color} 5%, transparent 6%)`,
				`radial-gradient(circle at top left, ${pattern_color} 5%, transparent 6%)`,
				`radial-gradient(circle at top right, ${pattern_color} 5%, transparent 6%)`,
				gradient,
			].filter( el => el );

			break;

		// -------------------------------------------------------------------------

		// source: https://10015.io/tools/css-background-pattern-generator - crossdots
		case 'Diagonal Small Dots':

			background = [
				`radial-gradient(${pattern_color} 2px, transparent 2px)`,
				`radial-gradient(${pattern_color} 2px, transparent 2px)`,
				gradient,
			].filter( el => el );

			return {
				background				: background.join(','),
				backgroundColor		: background_color,
				backgroundSize		: `${repeat_property( background, '32px 32px' )} ${gradient ? ', auto' : ''}`,
				backgroundRepeat	: `${repeat_property( background, 'repeat' )} ${gradient ? ', no-repeat' : ''}`,
				backgroundPosition: '0 0, 16px 16px, 0 0',
			};

		// -------------------------------------------------------------------------

		// source: https://10015.io/tools/css-background-pattern-generator - dot
		case 'Small Dots':

			background = [
				`radial-gradient(${pattern_color} 2px, transparent 2px)`,
				gradient,
			].filter( el => el );

			background_size = '32px 32px';

			break;
	}

	// ---------------------------------------------------------------------------

	if ( background )
	{
		return {
			background			: background.join(','),
			backgroundSize	: `${repeat_property(background, background_size)} ${gradient ? ', auto' : ''}`,
			backgroundRepeat: `${repeat_property(background, 'repeat')} ${gradient ? ', no-repeat' : ''}`,
		};
	}

	// ---------------------------------------------------------------------------

	return {};

}
// get_pattern_style()

// -----------------------------------------------------------------------------

function get_background_style( background )
{

	const styles = {
		backgroundColor: background.color, // Base background color
	};

	// ---------------------------------------------------------------------------

	// Add pattern if specified

	if ( background.pattern )
	{
		const pattern_styles = get_pattern_style(
			background.pattern,
			background.color,
			background.gradient_effect
		);

		Object.assign( styles, pattern_styles );
	}
	// @todo: should be added regardless with pattern or not
	else if ( background.gradient_effect )
	{
		// Simple gradient when no pattern

		const color2			= generate_subtle_color( background.color, false, 20 );
		styles.background	= `radial-gradient(ellipse at center, ${background.color} 0%, ${color2} 100%)`;
	}

	// ---------------------------------------------------------------------------

	return styles;

}
// get_background_style()

// -----------------------------------------------------------------------------

function get_content_box_background_style( content_box, transparency )
{

	const styles = {};

	// ---------------------------------------------------------------------------

	if ( ! content_box.background_color )
	{
		return styles;
	}

	// ---------------------------------------------------------------------------

	// Convert hex to rgba for transparency

	const hex	= content_box.background_color.replace('#', '');
	const r		= parseInt(hex.substr(0, 2), 16);
	const g		= parseInt(hex.substr(2, 2), 16);
	const b		= parseInt(hex.substr(4, 2), 16);

	// ---------------------------------------------------------------------------

	// Base background color with transparency

	const rgba_color = `rgba(${r}, ${g}, ${b}, ${transparency})`;
	styles.backgroundColor = rgba_color;

	// ---------------------------------------------------------------------------

	// Add pattern if specified

	if ( content_box.background_pattern )
	{
		const pattern_styles = get_pattern_style(
			content_box.background_pattern,
			content_box.background_color,
			content_box.background_gradient_effect,
			transparency
		);

		Object.assign( styles, pattern_styles );
	}
	else if ( content_box.background_gradient_effect )
	{
		// Simple gradient when no pattern

		const color2			= generate_subtle_color( content_box.background_color, false, 20 );
		const hex2				= color2.replace('#', '');
		const r2					= parseInt(hex2.substr(0, 2), 16);
		const g2					= parseInt(hex2.substr(2, 2), 16);
		const b2					= parseInt(hex2.substr(4, 2), 16);
		const rgba_color2	= `rgba(${r2}, ${g2}, ${b2}, ${transparency})`;

		styles.background	= `linear-gradient(135deg, ${rgba_color} 0%, ${rgba_color2} 100%)`;
	}

	// ---------------------------------------------------------------------------

	return styles;

}
// get_content_box_background_style()

// -----------------------------------------------------------------------------

function create_content_container(data)
{

	const container = document.createElement('div');

	// ---------------------------------------------------------------------------

	// Calculate scale factor for HD base

	const hd_width			= 1280;
	const scale_factor	= data.canvas.width / hd_width;
	const gap						= `${1 * scale_factor}em`;

	// ---------------------------------------------------------------------------

	// Container styles - full canvas with scaled margin

	const scaled_margin = Math.round( data.content_box.margin * scale_factor );

	container.style.cssText = `
		width: ${data.canvas.width}px;
		height: ${data.canvas.height}px;
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
		padding: ${scaled_margin}px;
		box-sizing: border-box;
		font-family: ${data.text.font_family}, sans-serif;
		position: relative;
		overflow: hidden;
	`;

	// ---------------------------------------------------------------------------

	// Apply background styles to container

	const bg_styles = get_background_style( data.background );

	Object.assign( container.style, bg_styles );

	// ---------------------------------------------------------------------------

	// Create content box if background color is set

	let content_wrapper;

	if ( data.content_box.background_color && data.content_box.transparency >= 0 )
	{
		content_wrapper = document.createElement('div');

		content_wrapper.className = 'content-box';

		// -------------------------------------------------------------------------

		// Content box styles - fill available space after margin, then apply scaled padding

		const scaled_padding = Math.round( data.content_box.padding * scale_factor );

		content_wrapper.style.cssText = `
			width: 100%;
			height: 100%;
			padding: ${scaled_padding}px;
			border-radius: ${get_border_radius_css(data.content_box.rounded_corners)};
			box-shadow: ${get_box_shadow_css(data.content_box.shadow)};
			box-sizing: border-box;
			display: flex;
			flex-direction: column;
			gap: ${gap};
		`;

		// -------------------------------------------------------------------------

		// Apply content box background

		const transparency_decimal = data.content_box.transparency / 100;

		const content_box_bg = get_content_box_background_style(
			data.content_box,
			transparency_decimal
		);

		Object.assign( content_wrapper.style, content_box_bg );

		// -------------------------------------------------------------------------

		container.appendChild( content_wrapper );
	}
	else
	{
		// No content box, use container directly with scaled padding

		const scaled_total_padding = Math.round( ( data.content_box.margin + data.content_box.padding ) * scale_factor );

		content_wrapper = container;

		content_wrapper.style.padding				= `${scaled_total_padding}px`;
		content_wrapper.style.display				= 'flex';
		content_wrapper.style.flexDirection	= 'column';
	}

	// ---------------------------------------------------------------------------

	// Create logo element if logo_url and logo_location are provided

	if ( data.brand && data.brand.logo_location && data.brand.logo_url )
	{
		const logo_element			= document.createElement('img');
		logo_element.className	= 'content-logo';
		logo_element.src				= data.brand.logo_url;

		// -------------------------------------------------------------------------

		// Calculate logo size

		const logo_size = Math.round( data.brand.logo_size * scale_factor );

		logo_element.style.cssText = `
			height: ${logo_size}px;
			width: auto;
			object-fit: contain;
			flex-shrink: 0;
		`;

		// -------------------------------------------------------------------------

		// Position logo in flex layout based on location

		if ( data.brand.logo_location.startsWith('top_') )
		{
			logo_element.style.order = '1';
		}
		else
		{
			logo_element.style.order = '3';
		}

		switch ( data.brand.logo_location )
		{
			case 'top_left':
			case 'bottom_left':

				logo_element.style.alignSelf = 'flex-start';

				break;

			// -----------------------------------------------------------------------

			case 'top_center':
			case 'bottom_center':

				logo_element.style.alignSelf = 'center';

				break;

			// -----------------------------------------------------------------------

			case 'top_right':
			case 'bottom_right':

				logo_element.style.alignSelf = 'flex-end';

				break;
		}

		// -------------------------------------------------------------------------

		content_wrapper.appendChild( logo_element );
	}

	// ---------------------------------------------------------------------------

	// Create text element

	const text_element			= document.createElement('div');
	text_element.className	= 'content-text';

	// ---------------------------------------------------------------------------

	// Use innerHTML to handle HTML content (like <br> tags)

	text_element.innerHTML = data.content;

	// @notes:
	// `hyphens: auto;` was removed because it wasn't applied to generated image
	// which caused the text to overflow in the cases of words split with hyphens
	//
	text_element.style.cssText = `
		color: ${data.text.color};
		text-align: ${get_text_align_css(data.text.alignment)};
		font-weight: ${get_font_weight_css(data.text.font_weight)};
		line-height: 1.4;
		width: 100%;
		word-wrap: break-word;
		word-break: break-word;
		display: flex;
		flex-direction: column;
		gap: 1em;
		justify-content: center;
		order: 2;
		flex: 1;
	`;

	// ---------------------------------------------------------------------------

	content_wrapper.appendChild( text_element );

	// ---------------------------------------------------------------------------

	// Create copyright text element if copyright_text is provided

	if ( data.brand && data.brand.copyright_text )
	{
		const copyright_element			= document.createElement('div');
		copyright_element.className	= 'content-copyright';
		copyright_element.innerHTML	= data.brand.copyright_text;

		// -------------------------------------------------------------------------

		// Calculate copyright font size relative to canvas

		const copyright_text_font_size = Math.round( data.brand.copyright_text_font_size * scale_factor );

		// -------------------------------------------------------------------------

		copyright_element.style.cssText = `
			font-size: ${copyright_text_font_size}px;
			color: ${data.text.color};
			text-align: center;
			width: 100%;
			flex-shrink: 0;
			order: 4;
			opacity: 0.7;
		`;

		// -------------------------------------------------------------------------

		content_wrapper.appendChild( copyright_element );
	}

	// ---------------------------------------------------------------------------

	return container;

}
// create_content_container()

// -----------------------------------------------------------------------------

// Main image generation function

APP.generate_content_image = async function( data )
{

	return new Promise( ( resolve, reject ) => {

		try {

			// Create container for the content

			const container = create_content_container( data );

			// -----------------------------------------------------------------------

			// Add to DOM temporarily (hidden)

			const temp_wrapper = document.createElement('div');

			temp_wrapper.style.cssText = `
				position: absolute;
				top: -9999px;
				left: -9999px;
				opacity: 0;
				pointer-events: none;
				width: ${data.canvas.width}px;
				height: ${data.canvas.height}px;
			`;

			document.body.appendChild( temp_wrapper );

			temp_wrapper.appendChild( container );

			// -----------------------------------------------------------------------

			// Wait for layout, then auto-resize text

			setTimeout(() => {

				const text_element = container.querySelector('.content-text');

				if ( text_element )
				{
					auto_resize_text(container, text_element, data.canvas.max_font_size);
				}

				// ---------------------------------------------------------------------

				// Wait a bit more for final layout, then generate image

				setTimeout(() => {

					const options = {
						width						: data.canvas.width,
						height					: data.canvas.height,
						useCORS					: true,
						allowTaint			: true,
						skipFonts				: false,
						pixelRatio			: 1, // Ensure consistent rendering
						//backgroundColor	: data.background.color,
						style						: get_background_style( data.background ),
					};

					// @todo: should chec get_background_style() and fix issues
					// Only add backgroundColor if no gradient effect
					//if ( ! data.background.gradient_effect && ! data.background.pattern )
					if ( ! data.background.gradient_effect )
					{
						options.backgroundColor = data.background.color;
					}

					toPng( container, options )
					.then( dataUrl => {

						document.body.removeChild( temp_wrapper );

						resolve( dataUrl );

					})
					.catch( error => {

						document.body.removeChild( temp_wrapper );

						reject( error );

					});

				}, 100 ); // Increased delay for complex layouts

			}, 100 ); // Increased delay for DOM insertion

		} catch ( error ) {

			reject( error );

		}

	});

};
// APP.generate_content_image()
