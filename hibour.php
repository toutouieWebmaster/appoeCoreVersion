<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/app/main.php');
includePluginsFiles();
require_once(WEB_SYSTEM_PATH . 'auth_user.php');
?>
<!doctype html>
<html lang="<?= LANG; ?>">
<head>
    <meta charset="UTF-8">
    <?= getAppoeFavicon(); ?>
    <title>Connexion à <?= WEB_TITLE; ?></title>
    <style>:root {--docHeight: -1200px;}@font-face{font-family:'Source Sans Pro';font-style:normal;font-weight:200;src:url(https://fonts.gstatic.com/s/sourcesanspro/v14/6xKydSBYKcSV-LCoeQqfX1RYOo3i94_wlxdr.ttf) format('truetype')}@font-face{font-family:'Source Sans Pro';font-style:normal;font-weight:300;src:url(https://fonts.gstatic.com/s/sourcesanspro/v14/6xKydSBYKcSV-LCoeQqfX1RYOo3ik4zwlxdr.ttf) format('truetype')}*{box-sizing:border-box;margin:0;padding:0;font-weight:300}body{font-family:'Source Sans Pro',sans-serif;color:#fff;font-weight:300}body ::-webkit-input-placeholder{font-family:'Source Sans Pro',sans-serif;color:#fff;font-weight:300}body :-moz-placeholder{font-family:'Source Sans Pro',sans-serif;color:#fff;opacity:1;font-weight:300}body ::-moz-placeholder{font-family:'Source Sans Pro',sans-serif;color:#fff;opacity:1;font-weight:300}body :-ms-input-placeholder{font-family:'Source Sans Pro',sans-serif;color:#fff;font-weight:300}.hibourContainer{background:#50a3a2;background:linear-gradient(to bottom right,#50a3a2 0,#53e3a6 100%);position:absolute;top:0;left:0;width:100%;height:100vh;margin:0;overflow:hidden;display:flex}.hibourContainer.form-success .container h1{transform:translateY(85px);text-transform:unset !important}.container{max-width:600px;margin:auto;padding:20px 0;text-align:center}.container h1{line-height: 25px;text-transform:unset !important;font-size:40px;transition-duration:1s;transition-timing-function:ease-in;font-weight:200}form{padding:20px 0;position:relative;z-index:2}form input{-webkit-appearance:none;-moz-appearance:none;appearance:none;outline:0;border:1px solid rgba(255,255,255,.4);background-color:rgba(255,255,255,.2);width:250px;border-radius:3px;padding:10px 15px;margin:0 auto 10px auto;display:block;text-align:center;font-size:18px;color:#fff;transition-duration:.25s;font-weight:300}form input:hover{background-color:rgba(255,255,255,.4)}form input:focus{background-color:#fff;width:300px;color:#53e3a6}form button{-webkit-appearance:none;-moz-appearance:none;appearance:none;outline:0;background-color:#fff;border:0;padding:10px 15px;color:#53e3a6;border-radius:3px;width:250px;cursor:pointer;font-size:18px;transition-duration:.25s}form button:hover{background-color:#f5f7f9}.bg-bubbles{position:absolute;top:0;left:0;width:100%;height:100%;z-index:1}.bg-bubbles li{position:absolute;list-style:none;display:block;width:40px;height:40px;background-color:rgba(255,255,255,.15);bottom:-160px;-webkit-animation:square 25s infinite;animation:square 25s infinite;transition-timing-function:linear}.bg-bubbles li:nth-child(1){left:10%}.bg-bubbles li:nth-child(2){left:20%;width:80px;height:80px;-webkit-animation-delay:2s;animation-delay:2s;-webkit-animation-duration:17s;animation-duration:17s}.bg-bubbles li:nth-child(3){left:25%;-webkit-animation-delay:4s;animation-delay:4s}.bg-bubbles li:nth-child(4){left:40%;width:60px;height:60px;-webkit-animation-duration:22s;animation-duration:22s;background-color:rgba(255,255,255,.25)}.bg-bubbles li:nth-child(5){left:70%}.bg-bubbles li:nth-child(6){left:80%;width:120px;height:120px;-webkit-animation-delay:3s;animation-delay:3s;background-color:rgba(255,255,255,.2)}.bg-bubbles li:nth-child(7){left:32%;width:160px;height:160px;-webkit-animation-delay:7s;animation-delay:7s}.bg-bubbles li:nth-child(8){left:55%;width:20px;height:20px;-webkit-animation-delay:15s;animation-delay:15s;-webkit-animation-duration:40s;animation-duration:40s}.bg-bubbles li:nth-child(9){left:25%;width:10px;height:10px;-webkit-animation-delay:2s;animation-delay:2s;-webkit-animation-duration:40s;animation-duration:40s;background-color:rgba(255,255,255,.3)}.bg-bubbles li:nth-child(10){left:90%;width:160px;height:160px;-webkit-animation-delay:11s;animation-delay:11s}p.return{width:250px;margin:5px auto;display:flex;justify-content:space-between;align-items:center}p.return a{color:#fff;text-decoration:none}@-webkit-keyframes square{0%{transform:translateY(0)}100%{transform:translateY(var(--docHeight)) rotate(600deg)}}@keyframes square{0%{transform:translateY(0)}100%{transform:translateY(var(--docHeight)) rotate(600deg)}}</style>
</head>
<body>
<div class="hibourContainer">
    <div class="container">
        <div id="dateContainer"><span><?= displayCompleteDate(date('d/m/Y')); ?></span></div>
        <h1><?= trans('Bienvenue'); ?></h1>
        <form class="form" id="loginForm" action="" method="post">
            <input type="text" maxlength="70" name="loginInput" id="emailInput"
                   value="<?= !empty($_POST['loginInput']) ? $_POST['loginInput'] : ''; ?>"
                   required="required" placeholder="<?= trans('Login'); ?>">
            <input type="password" id="passwordInput" name="passwordInput" required="required"
                   placeholder="<?= trans('Mot de passe'); ?>">
            <?= getFieldsControls(); ?>
            <button type="submit" name="APPOECONNEXION" id="login-button"><?= trans('Connexion'); ?></button>
            <p class="return"><a href="/">‹ Revenir au site</a><img src="<?= getLogo(APP_IMG_URL . 'appoe-logo-white-sm.png'); ?>" alt="APPOE" style="width: 25px;height: 100%;"></p>
            <span style="color:#000;"><?php App\Flash::display(); ?></span>
        </form>
    </div>
    <ul class="bg-bubbles"><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li></ul>
</div>
<script type="text/javascript">
    let body = document.body, html = document.documentElement;
    let height = Math.max( body.scrollHeight, body.offsetHeight,
        html.clientHeight, html.scrollHeight, html.offsetHeight ) + 200;
    let root = document.documentElement;
    root.style.setProperty('--docHeight', '-' + height + 'px');
</script>
</body>
</html>