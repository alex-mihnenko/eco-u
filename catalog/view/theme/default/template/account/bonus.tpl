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
                    Вы делаете заказы и получаете обратно кэшбек в виде 3% от суммы на свой бонусный счет в кабинете.
                    При следующем заказе используйте бонусы при оформлении заказа и получите скидку на эту сумму.
                    В ближайшее время для наших постоянных покупателей мы реализуем программу привилегий!
                </p>
            </div>

            <hr class="indent lg">
        </div>
    </div>

    <div class="addon">
        <hr class="indent lg">

        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="h4">Как это работает?</h3>
                    <hr class="indent xs">

                    <p class="xs">
                        Мы разработали бонусную систему, которая позволит вам получить накопительную скидку уже с первой покупки.
                        <br><br>
                        Теперь за каждые 30 рублей совершенного заказа на Ваш бонусный счет будет начислен 1 экоин. При следующем заказе вы сможете использовать его при оформлении заказа и получить скидку на эквивалент накопленных бонусов в рублях.
                        <br>
                        Кроме того в будущем мы реализуем возможность дополнительных бонусов за общую сумму всех заказов, а также программу привилегий!
                    </p>
                    <hr class="indent md d-block d-sm-none">
                </div>
                <div class="col-md-6">
                    <h3 class="h4">Как получить бонусы?</h3>
                    <hr class="indent xs">

                    <p class="xs"><b>За частоту покупок:</b></p>
                    <ul class="list">
                        <li>повторный заказ в течении недели + 100 екоинов</li>
                        <li>повторный заказ в течении двух недель + 50 екоинов</li>
                    </ul>
                    <hr class="indent xs">

                    <p class="xs"><b>За обратную связь:</b></p>
                    <ul class="list">
                        <li>За оставленный отзыв на сайте + 30 екоинов</li>
                        <li>За размещение поста в инстаграмм + 100 екоинов</li>
                    </ul>
                    <hr class="indent md d-block d-sm-none">
                </div>
            </div>
        </div>

        <hr class="indent lg d-none d-md-block">
    </div>
    <hr class="indent md">

    <div class="text-align-center">
        <a href="/#l-p_new" class="btn">Начать покупки</a>
    </div>
    <hr class="indent lg">
</div>


<?php echo $content_bottom; ?>

<?php echo $column_right; ?>

<?php echo $footer; ?>