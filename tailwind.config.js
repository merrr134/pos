/** @type {import('tailwindcss').Config} */
export default {
  content: [],
  theme: {
    extend: {},
  },
  plugins: [],
}

import forms from '@tailwindcss/forms'

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './vendor/mallardduck/blade-lucide-icons/resources/svg/*.svg',
    ],
    theme: {
        extend: {
            colors: {
                // Aturan brand ketat Pitou Cafe — JANGAN diganti.
                brand: {
                    DEFAULT: '#7C4A2D', // coffee brown
                    light: '#A9714B',   // brand-light
                },
                cream: '#FAF6F0',       // background cream
            },
            fontFamily: {
                sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                display: ['"Playfair Display"', 'ui-serif', 'Georgia', 'serif'],
            },
        },
    },
    plugins: [forms],
}
