export default {
  content: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.js',
  ],
  theme: {
    extend: {
      colors: {
        brand: {
          dark: '#294755',      // header background
          green: '#00871f',     // primary actions
          accent: '#ffbb00',    // menu / highlight
          light: '#f5faf7',     // page background
        },
      },
    },
  },
  plugins: [],
}
