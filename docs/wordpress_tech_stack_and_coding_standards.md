# MENAmaps WordPress Technology Stack and Coding Standards

## Introduction

This document defines the coding standards and technology stack for the MENAmaps project. These standards ensure consistent code style across all components and languages used in the project. AI code generation should follow these standards exactly to maintain code quality and readability.

## Technology Stack

### Backend Technologies

- **WordPress 6.8+**: Base CMS platform
- **PHP 7.4+**: Primary backend language
- **MySQL 8.0+**: Database

### Backend Architecture

- **Custom WordPress Theme**: All application code is implemented within a custom theme
- **Singleton Pattern**: Core components follow a lazy-loaded singleton pattern
- **Namespace Structure**: `DotAim\MENAmaps\*` for all MENAmaps related classes and functionalities
- **Component Hierarchy**: Parent-child relationships with parent references

### Frontend Technologies

- **TailwindCSS 3.4+**: Primary CSS framework for styling
  - Dark mode support with `.dark:` variants
  - Responsive design patterns
  - Custom color scheme (primary, secondary) using CSS variables
- **Flowbite 2.5+**: Component library built on TailwindCSS
  - Form inputs, buttons, cards, modals, etc.
  - Typography plugin for content styling

### JavaScript Libraries

- **HTMX 2.0+**: Used for dynamic page updates without full reloads
  - Form submissions with `hx-post`
  - Page transitions with `hx-get` and `hx-swap`
  - Loading indicators with `hx-indicator`
- **Alpine.js 3.14+**: Lightweight framework for component interactivity
  - State management with `x-data`
  - Conditional rendering with `x-show`
  - Event handling with `@click`
  - Attribute binding with `:attribute`
  - Sort plugin for data sorting

### Build Tools

- **Vite 5.4+**: Modern build tool for fast development and optimized production builds
- **PostCSS 8.4+**: CSS processing with plugins:
  - Autoprefixer for browser compatibility
  - PostCSS Nested for nesting support
- **Live Reload**: Automatic browser refresh during development

### TailwindCSS Color Configuration

The project uses a customized color scheme with:
- `primary`: Base colors for general UI elements (neutrals)
- `secondary`: Accent colors for buttons, links, and interactive elements

Colors are implemented using CSS variables for theme customization and are defined in `tailwind.config.js`:

```js
colors: {
  primary: {
    50: 'rgb(var(--color-primary-50) / <alpha-value>)',
    // ... other shades
    950: 'rgb(var(--color-primary-950) / <alpha-value>)',
  },
  secondary: {
    50: 'rgb(var(--color-secondary-50) / <alpha-value>)',
    // ... other shades
    950: 'rgb(var(--color-secondary-950) / <alpha-value>)',
  },
}
```

When using TailwindCSS color classes, prefix with the appropriate namespace:
- `bg-primary-50` to `bg-primary-950`
- `text-secondary-500` to `text-secondary-600`
- Include `dark:` variants for dark mode support (e.g., `dark:bg-primary-800`)

## Naming Conventions

### General Rule
Use `snake_case` for all identifiers across all languages (PHP, JavaScript, CSS, Bash, etc.)

**Examples:**
- Variables: `current_user`, `api_endpoint`, `is_active`
- Functions: `get_user_data()`, `calculate_total()`, `handle_click()`
- Files: `index.php`, `utility_functions.js`, `main_styles.css`

### Class Naming
- Use capitalized `Snake_Case` with the same name for file and class
- One class per file

**Examples:**
```php
// File: Class_Name.php
class Class_Name
{

	// ...

}
// class Class_Name
```

```js
// File: User_Manager.js
class User_Manager
{

	// ...

}
// class User_Manager
```

## Indentation & Code Formatting

### Tabs & Indentation
- Use **tabs** for indentation (1 tab per level)
- Set IDE tab size to **2 spaces** for visual consistency
- Indent all nested structures

### Function & Class Structure
- Add an empty line after opening bracket
- Add an empty line before closing bracket
- Add comment with function/class name after closing bracket for clarity
- One class per file

### Spacing in Control Structures
- Add spaces inside parentheses of control structures (if, while, foreach)

