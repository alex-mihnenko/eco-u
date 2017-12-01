<?php echo $header; ?>

<script>
    window.bodyClass = 'page_2';
</script>

<!-- Container -->
<section class="fond-white">
        <div class="width-1194 pd-29">
                <ul class="breadcrumbs clearfix" itemscope itemtype="http://schema.org/BreadcrumbList">
                        <?php foreach($breadcrumbs as $i => $item) { 
                            if($i < count($breadcrumbs) - 1) { ?>
                                <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                                        <a href="<?php echo $item['href']; ?>" itemprop="item">
                                                <span itemprop="name"><?php echo $item['text']; ?></span>
                                        </a>
                                </li>
                            <? } else { ?>
                                <li itemprop="itemListElement">
                                        <span itemprop="name"><?php echo $item['text']; ?></span>
                                </li>
                            <?php } ?>
                        <?php } ?>
                </ul>
                <div class="card-product clearfix" itemscope itemtype="http://schema.org/Product">
                        <div class="c-p_left">
                                <div class="c-p_thumb">
                                    <img src="<?php echo $popup; ?>" alt="" itemprop="image">
                                    <?php if(isset($discount_sticker)) { ?><div class="c-p_discount sticker_discount">-<?php echo $discount_sticker; ?>%</div>
                                    <?php } elseif($sticker_class) { ?><div class="c-p_discount sticker_<?php echo $sticker_class; ?>"><?php echo $sticker_name; ?></div><?php } ?>
                                </div>
                                <h1 class="c-p_title c-p_title-mobile" itemprop="name"><?php echo $heading_title; ?></h1>
                                <ul class="list-composition">
                                        <?php
                                        foreach($attribute_groups as $aGroup) { 
                                            if($aGroup['attribute_group_id'] == '7') { 
                                                foreach($aGroup['attribute'] as $attribute) {
                                                ?>
                                                    <li>
                                                            <div class="l-c_left"><?php echo $attribute['name']; ?></div>
                                                            <div class="l-c_right"><?php echo $attribute['text']; ?></div>
                                                    </li>
                                                <?php  
                                                }
                                            } 
                                        } 
                                        ?>
                                </ul>
                        </div>
                        <div class="c-p_right">
                                <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                <h1 class="c-p_title" itemprop="name"><?php echo $heading_title; ?></h1>
                                <div class="c-p_city"><?php echo $description_short; ?></div>
                                <div class="c-p_txt">
                                    О продукте:
                                </div>
                                <ul class="c-p_list" itemprop="description">
                                        <?php foreach($props3 as $prop) { 
                                            if(!empty($prop)) { ?>
                                            <li><?php echo $prop; ?></li>
                                            <?php }
                                        } ?>
                                </ul>
                                <div class="box-a_d">
                                        <a href="#anchor-details" class="anchor-details">Подробнее</a>
                                </div>
                                <div class="size-0" itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
                                        <meta itemprop="price" content="<?php if($special) echo (int)$special; else echo (int)$price; ?>">
                                        <meta itemprop="priceCurrency" content="RUB">
                                        <div class="c-p_select">
                                            <select name="tech" class="tech">
                                                    <?php for($i=1;$i<=5;$i++) { ?>
                                                    <option value="<?php echo $i; ?>"><?php echo $i; ?> шт. </option>
                                                    <?php } ?>
                                                </select> 
                                        </div>
                                        <div class="c-p_price"><?php if($special) echo $special; else echo $price; ?></div>
                                </div>
                                <a href="#" class="c-p_submit">Добавить в корзину</a>
                        </div>
                </div>
        </div>
</section>
<!-- END Container  -->
<!-- Together with this product is often bought -->
<?php if(count($products) > 0) { ?>
<section class="fond-profitable_offer"> 
        <div class="width-1660">
                <div class="p-o_title"><?php echo $text_related; ?></div>
                <div class="slider-profitable_offer">
                    <?php foreach ($products as $product) { ?> 
                        <div>
                                <div class="box-p_o">
                                        <a href="<?php echo $product['href'];?>" class="p-o_thumb">
                                            <img src="<?php echo $product['thumb'];?>" alt="">
                                        </a>
                                        <div class="p-o_block">
                                                <div class="p-o_link">
                                                        <a href="<?php echo $product['href'];?>"><?php echo $product['name'];?></a>
                                                </div>
                                                <div class="clearfix">
                                                        <div class="p-o_select">
                                                            <select name="tech" class="tech">
                                                                        <?php for($i=1;$i<=5;$i++) { ?>
                                                                        <option value="<?php echo $i; ?>"><?php echo $i; ?> шт.</option>
                                                                        <? } ?>
                                                                </select> 
                                                        </div>
                                                        <div class="p-o_right">
                                                                <div class="p-o_price"><?php if($product['special']) echo $product['special']; else echo $product['price']; ?></div>
                                                                <input type="submit" value="" class="p-o_submit">
                                                        </div>
                                                </div>
                                        </div>
                                </div>
                        </div>
                    <?php } ?>
                </div>
        </div>
</section>
<? } ?>
<!-- END Together with this product is often bought -->
<!-- About the product -->
<section id="anchor-details" class="fond-box_1">
        <div class="width-1194 about-product">
                <div class="a-p_title"><?php echo $heading_title; ?></div>
                <p>
                    <?php echo $description; ?>
                </p>
        </div>
</section>
<!-- END About the product -->

<?php echo $footer; ?>
