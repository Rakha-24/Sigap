/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
            },
            colors: {
                sigap: {
                    50: '#EFF4FF',
                    100: '#DCE6FE',
                    200: '#BFD0FD',
                    500: '#3D63E8',
                    600: '#2554D6',
                    700: '#1D42B0',
                    800: '#17348A',
                    900: '#122868',
                },
            },
            boxShadow: {
                'sigap-card': '0 1px 2px 0 rgb(18 40 104 / 0.05), 0 2px 8px -2px rgb(18 40 104 / 0.08)',
            },
            borderRadius: {
                'sigap': '0.625rem',
            },
        },
    },
    plugins: [],
};