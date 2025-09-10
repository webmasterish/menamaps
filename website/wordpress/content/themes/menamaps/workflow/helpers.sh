#!/bin/bash

# usage: source helpers.sh

# ------------------------------------------------------------------------------

is_automated()
{

	# This can be as simple or complex as needed, depending on environment
	# For example, checking for a CI environment variable which many CI/CD sets

	[ -n "$CI" ]

}
# is_automated()


arr_join_by()
{

	local d=$1

	shift

	echo -n "$1"

	shift

	printf "%s" "${@/#/$d}"

}
# arr_join_by()


join_paths()
{

	# using parameter expansion
	# - remove any trailing slashes from the first argument ${1%/}
	# - remove any leading slashes from the second argument ${2#\/}

	echo "${1%/}/${2#\/}";

}
