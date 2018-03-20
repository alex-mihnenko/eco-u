<div id="form-payment" class="cart-380-">
    <div class="cart-top-line" data-go-to="2"><div style="float: left"><img src="/new_design/img/left-arrow.svg" alt="назад" style="width: 20px; height: 20px; margin-top:-3px"></div> назад</div>
    <div class="o-i_txt4">Как вы хотите оплатить заказ?</div>
    <div class="fill-div"></div>
    
    <div id="cart-loading">
        
        <div class="ajax-loader"></div>
        
    </div>
    
    <div class="pad-md payment-methods">
        <?php foreach($payment_methods as $payment_method) { ?>
        <div class="payment-method" data-payment-method-code="<?php echo $payment_method['code']; ?>">
            <div class="pm-title"><?php echo $payment_method['title']; ?></div>
            <div class="pm-image">
                <img alt="<?php echo $payment_method['title']; ?>" title="<?php echo $payment_method['title']; ?>" src="<?php echo $payment_method['image']; ?>">
            </div>
        </div>
        <?php } ?>
    </div>
    <div class="fill-div"></div>
    <!--div class="pad-md">
        <span class="c-m_submit2" data-go-to="2">Назад</span>
    </div-->
</div>