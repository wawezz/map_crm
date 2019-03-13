FROM rmrevin/debian-nginx:latest

ENV NGINX_PUSH_STREAM_VERSION 0.5.2

RUN set -xe \
 && wget "https://github.com/wandenberg/nginx-push-stream-module/archive/$NGINX_PUSH_STREAM_VERSION.tar.gz" \
 && mkdir -p /usr/src/nginx/nginx-push-stream-module \
 && tar -xof $NGINX_PUSH_STREAM_VERSION.tar.gz -C /usr/src/nginx/nginx-push-stream-module --strip-components=1 \
 && rm $NGINX_PUSH_STREAM_VERSION.tar.gz \
 && cd /usr/src/nginx \
 && ./configure \
        --prefix=/etc/nginx \
        --sbin-path=/usr/sbin/nginx \
        --conf-path=/etc/nginx/nginx.conf \
        --error-log-path=/var/log/nginx/error.log \
        --http-log-path=/var/log/nginx/access.log \
        --pid-path=/var/run/nginx.pid \
        --lock-path=/var/run/nginx.lock \
        --http-client-body-temp-path=/var/cache/nginx/client_temp \
        --http-proxy-temp-path=/var/cache/nginx/proxy_temp \
        --http-fastcgi-temp-path=/var/cache/nginx/fastcgi_temp \
        --http-uwsgi-temp-path=/var/cache/nginx/uwsgi_temp \
        --http-scgi-temp-path=/var/cache/nginx/scgi_temp \
        --user=nginx \
        --group=nginx \
        --with-http_ssl_module \
        --with-http_realip_module \
        --with-http_addition_module \
        --with-http_sub_module \
        --with-http_dav_module \
        --with-http_flv_module \
        --with-http_mp4_module \
        --with-http_gunzip_module \
        --with-http_gzip_static_module \
        --with-http_random_index_module \
        --with-http_secure_link_module \
        --with-http_stub_status_module \
        --with-http_auth_request_module \
        --with-http_image_filter_module \
        --with-mail \
        --with-mail_ssl_module \
        --with-file-aio \
        --with-ipv6 \
        --with-threads \
        --with-stream \
        --with-stream_ssl_module \
        --with-http_v2_module \
        --with-http_image_filter_module \
        --add-module=/usr/src/nginx/nginx-push-stream-module \
    && make -j2 \
    && make install \
    && make clean \
 && apt-get autoremove -yqq \
 && apt-get clean

COPY nginx.d/ /etc/nginx/

ARG PROJECT
ARG HTTP_HOSTNAME

RUN sed -i -- "s/<PROJECT>/$PROJECT/g" /etc/nginx/sites-enabled/*.conf \
 && sed -i -- "s/<HTTP_HOSTNAME>/$HTTP_HOSTNAME/g" /etc/nginx/sites-enabled/*.conf

EXPOSE 8867
