import js from '@eslint/js';
import tseslint from 'typescript-eslint';
import tseslintParser from '@typescript-eslint/parser';
import {defineConfig} from 'eslint/config';
import importPlugin from 'eslint-plugin-import';
import globals from 'globals';

export default defineConfig([
    {
        languageOptions: {
            ecmaVersion: 2023,
            sourceType: 'module',
            globals: {
                ...globals.browser,
                ...globals.node,
            },
            parser: tseslintParser,
            parserOptions: {
                ecmaVersion: 2023,
                sourceType: 'module',
            },
        },
        plugins: {
            js,
            import: importPlugin,
            'typescript-eslint': tseslint,
        },

        extends: ['js/recommended'],
        files: ['**/*.js', '**/*.ts'],
        ignores: [
            'assets/controllers/utils/csrf_protection_controller.js',
            'assets/vendor/**',
        ],
        rules: {
            ...js.configs.recommended.rules,
            'array-bracket-spacing': ['error', 'never'],
            'camelcase': [
                'error',
                {
                    'properties': 'never',
                },
            ],
            'comma-dangle': ['error', 'always-multiline'],
            'eqeqeq': 'error',
            'id-denylist': ['error', 'd', 'e', 'err'],
            'indent': ['off', 4],
            'import/no-named-as-default-member': 'off',
            'max-len': [
                'warn', {'code': 130, 'ignoreComments': true},
            ],
            'newline-after-var': ['error', 'always'],
            'newline-before-return': 'error',
            'no-console': ['warn', {allow: ['warn', 'error']}],
            'no-warning-comments': [
                'warn',
                {
                    'terms': ['  {', '}  ', ' | ', ' |', '| '],
                    'location': 'anywhere',
                },
            ],
            'no-unused-vars': [
                'warn', {'destructuredArrayIgnorePattern': '^_'},
            ],
            'no-unused-vars': 'off',
            'no-undef': 'off',
            'no-empty': 'error',
            'no-multiple-empty-lines': [
                'error',
                {
                    'max': 1,
                    'maxBOF': 0,
                    'maxEOF': 0,
                },
            ],
            'no-trailing-spaces': ['warn'],
            'no-var': 'error',
            'object-curly-spacing': ['error', 'never'],
            'padding-line-between-statements': [
                'warn',
                {'blankLine': 'always', 'prev': '*', 'next': ['if', 'for']},
            ],
            'padded-blocks': ['error', 'never'],
            'quotes': [
                'error',
                'single',
                {'avoidEscape': true},
            ],
            'semi': ['error', 'always'],
        },
    },
]);
