<!-- NEWS LETTER -->
<!--<div class="page-section nl-cont">
    <div class="container">
        <div class="relative" >
            <div id="mc_embed_signup" class="nl-form-container clearfix">
                <form action="http://abcgomel.us9.list-manage.com/subscribe/post-json?u=ba37086d08bdc9f56f3592af0&amp;id=e38247f7cc&amp;c=?" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="newsletterform validate" target="_blank" novalidate>
                    <input type="email" value="" name="EMAIL" class="email nl-email-input" id="mce-EMAIL" placeholder="Enter your email" required>

                    <div style="position: absolute; left: -5000px;"><input type="text" name="b_ba37086d08bdc9f56f3592af0_e38247f7cc" tabindex="-1" value=""></div>

                    <input type="submit" value="SUBSCRIBE" name="subscribe" id="mc-embedded-subscribe" class="button medium gray">
                </form>
                <div id="notification_container"  ></div>
            </div>
        </div>
    </div>
</div>-->
<footer id="footer2" class="page-section pt-80 pb-50 noPrint">
	<div class="container">
		<div class="row">

			<div class="col-md-3 col-sm-3 widget">
				<div class="logo-footer-cont">
					<a href="/">
						<img class="logo-footer" src="/APPOE/public/images/logo-footer.png" alt="logo">
					</a>
				</div>
				<div class="footer-2-text-cont">
					<address>
						555 California str, Suite 100<br>
						San&nbsp;Francisco, CA 94107
					</address>
				</div>
				<div class="footer-2-text-cont">
					1-800-312-2121<br>
					1-800-310-1010
				</div>
				<div class="footer-2-text-cont">
					<a class="a-text" href="mailto:info@haswell.com">info@haswell.com</a>
				</div>
			</div>

			<div class="col-md-3 col-sm-3 widget">
				<h4>NAVIGATE</h4>
				<ul class="links-list bold a-text-cont">
					<li><a href="/">HOME</a></li>
					<li><a href="/">SERVICES</a></li>
					<li><a href="/">PORTFOLIO</a></li>
					<li><a href="/">BLOG</a></li>
					<li><a href="/">SHOP</a></li>
					<li><a href="/">PAGES</a></li>
				</ul>
			</div>

			<div class="col-md-3 col-sm-3 widget">
				<h4>ABOUT US</h4>
				<ul class="links-list a-text-cont">
					<li><a href="/">COMPANY</a></li>
					<li><a href="/">WHAT WE DO</a></li>
					<li><a href="/">HELP CENTER</a></li>
					<li><a href="/">TERMS OF SERVICE</a></li>
					<li><a href="/contact">CONTACT</a></li>
				</ul>
			</div>

			<div class="col-md-3 col-sm-3 widget">
				<h4>RECENT POSTS</h4>
				<div id="post-list-footer">

					<div class="post-prev-title">
						<h3><a class="a-text" href="/">New trends in web design</a></h3>
					</div>
					<div class="post-prev-info">
						Jule 10
					</div>

					<div class="post-prev-title">
						<h3><a class="a-text" href="/">The sound of life</a></h3>
					</div>
					<div class="post-prev-info">
						October 10
					</div>

					<div class="post-prev-title">
						<h3><a class="a-text" href="/">Time for minimalism</a></h3>
					</div>
					<div class="post-prev-info">
						September 21
					</div>

				</div>
			</div>
		</div>

		<div class="footer-2-copy-cont clearfix">
			<!-- Social Links -->
			<div class="footer-2-soc-a right">
				<a href="/" title="Facebook"
				   target="_blank"><i class="fa fa-facebook"></i></a>
				<a href="/" title="Twitter" target="_blank"><i
						class="fa fa-twitter"></i></a>
				<a href="/" title="Behance" target="_blank"><i
						class="fa fa-behance"></i></a>
				<a href="/" title="LinkedIn+" target="_blank"><i
						class="fa fa-linkedin"></i></a>
				<a href="/" title="Dribbble"
				   target="_blank"><i class="fa fa-dribbble"></i></a>
			</div>

			<!-- Copyright -->
			<div class="left">
				<a class="footer-2-copy" href="/"
				   target="_blank"><?= getAppoeCredit(); ?></a>
			</div>
		</div>
	</div>
</footer>

<!-- BACK TO TOP -->
<p id="back-top">
	<a href="#top" title="Back to Top"><span class="icon icon-arrows-up"></span></a>
</p>

</div><!-- End BG -->
</div><!-- End wrap -->

<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="<?= WEB_PUBLIC_URL; ?>js/bootstrap.min.js"></script>

<!-- MAGNIFIC POPUP -->
<script src='<?= WEB_PUBLIC_URL; ?>js/jquery.magnific-popup.min.js'></script>

<!-- PORTFOLIO SCRIPTS -->
<script type="text/javascript" src="<?= WEB_PUBLIC_URL; ?>js/isotope.pkgd.min.js"></script>
<script type="text/javascript" src="<?= WEB_PUBLIC_URL; ?>js/imagesloaded.pkgd.min.js"></script>
<script type="text/javascript" src="<?= WEB_PUBLIC_URL; ?>js/masonry.pkgd.min.js"></script>

<!-- COUNTER -->
<script type="text/javascript" src="<?= WEB_PUBLIC_URL; ?>js/jquery.countTo.js"></script>

<!-- APPEAR -->
<script type="text/javascript" src="<?= WEB_PUBLIC_URL; ?>js/jquery.appear.js"></script>

<!-- MAIN SCRIPT -->
<script src="<?= WEB_PUBLIC_URL; ?>js/main.js?v=1"></script>

