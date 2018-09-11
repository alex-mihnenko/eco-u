<!DOCTYPE html>
<!--[if IE]><![endif]-->
<!--[if IE 8 ]><html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" class="ie8"><![endif]-->
<!--[if IE 9 ]><html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" class="ie9"><![endif]-->
<!--[if (gt IE 9)|!(IE)]><!-->
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" itemscope itemtype="http://schema.org/WebPage">
<!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $title; ?></title>
	<base href="<?php echo $base; ?>" />
	<?php if ($description) { ?>
	<meta name="description" content="<?php echo $description; ?>" />
	<?php } ?>
	<?php if ($keywords) { ?>
	<meta name="keywords" content= "<?php echo $keywords; ?>" />
	<?php } ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php foreach ($styles as $style) { ?>
	<link href="<?php echo $style['href']; ?>" type="text/css" rel="<?php echo $style['rel']; ?>" media="<?php echo $style['media']; ?>" />
	<?php } ?>

    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <!-- <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,500i,700,900&amp;subset=cyrillic-ext" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/suggestions-jquery@17.10.1/dist/css/suggestions.min.css" type="text/css" rel="stylesheet" /> -->

    <?php foreach ($analytics as $analytic) { ?>
	<?php echo $analytic; ?>
	<?php } ?>
</head>

