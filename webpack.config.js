const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const path = require('path');

module.exports = {
	...defaultConfig,
	entry: {
		...defaultConfig.entry(),
		'editor-script': path.resolve(process.cwd(), 'src', 'editor-script.js'),
		'frontend-script': path.resolve(process.cwd(), 'src', 'frontend-script.js'),
		'tailwind-styles': path.resolve(process.cwd(), 'src', 'tailwind.css'),
	},
};
