#!/bin/sh

old_path=$(pwd)
cd $(dirname "${0}")
script_path=$(pwd)
cd "$old_path"

__ORIG_PATH="$PATH"
PATH="$script_path:$PATH"
export PATH
export __ORIG_PATH

vendor/bin/phpcs "$@"
