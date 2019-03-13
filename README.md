Require environment
-------------------

docker-ce
docker-compose
nginx
yarn
node

Prepare environment
-------------------

Copy `.env.example` to `.env` and fill parameters.

Add virtual host to `/etc/hosts`

```
127.0.0.1   {HTTP_HOSTNAME from .env}
```

Installation and upgrading
--------------------------

For development:
```bash
sudo ./bin/prepare.sh
 
sudo ./bin/frontend.sh yarn start
```

For production:
```bash
sudo ./bin/build
```

DB import/export
--------------------------

backup path: /dump.sql

For export:
```bash
sudo ./bin/msdump.sh
```

For export:
```bash
sudo ./bin/msrestore.sh
```

API
--------------------------

```
use MapCRM.postman_collection.json
```

Optional
--------

For restoring legacy postgres db execute (by default postgres container not created):
```bash
sudo ./bin/postgresql.sh dump-restore
```
