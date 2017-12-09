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
                                                        <div class="c-d_size"><?php echo $customer_discount; ?>%</div>
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
                <!--<div class="f-p_all">Смотреть все</div>-->
        </div>
        <div class="width-1660">
                <div class="slider-preferable-products">
                        <?php foreach ($pref_products as $product) { 
                            if($product['stock_status_id'] == 5 && $product['quantity'] < 0) continue;
                            ?> 
                            <div>
                                    <div class="box-p_o">
                                        <meta content="<?php echo $product['thumb']; ?>" itemprop="image">
                                        <a href="<?php echo $product['href']; ?>" class="p-o_thumb">
                                                <img src="<?php if(!empty($product['thumb'])) echo $product['thumb']; else echo '/image/eco_logo.jpg'; ?>" alt="">
                                         </a>
                                         <div class="p-o_block">
                                                 <?php if(isset($product['composite_price'])) { ?><input type="hidden" class="composite_price" value='<?php echo $product['composite_price']?>'><? } ?>
                                                 <?php if(isset($product['discount_sticker'])) { ?><div class="p-o_discount sticker_discount">-<?php echo $product['discount_sticker']; ?>%</div>
                                                 <?php } elseif($product['sticker_class']) { ?><div class="p-o_discount sticker_<?php echo $product['sticker_class']; ?>"><?php echo $product['sticker_name']; ?></div><?php } ?>
                                                 <div class="p-o_link">
                                                         <meta itemprop="name" content="<?php echo $product['name']; ?>">
                                                         <a href="<?php echo $product['href']; ?>" itemprop="url"><?php echo $product['name']; ?></a> 
                                                         <?php if($is_admin) {?><a target="_blank" href="<?php echo $product['edit_link']; ?>" class="btn btn-default admin-product-edit"><i class="fa fa-edit"></i></a><?php } ?>
                                                 </div>
                                                 <div class="p-o_short-descr"><?php echo $product['description_short']; ?></div>
                                                 <div class="clearfix" itemscope itemtype="http://schema.org/Offer" itemprop="offers">
                                                         <?php if($product['quantity'] > 0 || $product['stock_status_id'] == 7) { ?>
                                                         <div class="p-o_select">
                                                             <?php if(empty($product['weight_variants'])) { ?>
                                                                 <select name="tech" class="tech">
                                                                         <?php for($i=1; $i<=5; $i++) { ?>
                                                                             <option value="<?php echo $i; ?>"><?php echo $i; ?> шт.</option>
                                                                         <? } ?>
                                                                 </select> 
                                                             <?php } else { ?>
                                                                 <select name="tech" class="tech">
                                                                         <?php 
                                                                         $arVariants = explode(',', $product['weight_variants']);
                                                                         foreach($arVariants as $i => $variant) { ?>
                                                                             <option value="<?php echo $i; ?>"><?php echo trim($variant); ?> <?php echo $product['weight_class']; ?></option>
                                                                         <? } ?>
                                                                 </select> 
                                                             <?php } ?>
                                                         </div>
                                                         <div class="p-o_right">
                                                                 <meta itemprop="price" content="<?php echo intval($product['price']); ?>" />
                                                                 <meta itemprop="priceCurrency" content="RUB" />
                                                                 <?php if(empty($product['weight_variants'])) { ?>
                                                                     <div class="p-o_price"><?php if($product['price'] > 999) echo (int)$product['price'].' р'; else echo $product['price']; ?></div>
                                                                 <?php } else { ?>
                                                                     <div class="p-o_price"><?php $tp = (int)((float)trim($arVariants[0])*(float)$product['price']); echo $tp; ?> <?php if($tp > 999) echo ' р'; else echo ' руб'; ?></div>
                                                                 <?php } ?>
                                                                 <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                                                 <input type="submit" value="" class="p-o_submit">
                                                         </div>
                                                         <?php } elseif($product['quantity'] < 0 && $product['stock_status_id'] == 6) { ?>
                                                         <div class="not-available clearfix">
                                                                <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                                                <?php if(empty($product['weight_variants'])) { ?>
                                                                   <input type="hidden" name="quantity" value="1">
                                                                <?php } else {
                                                                   $arVariants = explode(',', $product['weight_variants']); 
                                                                   $quantity = $arVariants[0]; ?>
                                                                       <input type="hidden" name="quantity" value="<?php echo $quantity; ?>">
                                                                <?php } ?>
                                                                <input type="hidden" name="weight_class" value="<?php echo $product['weight_class']; ?>">
                                                                <div class="n-a_text">Скоро будет</div>
                                                                <div class="n-a_time" rel="tooltip" title="<?php echo $product['stock_status']; ?>"></div>
                                                        </div>
                                                         <?php } ?>
                                                 </div>
                                         </div>
                                 </div>
                            </div>
                        <?php } ?>
                </div>
        </div>
</section>
<!-- Favorite Products -->
<?php echo $footer; ?> 