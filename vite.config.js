import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

const host = process.env.VITE_HOST || '127.0.0.1';
const port = Number(process.env.VITE_PORT || process.env.PORT || 5180);

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/fiscalizer-theme.css',
                'resources/sass/app.scss',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        host,
        port,
        strictPort: true,
        hmr: {
            host,
            port,
        },
    },
resolve: {
        alias: {
            '@': '/resources/js', // permite importar arquivos como "@/global"
        },
    },
});
