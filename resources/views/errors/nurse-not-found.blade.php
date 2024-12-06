<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Not Found</title>
    <style>
        body {
            font-family: 'Roboto', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #FF8A65, #FF5722);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            text-align: center;
        }

        .container {
            background: #ffffff;
            color: #333333;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            max-width: 500px;
            animation: fadeIn 0.8s ease-in-out;
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            color: #FF5722;
        }

        p {
            font-size: 1.2rem;
            margin-top: 10px;
            line-height: 1.5;
        }

        .icon {
            font-size: 4rem;
            color: #FF5722;
            margin-bottom: 15px;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="icon">🚫</div>
        <h1>Error</h1>
        <p>{{ $message }}</p>
    </div>
</body>

</html>
