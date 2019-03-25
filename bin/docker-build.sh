#!/bin/bash

if [ "$(id -u)" != "0" ]; then
   echo "This script must be run as root" 1>&2
   exit 1
fi

# include
. ./.env

BUILD=$(cat /dev/urandom | tr -dc 'a-zA-Z0-9' | fold -w 8 | head -n 1)
BASEPATH_ESCAPED=$(echo ${BASEPATH} | sed 's/\//\\\//g')

echo "## Begin building project \"$PROJECT\"."
echo ""

cert_key="./.ssl/$PROJECT.key"
cert_key_orig="./.ssl/$PROJECT.key.original"
cert_csr="./.ssl/$PROJECT.csr"
cert_crt="./.ssl/$PROJECT.crt"

echo "--- Prepare self-signed ssl certificate ---------"

if [ -f $cert_crt ]
then
    echo "Self-signed certificate already exists."
else
    openssl genrsa -des3 -passout pass:$CERT_PASSWORD -out $cert_key 1024 \
    && openssl req -new -key $cert_key -out $cert_csr -passin pass:$CERT_PASSWORD -subj $CERT_SUBJECT \
    && cp $cert_key $cert_key_orig \
    && openssl rsa -in $cert_key_orig -out $cert_key -passin pass:$CERT_PASSWORD \
    && openssl x509 -req -days 365 -in $cert_csr -signkey $cert_key -out $cert_crt

    echo "Self-signed certificate successfully created."
fi

chown $_UID:$_GID $cert_key $cert_key_orig $cert_csr $cert_crt

echo "--- done. ---------------------------------------"
echo ""

echo "--- Prepare 502 error page ----------------------"

e502_file="./services/proxy/errors.d/502.html"
cp "./services/proxy/errors.d/502.$_ENV.html" $e502_file
sed -i -- "s/\$BASEPATH/${BASEPATH_ESCAPED}/g" $e502_file

echo "--- done. ---------------------------------------"
echo ""

echo "--- Rebuild docker instance ---------------------"

docker-compose build

echo "--- done. ---------------------------------------"
echo ""

echo "--- Copy frontend env config -----------------------------"

FONT_PATH="./src/frontend/"
ENV_PATH="./src/frontend/source/"
ENV_FILE="$ENV_PATH/env.js"
ADD_ENV_FILE="$FONT_PATH/.env"

case "$USE_SSL" in
 true) HTTP_SCHEME=https ;;
 *) HTTP_SCHEME=http ;;
esac

mkdir -p $ENV_PATH

SENTRY_PUBLIC_DSN_ESCAPED=$(echo ${SENTRY_PUBLIC_DSN} | sed 's/\//\\\//g')

cp ./services/frontend/.env $FONT_PATH \
&& sed -i -- "s/<VUE_APP_GOOGLE_MAP_API>/$VUE_APP_GOOGLE_MAP_API/g" $ADD_ENV_FILE \
&& sed -i -- "s/<VUE_APP_I18N_LOCALE>/$VUE_APP_I18N_LOCALE/g" $ADD_ENV_FILE \
&& sed -i -- "s/<VUE_APP_I18N_FALLBACK_LOCALE>/$VUE_APP_I18N_FALLBACK_LOCALE/g" $ADD_ENV_FILE 

cp ./services/frontend/env.js $ENV_PATH \
&& sed -i -- "s/<PROJECT>/$PROJECT/g" $ENV_FILE \
&& sed -i -- "s/<_ENV>/$_ENV/g" $ENV_FILE \
&& sed -i -- "s/<HTTP_SCHEME>/$HTTP_SCHEME/g" $ENV_FILE \
&& sed -i -- "s/<HTTP_HOSTNAME>/$HTTP_HOSTNAME/g" $ENV_FILE \
&& sed -i -- "s/<RECAPTCHA_SITEKEY>/$RECAPTCHA_SITEKEY/g" $ENV_FILE \
&& sed -i -- "s/<GOOGLE_ANALYTICS_ID>/$GOOGLE_ANALYTICS_ID/g" $ENV_FILE \
&& sed -i -- "s/<YANDEX_ANALYTICS_ID>/$YANDEX_ANALYTICS_ID/g" $ENV_FILE \
&& sed -i -- "s/<SENTRY_PUBLIC_DSN>/$SENTRY_PUBLIC_DSN_ESCAPED/g" $ENV_FILE
echo "--- done. ---------------------------------------"
echo ""

