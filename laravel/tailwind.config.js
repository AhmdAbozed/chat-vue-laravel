/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      keyframes: {
        fadeInOut: {
          '0%, 100%': { opacity: '0%' },
          '10%, 90%': { opacity: '100%' },
        },
        fadeIn: {
          '0%': { opacity: '0%' },
          '100%': { opacity: '100%' },
        }
      },
      animation: {
        fadeIn: 'fadeIn 1s forwards',
        fadeInOut: 'fadeInOut 3s forwards',
      }
    },
  },
  plugins: [],
}