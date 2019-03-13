module.exports = function () {
  return {
    module: {
      rules: [
        {
          test: /\.(jpg|png|gif)$/,
          loader: "file-loader",
          options: {
            name: "files/images/[name].[ext]"
          }
        },
        {
          test: /\.(woff|woff2|ttf|eot|svg)$/,
          loader: "file-loader",
          options: {
            name: "files/fonts/[name].[ext]"
          }
        },
        {
          test: /\.(pdf)$/,
          loader: "file-loader",
          options: {
            name: "files/documents/[name].[ext]"
          }
        },
        {
          test: /\.(mp4)$/,
          loader: "file-loader",
          options: {
            name: "files/videos/[name].[ext]"
          }
        },
        {
          test: /\.(wav)$/,
          loader: "file-loader",
          options: {
            name: "files/audios/[name].[ext]"
          }
        }
      ]
    }
  };
};