### Array & Object Syntax
- Use `[]` instead of `array()` in PHP for consistency with JavaScript
- Align array element values with tabs

### Formatting Examples
```php
// PHP function
function process_user_data()
{

	if ( $user->is_valid() )
	{
		$processed_data = [
			'user_id'	=> $user->get_id(),
			'status'	=> 'active',
		];
	}

}
// process_user_data()

// PHP class
class User_Manager
{

	public function get_user()
	{

		// implementation

	}
	// get_user()

}
// class User_Manager
```

```js
// JS function
function process_user_data()
{

	if ( user.is_valid() )
	{
		let processed_data = {
			user_id	: user.get_id(),
			status	: 'active',
		};
	}

}
// process_user_data()
```

## Line Length & Breaking

### Line Length
- Maximum **80 characters** per line

### Breaking Long Lines

**Function Parameters:**
- Prefer $options or $args arrays instead of many parameters
- Keep parameters on one line when possible for clarity, focus on function body

**Function Calls:**
```php
$result = $user_manager->process_user_data(
	$current_user_id,
	'active',
	$user_preferences,
	$additional_options
);
```

**Long Conditionals:**
- Break with operators at the start of new line
- Indent to visually distinguish from function body
```php
if (		$user->is_valid()
		 && $user->has_permission()
		 && $status === 'active' )
{
	// implementation
}
```

**Chained Methods:**
- Align arrows with the equals sign
```php
$result = $query_builder
				->select('*')
				->from('users')
				->where('status', 'active')
				->order_by('created_at', 'DESC')
				->execute();
```

**Arrays:**
- Break elements across lines without extra indentation
```php
$data = [
	'very_long_key_name'		=> 'value1',
	'another_long_key_name'	=> 'value2',
	'even_longer_key_name'	=> 'value3',
];
```

## Spacing

### General Spacing Rules

**Around Operators:**
```php
// Align equals signs when listing variables
$total		= $a + $b;
$is_valid	= $user && $active;
$result		= $value === 'expected';
```

**Commas:**
```php
function process_data( $arg1, $arg2, $arg3 )
{
	// Space after comma only
}

$array = [
	'first',
	'second',
	'third',
];

$result = function_call( $param1, $param2, $param3 );
```

**Around Parentheses/Brackets/Braces:**
```php
// Space inside control structure parentheses
if ( $condition )
{
	// implementation
}

// Space in function calls
function_name( $param1, $param2 );

// Space around array brackets
$value = $array[ $key ];

// Space inside array brackets
$items = [ 1, 2, 3 ];
```

**Variable Interpolation:**
```php
// Double quotes with variables
echo "Hello {$name}";

// Single quotes otherwise
echo 'Hello World';
```

## Bracket Placement

### Opening Brackets
- Always place opening braces `{` on new line
- Applies to: functions, classes, control structures (if, for, while, etc.)

```php
function process_data()
{

	// implementation

}
// process_data()

class User_Manager
{

	// properties and methods

}
// class User_Manager

if ( $condition )
{
	// code block
}

for ( $i = 0; $i < $count; $i++ )
{
	// loop body
}
```

### Closing Brackets
- Matching closing bracket on its own line
- Include identifier comment after closing bracket

```php
function process_data()
{

	// function body

}
// process_data()

class User_Manager
{

	// class body

}
// class User_Manager
```

## Quote Usage

### Quote Selection Rules

**Single Quotes:**
- Default choice for all strings
- Use when no variable interpolation needed

```php
// PHP
$message	= 'Hello World';
$sql			= 'SELECT * FROM users WHERE status = "active"';

// JavaScript
const title			= 'My Awesome Title';
const selector	= '.button_primary';
```

**Double Quotes:**
- Use in PHP when variable interpolation is needed
- Always use for HTML attributes

```php
// PHP - with variable interpolation
$greeting	= "Hello {$username}";
$query		= "SELECT * FROM {$table_name} WHERE id = {$id}";

// HTML attributes
<div class="container" id="main-content">
<button data-action="submit" value="process">Submit</button>
```

### Template Literals (JavaScript)
Template literals (backticks) are used for:
- Multi-line strings
- String interpolation with variables
- HTML template generation

