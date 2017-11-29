<section class="fond-profitable_offer" itemscope="" itemtype="http://schema.org/ItemList"> 
        <div class="width-1660">
                <h2 class="p-o_title"><?php echo $heading_title; ?></h2>
                <div class="slider-profitable_offer">
                        <?php foreach($products as $key => $product) { ?>
                        <div itemscope itemtype="http://schema.org/Product" itemprop="itemListElement">
                            <meta itemprop="position" content="<?php echo $key; ?>" />
                            <div class="box-p_o">
                                   <meta content="<?php echo $product['thumb']; ?>" itemprop="image">
                                   <a href="<?php echo $product['href']; ?>" class="p-o_thumb">
                                            <img src="<?php if(!empty($product['thumb'])) echo $product['thumb']; else echo '/image/placeholder.png'; ?>" alt="">
                                    </a>
                                    <div class="p-o_block">
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
                                                                    foreach($arVariants as $variant) { ?>
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
                                                            <div class="n-a_text">Скоро будет</div>
                                                            <div class="n-a_time" rel="tooltip" title="<?php echo $product['stock_status']; ?>"></div>
                                                    </div>
                                                    <?php } ?>
                                            </div>
                                    </div>
                            </div>
                    </div>
                        <? } ?>
                </div>
        </div>
</section>