/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: "class",
  content: ["./src/**/*.{html,js}", "./src/**/*.{vue,js,ts,jsx,tsx}"],
  theme: {
    fontFamily: {
      sans: ["Inter", "sans-serif"],
      nunito: ["Nunito", "sans-serif"],
      roboto: ["Roboto", "sans-serif"],
      poppins: ["Poppins", "sans-serif"],
      helvetica: ["Helvetica", "sans-serif"],
      fira: ["Fira Sans", "sans-serif"],
      quicksand: ["Quicksand", "sans-serif"],
      playfair: ["Playfair Display", "serif"],
      valley: ["Valley Sans", "sans-serif"],
    },
    extend: {
      colors: {
        gold: "#D4AF37",
        "olive-wood": {
          50: "#f7f2ee",
          100: "#eee6dd",
          200: "#decdba",
          300: "#cdb398",
          400: "#bd9a75",
          500: "#ac8153",
          600: "#8a6742",
          700: "#674d32",
          800: "#453421",
          900: "#221a11",
          950: "#18120c",
        },
        "primary-scarlet": {
            50: "#ffe5ea",
            100: "#ffccd5",
            200: "#ff99aa",
            300: "#ff6680",
            400: "#ff3355",
            500: "#ff002b",
            600: "#cc0022",
            700: "#99001a",
            800: "#660011",
            900: "#330009",
            950: "#240006"
        },
        "carbon-black": {
            50: "#f2f2f2",
            100: "#e6e6e6",
            200: "#cccccc",
            300: "#b3b3b3",
            400: "#999999",
            500: "#808080",
            600: "#666666",
            700: "#4d4d4d",
            800: "#333333",
            900: "#1A1A1A",
            950: "#121212",
        }
      },
      keyframes: {
        "accordion-down": {
          from: { height: "0" },
          to: { height: "var(--radix-accordion-content-height)" },
        },
        "accordion-up": {
          from: { height: "var(--radix-accordion-content-height)" },
          to: { height: "0" },
        },
      },
    },
  },
};
