const Path = require('path');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const UglifyJsPlugin = require("uglifyjs-webpack-plugin");
const OptimizeCSSAssetsPlugin = require("optimize-css-assets-webpack-plugin");
const RemovePlugin = require('remove-files-webpack-plugin');
const Webpack = require('webpack');

module.exports = (env, argv) => ({
	entry: {
		'frontend': './src/ts/index.ts',
	},
	output: {
		path: Path.resolve(__dirname, 'dist'),
		filename: 'child-scripts.[name].bundle.min.js',
	},
	optimization: {
		minimizer: [
			new UglifyJsPlugin({
				cache: true,
				parallel: true,
				sourceMap: (argv.mode == 'development') ? true : false   // set to true if you want JS source maps
			}),
			new OptimizeCSSAssetsPlugin({})
		]
	},
	plugins: [
		new MiniCssExtractPlugin({
			filename: "child-styles.min.css"
		}),
		new Webpack.ProvidePlugin({
			$: 'jquery',
			jQuery: 'jquery',
			'window.jQuery': 'jquery',
		}),
		new RemovePlugin({
			before: {
				include: [
					'./dist'
				]
			}	
		})
	],
	module: {
		rules: [
			{
				test: /\.ts$/,
				exclude: /node_modules/,
				use: {
					loader: "babel-loader"
				}
			},
			{
				test: /\.(sass|scss)$/,
				use: [
					MiniCssExtractPlugin.loader, // creates style nodes from JS strings
					{
						loader: "css-loader", // translates CSS into CommonJS
						options: { 
							url: false, 
							sourceMap: (argv.mode == 'development') ? true : false
						}
					},
					{
						loader: "postcss-loader"
					},
					{
						loader: "sass-loader", // compiles Sass to CSS
						options: { sourceMap: (argv.mode == 'development') ? true : false  }
					}
				]
			}
		],
	},
	externals: {
		jquery: 'jQuery',
	},
	devtool: (argv.mode == 'development') ? 'source-map' : '',
	resolve: {
		extensions: ['.ts']
	}
});