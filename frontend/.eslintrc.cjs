module.exports = {
  env: {browser: true, es2020: true},
  extends: [
    'eslint:recommended',
    'plugin:@typescript-eslint/recommended',
    'plugin:react-hooks/recommended',
    'plugin:@shopify/es5',
  ],
  parser: '@typescript-eslint/parser',
  parserOptions: {ecmaVersion: 'latest', sourceType: 'module'},
  plugins: [
    'react-refresh',
    '@stylistic/js',
  ],
  rules: {
    'react-refresh/only-export-components': 'warn',
    'no-console': ['error', {allow: ['warn', 'error']}],
    'indent-legacy': 'off',
    '@stylistic/js/indent': ['error', 2],
  },
};
