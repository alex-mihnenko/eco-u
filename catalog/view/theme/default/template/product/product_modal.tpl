<div class="product">
    <!-- General -->
    <section class="fond-white">
        <div class="width-1194 pd-29">

            <ul class="breadcrumbs clearfix" itemscope itemtype="http://schema.org/BreadcrumbList" style="display: none;">
                <?php foreach($breadcrumbs as $i => $item) { 
                    if($i < count($breadcrumbs) - 1) { ?>
                        <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                            <span itemprop="name"><?php echo $item['text']; ?></span>
                        </li>
                    <? } else { ?>
                        <li itemprop="itemListElement">
                            <span itemprop="name"><?php echo $item['text']; ?></span>
                        </li>
                    <?php } ?>
                <?php } ?>
            </ul>

            <div class="card-product clearfix" itemscope itemtype="http://schema.org/Product">
                <h1 class="c-p_title" itemprop="name"><?php echo $heading_title; ?></h1>
                <hr class="indent xs">

                <div class="c-p_left">
                    <div class="c-p_thumb">
                        <img src="<?php echo $popup; ?>" alt="<?php echo $heading_title; ?>" itemprop="image">

                        <?php if(isset($discount) && $discount > 0) { ?><div class="c-p_discount sticker_discount"><?php echo $discount; ?>%</div>
                        <?php } elseif($sticker_class) { ?><div class="c-p_discount sticker_<?php echo $sticker_class; ?>"><?php echo $sticker_name; ?></div><?php } ?>
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
                            <div class="product-sale">
                                <span>Без скидки: </span>
                                <span class="price">
                                    <?php
                                        $price = floatval($price); 

                                        if(empty($weight_variants)) {
                                            $price_quantity=1;
                                        }
                                        else{
                                            $arVariants = explode(',', $weight_variants);
                                            $price_quantity=$arVariants[0];
                                        }

                                        $composit = 1;

                                        $currency = ' руб';

                                        if(isset($composite_price)) {
                                            $format = (array)json_decode($composite_price);
                                            if( $format[$price_quantity] ) { $composit = $format[$price_quantity]; }
                                        }
                                        
                                        $total = round($composit * $price_quantity * $price);

                                        if($total > 999) $currency = ' р';
                                    ?>
                                    <?php echo $total; ?> <?php echo $currency; ?>
                                </span>
                            </div>

                            <hr class="indent xs">
                        <?php } ?>

                        <div class="size-0" itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
                            <meta itemprop="baseprice" content="<?php echo floatval($price); ?>" />
                            <meta itemprop="price" content="<?php if($special) { echo floatval($special); } else { echo floatval($price); } ?>" />
                            <meta itemprop="priceCurrency" content="RUB" />


                            <div class="c-p_select">
                                <?php
                                    $price = floatval($price); 
                                    if(empty($weight_variants)) {
                                        $value=1;
                                    }
                                    else{
                                        $arVariants = explode(',', $weight_variants);
                                        $value=$arVariants[0];
                                    }
                                ?>

                                <?php if(empty($weight_variants)) { ?>
                                    <div class="form-select" data-custom="product-page">
                                        <div class="select lg" data-value="<?php echo $value; ?>" data-index="1" tabindex="-1">
                                            <div class="options">
                                                <?php for($i=1; $i<=5; $i++) { ?>
                                                    <?php if( $i == 1 ) { ?>
                                                        <div class="current" data-index="<?php echo $i; ?>" data-value="<?php echo $i; ?>"><?php echo $i; ?> <?php echo $weight_class; ?></div>
                                                        <div class="option" data-index="<?php echo $i; ?>" data-value="<?php echo $i; ?>" data-style="active"><?php echo $i; ?> <?php echo $weight_class; ?></div>
                                                    <?php } else { ?>
                                                        <div class="option" data-index="<?php echo $i; ?>" data-value="<?php echo $i; ?>"><?php echo $i; ?> <?php echo $weight_class; ?></div>
                                                    <?php } ?>
                                                <?php } ?>
                                            </div>
                                            
                                            <select name="tech" class="tech">
                                                <?php for($i=1; $i<=5; $i++) { ?>
                                                    <?php if( $i == 1 ) { ?>
                                                        <option value="<?php echo $i; ?>" selected><?php echo $i; ?> <?php echo $weight_class; ?></option>
                                                    <?php } else { ?>
                                                        <option value="<?php echo $i; ?>"><?php echo $i; ?> <?php echo $weight_class; ?></option>
                                                    <?php } ?>
                                                <?php } ?>
                                            </select> 
                                        </div>
                                    </div>
                                <?php } else { ?>
                                    <div class="form-select" data-custom="product-page">
                                        <div class="select lg" data-value="<?php echo $value; ?>" data-index="0" tabindex="-1">
                                            <div class="options">
                                                <?php  $arVariants = explode(',', $weight_variants); ?>
                                                <?php $cnt = 0; ?>

                                                <?php foreach($arVariants as $i => $variant) { ?>
                                                    <?php if( $cnt == 0 ) { ?>
                                                        <div class="current" data-index="<?php echo $i; ?>" data-value="<?php echo trim($variant); ?>"><?php echo trim($variant); ?> <?php echo $weight_class; ?></div>
                                                        <div class="option" data-index="<?php echo $i; ?>" data-value="<?php echo trim($variant); ?>" data-style="active"><?php echo trim($variant); ?> <?php echo $weight_class; ?></div>
                                                    <?php } else { ?>
                                                        <div class="option" data-index="<?php echo $i; ?>" data-value="<?php echo trim($variant); ?>"><?php echo trim($variant); ?> <?php echo $weight_class; ?></div>
                                                    <?php } ?>

                                                    <?php $cnt++; ?>
                                                <?php } ?>
                                            </div>
                                            
                                            <select name="tech" class="tech">
                                                <?php  $arVariants = explode(',', $weight_variants); ?>
                                                <?php $cnt = 0; ?>

                                                <?php foreach($arVariants as $i => $variant) { ?>
                                                    <?php if( $cnt == 0 ) { ?>
                                                        <option value="<?php echo trim($variant); ?>" selected><?php echo trim($variant); ?> <?php echo $weight_class; ?></option>
                                                    <?php } else { ?>
                                                        <option value="<?php echo trim($variant); ?>"><?php echo trim($variant); ?> <?php echo $weight_class; ?></option>
                                                    <?php } ?>

                                                    <?php $cnt++; ?>
                                                <?php } ?>
                                            </select> 
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                          
                            <div class="c-p_price">
                                <div class="c-p_price_shadow">
                                    <?php
                                        if($special) { $price = floatval($special); }
                                        else { $price = floatval($price); }

                                        if(empty($weight_variants)) {
                                            $price_quantity=1;
                                        }
                                        else{
                                            $arVariants = explode(',', $weight_variants);
                                            $price_quantity=$arVariants[0];
                                        }

                                        $composit = 1;

                                        $currency = ' руб';

                                        if(isset($composite_price)) {
                                            $format = (array)json_decode($composite_price);
                                            if( $format[$price_quantity] ) { $composit = $format[$price_quantity]; }
                                        }
                                        

                                        $total = round($composit * $price_quantity * $price);
                                    ?>
                                    <?php echo $total; ?> <?php echo $currency; ?>
                                </div>
                            </div>
                        </div>

                        <hr class="indent xs">

                        <?php if($quantity > 0 || ($quantity <= 0 && $stock_status_id == 7)) { ?>
                            <button class="c-p_submit">Добавить в корзину</button>
                        <?php } elseif($quantity <= 0 && $stock_status_id == 6) { ?>
                            <button class="c-p_submit navl">Добавить в корзину</button>
                        <?php } elseif($quantity <= 0 && $stock_status_id == 5) { ?>
                            <button class="c-p_submit sold">Нет в наличии</button>
                        <?php } ?>

                        <hr class="indent md">

                        <?php if( $quantity <= 0 && $stock_status_id == 6 ) { ?>
                            <?php if(!empty($available_in_time)) { ?>
                                
                                <?php
                                    $available_day = intval($available_in_time);

                                    if( $available_day == 1 ){
                                        $available_postfix = 'день';
                                    }
                                    else if( $available_day > 1 && $available_day < 5 ){
                                        $available_postfix = 'дня';
                                    }
                                    else if( $available_day >= 5 && $available_day <= 20 ){
                                        $available_postfix = 'дней';
                                    }
                                    else {
                                        $available_day_last = intval(substr($available_in_time, -1));

                                        if( $available_day_last == 1 ) { $available_postfix = 'день'; } 
                                        else if( $available_day_last > 1 && $available_day_last < 5 )  { $available_postfix = 'дня'; }
                                        else { $available_postfix = 'дней'; }
                                    }
                                ?>

                                <div class="c-p_txt">Ожидаем поставку: <span style="font-weight:400;">через <?php echo $available_day; ?> <?php echo $available_postfix; ?></span></div>
                            <?php } ?>
                        <?php } ?>

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
    <!-- END General  -->

    <hr class="indent md">

    <!-- About the product -->
    <div class="grid-container about-product">
        <?php echo $description; ?>
    </div>
    <!-- END About the product -->

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
