<?php foreach($products as $product) { 
    if($product['quantity'] <= 0 && $product['stock_status_id'] == 5) continue;
?>
<div class="modal-product">
    <div class="clearfix">
            <input type="hidden" class="product_id" value="<?php echo $product['product_id']; ?>">
            <?php if( $product['description']=='' ) { ?>
            <a href="<?php echo $product['href']; ?>" class="m-product_thumb" data-display="disabled">
            <?php } else { ?>
            <a href="<?php echo $product['href']; ?>" class="m-product_thumb">
            <?php } ?>
                <img src="/image/<?php if(!empty($product['image'])) echo $product['image']; else echo 'eco_logo.jpg'; ?>" alt="">

                <?php if(isset($product['discount']) && $product['discount'] > 0) { ?>
                    <div class="m-product_discount sticker_discount">
                        <span><?php echo $product['discount']; ?>%</span>
                    </div>
                <?php } elseif($product['sticker_class']) { ?>
                    <div class="m-product_discount sticker_<?php echo $product['sticker_class']; ?>"><span><?php echo $product['sticker_name']; ?></span></div>
                <?php } ?>
            </a>
            <div class="m-product_right">
                <div class="m-product_link">
                    <?php if( $product['description']=='' ) { ?>
                    <a href="<?php echo $product['href']; ?>" data-display="disabled">
                    <?php } else { ?>
                    <a href="<?php echo $product['href']; ?>">
                    <?php } ?>
                        <?php echo $product['name']; ?>
                    </a>
                </div>
                <div class="m-product_city"><?php echo $product['description_short']; ?></div>
                

                <?php if(!empty($product['props3']) && isset($product['props3'][0]) && !empty($product['props3'][0]) ) { ?>
                    <div class="m-product_text"><span style="line-height:2em;">О продукте:</span><br></div>
                    
                    <?php print_r($product['props3']);  ?>
                    
                    <ul class="m-product_list">
                        <?php foreach($product['props3'] as $prop) { 
                            if(!empty($prop)) { ?> <li><?php echo $prop; ?></li> <?php } ?>
                        <?php } ?>
                    </ul>
                <?php } ?>


                <div class="size-0">
                    <?php if(isset($product['composite_price'])) { ?><input type="hidden" class="composite_price" value='<?php echo json_encode($product['composite_price']);?>'><? } ?>
                    <div class="m-product_select">
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
                    <input type="hidden" class="product_baseprice" value="<?php echo intval($product['price']); ?>">
                    <input type="hidden" class="product_price" value="<?php if($product['special_price']) { echo intval($product['special_price']); } else { echo intval($product['price']); } ?>">

                    <?php if(empty($product['weight_variants'])) { ?>
                        <div class="m-product_price"><div class="m-product_price_shadow"></div></div>
                    <?php } else { ?>
                        <div class="m-product_price"><div class="m-product_price_shadow"></div></div>
                    <?php } ?>
                </div>


                <?php if($product['quantity'] <= 0 && $product['stock_status_id'] == 6) { ?>
                    <a href="#" class="m-product_submit navl" rel="tooltip" title="<?php echo $product['available_in_time']; ?>">Ожидаем поставку</a>
                <?php } else { ?>
                    <a href="#" class="m-product_submit">Добавить в корзину</a>
                <?php } ?>
                

                <?php if(isset($product['discount']) && $product['discount'] > 0) { ?>
                    <div class="product-sale"><span>Цена без скидки: </span><span class="price"><?php echo $product['price']; ?></span></div>
                <?php } else { ?>
                    <div class="product-sale empty"></div>
                <?php } ?>
            </div>
    </div>
</div>
<?php } ?>