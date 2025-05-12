<div id="cookieInfo"
     style="display:none;position: fixed; bottom: 0; left: 0; min-width: 340px; width:100%; max-width: 700px; padding: 20px; background: rgba(255,255,255,0.9); color: #000; box-sizing: border-box;z-index: 999999;text-align: left;font-size: 16px;line-height: 20px;">
    <h3 style="margin-top: 0;line-height: 25px;font-size: 22px;">Nous utilisons des cookies de Google Analytics</h3>
    <p>Ce site Web utilise des cookies qui nous permettent d'analyser le trafic.<br>
        Ces informations ne sont pas partagées avec des partenaires d'analyse ou autres.</p>
    <button style="float: left; cursor:pointer; border: 0; margin-top:5px; padding: 10px 20px;
    background:#692727; color: #FFF;display: block;width: auto;" id="refuseCookies">REFUSER
    </button>
    <button style="float: right; cursor:pointer; border: 0; margin-top:5px; padding: 10px 20px;
    background:#000; color: #FFF;display: block;width: auto;" id="acceptCookies">ACCEPTER
    </button>
</div>
<script type="text/javascript">
    if (document.body.hasAttribute('data-ua')) {

        function getGoogleAnalytics() {

            let ua = document.body.dataset.ua;

            let s = document.createElement('script');
            s.type = 'text/javascript';
            s.src = 'https://www.googletagmanager.com/gtag/js?id=' + ua;
            document.body.appendChild(s);

            let uas = document.createElement('script');
            uas.type = 'text/javascript';
            uas.innerHTML = 'window.dataLayer = window.dataLayer || [];' +
                'function gtag() {dataLayer.push(arguments);}' +
                'gtag("js", new Date());' +
                'gtag("config", "'+ua+'");';
            document.body.appendChild(uas);
        }

        //Check if User accepted cookies
        if (getCookie('acceptCookies')) {
            getGoogleAnalytics();

        } else {
            if (!getCookie('refuseCookies')) {

                document.getElementById('cookieInfo').style.display = 'block';

                //The user accepts cookies
                document.getElementById('acceptCookies').addEventListener('click', function () {
                    setCookie('acceptCookies', 'OK', '365');
                    document.getElementById('cookieInfo').style.display = 'none';
                    getGoogleAnalytics();
                }, {passive: true});

                //The user refuse cookies
                document.getElementById('refuseCookies').addEventListener('click', function () {
                    setCookie('refuseCookies', 'OK', '365');
                    document.getElementById('cookieInfo').style.display = 'none';
                }, {passive: true});
            }
        }
    }
</script>