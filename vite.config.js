import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig(({ mode }) => ({
  plugins: [
    tailwindcss(), // v4: plugin resmi Tailwind untuk Vite
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.js'],
      refresh: true,
    }),
  ],
  server: {
    host: process.env.VITE_DEV_SERVER_HOST || true,
    port: Number(process.env.VITE_DEV_SERVER_PORT) || 5173,
    watch: { usePolling: true },
    hmr: {
      host: process.env.VITE_HMR_HOST || 'localhost',
      port: Number(process.env.VITE_HMR_PORT) || 5173,
    },
  },
}))
