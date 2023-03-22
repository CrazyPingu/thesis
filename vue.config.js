const { defineConfig } = require('@vue/cli-service')
module.exports = defineConfig({
  publicPath: './',
  transpileDependencies: true,
  devServer: {
    historyApiFallback: true,
    proxy: {
        '/php': {
          target: 'http://127.0.0.1:80',
          changeOrigin: true,
          pathRewrite: {
            '^/php': '/tirocinio/src/php/'
          }
      }
    }
  }
})
