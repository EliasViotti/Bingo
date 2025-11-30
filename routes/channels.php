<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});


Broadcast::channel('bingo.{codigoJuego}', function ($user, $codigoJuego) {
    return true; // público: permite a cualquiera escuchar
});

