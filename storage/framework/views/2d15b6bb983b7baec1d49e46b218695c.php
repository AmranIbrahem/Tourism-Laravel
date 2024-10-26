<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Email Verification</title>
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

        .header {
            background-color: #6b00ff;
            padding: 10px;
            border-radius: 5px 5px 0 0;
            color: #ffffff;
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
        }

        .content {
            color: #333333;
            font-size: 16px;
            line-height: 24px;
            margin-bottom: 10px;
        }

        .code {
            background-color: #3b5998;
            color: #ffffff;
            padding: 10px;
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .coode {
            background-color: #3b5998;
            color: #ffffff;
            padding: 10px;
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            border-radius: 50%;
            margin-bottom: 20px;
        }

        .support {
            color: #666666;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .signature {
            color: #3b5998;
            font-weight: bold;
            font-size: 16px;
        }
        .hidden-link {
            display: inline-block;
            background-color: #3b5998;
            color: #ffffff;
            padding: 10px;
            font-size: 18px;
            font-weight: bold;
            text-decoration: none;
            border-radius: 5px;
        }

        .hidden-link:active {
            display: none;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        Add Admin
    </div>
    <div class="content">
        <p>Hi <?php echo e($name); ?>,</p>
        <p>Added you to Admin website.</p>
    </div>
    <div class="coode">
        <a href="<?php echo e($link); ?>" class="hidden-link">Press On </a>
    </div>

    <div class="content">
        <p>If you have any questions, feel free to contact our support team.</p>
        <p class="support">Thank you,</p>
        <p class="support">Support Team</p>
    </div>
    <div class="signature">
        &copy; 2024 Movie App
    </div>
</div>
</body>
</html>
<?php /**PATH D:\Back\Projects\MovieApp\resources\views/emails/AddAdmin.blade.php ENDPATH**/ ?>