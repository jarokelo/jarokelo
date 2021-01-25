#!/bin/sh

#task:Start a shell in the web container.

if [ "$1" = "--help" ]; then
    echo "Start a shell or execute a command in the web container as www-data."
    echo
    echo "Usage:"
    printf "\t%s\n" "$TASKRUNNER $TASK [options] [command]"
    echo
    echo "Options:"
    printf "\t%s\t\t%s\n" "--root" "run as root, not www-data"
    exit 0
fi

user="www-data"
shelltype=""

# We use /bin/su instead of the -u option because -u doesn't set some enviornment
# variables, such as USER and SHELL.
# /bin/su - www-data would simulate a full login and execute ~/.profile, but
# it would also clear the docker environment variables which we need.
# /bin/su -lm www-data would preserve USER and HOME, which is bad.
if [ "$1" = "--root" ]; then
    shift 1
    user="root"
    shelltype="root "
fi

title='PROMPT_COMMAND="printf '"'"'\033]0;%s\007'"'"' \"Docker '"$shelltype"'shell: '"$PROJECT_NAME"'\""'
command="env TERM=xterm $title su $user"

container=$(docker-compose ps -q web | tr -d '\r')
command="docker exec -it $container $command"

if [ $# -gt 0 ]; then
    # shellcheck disable=SC2145
    eval "$command -c \"$@\""
else
    eval "$command"
fi
