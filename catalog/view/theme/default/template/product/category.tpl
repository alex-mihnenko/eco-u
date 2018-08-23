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
    	<div class="fond-catalog products-grid" id="category" data-id="<?php echo $category_id; ?>" data-page="category">
            <!-- /// -->
    		

    		<div class="all-l_a2"  style="display: block">
    			<div class="qwe-bg"></div>
    			<div class="qwe vertical">
    				<ul class="list-products" data-scroll="0">
                        <?php foreach($categories as $i => $category) { ?>
                            <?php if(empty($category['sub'])) continue; ?>
                            <?php if(empty($products_catsorted[$category['id']]['sub'])) continue; ?>
                            
                            <li>
                                <?php if( $category['id'] == 'new' || $category['id'] == 'sale' ) { ?>
                                <a href="#l-p_<?php echo $category['id']; ?>" data-style="active">
                                <?php } else { ?>
                                <a href="#l-p_<?php echo $category['id']; ?>">
                                <?php } ?>
                                    <?php if( $category['id'] == 'new' || $category['id'] == 'sale' ) { ?>
                                        <?php if(!empty($category['image'])) { ?><i class="svg"><?php loadSvg('path', $category['image']); ?></i><?php } ?>
                                    <?php } else { ?>
                                        <?php if(!empty($category['image'])) { ?><i class="svg"><?php loadSvg('path', '/image/'.$category['image']); ?></i><?php } ?>
                                    <?php } ?>


    							    <span><?php echo $category['name']; ?></span>
    						     </a>
                            </li>
                        <?php } ?>

                        <li class="slide-selector"></li>
    				</ul>
    			</div>
    		</div>
   

            <!-- Categories products -->
    		<div id="container-products-categories">
    			
                <div class="clearfix rel">

    				<div id="contentcontainer2">
    					<div class="container">

                            <?php foreach($categories as $category) {  ?>
                                <div id="l-p_<?php echo $category['id'] ?>" class="subcategory" data-scrollspy="category" data-lazy-load="false">
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
                                                                
                                                                <!-- b-lazy -->
                                                                <a href="<?php echo $product['href']; ?>" class="p-o_thumb">
                                                                    <?php if( !empty($product['thumb']) ) { ?>
                                                                        <?php if( $category['id'] == 'new' || $category['id'] == 'sale' ) { ?>
                                                                            <img src="<?php echo $product['thumb']; ?>" data-src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>">
                                                                        <?php } else { ?>
                                                                            <img src="/catalog/view/theme/default/img/loading.svg" data-src="<?php echo $product['thumb']; ?>" class="image-lazy-load" alt="<?php echo $product['name']; ?>">
                                                                        <?php } ?>
                                                                    <?php } else { ?>
                                                                        <img src="/image/eco_logo.jpg" data-src="/image/eco_logo.jpg" alt="<?php echo $product['name']; ?>">
                                                                    <?php } ?> 
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
                                                                            <div class="product-sale">
                                                                                <span>Без скидки: </span>
                                                                                <span class="price">
                                                                                    <?php
                                                                                        $price = floatval($product['price']); 

                                                                                        if(empty($product['weight_variants'])) {
                                                                                            $quantity=1;
                                                                                        }
                                                                                        else{
                                                                                            $arVariants = explode(',', $product['weight_variants']);
                                                                                            $quantity=$arVariants[0];
                                                                                        }

                                                                                        $composit = 1;

                                                                                        $currency = ' руб';

                                                                                        if(isset($product['composite_price'])) {
                                                                                            $format = (array)json_decode($product['composite_price']);
                                                                                            if( $format[$quantity] ) { $composit = $format[$quantity]; }
                                                                                        }
                                                                                        
                                                                                        $total = round($composit * $quantity * $price);

                                                                                        if($total > 999) $currency = ' р';
                                                                                    ?>
                                                                                    <?php echo $total; ?> <?php echo $currency; ?>
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    <?php } else { ?>
                                                                        <div class="p-o_short-descr" itemprop="description"><?php echo $product['description_short']; ?></div>
                                                                    <?php } ?>
                                                                    

                                                                    <div class="clearfix" itemscope itemtype="http://schema.org/Offer" itemprop="offers">
                                                                        <div class="p-o_select">
                                                                            <?php
                                                                                $price = floatval($product['price']); 
                                                                                if(empty($product['weight_variants'])) {
                                                                                    $value=1;
                                                                                }
                                                                                else{
                                                                                    $arVariants = explode(',', $product['weight_variants']);
                                                                                    $value=$arVariants[0];
                                                                                }
                                                                            ?>

                                                                            <?php if(empty($product['weight_variants'])) { ?>
                                                                                <div class="form-select" data-custom="product-grid">
                                                                                    <div class="select" data-value="<?php echo $value; ?>" data-index="1" tabindex="-1">
                                                                                        <div class="options">
                                                                                            <?php for($i=1; $i<=5; $i++) { ?>
                                                                                                <?php if( $i == 1 ) { ?>
                                                                                                    <div class="current" data-index="<?php echo $i; ?>" data-value="<?php echo $i; ?>"><?php echo $i; ?> <?php echo $product['weight_class']; ?></div>
                                                                                                    <div class="option" data-index="<?php echo $i; ?>" data-value="<?php echo $i; ?>" data-style="active"><?php echo $i; ?> <?php echo $product['weight_class']; ?></div>
                                                                                                <?php } else { ?>
                                                                                                    <div class="option" data-index="<?php echo $i; ?>" data-value="<?php echo $i; ?>"><?php echo $i; ?> <?php echo $product['weight_class']; ?></div>
                                                                                                <?php } ?>
                                                                                            <?php } ?>
                                                                                        </div>
                                                                                        
                                                                                        <select name="tech" class="tech">
                                                                                            <?php for($i=1; $i<=5; $i++) { ?>
                                                                                                <?php if( $i == 1 ) { ?>
                                                                                                    <option value="<?php echo $i; ?>" selected><?php echo $i; ?> <?php echo $product['weight_class']; ?></option>
                                                                                                <?php } else { ?>
                                                                                                    <option value="<?php echo $i; ?>"><?php echo $i; ?> <?php echo $product['weight_class']; ?></option>
                                                                                                <?php } ?>
                                                                                            <?php } ?>
                                                                                        </select> 
                                                                                    </div>
                                                                                </div>
                                                                            <?php } else { ?>
                                                                                <div class="form-select" data-custom="product-grid">
                                                                                    <div class="select" data-value="<?php echo $value; ?>" data-index="0" tabindex="-1">
                                                                                        <div class="options">
                                                                                            <?php  $arVariants = explode(',', $product['weight_variants']); ?>
                                                                                            <?php $cnt = 0; ?>

                                                                                            <?php foreach($arVariants as $i => $variant) { ?>
                                                                                                <?php if( $cnt == 0 ) { ?>
                                                                                                    <div class="current" data-index="<?php echo $i; ?>" data-value="<?php echo trim($variant); ?>"><?php echo trim($variant); ?> <?php echo $product['weight_class']; ?></div>
                                                                                                    <div class="option" data-index="<?php echo $i; ?>" data-value="<?php echo trim($variant); ?>" data-style="active"><?php echo trim($variant); ?> <?php echo $product['weight_class']; ?></div>
                                                                                                <?php } else { ?>
                                                                                                    <div class="option" data-index="<?php echo $i; ?>" data-value="<?php echo trim($variant); ?>"><?php echo trim($variant); ?> <?php echo $product['weight_class']; ?></div>
                                                                                                <?php } ?>

                                                                                                <?php $cnt++; ?>
                                                                                            <?php } ?>
                                                                                        </div>
                                                                                        
                                                                                        <select name="tech" class="tech">
                                                                                            <?php  $arVariants = explode(',', $product['weight_variants']); ?>
                                                                                            <?php $cnt = 0; ?>

                                                                                            <?php foreach($arVariants as $i => $variant) { ?>
                                                                                                <?php if( $cnt == 0 ) { ?>
                                                                                                    <option value="<?php echo trim($variant); ?>" selected><?php echo trim($variant); ?> <?php echo $product['weight_class']; ?></option>
                                                                                                <?php } else { ?>
                                                                                                    <option value="<?php echo trim($variant); ?>"><?php echo trim($variant); ?> <?php echo $product['weight_class']; ?></option>
                                                                                                <?php } ?>

                                                                                                <?php $cnt++; ?>
                                                                                            <?php } ?>
                                                                                        </select> 
                                                                                    </div>
                                                                                </div>
                                                                            <?php } ?>
                                                                        </div>

                                                                        <div class="p-o_right <?php if(isset($product['discount']) && $product['discount'] > 0) { echo 'sale'; } ?>">
                                                                            <meta itemprop="baseprice" content="<?php echo floatval($product['price']); ?>" />
                                                                            <meta itemprop="price" content="<?php if($product['special']) { echo floatval($product['special']); } else { echo floatval($product['price']); } ?>" />
                                                                            <meta itemprop="priceCurrency" content="RUB" />
                                                                            
                                                                            <div class="p-o_price">
                                                                                <?php
                                                                                    if($product['special']) { $price = floatval($product['special']); }
                                                                                    else { $price = floatval($product['price']); }

                                                                                    if(empty($product['weight_variants'])) {
                                                                                        $quantity=1;
                                                                                    }
                                                                                    else{
                                                                                        $arVariants = explode(',', $product['weight_variants']);
                                                                                        $quantity=$arVariants[0];
                                                                                    }

                                                                                    $composit = 1;

                                                                                    $currency = ' руб';

                                                                                    if(isset($product['composite_price'])) {
                                                                                        $format = (array)json_decode($product['composite_price']);
                                                                                        if( $format[$quantity] ) { $composit = $format[$quantity]; }
                                                                                    }
                                                                                    

                                                                                    $total = round($composit * $quantity * $price);

                                                                                    if($total > 999) $currency = ' р';
                                                                                ?>
                                                                                <?php echo $total; ?> <?php echo $currency; ?>
                                                                            </div>
                                                                            
                                                                            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                                                            
                                                                            <?php if($product['stock_status_id'] == 7) { ?>
                                                                                <input type="submit" value="" class="p-o_submit">
                                                                            <?php } else { ?>
                                                                                <div class="p-o_submit n-a_time" rel="tooltip" title="Поставка через <?php echo $product['available_in_time'] дн.; ?>"></div>
                                                                            <?php } ?>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                <?php } ?>
                                            </ul>

                                            <?php if( $lCount-2 > 0 ) { ?>
                                                <div class="show-more sm-lg" data-mode="catsort" data-target="<?php echo $subcategory['id']; ?>" data-parent="<?php echo $subcategory['parent']; ?>" data-default="еще <?php echo ($lCount-5); ?> продуктов"  style="<?php if($lCount <= 5) { ?>visibility:hidden;<?php } ?>">еще <?php echo ($lCount-5); ?> продуктов</div>
                                                <div class="show-more sm-md" data-mode="catsort" data-target="<?php echo $subcategory['id']; ?>" data-parent="<?php echo $subcategory['parent']; ?>" data-default="еще <?php echo ($lCount-4); ?> продуктов"  style="<?php if($lCount <= 4) { ?>visibility:hidden;<?php } ?>">еще <?php echo ($lCount-4); ?> продуктов</div>
                                                <div class="show-more sm-sm" data-mode="catsort" data-target="<?php echo $subcategory['id']; ?>" data-parent="<?php echo $subcategory['parent']; ?>" data-default="еще <?php echo ($lCount-3); ?> продуктов"  style="<?php if($lCount <= 3) { ?>visibility:hidden;<?php } ?>">еще <?php echo ($lCount-3); ?> продуктов</div>
                                                <div class="show-more sm-xs" data-mode="catsort" data-target="<?php echo $subcategory['id']; ?>" data-parent="<?php echo $subcategory['parent']; ?>" data-default="еще <?php echo ($lCount-2); ?> продуктов"  style="<?php if($lCount <= 2) { ?>visibility:hidden;<?php } ?>">еще <?php echo ($lCount-2); ?> продуктов</div>
                                            <?php } ?>
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
<!-- END Footer