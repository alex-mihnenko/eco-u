<?php echo $header; ?>


<script>
    window.bodyClass = 'page_2';
</script>

<div class="product">
    <!-- Container -->
    <section class="fond-white">
        <div class="width-1194 pd-29">
            <!-- <div class="prod-page-top"></div> -->
            <hr class="indent xl">
            <hr class="indent xs">


            <ul class="breadcrumbs clearfix" itemscope itemtype="http://schema.org/BreadcrumbList" style="display: none;">
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

            <div class="hidden-md hidden-lg hidden-xl hidden-xxl text-align-center">
                <a href="/">
                    <img src="/catalog/view/theme/default/img/logo.png" alt="ЭКО-Ю" title="ЭКО-Ю" class="logo" style="width: 100px; height: 49px; margin-top: -12px;">
                </a>
                <hr class="indent sm">
            </div>

            <div class="hidden-xs hidden-sm">
                <div class="grid-row">
                    <div class="grid-col col-6">
                        <h1 class="c-p_title" itemprop="name"><?php echo $heading_title; ?></h1>
                    </div>
                    
                    <div class="grid-col col-6 align-end" style="position: relative;">
                        <a class="btn btn-sm" href="/">на главную</a>
                    </div>
                </div>
                
                <hr class="indent md">
            </div>

            <div class="card-product clearfix" itemscope itemtype="http://schema.org/Product">
                <div class="c-p_left">
                    <div class="c-p_thumb">
                        <img src="<?php echo $popup; ?>" alt="<?php echo $heading_title; ?>" itemprop="image">

                        <?php if(isset($discount) && $discount > 0) { ?><div class="c-p_discount sticker_discount"><?php echo $discount; ?>%</div>
                        <?php } elseif($sticker_class) { ?><div class="c-p_discount sticker_<?php echo $sticker_class; ?>"><?php echo $sticker_name; ?></div><?php } ?>
                    </div>

                    <div class="xxs-add-title text-align-center">
                        <h2 class="c-p_title" itemprop="name"><?php echo $heading_title; ?></h2>
                        <hr class="indent xs">
                    </div>
                </div>

                <div class="c-p_right">
                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                    <?php if(isset($composite_price)) { ?><input type="hidden" class="composite_price" value='<?php echo $composite_price;?>'><? } ?>
                    
                    <?php if(!empty($props3)) { ?>
                        <?php $props3Flag = false; ?>

                        <?php foreach($props3 as $prop) { ?>
                            <?php if(!empty($prop)) { $props3Flag = true; } ?>
                        <?php } ?>

                        <?php if( $props3Flag == true ) { ?>
                            <div class="c-p_txt">О продукте:</div>
                            <ul class="c-p_list" itemprop="description">
                                <?php foreach($props3 as $prop) { ?>
                                    <?php if(!empty($prop)) { ?><li><?php echo $prop; ?></li><?php } ?>
                                <?php } ?>
                            </ul>
                            
                            <hr class="indent xs">
                        <?php } ?>
                    <?php } ?>


                    <?php if(isset($discount) && $discount > 0) { ?>
                        <div class="product-sale"><span>Цена без скидки: </span><span class="price"><?php echo $price; ?></span></div>
                        <hr class="indent xs">
                    <?php } ?>

                    <div class="size-0" itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
                        <meta itemprop="baseprice" content="<?php echo intval($price); ?>" />
                        <meta itemprop="price" content="<?php if($special) { echo intval($special); } else { echo intval($price); } ?>" />
                        <meta itemprop="priceCurrency" content="RUB">
                        <div class="c-p_select">
                                <?php if(empty($weight_variants)) { ?>
                                    <select name="tech" class="tech">
                                            <?php for($i=1; $i<=5; $i++) { ?>
                                                <option value="<?php echo $i; ?>"><?php echo $i; ?> <?php echo $weight_class; ?></option>
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
                            <div class="c-p_price"><div class="c-p_price_shadow"></div></div>
                        <?php } else { ?>
                            <div class="c-p_price"><div class="c-p_price_shadow"></div></div>
                        <?php } ?>
                    </div>

                    <hr class="indent xs">

                    <?php if($quantity > 0 || ($quantity <= 0 && $stock_status_id == 7)) { ?>
                        <a href="#" class="c-p_submit">Добавить в корзину</a>
                    <?php } elseif($quantity <= 0 && $stock_status_id == 6) { ?>
                        <a href="#" class="c-p_submit navl">Ожидаем поставку<?php if(!empty($available_in_time)) { ?> через <?php echo $available_in_time; } ?></a>
                    <?php } elseif($quantity <= 0 && $stock_status_id == 5) { ?>
                        <a href="#" class="c-p_submit sold">Нет в наличии</a>
                    <?php } ?>

                    <hr class="indent md">

                    <?php if( $location != '' ) { ?>
                        <div class="c-p_txt">Регион поставки: <span style="font-weight:400;"><?php echo $location; ?></span></div>
                    <?php } ?>

                    <?php if( $shelf_life != '' ) { ?>
                        <div class="c-p_txt">Срок хранения: <span style="font-weight:400;"><?php echo $shelf_life; ?></span></div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>
    <!-- END Container  -->

    <hr class="indent md hidden-xxs">

    <!-- About the product -->
    <div class="grid-container width-1194 about-product">
        <div class="xxs-remove-title hidden-md hidden-lg hidden-xl hidden-xxl text-align-center">
            <h2 class="c-p_title" itemprop="name"><?php echo $heading_title; ?></h2>
            <hr class="indent xs">
        </div>

        <?php echo $description; ?>
    </div>
    <!-- END About the product -->

    <div class="width-1194 about-product">
        <?php $attributesFlag = false; ?>
        <?php $attributesNotNull = 0; ?>

        <?php foreach($attribute_groups as $aGroup) { ?>
            <?php if($aGroup['attribute_group_id'] == '7') { ?>

                <?php foreach($aGroup['attribute'] as $attribute) { ?>
                    <?php if($attribute['text'] != '0') { ?>
                        <?php $attributesNotNull = $attributesNotNull + 1; ?>
                    <?php } ?>
                <?php } ?>

                <?php $attributesFlag = true; ?>
            <?php } ?>
        <?php } ?>

        <?php if($attributesFlag == true && $attributesNotNull > 0 ) { ?>
            <div class="grid-container">
                <hr>
                <hr class="indent sm">

                <div class="grid-row align-start">
                    <div class="grid-col col-6">
                        <!-- Product attributes -->
                        <div class="attributes">
                            <span>Пищевая ценность:</span>
                            <hr class="indent xs">

                            <?php foreach($attribute_groups as $aGroup) { ?>
                                <?php if($aGroup['attribute_group_id'] == '7') { ?>
                                    <?php foreach($aGroup['attribute'] as $attribute) { ?>
                                        <?php if($attribute['text'] != '0') { ?>
                                            <div class="item">
                                                <div><p><?php echo $attribute['name']; ?></p></div>
                                                <div class="text-align-right"><p><b><?php echo $attribute['text']; ?></b></p></div>
                                            </div>
                                            <hr class="indent xxs">
                                        <?php } ?>
                                    <?php } ?>
                                <?php } ?>
                            <?php } ?>
                        </div>
                        <!-- END Product attributes -->
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
    <hr class="indent md">

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
                                                <img src="<?php if(!empty($product['thumb'])) echo $product['thumb']; else echo '/image/eco_logo.jpg'; ?>" alt="<?php echo $product['name']; ?>">
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
</div>

<?php echo $footer; ?>
