<?php echo $header; ?>
<script>
    window.bodyClass = 'page_2';
</script>
<!-- Container -->
<section class="fond-white">
        <div class=""> 
                <ul class="liTabs_2 t_wrap t_wrap_2">
                    <li class="t_item t_item_2">
                        <a class="t_link t_link_2 cur" href="#">
                                <span class="t-l_round"></span>
                                <span class="t-l_txt">Ошибка</span>
                        </a>
                        <div class="t_content">
                                <div class="padding-mobile">
                                        <div class="b-order_accepted">
                                                    <div class="o-a_check">
                                                        <div class="o-a_text chackout_failure">Ошибка оплаты заказа</div>
                                                    </div>
                                                    <div class="o-a_number"><?php echo $text_message; ?></div>	
                                                </div>
                                        </div>
                        </div>
                    </li>
                </ul>
        </div>

</section>
<!-- END Container  -->
<!-- Favorite Products -->
<section class="fond-f-p">
        <div class="width-1418">
                <div class="f-p_title">Только сейчас</div>
        </div>
        <div class="width-1660">
                <div class="slider-favorite-products">
                        <?php foreach ($spec_products as $product) { 
                            if($product['stock_status_id'] == 5 && $product['quantity'] <= 0) continue;
                        ?> 
                            <div>
                                    <div class="box-p_o">
                                        <meta content="<?php echo $product['thumb']; ?>" itemprop="image">
                                        <a href="<?php echo $product['href']; ?>" class="p-o_thumb" target="_blank">
                                                <img src="<?php if(!empty($product['thumb'])) echo $product['thumb']; else echo '/image/eco_logo.jpg'; ?>" alt="">
                                         </a>
                                         <div class="p-o_block">
                                                 <?php if(isset($product['composite_price'])) { ?><input type="hidden" class="composite_price" value='<?php echo $product['composite_price']?>'><? } ?>
                                                 <?php if(isset($product['discount_sticker'])) { ?><div class="p-o_discount sticker_discount">-<?php echo $product['discount_sticker']; ?>%</div>
                                                                                                                 <?php } elseif($product['sticker_class']) { ?><div class="p-o_discount sticker_<?php echo $product['sticker_class']; ?>"><span><?php echo $product['sticker_name']; ?></span></div><?php } ?>
                                                 <div class="p-o_link">
                                                         <meta itemprop="name" content="<?php echo $product['name']; ?>">
                                                         <a href="<?php echo $product['href']; ?>" itemprop="url" target="_blank"><?php echo $product['name']; ?></a> 
                                                         <?php if($is_admin) {?><a target="_blank" href="<?php echo $product['edit_link']; ?>" class="btn btn-default admin-product-edit"><i class="fa fa-edit"></i></a><?php } ?>
                                                 </div>
                                                 <div class="p-o_short-descr"><?php echo $product['description_short']; ?></div>
                                                 <div class="clearfix" itemscope itemtype="http://schema.org/Offer" itemprop="offers">
                                                         <?php if($product['quantity'] > 0 || $product['stock_status_id'] == 7) { ?>
                                                         <div class="p-o_select">
                                                             <?php if(empty($product['weight_variants'])) { ?>
                                                                 <select name="tech" class="tech">
                                                                         <?php for($i=1; $i<=5; $i++) { ?>
                                                                             <option value="<?php echo $i; ?>"><?php echo $i; ?> <?php echo $product['weight_class']; ?></option>
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
                                                                     <div class="p-o_price"><?php if($product['price'] > 999) echo (int)$product['price'].' р'; else echo (int)$product['price']; ?> руб</div>
                                                                 <?php } else { ?>
                                                                     <div class="p-o_price"><?php $tp = (int)((float)trim($arVariants[0])*(float)$product['price']); echo $tp; ?> <?php if($tp > 999) echo ' р'; else echo ' руб'; ?></div>
                                                                 <?php } ?>
                                                                 <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                                                 <input type="submit" value="" class="p-o_submit">
                                                         </div>
                                                         <?php } elseif($product['quantity'] <= 0 && $product['stock_status_id'] == 6) { ?>
                                                         <div class="p-o_select">
                                                            <?php if(empty($product['weight_variants'])) { ?>
                                                                <select name="tech" class="tech">
                                                                        <?php for($i=1; $i<=5; $i++) { ?>
                                                                            <option value="<?php echo $i; ?>"><?php echo $i; ?> <?php echo $product['weight_class']; ?></option>
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
                                                                    <div class="p-o_price"><?php if($product['price'] > 999) echo (int)$product['price'].' р'; else echo (int)$product['price']; ?> руб</div>
                                                                <?php } else { ?>
                                                                    <div class="p-o_price"><?php $tp = (int)((float)trim($arVariants[0])*(float)$product['price']); echo $tp; ?> <?php if($tp > 999) echo ' р'; else echo ' руб'; ?></div>
                                                                <?php } ?>
                                                                <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                                                <div class="p-o_submit n-a_time" rel="tooltip" title="<?php echo $product['available_in_time']; ?>"></div>
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
