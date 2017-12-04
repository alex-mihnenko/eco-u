<?php foreach($products as $product) { 
    if($product['quantity'] < 0 && $product['stock_status_id'] == 5) continue;
?>
<div class="modal-product">
    <div class="clearfix">
            <input type="hidden" class="product_id" value="<?php echo $product['product_id']; ?>">
            <a href="<?php echo $product['href']; ?>" class="m-product_thumb">
                <img src="/image/<?php if(!empty($product['image'])) echo $product['image']; else echo 'eco_logo.jpg'; ?>" alt="">
                <?php if(isset($product['discount_sticker'])) { ?><div class="m-product_discount sticker_discount">-<?php echo $product['discount_sticker']; ?>%</div>
                <?php } elseif($product['sticker_class']) { ?><div class="m-product_discount sticker_<?php echo $product['sticker_class']; ?>"><?php echo $product['sticker_name']; ?></div><?php } ?>
            </a>
            <div class="m-product_right">
                    <div class="m-product_link">
                        <a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
                    </div>
                    <div class="m-product_city"><?php echo $product['description_short']; ?></div>
                    <?php if(!empty($product['attribute_groups'])) { ?>
                        <div class="m-product_text"><span style="line-height:2em;">О продукте:</span><br></div>
                        <ul class="m-product_list">
                                <?php foreach($product['props3'] as $prop) { 
                                    if(!empty($prop)) { ?>
                                    <li><?php echo $prop; ?></li>
                                    <?php }
                                } ?>
                        </ul>
                    <?php } ?>
                    <div class="size-0">
                            <?php if(isset($product['composite_price'])) { ?><input type="hidden" class="composite_price" value='<?php echo json_encode($product['composite_price']);?>'><? } ?>
                            <div class="m-product_select">
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
                            <input type="hidden" class="product_price" value="<?php if(!empty($product['special'])) echo (int)$product['special']; else echo (int)$product['price']; ?>">
                            <?php if(empty($product['weight_variants'])) { ?>
                                <div class="m-product_price"><?php if($product['price'] > 999) echo (int)$product['price'].' р'; else echo (int)$product['price']; ?> руб</div>
                            <?php } else { ?>
                                <div class="m-product_price"><?php $tp = (int)((float)trim($arVariants[0])*(float)$product['price']); echo $tp; ?> <?php if($tp > 999) echo ' р'; else echo ' руб'; ?></div>
                            <?php } ?>
                    </div>
                    <a href="#" class="m-product_submit">Добавить в корзину</a>
            </div>
    </div>
</div>
<?php } ?>