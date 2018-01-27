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
							<div class="l-a_title">Выбирайте из более 700 редких и полезных продуктов</div>
						</li>
						<li>
							<div class="l-a_thumb">
								<div class="l-a_icon l-a_2"></div>
							</div>
							<div class="l-a_title"> Ешьте свежие и качественные продукты</div>
						</li>
						<li>
							<div class="l-a_thumb">
								<div class="l-a_icon l-a_3"></div>
							</div>
							<div class="l-a_title">Используйте только натуральную био-упаковку</div>
						</li>
						<li>
							<div class="l-a_thumb">
								<div class="l-a_icon l-a_4"></div>
							</div>
							<div class="l-a_title">Оформите доставку по Москве за 290 рублей или бесплатно от 3900 рублей</div>
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
                                        <!--<li>
                                                <a href="#l-p_1">
                                                        <div class="l-p_icon l-p_i1"></div>
                                                        <span>Все</span>
                                                </a>
                                        </li>-->
                                        <?php
                                        $cell_num=0;
                                        foreach($categories as $i => $category) {
                                            if(empty($category['sub'])) continue;
                                            if(empty($products_catsorted[$category['id']]['sub'])) continue;
                                        ?>
                                        <li>
                                                <a href="#l-p_<?php echo $category['id']; ?>">
                                                        <div class="category-icon" style="background-image:url('/image/<?php echo $category["image"]; ?>');">
                                                            <?php if(!empty($category['image'])) { ?><object data="/image/<?php echo $category['image']; ?>" type="image/svg+xml" class="category-icon-active"></object><?php } ?>
                                                        </div>
                                                        <span><?php echo $category['name']; ?></span>
                                                </a>
                                        </li>
                                        <?
                                        $cell_num++;
                                        echo $cell_num."!!!";
                                        }
                                        $addon_cell=$cell_num%4;
                                        echo $addon_cell;
                                        while($addon_cell<4){
                                            echo "<li></li>";
                                            $addon_cell++;
                                        }

                                        ?>
				</ul>
			</div>
			<!--         -->
			<section class="fond-catalog">
				<div class="f-c_top">
					<div class="width-1418 clearfix">
						<ul class="tabs__catalog">
							<li class="modal8 active"><span>от А до Я</span></li>
							<li class="modal9"><span>Каталог продуктов</span></li>
							<li class="modal-hide"><span>Без картинок</span></li>
                                                        <li style="display:none;"><span>Без картинок</span></li>
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
                                                                    if(empty($category['sub'])) continue;
                                                                    if(empty($products_catsorted[$category['id']]['sub'])) continue;
                                                                ?>
								<li>
                                                                        <a href="#l-p_<?php echo $category['id']; ?>">
										<div class="category-icon" style="background-image:url('/image/<?php echo $category["image"]; ?>');">
                                                                                     <?php if(!empty($category['image'])) { ?><object data="/image/<?php echo $category['image']; ?>" type="image/svg+xml" class="category-icon-active"></object><?php } ?>
                                                                                </div>
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
					<div class="button-alphabetic button-alphabetic-shadow" data-remodal-target="modal8">А-Я</div>
					<div class="clearfix rel"> 
						
						<div id="contentcontainer">
							<div class="container">
                                                            
                                                            <?php foreach ($alphabet_list as $lCode => $letter) { 
                                                                $lCount = count($products_asorted[$letter]);
                                                            ?>
                                                                <div id="letter_<?php echo $lCode; ?>" class="rel">
									<div class="big-letter"><?php echo $letter; ?></div>
									<ul class="list-letter">
										<?php 
                                                                                $iCount = 0;
                                                                                foreach($products_asorted[$letter] as $key => $product) {
                                                                                    if(($product['quantity'] <= 0 && $product['stock_status_id'] == 5) || $product['status'] != 1) {
                                                                                        $lCount--;    
                                                                                        continue;
                                                                                    }
                                                                                    if($iCount > 4) break;
                                                                                    $iCount++;
                                                                                ?>
                                                                                <li data-product="<?php echo $product['product_id']; ?>">
                                                                                    <div id="asorted_prod_<?php echo $product['product_id']; ?>" itemscope itemtype="http://schema.org/Product" itemprop="itemListElement">
                                                                                            <meta itemprop="position" content="<?php echo $key; ?>" />
                                                                                            <div class="box-p_o">
                                                                                                   <meta content="<?php echo $product['thumb']; ?>" itemprop="image">
                                                                                                   <a href="<?php echo $product['href']; ?>" class="p-o_thumb" target="_blank">
                                                                                                            <img src="<?php if(!empty($product['thumb'])) echo $product['thumb']; else echo '/image/eco_logo.jpg'; ?>" alt="">
                                                                                                    </a>
                                                                                                    <div class="p-o_block">
                                                                                                            <?php if(isset($product['composite_price'])) { ?><input type="hidden" class="composite_price" value='<?php echo $product['composite_price']?>'><? } ?>
                                                                                                            <?php if(isset($product['discount_sticker'])) { ?><div class="p-o_discount sticker_discount">-<?php echo $product['discount_sticker']; ?>%</div>
                                                                                                        <?php } elseif(isset($product['sticker_class'])) { ?><div class="p-o_discount sticker_<?php echo $product['sticker_class']; ?>"><span><?php echo $product['sticker_name']; ?></span></div><?php } ?>
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
                                                                                                                                <div class="p-o_price"><?php if($product['price'] > 999) echo (int)$product['price'].' р'; else echo $product['price']; ?></div>
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
                                                                                                                                <div class="p-o_price"><?php if($product['price'] > 999) echo (int)$product['price'].' р'; else echo $product['price']; ?></div>
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
                                                                                </li>
                                                                                <? } ?>
									</ul>
                                                                        <div class="show-more" data-mode="asort" data-target="<?php echo $letter; ?>"  style="<?php if($lCount <= 5) { ?>visibility:hidden;<? } ?>">еще <?php echo ($alphabetCount[$letter]-5); ?> продуктов</div>
								</div>
                                                            <?php } ?>
                                                        </div>
						</div>
					</div>
				</div>
				<div class="tabs__block">
					<div class="button-tabs2 button-alphabetic-shadow" data-remodal-target="modal9"></div>
					<div class="clearfix rel"> 
						<div id="contentcontainer2">
							<div class="container">
                                                            <?php
                                                            foreach($categories as $category) { 
                                                                ?><div id="l-p_<?php echo $category['id'] ?>"><?php
                                                                foreach($category['sub'] as $sub_index => $subcategory) {
                                                                    if(!isset($products_catsorted[$category['id']]['sub'][$subcategory['id']])) continue;
                                                                    $lCount = (int)$subcategory['total'];
                                                                    ?>
                                                                    <div id="l-p_<?php echo $category['id'] . ('_' . $sub_index) ?>" class="rel">
                                                                        <?php if(!empty($subcategory['image'])) { ?><div class="big-thumb"><img src="/image/<?php echo $subcategory['image']; ?>" alt=""></div><?php } ?>
									<div class="l-p_title"><?php echo $subcategory['name']; ?></div>
									<ul class="list-letter">
                                                                                <?php 
                                                                                $iCount = 0;
                                                                                foreach($products_catsorted[$category['id']]['sub'][$subcategory['id']] as $key => $product) {
                                                                                    if(($product['quantity'] <= 0 && $product['stock_status_id'] == 5) || $product['status'] != 1) {
                                                                                        $lCount--;
                                                                                        continue;
                                                                                    }
                                                                                    if($iCount > 4) break;
                                                                                    $iCount++;
                                                                                ?>
                                                                                <li data-product="<?php echo $product['product_id']; ?>">
                                                                                    <div id="catsorted_prod_<?php echo $product['product_id']; ?>" itemscope itemtype="http://schema.org/Product" itemprop="itemListElement">
                                                                                            <meta itemprop="position" content="<?php echo $key; ?>" />
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
                                                                                                                                <div class="p-o_price"><?php if($product['price'] > 999) echo (int)$product['price'].' р'; else echo $product['price']; ?></div>
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
                                                                                                                                <div class="p-o_price"><?php if($product['price'] > 999) echo (int)$product['price'].' р'; else echo $product['price']; ?></div>
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
                                                                                </li>
                                                                                <? } ?>
									</ul>
                                                                        <div class="show-more" data-mode="catsort" data-target="<?php echo $subcategory['id']; ?>" style="<?php if($lCount <= 5) { ?>visibility:hidden;<? } ?>">еще <?php echo ($lCount-5); ?> продуктов</div>
								</div>
                                                                <?php } ?></div><?php } ?>
							</div>
						</div>
					</div>
				</div>
				<div class="tabs__block">
					<div class="width-1418">
						<div class="auto-columnizer clearfix">
                                                        <div class="no-pictures">
                                                        <?php 
                                                            $letter = '';
                                                            foreach($products_tagsorted as $tag) { 
                                                                $new_letter = mb_strtoupper(mb_substr($tag, 0, 1));
                                                                if($new_letter != $letter) {
                                                        ?>
                                                            </div><div class="no-pictures">
                                                                <div class="n-p_title"><?php echo $new_letter; ?></div>
                                                                <?php } ?>
                                                                    <div class="n-p_list" data-remodal-target="modal5" data-tag="<?php echo $tag; ?>"><?php echo $tag; ?></div>
                                                            <?php
                                                                $letter = $new_letter;
                                                            } ?>
                                                        </div>
						</div>
						<!-- Modal -->
						<div class="remodal list-modal" data-remodal-id="modal5">
							<button data-remodal-action="close" class="remodal-close"></button>
							<div class="l-m_title"></div>
							<div class="modal-content"></div>
						</div>
						<!-- END modal -->
					</div>
				</div>
                                <div class="tabs__block" id="search-content">
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