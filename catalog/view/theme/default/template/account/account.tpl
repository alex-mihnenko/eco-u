<?php echo $header; ?>
<!-- Container -->
<script>
    window.bodyClass = 'page_2';
</script>
<section class="fond-white">
        <div class="width-1194 pd-29"> 
                <ul class="liTabs t_wrap t_wrap_1">
                    <li class="t_item t-item_1">
                        <a class="t_link t_link_1 cur" href="#"> <span>ИСТОРИЯ ЗАКАЗОВ</span></a>
                        <div class="t_content">
                                <div class="table-responsive">
                                                <table class="table-history">
                                                        <tr>
                                                                <th>Номер заказа</th>
                                                                <th>Дата</th>
                                                                <th>Статус</th>
                                                                <th>Сумма</th>
                                                        </tr>
                                                        <?php foreach($orders as $order) { ?>
                                                            <tr>
                                                                <td class="t-h_number">№ <?php echo $order['order_id']; ?></td>
                                                                <td class="t-h_width"><?php echo $order['date']; ?></td>
                                                                <td class="t-h_width">
                                                                        <div class="t-h_pay"><?php echo $order['status']; ?></div>
                                                                </td>
                                                                <td>
                                                                        <div class="t-h_price"><?php echo (int)$order['total']; ?> руб.</div>
                                                                        <?php if(in_array($order['status_id'], Array(1, 2))) { ?><a href="#" class="t-h_submit">Оплатить</a><?php } ?>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                        <!--<tr>
                                                                <td class="t-h_number">№ 723847329492</td>
                                                                <td class="t-h_width">26.11.2017</td>
                                                                <td class="t-h_width">
                                                                        <div class="t-h_pay">Оплачен</div>
                                                                </td>
                                                                <td>
                                                                        <div class="t-h_price">149.00 руб.</div>
                                                                        <a href="#" class="t-h_submit">Оплатить</a>
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                                <td>№ 723847329492</td>
                                                                <td>26.11.2017</td>
                                                                <td>
                                                                        <div class="t-h_pay">Оплачен</div>
                                                                </td>
                                                                <td>
                                                                        <div class="t-h_price">149.00 руб.</div>
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                                <td>№ 723847329492</td>
                                                                <td>26.11.2017</td>
                                                                <td>
                                                                        <div class="t-h_canceled">Отменен</div>
                                                                </td>
                                                                <td>
                                                                        <div class="t-h_price">149.00 руб.</div>
                                                                </td>
                                                        </tr>-->
                                                </table>
                                        </div>
                        </div>
                    </li>
                    <li class="t_item t-item_1">
                        <a class="t_link t_link_1" href="#"><span>ЛИЧНЫЕ ДАННЫЕ</span></a>
                        <div class="t_content"> 
                                <div>
                                                <div class="current-discount">
                                                        <div class="c-d_text">Текущая скидка</div>
                                                        <div class="c-d_size">-30%</div>
                                                </div>
                                                <form class="form-personal">
                                                        <div class="f-p_box">
                                                            <input type="text" data-name="customer_firstname" placeholder="Имя" value="<?php echo $customer['firstname']; ?>" class="f-p_input">
                                                        </div>
                                                        <div class="f-p_box">
                                                                <input type="text" data-name="customer_telephone" placeholder="Телефон" value="<?php echo $customer['telephone']; ?>" class="f-p_input" id="phone">
                                                        </div>
                                                        <div class="f-p_box">
                                                                <input type="text" data-name="customer_email" placeholder="EMAIL" value="<?php echo $customer['email']; ?>" class="f-p_input">
                                                        </div>
                                                        <div class="f-p_box2">
                                                                <?php foreach($customer['addresses'] as $address) { ?>
                                                                <input type="text" name="dynamic[]" data-name="customer_address" data-target-id="<?php echo $address['address_id']; ?>" placeholder="Адрес Доставки" value="<?php echo $address['value']; ?>" class="f-p_input">
                                                                <?php } ?>
                                                                <div class="f-p_plus"></div>
                                                        </div>
                                                        <div class="f-p_chek">
                                                                <input type="checkbox" id="myId1" name="myName1" checked="">
                                                            <label for="myId1">
                                                                <span class="pseudo-checkbox"></span>
                                                                <span class="label-text">Я согласен получать информацию о специальных предложениях</span>
                                                            </label>
                                                        </div>
                                                        <div class="clearfix f-p_mobile">
                                                                <span class="f-p_submit">Сохранить изменения</span>
                                                        </div>
                                                </form>
                                        </div>
                        </div>
                    </li>
                </ul>
        </div>
</section>
<!-- END Container  -->
<!-- Favorite Products -->
<section class="fond-f-p">
        <div class="width-1418 clearfix">
                <div class="f-p_title">Любимые продукты</div>
                <div class="f-p_all">Смотреть все</div>
        </div>
        <div class="width-1660">
                <div class="slider-favorite-products">
                        <div>
                                <div class="box-p_o">
                                        <a href="#" class="p-o_thumb">
                                                <img src="pic/photo_2.png" alt="">
                                        </a>
                                        <div class="p-o_block">
                                                <div class="p-o_link">
                                                        <a href="#">Органический фермерский бифидокефир 1% - 500</a>
                                                </div>
                                                <div class="clearfix">
                                                        <div class="p-o_select">
                                                            <select name="tech" class="tech">
                                                                        <option value="">1 шт.</option>
                                                                        <option value="">2 шт.</option>
                                                                        <option value="">5 шт.</option>
                                                                </select> 
                                                        </div>
                                                        <div class="p-o_right">
                                                                <div class="p-o_price">390 <span>руб</span></div>
                                                                <input type="submit" value="" class="p-o_submit">
                                                        </div>
                                                </div>
                                        </div>
                                </div>
                        </div>
                        <div>
                                <div class="box-p_o">
                                        <a href="#" class="p-o_thumb">
                                                <img src="pic/photo_1.png" alt="">
                                        </a>
                                        <div class="p-o_block">
                                                <div class="p-o_discount">-30%</div>
                                                <div class="p-o_link">
                                                        <a href="#">Органический фермерский бифидокефир 1% - 500</a>
                                                </div>
                                                <div class="clearfix">
                                                        <div class="p-o_select">
                                                            <select name="tech" class="tech">
                                                                        <option value="">1 шт.</option>
                                                                        <option value="">2 шт.</option>
                                                                        <option value="">5 шт.</option>
                                                                </select> 
                                                        </div>
                                                        <div class="p-o_right">
                                                                <div class="p-o_price">390 <span>руб</span></div>
                                                                <input type="submit" value="" class="p-o_submit">
                                                        </div>
                                                </div>
                                        </div>
                                </div>
                        </div>
                        <div>
                                <div class="box-p_o">
                                        <a href="#" class="p-o_thumb">
                                                <img src="pic/photo_3.png" alt="">
                                        </a>
                                        <div class="p-o_block">
                                                <div class="p-o_discount">-30%</div>
                                                <div class="p-o_link">
                                                        <a href="#">Органический фермерский бифидокефир 1% - 500</a>
                                                </div>
                                                <div class="not-available clearfix">
                                                        <div class="n-a_text">Нет в наличии</div>
                                                        <div class="n-a_time" title="Товар появится через 2-3 дня" rel="tooltip"></div>
                                                </div>
                                        </div>
                                </div>
                        </div>
                        <div>
                                <div class="box-p_o">
                                        <a href="#" class="p-o_thumb">
                                                <img src="pic/photo_4.png" alt="">
                                        </a>
                                        <div class="p-o_block">
                                                <div class="p-o_link">
                                                        <a href="#">Органический фермерский бифидокефир 1% - 500</a>
                                                </div>
                                                <div class="clearfix">
                                                        <div class="p-o_select">
                                                            <select name="tech" class="tech">
                                                                        <option value="">1 шт.</option>
                                                                        <option value="">2 шт.</option>
                                                                        <option value="">5 шт.</option>
                                                                </select> 
                                                        </div>
                                                        <div class="p-o_right">
                                                                <div class="p-o_price">390 <span>руб</span></div>
                                                                <input type="submit" value="" class="p-o_submit">
                                                        </div>
                                                </div>
                                        </div>
                                </div>
                        </div>
                        <div>
                                <div class="box-p_o">
                                        <a href="#" class="p-o_thumb">
                                                <img src="pic/photo_5.png" alt="">
                                        </a>
                                        <div class="p-o_block">
                                                <div class="p-o_discount">-30%</div>
                                                <div class="p-o_link">
                                                        <a href="#">Органический фермерский бифидокефир 1% - 500</a>
                                                </div>
                                                <div class="clearfix">
                                                        <div class="p-o_select">
                                                            <select name="tech" class="tech">
                                                                        <option value="">1 шт.</option>
                                                                        <option value="">2 шт.</option>
                                                                        <option value="">5 шт.</option>
                                                                </select> 
                                                        </div>
                                                        <div class="p-o_right">
                                                                <div class="p-o_price">390 <span>руб</span></div>
                                                                <input type="submit" value="" class="p-o_submit">
                                                        </div>
                                                </div>
                                        </div>
                                </div>
                        </div>
                        <div>
                                <div class="box-p_o">
                                        <a href="#" class="p-o_thumb">
                                                <img src="pic/photo_2.png" alt="">
                                        </a>
                                        <div class="p-o_block">
                                                <div class="p-o_discount">-30%</div>
                                                <div class="p-o_link">
                                                        <a href="#">Органический фермерский бифидокефир 1% - 500</a>
                                                </div>
                                                <div class="clearfix">
                                                        <div class="p-o_select">
                                                            <select name="tech" class="tech">
                                                                        <option value="">1 шт.</option>
                                                                        <option value="">2 шт.</option>
                                                                        <option value="">5 шт.</option>
                                                                </select> 
                                                        </div>
                                                        <div class="p-o_right">
                                                                <div class="p-o_price">390 <span>руб</span></div>
                                                                <input type="submit" value="" class="p-o_submit">
                                                        </div>
                                                </div>
                                        </div>
                                </div>
                        </div>
                        <div>
                                <div class="box-p_o">
                                        <a href="#" class="p-o_thumb">
                                                <img src="pic/photo_1.png" alt="">
                                        </a>
                                        <div class="p-o_block">
                                                <div class="p-o_discount">-30%</div>
                                                <div class="p-o_link">
                                                        <a href="#">Органический фермерский бифидокефир 1% - 500</a>
                                                </div>
                                                <div class="clearfix">
                                                        <div class="p-o_select">
                                                            <select name="tech" class="tech">
                                                                        <option value="">1 шт.</option>
                                                                        <option value="">2 шт.</option>
                                                                        <option value="">5 шт.</option>
                                                                </select> 
                                                        </div>
                                                        <div class="p-o_right">
                                                                <div class="p-o_price">390 <span>руб</span></div>
                                                                <input type="submit" value="" class="p-o_submit">
                                                        </div>
                                                </div>
                                        </div>
                                </div>
                        </div>
                        <div>
                                <div class="box-p_o">
                                        <a href="#" class="p-o_thumb">
                                                <img src="pic/photo_3.png" alt="">
                                        </a>
                                        <div class="p-o_block">
                                                <div class="p-o_discount">-30%</div>
                                                <div class="p-o_link">
                                                        <a href="#">Органический фермерский бифидокефир 1% - 500</a>
                                                </div>
                                                <div class="clearfix">
                                                        <div class="p-o_select">
                                                            <select name="tech" class="tech">
                                                                        <option value="">1 шт.</option>
                                                                        <option value="">2 шт.</option>
                                                                        <option value="">5 шт.</option>
                                                                </select> 
                                                        </div>
                                                        <div class="p-o_right">
                                                                <div class="p-o_price">390 <span>руб</span></div>
                                                                <input type="submit" value="" class="p-o_submit">
                                                        </div>
                                                </div>
                                        </div>
                                </div>
                        </div>
                        <div>
                                <div class="box-p_o">
                                        <a href="#" class="p-o_thumb">
                                                <img src="pic/photo_4.png" alt="">
                                        </a>
                                        <div class="p-o_block">
                                                <div class="p-o_discount">-30%</div>
                                                <div class="p-o_link">
                                                        <a href="#">Органический фермерский бифидокефир 1% - 500</a>
                                                </div>
                                                <div class="clearfix">
                                                        <div class="p-o_select">
                                                            <select name="tech" class="tech">
                                                                        <option value="">1 шт.</option>
                                                                        <option value="">2 шт.</option>
                                                                        <option value="">5 шт.</option>
                                                                </select> 
                                                        </div>
                                                        <div class="p-o_right">
                                                                <div class="p-o_price">390 <span>руб</span></div>
                                                                <input type="submit" value="" class="p-o_submit">
                                                        </div>
                                                </div>
                                        </div>
                                </div>
                        </div>
                        <div>
                                <div class="box-p_o">
                                        <a href="#" class="p-o_thumb">
                                                <img src="pic/photo_5.png" alt="">
                                        </a>
                                        <div class="p-o_block">
                                                <div class="p-o_discount">-30%</div>
                                                <div class="p-o_link">
                                                        <a href="#">Органический фермерский бифидокефир 1% - 500</a>
                                                </div>
                                                <div class="clearfix">
                                                        <div class="p-o_select">
                                                            <select name="tech" class="tech">
                                                                        <option value="">1 шт.</option>
                                                                        <option value="">2 шт.</option>
                                                                        <option value="">5 шт.</option>
                                                                </select> 
                                                        </div>
                                                        <div class="p-o_right">
                                                                <div class="p-o_price">390 <span>руб</span></div>
                                                                <input type="submit" value="" class="p-o_submit">
                                                        </div>
                                                </div>
                                        </div>
                                </div>
                        </div>
                </div>
                <ul class="list-favorite-products">
                        <li>
                                <div class="box-p_o">
                                        <a href="#" class="p-o_thumb">
                                                <img src="pic/photo_5.png" alt="">
                                        </a>
                                        <div class="p-o_block">
                                                <div class="p-o_discount">-30%</div>
                                                <div class="p-o_link">
                                                        <a href="#">Органический фермерский бифидокефир 1% - 500</a>
                                                </div>
                                                <div class="clearfix">
                                                        <div class="p-o_select">
                                                            <select name="tech" class="tech">
                                                                        <option value="">1 шт.</option>
                                                                        <option value="">2 шт.</option>
                                                                        <option value="">5 шт.</option>
                                                                </select> 
                                                        </div>
                                                        <div class="p-o_right">
                                                                <div class="p-o_price">390 <span>руб</span></div>
                                                                <input type="submit" value="" class="p-o_submit">
                                                        </div>
                                                </div>
                                        </div>
                                </div>
                        </li>
                        <li>
                                <div class="box-p_o">
                                        <a href="#" class="p-o_thumb">
                                                <img src="pic/photo_5.png" alt="">
                                        </a>
                                        <div class="p-o_block">
                                                <div class="p-o_discount">-30%</div>
                                                <div class="p-o_link">
                                                        <a href="#">Органический фермерский бифидокефир 1% - 500</a>
                                                </div>
                                                <div class="clearfix">
                                                        <div class="p-o_select">
                                                            <select name="tech" class="tech">
                                                                        <option value="">1 шт.</option>
                                                                        <option value="">2 шт.</option>
                                                                        <option value="">5 шт.</option>
                                                                </select> 
                                                        </div>
                                                        <div class="p-o_right">
                                                                <div class="p-o_price">390 <span>руб</span></div>
                                                                <input type="submit" value="" class="p-o_submit">
                                                        </div>
                                                </div>
                                        </div>
                                </div>
                        </li>
                        <li>
                                <div class="box-p_o">
                                        <a href="#" class="p-o_thumb">
                                                <img src="pic/photo_5.png" alt="">
                                        </a>
                                        <div class="p-o_block">
                                                <div class="p-o_discount">-30%</div>
                                                <div class="p-o_link">
                                                        <a href="#">Органический фермерский бифидокефир 1% - 500</a>
                                                </div>
                                                <div class="clearfix">
                                                        <div class="p-o_select">
                                                            <select name="tech" class="tech">
                                                                        <option value="">1 шт.</option>
                                                                        <option value="">2 шт.</option>
                                                                        <option value="">5 шт.</option>
                                                                </select> 
                                                        </div>
                                                        <div class="p-o_right">
                                                                <div class="p-o_price">390 <span>руб</span></div>
                                                                <input type="submit" value="" class="p-o_submit">
                                                        </div>
                                                </div>
                                        </div>
                                </div>
                        </li>
                        <li>
                                <div class="box-p_o">
                                        <a href="#" class="p-o_thumb">
                                                <img src="pic/photo_5.png" alt="">
                                        </a>
                                        <div class="p-o_block">
                                                <div class="p-o_discount">-30%</div>
                                                <div class="p-o_link">
                                                        <a href="#">Органический фермерский бифидокефир 1% - 500</a>
                                                </div>
                                                <div class="clearfix">
                                                        <div class="p-o_select">
                                                            <select name="tech" class="tech">
                                                                        <option value="">1 шт.</option>
                                                                        <option value="">2 шт.</option>
                                                                        <option value="">5 шт.</option>
                                                                </select> 
                                                        </div>
                                                        <div class="p-o_right">
                                                                <div class="p-o_price">390 <span>руб</span></div>
                                                                <input type="submit" value="" class="p-o_submit">
                                                        </div>
                                                </div>
                                        </div>
                                </div>
                        </li>
                        <li>
                                <div class="box-p_o">
                                        <a href="#" class="p-o_thumb">
                                                <img src="pic/photo_5.png" alt="">
                                        </a>
                                        <div class="p-o_block">
                                                <div class="p-o_discount">-30%</div>
                                                <div class="p-o_link">
                                                        <a href="#">Органический фермерский бифидокефир 1% - 500</a>
                                                </div>
                                                <div class="clearfix">
                                                        <div class="p-o_select">
                                                            <select name="tech" class="tech">
                                                                        <option value="">1 шт.</option>
                                                                        <option value="">2 шт.</option>
                                                                        <option value="">5 шт.</option>
                                                                </select> 
                                                        </div>
                                                        <div class="p-o_right">
                                                                <div class="p-o_price">390 <span>руб</span></div>
                                                                <input type="submit" value="" class="p-o_submit">
                                                        </div>
                                                </div>
                                        </div>
                                </div>
                        </li>
                        <li>
                                <div class="box-p_o">
                                        <a href="#" class="p-o_thumb">
                                                <img src="pic/photo_5.png" alt="">
                                        </a>
                                        <div class="p-o_block">
                                                <div class="p-o_discount">-30%</div>
                                                <div class="p-o_link">
                                                        <a href="#">Органический фермерский бифидокефир 1% - 500</a>
                                                </div>
                                                <div class="clearfix">
                                                        <div class="p-o_select">
                                                            <select name="tech" class="tech">
                                                                        <option value="">1 шт.</option>
                                                                        <option value="">2 шт.</option>
                                                                        <option value="">5 шт.</option>
                                                                </select> 
                                                        </div>
                                                        <div class="p-o_right">
                                                                <div class="p-o_price">390 <span>руб</span></div>
                                                                <input type="submit" value="" class="p-o_submit">
                                                        </div>
                                                </div>
                                        </div>
                                </div>
                        </li>
                        <li>
                                <div class="box-p_o">
                                        <a href="#" class="p-o_thumb">
                                                <img src="pic/photo_5.png" alt="">
                                        </a>
                                        <div class="p-o_block">
                                                <div class="p-o_discount">-30%</div>
                                                <div class="p-o_link">
                                                        <a href="#">Органический фермерский бифидокефир 1% - 500</a>
                                                </div>
                                                <div class="clearfix">
                                                        <div class="p-o_select">
                                                            <select name="tech" class="tech">
                                                                        <option value="">1 шт.</option>
                                                                        <option value="">2 шт.</option>
                                                                        <option value="">5 шт.</option>
                                                                </select> 
                                                        </div>
                                                        <div class="p-o_right">
                                                                <div class="p-o_price">390 <span>руб</span></div>
                                                                <input type="submit" value="" class="p-o_submit">
                                                        </div>
                                                </div>
                                        </div>
                                </div>
                        </li>
                        <li>
                                <div class="box-p_o">
                                        <a href="#" class="p-o_thumb">
                                                <img src="pic/photo_5.png" alt="">
                                        </a>
                                        <div class="p-o_block">
                                                <div class="p-o_discount">-30%</div>
                                                <div class="p-o_link">
                                                        <a href="#">Органический фермерский бифидокефир 1% - 500</a>
                                                </div>
                                                <div class="clearfix">
                                                        <div class="p-o_select">
                                                            <select name="tech" class="tech">
                                                                        <option value="">1 шт.</option>
                                                                        <option value="">2 шт.</option>
                                                                        <option value="">5 шт.</option>
                                                                </select> 
                                                        </div>
                                                        <div class="p-o_right">
                                                                <div class="p-o_price">390 <span>руб</span></div>
                                                                <input type="submit" value="" class="p-o_submit">
                                                        </div>
                                                </div>
                                        </div>
                                </div>
                        </li>
                        <li>
                                <div class="box-p_o">
                                        <a href="#" class="p-o_thumb">
                                                <img src="pic/photo_5.png" alt="">
                                        </a>
                                        <div class="p-o_block">
                                                <div class="p-o_discount">-30%</div>
                                                <div class="p-o_link">
                                                        <a href="#">Органический фермерский бифидокефир 1% - 500</a>
                                                </div>
                                                <div class="clearfix">
                                                        <div class="p-o_select">
                                                            <select name="tech" class="tech">
                                                                        <option value="">1 шт.</option>
                                                                        <option value="">2 шт.</option>
                                                                        <option value="">5 шт.</option>
                                                                </select> 
                                                        </div>
                                                        <div class="p-o_right">
                                                                <div class="p-o_price">390 <span>руб</span></div>
                                                                <input type="submit" value="" class="p-o_submit">
                                                        </div>
                                                </div>
                                        </div>
                                </div>
                        </li>
                </ul>
        </div>
</section>
<!-- Favorite Products -->
<?php echo $footer; ?> 