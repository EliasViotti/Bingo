<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JuegoController;
use App\Http\Controllers\TarjetaController;

Route::get('/', fn() => view('bingo.inicio'));

Route::prefix('bingo')->name('bingo.')->group(function () {
    //vamos al inicio
    Route::get('/inicio', function () {
        return view('bingo.inicio');
    })->name('inicio');
    //creamos un juego nuevo
    Route::post('/juego/crear', [JuegoController::class, 'crear'])->name('juego.crear');

    //vista para controlar el juego
    Route::get('/control/{codigo}', [JuegoController::class, 'control'])->name('control');

    //sorteamos numero
    Route::post('/juego/{codigo}/sortear', [JuegoController::class, 'sortearNumero'])->name('sortear');

    //creamos tarjeta para jugar
    Route::get('/tarjeta/{codigoJuego}', [TarjetaController::class, 'create'])->name('tarjeta');

    //verificamos al ganador
    Route::post(
        '/juego/{codigoJuego}/tarjeta/{tarjetaId}/verificar',
        [TarjetaController::class, 'verificarGanador']
    )->name('verificar');
});
