/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Cairo', 'sans-serif'], // تفعيل خط كايرو كخط افتراضي
      },
    },
  },
  plugins: [],
}