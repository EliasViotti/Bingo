<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mi Tarjeta de Bingo</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .tarjeta-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .numero-bola {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: bold;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            transition: all 0.3s ease;
            cursor: pointer;
            border: 3px solid transparent;
        }

        .numero-bola.marcado {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            transform: scale(0.9);
            border-color: #0066cc;
        }

        .grid-numeros {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 15px;
            margin: 30px 0;
        }

        .ultimo-numero {
            font-size: 72px;
            font-weight: bold;
            color: #667eea;
            text-align: center;
            margin: 20px 0;
            animation: pulse 1s ease-in-out;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }
        }

        .confetti {
            position: fixed;
            width: 10px;
            height: 10px;
            background: #f0f;
            position: absolute;
            animation: confetti-fall 3s linear;
        }

        @keyframes confetti-fall {
            to {
                transform: translateY(100vh) rotate(360deg);
                opacity: 0;
            }
        }

        .ganador-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .ganador-modal.activo {
            display: flex;
        }

        .ganador-content {
            background: white;
            padding: 50px;
            border-radius: 20px;
            text-align: center;
            animation: bounce 0.5s ease;
        }

        @keyframes bounce {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.2);
            }
        }
    </style>
</head>

<body>
    <div class="tarjeta-container">
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="color: #667eea; font-size: 36px; margin-bottom: 10px;">🎲 BINGO 🎲</h1>
            <p style="color: #666;">Código de Juego: <strong>{{ $codigoJuego }}</strong></p>
            <p style="color: #666;">Tarjeta: <strong>{{ $tarjeta->codigo }}</strong></p>
            <p style="color: #666;">Jugador: <strong>{{ $tarjeta->nombre }}</strong></p>
        </div>

        <div id="ultimo-numero-container" style="display: none;">
            <p style="text-align: center; color: #666; margin-bottom: 5px;">Último número:</p>
            <div id="ultimo-numero" class="ultimo-numero"></div>
        </div>

        <div class="grid-numeros" id="grid-numeros">
            @foreach($tarjeta->lineas as $linea)
                @foreach(['n1', 'n2', 'n3', 'n4', 'n5', 'n6', 'n7', 'n8', 'n9', 'n10'] as $campo)
                    @if($linea->$campo)
                        <div class="numero-bola" data-numero="{{ $linea->$campo }}">
                            {{ $linea->$campo }}
                        </div>
                    @endif
                @endforeach
            @endforeach
        </div>

        <div style="text-align: center; margin-top: 30px;">
            <div style="display: flex; justify-content: space-between; padding: 0 20px;">
                <div>
                    <p style="color: #666; margin: 0;">Números marcados:</p>
                    <p id="contador-marcados"
                        style="font-size: 24px; font-weight: bold; color: #667eea; margin: 5px 0;">0/10</p>
                </div>
                <div>
                    <p style="color: #666; margin: 0;">Números sorteados:</p>
                    <p id="total-sorteados" style="font-size: 24px; font-weight: bold; color: #764ba2; margin: 5px 0;">0
                    </p>
                </div>
            </div>
        </div>

        <div id="estado-juego"
            style="text-align: center; margin-top: 20px; padding: 15px; background: #f0f0f0; border-radius: 10px;">
            <p style="margin: 0; color: #666;">⏳ Esperando que comience el sorteo...</p>
        </div>
    </div>

    <!-- Modal de Ganador -->
    <div id="ganador-modal" class="ganador-modal">
        <div class="ganador-content">
            <h1 style="color: #4caf50; font-size: 72px; margin: 0;">🎉 ¡BINGO! 🎉</h1>
            <p style="font-size: 24px; margin: 20px 0;">¡Felicidades, has ganado!</p>
        </div>
    </div>

    <script>
        // Datos de la tarjeta
        const codigoJuego = '{{ $codigoJuego }}';
        const tarjetaId = {{ $tarjeta->id }};
        const numerosTarjeta = [
            @foreach($tarjeta->lineas as $linea)
                @foreach(['n1', 'n2', 'n3', 'n4', 'n5', 'n6', 'n7', 'n8', 'n9', 'n10'] as $campo)
                    @if($linea->$campo)
                        {{ $linea->$campo }},
                    @endif
                @endforeach
            @endforeach
        ];

        let numerosMarcados = [];
        let juegoFinalizado = false;

        // Conectar a Echo (Reverb/Pusher)
        Echo.channel(`bingo.${codigoJuego}`)
            .listen('.numero.sorteado', (data) => {
                console.log('Número sorteado:', data);

                // Mostrar último número
                mostrarUltimoNumero(data.numero);

                // Actualizar total de números sorteados
                document.getElementById('total-sorteados').textContent = data.numerosSorteados.length;

                // Marcar número si está en la tarjeta
                if (numerosTarjeta.includes(data.numero)) {
                    marcarNumero(data.numero);
                }

                // Actualizar estado
                document.getElementById('estado-juego').innerHTML =
                    '<p style="margin: 0; color: #4caf50;">✅ Juego en progreso...</p>';
            })
            .listen('.juego.ganado', (data) => {
                console.log('Juego ganado:', data);
                juegoFinalizado = true;

                if (data.tarjeta.id === tarjetaId) {
                    mostrarModalGanador();
                    lanzarConfetti();
                } else {
                    document.getElementById('estado-juego').innerHTML =
                        `<p style="margin: 0; color: #f44336;">❌ Juego finalizado. Ganador: ${data.tarjeta.nombre}</p>`;
                }
            });

        function mostrarUltimoNumero(numero) {
            const container = document.getElementById('ultimo-numero-container');
            const elemento = document.getElementById('ultimo-numero');

            container.style.display = 'block';
            elemento.textContent = numero;
            elemento.style.animation = 'none';
            setTimeout(() => {
                elemento.style.animation = 'pulse 1s ease-in-out';
            }, 10);
        }

        function marcarNumero(numero) {
            if (juegoFinalizado || numerosMarcados.includes(numero)) return;

            numerosMarcados.push(numero);

            // Marcar visualmente
            const bola = document.querySelector(`[data-numero="${numero}"]`);
            if (bola) {
                bola.classList.add('marcado');
            }

            // Actualizar contador
            document.getElementById('contador-marcados').textContent = `${numerosMarcados.length}/10`;

            // Verificar si ganó
            if (numerosMarcados.length === 10) {
                verificarGanador();
            }
        }

        function verificarGanador() {
            fetch(`/bingo/juego/${codigoJuego}/tarjeta/${tarjetaId}/verificar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.ganador) {
                        console.log('¡Ganaste!');
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function mostrarModalGanador() {
            const modal = document.getElementById('ganador-modal');
            modal.classList.add('activo');
        }

        function lanzarConfetti() {
            for (let i = 0; i < 100; i++) {
                setTimeout(() => {
                    const confetti = document.createElement('div');
                    confetti.className = 'confetti';
                    confetti.style.left = Math.random() * 100 + '%';
                    confetti.style.background = `hsl(${Math.random() * 360}, 100%, 50%)`;
                    confetti.style.animationDelay = Math.random() * 3 + 's';
                    document.body.appendChild(confetti);

                    setTimeout(() => confetti.remove(), 3000);
                }, i * 30);
            }
        }

        // Click manual en las bolas (opcional, para testing)
        document.querySelectorAll('.numero-bola').forEach(bola => {
            bola.addEventListener('click', function () {
                if (!juegoFinalizado) {
                    const numero = parseInt(this.dataset.numero);
                    // Solo permite marcar si ya fue sorteado
                    console.log('Click en número:', numero);
                }
            });
        });
    </script>
</body>

</html>