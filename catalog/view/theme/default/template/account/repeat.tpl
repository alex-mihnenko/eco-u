<div class="content">
    <div class="cart-header">
        <div class="h4">Заказ №<?php echo $order_id?></div>
        <hr class="indent xs">
    </div>

    <div class="cart-products">
        <?php foreach($products as $product) { ?>
            <div class="product">

                <?php if( $product['quantity_stock'] > 0 ) { ?>
                <div class="col thumb">
                <?php } else { ?>
                <div class="col thumb none">
                <?php } ?>
                    <img src="<?php echo $product['image']; ?>" alt="" title="">
                </div>

                <div class="col about">
                    <p><?php echo $product['name']; ?></p>
                </div>

                <div class="col quantity text-align-left-xs text-align-left-sm">
                    <p><?php echo $product['quantity']; ?> <span><?php echo $product['weight_class']; ?></span></p>

                    <div class="total">
                        <hr class="indent xxs">
                        <p><?php echo $product['total']; ?> <span>руб.</span></p>
                    </div>
                </div>

                <div class="col total">
                    <?php if( $product['quantity_stock'] > 0 ) { ?>
                        <p><?php echo $product['total']; ?> <span>руб.</span></p>
                    <?php } else { ?>
                        <p class="text-color-red">Нет в наличии</p>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    </div>


    <div class="cart-footer text-align-center">
        <p class="xs">Стоимость заказа</p>
        <p class="h4"><?php echo $total; ?> рублей</p>
        <p class="xs">(без учета скидки и стоимости доставки)</p>

        <hr class="indent xs">

        <button class="btn" data-action="repeat-confirm" data-order-id="<?php echo $order_id?>">Добавить в корзину</button>
        <div class="message-error">
            <hr class="indent xs">
            <p class="text-color-red"></p>
        </div>
    </div>
</div>

<div class="message success">
    <div class="wrap-table">
        <div class="wrap-cell">
            <img src="catalog/view/theme/default/img/svg/check-square.svg" class="svg lg" alt="check-square" title="check-square" />
            <hr class="indent sm">

            <div class="message-success"></div>
        </div>
    </div>
</div>