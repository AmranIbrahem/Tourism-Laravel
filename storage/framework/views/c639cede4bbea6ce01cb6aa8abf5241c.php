s<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Safety and security</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333333;
            font-size: 24px;
            margin-bottom: 20px;
        }

        p {
            color: #666666;
            font-size: 16px;
            line-height: 24px;
            margin-bottom: 10px;
        }

        a {
            color: #009688;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Update Password</h1>
    <p>Hello <?php echo e($name); ?></p>
    <p>This is the password update code please put this code:</p>
    <p style="background-color: #ff0000; color: #ffffff; padding: 5px; font-size: 18px; font-weight: bold;"><?php echo e($recovery_code); ?></p>
    <p>If you have any questions, feel free to contact our support team.</p>
    <p>Thank you,</p>
    <p>Support Team</p>
</div>
</body>
</html>
<?php /**PATH D:\Back\Projects\MovieApp\resources\views/emails/email-password.blade.php ENDPATH**/ ?>