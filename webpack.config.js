// TODO: add sourcemaps
// TODO: split config file into webpack.local.js and webpack.production.js

const path = require('path');
const ManifestPlugin = require('webpack-manifest-plugin');

module.exports = {
  entry: {
    app: path.resolve(__dirname, 'resources/assets/js/app/app.js'),
    user: path.resolve(__dirname, 'resources/assets/js/user/user.js'),
  },
  output: {
    filename: '[name].[chunkhash].js',
    path: path.resolve(__dirname, 'public/assets/js')
  },
  module: {
    rules: [
      {
        test: /\.jsx?$/,
        include: [
          path.resolve(__dirname, 'resources/assets/js/app'),
          path.resolve(__dirname, 'resources/assets/js/user'),
        ],
        use: [
          'babel-loader',
          {
            loader: 'babel-loader',
            options: {
              presets: [
                'env',
                'react',
              ]
            }
          }
        ],
      },
    ]
  },
  plugins: [
    new ManifestPlugin({
      publicPath: '/assets/js/',
      fileName: '../../../resources/assets/build/manifest.json'
    })
  ]
};
