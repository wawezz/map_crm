#!/bin/bash

# include
. ./.env

echo "--- Creating mysql dump ------------------"
docker exec crm_mysql_1 /usr/bin/mysqldump -u root --password=$MYSQL_ROOT_PASSWORD $MYSQL_DATABASE > dump.sql
echo "--- done. --------------------------"
echo ""

