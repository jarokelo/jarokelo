#!/bin/sh

#task: Stop all containers.

if [ "$1" = "--help" ]; then
    echo "Stop all containers."
    echo
    echo "Usage:"
    printf "\t%s\n" "$TASKRUNNER $TASK"
    exit 0
fi

docker-compose stop
