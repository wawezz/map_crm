module.exports = function () {
  return {
    module: {
      rules: [
        {
          test: /\.pug$/,
          loader: "pug-loader",
          options: {
            self: true,
            pretty: true
          }
        }
      ]
    }
  };
};
