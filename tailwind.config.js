/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './templates/**/*.{twig,html.twig}',
    './assets/js/**/*.{js,jsx,ts,tsx,vue}'
  ],
  darkMode: 'class',
  theme: {
    extend: {
      boxShadow: {
        'r': [
            '1px 0 3px 0 rgb(0 0 0 / 0.1)',
            '1px 0 2px -1px rgb(0 0 0 / 0.1)'
        ],
        'r-sm': '1px 0 2px 0 rgb(0 0 0 / 0.05)',
        'r-md': [
            '4px 0 6px -1px rgb(0 0 0 / 0.1)',
            '2px 0 4px -2px rgb(0 0 0 / 0.1)'
        ],
        'r-lg': [
            '10px 0 15px -3px rgb(0 0 0 / 0.1)',
            '4px 0 6px -4px rgb(0 0 0 / 0.1)'
        ],
        'r-xl': [
            '20px 0 25px -5px rgb(0 0 0 / 0.1)',
            '8px 0 10px -6px rgb(0 0 0 / 0.1)'
        ],
        'r-2xl': '25px 0 50px -12px rgb(0 0 0 / 0.25)',
      }
    },
  },
  plugins: [
      require('@tailwindcss/forms'),
  ],
}
