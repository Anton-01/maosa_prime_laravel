import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import preset from './vendor/filament/support/tailwind.config.preset'

/** @type {import('tailwindcss').Config} */
export default {
    presets: [preset],
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './app/Livewire/**/*.php',
        './resources/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
        './resources/views/admin/**/*.blade.php',
        './resources/views/livewire/**/*.blade.php',
        './vendor/filament/tables/resources/views/**/*.blade.php',
    ],
    important: true,
    corePlugins: {
        preflight: false,
    },
    theme: {
        extend: {},
    },
    plugins: [forms],
};
