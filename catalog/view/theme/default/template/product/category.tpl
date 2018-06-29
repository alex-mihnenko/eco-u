<!-- Header -->
    <?php echo $header; ?>
    
    <div class="big-picture">
        <h1 class="b-p_title">
            <img src="/catalog/view/theme/default/img/logo.png" alt="ЭКО-Ю" title="ЭКО-Ю" class="logo">
            Полезные продукты от А до Я<br>с доставкой на дом
        </h1>
    </div>
<!-- END Header -->

<!-- Our advantages -->
    <?php if($hide_advantage) { ?>
        <div class="fond-advantage" style="display: none">
    <?php } else { ?>
        <div class="fond-advantage" >
    <?php } ?>
        <a class="btn-close-tab" id="b-close_advantage"></a>
    	<div class="width-1418">
    		<ul class="list-advantage">
    			<li>
    				<div class="l-a_thumb">
    					<div class="l-a_icon l-a_1"></div>
    				</div>
    				<div class="l-a_title">Выбирайте из&nbsp;более 700 редких и&nbsp;полезных продуктов</div>
    			</li>
    			<li>
    				<div class="l-a_thumb">
    					<div class="l-a_icon l-a_2"></div>
    				</div>
    				<div class="l-a_title">Покупайте свежие и&nbsp;качественные продукты</div>
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
    				<div class="l-a_title">Доставка по&nbsp;Москве за&nbsp;290 рублей или бесплатно от&nbsp;3&nbsp;500 рублей</div>
    			</li>
    		</ul>
    	</div>
    </div>
<!-- end Our advantages -->

<!-- Content top -->
    <?php echo $content_top; ?>
<!-- END Content top -->


