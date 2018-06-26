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
                            <div class="c-p_txt">О продукте:</div>
                            <ul class="c-p_list" itemprop="description">
                                <?php foreach($props3 as $prop) { ?>
                                    <?php if(!empty($prop)) { ?><li><?php echo $prop; ?></li><?php } ?>
                                <?php } ?>
                            </ul>
                        <?php } ?>

                        <hr class="indent xs">

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
                            <div class="c-p_txt">Происхождение: <span style="font-weight:400;"><?php echo $location; ?></span></div>
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
    <section id="anchor-details" class="fond-box_1">
        <div class="width-1194 about-product">
            <?php echo $description; ?>
        </div>
    </section>
    <!-- END About the product -->

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
                                <div class="item">
                                    <div><p><?php echo $attribute['name']; ?></p></div>
                                    <div class="text-align-right"><p><b><?php echo $attribute['text']; ?></b></p></div>
                                </div>
                                <hr class="indent xxs">
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                <!-- END Product attributes -->
            </div>
        </div>
    </div>
</div>
