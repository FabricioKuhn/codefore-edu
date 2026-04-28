import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    // 👇 ADICIONE ESTE BLOCO AQUI
    server: {
        host: '127.0.0.1', // Força o IPv4 normal (evita o [::1])
        cors: true,        // Permite que o escola-a.local puxe os arquivos do Vite
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
        }),
    ],
});