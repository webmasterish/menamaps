# DotAim Browser Screenshot Class

A PHP class for capturing screenshots of webpages or HTML content using headless Chrome/Chromium browser.

## Overview

The `Screenshot` class provides functionality to capture screenshots from HTML content or URLs using headless browsers. It's designed to work within a WordPress environment but can be adapted for other PHP applications.

## Features

- Capture screenshots from URLs or raw HTML content
- Configurable browser settings (width, height, user agent)
- Timeout management
- Browser process cleanup
- Support for Chrome/Chromium browser (extendable for other browsers)

## Requirements

- PHP 7.0+
- Chrome or Chromium browser installed on the server
- WordPress environment (for WP_Error handling)

## Installation

### 1. Install Chrome/Chromium on Ubuntu 20.04

```bash
# Update package lists
sudo apt update

# Install Chromium browser
sudo apt install -y chromium-browser

# Alternatively, install Google Chrome
# First, download the package
wget https://dl.google.com/linux/direct/google-chrome-stable_current_amd64.deb

# Install the package
sudo apt install -y ./google-chrome-stable_current_amd64.deb
```

### 2. Create Required Directories

Chrome/Chromium requires certain directories to store user data when running. Create these directories and ensure proper ownership if you're running the browser under a web server user (e.g., www-data):

```bash
# Create necessary directories and set ownership and permissions

CHROME_DIRS="/var/www/.local /var/www/.config /var/www/.cache /var/www/.pki" && \
sudo mkdir -p ${CHROME_DIRS} && \
sudo chown -R www-data:www-data ${CHROME_DIRS} && \
sudo chmod -R 755 ${CHROME_DIRS}
```

## Usage

```php
use DotAim\Browser\Screenshot;

// Capture a screenshot from a URL
$screenshot_path = Screenshot::save(
    'https://example.com',
    '/path/to/save/screenshot.png',
    [
        'width' => 1366,
        'height' => 768,
        'timeout' => 30000
    ]
);

// Capture a screenshot from HTML content
$html_content = '<html><body><h1>Hello World</h1></body></html>';
$screenshot_path = Screenshot::save(
    $html_content,
    '/path/to/save/screenshot.png'
);

// Check for errors
if (is_wp_error($screenshot_path)) {
    echo $screenshot_path->get_error_message();
} else {
    echo "Screenshot saved to: $screenshot_path";
}
```

## Configuration Options

The `save()` method accepts the following options in the third parameter:

| Option | Default | Description |
|--------|---------|-------------|
| `browser` | 'chrome' | Browser to use (currently only 'chrome' is supported) |
| `browser_bin_paths` | null | Custom paths to browser binaries |
| `flags` | Array of flags | Chrome command line flags |
| `width` | 1366 | Viewport width in pixels |
| `height` | 768 | Viewport height in pixels |
| `user_agent` | Chrome UA | User agent string |
| `timeout` | 30000 | Timeout in milliseconds |
| `refresh` | false | Whether to regenerate existing screenshots |

## Troubleshooting

### Common Issues

1. **Browser binary not found**:
   - Verify Chrome/Chromium is installed using `which chromium-browser` or `which google-chrome`
   - Customize browser paths using the `browser_bin_paths` option

2. **Permission errors**:
   - Ensure the web server user has proper permissions to execute the browser
   - Check that the required directories exist with correct ownership

3. **Screenshot generation fails**:
   - Check browser console output in the error message
   - Look for sandbox-related issues (may require `--no-sandbox` flag)
   - Ensure enough memory is available for browser processes

### Browser Process Management

The class automatically attempts to clean up any hanging browser processes when an error occurs. If you encounter issues with orphaned browser processes, you may need to manually terminate them:

```bash
# Kill all Chrome processes
pkill -f chrome
pkill -f chromium
```

## Additional Resources

- [Headless Chrome Documentation](https://developer.chrome.com/blog/headless-chrome/)
- [Chrome DevTools Protocol](https://chromedevtools.github.io/devtools-protocol/)
- [Chrome for Testing: reliable downloads for browser automation](https://developer.chrome.com/blog/chrome-for-testing/)
- [Chrome for Testing availability](https://googlechromelabs.github.io/chrome-for-testing/)
- [Command Line Arguments for Chrome](https://peter.sh/experiments/chromium-command-line-switches/)
- [List of Chromium Command Line Switches](https://gist.github.com/dodying/34ea4760a699b47825a766051f47d43b)
- [Puppeteer Documentation](https://pptr.dev/) (Node.js library for headless Chrome, useful reference)

## Advanced Usage

### Custom Browser Binary Paths

```php
$screenshot_path = Screenshot::save(
    'https://example.com',
    '/path/to/save/screenshot.png',
    [
        'browser_bin_paths' => [
            '/usr/local/bin/chromium',
            '/opt/google/chrome/chrome'
        ]
    ]
);
```

### Custom Chrome Flags

```php
$screenshot_path = Screenshot::save(
    'https://example.com',
    '/path/to/save/screenshot.png',
    [
        'flags' => [
            '--headless',
            '--disable-gpu',
            '--no-sandbox',
            '--disable-dev-shm-usage'
        ]
    ]
);
```
