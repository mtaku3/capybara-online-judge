/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./src/**/*.{twig,js,php}", "./node_modules/flowbite/**/*.js"],
  theme: {
    extend: {},
  },
  plugins: [
    require('flowbite/plugin')
  ],
}


