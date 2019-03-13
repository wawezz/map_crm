#!/bin/bash

# include
. ./.env

echo "--- Restoring mysql dump ------------------"
cat dump.sql | docker exec -i crm_mysql_1 /usr/bin/mysql -u root --password=$MYSQL_ROOT_PASSWORD $MYSQL_DATABASE
echo "--- done. --------------------------"
echo ""
