<!doctype html>
<html lang="<?= LANG; ?>">
<head>
	<title><?= getPageParam('currentPageName'); ?></title>
	<meta charset="utf-8">
	<!--[if IE]>
	<meta http-equiv="X-UA-Compatible" content="IE=edge"><![endif]-->
	<meta name="description" content="<?= getPageParam('currentPageDescription'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link rel="icon" type="image/png" sizes="32x32" href="<?= APP_LOGO_URL; ?>favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="<?= APP_LOGO_URL; ?>favicon-16x16.png">
	<meta name="apple-mobile-web-app-title" content="APPOE">
	<meta name="application-name" content="APPOE">
	<?= getMetaData(); ?>

	<!-- CSS -->
	<!-- REVOSLIDER CSS SETTINGS -->
	<link rel="stylesheet" type="text/css" href="<?= WEB_PUBLIC_URL; ?>rs-plugin/css/settings.min.css" media="screen"/>

	<!--  BOOTSTRAP -->
	<link rel="stylesheet" href="<?= WEB_PUBLIC_URL; ?>css/bootstrap.min.css">

	<!--  GOOGLE FONT -->
	<link href='https://fonts.googleapis.com/css?family=Lato:300,400,700%7COpen+Sans:400,300,700' rel='stylesheet'
	      type='text/css'>

	<!-- ICONS ELEGANT FONT & FONT AWESOME & LINEA ICONS  -->
	<link rel="stylesheet" href="<?= WEB_PUBLIC_URL; ?>css/icons-fonts.css">

	<!--  CSS THEME -->
	<link rel="stylesheet" href="<?= WEB_PUBLIC_URL; ?>css/style.css?v=2">

	<!-- ANIMATE -->
	<link rel='stylesheet' href="<?= WEB_PUBLIC_URL; ?>css/animate.min.css">
	<?php includePluginsStyles(); ?>
	<script src="<?= WEB_PUBLIC_URL; ?>js/modernizr.js"></script>

	<script type="text/javascript" src="<?= WEB_PUBLIC_URL; ?>js/jquery-1.11.2.min.js"></script>
	<?php includePluginsJs(); ?>
</head>
<body>
<!-- LOADER -->
<div id="loader-overflow">
	<div id="loader3">Veuillez activer JS</div>
</div>

<div id="wrap" class="boxed ">
	<div class="grey-bg"> <!-- Grey BG  -->
		<!-- HEADER -->
		<header id="nav" class="header header-1 noPrint">
			<div class="header-wrapper">
				<div class="container-m-30 clearfix">
					<div class="logo-row">

						<!-- LOGO -->
						<div class="logo-container-2">
							<div class="logo-2">
								<a href="/" class="clearfix">
									<img src="<?= WEB_PUBLIC_URL; ?>images/appoe-logo.png" class="logo-img" alt="Logo">
								</a>
							</div>
						</div>
						<!-- BUTTON -->
						<div class="menu-btn-respons-container">
							<button type="button" class="navbar-toggle btn-navbar collapsed" data-toggle="collapse"
							        data-target="#main-menu .navbar-collapse">
								<span aria-hidden="true" class="icon_menu hamb-mob-icon"></span>
							</button>
						</div>
					</div>
				</div>

				<!-- MAIN MENU CONTAINER -->
				<div class="main-menu-container">

					<div class="container-m-30 clearfix">

						<!-- MAIN MENU -->
						<div id="main-menu">
							<div class="navbar navbar-default" role="navigation">

								<!-- MAIN MENU LIST -->
								<nav class="collapse collapsing navbar-collapse right-1024">

									<?php if (hasMenu()): ?>
									<ul class="nav navbar-nav">
										<?php foreach (getSessionMenu() as $menuPage): ?>
											<?php if (hasSubMenu($menuPage->id)): ?>
												<li class="parent">
													<a href="#">
														<div class="main-menu-title"><?= $menuPage->name; ?></div>
													</a>
													<ul class="sub">
														<?php foreach (getSessionMenu(1, $menuPage->id) as $subMenu): ?>
															<?php if (!empty($primaryMenu[$subMenu->id])): ?>
																<li class="parent">
																<?= linkBuild($subMenu, ['activePage' => 'current', 'parent' => true]); ?>
																<ul class="sub">
																	<?php foreach (getSessionMenu(1, $subMenu->id) as $subSubMenu): ?>
																		<li>
																			<?= linkBuild($subSubMenu, ['activePage' => 'current']); ?>
																		</li>
																	<?php endforeach; ?>
																</ul>
															<?php else: ?>
																<li class="<?= activePage($subMenu->slug, 'current'); ?>">
																	<?= linkBuild($subMenu); ?>
																</li>
															<?php endif; ?>

														<?php endforeach; ?>
													</ul>
												</li>
											<?php else: ?>
												<li class="<?= activePage($menuPage->slug, 'current'); ?>"><?= linkBuild($menuPage); ?></li>
											<?php endif;
										endforeach;
										endif; ?>
										<li id="menu-cart">
											<a href="<?= webUrl('panier/'); ?>">
												<div class="main-menu-title"><span aria-hidden="true"
												                                   class="icon_cart"></span>PANIER
													(<span id="cardCount">0</span>)
												</div>
											</a>
										</li>
									</ul>
								</nav>
							</div>
						</div>
						<!-- END main-menu -->

					</div>
					<!-- END container-m-30 -->

				</div>
				<!-- END main-menu-container -->

				<!-- SEARCH READ DOCUMENTATION -->
				<ul class="cd-header-buttons">

					<li><a class="cd-search-trigger" href="#cd-search"><span></span></a></li>
				</ul> <!-- cd-header-buttons -->
				<div id="cd-search" class="cd-search">
					<form class="form-search" id="searchForm" action="page-search-results.html" method="get">
						<input type="text" value="" name="q" id="q" placeholder="Search...">
					</form>
				</div>

			</div>
			<!-- END header-wrapper -->
		</header>