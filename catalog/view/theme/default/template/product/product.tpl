<?php echo $header; ?>

<script>
    window.bodyClass = 'page_2';
</script>

<!-- Container -->
<section class="fond-white">
        <div class="width-1194 pd-29">
                <ul class="breadcrumbs clearfix" itemscope itemtype="http://schema.org/BreadcrumbList">
                        <?php foreach($breadcrumbs as $i => $item) { 
                            if($i < count($breadcrumbs) - 1) { ?>
                                <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                                        <a href="<?php echo $item['href']; ?>" itemprop="item">
                                                <span itemprop="name"><?php echo $item['text']; ?></span>
                                        </a>
                                </li>
                            <? } else { ?>
                                <li itemprop="itemListElement">
                                        <span itemprop="name"><?php echo $item['text']; ?></span>
                                </li>
                            <?php } ?>
                        <?php } ?>
                </ul>
                <div class="card-product clearfix" itemscope itemtype="http://schema.org/Product">
                        <div class="c-p_left">
                                <div class="c-p_thumb">
                                    <img src="<?php echo $popup; ?>" alt="" itemprop="image">
                                    <?php if(isset($discount_sticker)) { ?><div class="c-p_discount sticker_discount">-<?php echo $discount_sticker; ?>%</div>
                                    <?php } elseif($sticker_class) { ?><div class="c-p_discount sticker_<?php echo $sticker_class; ?>"><?php echo $sticker_name; ?></div><?php } ?>
                                </div>
                                <h1 class="c-p_title c-p_title-mobile" itemprop="name"><?php echo $heading_title; ?></h1>
                                <ul class="list-composition">
                                        <?php
                                        foreach($attribute_groups as $aGroup) { 
                                            if($aGroup['attribute_group_id'] == '7') { 
                                                foreach($aGroup['attribute'] as $attribute) {
                                                ?>
                                                    <li>
                                                            <div class="l-c_left"><?php echo $attribute['name']; ?></div>
                                                            <div class="l-c_right"><?php echo $attribute['text']; ?></div>
                                                    </li>
                                                <?php  
                                                }
                                            } 
                                        } 
                                        ?>
                                </ul>
                        </div>
                        <div class="c-p_right">
                                <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                <?php if(isset($composite_price)) { ?><input type="hidden" class="composite_price" value='<?php echo $composite_price;?>'><? } ?>
                                <h1 class="c-p_title" itemprop="name"><?php echo $heading_title; ?></h1>
                                <div class="c-p_city"><?php echo $description_short; ?></div>
                                <div class="c-p_txt">
                                    О продукте:
                                </div>
                                <ul class="c-p_list" itemprop="description">
                                        <?php foreach($props3 as $prop) { 
                                            if(!empty($prop)) { ?>
                                            <li><?php echo $prop; ?></li>
                                            <?php }
                                        } ?>
                                </ul>
                                <div class="box-a_d">
                                        <a href="#anchor-details" class="anchor-details">Подробнее</a>
                                </div>
                                <div class="size-0" itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
                                        <meta itemprop="price" content="<?php if($special) echo (int)$special; else echo (int)$price; ?>">
                                        <meta itemprop="priceCurrency" content="RUB">
                                        <div class="c-p_select">
                                                <?php if(empty($weight_variants)) { ?>
                                                    <select name="tech" class="tech">
                                                            <?php for($i=1; $i<=5; $i++) { ?>
                                                                <option value="<?php echo $i; ?>"><?php echo $i; ?> шт.</option>
                                                            <? } ?>
                                                    </select> 
                                                <?php } else { ?>
                                                    <select name="tech" class="tech">
                                                            <?php 
                                                            $arVariants = explode(',', $weight_variants);
                                                            foreach($arVariants as $i => $variant) { ?>
                                                                <option value="<?php echo $i; ?>"><?php echo trim($variant); ?> <?php echo $weight_class; ?></option>
                                                            <? } ?>
                                                    </select> 
                                                <?php } ?>
                                        </div>
                                        <?php if(empty($product['weight_variants'])) { ?>
                                            <div class="c-p_price"><?php if($special !== false) echo $special; else echo $price; ?></div>
                                        <?php } else { ?>
                                            <div class="c-p_price"><?php $tp = (int)((float)trim($arVariants[0])*(float)$product['price']); echo $tp; ?> <?php if($tp > 999) echo ' р'; else echo ' руб'; ?></div>
                                        <?php } ?>
                                </div>
                                <?php if($quantity > 0 || ($quantity <= 0 && $stock_status_id == 7)) { ?>
                                    <a href="#" class="c-p_submit">Добавить в корзину</a>
                                <?php } elseif($quantity <= 0 && $stock_status_id == 6) { ?>
                                    <a href="#" class="c-p_submit navl">Ожидаем поставку<?php if(!empty($available_in_time)) { ?> через <?php echo $available_in_time; } ?></a>
                                <?php } elseif($quantity <= 0 && $stock_status_id == 5) { ?>
                                    <a href="#" class="c-p_submit sold">Нет в наличии</a>
                                <?php } ?>
                        </div>
                </div>
        </div>
</section>
<!-- END Container  -->
<!-- Together with this product is often bought -->
<?php if(count($products) > 0) { ?>
<section class="fond-profitable_offer"> 
        <div class="width-1660">
                <div class="p-o_title"><?php echo $text_related; ?></div>
                <div class="slider-profitable_offer">
                    <?php foreach ($products as $product) { 
                        if($product['stock_status_id'] == 5 && $product['quantity'] <= 0) continue;
                    ?> 
                        <div>
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
                    <?php } ?>
                </div>
        </div>
</section>
<? } ?>
<!-- END Together with this product is often bought -->
<!-- About the product -->
<section id="anchor-details" class="fond-box_1">
        <div class="width-1194 about-product">
                <div class="a-p_title"><?php echo $heading_title; ?></div>
                <p>
                    <?php echo $description; ?>
                </p>
        </div>
</section>
<!-- END About the product -->

<?php echo $footer; ?>
