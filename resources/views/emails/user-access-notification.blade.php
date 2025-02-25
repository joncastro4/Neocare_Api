<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Request - NeoCare</title>
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
        max-width: 500px;
        margin: auto;
    ">
        <!-- Title -->
        <h1 style="
            font-size: 28px;
            font-weight: bold;
            color: #7469B6;
            margin-bottom: 10px;
        ">
            System Access Request
        </h1>

        <!-- Subtitle -->
        <h2 style="
            font-size: 20px;
            font-weight: bold;
            color: #AD88C6;
            margin-bottom: 30px;
        ">
            NeoCare - Super Admin
        </h2>

        <!-- Message -->
        <p style="
            font-size: 16px;
            color: #4A4A4A;
            margin-bottom: 20px;
        ">
            Hello Super Admin,
        </p>
        <p style="
            font-size: 16px;
            color: #4A4A4A;
            margin-bottom: 30px;
        ">
            The user <strong>{{ $person->name }} {{ $person->last_name_1 }}</strong> (username:
            <strong>{{ $user->name }}</strong>) has requested access to the NeoCare system using the email <a
                href="mailto:{{ $user->email }}" style="color: #7469B6;">{{ $user->email }}</a>.
        </p>
        <p style="
            font-size: 16px;
            color: #4A4A4A;
            margin-bottom: 30px;
        ">
            Please review the request and grant access if appropriate.
        </p>

        <!-- Action Button -->
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
            Accept Request
        </a>

        <!-- Additional Message -->
        <p style="
            font-size: 14px;
            color: #777777;
            margin-top: 30px;
        ">
            If you do not recognize this request, you can safely ignore this email.
        </p>
    </div>
</body>

</html>