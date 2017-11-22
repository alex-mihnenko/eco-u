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
                                                <div class="p-o_link">
                                                        <meta itemprop="name" content="<?php echo $product['name']; ?>">
                                                        <a href="<?php echo $product['href']; ?>" itemprop="url"><?php echo $product['name']; ?></a>
                                                </div>
                                                <div class="clearfix" itemscope itemtype="http://schema.org/Offer" itemprop="offers">
                                                        <?php if($product['stock_status_id'] == 7) { ?>
                                                        <div class="p-o_select">
                                                            <select name="tech" class="tech">
                                                                        <?php for($i=1; $i<=5; $i++) { ?>
                                                                            <option value="<?php echo $i; ?>"><?php echo $i; ?> шт.</option>
                                                                        <? } ?>
                                                                </select> 
                                                        </div>
                                                        <div class="p-o_right">
                                                                <meta itemprop="price" content="<?php echo intval($product['price']); ?>" />
                                                                <meta itemprop="priceCurrency" content="RUB" />
                                                                <div class="p-o_price"><?php echo $product['price']; ?></div>
                                                                <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                                                <input type="submit" value="" class="p-o_submit">
                                                        </div>
                                                        <?php } else { ?>
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