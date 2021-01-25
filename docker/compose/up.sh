#!/bin/sh

#task:Create and start all containers.

if [ "$1" = "--help" ]; then
    echo "Create and start all containers."
    echo
    echo "Usage:"
    printf "\t%s\n" "$TASKRUNNER $TASK [options]"
    echo
    echo "Options:"
    printf "\t%s\t%s\n" "--abort-on-container-exit" "Stops all containers if any container is stopped (default)."
    printf "\t\t\t\t\t%s\n" "Incompatible with -d."
    printf "\t%s\t\t\t%s\n" "--no-abort" "Do not stop all containers if any container is stopped."
    printf "\t%s\t\t\t\t%s\n" "-d" "Detached mode: run containers in background."
    printf "\t\t\t\t\t%s\n" "Incompatible with --abort-on-container-exit."
    printf "\t%s\t\t%s\n" "--force-recreate" "Recreate containers even if their configuration"
    printf "\t\t\t\t\t%s\n" "and image haven't changed."
    printf "\t\t\t\t\t%s\n" "Incompatible with --no-recreate."
    printf "\t%s\t\t\t%s\n" "--no-recreate" "If containers already exist, don't recreate them."
    printf "\t\t\t\t\t%s\n" "Incompatible with --force-recreate."
    printf "\t%s\t\t\t%s\n" "--no-build" "Don't build an image, even if it's missing."
    printf "\t%s\t\t\t\t%s\n" "--build" "Build images before starting containers."
    printf "\t%s\t\t\t%s\n" "--no-pull" "Don't pull images."
    exit 0
fi

abort="--abort-on-container-exit"
abort_on_failure=""
first=1
shouldpull=true
hasbuildparam=false
for arg in "$@"; do
    if [ "$arg" = "-d" ] || [ "$arg" = "--abort-on-container-exit" ]; then
        abort=""
    fi
    if [ "$arg" = "--no-pull" ]; then
        shouldpull=false
        if [ $first -eq 1 ]; then
            set --
            first=0
        fi
        continue
    fi
    if [ "$arg" = "--no-abort" ]; then
        abort_on_failure="false"
        abort=""
        if [ $first -eq 1 ]; then
            set --
            first=0
        fi
        continue
    fi
    if [ "$arg" = "--build" ]; then
        hasbuildparam=true
    fi
    if [ $first -eq 1 ]; then
        set -- "$arg"
        first=0
    else
        set -- "$@" "$arg"
    fi
done

case $TERM in
    xterm*)
        printf '\033]0;%s\007' "Docker: $PROJECT_NAME"
        ;;
esac

if [ "$shouldpull" = true ]; then
    embeddedpull=true # shellcheck disable=SC2034
    shouldbuild=false
    . "$TASKSDIR/pull.sh"
    if [ "$shouldbuild" = true ] && [ "$hasbuildparam" != true ]; then
        if [ $first -eq 1 ]; then
            set -- "--build"
            first=0
        else
            set -- "$@" "--build"
        fi
    fi
fi

wwwUid=$(id -u) wwwGid=$(id -g) DOCKER_ABORT_ON_FAILURE="$abort_on_failure" docker-compose up $abort "$@"
