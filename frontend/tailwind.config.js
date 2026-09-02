/** @type {import('tailwindcss').Config} */
module.exports = {
    darkMode: 'class',
    content: [
        './src/**/*.{html,js}',
        './src/**/*.{vue,js,ts,jsx,tsx}',
    ],
    theme: {
        fontFamily: {
            sans: ['Inter', 'sans-serif'],
            nunito: ['Nunito', 'sans-serif'],
            roboto: ['Roboto', 'sans-serif'],
            poppins: ['Poppins', 'sans-serif'],
            helvetica: ['Helvetica', 'sans-serif'],
        },
        extend: {
            colors: {
                gold: '#D4AF37',
            },
        }
    }
}