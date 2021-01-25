#!/bin/sh

#task:Pull images.

if [ "$1" = "--help" ]; then
    echo "Pull images for services."
    echo
    echo "Usage:"
    printf "\t%s\n" "$TASKRUNNER $TASK"
    echo
    exit 0
fi

buildneeded=false

handleDockerfile() {
    echo "${GREEN}Examining ${1}${NORMAL}"
    echo ""
    escapechar='\\'
    continuation=''
    while read -r line; do
        escape=$(echo "$line" | tr '[:upper:]' '[:lower:]' | sed -n 's/#[[:space:]]*escape[[:space:]]*=[[:space:]]*\(.*\)/\1/p')
        if [ -n "$escape" ]; then
            # escape the escape character for use in sed
            escapechar=$(echo "$escape" | sed 's/\([]$.*/[\\^]\)/\\\1/g')
            continue
        fi
        if echo "$line" | grep -q "$escapechar[[:space:]]*$"; then
            line=${line%$escapechar}
            continuation="${continuation}${line}"
            continue
        else
            line="${continuation}${line}"
            continuation=''
        fi

        line=$(echo "$line" | sed 's/[[:space:]]\{1,\}/ /g')
        line=${line% }
        line=${line# }
        imagename=$(echo "$line" | sed -n 's/[Ff][Rr][Oo][Mm][[:space:]]\{1,\}\([^[:space:]]\{1,\}\)/\1/p')
        if [ -n "$imagename" ]; then
            echo "${GREEN}Pulling ${imagename}${NORMAL}"
            oldid=$(docker images -q "$imagename")
            if ! docker pull "$imagename"; then
                echo "${RED}Failed to pull ${imagename}${NORMAL}"
            fi
            # Figure out if image has changed
            newid=$(docker images -q "$imagename")
            if [ "$oldid" != "$newid" ]; then
                buildneeded=true
            fi
        fi
    done < "$1"
}

handleService() {
    if [ "$hasbuild" = false ] && [ "$hasimage" = true ]; then
        echo "${GREEN}Pulling ${imagename}${NORMAL}"
        if ! docker pull "$imagename"; then
            echo "${RED}Failed to pull ${imagename}${NORMAL}"
        fi
        echo ""
    elif [ "$hasbuild" = true ]; then
        handleDockerfile "$buildcontext/$buildfile"
    fi
}

# docker-compose config gives a machine-readable, formatted config file,
# with comments removed and whitespace normalized.

inservices=false
hasbuild=false
hasimage=false
inbuild=false
buildcontext='.'
buildfile='Dockerfile'
imagename=''
IFS='
'
for line in $(docker-compose config | tr -d '\r'); do
    if [ "$line" = "services:" ]; then
        inservices=true
        continue
    fi
    if [ "$inservices" = false ]; then
        continue
    fi
    # in services

    left=false
    # check if we've left services
    indentationless=${line#"${line%%[![:space:]]*}"}
    indentation=$((${#line}-${#indentationless}))
    if [ $indentation -eq 0 ]; then
        left=true
        inservices=false
    fi

    # check if we've entered a new service
    if [ $indentation -eq 2 ]; then
        left=true
    fi

    if [ "$left" = true ]; then
        handleService

        hasbuild=false
        hasimage=false
        inbuild=false
        buildcontext='.'
        buildfile='Dockerfile'
        imagename=''
        continue
    fi

    # check if we've left build

    if [ "$inbuild" = true ]; then
        if [ $indentation -lt 6 ]; then
            inbuild=false
        fi
    fi

    if [ "$inbuild" = true ]; then
        context2=${indentationless#context\:}
        dockerfile2=${indentationless#dockerfile\:}
        context2=${context2#"${context2%%[![:space:]]*}"}
        dockerfile2=${dockerfile2#"${dockerfile2%%[![:space:]]*}"}
        if [ $indentation -eq 6 ]; then
            if [ "$context2" != "$indentationless" ]; then
                buildcontext="$context2"
            fi
            if [ "$dockerfile2" != "$indentationless" ]; then
                buildfile="$dockerfile2"
            fi
        fi
        continue
    fi

    # check if this service has build
    # TODO: support string form
    if [ "$indentationless" = "build:" ]; then
        inbuild=true
        hasbuild=true
        continue
    fi

    imagename2=${indentationless#image\:}
    imagename2=${imagename2#"${imagename2%%[![:space:]]*}"}
    if [ $indentation -eq 4 ] && [ "$imagename2" != "$indentationless" ]; then
        imagename="$imagename2"
        hasimage=true
    fi
done

# in case we didn't handle the last one
handleService

echo "${GREEN}Images updated.${NORMAL}"

if [ "$buildneeded" = true ]; then
    # shellcheck disable=SC2154
    if [ "$embeddedpull" = true ]; then
        shouldbuild=true # shellcheck disable=SC2034
    else
        echo "${GREEN}${STANDOUT}You should run ./composer up --build to rebuild containers.${NORMAL}"
    fi
fi
