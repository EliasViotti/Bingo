<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bingo Online</title>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
        }

        .container {
            background: white;
            padding: 50px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .boton {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px 40px;
            font-size: 18px;
            border-radius: 25px;
            cursor: pointer;
            margin: 10px;
            text-decoration: none;
            display: inline-block;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 style="color: #667eea; font-size: 48px;">🎲 Bingo Online 🎲</h1>
        <p style="color: #666; font-size: 18px;">Elige una opción para comenzar</p>

        <form action="{{ route('bingo.juego.crear') }}" method="POST" style="margin: 30px 0;">
            @csrf
            <button type="submit" class="boton">🎰 Crear Nuevo Juego</button>
        </form>
    </div>
</body>

</html>