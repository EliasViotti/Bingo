import axios from "axios";
window.axios = axios;
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

import Echo from "laravel-echo";

import Pusher from 'pusher-js'; // <--- IMPORTANTE: Importar Pusher
window.Pusher = Pusher;

console.log("[bootstrap] cargado");
console.log(window.Echo);
// Instanciación protegida

try {
    const echoInstance = new Echo({
        broadcaster: "reverb",
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST ?? window.location.hostname,
        wsPort: Number(import.meta.env.VITE_REVERB_PORT ?? 80),
        wssPort: Number(import.meta.env.VITE_REVERB_PORT ?? 443),
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? "https") === "https",
        enabledTransports: ["ws", "wss"],
        // si más adelante usás subruta detrás de proxy:
        // wsPath: '/ws',
    });

    console.log("[bootstrap] echoInstance (post new):", echoInstance);
    window.Echo = echoInstance;
    console.log("[bootstrap] window.Echo asignado:", window.Echo);
} catch (e) {
    console.error("[bootstrap] Error creando Echo:", e);
}


