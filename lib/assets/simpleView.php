<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/app/main.php'); ?>
<!doctype html>
<html lang="<?= LANG; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/jpg" href="<?= WEB_APP_URL; ?>images/appoe-favicon.png">
    <link rel="stylesheet" type="text/css" href="<?= WEB_TEMPLATE_URL; ?>css/appoe.css">
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <title><?= WEB_TITLE; ?> - {{title}}</title>
    <style>
        body {
            margin: 0;
            background-color: #3eb293;
            color: #FFF;
            font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;
        }

        div#container {
            display: flex;
            min-height: 100vh;
        }

        div#content {
            margin: auto;
            text-align: center;
            opacity: 0;
            transform: translateY(30px);
            max-width: 80%;
            transition: all 0.4s;
        }

        h1 {
            font-size: 4em;
            font-weight: 900;
        }

        p {
            font-size: 1em;
            font-weight: 400;
        }
    </style>
</head>
<body>
<div id="container">
    <div id="content">
        <h1>{{title}}</h1>
        <p>{{content}}</p>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('#content').animate({opacity: 1}, 200).delay(500).css('transform', 'translateY(0)');
    });
</script>
</body>
</html>