import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/css/home.css','resources/css/post-verification.css',
                'resources/js/delete_temp.js', 'resources/css/welcmain.css','resources/js/welcmain.js','resources/css/comp_ver.css'
                 ,'resources/js/phone.js'],
            refresh: true,
        }),
    ],
});
