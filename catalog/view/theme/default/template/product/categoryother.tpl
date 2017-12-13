<?php echo $header; ?>

			<div class="big-picture">
				<div class="b-p_title">Полезные продукты от А до Я<br>с доставкой на дом</div>
			</div>
			<!-- Our advantages -->
			<section class="fond-advantage">
				<div class="width-1418">
					<ul class="list-advantage">
						<li>
							<div class="l-a_thumb">
								<div class="l-a_icon l-a_1"></div>
							</div>
							<div class="l-a_title">Часть товаров мы производим на нашем производстве</div>
						</li>
						<li>
							<div class="l-a_thumb">
								<div class="l-a_icon l-a_2"></div>
							</div>
							<div class="l-a_title">Часть товаров мы производим на нашем производстве</div>
						</li>
						<li>
							<div class="l-a_thumb">
								<div class="l-a_icon l-a_3"></div>
							</div>
							<div class="l-a_title">Мы используем только натуральную био-упаковку и заботимся об окружающей среде</div>
						</li>
						<li>
							<div class="l-a_thumb">
								<div class="l-a_icon l-a_4"></div>
							</div>
							<div class="l-a_title">Доставляем заказы по Москве за 250 руб. или бесплатно от 4000 руб.</div>
						</li>
					</ul>
				</div>
			</section>
			<!-- end Our advantages -->
			<!-- Profitable offer -->
                        <?php echo $content_top; ?>
			<!-- END Profitable offer -->
			<!-- catalog -->

			<!--   -->
			<div class="remodal modal-alphabetic js-modal8" data-remodal-id="modal8">
				<ul class="list-m_a">
                                        <?php foreach($alphabet_list as $i => $letter) { ?>
                                            <li><a href="#letter_<?php echo $i; ?>"><?php echo $letter; ?></a></li>
                                        <?php } ?>
				</ul>
			</div>
			<div class="remodal modal-tabs2" data-remodal-id="modal9">
				<ul class="list-tabs2">
                                        <li>
                                                <a href="#l-p_1">
                                                        <div class="l-p_icon l-p_i1"></div>
                                                        <span>Все</span>
                                                </a>
                                        </li>
                                        <?php foreach($categories as $i => $category) { 
                                            if(!isset($products_catsorted[$category['id']])) continue;
                                        ?>
                                        <li>
                                                <a href="#l-p_<?php echo $category['id']; ?>">
                                                        <div class="l-p_icon l-p_i2"></div>
                                                        <span><?php echo $category['name']; ?></span>
                                                </a>
                                        </li>
                                        <? } ?>
				</ul>
			</div>
			<!--         -->
			<section class="fond-catalog">
				<div class="f-c_top">
					<div class="width-1418 clearfix">
						<ul class="tabs__catalog">
							<li class="modal9 active"><span>Каталог продуктов</span></li>
                                                        <li style="display:none;"><span></span></li>
                                                        <li style="display:none;"><span></span></li>
                                                        <li style="display:none;"><span></span></li>
						</ul>
						<form class="b-seach">
							<input type="text" placeholder="поиск..." class="b-seach_text">
							<input type="submit" value="" class="b-seach_submit">
                                                        <div class="cancel-search">&times;</div>
						</form>
					</div>
					<div class="qwe2">
						<div class="qwe-bg"></div>
						<div class="qwe vertical dragscroll">
							<ul class="list-alphabetic">
								<?php foreach($alphabet_list as $i => $letter) { ?>
                                                                    <li><a href="#letter_<?php echo $i; ?>"><span><?php echo $letter; ?></span></a></li>
                                                                <?php } ?>
                                                                <li class="magic-line2"></li>
							</ul>
						</div>
					</div>
					<div class="all-l_a2">
						<div class="qwe-bg"></div>
						<div class="qwe vertical dragscroll">
							<ul class="list-products">
								<!--<li>
									<a href="#l-p_1">
										<div class="l-p_icon l-p_i1"></div>
										<span>Все</span>
									</a>
								</li>-->
                                                                <?php foreach($categories as $i => $category) { 
                                                                    if(!isset($products_catsorted[$category['id']])) continue;
                                                                ?>
								<li>
                                                                        <a href="#l-p_<?php echo $category['id']; ?>">
										<div class="category-icon" style="background-image:url('/image/<?php echo $category["image"]; ?> ');"></div>
										<span><?php echo $category['name']; ?></span>
									</a>
								</li>
                                                                <? } ?>
                                                                <li class="magic-line3"></li>
							</ul>
						</div>
					</div>
					
					
					
				</div>
				
				<div class="tabs__block active">
					<div class="button-tabs2" data-remodal-target="modal9"></div>
					<div class="clearfix rel"> 
						<div id="contentcontainer2">
							<div class="container">
                                                                <?php 
                                                                foreach($categories as $category) { 
                                                                    if(!isset($products_catsorted[$category['id']])) {
                                                                        continue;
                                                                    }
                                                                    $lCount = count($products_catsorted[$category['id']]);
                                                                    ?>
                                                                    <div id="l-p_<?php echo $category['id'] ?>" class="rel">
									<div class="big-thumb"><img src="img/icon_3.png" alt=""></div>
									<div class="l-p_title"><?php echo $category['name']; ?></div>
									<ul class="list-letter">
                                                                                <?php foreach($products_catsorted[$category['id']] as $key => $product) {
                                                                                    if(($product['quantity'] <= 0 && $product['stock_status_id'] == 5) || $product['status'] != 1) {
                                                                                        $lCount--;
                                                                                        continue;
                                                                                    }
                                                                                ?>
                                                                                <li class="<?php if($key >= 5) echo 'hidden'; ?>">
                                                                                    <div id="catsorted_prod_<?php echo $product['product_id']; ?>" itemscope itemtype="http://schema.org/Product" itemprop="itemListElement">
                                                                                            <meta itemprop="position" content="<?php echo $key; ?>" />
                                                                                            <div class="box-p_o">
                                                                                                   <meta content="<?php echo $product['thumb']; ?>" itemprop="image">
                                                                                                   <a href="<?php echo $product['href']; ?>" class="p-o_thumb">
                                                                                                       <img src="<?php if(!empty($product['thumb'])) echo $product['thumb']; else echo '/image/eco_logo.jpg'; ?>" alt="">
                                                                                                    </a>
                                                                                                    <div class="p-o_block">
                                                                                                            <?php if(isset($product['composite_price'])) { ?><input type="hidden" class="composite_price" value='<?php echo $product['composite_price']?>'><? } ?>
                                                                                                            <?php if(isset($product['discount_sticker'])) { ?><div class="p-o_discount sticker_discount">-<?php echo $product['discount_sticker']; ?>%</div>    
                                                                                                            <?php } elseif($product['sticker_class']) { ?><div class="p-o_discount sticker_<?php echo $product['sticker_class']; ?>"><?php echo $product['sticker_name']; ?></div><?php } ?>
                                                                                                            <div class="p-o_link">
                                                                                                                    <meta itemprop="name" content="<?php echo $product['name']; ?>">
                                                                                                                    <a href="<?php echo $product['href']; ?>" itemprop="url"><?php echo $product['name']; ?></a>
                                                                                                                    <?php if($is_admin) {?><a target="_blank" href="<?php echo $product['edit_link']; ?>" class="btn btn-default admin-product-edit"><i class="fa fa-edit"></i></a><?php } ?>
                                                                                                            </div>
                                                                                                            <div class="p-o_short-descr"><?php echo $product['description_short']; ?></div>
                                                                                                            <div class="clearfix" itemscope itemtype="http://schema.org/Offer" itemprop="offers">
                                                                                                                    <?php if($product['quantity'] > 0 || $product['stock_status_id'] == 7) { ?>
                                                                                                                    <div class="p-o_select">
                                                                                                                        <?php if(empty($product['weight_variants'])) { ?>
                                                                                                                            <select name="tech" class="tech">
                                                                                                                                    <?php for($i=1; $i<=5; $i++) { ?>
                                                                                                                                        <option value="<?php echo $i; ?>"><?php echo $i; ?> шт.</option>
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
                                                                                                                                <div class="p-o_price"><?php if($product['price'] > 999) echo (int)$product['price'].' р'; else echo $product['price']; ?></div>
                                                                                                                            <?php } else { ?>
                                                                                                                                <div class="p-o_price"><?php $tp = (int)((float)trim($arVariants[0])*(float)$product['price']); echo $tp; ?> <?php if($tp > 999) echo ' р'; else echo ' руб'; ?></div>
                                                                                                                            <?php } ?>
                                                                                                                            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                                                                                                            <input type="submit" value="" class="p-o_submit">
                                                                                                                    </div>
                                                                                                                    <?php } elseif($product['quantity'] <= 0 && $product['stock_status_id'] == 6) { ?>
                                                                                                                    <div class="not-available clearfix">
                                                                                                                            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                                                                                                            <?php if(empty($product['weight_variants'])) { ?>
                                                                                                                               <input type="hidden" name="quantity" value="1">
                                                                                                                            <?php } else {
                                                                                                                               $arVariants = explode(',', $product['weight_variants']); 
                                                                                                                               $quantity = $arVariants[0]; ?>
                                                                                                                                   <input type="hidden" name="quantity" value="<?php echo $quantity; ?>">
                                                                                                                            <?php } ?>
                                                                                                                            <input type="hidden" name="weight_class" value="<?php echo $product['weight_class']; ?>">
                                                                                                                            <div class="n-a_text">Скоро будет</div>
                                                                                                                            <div class="n-a_time" rel="tooltip" title="<?php echo $product['available_in_time']; ?>"></div>
                                                                                                                    </div>
                                                                                                                    <?php } ?>
                                                                                                            </div>
                                                                                                    </div>
                                                                                            </div>
                                                                                    </div>
                                                                                </li>
                                                                                <? } ?>
									</ul>
									<div class="show-more" style="<?php if($lCount <= 5) { ?>visibility:hidden;<? } ?>">еще <?php echo ($lCount-5); ?> продуктов</div>
								</div>
                                                                <?php } ?>
							</div>
						</div>
					</div>
				</div>
				
                                <div class="tabs__block">
					<div class="clearfix rel"> 
						<div id="contentcontainer3">
                                                    <div class="container">
                                                        
                                                    </div>
                                                </div>
                                        </div>
                                </div>
                                <div class="tabs__block">
					<div class="clearfix rel"> 
						<div id="contentcontainer3">
                                                    <div class="container">
                                                        
                                                    </div>
                                                </div>
                                        </div>
                                </div>
                                <div class="tabs__block">
					<div class="clearfix rel"> 
						<div id="contentcontainer3">
                                                    <div class="container">
                                                        
                                                    </div>
                                                </div>
                                        </div>
                                </div>
			</section>
			<!-- END catalog -->
<? echo $footer; ?>