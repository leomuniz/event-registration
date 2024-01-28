module.exports = {
	root: true,
	env: {
		browser: true,
		es6: true,
		node: true
	},
	extends: [ 'eslint:recommended', 'plugin:jsdoc/recommended', 'wordpress' ],
	ignorePatterns: [ 'assets/js/**/*.min.js' ]
};
