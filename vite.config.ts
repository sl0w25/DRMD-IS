import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.ts'],
            ssr: 'resources/js/ssr.ts',
            refresh: true,
        }),
        tailwindcss(),
        wayfinder({
            formVariants: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],

    // server: {
    // host: '0.0.0.0',
    // port: 5173,
    // https: false, // simpler for dev
    // cors: {
    //     origin: 'http://172.31.37.155', // Laravel backend
    //     methods: ['GET','POST','PUT','DELETE','OPTIONS'],
    //     credentials: true,
    // },
    // }
});
