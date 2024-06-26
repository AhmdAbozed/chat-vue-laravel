import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path'
import { resolve } from 'url';
export default defineConfig({
    resolve: {
            alias: {
                '@/': resolve(__dirname, 'resources/js')
            }
        },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
         vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        })
    ],
   
});
