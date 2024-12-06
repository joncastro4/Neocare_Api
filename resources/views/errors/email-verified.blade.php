<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verified</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #e8f5e9;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            text-align: center;
        }

        .container {
            background: #ffffff;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 30px;
            max-width: 500px;
            animation: fadeIn 0.8s ease-in-out;
        }

        h1 {
            font-size: 2rem;
            color: #4CAF50;
            margin-bottom: 15px;
        }

        p {
            font-size: 1.1rem;
            margin: 10px 0;
        }

        p strong {
            color: #2e7d32;
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
            color: #4CAF50;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="icon">✅</div>
        <h1>{{ $message }}</h1>
        <p><strong>User:</strong> {{ $user->name }}</p>
        <p><strong>Nurse ID:</strong> {{ $nurse->id }}</p>
        <p><strong>Admin:</strong> {{ $admin->name }}</p>
    </div>
</body>

</html>
