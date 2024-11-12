<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
    <h1 style="
        text-align: center;
        font-size: 40px;
        font-weight: bold;
        color: #000000;
    ">
        Hello {{ $name }},<br>
    </h1>
    <h2 style="
        text-align: center;
        font-size: 20px;
        font-weight: bold;
        color: #000000;
    ">
        Please click the link below to verify your email.<br>
        <a href="{{ $signedUrl }}" style="
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            color: #000000;
        ">Activate</a>
    </h2>
</body>
</html>