<div class="m-basket_border">
        <div class="m-basket_title">Сумма заказа <?php echo $total; ?> рублей</div>
        <div class="m-basket_title-message">Скидка <?php echo $discount; ?> рублей. <a href="#" data-ecomodal-target="m-basket-coupon">Введите купон</a><?php if(!$islogged) { ?> или <a href="#" id="btn-cart-auth">авторизируйтесь</a><?php } ?>, чтобы получить дополнительную скидку</div>
        <?php if($error_total) { ?>
        <div class="total-price-error" style="display: block">Сумма заказа меньше 1000 рублей.</div>
        <?php } ?>
</div>
<div class="m-basket_padding">
        <div class="scroll-pane-cart">
                <ul class="list-letter list-modal">
                        <?php foreach($products as $product) { ?>
                        <li>
                                <div class="box-p_o">
                                        <div class="list-modal_close" data-href="<?php echo $product['link_remove']; ?>"></div>
                                        <div class="p-o_thumb">
                                            <img src="<?php echo $product['image']; ?>" alt="">
                                        </div>
                                        <div class="p-o_block">
                                                <div class="p-o_link">
                                                        <a href="#"><?php echo $product['name']; ?></a>
                                                </div>
                                                <div class="clearfix">
                                                        <div class="p-o_select">
                                                            <select data-cart-quantity="<?php echo (int)$product['quantity']; ?>" data-cart-id="<?php echo $product['cart_id']; ?>" data-cart-variant="<?php echo $product['weightVariant']; ?>" name="m_cart[<?php echo $product['cart_id']; ?>]" class="tech change-m-cart-quantity">
                                                                <?php 
                                                                    $quantity = (int)$product['quantity']; 
                                                                    $start = $quantity - 5;
                                                                    $end = $quantity + 5;
                                                                    if($start < 1) $start = 1;
                                                                ?>
                                                                <?php for($i = $start; $i <= $end; $i++) { ?>
                                                                <?php if($i == $quantity) { ?>
                                                                        <option value="<?php echo $i; ?>" selected="selected"><?php echo $i; ?> шт.</option>
                                                                <?php } else { ?>
                                                                        <option value="<?php echo $i; ?>"><?php echo $i; ?> шт.</option>
                                                                <?php } ?>
                                                                <?php } ?>
                                                                </select> 
                                                        </div>
                                                        <div class="p-o_right">
                                                                <div class="p-o_price"><?php echo $product['total']; ?> <span>руб</span></div>
                                                        </div>
                                                </div>
                                        </div>
                                </div>
                        </li>
                        <?php } ?>
                </ul>
        </div>
</div>
<?php if(!$error_total) { ?>
<div class="m_basket_link">
        <a href="#" class="m-checkout-step-2">Оформить заказ</a>
</div>
<?php } ?>