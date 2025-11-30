document.addEventListener("DOMContentLoaded", () => {
    if (!window.Echo) {
        console.error("[echo.js] Echo no está inicializado");
        return;
    }
    const { codigoJuego, numerosSorteados, juegoFinalizado } =
        window.juegoConfig ?? {};
    if (!codigoJuego) {
        console.error("[echo.js] Falta codigoJuego");
        return;
    }

    window.Echo.channel(`bingo.${codigoJuego}`)
        .listen(".numero.sorteado", (data) => {
            console.log("Número sorteado recibido:", data);
            // TODO: actualizar tu UI aquí: bola, grid, stats…
        })
        .listen(".juego.ganado", (data) => {
            console.log("Juego ganado:", data);
            // TODO: finalizar UI aquí…
        });
});
