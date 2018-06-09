<div class="cart-header">
    <div class="h3">Корзина</div>
    <hr class="indent xs">
</div>


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


<div class="cart-footer text-align-center">
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

    <p class="h4"><?php echo $total; ?> <?php echo $currency; ?></p>
    <p class="xs">(без учета стоимости доставки)</p>

    <hr class="indent xs">


    <?php if($error_total) { ?>
        <p class="xs" style="display: block">Сумма заказа меньше 1000 рублей.</p>
    <?php } else { ?>
        <div class="coupon"> 
            <?php if( intval($discount) == 0 ) { ?>
                <button class="link" data-action="show-apply-coupon" type="button">Есть купон на скидку?</button>
                
                <div class="apply">
                    <input type="text" name="coupon" class="form-control" placeholder="Купон на скидку">
                    <button class="btn btn-bordered btn-sm btn-inline margin xs" type="button" data-action="send-apply-coupon">OK</button>
                </div>

                <p class="xs message-error"></p>
            <?php } else { ?>
                <p class="text-color-green">Ваша скидка <?php echo intval($discount) ?> руб.</p>
            <?php } ?>
        </div>
        <hr class="indent xs">

        <button class="btn" data-step="2">Оформить заказ</button>
    <?php } ?>
</div>