#!/bin/sh

#task:List port mappings for services.

if [ "$1" = "--help" ]; then
    echo "List port mappings for services."
    echo
    echo "Usage:"
    printf "\t%s\n" "$TASKRUNNER $TASK"
    exit 0
fi

IFS='
'
services=$(docker-compose config --services | tr -d '\r')
for service in $services; do
    containers=$(docker-compose ps -q "$service" | tr -d '\r')
    echo "$service:";
    for container in $containers; do
        ports=$(docker port "$container" | tr -d '\r')
        for port in $ports; do
            printf '\t%s\n' "$(echo "$port" | sed "s/0.0.0.0/127.0.0.1/")"
        done
    done
done
