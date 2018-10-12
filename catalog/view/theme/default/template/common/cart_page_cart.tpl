<div class="cart-header">
    <div class="h3">Корзина</div>
    <hr class="indent xs">
</div>


<?php if($products) { ?>
    <div class="cart-products">
        <?php foreach($products as $product) { ?>
            <div class="product">

                <?php
                    if( $product['weight_class_id'] == 9 && $product['weight_variants'] == '' ) { $unit = $product['weight_class']; }
                    else { $unit = 'шт.'; }
                ?>

                <div class="col thumb">
                    <img src="<?php echo $product['image']; ?>" alt="" title="">
                </div>

                <div class="col about">
                    <p><?php echo $product['name']; ?></p>
                    
                    <?php if( $product['date_available'] != '0000-00-00' ) { ?>
                        <?php $date_available = explode('-', $product['date_available']); ?>
                        <p class="xxs">Дата поставки: <span class="text-color-orange"><?php echo $date_available[2].'.'.$date_available[1].'.'.$date_available[0]; ?></span></p>
                    <?php } ?>
                </div>

                <div class="col quantity">
                    <div class="p-o_select">
                        <select data-cart-quantity="<?php echo (int)$product['quantity']; ?>" data-cart-id="<?php echo $product['cart_id']; ?>" data-cart-variant="<?php echo $product['weightVariant']; ?>" name="m_cart[<?php echo $product['cart_id']; ?>]" class="tech change-m-cart-quantity">
                            <?php 
                                $quantity = (int)$product['quantity']; 
                                $start = $quantity - 9;
                                $end = $quantity + 9;
                                if($start < 1) $start = 1;
                            ?>
                            <?php for($i = $start; $i <= $end; $i++) { ?>
                                <?php if($i == $quantity) { ?>
                                    <option value="<?php echo $i; ?>" selected="selected"><?php echo $i; ?> <?php echo $unit; ?></option>
                                <?php } else { ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?> <?php echo $unit; ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select> 
                    </div>

                    <div class="total">
                        <hr class="indent xxs">
                        <p><?php echo $product['total']; ?> <span>руб.</span></p>
                    </div>
                </div>

                <div class="col total">
                    <p><?php echo $product['total']; ?> <span>руб.</span></p>
                </div>

                <button class="remove" data-href="<?php echo $product['link_remove']; ?>"></button>
            </div>
        <?php } ?>
    </div>

    <div class="text-align-right text-align-center-xs text-align-center-sm">
        <hr class="indent sm">
        <button data-action="cart-clear">очистить все</button>
    </div>
<?php } ?>


<div class="cart-footer text-align-center">

    <hr class="indent sm d-none d-md-block">

    <div class="cart-page-total">
        <div class="bonus">
            <?php if( $bonus != false ) { ?>
                <?php if( $bonus_apply == true ) { ?>
                    <div class="body">
                        <span><?php echo $bonus ?> <img src="/catalog/view/theme/default/img/cart/icon-ecoin.png" /></span>
                        <p>Бонусы <br class="d-block d-sm-none">зачислены!</p>
                    </div>
                <?php } else { ?>
                    <div class="body" data-action="apply-bonus">
                        <span><?php echo $bonus ?> <img src="/catalog/view/theme/default/img/cart/icon-ecoin.png" /></span>
                        <p>Оплатить <br class="d-block d-sm-none">бонусами</p>
                    </div>
                <?php } ?>
            <?php } else { ?> 
                <div class="body">
                    <a href="#modal-bonus-program">
                        <span><img src="/catalog/view/theme/default/img/cart/icon-ecoin.png" /></span>
                        <p>У вас пока нет <br class="d-block d-sm-none">бонусов</p>
                    </a>
                </div>
            <?php } ?>
        </div>

        <div class="total">
            <p class="xs">Стоимость заказа</p>

            <?php $totalend = intval(substr($total, -2)); ?>
            <?php $totallast = intval(substr($total, -1)); ?>

            <?php if( $totalend > 10 && $totalend <= 19 ) { ?>
                <?php $currency = 'рублей'; ?>
            <?php } else { ?>
                <?php
                    if( $totallast == 1 ) { $currency = 'рубль'; } 
                    else if( $totallast > 1 && $totallast < 5 )  { $currency = 'рубля'; }
                    else { $currency = 'рублей'; }
                ?>
            <?php } ?>

            <p class="h4 padding no"><?php echo $total; ?> <?php echo $currency; ?></p>
            <p class="xs">(без учета стоимости доставки)</p>
        </div>

        <div class="coupon">
            <?php if( intval($discount_percentage) > 0 ) { ?>
                <div class="body">
                    <span><?php echo round($discount_percentage) ?> <img src="/catalog/view/theme/default/img/cart/icon-coupon.png" /></span>
                    <p>Купон <br class="d-block d-sm-none">применен</p>
                </div>
            <?php } else { ?>
                <div class="body" data-action="show-apply-coupon">
                    <span><img src="/catalog/view/theme/default/img/cart/icon-coupon.png" /></span>
                    <p>Есть купон на <br class="d-block d-sm-none">скидку?</p>
                </div>
            <?php } ?>
        </div>
    </div>
    <hr class="indent xs">

    <div class="cart-page-total-mobile">
        <div class="bonus">
            <?php if( $bonus != false ) { ?>
                <?php if( $bonus_apply == true ) { ?>
                    <div class="body">
                        <span><?php echo $bonus ?> <img src="/catalog/view/theme/default/img/cart/icon-ecoin.png" /></span>
                        <p>Бонусы <br class="d-block d-sm-none">зачислены</p>
                    </div>
                <?php } else { ?>
                    <div class="body" data-action="apply-bonus">
                        <span><?php echo $bonus ?> <img src="/catalog/view/theme/default/img/cart/icon-ecoin.png" /></span>
                        <p>Оплатить <br class="d-block d-sm-none">бонусами</p>
                    </div>
                <?php } ?>
            <?php } else { ?> 
                <div class="body">
                    <a href="#modal-bonus-program">
                        <span><img src="/catalog/view/theme/default/img/cart/icon-ecoin.png" /></span>
                        <p>У вас пока нет <br class="d-block d-sm-none">бонусов</p>
                    </a>
                </div>
            <?php } ?>
        </div>

        <div class="coupon">
            <?php if( intval($discount_percentage) > 0 ) { ?>
                <div class="body">
                    <span><?php echo round($discount_percentage) ?> <img src="/catalog/view/theme/default/img/cart/icon-coupon.png" /></span>
                    <p>Купон применен</p>
                </div>
            <?php } else { ?>
                <div class="body" data-action="show-apply-coupon">
                    <span><img src="/catalog/view/theme/default/img/cart/icon-coupon.png" /></span>
                    <p>Есть купон на <br class="d-block d-sm-none">скидку?</p>
                </div>
            <?php } ?>
        </div>
    </div>
    <hr class="indent xs">

    <div class="coupon">
        <div class="apply">
            <input type="text" name="coupon" class="form-control" placeholder="Купон на скидку">
            <button class="btn btn-bordered btn-sm btn-inline margin xs" type="button" data-action="send-apply-coupon">OK</button>
        </div>
    </div>
    <hr class="indent xs">

    
    <?php if($error_total) { ?>
        <p class="xs text-color-green" style="display: block">Чтобы перейти к оформлению заказа,<br>его стоимость должна быть больше 1000 рублей.</p>
    <?php } else { ?>
        <hr class="indent xs">
        <button class="btn" data-step="2">Оформить заказ</button>
    <?php } ?>
</div>