<!-- SLIDER REVOLUTION 4.x SCRIPTS  -->
<script type="text/javascript" src="<?= WEB_PUBLIC_URL; ?>rs-plugin/js/jquery.themepunch.tools.min.js"></script>
<script type="text/javascript"
        src="<?= WEB_PUBLIC_URL; ?>rs-plugin/js/jquery.themepunch.revolution-parallax.min.js"></script>

<!-- SLIDER REVOLUTION INIT  -->
<script type="text/javascript">
    jQuery(document).ready(function ($) {

        if($('#insta').length) {
            let instaContainer = $('#insta');
            getInstagramTimelineFile().done(function (data) {
                let instaJson = data.graphql.user.edge_owner_to_timeline_media.edges;
                $.each(instaJson, function (i, obj) {

                    let title = obj.node.accessibility_caption;
                    let img = '<img src="' + obj.node.display_url + '" alt="' + obj.node.accessibility_caption + '">';
                    let link = data.link + obj.node.shortcode + '/';
                    let description = obj.node.owner.username ;

                    let item = '<div style="width:20rem;float:left;">'+img+'</div>';
                    instaContainer.append(item);
                });
            });
        }

        getCountShippingCard().done(function (data) {
            $('#cardCount').html(data);
        });

        if($('div.map-responsive').length){
            $('div.map-responsive').mappoe();
        }

        if ((navigator.appVersion.indexOf("Win") != -1) && (ieDetect == false)) {
            jQuery('#rs-fullscr').revolution(
                {
                    dottedOverlay: "none",
                    delay: 16000,
                    startwidth: 1170,
                    startheight: 700,
                    hideThumbs: 200,

                    thumbWidth: 100,
                    thumbHeight: 50,
                    thumbAmount: 5,

                    //fullScreenAlignForce: "off",

                    navigationType: "none",
                    navigationArrows: "solo",
                    navigationStyle: "preview4",

                    hideTimerBar: "on",

                    touchenabled: "on",
                    onHoverStop: "on",

                    swipe_velocity: 0.7,
                    swipe_min_touches: 1,
                    swipe_max_touches: 1,
                    drag_block_vertical: false,

                    parallax: "scroll",
                    parallaxBgFreeze: "on",
                    parallaxLevels: [45, 40, 35, 50],
                    parallaxDisableOnMobile: "on",

                    keyboardNavigation: "off",

                    navigationHAlign: "center",
                    navigationVAlign: "bottom",
                    navigationHOffset: 0,
                    navigationVOffset: 20,

                    soloArrowLeftHalign: "left",
                    soloArrowLeftValign: "center",
                    soloArrowLeftHOffset: 20,
                    soloArrowLeftVOffset: 0,

                    soloArrowRightHalign: "right",
                    soloArrowRightValign: "center",
                    soloArrowRightHOffset: 20,
                    soloArrowRightVOffset: 0,

                    shadow: 0,
                    fullWidth: "off",
                    fullScreen: "on",

                    spinner: "spinner4",

                    stopLoop: "off",
                    stopAfterLoops: -1,
                    stopAtSlide: -1,

                    shuffle: "off",

                    autoHeight: "off",
                    forceFullWidth: "off",

                    hideThumbsOnMobile: "off",
                    hideNavDelayOnMobile: 1500,
                    hideBulletsOnMobile: "off",
                    hideArrowsOnMobile: "off",
                    hideThumbsUnderResolution: 0,

                    hideSliderAtLimit: 0,
                    hideCaptionAtLimit: 0,
                    hideAllCaptionAtLilmit: 0,
                    startWithSlide: 0,
                    //fullScreenOffsetContainer: ""
                });
        } else {
            jQuery('#rs-fullscr').revolution(
                {
                    dottedOverlay: "none",
                    delay: 16000,
                    startwidth: 1170,
                    startheight: 700,
                    hideThumbs: 200,

                    thumbWidth: 100,
                    thumbHeight: 50,
                    thumbAmount: 5,

                    navigationType: "none",
                    navigationArrows: "solo",
                    navigationStyle: "preview4",

                    hideTimerBar: "on",

                    touchenabled: "on",
                    onHoverStop: "on",

                    swipe_velocity: 0.7,
                    swipe_min_touches: 1,
                    swipe_max_touches: 1,
                    drag_block_vertical: false,

                    parallax: "mouse",
                    parallaxBgFreeze: "on",
                    parallaxLevels: [0],
                    parallaxDisableOnMobile: "on",

                    keyboardNavigation: "off",

                    navigationHAlign: "center",
                    navigationVAlign: "bottom",
                    navigationHOffset: 0,
                    navigationVOffset: 20,

                    soloArrowLeftHalign: "left",
                    soloArrowLeftValign: "center",
                    soloArrowLeftHOffset: 20,
                    soloArrowLeftVOffset: 0,

                    soloArrowRightHalign: "right",
                    soloArrowRightValign: "center",
                    soloArrowRightHOffset: 20,
                    soloArrowRightVOffset: 0,

                    shadow: 0,
                    fullWidth: "off",
                    fullScreen: "on",

                    spinner: "spinner4",

                    stopLoop: "off",
                    stopAfterLoops: -1,
                    stopAtSlide: -1,

                    shuffle: "off",

                    autoHeight: "off",
                    forceFullWidth: "off",

                    hideThumbsOnMobile: "off",
                    hideNavDelayOnMobile: 1500,
                    hideBulletsOnMobile: "off",
                    hideArrowsOnMobile: "off",
                    hideThumbsUnderResolution: 0,

                    hideSliderAtLimit: 0,
                    hideCaptionAtLimit: 0,
                    hideAllCaptionAtLilmit: 0,
                    startWithSlide: 0,

                });
        }
    });	//ready
</script>

<!-- JS end -->
<?php getAsset('cookies'); ?>
</body>
</html>