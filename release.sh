#!/bin/sh
GIT_BRANCH="master"

GIT_MERGE_AUTOEDIT=no
export GIT_MERGE_AUTOEDIT

numcolors=$(tput colors)
[ $numcolors -ge 8 ] && [ -t 1 ] && {
       	colored=1
} || {
       	colored=0
}

fg_black="$(tput setaf 0)"
fg_red="$(tput setaf 1)"
fg_green="$(tput setaf 2)"
fg_yellow="$(tput setaf 3)"
fg_blue="$(tput setaf 4)"
fg_magenta="$(tput setaf 5)"
fg_cyan="$(tput setaf 6)"
fg_white="$(tput setaf 7)"
reset="$(tput sgr0)"

printlnc () {
       	if [ $colored -eq 1 ] && [ -n "$2" ]; then
       		eval echo \$3 "\${$2}\$1\${reset}"
       	else
       		echo $3 $1
       	fi
}

printc () {
       	if [ $colored -eq 1 ] && [ -n "$2" ]; then
       		eval echo -n \$3 "\${$2}\$1\${reset}"
       	else
       		echo -n $3 $1
       	fi
}

error_out () {
       	if [ -n "$1" ]; then
       		printlnc "$1" fg_red
       	fi
       	if [ -n "$2" ]; then
       		printlnc "$2"
       	fi
       	if [ $stashed -eq 1 ]; then
       		printlnc "A stash-elt változásaidat a 'git stash pop --index' paranccsal tudod visszatölteni." fg_green
       	fi
       	printlnc "Ezen a branch-en voltál: $branch" fg_green
       	exit
}

stashed=0
if [ "$(git diff --shortstat --ignore-submodules 2> /dev/null | tail -n1)" != "" ]; then
       	printlnc "Nem tiszta a working directory, elmentem stash-be" fg_green
       	git stash save "stashed by release script"
       	stashed=1
fi

# If HEAD is a sym-ref, the first assignment will work
# otherwise, it's detached, so get the SHA1 with rev-parse
if ! branch=$(git symbolic-ref HEAD 2>&1); then
    branch=$(git rev-parse HEAD)
fi
# trim a refs/heads/ prefix; no-op otherwise
branch=${branch#refs/heads/}

git checkout $GIT_BRANCH
git push origin $GIT_BRANCH --dry-run --porcelain >/dev/null 2>&1 || {
       	printlnc "origin/$GIT_BRANCH módosult, nem tudok push-olni" fg_green
       	printc  "pulloljak (i/n)? " fg_green
       	read answer
       	if [ "$answer" = "n" ]; then
       		if [ $stashed -eq 1 ]; then
       			printlnc "Visszaállok $branch branch-re" fg_green
       			git checkout "$branch"
       			printlnc "Változások visszatöltése stash-ből" fg_green
       			git stash pop --index
       		fi
       		exit
       	fi
       	git pull || error_out "conflict történt, kilépek"
}

toplevel="$(git rev-parse --show-toplevel)"
if [ -f "$toplevel"/gulpfile.js ]; then
       	printlnc "gulp build futtatása" fg_green
       	./compose run gulp build || error_out "Hiba történt"

       	if [ "$(git diff --shortstat --ignore-submodules 2> /dev/null | tail -n1)" != "" ]; then
       		error_out "Elfelejtetted legenerálni a production asset-eket!" "A módosított fájlokat commitold be, majd futtasd újra a release parancsot."
       	fi
fi

git branch release 2>/dev/null

git checkout release && git merge $GIT_BRANCH --ff-only && git push origin $GIT_BRANCH release || {
       	printlnc "Hiba történt" fg_red
}

printlnc "Visszaállok $branch branch-re" fg_green
git checkout "$branch"


if [ $stashed -eq 1 ]; then
       	printlnc "Változások visszatöltése stash-ből" fg_green
       	git stash pop --index
fi
