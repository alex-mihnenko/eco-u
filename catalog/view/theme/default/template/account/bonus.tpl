<?php echo $header; ?>

<?php echo $column_left; ?>

<!-- Content top -->
<?php echo $content_top; ?>
<!-- END Content top -->

<!-- Container -->
<hr class="indent xl">
<hr class="indent xl">

<div id="account" class="bonus">
    <div class="container-wrapper">
        <div class="container text-align-center">

            <div class="container-center">
                <h1 class="h3 margin padding no"><?php echo $heading_title; ?></h1>
            </div>
            <hr class="indent xs">

            <?php if( $bonus > 0 ) { ?>
                <div class="ecoins">
                    <p><i class="svg" data-src="icon-ecoin-sample.svg"><?php loadSvg('name', 'icon-ecoin-sample.svg'); ?></i> <?php echo $bonus; ?></p>
                </div>
            <?php } else { ?>
                <h3 class="h3 text-color-red">Нет бонусов</h3>
            <?php } ?>
            <hr class="indent md">


            <div class="container-center">
                <p>
                    Покупайте больше - платите меньше.<br>
                    Возрашаем кэшбек в виде бонусов за каждый заказ!
                </p>
            </div>

            <hr class="indent lg">

            <div class="container-center bonus-list">
                
                <div class="container-center">
                    <h2 class="h5 margin padding no">История ваших начислений</h2>
                </div>
                <hr class="indent md">

                <div class="d-none d-sm-block">
                    <div class="row header">
                        <div class="col-sm-3 col-md-3"><span>Дата</span></div>
                        <div class="col-sm-3 col-md-3"><span>Сумма</span></div>
                        <div class="col-sm-6 col-md-6"><span>Основание</span></div>
                    </div>
                </div>

                <div class="body">
                    <?php foreach($bonuses as $bonus) { ?>

                        <div class="row header">
                            <div class="col-xs-6 col-sm-3 col-md-3">
                                <span><?php echo date('d.m.Y', $bonus['time']); ?></span>
                            </div>
                            <div class="col-xs-6 col-sm-3 col-md-3">
                                <?php if( $bonus['amount'] > 0) { ?>
                                    <span class="text-color-green">+<?php echo $bonus['amount']; ?></span>
                                <?php } else { ?>
                                    <span class="text-color-red"><?php echo $bonus['amount']; ?></span>
                                <?php } ?>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-6">
                                <hr class="indent xs d-block d-sm-none">
                                <p class="xs"><?php echo $bonus['comment']; ?></p>
                            </div>
                        </div>

                    <?php } ?>
                </div>

            </div>

            <hr class="indent lg">
        </div>
    </div>

    <div class="addon">
        <hr class="indent lg">

        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="h4">Как работают бонусы?</h3>
                    <hr class="indent xs">

                    <p class="xs">
                        <span class="h5 text-color-green"><i class="fa fa-retweet"></i> Бонусы за каждую покупку</span><br>
                        Получайте 1 бонус за каждые 30 рублей сделанного заказа.
                        <br><br>
                        
                        <span class="h5 text-color-green"><i class="fa fa-pie-chart"></i> Оплата покупок бонусами</span><br>
                        Списывайте бонусы для получения скидки на покупки по курсу<br>1 бонус= 1 рубль. Но не более 20% от общей стоимости заказа.
                        <br><br>

                        <span class="h5 text-color-green"><i class="fa fa-star-o"></i> Оставайтесь с нами</span><br>
                        Наши постоянным клиентам мы увеличиваем сумму начислений<br>бонусов в зависимости от общей суммы покупок.
                    </p>
                    <hr class="indent xs">

                    <p class="xxs">* Бонусы действуют в течении 160 дней с момента последнего заказа.</p>
                    <hr class="indent md d-block d-sm-none">
                </div>
                <div class="col-md-6">
                    <h3 class="h4">За что еще можно получить бонусы?</h3>
                    <hr class="indent xs">

                    <ul class="list colors green">
                        <li><i class="fa fa-calendar-o"></i> За повторный заказ в течении недели 100 бонусов</li>
                        <li><i class="fa fa-calendar-plus-o"></i> За повторный заказ в течении двух недель 50 бонусов</li>
                        <li><i class="fa fa-comments-o"></i> За оставленный отзыв на сайте 30 бонусов</li>
                        <li><i class="fa fa-instagram"></i> За размещение поста в Instagram 100 бонусов</li>
                    </ul>
                    <hr class="indent md d-block d-sm-none">
                </div>
            </div>
        </div>

        <hr class="indent lg d-none d-md-block">
    </div>
    <hr class="indent md">

    <div class="container text-align-center">
        <a href="/#l-p_new" class="btn">Начать покупки</a>
    </div>
    <hr class="indent lg">
</div>


<?php echo $content_bottom; ?>

<?php echo $column_right; ?>

<?php echo $footer; ?>