<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title><?php echo $title; ?></title>
        <meta name="description" content="<?php echo $description; ?>">
        <meta name="keywords" content="<?php echo $keywords; ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
        <link rel="stylesheet" href="/new_design/css/style.css">
        <link rel="stylesheet" href="/new_design/css/remodal.css">
        <!--<link rel="stylesheet" href="/new_design/css/dd.css">-->
        <link rel="stylesheet" href="/new_design/css/selectric.css">
        <link rel="stylesheet" href="/new_design/css/slick.css">
        <link rel="stylesheet" href="/new_design/css/jquery-ui.css">
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
        <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,500i,700,900&amp;subset=cyrillic-ext" rel="stylesheet">
    </head>
    <body>
        	<div class="wreaper">
			<header class="sticker">
				<div class="size-0">
					<div class="h-box_left">
						<div class="rel">
							<div class="h-menu">еда</div>
							<div class="hidden-menu">
                                                                <?php foreach($categories as $category) { ?>
                                                                    <a href="<?=$category['href']?>" class="h-m_link"><?=$category['name']?></a>
                                                                <? } ?>
							</div>
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
							<a href="/cart" class="b-basket_mobile"> 
								<div class="b-b_price"></span></div>
								<div class="b-b_quantity">0</div>
							</a>
							
                                                        <a href="/cart">
                                                            <div class="b-basket">
                                                                <div class="b-b_price"></div>
                                                                <div class="b-b_quantity">0</div>
                                                            </div>
                                                        </a>
							<div class="hidden-basket">
								<div class="h-b_box">
									<div class="h-b_title">Корзина</div>
									<div class="h-b_15">
										<div class="cart-container">
											
										</div>
										<a href="/cart" class="h-b_buy">Купить (<span class="cart-price-total">0</span> руб)</a>
									</div>
								</div>
							</div>
						</div>
						<?php if(!$customer_id) { ?>
                                                    <div class="b-profile" data-remodal-target="modal">Войти</div>
                                                <?php } else { ?>
                                                    <a href="/my-account"><div class="b-profile"><?php if(!empty($customer_firstname)) echo $customer_firstname; else { ?> Личный кабинет <? } ?></div></a>
                                                <?php } ?>
					</div>
				</div>
			</header>
			<!-- modal <div class="b-profile" data-remodal-target="modal">Войти</div>  -->
			<div class="remodal modal-profile" data-remodal-id="modal">
				<ul class="tabs__caption clearfix">
					<li>Я новый покупатель</li>
					<li class="active">Я уже покупал</li> 
				</ul>
				<div class="tabs__content">
					<div class="m-p_shadow">
						<div class="show-registration">
							<div class="t-c_input input-error_1">
								<input type="text" placeholder="+7 (999) 999-99-99" class="input" id="phone5">
								<span class="underline"></span>
							</div>
							<div class="t-c_input input-error_1">
								<input id="password5" type="password" placeholder="&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;" class="input i-2">
								<span class="underline"></span>
							</div>
							<div class="t-c_input t-c_code">
								<input id="smscode5" type="text" placeholder="Введите SMS код" class="input">
								<span class="underline"></span>
							</div>
							<a href="#" class="m-p_registration js-reg" onclick="return false;">Зарегистрироваться</a>
                                                        <div class="sms-hint"></div>
						</div>
					</div>
				</div>
				<div class="tabs__content active">
					<div class="t-c_box">
						<div class="js-hide_1">
							<div class="t-c_title">Войти</div>
							<div class="t-c_input input-error_1">
								<input type="text" placeholder="+7 913 44 30 37" class="input" id="phone3">
								<span class="underline"></span>
							</div>
							<div class="t-c_input input-error_1">
								<input id="password3" type="password" placeholder="&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;" class="input i-2">
								<span class="underline"></span>
							</div>
							<input type="submit" value="Войти" class="m-p_entrance" data-remodal-action="confirm">
							<div>
								<div class="m-p_forgot">Напомнить пароль?</div>
							</div>
						</div>
						<div class="show-forgot">
							<div class="t-c_input">
								<input type="text" placeholder="+7 913 44 30 37" class="input" id="phone4">
								<span class="underline"></span>
							</div>
                                                        <div class="t-c_input t-c_code">
								<input type="password" placeholder="&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;" class="input i-2" id="smscode4">
								<span class="underline"></span>
							</div>
							<a href="#" class="m-p_registration js-reg-2" onclick="return false;">Напомнить пароль</a>
							<div class="password-sent"></div>
						</div>

					</div>
					
				</div>
			</div>
                        
			<!-- END modal  -->