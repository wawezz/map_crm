FROM mysql:5.7

COPY mysql.d/ /etc/mysql/

COPY initial.sql /docker-entrypoint-initdb.d/initial.sql

ARG MYSQL_USER
ARG MYSQL_USER_PASSWORD
ARG MYSQL_DATABASE

RUN sed -i -- "s/<MYSQL_USER>/$MYSQL_USER/g" /docker-entrypoint-initdb.d/initial.sql \
    && sed -i -- "s/<MYSQL_DATABASE>/$MYSQL_DATABASE/g" /docker-entrypoint-initdb.d/initial.sql \
    && sed -i -- "s/<MYSQL_USER_PASSWORD>/$MYSQL_USER_PASSWORD/g" /docker-entrypoint-initdb.d/initial.sql
