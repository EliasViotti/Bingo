<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Control del Bingo</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            min-height: 100vh;
            padding: 20px;
            font-family: Arial, sans-serif;
        }

        .control-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .panel {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            margin-bottom: 20px;
        }

        .bola-gigante {
            width: 250px;
            height: 250px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 120px;
            font-weight: bold;
            color: white;
            margin: 30px auto;
            box-shadow: 0 15px 35px rgba(245, 87, 108, 0.4);
            animation: aparecer 0.5s ease;
        }

        @keyframes aparecer {
            from {
                transform: scale(0) rotate(-180deg);
                opacity: 0;
            }

            to {
                transform: scale(1) rotate(0deg);
                opacity: 1;
            }
        }

        .boton-sortear {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 20px 60px;
            font-size: 24px;
            font-weight: bold;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }

        .boton-sortear:hover:not(:disabled) {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.6);
        }

        .boton-sortear:disabled {
            background: #ccc;
            cursor: not-allowed;
            box-shadow: none;
        }

        .grid-sorteados {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(50px, 1fr));
            gap: 10px;
            margin-top: 20px;
        }

        .numero-sorteado {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #666;
            font-size: 18px;
        }

        .numero-sorteado.ultimo {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            animation: resaltar 1s ease;
        }

        @keyframes resaltar {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.3);
            }
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin: 20px 0;
        }

        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
        }

        .stat-number {
            font-size: 48px;
            font-weight: bold;
            margin: 10px 0;
        }

        .ganador-anuncio {
            background: linear-gradient(135deg, #4caf50 0%, #8bc34a 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            font-size: 24px;
            margin: 20px 0;
            animation: aparecer 0.5s ease;
        }
    </style>
</head>

<body>
    <div class="control-container">
        <!-- Header -->
        <div class="panel" style="text-align: center;">
            <h1 style="color: #11998e; font-size: 48px; margin: 0;">🎰 CONTROL DE BINGO 🎰</h1>
            <p style="font-size: 20px; color: #666; margin: 10px 0;">Código del Juego:
                <strong>{{ $juego->codigo }}</strong>
            </p>
            <p style="color: #999;">Comparte este código con los jugadores para que se unan</p>
            <div style="margin-top: 15px;">
                <a href="/bingo/tarjeta/{{ $juego->codigo }}" target="_blank"
                    style="background: #667eea; color: white; padding: 10px 20px; border-radius: 25px; text-decoration: none; display: inline-block;">
                    🎫 Abrir Nueva Tarjeta
                </a>
            </div>
        </div>

        <!-- Panel Principal -->
        <div class="panel">
            <div style="text-align: center;">
                <h2 style="color: #333; margin-bottom: 20px;">Sorteo Actual</h2>

                <div id="bola-container" style="display: none;">
                    <div class="bola-gigante" id="bola-numero">?</div>
                </div>

                <div id="mensaje-inicial" style="padding: 50px; color: #999; font-size: 20px;">
                    Presiona el botón para comenzar el sorteo
                </div>

                <button id="btn-sortear" class="boton-sortear" onclick="sortearNumero()">
                    🎲 SORTEAR NÚMERO
                </button>
            </div>

            <!-- Estadísticas -->
            <div class="stats">
                <div class="stat-card">
                    <div>Números Sorteados</div>
                    <div class="stat-number" id="total-sorteados">0</div>
                    <div>de 100</div>
                </div>
                <div class="stat-card">
                    <div>Último Número</div>
                    <div class="stat-number" id="ultimo-stat">-</div>
                </div>
                <div class="stat-card">
                    <div>Estado</div>
                    <div class="stat-number" style="font-size: 24px; margin-top: 20px;" id="estado-juego">
                        {{ $juego->estado === 'esperando' ? '⏳ Esperando' : ($juego->estado === 'jugando' ? '▶️ Jugando' : '✅ Finalizado') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Anuncio de Ganador -->
        <div id="ganador-anuncio" style="display: none;" class="ganador-anuncio">
            <h2 style="margin: 0 0 10px 0;">🎉 ¡TENEMOS UN GANADOR! 🎉</h2>
            <p id="ganador-info" style="margin: 0; font-size: 28px;"></p>
        </div>

        <!-- Panel de Números Sorteados -->
        <div class="panel">
            <h2 style="color: #333; margin-bottom: 20px;">Números Sorteados</h2>
            <div class="grid-sorteados" id="grid-sorteados">
                <!-- Los números se agregarán aquí dinámicamente -->
            </div>
            <p id="sin-numeros" style="text-align: center; color: #999; padding: 30px;">
                Aún no se han sorteado números
            </p>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // --- 1. DATOS INICIALES ---
            const codigoJuego = '{{ $juego->codigo }}';
            // Convertimos a array de JS
            let numerosSorteados = @json($juego->numeros_sorteados ?? []); 
            let juegoFinalizado = {{ $juego->estado === 'finalizado' ? 'true' : 'false' }};
    
            // --- 2. ESTADO INICIAL ---
            if (numerosSorteados.length > 0) {
                const ultimo = numerosSorteados[numerosSorteados.length - 1];
                // Simulamos que se acaba de mostrar el último para inicializar la vista
                mostrarNumeroSorteado(ultimo, false); // false = sin animación
                actualizarGridSorteados();
            }
    
            if (juegoFinalizado) {
                bloquearJuego();
            }
    
            // Definir config global por si echo.js lo necesita (opcional)
            window.juegoConfig = {
                codigoJuego: codigoJuego,
                numerosSorteados: numerosSorteados,
                juegoFinalizado: juegoFinalizado,
            };
    
            // --- 3. CONEXIÓN WEBSOCKET (La parte que faltaba) ---
            const iniciarConexionControl = () => {
                if (!window.Echo) {
                    console.log("[Control] Esperando a Echo...");
                    setTimeout(iniciarConexionControl, 100);
                    return;
                }
    
                console.log("[Control] Escuchando canal:", `bingo.${codigoJuego}`);
    
                window.Echo.channel(`bingo.${codigoJuego}`)
                    .listen('.numero.sorteado', (data) => {
                        console.log("⚡ Evento Recibido:", data);
                        
                        // 1. Agregar al array local
                        const nuevoNumero = parseInt(data.numero);
                        
                        // Evitar duplicados visuales si el evento llega dos veces
                        if (!numerosSorteados.includes(nuevoNumero)) {
                            numerosSorteados.push(nuevoNumero);
                            
                            // 2. Actualizar UI
                            mostrarNumeroSorteado(nuevoNumero, true); // true = con animación
                            actualizarGridSorteados();
                        }
                        
                        // 3. Reactivar botón si estaba deshabilitado por el fetch
                        const btn = document.getElementById('btn-sortear');
                        if(btn) {
                            btn.disabled = false;
                            btn.textContent = '🎲 SORTEAR NÚMERO';
                        }
                    })
                    .listen('.juego.ganado', (data) => {
                        console.log("Ganador detectado:", data);
                        juegoFinalizado = true;
                        bloquearJuego();
                        alert(`¡JUEGO FINALIZADO!\nGanador: ${data.tarjeta.nombre}`);
                    });
            };
    
            iniciarConexionControl();
    
            // --- 4. FUNCIÓN SORTEAR (Fetch) ---
            // Hacemos global la función para que el onclick del HTML funcione
            window.sortearNumero = function() {
                if (juegoFinalizado) return;
    
                const boton = document.getElementById('btn-sortear');
                boton.disabled = true;
                boton.textContent = '🎲 Sorteando...';
    
                fetch(`/bingo/juego/${codigoJuego}/sortear`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(res => res.json())
                .then(data => {
                    console.log('Orden enviada. Esperando WebSocket...', data);
                    // NO actualizamos la UI aquí. Esperamos al evento .listen de arriba
                    // para asegurar que todos vean lo mismo al mismo tiempo.
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al sortear número');
                    boton.disabled = false;
                    boton.textContent = '🎲 SORTEAR NÚMERO';
                });
            };
    
            // --- 5. FUNCIONES DE UI ---
    
            function mostrarNumeroSorteado(numero, animar = true) {
                const msgInicial = document.getElementById('mensaje-inicial');
                if(msgInicial) msgInicial.style.display = 'none';
    
                const bolaContainer = document.getElementById('bola-container');
                const bolaNumerElement = document.getElementById('bola-numero');
    
                if(bolaContainer) bolaContainer.style.display = 'block';
                
                if(bolaNumerElement) {
                    bolaNumerElement.textContent = numero;
                    
                    if(animar) {
                        bolaNumerElement.style.animation = 'none';
                        bolaNumerElement.offsetHeight; // Trigger reflow
                        bolaNumerElement.style.animation = 'aparecer 0.5s ease';
                    }
                }
    
                // Actualizar estadísticas simples
                const statUltimo = document.getElementById('ultimo-stat');
                const statTotal = document.getElementById('total-sorteados');
                const statEstado = document.getElementById('estado-juego');
    
                if(statUltimo) statUltimo.textContent = numero;
                if(statTotal) statTotal.textContent = numerosSorteados.length;
                if(statEstado) statEstado.textContent = '▶️ Jugando';
            }
    
            function actualizarGridSorteados() {
                const grid = document.getElementById('grid-sorteados');
                const sinNumeros = document.getElementById('sin-numeros');
    
                if (!grid) return;
    
                if (numerosSorteados.length === 0) {
                    if(sinNumeros) sinNumeros.style.display = 'block';
                    grid.innerHTML = '';
                    return;
                }
    
                if(sinNumeros) sinNumeros.style.display = 'none';
                grid.innerHTML = '';
    
                // Renderizar grid
                numerosSorteados.forEach((numero, index) => {
                    const div = document.createElement('div');
                    div.className = 'numero-sorteado';
                    // Efecto visual para el último
                    if (index === numerosSorteados.length - 1) {
                        div.classList.add('ultimo');
                    }
                    div.textContent = numero;
                    grid.appendChild(div);
                });
            }
    
            function bloquearJuego() {
                const btn = document.getElementById('btn-sortear');
                if(btn) {
                    btn.disabled = true;
                    btn.textContent = '🏁 JUEGO FINALIZADO';
                    btn.classList.add('opacity-50', 'cursor-not-allowed');
                }
                const estado = document.getElementById('estado-juego');
                if(estado) estado.textContent = '🏁 Finalizado';
            }
        });
    </script>
</body>

</html>
