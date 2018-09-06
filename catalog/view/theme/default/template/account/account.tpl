<?php echo $header; ?>

<div class="remodal modal-order-about" data-remodal-id="modal-order-about">
    <button data-remodal-action="close" class="remodal-close"></button>

    <div class="body">
    </div>
</div>


<div class="remodal modal-repeat" data-remodal-id="modal-repeat">
    <button data-remodal-action="close" class="remodal-close"></button>

    <div class="body">
    </div>
</div>


<!-- Content top -->
<?php echo $content_top; ?>
<!-- END Content top -->


<!-- Container -->
<hr class="indent lg">
<hr class="indent md">

<div id="account">
    <div class="container">
        <div class="table-responsive">
            <table class="table-history">
                <tr>
                    <th>Номер заказа</th>
                    <th>Дата</th>
                    <th>Статус</th>
                    <th>Сумма</th>
                    <th></th>
                </tr>


                <?php foreach($orders as $order) { ?>
                    <tr data-order-id="<?php echo $order['order_id']; ?>">
                        <td class="t-h_number" data-action="order-about">№ <?php echo $order['order_id']; ?></td>
                        <td class="t-h_width" data-action="order-about"><?php echo $order['date']; ?></td>
                        <td class="t-h_width" data-action="order-about">
                            <?php if( $order['status_id'] != 7 ) { ?>
                                <p class="text-color-green"><?php echo $order['status']; ?></p>
                            <?php } else { ?>
                                <p class="text-color-red"><?php echo $order['status']; ?></p>
                            <?php } ?>
                        </td>
                        <td data-action="order-about">
                            <p><?php echo round($order['total'],2); ?> руб.</p>
                        </td>
                        <td>
                            <?php if( $order['status_id'] == 5 || $order['status_id'] == 7 ) { ?>
                                <a href="#repeat-<?php echo $order['order_id']; ?>" class="btn btn-bordered btn-sm" data-action="order-repeat" data-order-id="<?php echo $order['order_id']; ?>">Повторить</a>
                            <?php } else { ?>
                                <?php if( $order['paid'] == false ) { ?>
                                    <?php if( $order['surcharge'] == true ) { ?>
                                        <a href="#pay-rbs-<?php echo $order['payment_custom_field']; ?>" class="btn btn-sm" data-action="rbs-surcharge" data-order-id="<?php echo $order['order_id']; ?>">Доплатить</a>
                                    <?php } else { ?>
                                        <a href="#pay-rbs-<?php echo $order['payment_custom_field']; ?>" class="btn btn-sm" data-action="rbs-payment" data-order-id="<?php echo $order['order_id']; ?>">Оплатить</a>
                                    <?php } ?>
                                <?php } ?>
                            <?php } ?>

                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</div>

<hr class="indent lg">

<!-- Favorite Products -->
<section class="fond-f-p">
        <div class="width-1418 clearfix">
                <div class="f-p_title">Любимые продукты</div>
        </div>
        <div class="width-1660">
                <div class="slider-preferable-products">
                        <?php foreach ($pref_products as $product) { 
                            if($product['stock_status_id'] == 5 && $product['quantity'] <= 0) continue;
                            ?> 
                            <div>
                                <div class="box-p_o">
                                   <meta content="<?php echo $product['thumb']; ?>" itemprop="image">
                                    
                                    <!-- b-lazy -->
                                    <a href="<?php echo $product['href']; ?>" class="p-o_thumb">
                                        <?php if( !empty($product['thumb']) ) { ?>
                                            <img src="<?php echo $product['thumb']; ?>" data-src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>">
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
                                                        <div class="select" data-value="<?php echo $value; ?>" data-index="0" tabindex="-1">
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
                                                    <div class="p-o_submit n-a_time" rel="tooltip" title="<?php echo $product['available_in_time']; ?>"></div>
                                                <?php } ?>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                </div>
        </div>
</section>
<!-- Favorite Products -->

<?php echo $content_bottom; ?>

<?php echo $footer; ?> 
