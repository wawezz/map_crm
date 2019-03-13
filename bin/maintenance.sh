#!/bin/bash

if [ $1 == "on" ]
then
    touch ./services/proxy/maintenance.lock
    echo "--- Maintenance on --------------------------------"
    echo ""
else
    rm ./services/proxy/maintenance.lock
    echo "--- Maintenance off -------------------------------"
    echo ""
fi
