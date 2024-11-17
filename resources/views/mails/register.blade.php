<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activación de Cuenta - Neocare</title>
</head>

<body style="
    font-family: Arial, sans-serif;
    background-color: #FFE6E6;
    color: #4A4A4A;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
">
    <div style="
        background-color: #FFFFFF;
        border-radius: 15px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        padding: 40px;
        text-align: center;
        max-width: 400px;
        margin: auto;
    ">
        <h1 style="
            font-size: 36px;
            font-weight: bold;
            color: #7469B6;
            margin-bottom: 10px;
        ">
            ¡Hola, {{ $name }}!
        </h1>
        <h2 style="
            font-size: 20px;
            font-weight: bold;
            color: #AD88C6;
            margin-bottom: 30px;
        ">
            Bienvenido a Neocare
        </h2>
        <p style="
            font-size: 16px;
            color: #4A4A4A;
            margin-bottom: 30px;
        ">
            Incubando el futuro. Por favor, haz clic en el siguiente enlace para verificar tu correo electrónico.
        </p>
        <a href="{{ $signedUrl }}" style="
            background-color: #7469B6;
            color: #FFFFFF;
            font-size: 18px;
            padding: 15px 30px;
            border-radius: 10px;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s ease;
        " onmouseover="this.style.backgroundColor='#AD88C6'" onmouseout="this.style.backgroundColor='#7469B6'">
            Activar Cuenta
        </a>
    </div>
</body>

</html>