<div>
    <hr class="indent xs">
    <div class="h3">Доставка</div>
    <hr class="indent xs">
</div>

<div class="cart-form">
    <form action="index.php" method="post">
        <div class="row">
            <div class="col">
                <div class="input-group required">
                    <input type="text" class="form-input" placeholder="Имя" name="firstname" value="<?php echo $customer['first_name']; ?>">
                </div>
                <hr class="indent xs">
            </div>

            <div class="col">
                <div class="input-group required">
                    <input type="text" class="form-input" placeholder="Номер телефона" name="telephone" value="<?php echo substr($customer['phone'], 1); ?>">
                </div>
                <hr class="indent xs">
            </div>
        </div>

        <div class="delivery-address-container">
            <div class="input-group required">
                <?php if(isset($delivery_address) && count($delivery_address) > 0) { ?>
                <select name="address" class="form-input select">
                    <?php foreach($delivery_address as $address) { ?>
                    <option value="<?php echo $address['value']; ?>"><?php echo $address['value']; ?></option>
                    <?php } ?>
                    <option value="0">Новый адрес доставки</option>
                </select> 
                <?php } else { ?>
                <input type="text" class="form-input text-align-center text-align-left-xs input" name="address" value="" placeholder="Адрес доставки">
                <?php } ?>
            </div>
        </div>
        <hr class="indent xs">
        
        <div class="input-group">
            <textarea class="form-input " name="comment" value="" placeholder="Комментарий к заказу"></textarea>
        </div>
        <hr class="indent xs">


        <div class="row payment-methods">
            <?php foreach($payment_methods as $payment_method) { ?>
                <?php if ($payment_method['code']=='cod') { $display = 'active'; } else { $display = 'default'; } ?>
                <div class="col payment-method" data-title="<?php echo $payment_method['title']; ?>" data-code="<?php echo $payment_method['code']; ?>" data-display="<?php echo $display; ?>">
                    <p>
                        <img alt="<?php echo $payment_method['title']; ?>" title="<?php echo $payment_method['title']; ?>" src="<?php echo $payment_method['image']; ?>">
                        <?php echo $payment_method['title']; ?>
                    </p>
                </div>
            <?php } ?>
        </div>
        <hr class="indent xxs">

        <div class="row">
            <div class="col">
                <select id="delivery_date_m" name="tech" class="tech ca-i_input">
                    <?php foreach($delivery_date as $date) { ?>
                        <?php if( $date['format'] != '30.04.2018' && $date['format'] != '01.05.2018' && $date['format'] != '09.05.2018' ) { ?>
                            <option value="<?php echo $date['format'] ?>"><?php echo $date['text']; ?></option>
                        <?php } ?>
                    <?php } ?>
                </select> 
                <hr class="indent xs">
            </div>

            <div class="col">
                <select id="delivery_time_m" name="tech" class="tech ca-i_input">
                    <?php foreach($delivery_intervals as $interval) { ?>
                    <option value="<?php echo $interval; ?>"><?php echo $interval; ?></option>
                    <?php } ?>
                </select> 
                <hr class="indent xs">
            </div>
        </div>

        <input type="hidden" name="order_id" value="0">
        <input type="hidden" name="payment_method" value="Наличными курьеру">
        <input type="hidden" name="payment_code" value="cod">
        <input type="hidden" name="discount" value="<?php echo $discount; ?>">
        <input type="hidden" name="total" value="<?php echo $total; ?>">
        <input type="hidden" name="deliveryprice" value="-1">
        <input type="hidden" name="deliverydistance" value="-1">
        <hr class="indent sm">

        <div class="cart-first-purchase">
            <p class="xs">Скидка на первую покупку</p>
            <p class="text-color-green" data-type="value"></p>
        </div>

        <div class="cart-shipping-price">
            <p class="xs">Стоимость доставки</p>
            <p class="h4" data-type="value"></p>
        </div>

        <div class="cart-total-price">
            <p class="xs">Итого</p>
            <p class="h4" data-type="value"></p>
        </div>

        <button class="btn justify" type="submit">Рассчитать стоимость доставки</button>
        <hr class="indent xs">

        <div class="privacy text-align-center">
          <div class="form-checkbox" data-checked="true">
            <input class="input" type="checkbox" value="" name="privacy-check" checked="true">
          </div>
          <span for="privacy-check">Даю согласие на обработку персональных данных</span>
        </div>
    </form>
</div>

 <hr class="indent sm">