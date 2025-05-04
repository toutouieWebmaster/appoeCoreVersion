<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/APPOE/app/main.php'); ?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= WEB_TITLE; ?> - <?= trans('Erreur'); ?></title>
    <style>
        html,
        body {
            margin: 0;
            padding: 0;
            height: 100%;
        }

        body {
            font-family: Helvetica, Arial, sans-serif;
            background-color: #2D72D9;
            color: #fff;
            -webkit-font-smoothing: antialiased;
        }

        .error-container {
            text-align: center;
            height: 100%;
        }

        .error-container h1 {
            margin: 0;
            font-size: 220px;
            font-weight: 300;
            position: relative;
            top: 50%;
            -ms-transform: translateY(-50%);
            transform: translateY(-50%);
        }

        @media (max-width: 768px) {

            .error-container h1 {
                font-size: 70px;
            }
        }

        @media (max-width: 480px) {

            .error-container {
                position: relative;
                top: 50%;
                height: initial;
                -ms-transform: translateY(-50%);
                transform: translateY(-50%);
            }

            .error-container h1 {
                font-size: 40px;
            }
        }


        @media (min-width: 480px) {

            .return {
                position: absolute;
                width: 100%;
                bottom: 30px;
            }
        }

        .return {
            color: rgba(255, 255, 255, 0.6);
            font-weight: 400;
            letter-spacing: -0.04em;
            margin: 0;
        }

        .return a {
            padding-bottom: 1px;
            color: #fff;
            text-decoration: none;
            border-bottom: 1px solid rgba(255, 255, 255, 0.6);
            -webkit-transition: border-color 0.1s ease-in;
            transition: border-color 0.1s ease-in;
        }

        .return a:hover {
            border-bottom-color: #fff;
        }
    </style>
</head>
<body>
<div class="error-container">
    <h1><?= trans('Erreur'); ?> !</h1>
    <p class="return"><a href="javascript:history.back()"><?= trans('Revenir en arrière'); ?></a></p>
</div>
</body>
</html>