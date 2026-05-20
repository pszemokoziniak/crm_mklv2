const colors = require('tailwindcss/colors')
const defaultTheme = require('tailwindcss/defaultTheme')

// eslint-disable-next-line no-undef
module.exports = {
  purge: [
    // prettier-ignore
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
  darkMode: false, // or 'media' or 'class'
  theme: {
    colors: {
      transparent: 'transparent',
      current: 'currentColor',
      black: colors.black,
      white: colors.white,
      red: colors.red,
      orange: colors.orange,
      yellow: colors.yellow,
      green: colors.green,
      gray: colors.blueGray,
      purple: colors.purple,
      blue: colors.blue,
      indigo: {
        100: '#e6e8ff',
        300: '#b2b7ff',
        400: '#7886d7',
        500: '#6574cd',
        600: '#5661b3',
        800: '#2f365f',
        900: '#191e38',
      },
    },
    extend: {
      borderColor: theme => ({
        DEFAULT: theme('colors.gray.200', 'currentColor'),
      }),
      fontFamily: {
        sans: ['Cerebri Sans', ...defaultTheme.fontFamily.sans],
      },
      boxShadow: theme => ({
        outline: '0 0 0 2px ' + theme('colors.indigo.500'),
      }),
      fill: theme => theme('colors'),
      keyframes: {
        'bell-ring': {
          '0%, 100%': { transform: 'rotate(0deg) scale(1)', opacity: '1' },
          '10%, 30%': { transform: 'rotate(-18deg) scale(1.15)', opacity: '1' },
          '20%, 40%': { transform: 'rotate(18deg) scale(1.15)', opacity: '1' },
          '50%': { transform: 'rotate(0deg) scale(1)', opacity: '0.5' },
        },
      },
      animation: {
        'bell-ring': 'bell-ring 1.2s ease-in-out infinite',
      },
    },
  },
  variants: {
    extend: {
      fill: ['focus', 'group-hover'],
    },
  },
  plugins: [],
}
