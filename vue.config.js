const { defineConfig } = require('@vue/cli-service');
const redirection = require('./vue.config.json');

module.exports = defineConfig({
  publicPath: './',
  transpileDependencies: true,
  devServer: {
    historyApiFallback: true,
    proxy: {
      [redirection.name_redirect]: {
        target: `http://${redirection.url_redirect}`,
        changeOrigin: true,
        pathRewrite: {
          [`^${redirection.name_redirect}`]: redirection.path_redirect,
        },
      },
    },
  },
});
