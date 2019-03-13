#!/bin/bash

./bin/maintenance.sh on

echo "--- Down docker instance -----------"
docker-compose stop
echo "--- done. --------------------------"
echo ""

echo "--- Upping docker instance ---------"
docker-compose up -d
echo "--- done. --------------------------"
echo ""

echo "--- Waiting 5 sec ---------------------------------"
sleep 5
echo "--- done. -----------------------------------------"
echo ""

./bin/nginx-reload.sh

./bin/maintenance.sh off
