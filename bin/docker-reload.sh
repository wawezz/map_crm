#!/bin/bash

if [ "$(id -u)" != "0" ]; then
   echo "This script must be run as root" 1>&2
   exit 1
fi

echo "--- Down docker instance --------------------------"
if [ -z "$1" ]
then docker-compose down -v --remove-orphans
else docker-compose stop $1 && docker-compose rm -fa $1
fi
echo "--- done. -----------------------------------------"
echo ""

echo "--- Upping docker instance ------------------------"
docker-compose up -d
echo "--- done. -----------------------------------------"
echo ""

echo "--- Waiting 5 sec ---------------------------------"
sleep 5
echo "--- done. -----------------------------------------"
echo ""

./bin/nginx-reload.sh
