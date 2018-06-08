<div class="content">
    <div class="cart-header">
        <div class="h4">Заказ №<?php echo $order_id?></div>
        <hr class="indent xs">
    </div>

    <div class="cart-products">
        <?php foreach($products as $product) { ?>
            <div class="product">

                <?php if( $product['status'] == 1 && ($product['stock_status_id'] == 7 || $product['stock_status_id'] == 6) ) { ?>
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
                    <?php if( $product['status'] == 1 && ($product['stock_status_id'] == 7 || $product['stock_status_id'] == 6) ) { ?>
                        <p><?php echo $product['quantity']; ?> <span><?php echo $product['weight_class']; ?></span></p>
                    <?php } ?>

                    <div class="total">
                        <?php if( $product['status'] == 1 && ($product['stock_status_id'] == 7 || $product['stock_status_id'] == 6) ) { ?>
                            <hr class="indent xxs">
                            <p><?php echo $product['total']; ?> <span>руб.</span></p>
                        <?php } else { ?>
                            <p class="text-color-red">Нет в наличии</p>
                        <?php } ?>
                        
                    </div>
                </div>

                <div class="col total">
                    <?php if( $product['status'] == 1 && ($product['stock_status_id'] == 7 || $product['stock_status_id'] == 6) ) { ?>
                        <p><?php echo $product['total']; ?> <span>руб.</span></p>
                    <?php } else { ?>
                        <p class="text-color-red">Нет в наличии</p>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    </div>


    <div class="cart-footer text-align-center">
        <p class="xs">Сумма без учета скидок и доставки</p>

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