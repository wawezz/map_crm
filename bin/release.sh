#!/bin/bash

# include
. ./.env

git checkout master
git pull origin master
git checkout release
git pull origin release
git merge master
git push origin release

now=$(date +"%Y-%m-%d")

latestTag=$(git describe --tags `git rev-list --tags --max-count=1`)

echo
echo "now: ${now}"
echo "latestTag: ${latestTag}"

if [[ ${latestTag} =~ ^$now\.([0-9]+) ]]
then
    nextVersion=$((10#${BASH_REMATCH[1]} + 1));
else
    nextVersion=1
fi

echo "nextTag: ${now}.${nextVersion}"
echo

git tag "${now}.${nextVersion}"
git push --tags
git checkout master
