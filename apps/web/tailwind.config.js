/** @type {import('tailwindcss').Config} */
export default {
  darkMode: ['class'],
  content: [
    './index.html',
    './src/**/*.{js,ts,jsx,tsx}',
  ],
  theme: {
    extend: {
      colors: {
        imd: {
          50: '#f0f7ff',
          100: '#e0effe',
          200: '#bae0fd',
          300: '#7cc8fb',
          400: '#36abf7',
          500: '#0c8ee9',
          600: '#0270c7',
          700: '#0359a1',
          800: '#074b84',
          900: '#0c3f6e',
          950: '#082849',
        },
        moes: {
          50: '#f4fbf7',
          100: '#e5f6ee',
          500: '#10b981',
          600: '#059669',
          700: '#047857',
        },
        surface: {
          dark: '#0B132B',
          card: '#1C2541',
          border: '#3A506B',
          accent: '#48E5C2',
          highlight: '#5BC0BE',
        }
      },
      fontFamily: {
        sans: ['Outfit', 'Inter', 'system-ui', 'sans-serif'],
      },
      boxShadow: {
        glass: '0 8px 32px 0 rgba(0, 0, 0, 0.37)',
        glow: '0 0 20px rgba(54, 171, 247, 0.35)',
        'glow-emerald': '0 0 20px rgba(16, 185, 129, 0.35)',
      },
    },
  },
  plugins: [],
}
