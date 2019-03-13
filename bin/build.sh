#!/bin/bash

# include
. ./.env

sudo ./bin/maintenance.sh on

sudo ./bin/docker-build.sh
sudo ./bin/docker-reload.sh

sudo ./bin/backend.sh build

sudo ./bin/frontend.sh yarn
sudo ./bin/frontend.sh yarn build

sudo ./bin/docker-reload.sh

sudo ./bin/maintenance.sh off
