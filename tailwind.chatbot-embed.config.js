import defaultTheme from 'tailwindcss/defaultTheme';
import defaultConfig from './tailwind.config';

/** @type {import('tailwindcss').Config} */
export default {
	content: [
		'./resources/views/**/layout/*.blade.php',
		'./resources/views/**/components/**/*.blade.php',
		'./resources/views/**/landing-page/**/*.blade.php',
		'./resources/views/**/index.blade.php',
		'./resources/views/**/vendor/**/*.blade.php',
		'./resources/views/**/blog/**/*.blade.php',
		'./resources/views/**/page/**/*.blade.php',
		'./resources/views/**/panel/admin/frontend/**/*.blade.php',
		'./resources/views/**/panel/admin/custom/**/*.blade.php',
		'./resources/views/**/panel/chatbot/**/*.blade.php',
	],
	theme: {
		extend: {
			...defaultConfig.theme.extend,
			screens: {
				sm: '576px',
				md: '768px',
				lg: '992px',
				xl: '1170px',
				'2xl': '1170px',
			},
			boxShadow: {
				'xs': '0 1px 11px rgb(0 0 0 / 6%)',
				'sm': '0 3px 6px rgb(0 0 0 / 16%)',
				'lg': '0 15px 33px rgb(0 0 0 / 5%)',
				'xl': '0 20px 50px rgb(0 0 0 / 20%)',
			},
			keyframes: {
				...defaultConfig.theme.extend.keyframes,
				'pulse-intense': {
					'0%, 100%': { opacity: 1, transform: 'scale(1)' },
					'50%': { opacity: 0.5, transform: 'scale(0.75)' },
				},
				'hue-rotate': {
					'0%': { filter: 'hue-rotate(0deg)' },
					'100%': { filter: 'hue-rotate(360deg)' },
				},
			},
			animation: {
				...defaultConfig.theme.extend.animation,
				'pulse-intense': 'pulse-intense 2s ease-in-out infinite',
				'hue-rotate': 'hue-rotate 1.9s linear infinite',
			},
		},
	},
};
