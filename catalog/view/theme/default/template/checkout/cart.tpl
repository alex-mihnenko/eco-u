<?php echo $header; ?>
        <script>
            window.bodyClass = 'page_2';
        </script>
			<!-- Container -->
			<section class="fond-white">
				<div class=""> 
					<ul class="liTabs_2 t_wrap t_wrap_2">
						<li class="t_item t_item_2">
					    	<a class="t_link t_link_2 <?php if(!$success_order_id) { ?>cur<?php } ?>" href="#">
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
                                                                                                        <a href="<?php echo $product['href']; ?>" class="table-b_thumb" target="_blank">
                                                                                                                <img src="<?php echo $product['thumb']; ?>" alt="">
					    								</a>
					    							</td>
					    							<td class="table-b_width1">
					    								<a href="<?php echo $product['href']; ?>" class="table-b_link" target="_blank">
                                                                                                            <?php echo $product['name']; ?>
                                                                                                            <?php $weight_variants = explode(',', $product['weight_variants']); ?>
                                                                                                            <?php
                                                                                                                if($weight_variants[0] != '') {
                                                                                                                    echo '(<span id="variant_'.$product['cart_id'].'">'.$weight_variants[$product['weight_variant']].'</span> '.$product['weight_class'].')'; 
                                                                                                                } else {
                                                                                                                    echo '<span style="display:none" id="variant_'.$product['cart_id'].'">1</span>'; 
                                                                                                                }
                                                                                                            ?>
                                                                                                        </a>
					    								<!--<div class="table-b_text">1Л, Россия</div>-->
					    							</td>
					    							<td class="table-b_width2">
                                                                                                        <?php if(empty($product['weight_variants'])) { ?>
					    								<div>
                                                                                                                <div class="table-b_minus button_decrease" data-variant="variant_<?php echo $product['cart_id'];?>" data-target="quantity_<?php echo $product['cart_id'];?>">-</div>
                                                                                                                <input data-variant="variant_<?php echo $product['cart_id'];?>" data-cart-id="<?php echo $product['cart_id']; ?>" data-product-id="<?php echo $product['product_id']; ?>" id="quantity_<?php echo $product['cart_id'];?>" type="text" value="<?php echo $product['quantity']; ?>" class="table-b_input">
					    									<div class="table-b_quantity"></div>
					    									<div class="table-b_minus button_increase" data-variant="variant_<?php echo $product['cart_id'];?>" data-target="quantity_<?php echo $product['cart_id'];?>">+</div>
					    								</div>
                                                                                                        <?php } else { ?>
                                                                                                        <div>
                                                                                                                <div class="table-b_minus button_decrease" data-variant="variant_<?php echo $product['cart_id'];?>" data-target="quantity_<?php echo $product['cart_id'];?>">-</div>
                                                                                                                <input data-variant="variant_<?php echo $product['cart_id'];?>" data-cart-id="<?php echo $product['cart_id']; ?>" data-product-id="<?php echo $product['product_id']; ?>" id="quantity_<?php echo $product['cart_id'];?>" type="text" value="<?php echo (int)((float)$product['quantity']/(float)$weight_variants[$product['weight_variant']]); ?>" class="table-b_input">
					    									<div class="table-b_quantity"></div>
					    									<div class="table-b_minus button_increase" data-variant="variant_<?php echo $product['cart_id'];?>" data-target="quantity_<?php echo $product['cart_id'];?>">+</div>
					    								</div>
                                                                                                        <?php } ?>
					    							</td>
					    							<td>
					    								<div class="table-b_price"><?php echo floor((int)$product['price'] * (float)$product['quantity']); ?> руб.</div>
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
					    					<div class="o-i_price"><?php echo $order_price; ?> руб</div>
					    					<div class="o-i_txt2">(без учета стоимости доставки)</div>
                                                                                <div class="b-discount">
                                                                                        <input type="hidden" id="order_discount" value="0">

                                                                                        <?php if(isset($customer_discount)) { ?>
																							<div class="personal-discount" style="position:relative;color:#666;font-size:18px;font-weight:700;height:50px;line-height:50px; margin-top: -32px">
                                                                                            Текущая скидка <span class="p-o_discount sticker_discount" style="position:relative;top:0;left:10px;display:inline-block;width:40px;height:40px;line-height:40px;font-size:16px;"><?php echo (-1*(int)$customer_discount); ?>%</span>
                                                                                            <input type="hidden" id="customer_discount" data-type="P" value="<?php echo (int)$customer_discount; ?>">
																							</div>
                                                                                        <?php } ?>
                                                                                    
                                                                                        <div class="personal-coupon" style="height:50px;  margin-top: -32px">
                                                                                        <?php if(isset($customer_coupon)) { ?>
                                                                                            <?php if($customer_coupon['type'] == 'P') { 
                                                                                                $cDcnt = (int)$totals[0]['text']*((int)$customer_coupon['discount']/100);
                                                                                            ?>
                                                                                                Текущая скидка <span class="p-o_discount sticker_discount b-d_coupon_circle"><?php echo (-1*(int)$customer_coupon['discount']); ?>%</span>
                                                                                            <?php } elseif($customer_coupon['type'] == 'F') { 
                                                                                                $cDcnt = (int)$customer_coupon['discount'];
                                                                                                ?>
                                                                                                Ваша скидка <span class="c-d_amount"><?php echo (int)$customer_coupon['discount']; ?></span> руб
                                                                                            <?php } ?>
                                                                                            <input type="hidden" id="customer_coupon" data-type="<?php echo $customer_coupon['type']; ?>" value="<?php echo (int)$customer_coupon['discount']; ?>">
                                                                                        <?php } ?>
                                                                                        </div>
                                                                                            <?php if(!isset($customer_coupon) && !isset($customer_discount)) { ?>
																							<div class="b-d_coupon">
																								Есть купон на скидку?
																							</div>
                                                                                            <?php } else {?>
                                                                                            <div class="b-d_coupon_discount">
                                                                                            Увеличить скидку
                                                                                            </div>
																							<?
                                                                                            } ?>
					    						<div class="b-d_hidden">
					    							<div class="b-d_coupon2">Применить скидку</div>
					    							<input type="text" value="" class="b-dis_input">
													<span class="b-dis_submit" style="cursor:pointer">OK</span>
					    						</div>
					    					</div>
					    					<div class="o-i_txt3">Контактные данные</div>
                                                                                <input type="text" placeholder="Ваше имя *" class="o-i_input field_firstname" value="<?php echo $customer['first_name']; ?>">
                                                                                <input type="tel" placeholder="Номер телефона *" class="o-i_input field_phone" id="phone2" value="<?php echo $customer['phone']; ?>">
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
													<p>ООО «ЭКО-Ю» (далее – ЭКО-Ю) предпринимает все разумные меры по защите полученных персональных данных от уничтожения, искажения или разглашения. Настоящим субъект персональных указывая свои данные на сайте http://www.eco-u.ru/ дает согласие ЭКО-Ю, расположенному по адресу 125080, Москва, Волоколамское шоссе, 6-76, на обработку своих персональных данных, включая сбор, систематизацию, накопление, хранение, уточнение (обновление, изменение), использование, распространение (в том числе передачу, включая трансграничную передачу данных), обезличивание, блокирование, уничтожение персональных данных, в том числе с использованием средств автоматизации в целях:</p>
													<p>заключения и исполнения договора по инициативе субъекта персональных данных или договора, по которому субъект персональных данных будет являться выгодоприобретателем или поручителем;</p>
													<p>анализа покупательского поведения и улучшения качества предоставляемых ЭКО-Ю товаров и услуг,</p>
													<p>а также предоставления субъекту персональных данных информации коммерческого и информационного характера (в том числе о специальных предложениях и акциях ЭКО-Ю) через различные каналы связи, в том числе по почте, смс, электронной почте, телефону, если субъект персональных данных изъявит желание на получение подобной информации соответствующими средствами связи.</p>
													<p>Помимо ЭКО-Ю, доступ к своим персональным данным имеют сами субъекты; лица, в том числе партнеры ЭКО-Ю, осуществляющие продажу товаров ЭКО-Ю, поддержку служб и сервисов ЭКО-Ю, в необходимом для осуществления такой поддержки объеме; иные лица, права и обязанности которых по доступу к соответствующей информации установлены законодательством РФ.</p>
													<p>ЭКО-Ю гарантирует соблюдение следующих прав субъекта персональных данных: право на получение сведений о том, какие персональные данные субъекта персональных данных хранятся у ЭКО-Ю; право на удаление, уточнение или исправление хранящихся у ЭКО-Ю персональных данных; иные права, установленные действующим законодательством РФ.</p>
													<p>ЭКО-Ю обязуется немедленно прекратить обработку персональных данных после получения соответствующего требования субъекта персональных данных, оформленного в письменной форме.</p>
													<p>Согласие субъекта персональных данных на обработку персональных данных действует бессрочно и может быть в любой момент отозвано субъектом персональных данных путем письменного обращения в адрес ЭКО-Ю по адресу: 117292, Москва, ул. Кедрова, дом 6, к 2,</p>
													<p>Настоящим субъект персональных данных обязуется (1) не представляться чужим именем или от чужого имени (частного лица или организации), (2) не указывать заведомо недостоверную информацию и информацию, идентифицирующую третьих лиц или в отношении третьих лиц.</p>
													<p>Настоящим субъект персональных данных, предоставляя их ЭКО-Ю, подтверждает достоверность указанной информации и соглашается на их обработку на указанных условиях.</p>
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
                                                                            <?php if(isset($delivery_address) && count($delivery_address) > 0) { ?>
									    <select id="delivery_address" name="tech" class="tech">
                                                                                    <?php foreach($delivery_address as $address) { ?>
                                                                                    <option value="<?php echo $address['address_id']; ?>"><?php echo $address['value']; ?></option>
                                                                                    <?php } ?>
                                                                                    <option class="new_address" value="0">Новый адрес доставки</option>
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
                                                                                                <?php foreach($delivery_intervals as $interval) { ?>
                                                                                                <option value="<?php echo $interval; ?>"><?php echo $interval; ?></option>
                                                                                                <?php } ?>
											</select> 
										</div>
									</div>
                                                                        <div class="block-delivery-price">
                                                                            <div class="cost-delivery">Стоимость доставки</div>
                                                                            <div class="c-d_price">330 руб</div>
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
					    	<a class="t_link t_link_2 <?php if($success_order_id) { ?>cur<?php } ?>" href="#">
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
						<?php foreach ($spec_products as $product) { 
                                                    if($product['stock_status_id'] == 5 && $product['quantity'] <= 0) continue;
                                                ?> 
                                                    <div>
                                                            <div class="box-p_o">
                                                                <meta content="<?php echo $product['thumb']; ?>" itemprop="image">
                                                                <a href="<?php echo $product['href']; ?>" class="p-o_thumb" target="_blank">
                                                                        <img src="<?php if(!empty($product['thumb'])) echo $product['thumb']; else echo '/image/eco_logo.jpg'; ?>" alt="">
                                                                 </a>
                                                                 <div class="p-o_block">
                                                                         <?php if(isset($product['composite_price'])) { ?><input type="hidden" class="composite_price" value='<?php echo $product['composite_price']?>'><? } ?>
                                                                         <?php if(isset($product['discount_sticker'])) { ?><div class="p-o_discount sticker_discount">-<?php echo $product['discount_sticker']; ?>%</div>
																	 <?php } elseif($product['sticker_class']) { ?><div class="p-o_discount sticker_<?php echo $product['sticker_class']; ?>"><span><?php echo $product['sticker_name']; ?></span></div><?php } ?>
                                                                         <div class="p-o_link">
                                                                                 <meta itemprop="name" content="<?php echo $product['name']; ?>">
                                                                                 <a href="<?php echo $product['href']; ?>" itemprop="url" target="_blank"><?php echo $product['name']; ?></a> 
                                                                                 <?php if($is_admin) {?><a target="_blank" href="<?php echo $product['edit_link']; ?>" class="btn btn-default admin-product-edit"><i class="fa fa-edit"></i></a><?php } ?>
                                                                         </div>
                                                                         <div class="p-o_short-descr"><?php echo $product['description_short']; ?></div>
                                                                         <div class="clearfix" itemscope itemtype="http://schema.org/Offer" itemprop="offers">
                                                                                 <?php if($product['quantity'] > 0 || $product['stock_status_id'] == 7) { ?>
                                                                                 <div class="p-o_select">
                                                                                     <?php if(empty($product['weight_variants'])) { ?>
                                                                                         <select name="tech" class="tech">
                                                                                                 <?php for($i=1; $i<=5; $i++) { ?>
                                                                                                     <option value="<?php echo $i; ?>"><?php echo $i; ?> <?php echo $product['weight_class']; ?></option>
                                                                                                 <? } ?>
                                                                                         </select> 
                                                                                     <?php } else { ?>
                                                                                         <select name="tech" class="tech">
                                                                                                 <?php 
                                                                                                 $arVariants = explode(',', $product['weight_variants']);
                                                                                                 foreach($arVariants as $i => $variant) { ?>
                                                                                                     <option value="<?php echo $i; ?>"><?php echo trim($variant); ?> <?php echo $product['weight_class']; ?></option>
                                                                                                 <? } ?>
                                                                                         </select> 
                                                                                     <?php } ?>
                                                                                 </div>
                                                                                 <div class="p-o_right">
                                                                                         <meta itemprop="price" content="<?php echo intval($product['price']); ?>" />
                                                                                         <meta itemprop="priceCurrency" content="RUB" />
                                                                                         <?php if(empty($product['weight_variants'])) { ?>
                                                                                             <div class="p-o_price"><?php if($product['price'] > 999) echo (int)$product['price'].' р'; else echo (int)$product['price']; ?> руб</div>
                                                                                         <?php } else { ?>
                                                                                             <div class="p-o_price"><?php $tp = (int)((float)trim($arVariants[0])*(float)$product['price']); echo $tp; ?> <?php if($tp > 999) echo ' р'; else echo ' руб'; ?></div>
                                                                                         <?php } ?>
                                                                                         <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                                                                         <input type="submit" value="" class="p-o_submit">
                                                                                 </div>
                                                                                 <?php } elseif($product['quantity'] <= 0 && $product['stock_status_id'] == 6) { ?>
                                                                                 <div class="p-o_select">
                                                                                    <?php if(empty($product['weight_variants'])) { ?>
                                                                                        <select name="tech" class="tech">
                                                                                                <?php for($i=1; $i<=5; $i++) { ?>
                                                                                                    <option value="<?php echo $i; ?>"><?php echo $i; ?> <?php echo $product['weight_class']; ?></option>
                                                                                                <? } ?>
                                                                                        </select> 
                                                                                    <?php } else { ?>
                                                                                        <select name="tech" class="tech">
                                                                                                <?php 
                                                                                                $arVariants = explode(',', $product['weight_variants']);
                                                                                                foreach($arVariants as $i => $variant) { ?>
                                                                                                    <option value="<?php echo $i; ?>"><?php echo trim($variant); ?> <?php echo $product['weight_class']; ?></option>
                                                                                                <? } ?>
                                                                                        </select> 
                                                                                    <?php } ?>
                                                                                </div>
                                                                                <div class="p-o_right">
                                                                                        <meta itemprop="price" content="<?php echo intval($product['price']); ?>" />
                                                                                        <meta itemprop="priceCurrency" content="RUB" />
                                                                                        <?php if(empty($product['weight_variants'])) { ?>
                                                                                            <div class="p-o_price"><?php if($product['price'] > 999) echo (int)$product['price'].' р'; else echo (int)$product['price']; ?> руб</div>
                                                                                        <?php } else { ?>
                                                                                            <div class="p-o_price"><?php $tp = (int)((float)trim($arVariants[0])*(float)$product['price']); echo $tp; ?> <?php if($tp > 999) echo ' р'; else echo ' руб'; ?></div>
                                                                                        <?php } ?>
                                                                                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                                                                        <div class="p-o_submit n-a_time" rel="tooltip" title="<?php echo $product['available_in_time']; ?>"></div>
                                                                                </div>
                                                                                 <?php } ?>
                                                                         </div>
                                                                 </div>
                                                         </div>
                                                    </div>
                                                <?php } ?>
					</div>
				</div>
			</section>
			<!-- Favorite Products -->
<?php if($success_order_id) { ?>
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        $('.field_order_id').html(<?php echo $success_order_id; ?>);
    });
</script>
<?php } ?>
<?php echo $footer; ?>
