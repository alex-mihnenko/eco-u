<?php
    if(count($products) == 0) { ?>
        <div class="search-not-found">По вашему запросу товары не найдены</div>
<?php } ?>
<ul class="list-letter search_product_list">
    <?php foreach($products as $key => $product) { 
        if($product['quantity'] <= 0 && $product['stock_status_id'] == 5) continue;
    ?>
    <li data-type="dynamic" data-product="<?php echo $product['product_id']; ?>">
        <div itemscope itemtype="http://schema.org/Product" itemprop="itemListElement">
                <meta itemprop="position" content="<?php echo $key; ?>" />
                <div class="box-p_o">
                       <meta content="<?php echo $product['thumb']; ?>" itemprop="image">
            
                        <a href="<?php echo $product['href']; ?>" class="p-o_thumb">
                           <img <?php if(!empty($product['thumb'])) echo 'src="/new_design/img/spinner.gif" data-src="'.$product['thumb'].'" class="b-lazy"'; else echo 'src="/image/eco_logo.jpg"'; ?> alt="<?php echo $product['name']; ?>">
                        </a>

                        <div class="p-o_block">
                                <?php if(isset($product['composite_price'])) { ?>
                                    <input type="hidden" class="composite_price" value='<?php echo json_encode($product['composite_price']); ?>'>
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
    <? } ?>
</ul>