echo "--- Create port forwarding ----------------------"

echo "http://$HTTP_HOSTNAME -> http://127.0.0.1:$HTTP_PORT"
echo "https://$HTTP_HOSTNAME -> https://127.0.0.1:$HTTPS_PORT"

NGINX_PROXY_FILE="$NGINX_HOSTS_PATH/$PROJECT.conf"

if [[ "$USE_SSL" = true ]]
then
    cp ./services/proxy/proxy.ssl.conf $NGINX_PROXY_FILE
else
    cp ./services/proxy/proxy.regular.conf $NGINX_PROXY_FILE
fi

if [[ -f "/etc/letsencrypt/live/$HTTP_HOSTNAME/fullchain.pem" ]]
then
    CERT_FILE=$(echo "/etc/letsencrypt/live/$HTTP_HOSTNAME/fullchain.pem" | sed 's/\//\\\//g')
    CERT_KEY_FILE=$(echo "/etc/letsencrypt/live/$HTTP_HOSTNAME/privkey.pem" | sed 's/\//\\\//g')
else
    CERT_FILE=$(echo "$BASEPATH/.ssl/$PROJECT.crt" | sed 's/\//\\\//g')
    CERT_KEY_FILE=$(echo "$BASEPATH/.ssl/$PROJECT.key" | sed 's/\//\\\//g')
fi

if [[ -f "/etc/letsencrypt/live/bot-dev.$PROXY_HOSTNAME/fullchain.pem" ]]
then
    PROXY_CERT_FILE=$(echo "/etc/letsencrypt/live/bot-dev.$PROXY_HOSTNAME/fullchain.pem" | sed 's/\//\\\//g')
    PROXY_CERT_KEY_FILE=$(echo "/etc/letsencrypt/live/bot-dev.$PROXY_HOSTNAME/privkey.pem" | sed 's/\//\\\//g')
else
    PROXY_CERT_FILE=$(echo "$BASEPATH/.ssl/$PROJECT.crt" | sed 's/\//\\\//g')
    PROXY_CERT_KEY_FILE=$(echo "$BASEPATH/.ssl/$PROJECT.key" | sed 's/\//\\\//g')
fi

sed -i -- "s/<BASEPATH>/$BASEPATH_ESCAPED/g" $NGINX_PROXY_FILE \
&& sed -i -- "s/<PUBLIC_IP>/$PUBLIC_IP/g" $NGINX_PROXY_FILE \
&& sed -i -- "s/<PROJECT>/$PROJECT/g" $NGINX_PROXY_FILE \
&& sed -i -- "s/<PROXY_HOSTNAME>/$PROXY_HOSTNAME/g" $NGINX_PROXY_FILE \
&& sed -i -- "s/<HTTP_HOSTNAME>/$HTTP_HOSTNAME/g" $NGINX_PROXY_FILE \
&& sed -i -- "s/<HTTP_PORT>/$HTTP_PORT/g" $NGINX_PROXY_FILE \
&& sed -i -- "s/<HTTPS_PORT>/$HTTPS_PORT/g" $NGINX_PROXY_FILE \
&& sed -i -- "s/<STREAM_PORT>/$STREAM_PORT/g" $NGINX_PROXY_FILE \
&& sed -i -- "s/<CERT_FILE>/$CERT_FILE/g" $NGINX_PROXY_FILE \
&& sed -i -- "s/<CERT_KEY_FILE>/$CERT_KEY_FILE/g" $NGINX_PROXY_FILE \
&& sed -i -- "s/<PROXY_CERT_FILE>/$PROXY_CERT_FILE/g" $NGINX_PROXY_FILE \
&& sed -i -- "s/<PROXY_CERT_KEY_FILE>/$PROXY_CERT_KEY_FILE/g" $NGINX_PROXY_FILE

echo "--- done. ---------------------------------------"
echo ""

echo "## Build successful end."