```js
// Multi-line strings
const html = `
	<div class="component">
		<h2>${title}</h2>
		<p>${content}</p>
	</div>
`;

// Variable interpolation
const message = `User ${user_name} has ${points} points`;
```

## Semicolons and Commas

### Usage Rules
- Semicolon required after every statement in both PHP and JavaScript
- Comma required after last item in arrays and objects

**PHP:**
```php
$total = $a + $b;

return $result;

$data = [
	'first'		=> 'value1',
	'second' 	=> 'value2',
	'third'		=> 'value3',
];
```

**JavaScript:**
```js
let total = a + b;

return result;

const data = {
	first	: 'value1',
	second: 'value2',
	third	: 'value3',
};

const items = [
	'first',
	'second',
	'third',
];
```

## Code Structure & Best Practices

### Early Returns
- Always return, continue, or break early to reduce nesting
- Handle edge cases first

```php
function process_user( $user )
{

	if ( ! $user->is_valid() )
	{
		return false;
	}

	// ---------------------------------------------------------------------------

	if ( $user->is_banned() )
	{
		return 'banned';
	}

	// ---------------------------------------------------------------------------

	// main logic here

}
// process_user()
```

### Nesting Limits
- Maximum 3 nesting levels
- Refactor deeper nesting using functions or early returns

```php
// Good - refactored to avoid deep nesting
function check_user_permissions( $user, $action )
{

	if ( ! $user->is_authenticated() )
	{
		return false;
	}

	// ---------------------------------------------------------------------------

	return $this->validate_action( $user, $action );

}
// check_user_permissions()
```

### Code Block Separation
- Use `// --------------------` to separate logical blocks
- Add `-` until reaching column 80
- Preceded and followed by empty lines
- Makes code flow and sections clear

```php
public function get_meta_tag( $url, $key, $cache_time = null, $args = [] )
{

	$meta_tags = $this->get_meta_tags( $url, $cache_time, $args );

	if ( empty( $meta_tags ) || is_wp_error( $meta_tags ) )
	{
		return;
	}

	// ---------------------------------------------------------------------------

	if ( ! is_array( $key ) )
	{
		return isset( $meta_tags[ $key ] ) ? $meta_tags[ $key ] : null;
	}

	// ---------------------------------------------------------------------------

	// that means it's an array of keys based on priority

	foreach ( $key as $priority_key )
	{
		if ( ! empty( $meta_tags[ $priority_key ] ) )
		{
			return $meta_tags[ $priority_key ];
		}
	}

}
// get_meta_tag()
```

### Descriptive Naming
- Variables and functions should be self-explanatory
- Avoid abbreviated names unless widely understood
- Name should describe purpose/content clearly

```php
// Good - descriptive naming
$user_authentication_status				= check_user_permissions();
$database_connection_retry_count	= 3;

function calculate_monthly_subscription_total( $users )
{

	// clear purpose from name alone

}
// calculate_monthly_subscription_total()

// Avoid - unclear naming
$st	= check();
$c	= 3;
```

## File Structure

The project follows this file structure:

```
app/wordpress/content/themes/menamaps/
├── assets/
│   ├── build/
│   ├── images/
│   └── src/
│       ├── js/
│       └── css/
├── includes/
│   └── DotAim/
│       ├── Base/
│       │   ├── Singleton.php
│       │   └── ... (other base classes)
│       ├── MENAmaps/
│       │   └── MENAmaps.php
│       ├── DotAim.php (main core class)
│       └── ... (other core components)
├── template_parts/
├── workflow/
└── ...
```

## Conclusion

These coding standards and technology stack reference ensure consistent, readable, and maintainable code across the project. All developers (including AI tools) should strictly follow these standards to maintain uniformity and quality in the codebase.

When generating or modifying code for MENAmaps, ensure that:
1. Coding style follows all formatting standards
2. Uses appropriate technologies for frontend (TailwindCSS, Flowbite, HTMX, Alpine.js)
3. Follows established patterns (singleton, lazy loading)
4. Maintains the hierarchical structure and naming conventions
5. All UI supports both light and dark modes
6. Security considerations are properly implemented
