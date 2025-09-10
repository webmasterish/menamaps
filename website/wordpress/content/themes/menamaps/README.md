# [MENA Maps](https://menamaps.com) WordPress theme

## Overview

MENA Maps WordPress theme.

## Development

### Local Development

```bash
# Start the development server
npm run dev

# Build assets for production
npm run build
```

### Development Standards

This project follows specific coding standards and uses a defined technology stack. Before contributing or developing, please review:

- [WordPress Technology Stack and Coding Standards](/docs/wordpress_tech_stack_and_coding_standards.md) - Guidelines for code formatting, naming conventions, and technology usage

## Theme Versioning

This theme uses a custom versioning system that updates both `package.json` and WordPress `style.css` files while maintaining proper Git tagging.

### Usage

```bash
# From repository root
npm run wp_theme_version

# From theme directory
npm run wp_theme_version
```

### Options

The versioning script supports several options:

```bash
# Increment type (patch by default)
npm run wp_theme_version --type=patch|minor|major

# Custom commit message
npm run wp_theme_version --message="Your commit message here"

# Skip pushing to remote repository
npm run wp_theme_version --no_push

# Automatically deploy after versioning
npm run wp_theme_version --deploy
```

You can combine multiple options:

```bash
npm run wp_theme_version --type=minor --message="Add new component" --deploy
```

### What the Script Does

1. Increments the version number in `package.json`
2. Updates the version number and date in `style.css`
3. Creates a Git commit with the changes
4. Creates a Git tag with the prefix `wp_theme-v` (e.g., `wp_theme-v0.1.0`)
5. Pushes changes and tags to the remote repository (unless `--no_push` is specified)
6. Optionally deploys the theme (if `--deploy` is specified)

### Example Workflow

```bash
# Start development
npm run dev

# Make changes to theme files...

# Build assets for production
npm run build

# Increment version and deploy
npm run wp_theme_version --type=minor --message="Add header component" --deploy
```

## Deployment

### Standard Deployment

```bash
# Deploy without bumping the version
npm run deploy

# Bump version and deploy
npm run wp_theme_version --deploy
```

### Deployment Script (`deploy.sh`)

The `deploy.sh` script is responsible for deploying the project to a production or staging server. The destination directory can be specified in three ways:

1. **As a Command-Line Argument**: If provided, this takes precedence.
2. **Via `deploy.config` File**: The script will read the destination directory from this file if present.
3. **Interactive Prompt**: As a fallback, if the script is not running in an automated environment, it will prompt for the destination.

### Configuring the Deployment Destination

1. **Using `deploy.config` File**:
   - Rename `deploy.config.example` to `deploy.config`.
   - Set your destination directory in `deploy.config`.
   - Ensure `deploy.config` is listed in `.gitignore` to avoid exposing sensitive information.

2. **Interactive Prompt**:
   - If `deploy.config` is not present and the script is not part of an automated process, it will prompt for the destination directory.

## Remote Logs Actions

This script provides various actions for managing logs on remote production server. By default, it downloads all logs. You can specify a different action by providing an argument when running the script.

**Note**:
The default action is `download_all_logs`. If no action is provided, the script will execute the default action.

### Usage

```bash
# Command
npm run remote_logs [action]

# ------------------------------------------------------------------------------

# Examples:

# Download all logs (default action download_all_logs)
npm run remote_logs

# Download server logs
npm run remote_logs download_server_logs

# Download theme logs
npm run remote_logs download_theme_logs

# List server logs
npm run remote_logs list_server_logs

# List theme logs
npm run remote_logs list_theme_logs

# View server error log
npm run remote_logs view_server_error_log

# View server access log
npm run remote_logs view_server_access_log

# View theme debug log
npm run remote_logs view_theme_debug_log

# Clear server error log
npm run remote_logs clear_server_error_log

# Clear server access log
npm run remote_logs clear_server_access_log

# Clear theme debug log
npm run remote_logs clear_theme_debug_log
```
