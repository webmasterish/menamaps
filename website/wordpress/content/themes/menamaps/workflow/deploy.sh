#!/bin/bash

# usage: bash workflow/deploy.sh or ./workflow/deploy.sh

# ------------------------------------------------------------------------------

# source directory

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )"
THEME_DIR=$(realpath "${SCRIPT_DIR}/..")

[ ! -d "${THEME_DIR}" ] && echo -e "\n\u274c Source Directory '${THEME_DIR}' Doesn't Exist. Aborting.\n" && exit 1

# ------------------------------------------------------------------------------

source "${SCRIPT_DIR}/helpers.sh"

[ -f "${SCRIPT_DIR}/deploy.config" ] && source "${SCRIPT_DIR}/deploy.config"

# ------------------------------------------------------------------------------

# destination directory

# Use the first argument as DEST_DIR if provided
[ -n "$1" ] && DEST_DIR=$1

# If DEST_DIR is still not set
# and the script is not running in an automated environment, prompt the user
[ -z "$DEST_DIR" ] && ! is_automated && read -p "Enter the destination directory: " DEST_DIR

# Check if DEST_DIR is set
[ -z "$DEST_DIR" ] && echo -e "\n\u274c No destination directory set. Aborting.\n" && exit 1

# Set dir to realpath
DEST_DIR="$(realpath ${DEST_DIR})"

# ------------------------------------------------------------------------------

STARTED_AT="$(date '+%Y-%m-%d_%H-%M-%S')"
TIMER_START="`date +%s.%N`"

# ------------------------------------------------------------------------------

if [ -z "${RSYNC_DRY_RUN}" ]; then

	# Build assets locally (if needed)

	cd "${THEME_DIR}"

	[ -n "${NPM_RUN_INSTALL}" ] && npm install

	[ -n "${NPM_RUN_BUILD}" ] && npm run build

	# ----------------------------------------------------------------------------

	# PHP dependencies with composer

	if [ -n "${COMPOSER_RUN_INSTALL}" ]; then

		# Navigate to the directory containing composer.json and install PHP dependencies
		cd "${THEME_DIR}/includes/lib" && composer install

		# Return to theme directory
		cd "${THEME_DIR}"

	fi

fi

# ------------------------------------------------------------------------------

CMD=(
	"rsync"
	"-auvz"
	"--info=progress2"
	# this would only printout errors making it easier to understand what happened
	# details are saved to log file so no need to print it out
	"--quiet"
)

# ------------------------------------------------------------------------------

[ -n "${RSYNC_DRY_RUN}" ] && CMD+=("--dry-run")

# ------------------------------------------------------------------------------

EXCLUDE_FILE="${SCRIPT_DIR}/rsync_exclude"

[ -f "${EXCLUDE_FILE}" ] && CMD+=("--exclude-from=\"${EXCLUDE_FILE}\"")

# ------------------------------------------------------------------------------

LOG_FILE="${SCRIPT_DIR}/logs/deploy_rsync_${STARTED_AT}.log"

if [ -n "${LOG_FILE}" ]; then

	mkdir -p "$(dirname ${LOG_FILE})"

	CMD+=("--log-file=\"${LOG_FILE}\"")

fi

# ------------------------------------------------------------------------------

CMD+=("\"${THEME_DIR}\"")
CMD+=("\"${DEST_DIR}\"")

# ------------------------------------------------------------------------------

echo
echo "command to be executed:"
echo -e "$(arr_join_by " \\ \n" "${CMD[@]}")\n"

eval "${CMD[@]}"

[ $? -eq 0 ] && echo -e "\u2714 command executed with no errors\n" || exit $?

# ------------------------------------------------------------------------------

[ -n "${LOG_FILE}" ] && echo -e "log file: ${LOG_FILE}\n"

# ------------------------------------------------------------------------------

TIMER_STOP="`date +%s.%N`"
TIMER_TOTAL=$(echo "${TIMER_STOP} - ${TIMER_START}" | bc)

echo -e "Performed in: ${TIMER_TOTAL} seconds\n"
