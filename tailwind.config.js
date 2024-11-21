/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {
      colors: {
        primary: "#1E90FF",
        secondary: "#FF6B6B",
        accent: "#4ADE80", 
        background: "#F0F4F8", 
        textPrimary: "#1A202C", 
        textSecondary: "#718096", 
      },
      fontFamily: {
        sans: ["Inter", "sans-serif"],
        heading: ["Poppins", "sans-serif"],
      },
      spacing: {
        18: "4.5rem",
        36: "9rem",
      },
      borderRadius: {
        lg: "1rem",
        xl: "1.5rem",
      },
      boxShadow: {
        modern: "0 4px 20px rgba(0, 0, 0, 0.1)",
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}
