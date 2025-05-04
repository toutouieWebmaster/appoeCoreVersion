<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/APPOE/app/main.php'); ?>
<!doctype html>
<html lang="<?= LANG; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= WEB_TITLE; ?> - <?= trans('En maintenance'); ?></title>
    <style>
        body {
            box-sizing: border-box;
            overflow: hidden;
            position: absolute;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 10px 20px;
        }

        h1 {
            font-size: 50px;
            text-align: left;
        }

        body {
            font: 20px Helvetica, sans-serif;
            color: #333;
            margin: 0;
        }

        article {
            display: block;
            max-width: 800px;
            margin: 0 auto;
        }

        article p {
            text-align: justify;
        }

        a {
            color: #dc8100;
            text-decoration: none;
        }

        a:hover {
            color: #333;
            text-decoration: none;
        }
    </style>
</head>
<body>
<article>
    <h1><?= trans('On revient bientôt'); ?></h1>
    <div>
        <p><?= trans('Désolé pour la gêne occasionnée, mais nous effectuons actuellement une maintenance'); ?>
            . <?= trans('Nous serons de retour en ligne sous peu'); ?>!</p>
        <p id="getIp" data-ip="<?= getIP(); ?>"><strong><?= WEB_TITLE; ?></strong></p>
    </div>
</article>
<script type="text/javascript">
    let el = document.getElementById('getIp');
    el.addEventListener('dblclick', function () {
        el.innerHTML = 'Votre adresse IP : <strong>' + el.getAttribute('data-ip') + '</strong>';
    });

    setTimeout(function () {
        window.location.replace(window.location.href);
    }, 20000);
</script>
</body>
</html>