<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NeoCare - Nurse Activation</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #FFE6E6; padding: 20px;">

    <div
        style="max-width: 600px; margin: 0 auto; background-color: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
        <!-- Header -->
        <div style="background-color: #7469B6; color: white; padding: 20px; text-align: center;">
            <h2 style="margin: 0;">NeoCare Notification</h2>
        </div>

        <!-- Content -->
        <div style="padding: 20px;">
            <p>Hello Admin,</p>
            <p>The user <strong>{{ $person->name }} {{ $person->last_name_1 }}</strong> is ready to be activated as a
                Nurse in the system.</p>
            <p>Please click the button below to confirm their role activation:</p>
            <a href="{{ $signedUrl }}"
                style="display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: #AD88C6; color: white; text-decoration: none; border-radius: 5px;">Activate
                Role</a>
            <p style="margin-top: 20px;">If you didn't request this, please ignore this email.</p>
        </div>

        <!-- Footer -->
        <div style="background-color: #E1AFD1; color: white; text-align: center; padding: 10px;">
            <p style="margin: 0;">&copy; 2024 NeoCare. All rights reserved.</p>
        </div>
    </div>

</body>

</html>