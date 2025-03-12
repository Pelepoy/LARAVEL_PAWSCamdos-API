<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Request</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }

        .container {
            background: white;
            padding: 30px 40px 30px 40px;
            border-radius: 20px;
            max-width: 600px;
            margin: auto;
            text-align: left;
            color: gray;
            border: 1px solid;
            box-shadow: 5px 10px #888888;
        }


        .button {
            background-color: #2d3748;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            text-align: center;
        }

        .button-container {
            display: flex;
            justify-content: center;
            margin: 25px 0 25px 0;
        }

        .footer {
            font-size: 12px;
            color: gray;
            margin-top: 20px;
            line-height: 1.5;
        }

        .long-url {
            word-wrap: break-word;
            overflow-wrap: break-word;
            word-break: break-word;
            display: inline;
        }

        .signature {
            margin-top: 20px;
        }

        .divider {
            border-top: 1px solid #ddd;
            margin-top: 20px;
            padding-top: 10px;
        }

        .gray {
            color: #2d3748;
            text-align: center;
            margin-bottom: 40px;
            ;
        }

        .name {
            color: #2d3748;
            font-weight: bold;
        }

        .footer {
            font-size: 14px;
            color: gray;
        }

        .inline-link {
            display: inline;
            word-break: break-all;
            overflow-wrap: break-word;
        }
    </style>
</head>

<body>
    <div class="container">

        <h2 class="gray">🛠️ Let's Fix This – Reset Your Password!</h2>
        <p>Hi <span class="name">{{ $user->first_name }},</span></p>
        <p>You are receiving this email because we received a password reset request for your account.</p>
        <p>Please click the button below to reset your password:</p>
        <div class="button-container">
            <a href="{{ $resetUrl }}" class="button">Reset Your Password</a>
        </div>
        <p>If you did not request a password reset, no further action is required.</p>
        <p class="signature">Regards,<br>Pekomamushi</p>
        <div class="divider"></div>
        <p class="footer">
            If you're having trouble clicking the <strong>"Reset Password"</strong> button, copy and paste the URL below
            into
            your web browser:
            <span class="inline-link"><a href="{{ $resetUrl }}">{{ $resetUrl }}</a></span>
        </p>
    </div>
</body>

</html>
