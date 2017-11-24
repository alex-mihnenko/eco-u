<?php echo $header; ?>
        <script>
            window.bodyClass = 'page_2';
        </script>
			<!-- Container -->
			<section class="fond-white">
				<div class=""> 
					<ul class="liTabs_2 t_wrap t_wrap_2">
						<li class="t_item t_item_2">
					    	<a class="t_link t_link_2 cur" href="#">
					    		<span class="t-l_round"></span>
					    		<span class="t-l_txt">Корзина</span>
					    	</a>
					    	<div class="t_content">
					    		<div class="width-1418 clearfix">
					    			<div class="sidebar_left">
					    				<div class="title-basket">Корзина</div>
					    				<div class="teble-responsive2">
					    					<table class="table-basket">
                                                                                    <?php if(isset($products)) foreach($products as $product) { ?>
                                                                                        <tr>
					    							<td>
                                                                                                        <div class="table-b_close" data-target="<?php echo $product['cart_id']; ?>"></div>						
                                                                                                        <a href="<?php echo $product['href']; ?>" class="table-b_thumb">
                                                                                                                <img src="<?php echo $product['thumb']; ?>" alt="">
					    								</a>
					    							</td>
					    							<td class="table-b_width1">
					    								<a href="<?php echo $product['href']; ?>" class="table-b_link"><?php echo $product['name']; ?></a>
					    								<!--<div class="table-b_text">1Л, Россия</div>-->
					    							</td>
					    							<td class="table-b_width2">
					    								<div>
                                                                                                            <?php if(empty($product['weight_variants'])) { ?>
					    									<div class="table-b_minus button_decrease" data-target="quantity_<?php echo $product['cart_id'];?>">-</div>
                                                                                                                <input data-cart-id="<?php echo $product['cart_id']; ?>" data-product-id="<?php echo $product['product_id']; ?>" id="quantity_<?php echo $product['cart_id'];?>" type="text" value="<?php echo $product['quantity']; ?>" class="table-b_input">
					    									<div class="table-b_quantity"><?php echo $product['weight_class']; ?></div>
					    									<div class="table-b_minus button_increase" data-target="quantity_<?php echo $product['cart_id'];?>">+</div>
                                                                                                            <?php } else { 
                                                                                                                $arVariants = explode(',', $product['weight_variants']);
                                                                                                            ?>
                                                                                                                <select class="tech">
                                                                                                                    <?php foreach($arVariants as $variant) { ?>
                                                                                                                    <option value="<?php echo trim($variant); ?>"><?php echo trim($variant); ?> <?php echo $product['weight_class']; ?></option>
                                                                                                                    <?php } ?>
                                                                                                                </select>
                                                                                                            <?php } ?>
					    								</div>
					    							</td>
					    							<td>
					    								<div class="table-b_price"><?php echo ((int)$product['price'] * (float)$product['quantity']); ?> руб.</div>
					    								<div class="table-b_price2"><?php echo $product['price']; ?> за 1 <?php echo $product['weight_class']; ?></div>
					    							</td>
					    						</tr>
                                                                                    <?php } ?>
					    					</table>
					    				</div>
					    				<div class="clearfix center-mobile">
						    				<!--<ul class="b-pagination">
						    					<li><a href="#"></a></li>
						    					<li class="active"><a href="#">1</a></li>
						    					<li><a href="#">2</a></li>
						    					<li><a href="#"></a></li>
						    				</ul>-->
						    			</div>
					    			</div>
					    			<div class="sidebar_right" data-sticky>
					    				<div id="form1" class="order-information">
					    					<div class="o-i_txt1">Стоимость заказа</div>
					    					<div class="o-i_price"><?php if(isset($totals[0])) echo $totals[0]['text']?></div>
					    					<div class="o-i_txt2">(без учета стоимости доставки)</div>
					    					<div class="b-discount">
					    						<div class="b-d_coupon">Есть купон на скидку?</div>
					    						<div class="b-d_hidden">
					    							<div class="b-d_coupon2">Применить скидку</div>
					    							<input type="text" value="" class="b-dis_input">
													<span class="b-dis_submit" style="cursor:pointer">OK</span>
					    						</div>
					    					</div>
					    					<div class="o-i_txt3">Контактные данные</div>
                                                                                <input type="text" placeholder="Ваше имя *" class="o-i_input field_firstname" value="<?php echo $customer['first_name']; ?>">
                                                                                <input type="text" placeholder="Номер телефона *" class="o-i_input field_phone" id="phone2" value="<?php echo $customer['phone']; ?>">
					    					<div class="o-i_border">
												<span class="o-i_submit"><span class="ajax-loader"></span>Оформить заказ</span>
                                                                                                <div class="total-price-error">Сумма заказа меньше 1000 рублей.</div>
                                                                                                <div class="customer-exists-error">Пользователь с таким номером уже зарегистрирован.</div>
					    						<div class="mobile-center">
						    						<div class="clearfix mobile-inline">
							    						<div class="o-i_chek">
															<input type="checkbox" id="myId1" class="check_agreement" name="myName1" checked>
														    <label for="myId1">
														    	<span class="pseudo-checkbox"></span>
														        
														    </label>
														    <span class="label-text23" data-remodal-target="modal10">Согласие на обработку персональных данных</span>
														</div>
													</div>
												</div>
												<div class="remodal list-modal privacy-policy" data-remodal-id="modal10">
													<button data-remodal-action="close" class="remodal-close"></button>
													<div class="p-p_title-global">Согласие на обработку персональных данных</div>
													<h3>Параграф 1: Сбор информации</h3>
													<p>В описании политики конфиденциальности должно быть обязательно указано, какая информация и каким образом собирается сайтом.</p>
													<h3>Параграф 2: Использование информации</h3>	
													<p>После указания о том, как и какая информация собирается, следует определить, как она будет использоваться владельцами сайта. У Facebook возникла проблема, когда в 2013 году компания решила обновить политику конфиденциальности в 2013 году. Они собирались добавить разрешение на использование личных данных о подписчиках, в том числе о детях до 18 лет, в рекламных целях, написанное достаточно неясным языком.</p>
													<p>Facebook отказались от этой строчки, когда она привлекла внимание Федеральной торговой комиссии. В 20014 на сайте изменилось описание политики конфиденциальности на более простое и понятное.</p>
												</div> 
					    					</div>
					    				</div>
					    			</div>
					    		</div>
					    	</div>
					    </li>
					    <li class="t_item t_item_2">
					    	<a class="t_link t_link_2" href="#">
					    		<span class="t-l_round"></span>
					    		<span class="t-l_txt">Оформление<br>доставки</span>
					    	</a>
					    	<div class="t_content">
					    		<div class="b-delivery b-delivery2">
									<div class="c-m_title">Доставка</div>
									<div class="delivery-address mobile-pd-30">
                                                                            <?php if(!empty($delivery_address)) { ?>
									    <select id="delivery_address" name="tech" class="tech">
                                                                                    <?php foreach($delivery_address as $address_id => $address) { ?>
                                                                                    <option value="<?php echo $address_id; ?>"><?php echo $address; ?></option>
                                                                                    <?php } ?>
                                                                                    <option class="new_address" value="new">Новый адрес доставки</option>
                                                                            </select> 
                                                                            <?php } else { ?>
                                                                            <input type="text" id="delivery_address" value="" placeholder="Новый адрес доставки">
                                                                            <?php } ?>
									</div>
									<div class="order-comment">
										<textarea id="delivery_comment" placeholder="Комментарий к заказу"></textarea>
									</div>
									<div class="clearfix">
										<div class="days-select">
											<select id="delivery_date" name="tech" class="tech">
												<?php foreach($delivery_date as $date) { ?>
                                                                                                <option value="<?php echo $date['format'] ?>"><?php echo $date['text']; ?></option>
                                                                                                <?php } ?>
											</select> 
										</div>
										<div class="days-select time-select">
											<select id="delivery_time" name="tech" class="tech">
												<option value="09:00-14:00">09:00 - 14:00</option>
												<option value="14:00-22:00">14:00 - 22:00</option>
											</select> 
										</div>
									</div>
                                                                        <div class="block-delivery-price">
                                                                            <div class="cost-delivery">Стоимость доставки</div>
                                                                            <div class="c-d_price">250 руб</div>
                                                                        </div>
								</div>
								<div class="shipping-amount">
									<div class="sh-a_txt1">Итого</div>
									<div class="sh-a_price">1 050,00 руб</div>
								</div>
								<div class="payment-title">Оплата</div>
								<div class="m_width-690">
									<div class="can-toggle demo-rebrand-1">
										<input id="d" type="checkbox">
										<label for="d">
											<div class="can-toggle__switch" data-checked="ОПЛАТА ПРИ ПОЛУЧЕНИИ" data-unchecked="ОПЛАТА НА САЙТЕ"></div>
										</label>
									</div>
									<div class="mobile-pd-30">
										<span class="c-m_submit">Рассчитать стоимость доставки</span>
									</div>
								</div>
								
								
					    	</div>
					    </li>
					    <li class="t_item t_item_2">
					    	<a class="t_link t_link_2" href="#">
					    		<span class="t-l_round"></span>
					    		<span class="t-l_txt">Заказ <br>принят</span>
					    	</a>
					    	<div class="t_content">
					    		<div class="padding-mobile">
						    		<div class="b-order_accepted">
									    <div class="o-a_check">
									    	<div class="o-a_text">Заказ принят</div>
									    </div>
									    <div class="o-a_number">Ваш заказ №<span class="field_order_id">0</span> оформлен и мы свяжемся с Вами в ближайшее время.</div>	
									</div>
								</div>
					    	</div>
					    </li>
					</ul>
				</div>
			
			</section>
			<!-- END Container  -->
			<!-- Favorite Products -->
			<section class="fond-f-p">
				<div class="width-1418">
					<div class="f-p_title">Только сейчас</div>
				</div>
				<div class="width-1660">
					<div class="slider-favorite-products">
						
					</div>
				</div>
			</section>
			<!-- Favorite Products -->
<?php echo $footer; ?>