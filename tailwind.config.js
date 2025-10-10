import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: 'class',
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
    "./vendor/laravel/jetstream/**/*.blade.php",
    "./vendor/livewire/livewire/src/**/*.php",
  ],
  theme: {
    extend: {
      boxShadow: {
        'soft': '0 10px 30px -12px rgba(0,0,0,0.18)',
      }
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
    require('@tailwindcss/line-clamp'),
  ],
   safelist: [
    // toast
    'bg-emerald-600', 'bg-amber-500', 'bg-rose-600', 'text-white',
    // small helpers used in buttons
    'hover:bg-indigo-50', 'hover:bg-amber-50',
    'border-amber-500', 'border-indigo-600',
    'ring-1', 'ring-black/10', 'shadow-lg',
  ],
}
