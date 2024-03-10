/** @type {import('tailwindcss').Config} */
export default {
  content: ['./index.html', './src/**/*.{vue,js,ts,jsx,tsx}'],
  theme: {
    extend: {
      keyframes: {
        slideIn: {
          '0%': { transform: 'translate(0rem, -50rem)' },
          '50%': { transform: 'translate(0rem, 0rem)' },
        }
      },
    },
    plugins: [],
  }
}
/*
@keyframes transparency {
  from { opacity: 10 %; }
  to { opacity: 50 %}
}*/