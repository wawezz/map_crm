#!/bin/bash

# include
. ./.env

docker-compose exec --user node frontend bash -c "cd /opt/www/frontend && $*"
