<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Verified</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #e3fcef;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            color: #333;
        }

        .container {
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
            animation: fadeIn 0.8s ease-in-out;
        }

        h1 {
            font-size: 2rem;
            color: #4CAF50;
            margin-bottom: 15px;
        }

        p {
            font-size: 1.1rem;
            color: #555;
        }

        p strong {
            color: #2e7d32;
        }

        .icon {
            font-size: 4rem;
            color: #4CAF50;
            margin-bottom: 20px;
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
        <div class="icon">️✅</div>
        <h1>{{ $message }}</h1>
        <p><strong>User:</strong> {{ $user->name }}</p>
    </div>
</body>

</html>