module.exports = {
  id: "<PROJECT>",
  env: "<_ENV>",
  hostname: "<HTTP_HOSTNAME>",
  urls: {
    frontend: "<HTTP_SCHEME>://<HTTP_HOSTNAME>",
    api: "<HTTP_SCHEME>://<HTTP_HOSTNAME>/api"
  },
  recaptcha: {
    sitekey: '<RECAPTCHA_SITEKEY>'
  },
  analytics: {
    google: "<GOOGLE_ANALYTICS_ID>",
    yandex: "<YANDEX_ANALYTICS_ID>"
  },
  sentry: {
    dsn: "<SENTRY_PUBLIC_DSN>"
  },
};
