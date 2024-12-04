<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Already Verified</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #ffeb3b, #ffc107);
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .container {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 30px;
            max-width: 400px;
            text-align: center;
            animation: fadeIn 0.8s ease-in-out;
        }

        h1 {
            font-size: 2.5rem;
            color: #FFC107;
            margin-bottom: 10px;
        }

        p {
            font-size: 1.2rem;
            color: #555;
            line-height: 1.5;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .icon {
            font-size: 4rem;
            color: #FFC107;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="icon">⚠️</div>
        <h1>Notice</h1>
        <p>{{ $message }}</p>
    </div>
</body>

</html>
