<div class="cart-header">
    <div class="h3">Корзина</div>
    <hr class="indent xs">
</div>


<div class="cart-products">
    <?php foreach($products as $product) { ?>
        <div class="product">

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
                            $start = $quantity - 5;
                            $end = $quantity + 5;
                            if($start < 1) $start = 1;
                        ?>
                        <?php for($i = $start; $i <= $end; $i++) { ?>
                            <?php if($i == $quantity) { ?>
                                <option value="<?php echo $i; ?>" selected="selected"><?php echo $i; ?> шт.</option>
                            <?php } else { ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?> шт.</option>
                            <?php } ?>
                        <?php } ?>
                    </select> 
                </div>

                <div class="total">
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
    <p class="h4"><?php echo $total; ?> рублей</p>
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
                    <button class="btn btn-bordered btn-sm margin xs" type="button" data-action="send-apply-coupon">OK</button>
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