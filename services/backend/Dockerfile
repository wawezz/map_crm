FROM rmrevin/debian-php7:4.0

RUN set -xe \
 && apt-get update -qq \
 && apt-get install -y --no-install-recommends libgmp-dev \
 && docker-php-ext-install -j$(nproc) gmp \
 && apt-get install -y --no-install-recommends cron \
 && rm -rf /var/lib/apt/lists/*

RUN ln -s /opt/www/backend/bin/build /usr/local/bin/build \
 && ln -s /opt/www/backend/bin/console /usr/local/bin/console \
 && ln -s /opt/www/backend/vendor/bin/phpunit /usr/local/bin/phpunit

ARG _ENV

COPY etc.d/ /usr/local/etc/
COPY supervisor.d/ /etc/supervisor/

RUN ln -s /usr/local/etc/php.$_ENV.ini /usr/local/etc/php/php.ini

ARG PROJECT
RUN sed -i -- "s/<PROJECT>/$PROJECT/g" /etc/supervisor/conf.d/*.conf

COPY entrypoint.sh /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]

CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]