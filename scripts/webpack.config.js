const path = require( 'path' );
const { merge } = require( 'webpack-merge' );
const wpScriptsConfig = require( '@wordpress/scripts/config/webpack.config' );

module.exports = merge( wpScriptsConfig, {
	entry: {
		'editor-support/index': path.resolve(
			process.cwd(),
			'src/editor-support/index.js'
		),
	},
} );
