#!/bin/bash

# include
. ./.env

docker-compose exec --user www-data backend bash -c "cd /opt/www/backend && $*"
