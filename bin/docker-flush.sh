#!/bin/bash

if [ "$(id -u)" != "0" ]; then
   echo "This script must be run as root" 1>&2
   exit 1
fi

# include
. ./.env

echo
read -p "Remove all containers and images? [y/N] " -n 1 -r
if [[ $REPLY =~ ^[Yy]$ ]]
then
    echo "   > Removing containers..."
    docker stop $(docker ps -a -q)
    docker rm -f $(docker ps -a -q)
    echo "     done."

    echo

    echo "   > Removing images..."
    docker rmi -f $(docker images -q)
    echo "     done."
fi

echo
