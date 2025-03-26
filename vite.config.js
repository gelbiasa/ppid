import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js',
                'resources/css/summernote.css',
                'resources/js/summernote.js'
            ],
            refresh: true,
        }),
    ],
});