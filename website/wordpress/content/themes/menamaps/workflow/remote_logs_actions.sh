#!/bin/bash

# usage: bash workflow/remote_logs_actions.sh or ./workflow/remote_logs_actions.sh

# ------------------------------------------------------------------------------

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )"

source "${SCRIPT_DIR}/remote_logs.config"

# make source directories are set
[ -z "$SERVER_LOGS_SOURCE_DIR" ] && echo -e "\n\u274c No Server logs source directory set. Aborting.\n" && exit 1
[ -z "$THEME_LOGS_SOURCE_DIR" ] && echo -e "\n\u274c No Theme logs source directory set. Aborting.\n" && exit 1

source "${SCRIPT_DIR}/helpers.sh"

# ------------------------------------------------------------------------------

# Supported Actions:
#
# download_all_logs (default)
# download_server_logs
# download_theme_logs
#
# list_server_logs
# list_theme_logs
#
# view_server_error_log
# view_server_access_log
# view_theme_debug_log
#
# clear_server_error_log
# clear_server_access_log
# clear_theme_debug_log

ACTION="${1:-download_all_logs}"

# ------------------------------------------------------------------------------

STARTED_AT="$(date '+%Y-%m-%d_%H-%M-%S')"
TIMER_START="`date +%s.%N`"

# ------------------------------------------------------------------------------

# functions

download_logs()
{

	local _src_name="${1:-server}"
	local _destination_dir=$(realpath "${SCRIPT_DIR}/../__/from_remote/${_src_name}_logs")
	local _source_dir="$SSH_USER:$SERVER_LOGS_SOURCE_DIR"

	[ "${_src_name}" = "theme" ] && _source_dir="$SSH_USER:$THEME_LOGS_SOURCE_DIR"

	# ----------------------------------------------------------------------------

	CMD=(
		"mkdir -p \"${_destination_dir}\" &&"
		"rsync"
		"-auvz"
		"--info=progress2"
		"$_source_dir"
		"$_destination_dir"
	)

	echo
	echo "Action : download_${_src_name}_logs"
	echo "Command:"
	echo -e "$(arr_join_by " \\ \n" "${CMD[@]}")\n"

	# ----------------------------------------------------------------------------

	eval "${CMD[@]}"

}
# download_logs()



list_logs()
{

	local _name="${1:-server}"
	local _dir="$SERVER_LOGS_SOURCE_DIR"

	[ "${_name}" = "theme" ] && _dir="$THEME_LOGS_SOURCE_DIR"

	# ----------------------------------------------------------------------------

	CMD=(
		"ssh -tt $SSH_USER"
		"ls -la  $_dir"
	)

	echo
	echo "Action : list_${_name}_logs"
	echo "Command:"
	echo -e "$(arr_join_by " \\ \n" "${CMD[@]}")\n"

	# ----------------------------------------------------------------------------

	eval "${CMD[@]}"

}
# list_logs()



view_server_log()
{

	local _name="${1:-error}"
	local _lines="${2:-10}"
	local _log_file=$(join_paths "$SERVER_LOGS_SOURCE_DIR" "${_name}.log")

	# ----------------------------------------------------------------------------

	CMD=(
		"ssh -tt $SSH_USER"
		"tail -n $_lines $_log_file"
	)

	echo
	echo "Action : view_server_${_name}_log"
	echo "Command:"
	echo -e "$(arr_join_by " \\ \n" "${CMD[@]}")\n"

	# ----------------------------------------------------------------------------

	eval "${CMD[@]}"

}
# view_server_log()



view_theme_log()
{

	local _name="${1:-debug}"
	local _lines="${2:-10}"
	local _log_file=$(join_paths "$THEME_LOGS_SOURCE_DIR" "${_name}.log")

	# ----------------------------------------------------------------------------

	CMD=(
		"ssh -tt $SSH_USER"
		"tail -n $_lines $_log_file"
	)

	echo
	echo "Action : view_theme_${_name}_log"
	echo "Command:"
	echo -e "$(arr_join_by " \\ \n" "${CMD[@]}")\n"

	# ----------------------------------------------------------------------------

	eval "${CMD[@]}"

}
# view_theme_log()



clear_server_log()
{

	local _name="${1:-error}"
	local _log_file=$(join_paths "$SERVER_LOGS_SOURCE_DIR" "${_name}.log")

	# ----------------------------------------------------------------------------

	CMD=(
		"ssh -tt $SSH_USER"
		"\"sudo bash -c 'echo > ${_log_file}'\""
	)

	echo
	echo "Action : clear_server_${_name}_log"
	echo "Command:"
	echo -e "$(arr_join_by " \\ \n" "${CMD[@]}")\n"

	# ----------------------------------------------------------------------------

	eval "${CMD[@]}"

}
# clear_server_log()



clear_theme_log()
{

	local _name="${1:-debug}"
	local _log_file=$(join_paths "$THEME_LOGS_SOURCE_DIR" "${_name}.log")

	# ----------------------------------------------------------------------------

	CMD=(
		"ssh -tt $SSH_USER"
		"\"bash -c 'echo > ${_log_file}'\""
	)

	echo
	echo "Action : clear_theme_${_name}_log"
	echo "Command:"
	echo -e "$(arr_join_by " \\ \n" "${CMD[@]}")\n"

	# ----------------------------------------------------------------------------

	eval "${CMD[@]}"

}
# clear_theme_log()

# ------------------------------------------------------------------------------

case $ACTION in

	download_server_logs )

		download_logs "server"

		;;

	# ----------------------------------------------------------------------------

	download_theme_logs )

		download_logs "theme"

		;;

	# ----------------------------------------------------------------------------

	download_all_logs )

		download_logs "server"

		download_logs "theme"

		;;

	# ----------------------------------------------------------------------------

	list_server_logs )

		list_logs "server"

		;;

	# ----------------------------------------------------------------------------

	list_theme_logs )

		list_logs "theme"

		;;

	# ----------------------------------------------------------------------------

	view_server_error_log )

		view_server_log "error"

		;;

	# ----------------------------------------------------------------------------

	view_server_access_log )

		view_server_log "access"

		;;

	# ----------------------------------------------------------------------------

	view_theme_debug_log )

		view_theme_log "debug"

		;;

	# ----------------------------------------------------------------------------

	clear_server_error_log )

		clear_server_log "error"

		;;

	# ----------------------------------------------------------------------------

	clear_server_access_log )

		clear_server_log "access"

		;;

	# ----------------------------------------------------------------------------

	clear_theme_debug_log )

		clear_theme_log "debug"

		;;

	# ----------------------------------------------------------------------------

	* )

		echo -e "\n\u274c Unsupported action '$ACTION'. Aborting.\n" && exit 1

		;;

esac

# ------------------------------------------------------------------------------

[ $? -eq 0 ] && echo -e "\n\u2714 Action '$ACTION' executed with no errors\n" || exit $?

# ------------------------------------------------------------------------------

TIMER_STOP="`date +%s.%N`"
TIMER_TOTAL=$(echo "${TIMER_STOP} - ${TIMER_START}" | bc)

echo -e "Performed in: ${TIMER_TOTAL} seconds\n"
