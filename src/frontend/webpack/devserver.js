const env = require("../source/env");

module.exports = function () {
  return {
    devServer: {
      stats: "errors-only",
      host: "0.0.0.0",
      port: 9080,
      public: env.hostname,
      allowedHosts: [
        env.hostname
      ],
    }
  };
};
