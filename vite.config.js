import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    // ADICIONE ESSE BLOCO SERVER AQUI:
    server: {
        hmr: {
            host: 'localhost',
        },
        cors: true, // Isso libera o acesso de domínios como escola-a.local
    },
});