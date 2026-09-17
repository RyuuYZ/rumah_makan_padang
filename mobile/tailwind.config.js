/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        background: '#F5EFE2',
        foreground: '#241B16',
        songket: {
          red: '#7A1F2B',
          gold: '#C9A227',
          dark: '#3D0F15',
          light: '#F8F4EC',
        },
      },
      fontFamily: {
        serif: ['"Playfair Display"', 'Georgia', 'serif'],
        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
      },
      spacing: {
        'gonjong': '2rem',
      },
      maxWidth: {
        'content': '80ch',
      },
      boxShadow: {
        'songket': '0 10px 25px -5px rgba(122, 31, 43, 0.15), 0 8px 10px -6px rgba(122, 31, 43, 0.1)',
        'gold': '0 10px 25px -5px rgba(201, 162, 39, 0.2), 0 8px 10px -6px rgba(201, 162, 39, 0.15)',
      },
    },
  },
  plugins: [],
}
