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


<?php echo $header; ?>

<?php echo $column_left; ?>

<!-- Content top -->
<?php echo $content_top; ?>
<!-- END Content top -->

<!-- Container -->
<hr class="indent xl">
<hr class="indent lg">

<div id="account" class="orders">
    <div class="container">
        <div class="container-center order-list">
            
            <div class="d-none d-md-block">
                <div class="row header">
                    <div class="col-md-2"><span>Номер заказа</span></div>
                    <div class="col-md-3"><span>Дата</span></div>
                    <div class="col-md-3"><span>Статус</span></div>
                    <div class="col-md-2"><span>Сумма</span></div>
                    <div class="col-md-2"></div>
                </div>
                
                <!-- <hr class="indent sm"> -->
            </div>

            <div class="body">
                <?php foreach($orders as $order) { ?>

                    <div class="row order" data-order-id="<?php echo intval($order['order_id']); ?>">
                        <div class="col-xs-4 col-sm-4 col-md-2" data-action="order-about">
                            <div class="d-block d-md-none">
                                <p class="xs d-block d-md-none">Дата заказа</p>
                                <span><?php echo $order['date']; ?></span>
                            </div>

                            <div class="d-none d-md-block">
                                <p class="xs d-block d-md-none">Номер заказа</p>
                                <span>#<?php echo $order['order_id']; ?></span>
                            </div>
                        </div>
                        
                        <div class="col-xs-4 col-sm-4 col-md-3" data-action="order-about">
                            <div class="d-none d-md-block">
                                <p class="xs d-block d-md-none">Дата заказа</p>
                                <span><?php echo $order['date']; ?></span>
                            </div>

                            <div class="d-block d-md-none">
                                <p class="xs d-block d-md-none">Сумма заказа</p>
                                <span><?php echo round($order['total'],2); ?> руб.</span>
                            </div>
                        </div>
                        
                        <div class="col-xs-4 col-sm-4 col-md-3" data-action="order-about">
                            <?php if( $order['status_id'] != 7 ) { ?>
                                <span class="text-color-green"><?php echo $order['status']; ?></span>
                            <?php } else { ?>
                                <span class="text-color-red"><?php echo $order['status']; ?></span>
                            <?php } ?>
                        </div>
                        
                        <div class="col-xs-8 col-sm-8 col-md-2" data-action="order-about">
                            <div class="d-none d-md-block">
                                <span><?php echo round($order['total'],2); ?> руб.</span>
                            </div>
                        </div>

                        <div class="col-xs-4 col-sm-4 col-md-2">
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
                        </div>
                    </div>
                    
                <?php } ?>
            </div>

        </div>
    </div>
</div>


<hr class="indent xl">
<hr class="indent xl d-none d-md-block">


<?php echo $content_bottom; ?>

<?php echo $column_right; ?>

<?php echo $footer; ?>