/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './pages/**/*.php',
    './admin/**/*.php',
    './includes/**/*.php',
    './app/**/*.php',
    './assets/js/**/*.js',
    './public/**/*.php',
    './*.php',
  ],
  theme: {
    extend: {
      maxWidth: {
        '8xl': '90rem',
      },
      borderRadius: {
        '4xl': '2.5rem',
      },
    },
  },
  plugins: [],
}
