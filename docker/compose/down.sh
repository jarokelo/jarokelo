#!/bin/sh

#task:Stop and remove all containers, including unnamed volumes.

if [ "$1" = "--help" ]; then
    echo "Stop and remove all containers, including unnamed volumes."
    echo
    echo "Usage:"
    printf "\t%s\n" "$TASKRUNNER $TASK [options]"
    echo
    echo "Options:"
    printf "\t%s\t\t%s\n" "--keep" "Don't remove containers and volumes"
    exit 0
fi

docker-compose stop

if [ "$1" != "--keep" ]; then
    docker-compose rm -v
fi
