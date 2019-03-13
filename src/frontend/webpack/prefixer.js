const autoprefixer = require("autoprefixer");

module.exports = function () {
  return {
    module: {
      rules: [
        {
          test: /\.css$/,
          use: {
            loader: "postcss-loader",
            options: {
              plugins: [
                autoprefixer({
                  browsers: ["ie >= 8", "last 4 version"]
                })
              ],
              sourceMap: true
            }
          }
        }
      ]
    }
  };
};
