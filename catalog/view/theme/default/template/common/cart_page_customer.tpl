<div id="form-customer" class="cart-380-">
    <div class="cart-top-line" data-go-to="1"><div style="float: left"><img src="/new_design/img/left-arrow.svg" alt="назад" style="width: 20px; height: 20px; margin-top:-3px"></div> назад</div>
    <div class="hidden o-i_price"></div>
    <div class="o-i_txt4">Доставка</div>
    
    <div class="pad-md">
        <input type="hidden" id="checkout-order-id" value="0">
        <input type="text" placeholder="Имя" value="<?php echo $customer['first_name']; ?>" class="ca-i_input ta-center" id="customer-name">

        <input type="tel" placeholder="Номер телефона" class="ca-i_input ca-i_input-dark ta-center field_phone" id="phone2_m" value="<?php echo $customer['phone']; ?>">

        <div class="delivery-address-m">
            <?php if(isset($delivery_address) && count($delivery_address) > 0) { ?>
            <select id="delivery_address_m" name="tech" class="tech ca-i_input ta-center">
                <?php foreach($delivery_address as $address) { ?>
                <option value="<?php echo $address['address_id']; ?>"><?php echo $address['value']; ?></option>
                <?php } ?>
                <option class="new_address" value="0">Новый адрес доставки</option>
            </select> 
            <?php } else { ?>
            <input type="text" class="ca-i_input ta-center" id="delivery_address_m" value="" placeholder="Новый адрес доставки">
            <?php } ?>
        </div>
        
        <textarea id="delivery_comment_m" class="ca-i_input ca-i_input-dark" placeholder="Комментарий к заказу"></textarea>

        <div class="clearfix">
                <div class="days-select">
                        <select id="delivery_date_m" name="tech" class="tech ca-i_input">
                                <?php foreach($delivery_date as $date) { ?>
                                    <?php if( $date['format'] != '30.04.2018' && $date['format'] != '01.05.2018' && $date['format'] != '09.05.2018' ) { ?>
                                        <option value="<?php echo $date['format'] ?>"><?php echo $date['text']; ?></option>
                                    <?php } ?>
                                <?php } ?>
                        </select> 
                </div>
                <div class="days-select time-select">
                        <select id="delivery_time_m" name="tech" class="tech ca-i_input">
                                <?php foreach($delivery_intervals as $interval) { ?>
                                <option value="<?php echo $interval; ?>"><?php echo $interval; ?></option>
                                <?php } ?>
                        </select> 
                </div>
        </div>
    </div>
    
    <div class="block-delivery-price">
        <div class="cost-delivery">Стоимость доставки</div>
        <div class="c-d_price">330 руб</div>
    </div>

    <div class="shipping-amount">
        <div class="sh-a_txt1">Итого</div>
        <div class="sh-a_price">330 руб</div>
    </div>

    <div class="btn-block">
        <div class="pad-md">
            <span class="c-m_submit">Рассчитать стоимость доставки</span>
        </div>
        <!--div class="pad-md">
            <span class="c-m_submit2" data-go-to="1">Назад</span>
        </div-->
    </div>

</div>
