const path = require("path");
const webpack = require("webpack");
const HtmlWebpackPlugin = require("html-webpack-plugin");
const CopyWebPackPlugin = require("copy-webpack-plugin");
const merge = require("webpack-merge");
const pug = require("./webpack/pug");
const devserver = require("./webpack/devserver");
const sass = require("./webpack/sass");
const css = require("./webpack/css");
const extractCSS = require("./webpack/css.extract");
const static = require("./webpack/static");
const prefixer = require("./webpack/prefixer");
const HardSourceWebpackPlugin = require("hard-source-webpack-plugin");

const PATHS = {
  source: path.join(__dirname, "source"),
  static: path.join(__dirname, "static"),
  build: path.join(__dirname, "build")
};

const common = function (env) {

  const commonPage = {};

  const pages = [
    Object.assign({}, commonPage, {
      filename: "index.html",
      chunks: ["common", "vendor", "index"],
      template: PATHS.source + "/pages/index/index.pug",
    }),
  ];

  const config = {
    entry: {
      "common": PATHS.source + "/common/scripts/index.js",
      "index": PATHS.source + "/pages/index/index.js"
    },
    output: {
      path: PATHS.build,
      filename: "js/[name].[chunkhash].js"
    },
    devtool: env.serv === "production" ? false : "eval-source-map",
    plugins: [
      new CopyWebPackPlugin([{
        from: PATHS.static
      }]),
      new webpack.optimize.CommonsChunkPlugin({
        name: "vendor"
      }),
      new webpack.ProvidePlugin({
        $: "jquery",
        jQuery: "jquery",
        "window.jQuery": "jquery"
      }),
      new webpack.DefinePlugin({
        "process.env": {
          NODE_ENV: JSON.stringify(env.serv)
        }
      }),
    ],
    module: {
      rules: [
        {
          enforce: "pre",
          test: /\.js$/,
          exclude: /(node_modules|bower_components)/,
          use: {
            loader: "eslint-loader",
            options: {
              failOnWarning: false,
              failOnError: true
            }
          }
        },
        {
          test: /\.js$/, 
          exclude: /(node_modules|bower_components)/,
          use: {
            loader: "babel-loader",
            options: {
              presets: ["env"],
              plugins: [
                "transform-class-properties",
                "transform-object-rest-spread"
              ],
            }
          }
        },
        {
          test: /\.vue$/,
          loader: "vue-loader",
          options: {
            loaders: {
              scss: "vue-style-loader!css-loader!sass-loader",
            }
          }
        }
      ]
    },
    resolve: {
      alias: {
        "~": path.resolve(__dirname, "source"),
        vue: env.serv === "production" ? "vue/dist/vue.min.js" : "vue/dist/vue.js"
      },
      extensions: [".js", ".json", ".jsx", ".vue", ".css"]
    }
  };

  for (const page of pages) {
    config.plugins.push(new HtmlWebpackPlugin(page));
  }

  if (env.serv === "development") {
    config.plugins.push(new HardSourceWebpackPlugin());
  }

  return merge([config, pug(), static()]);
};

module.exports = async function (env) {

  const modules = [common(env)];

  if (env.serv === "production") {
    modules.push(extractCSS());
  }

  if (env.serv === "development") {
    modules.push(sass());
    modules.push(css());
    modules.push(devserver());
  }

  modules.push(prefixer);

  return merge(modules);
};
