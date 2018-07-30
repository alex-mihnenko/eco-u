<div class="content">
    <div class="cart-header">
        <h2 class="h4">Заказ №<?php echo $order_id; ?></h2>
        <p class="order-status">
            <span>от <?php echo $order_date; ?></span> 

            <?php if( $order_status_id == 5 ) { ?>
                    <span><i class="svg"><?php loadSvg('name', 'icon-status-success.svg'); ?></i> <?php echo $order_status_text; ?></span>
            <?php } else if( $order_status_id == 1 ) { ?>
                    <span><i class="svg"><?php loadSvg('name', 'icon-status-wait.svg'); ?></i> <?php echo $order_status_text; ?></span>
            <?php } else if( $order_status_id == 2 ) { ?>
                    <span><i class="svg"><?php loadSvg('name', 'icon-status-wait.svg'); ?></i> <?php echo $order_status_text; ?></span>
            <?php } else if( $order_status_id == 16 ) { ?>
                    <span><i class="svg"><?php loadSvg('name', 'icon-status-wait.svg'); ?></i> <?php echo $order_status_text; ?></span>
            <?php } else if( $order_status_id == 3 ) { ?>
                    <span><i class="svg"><?php loadSvg('name', 'icon-status-success.svg'); ?></i> <?php echo $order_status_text; ?></span>
            <?php } else if( $order_status_id == 12 ) { ?>
                    <span><i class="svg"><?php loadSvg('name', 'icon-status-wait.svg'); ?></i> <?php echo $order_status_text; ?></span>
            <?php } else if( $order_status_id == 14 ) { ?>
                    <span><i class="svg"><?php loadSvg('name', 'icon-status-success.svg'); ?></i> <?php echo $order_status_text; ?></span>
            <?php } else if( $order_status_id == 13 ) { ?>
                    <span><i class="svg"><?php loadSvg('name', 'icon-status-success.svg'); ?></i> <?php echo $order_status_text; ?></span>
            <?php } else if( $order_status_id == 11 ) { ?>
                    <span><i class="svg"><?php loadSvg('name', 'icon-status-wait.svg'); ?></i> <?php echo $order_status_text; ?></span>
            <?php } else if( $order_status_id == 17 ) { ?>
                    <span><i class="svg"><?php loadSvg('name', 'icon-status-cancel.svg'); ?></i> <?php echo $order_status_text; ?></span>
            <?php } else if( $order_status_id == 8 ) { ?>
                    <span><i class="svg"><?php loadSvg('name', 'icon-status-success.svg'); ?></i> <?php echo $order_status_text; ?></span>
            <?php } else if( $order_status_id == 9 ) { ?>
                    <span><i class="svg"><?php loadSvg('name', 'icon-status-success.svg'); ?></i> <?php echo $order_status_text; ?></span>
            <?php } else if( $order_status_id == 10 ) { ?>
                    <span><i class="svg"><?php loadSvg('name', 'icon-status-success.svg'); ?></i> <?php echo $order_status_text; ?></span>
            <?php } else if( $order_status_id == 7 ) { ?>
                    <span><i class="svg"><?php loadSvg('name', 'icon-status-cancel.svg'); ?></i> <?php echo $order_status_text; ?></span>
            <?php } ?>
        </p>
        <hr class="indent xs">
    </div>

    <div class="cart-products">
        <?php foreach($products as $product) { ?>
            <div class="product">

                <?php
                    if( $product['quantity_stock'] > 0 && $product['status'] == 1 ) { $instock = true; }
                    else if ( $product['quantity_stock'] <= 0 && $product['status'] == 1 && ($product['stock_status_id'] == 7 || $product['stock_status_id'] == 6) ) { $instock = true; }
                    else { $instock = false; }
                ?>

                <div class="col thumb">
                    <img src="<?php echo $product['image']; ?>" alt="" title="">
                </div>

                <div class="col about">
                    <p><?php echo $product['name']; ?></p>
                </div>

                <div class="col quantity text-align-left-xs text-align-left-sm">
                    <p><?php echo $product['quantity']; ?> <span><?php echo $product['weight_class']; ?></span></p>

                    <div class="total">
                        <hr class="indent xxs">
                        <p><?php echo round($product['total']); ?> <span>руб.</span></p>
                    </div>
                </div>

                <div class="col total">
                    <p><?php echo $product['wKey']; ?></p>
                    <p><?php echo round($product['total']); ?> <span>руб.</span></p>
                </div>
            </div>
        <?php } ?>
    </div>

    <div class="delivery">
        <div class="col thumb">
            <i class="svg"><?php loadSvg('name', 'icon-delivery.svg'); ?></i>
        </div>

        <div class="col about">
            <p><?php echo $shipping_method; ?></p>
            <hr class="indent xs">

            <p class="xs"><?php echo $shipping_address_1; ?></p>
            <p class="xs"><?php echo $delivery_date; ?> <?php echo $delivery_time; ?></p>
        </div>

        <div class="col total">
            <p><?php echo $shipping_total; ?> <span>руб.</span></p>
        </div>
    </div>

    <div class="cart-footer text-align-center">
        <hr class="indent sm">

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