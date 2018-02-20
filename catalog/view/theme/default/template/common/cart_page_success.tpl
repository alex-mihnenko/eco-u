<div class="cart-380-">
    <div class="cart-top-line"></div>
    <div class="o-i_txt4">Спасибо за заказ</div>
    
    <div class="pad-md">
        <div class="o-i_txt2">
            <div>&nbsp;</div>
            <div>&nbsp;</div>
            <div>Номер вашего заказа <span id="cart-order-id"><?php echo $order_id; ?></span></div>
            <div>в течении 20 минут с вами свяжется оператор.</div>
            <div>&nbsp;</div>
            <div>Телефон отдела по заботе о клиентах</div>
            <div>+7 (495) 108-01-08</div>
        </div>

        <div class="fill-div"></div>
        <span class="c-m_submit2" onclick="document.location='/'">На главную страницу</span>
    </div>
</div>
<?php if($order_id) { ?>
<script type="text/javascript">
    setTimeout(function() {
        $('.modal-basket').addClass('modal-basket-380');
        $('.modal-basket .remodal-close').hide();
        $('.t_item_p4 a').click();
        var inst = $('[data-remodal-id="modal-basket"]').remodal();
        inst.open();
    }, 300);
</script>
<?php } ?>