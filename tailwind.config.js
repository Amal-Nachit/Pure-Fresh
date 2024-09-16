/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./templates/**/*.html.twig", "./assets/**/*.js"],
  theme: {
    screens: {
      xs: "480px",
      sm: "640px",
      md: "768px",
      lg: "1024px",
      xl: "1280px",
      "2xl": "1536px",
    },
    extend: {
      colors: {
        primary: "#334B35",
        secondary: "#F7C35F",
        tertiary: "#E8FCCF",
      },
    },
  },
  plugins: [],
};
