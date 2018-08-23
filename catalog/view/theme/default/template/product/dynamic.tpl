<?php foreach($products as $key => $product) { ?>
    <?php if(($product['quantity'] <= 0 && $product['stock_status_id'] == 5) || $product['status'] != 1) { ?>
        <?php continue; ?>
    <?php } ?>

    <li data-product="<?php echo $product['product_id']; ?>" data-type="dynamic">
        <div id="catsorted_prod_<?php echo $product['product_id']; ?>" itemscope itemtype="http://schema.org/Product" itemprop="itemListElement">
            <meta itemprop="position" content="<?php echo $key; ?>" />

            <div class="box-p_o">
               <meta content="<?php echo $product['thumb']; ?>" itemprop="image">
                
                <!-- b-lazy -->
                <a href="<?php echo $product['href']; ?>" class="p-o_thumb">
                    <?php if( !empty($product['thumb']) ) { ?>
                        <img src="/catalog/view/theme/default/img/loading.svg" data-src="<?php echo $product['thumb']; ?>" class="image-lazy-load" alt="<?php echo $product['name']; ?>">
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
                            
                            <?php if($product['quantity'] > 0 || $product['stock_status_id'] == 7) { ?>
                                <input type="submit" value="" class="p-o_submit">
                            <?php } else { ?>
                                <?php if( $product['quantity'] <= 0 && !empty($product['available_in_time']) ) { ?>
                                    <div class="p-o_submit n-a_time" rel="tooltip" title="Поставка через <?php echo $product['available_in_time']; ?> дн."></div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </li>
<?php } ?>