const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const path = require('path');
const fs = require('fs');
const TerserPlugin = require('terser-webpack-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');

module.exports = {
	...defaultConfig,
	entry: {
		...defaultConfig.entry(),
		...getEntryPoints('admin'),
		...getEntryPoints('frontend')
	},
	optimization: {
		...defaultConfig.optimization,
		minimizer: [
			new TerserPlugin({
				terserOptions: {
					compress: {
						drop_console: true,
					},
					output: {
						comments: false,
					},
				},
				extractComments: false,
			}),
			new CssMinimizerPlugin(),
		],
	},
};

function getEntryPoints(folderName) {
	const entryPoints = {};
	const folderPath = path.resolve(process.cwd(), 'src', folderName);

	fs.readdirSync(folderPath).forEach(file => {
		const filePath = path.resolve(folderPath, file);
		const fileName = path.basename(file, path.extname(file));
		entryPoints[folderName + '/' + fileName] = filePath;
	});

	return entryPoints;
}
