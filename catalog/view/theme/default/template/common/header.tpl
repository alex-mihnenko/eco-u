<!DOCTYPE html>
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" itemscope itemtype="http://schema.org/WebPage">
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

	<!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
    <link rel="stylesheet" href="/new_design/css/style.<?php echo $cssversion; ?>.css">
    <link rel="stylesheet" href="/new_design/css/remodal.css">
    <link rel="stylesheet" href="/new_design/css/ecomodal.css">
    <link rel="stylesheet" href="/new_design/css/selectric.css">
    <link rel="stylesheet" href="/new_design/css/slick.css">
    <!--<link rel="stylesheet" href="/new_design/css/dd.css">-->

    <link rel="stylesheet" href="/new_design/css/jquery-ui.css">
    <link rel="stylesheet" href="/new_design/css/jquery.jscrollpane.css">

    <link href="catalog/view/theme/default/stylesheet/core.<?php echo $cssversion; ?>.css" rel="stylesheet">

    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,500i,700,900&amp;subset=cyrillic-ext" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/suggestions-jquery@17.10.1/dist/css/suggestions.min.css" type="text/css" rel="stylesheet" />

    <!-- Libs -->
    <link rel="stylesheet" href="catalog/view/libs/enjoyhint-master/enjoyhint.css">
</head>
<body>
	<div class="wreaper">
		<header class="sticker" style="position:absolute;left:0;right:0;top:0;z-index:333;">
			<div class="size-0">
				<div class="h-box_left">
					<div class="rel">
						<div class="h-menu">Меню</div>
						<div class="hidden-menu">
							<a href="/#l-p_35" class="h-m_link menu-cat-link">Каталог товаров</a>
							<a href="#l-p_new" class="h-m_link menu-cat-link" data-action="scrollto">Новинки</a>
							<a href="/about/#delivery" target="_blank" class="h-m_link">Доставка</a>
							<a href="/about/#payment" target="_blank" class="h-m_link">Оплата</a>
							<a href="/about/#return" target="_blank" class="h-m_link">Возвраты</a>
							<a href="/about/#contacts" target="_blank" class="h-m_link">Контакты</a>
							<a href="/about" target="_blank" class="h-m_link">О нас</a>
							<!-- <php foreach($categories as $category) { if($category['name']!='Косметикаа') {?> -->
	                        <!--a href="<=$category['href']?>" class="h-m_link"><=$category['name']?></a-->
	                    	<!-- <php }} ?> -->
						</div>
						<div class="b-b_phone" data-remodal-target="modal-phone"></div>
					</div>
				</div>
				<div class="h-box_center">
					<a href="/" class="b-logo">
						<img src="/new_design/img/logo.png" alt="">
					</a>
					<a href="/" class="b-logo_3">
						<img src="/new_design/img/logo_3.png" alt="">
					</a>
				</div>
				<div class="h-box_right">
	                                   
					<div class="all-b_basket">
						<!--
						<a href="/cart" class="b-basket_mobile"> 
							<div class="b-b_price"></span></div>
							<div class="b-b_quantity">0</div>
						</a>
						-->
	                    
	                    <div class="b-basket" data-remodal-target="modal-basket">
	                        <div class="b-b_price"></div>
	                        <div class="b-b_quantity">0</div> 
	                    </div>
	                    <?php echo $cart; ?>

	                    <!--
						<div class="hidden-basket">
							<div class="h-b_box">
								<div class="h-b_title">Корзина</div>
								<div class="h-b_15">
									<div class="cart-container">
										
									</div>
									<a href="/cart" class="h-b_buy">Купить (<span class="cart-price-total">0</span> руб)</a>
								</div>
							</div>
	                        <a href="/cart"><div style="width:40px;height:40px;position:absolute;top:0px;right:93px;"></div></a>
						</div>
						-->

					</div>
					<?php if(!$customer_id) { ?>
	                    <div class="b-profile" data-remodal-target="modal">Войти</div>
	                <?php } else { ?>
	                    <a href="/my-account"><div class="b-profile"><?php if(!empty($customer_firstname)) echo $customer_firstname; else { ?> Личный кабинет <?php } ?></div></a>
	                <?php } ?>
				</div>
			</div>
		</header>

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
								<input type="text" placeholder="+7 (___) ___-__-__" class="input" name="phone" required="">
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
							<input type="tel" placeholder="+7 (___) ___-__-__" class="input" name="phone" required="">
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
								<input type="tel" placeholder="+7 (___) ___-__-__" class="input" name="phone" style="text-align: center;" required="">
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


			<?php if( date('G', time()) >= 9 && date('G', time()) < 18 ) { ?>
				<div class="body t-c_box">
					<div class="form">
						<div class="t-c_title" style="text-align: center; margin-bottom: 10px;">Обратный звонок</div>
						<hr class="indent sm">

						<form id="form-phone" action="/index.php" method="POST">
							<div class="t-c_input">
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
					<p class="t-c_subtitle">Мы работаем без выходных<br>Принимаем звонки с 9-00 до 18-00</p>

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