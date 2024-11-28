<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verified</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 50px;
        }

        h1 {
            color: #4CAF50;
        }
    </style>
</head>

<body>
    <h1>{{ $message }}</h1>
    <p><strong>User:</strong> {{ $user->name }}</p>
    <p><strong>Nurse ID:</strong> {{ $nurse->id }}</p>
    <p><strong>Admin:</strong> {{ $admin->name }}</p>
</body>

</html>