/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],
    // Class komponen yang dibentuk dinamis dari data (mis. {{ $ticket->status }})
    // tidak terdeteksi scanner konten. Safelist memastikan utility warna/layout-nya
    // tetap di-generate pada build produksi.
    safelist: [
        {
            pattern: /^(sigap-timeline__dot|sigap-badge|sigap-queue-card)--/,
        },
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
            },
            colors: {
                // Trust-blue korporat: sigap-600 adalah warna aksi utama (CTA)
                sigap: {
                    50: '#EFF8FC',
                    100: '#DCEEF9',
                    200: '#B8DCF2',
                    300: '#86C4E6',
                    400: '#4FA5D6',
                    500: '#1F86C4',
                    600: '#0369A1',
                    700: '#075B8B',
                    800: '#0A4A70',
                    900: '#0C3E5C',
                },
            },
            boxShadow: {
                // Bayangan halus + satu hairline, tanpa lift berlebihan
                'sigap-card': '0 1px 2px 0 rgb(15 23 42 / 0.03), 0 1px 3px -1px rgb(15 23 42 / 0.04)',
                'sigap-pop': '0 6px 24px -6px rgb(15 23 42 / 0.14)',
            },
        },
    },
    plugins: [],
};