<?php if (stripslashes($route['_route_']) == 'eda/') { ?>
<body>
<?php } else { ?>
<body class="page_2">
<?php } ?>

	<div class="wreaper">
		<nav>
			<a href="/" class="logo"><img src="/catalog/view/theme/default/img/logo.png" alt="ЭКО-Ю" title="ЭКО-Ю"></a>

			<div class="grid-container">
				<div class="grid-row hidden-md hidden-lg hidden-xl hidden-xxl">
					<div class="grid-col col-50 align-start">
		
                		<a class="svg-container pointer" href="#empty" data-marker="auth-button">
							<i class="svg" data-src="icon-user.svg"><?php loadSvg('name', 'icon-user.svg'); ?></i>
						</a>

						<a class="svg-container pointer" href="<?php echo $logout; ?>" data-marker="logout-button" style="display: none;">
							<i class="svg logout" data-src="icon-logout.svg"><?php loadSvg('name', 'icon-logout.svg'); ?></i>
						</a>

						<div class="svg-container pointer" data-remodal-target="modal-phone">
							<i class="svg" data-src="icon-phone.svg"><?php loadSvg('name', 'icon-phone.svg'); ?></i>
						</div>

						<div class="svg-container pointer">
							<div class="flex flex-row">
								<div class="search" data-style="default">
									<input type="text" name="search" value="" placeholder="Поиск..." />
									<button class="btn-close btn-xs" data-action="search-close"></button>
								</div>
								<i class="svg" data-src="icon-search.svg" data-action="search-open"><?php loadSvg('name', 'icon-search.svg'); ?></i>
							</div>
						</div>
					</div>

					<div class="grid-col col-50 align-end">
						<div class="svg-container pointer cart" data-remodal-target="modal-basket">
							<i class="svg" data-src="icon-bucket.svg"><?php loadSvg('name', 'icon-bucket.svg'); ?></i>
							<span class="counter">0</span>
						</div>

						<div class="svg-container pointer dropdown inverse menu" data-marker="mobile-menu">
							<i data-action="toggle" tabindex="-1"><span data-marker="first-line"></span><span data-marker="second-line"></span><span data-marker="third-line"></span></i>

							<div class="list">
								<a href="/#l-p_new" class="item with-icon" data-action="scrollto" scroll-anchor="#l-p_new"> <div style="background: url(/catalog/view/theme/default/img/svg/icon-new-white.svg) no-repeat center center scroll; -webkit-background-size: contain; -moz-background-size: contain; -o-background-size: contain; background-size: contain;" class="category-icon"></div> Новинки</a>
								<!-- <a href="/#l-p_sale" class="item with-icon" data-action="scrollto" scroll-anchor="#l-p_sale"> <div style="background: url(/catalog/view/theme/default/img/svg/icon-sale-white.svg) no-repeat center center scroll; -webkit-background-size: contain; -moz-background-size: contain; -o-background-size: contain; background-size: contain;" class="category-icon"></div> Скидки</a> -->
								<div class="item sub-dropdown">
									<span data-action="toggle">Каталог товаров</span>

									<div class="list">
										<div class="sub-list">
											<?php foreach($categories as $i => $category) { ?>

									            <?php if( $category['id'] != 'new' && $category['id'] != 'sale' ) { ?>
									                <a class="item with-icon"  href="#l-p_<?php echo $category['id']; ?>" data-action="scrollto" scroll-anchor="#l-p_<?php echo $category['id']; ?>">
									                    <?php if(!empty($category['image'])) { ?><div style="background: url(/image/<?php echo $category['image']; ?>) no-repeat center center scroll; -webkit-background-size: contain; -moz-background-size: contain; -o-background-size: contain; background-size: contain;" class="category-icon"></div><?php } ?>
									                    <?php echo $category['name']; ?>
									                </a>
									            <?php } ?>

									        <?php } ?>
										</div>
									</div>
								</div>
								<a href="/about/#delivery" class="item" target="_blank">Доставка</a>
								<a href="/about/#payment" class="item" target="_blank">Оплата</a>
								<a href="/about/#return" class="item" target="_blank">Возвраты</a>
								<a href="/about/#contacts" class="item" target="_blank">Контакты</a>
								<a href="/blog" class="item">Блог</a>
								<a href="/testimonials" class="item">Отзывы</a>
								<a href="/about" class="item" target="_blank">О нас</a>
							</div>
						</div>
					</div>
				</div>

				<div class="grid-row hidden-xs hidden-sm">
					<div class="grid-col col-50 align-start">
						<div class="svg-container pointer dropdown menu" tabindex="-1" data-marker="menu">
							<i data-action="toggle"><span data-marker="first-line"></span><span data-marker="second-line"></span><span data-marker="third-line"></span></i>

							<div class="list">
								<a href="/#l-p_35" class="item" data-action="scrollto" scroll-anchor="#l-p_35">Каталог товаров</a>
								<a href="/#l-p_new" class="item" data-action="scrollto" scroll-anchor="#l-p_new">Новинки</a>
								<!-- <a href="/#l-p_sale" class="item" data-action="scrollto" scroll-anchor="#l-p_sale">Скидки</a> -->
								<a href="/about/#delivery" class="item" target="_blank">Доставка</a>
								<a href="/about/#payment" class="item" target="_blank">Оплата</a>
								<a href="/about/#return" class="item" target="_blank">Возвраты</a>
								<a href="/about/#contacts" class="item" target="_blank">Контакты</a>
								<a href="/blog" class="item">Блог</a>
								<a href="/testimonials" class="item">Отзывы</a>
								<a href="/about" class="item" target="_blank">О нас</a>
							</div>
						</div>

						<a class="svg-container pointer" href="#empty" data-marker="auth-button">
							<i class="svg" data-src="icon-user.svg"><?php loadSvg('name', 'icon-user.svg'); ?></i>
						</a>

						<a class="svg-container pointer" href="<?php echo $logout; ?>" data-marker="logout-button" style="display: none;">
							<i class="svg logout" data-src="icon-logout.svg"><?php loadSvg('name', 'icon-logout.svg'); ?></i>
						</a>


						<div class="svg-container pointer" data-remodal-target="modal-phone">
							<i class="svg" data-src="icon-phone.svg"><?php loadSvg('name', 'icon-phone.svg'); ?></i>
						</div>
					</div>

					<div class="grid-col col-50 align-end">
						<div class="svg-container pointer">
							<div class="flex flex-row">
								<div class="search" data-style="default">
									<input type="text" name="search" value="" placeholder="Поиск..." />
									<button class="btn-close btn-xs" data-action="search-close"></button>
								</div>
								<i class="svg" data-src="icon-search.svg" data-action="search-open"><?php loadSvg('name', 'icon-search.svg'); ?></i>
							</div>
						</div>

						<div class="svg-container pointer cart" data-remodal-target="modal-basket">
							<i class="svg" data-src="icon-bucket.svg"><?php loadSvg('name', 'icon-bucket.svg'); ?></i>
							<span class="counter">0</span>
						</div>
					</div>
				</div>
			</div>
		</nav>

		<div class="remodal modal-profile modal-sm" data-remodal-id="modal">
			<button data-remodal-action="close" class="remodal-close"></button>

			<ul class="tabs__caption clearfix">
				<li class="registration">Я новый покупатель</li>
				<li class="auth active">Я уже покупал</li> 
			</ul>
			<div class="tabs__content">
				<div class="t-c_box">
					<div class="form">
						<div class="t-c_title">Зарегистрироваться</div>

						<form id="form-registration" class="js-hide_1" action="/auth.php" method="POST" target="ph_iframe">
							<div class="t-c_input">
								<input type="text" placeholder="Ваше имя" class="input i-2" name="firstname" required="">
								<span class="underline"></span>
							</div>
							<div class="t-c_input">
								<input type="text" placeholder="Номер телефона" class="input" name="phone" required="">
								<span class="underline"></span>
							</div>
		                    
		                    <div class="message-error"></div>
							<input type="submit" value="Зарегистрироваться" class="m-p_entrance">
						</form>
					</div>

					<div class="message success">
						<div class="wrap-table">
							<div class="wrap-cell">
								<img src="catalog/view/theme/default/img/svg/paper-plane.svg" class="svg md" alt="paper-plane" title="paper-plane" />
								<br><br>

								<div class="message-success"></div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="tabs__content active">
				<div class="t-c_box">
					<div class="t-c_title">Войти</div>

					<form id="form-auth" class="js-hide_1" action="/auth.php" method="POST" target="ph_iframe">
						<div class="t-c_input">
							<input type="text" placeholder="Номер телефона" class="input" name="phone" required="">
							<span class="underline"></span>
						</div>
						<div class="t-c_input">
							<input name="password" type="password" placeholder="&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;" class="input i-2" required="">
							<span class="underline"></span>
						</div>
	                    
	                    <div class="message-error"></div>
						<input type="submit" value="Войти" class="m-p_entrance">
						
						<div>
							<div class="m-p_forgot" data-action="auth-recovery-init">Напомнить пароль?</div>
						</div>
					</form>
				</div>
			</div>
		</div>

		<div class="remodal modal-recovery modal-sm" data-remodal-id="modal-recovery">
			<button data-remodal-action="close" class="remodal-close"></button>

			<div class="tabs__content active">
				<div class="t-c_box">
					<div class="form">
						<div class="t-c_title" style="text-align: center;">Личный кабинет</div>
						<br><br>

						<form id="form-recovery" action="/auth.php" method="POST">
							<div class="t-c_input">
								<input type="text" placeholder="Номер телефона" class="input" name="phone" style="text-align: center;" required="">
								<span class="underline"></span>
							</div>
		                    
		                    <div class="message-error" style="text-align: center;"></div>
							<input type="submit" value="Напомнить пароль">
						</form>
					</div>

					<div class="message success">
						<div class="wrap-table">
							<div class="wrap-cell">
								<img src="catalog/view/theme/default/img/svg/paper-plane.svg" class="svg md" alt="paper-plane" title="paper-plane" />
								<br><br>

								<div class="message-success"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="remodal modal-coupon modal-sm" data-remodal-id="modal-coupon">
			<button data-remodal-action="close" class="remodal-close"></button>

			<div class="tabs__content active">
				<div class="t-c_box">
					<div class="form">
						<div class="t-c_title" style="text-align: center;">Купон на скидку</div>
						<br><br>

						<form id="form-coupon" action="/auth.php" method="POST">
							<div class="t-c_input">
								<input type="text" placeholder="Введите купон" class="input" id="coupon" name="coupon" style="text-align: center;" required="">
								<span class="underline"></span>
							</div>
		                    
		                    <div class="message-error" style="text-align: center;"></div>
							<input type="submit" value="Применить">
						</form>
					</div>
				</div>
			</div>
		</div>

		<div class="remodal modal-phone modal-sm" data-remodal-id="modal-phone">
			<button data-remodal-action="close" class="remodal-close"></button>


			<?php if( date('G', time()) >= 10 && date('G', time()) < 18 ) { ?>
				<div class="body t-c_box">
					<div class="form">
						<div class="t-c_title" style="text-align: center; margin-bottom: 10px;">Обратный звонок</div>
						<hr class="indent sm">

						<form id="form-phone" action="/index.php" method="POST">
							<div class="t-c_input input-group required">
								<input type="text" placeholder="Введите номер телефона" class="input" id="phone" name="phone" style="text-align: center;" required="">
								<span class="underline"></span>
							</div>

		                    
		                    <div class="message-success" style="text-align: center;"></div>
							<input type="submit" value="Заказать" class="btn">
						</form>
					</div>

					<div class="message success">
						<div class="wrap-table">
							<div class="wrap-cell">
								<img src="catalog/view/theme/default/img/svg/paper-plane.svg" class="svg md" alt="paper-plane" title="paper-plane" />
								<br><br>

								<div class="message-success"></div>
							</div>
						</div>
					</div>
				</div>
			<?php } else { ?>
				<div class="body">
					<hr class="indent lg">
					<hr class="indent lg">

					<div class="t-c_title" style="text-align: center; margin-bottom: 10px;">Наши телефоны</div>
					<hr class="indent xs">
					<p class="t-c_subtitle">Мы работаем без выходных<br>Принимаем звонки с 10-00 до 18-00</p>

					<hr class="indent lg">
					<hr class="indent lg">
				</div>
			<?php } ?>



			<div class="footer-row">
				<div class="col">
					<span>Забота о клиентах</span><br>
					<a class="phone_link" href="tel:+74951081876" itemprop="telephone">+7 (495) 108-18-76</a>
				</div>
				<div class="col">
					<span>Сотрудничество</span><br>
					<a class="phone_link" href="tel:+74951081876">+7 499 (404)-12-26</a>
				</div>
			</div>
		</div>

		<div class="remodal modal-privacy modal-md" data-remodal-id="modal-privacy">
        	<button data-remodal-action="close" class="remodal-close"></button>

        	<div class="body">
		        <p class="h3 text-align-center">Согласие на обработку персональных данных</p>
		        <br>

        		<div class="text-align-left">
			        <p>
			        	ООО «ЭКО-Ю» (далее – ЭКО-Ю) предпринимает все разумные меры по защите полученных персональных данных от уничтожения, искажения или разглашения. Настоящим субъект персональных указывая свои данные на сайте http://www.eco-u.ru/ дает согласие ЭКО-Ю, расположенному по адресу 125080, Москва, Волоколамское шоссе, 6-76, на обработку своих персональных данных, включая сбор, систематизацию, накопление, хранение, уточнение (обновление, изменение), использование, распространение (в том числе передачу, включая трансграничную передачу данных), обезличивание, блокирование, уничтожение персональных данных, в том числе с использованием средств автоматизации в целях:
			       	</p>

			        <ul class="list">
			        	<li>
			        		заключения и исполнения договора по инициативе субъекта персональных данных или договора, по которому субъект персональных 
			        		данных будет являться выгодоприобретателем или поручителем;
			        	</li>
			        	<li>
			        		анализа покупательского поведения и улучшения качества предоставляемых ЭКО-Ю товаров и услуг,
				        	а также предоставления субъекту персональных данных информации коммерческого и информационного характера (в том числе о специальных предложениях и акциях ЭКО-Ю) через различные каналы связи, в том числе по почте, смс, электронной почте, телефону, если субъект персональных данных изъявит желание на получение подобной информации соответствующими средствами связи.
				        </li>
			        </ul>
			        
			        <p>
			        	Помимо ЭКО-Ю, доступ к своим персональным данным имеют сами субъекты; лица, в том числе партнеры ЭКО-Ю, осуществляющие продажу товаров ЭКО-Ю, поддержку служб и сервисов ЭКО-Ю, в необходимом для осуществления такой поддержки объеме; иные лица, права и обязанности которых по доступу к соответствующей информации установлены законодательством РФ.
			        </p>
			        
			        <p>
			        	ЭКО-Ю гарантирует соблюдение следующих прав субъекта персональных данных: право на получение сведений о том, какие персональные данные субъекта персональных данных хранятся у ЭКО-Ю; право на удаление, уточнение или исправление хранящихся у ЭКО-Ю персональных данных; иные права, установленные действующим законодательством РФ.
			        </p>
			        
			        <p>
			        	ЭКО-Ю обязуется немедленно прекратить обработку персональных данных после получения соответствующего требования субъекта персональных данных, оформленного в письменной форме.
			        </p>
			        
			        <p>
			        	Согласие субъекта персональных данных на обработку персональных данных действует бессрочно и может быть в любой момент отозвано субъектом персональных данных путем письменного обращения в адрес ЭКО-Ю по адресу: 117292, Москва, ул. Кедрова, дом 6, к 2,
			        </p>
			        
			        <p>
			        	Настоящим субъект персональных данных обязуется:
			        </p>

			        <ul class="list">
			        	<li>
			        		не представляться чужим именем или от чужого имени (частного лица или организации);
			        	</li>
			        	<li>
			        		не указывать заведомо недостоверную информацию и информацию, идентифицирующую третьих лиц или в отношении третьих лиц.
				        </li>
			        </ul>
			        
			        <p>
			        	Настоящим субъект персональных данных, предоставляя их ЭКО-Ю, подтверждает достоверность указанной информации и соглашается на их обработку на указанных условиях.
			        </p>
	   			</div>
	   		</div>
	    </div> 

	    <div class="remodal modal-product modal-lg" data-remodal-id="modal-product">
        	<button data-remodal-action="close" class="remodal-close"></button>

        	<div class="body">
	   		</div>
	    </div>


	    <div class="remodal modal-blog modal-lg" data-remodal-id="modal-blog">
        	<button data-remodal-action="close" class="remodal-close"></button>

        	<div class="body">
	   		</div>
	    </div>


	    <div class="remodal modal-payment modal-sm" data-remodal-id="modal-payment">
			<button data-remodal-action="close" class="remodal-close"></button>

			<div class="body">

				<div class="message success">
					<div class="wrap-table">
						<div class="wrap-cell">
							<img src="catalog/view/theme/default/img/svg/check-square.svg" class="svg md" alt="paper-plane" title="paper-plane" />
							<hr class="indent md">

							<div class="message-success"></div>
						</div>
					</div>
				</div>

			</div>
		</div>

	    <?php echo $cart; ?>