<!-- Catalog -->
    <!-- Remodal -->
        <div class="remodal modal-alphabetic js-modal8" data-remodal-id="modal8">
        	<ul class="list-m_a">
        	</ul>
        </div>
    <!-- END Remodal -->

    <!-- Products -->
    	<div class="fond-catalog products-grid" id="category" data-id="<?php echo $category_id; ?>">
            <!-- /// -->
    		
            <div class="f-c_top">
    			<!-- <div class="width-1418 clearfix">
    				<ul class="tabs__catalog">
    					<li class="modal9 active"><span>Каталог продуктов</span></li>
                        <li class="modal8"><span>от А до Я</span></li>
    					<li class="modal-hide"><span>Без картинок</span></li>
                        <li style="display:none;"><span>Без картинок</span></li>
    				</ul>
    				<form class="b-seach">
    					<input type="text" placeholder="Поиск..." class="b-seach_text">
    					<input type="submit" value="" class="b-seach_submit">
                        <div class="cancel-search">&times;</div>
    				</form>
    			</div> -->

    			<div class="qwe2" style="display: none">
    				<div class="qwe-bg"></div>
    				<div class="qwe vertical dragscroll">
    					<ul class="list-alphabetic">
    					</ul>
    				</div>
    			</div>

    			<div class="all-l_a2"  style="display: block">
    				<div class="qwe-bg"></div>
    				<div class="qwe vertical dragscroll">
    					<ul class="list-products">
                            <?php foreach($categories as $i => $category) { ?>
                                <?php if(empty($category['sub'])) continue; ?>
                                <?php if(empty($products_catsorted[$category['id']]['sub'])) continue; ?>
                                
                                <li>
                                    <a href="#l-p_<?php echo $category['id']; ?>">
                                        <?php if( $category['id'] == 'new' || $category['id'] == 'sale' ) { ?>
                                            <div class="category-icon" style="background-image:url('<?php echo $category["image"]; ?>');">
                                                <?php if(!empty($category['image'])) { ?><object data="<?php echo $category['image']; ?>" type="image/svg+xml" class="category-icon-active"></object><?php } ?>
                                            </div>
                                        <?php } else { ?>
        								    <div class="category-icon" style="background-image:url('/image/<?php echo $category["image"]; ?>');">
                                                <?php if(!empty($category['image'])) { ?><object data="/image/<?php echo $category['image']; ?>" type="image/svg+xml" class="category-icon-active"></object><?php } ?>
                                            </div>
                                        <?php } ?>


    								    <span><?php echo $category['name']; ?></span>
    							     </a>
                                </li>
                            <?php } ?>
                            <li class="magic-line3"></li>
    					</ul>
    				</div>
    			</div>
    		</div>

            <!-- Categories products -->
    		<div class="tabs__block active" id="container-products-categories">
    			
                <div class="clearfix rel">

    				<div id="contentcontainer2">
    					<div class="container">

                            <?php foreach($categories as $category) {  ?>
                                <div id="l-p_<?php echo $category['id'] ?>">
                                    <?php foreach($category['sub'] as $sub_index => $subcategory) { ?>
                                        
                                        <?php if(!isset($products_catsorted[$category['id']]['sub'][$subcategory['id']])) continue; ?>
                                        <?php $lCount = (int)$subcategory['total']; ?>

                                        <div id="l-p_<?php echo $category['id'] . ('_' . $sub_index) ?>" class="rel">
                                            <?php if(!empty($subcategory['image'])) { ?>
                                                <?php if( $category['id'] == 'new' || $category['id'] == 'sale' ) { ?>
                                                    <div class="big-thumb"><img src="<?php echo $subcategory['image']; ?>" alt="" style="width: 150px; height: 150px; opacity: 0.15;"></div>
                                                <?php } else { ?>
                                                    <div class="big-thumb"><img src="/image/<?php echo str_replace('.png','.svg',$subcategory['image']); ?>" alt="" style="width: 150px; height: 150px; opacity: 0.15;"></div>
                                                <?php } ?>
                                            <?php } ?>

                                            <div class="l-p_title"><?php echo $subcategory['name']; ?></div>                                            
                                            
                                            <ul class="list-letter ll-open">
                                                <?php $iCount = 0; ?>
                                                
                                                <?php foreach($products_catsorted[$category['id']]['sub'][$subcategory['id']] as $key => $product) { ?>
                                                    <?php if(($product['quantity'] <= 0 && $product['stock_status_id'] == 5) || $product['status'] != 1) { ?>
                                                        <?php $lCount--; ?>
                                                        <?php continue; ?>
                                                    <?php } ?>

                                                    <?php if($iCount > 4) break; ?>
                                                    <?php $iCount++; ?>

                                                    <li data-product="<?php echo $product['product_id']; ?>">
                                                        <div id="catsorted_prod_<?php echo $product['product_id']; ?>" itemscope itemtype="http://schema.org/Product" itemprop="itemListElement">
                                                            <meta itemprop="position" content="<?php echo $key; ?>" />

                                                            <div class="box-p_o">
                                                               <meta content="<?php echo $product['thumb']; ?>" itemprop="image">
                                                                
                                                                <a href="<?php echo $product['href']; ?>" class="p-o_thumb">
                                                                   <img <?php if(!empty($product['thumb'])) echo 'src="/new_design/img/spinner.gif" data-src="'.$product['thumb'].'" class="b-lazy"'; else echo 'src="/image/eco_logo.jpg"'; ?> alt="<?php echo $product['name']; ?>">
                                                                </a>

                                                                <div class="p-o_block">
                                                                    <?php if(isset($product['composite_price'])) { ?>
                                                                        <input type="hidden" class="composite_price" value='<?php echo $product['composite_price']?>'>
                                                                    <?php } ?>

                                                                    <?php if(isset($product['discount']) && $product['discount'] > 0) { ?>
                                                                        <div class="p-o_discount sticker_discount">
                                                                            <span><?php echo $product['discount']; ?>%</span>
                                                                        </div>
                                                                    <?php } elseif($product['sticker_class']) { ?>
                                                                        <div class="p-o_discount sticker_<?php echo $product['sticker_class']; ?>"><span><?php echo $product['sticker_name']; ?></span></div>
                                                                    <?php } ?>

                                                                    <div class="p-o_link">
                                                                        <meta itemprop="name" content="<?php echo $product['name']; ?>">
                                                                        <a href="<?php echo $product['href']; ?>" itemprop="url">
                                                                            <?php echo $product['name']; ?>
                                                                        </a>
                                                                    </div>


                                                                    <?php if(isset($product['discount']) && $product['discount'] > 0) { ?>
                                                                        <div class="product-sale-container">
                                                                            <div class="product-sale"><span>Без скидки: </span><span class="price"><?php echo $product['price']; ?></span></div>
                                                                        </div>
                                                                    <?php } else { ?>
                                                                        <div class="p-o_short-descr" itemprop="description"><?php echo $product['description_short']; ?></div>
                                                                    <?php } ?>
                                                                    

                                                                    <div class="clearfix" itemscope itemtype="http://schema.org/Offer" itemprop="offers">
                                                                        <?php if($product['quantity'] > 0 || $product['stock_status_id'] == 7) { ?>
                                                                            <div class="p-o_select">
                                                                                <?php if(empty($product['weight_variants'])) { ?>
                                                                                    <select name="tech" class="tech">
                                                                                        <?php for($i=1; $i<=5; $i++) { ?>
                                                                                            <option value="<?php echo $i; ?>"><?php echo $i; ?> <?php echo $product['weight_class']; ?></option>
                                                                                        <?php } ?>
                                                                                    </select> 
                                                                                <?php } else { ?>
                                                                                    <select name="tech" class="tech">
                                                                                        <?php  $arVariants = explode(',', $product['weight_variants']); ?>
                                                                                        <?php foreach($arVariants as $i => $variant) { ?>
                                                                                            <option value="<?php echo $i; ?>"><?php echo trim($variant); ?> <?php echo $product['weight_class']; ?></option>
                                                                                        <?php } ?>
                                                                                    </select> 
                                                                                <?php } ?>
                                                                            </div>
                                                                            <div class="p-o_right <?php if(isset($product['discount']) && $product['discount'] > 0) { echo 'sale'; } ?>">
                                                                                <meta itemprop="baseprice" content="<?php echo intval($product['price']); ?>" />
                                                                                <meta itemprop="price" content="<?php if($product['special']) { echo intval($product['special']); } else { echo intval($product['price']); } ?>" />
                                                                                <meta itemprop="priceCurrency" content="RUB" />
                                                                                <?php if(empty($product['weight_variants'])) { ?>
                                                                                    <div class="p-o_price"></div>
                                                                                <?php } else { ?>
                                                                                    <div class="p-o_price"></div>
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
                                                                                            <?php } ?>
                                                                                    </select> 
                                                                                <?php } else { ?>
                                                                                    <select name="tech" class="tech">
                                                                                            <?php 
                                                                                            $arVariants = explode(',', $product['weight_variants']);
                                                                                            foreach($arVariants as $i => $variant) { ?>
                                                                                                <option value="<?php echo $i; ?>"><?php echo trim($variant); ?> <?php echo $product['weight_class']; ?></option>
                                                                                            <?php } ?>
                                                                                    </select> 
                                                                                <?php } ?>
                                                                            </div>

                                                                            <div class="p-o_right <?php if(isset($product['discount']) && $product['discount'] > 0) { echo 'sale'; } ?>">
                                                                                <meta itemprop="baseprice" content="<?php echo intval($product['price']); ?>" />
                                                                                <meta itemprop="price" content="<?php if($product['special']) { echo intval($product['special']); } else { echo intval($product['price']); } ?>" />
                                                                                <meta itemprop="priceCurrency" content="RUB" />
                                                                                <?php if(empty($product['weight_variants'])) { ?>
                                                                                    <div class="p-o_price"></div>
                                                                                <?php } else { ?>
                                                                                    <div class="p-o_price"></div>
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
                                                <?php } ?>
                                            </ul>

                                            <div class="show-more sm-lg" data-mode="catsort" data-target="<?php echo $subcategory['id']; ?>" data-parent="<?php echo $subcategory['parent']; ?>"  style="<?php if($lCount <= 5) { ?>visibility:hidden;<?php } ?>">еще <?php echo ($lCount-5); ?> продуктов</div>
                                            <div class="show-more sm-md" data-mode="catsort" data-target="<?php echo $subcategory['id']; ?>" data-parent="<?php echo $subcategory['parent']; ?>"  style="<?php if($lCount <= 4) { ?>visibility:hidden;<?php } ?>">еще <?php echo ($lCount-4); ?> продуктов</div>
                                            <div class="show-more sm-sm" data-mode="catsort" data-target="<?php echo $subcategory['id']; ?>" data-parent="<?php echo $subcategory['parent']; ?>"  style="<?php if($lCount <= 3) { ?>visibility:hidden;<?php } ?>">еще <?php echo ($lCount-3); ?> продуктов</div>
                                            <div class="show-more sm-xs" data-mode="catsort" data-target="<?php echo $subcategory['id']; ?>" data-parent="<?php echo $subcategory['parent']; ?>"  style="<?php if($lCount <= 2) { ?>visibility:hidden;<?php } ?>">еще <?php echo ($lCount-2); ?> продуктов</div>
                                        </div>

                                    <?php } ?>
                                </div>
                            <?php } ?>

    					</div> <!-- container -->
    				</div> <!-- contentcontainer2 -->

    			</div>
    		</div>
            <!-- END Categories products -->

            <!-- Alphabetic products -->
            <div class="tabs__block" id="container-products-alphabetic">
                <div class="button-alphabetic button-alphabetic-shadow" data-remodal-target="modal8">А-Я</div>
                <div class="clearfix rel"> 
                    
                    <div id="contentcontainer">
                        <div class="container">
                        </div> <!-- container -->
                    </div> <!-- contentcontainer -->

                </div>
            </div>
            <!-- END Alphabetic products -->
            
            <!-- List of products -->
            <div class="tabs__block" id="container-products-list">
                <div class="width-1418">
                    <div class="auto-columnizer clearfix">
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
            <!-- END List of products -->


            <!-- Searched products -->
            <div class="tabs__block" id="search-content">
                <div class="clearfix rel"> 
                   <div id="contentcontainer2">
                        <div class="container"></div>
                    </div>
                </div>
            </div>
            <!-- END Searched products -->

            <!-- /// -->
    	</div>
    <!-- END Products -->
<!-- END catalog -->

<!-- Footer -->
<?php echo $footer; ?>
<!-- END Footer -->


<!-- Mobile Menu -->
    <div data-marker="subcategories-hidden" style="display: none;">
        <?php foreach($categories as $i => $category) { ?>

            <?php if(empty($category['sub'])) continue; ?>
            <?php if(empty($products_catsorted[$category['id']]['sub'])) continue; ?>

            <?php if( $category['id'] != 'new' && $category['id'] != 'sale' ) { ?>
                <a class="item"  href="#l-p_<?php echo $category['id']; ?>">
                    <?php if(!empty($category['image'])) { ?><div style="background: url(/image/<?php echo $category['image']; ?>) no-repeat center center scroll; -webkit-background-size: contain; -moz-background-size: contain; -o-background-size: contain; background-size: contain;" class="category-icon"></div><?php } ?>
                    <?php echo $category['name']; ?>
                </a>
            <?php } ?>

        <?php } ?>
    </div>
<!-- Mobile Menu -->