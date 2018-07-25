<?php echo $header; ?>

<div class="remodal modal-repeat" data-remodal-id="modal-repeat">
    <button data-remodal-action="close" class="remodal-close"></button>

    <div class="body">
    </div>
</div>




<!-- Container -->
<script>
    window.bodyClass = 'page_2';
</script>

<hr class="indent md">

<section class="fond-white">
    <div class="width-1194 pd-29"> 
        <ul class="liTabs t_wrap t_wrap_1">
            <li class="t_item t-item_1">
                <!-- /// -->
                <a class="t_link t_link_1 cur" href="#"> <span>ИСТОРИЯ ЗАКАЗОВ</span></a>

                <div class="t_content">
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
                                <tr>
                                    <td class="t-h_number">№ <?php echo $order['order_id']; ?></td>
                                    <td class="t-h_width"><?php echo $order['date']; ?></td>
                                    <td class="t-h_width">
                                        <?php if( $order['status_id'] != 7 ) { ?>
                                            <p class="text-color-green"><?php echo $order['status']; ?></p>
                                        <?php } else { ?>
                                            <p class="text-color-red"><?php echo $order['status']; ?></p>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <p><?php echo round($order['total'],2); ?> руб.</p>
                                    </td>
                                    <td>
                                        <?php if( $order['paid'] == true ) { ?>
                                            <a href="#repeat-<?php echo $order['order_id']; ?>" class="btn btn-bordered btn-sm" data-action="order-repeat" data-order-id="<?php echo $order['order_id']; ?>">Повторить</a>
                                        <?php } else { ?>
                                            <?php if( $order['surcharge'] == true ) { ?>
                                                <a href="#pay-rbs-<?php echo $order['payment_custom_field']; ?>" class="btn btn-sm" data-action="rbs-surcharge" data-order-id="<?php echo $order['order_id']; ?>">Доплатить</a>
                                            <?php } else { ?>
                                                <a href="#pay-rbs-<?php echo $order['payment_custom_field']; ?>" class="btn btn-sm" data-action="rbs-payment" data-order-id="<?php echo $order['order_id']; ?>">Оплатить</a>
                                            <?php } ?>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </table>
                    </div>
                </div>
                <!-- /// -->
            </li>

            <li class="t_item t-item_1">
                <a class="t_link t_link_1" href="#"><span>ЛИЧНЫЕ ДАННЫЕ</span></a>
                <div class="t_content"> 
                        <div>
                                        <div class="current-discount">
                                            <div class="c-d_text">Текущая скидка</div>
                                            <div class="c-d_size"><?php echo intval($discount_percentage); ?>%</div>
                                        </div>
                                        <form class="form-personal">
                                                <div class="f-p_box">
                                                    <input type="text" data-name="customer_firstname" placeholder="Имя" value="<?php echo $customer['firstname']; ?>" class="f-p_input">
                                                </div>
                                                <div class="f-p_box">
                                                        <input type="text" data-name="customer_telephone" placeholder="Телефон" value="<?php echo $customer['telephone']; ?>" class="f-p_input" id="phone">
                                                </div>
                                                <div class="f-p_box">
                                                        <?php
                                                        $re = '/[0-9]+@eco-u.ru/';
                                                        if(1 === preg_match_all($re, $customer['email'], $matches, PREG_SET_ORDER, 0)) {
                                                        ?>
                                                            <input type="hidden" data-name="customer_email_virtual" value="<?php echo $customer['email']; ?>" class="f-p_input">
                                                            <input type="text" data-name="customer_email" placeholder="EMAIL" value="" class="f-p_input">
                                                        <? } else { ?>
                                                            <input type="text" data-name="customer_email" placeholder="EMAIL" value="<?php echo $customer['email']; ?>" class="f-p_input">
                                                        <? } ?>
                                                </div>
                                                <div class="f-p_box2">
                                                        <?php $lastAddress = count($customer['addresses'])-1;
                                                        foreach($customer['addresses'] as $i => $address) { ?>
                                                        <div class="f-p_address_container" data-index="<?php echo $address['address_id']; ?>">
                                                            <div class="f-p_address_remove <?php if($i == $lastAddress) { ?>last<?php } ?>" data-target="<?php echo $address['address_id']; ?>">&times;</div>
                                                            <input type="text" name="dynamic[]" data-name="customer_address" data-target-id="<?php echo $address['address_id']; ?>" placeholder="Адрес Доставки" value="<?php echo $address['value']; ?>" class="f-p_input">
                                                        </div>
                                                        <?php } ?>
                                                        <div class="f-p_plus"></div>
                                                </div>
                                                <div class="f-p_chek">
                                                    <input type="checkbox" id="myId1" name="myName1" <?php if($newsletter) { ?>checked=""<?php } ?>>
                                                    <label for="myId1">
                                                        <span class="pseudo-checkbox"></span>
                                                        <span class="label-text">Я согласен получать информацию о специальных предложениях</span>
                                                    </label>
                                                </div>
                                                <div class="clearfix f-p_mobile">
                                                        <span class="f-p_submit">Сохранить изменения</span>
                                                </div>
                                        </form>
                                </div>
                </div>
            </li>
        </ul>
    </div>
</section>
<!-- END Container  -->

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
<?php echo $footer; ?> 
