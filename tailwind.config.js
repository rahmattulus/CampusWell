/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
      },
      colors: {
        canvas: '#F4F3F0',
        surface: '#FFFFFF',
        'surface-soft': '#FAF9F7',
        line: '#E7E5E0',
        ink: {
          DEFAULT: '#17181A',
          soft: '#4B5058',
          mute: '#7A808A',
        },
        sage: {
          DEFAULT: '#E3EBD3',
          strong: '#7F9A66',
        },
        lavender: {
          DEFAULT: '#E8E1F3',
          strong: '#8E7BBF',
        },
        sky: {
          DEFAULT: '#DCE7F7',
          strong: '#6F93CC',
        },
        butter: {
          DEFAULT: '#F8ECCB',
          strong: '#C99A2E',
        },
        status: {
          'normal-bg': '#DDEBD8',
          'normal-fg': '#2F6B3A',
          'self-bg': '#FBEBC8',
          'self-fg': '#8A5A00',
          'risk-bg': '#FBDDD8',
          'risk-fg': '#A5372B',
        },
      },
      borderRadius: {
        card: '24px',
        'card-sm': '16px',
        control: '12px',
      },
      boxShadow: {
        card: '0 1px 2px rgba(23,24,26,.04)',
        float: '0 8px 24px rgba(23,24,26,.10)',
      },
    },
  },
  plugins: [],
};
