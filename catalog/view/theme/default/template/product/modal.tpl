<script>console.log(<?php echo json_encode($products); ?>);</script>
<?php foreach($products as $product) { ?>
<div class="modal-product">
    <div class="clearfix">
            <input type="hidden" class="product_id" value="<?php echo $product['product_id']; ?>">
            <a href="<?php echo $product['href']; ?>" class="m-product_thumb">
                <img src="/image/<?php if(!empty($product['image'])) echo $product['image']; else echo 'eco_logo.jpg'; ?>" alt="">
            </a>
            <div class="m-product_right">
                    <div class="m-product_link">
                        <a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
                    </div>
                    <div class="m-product_city"><?php echo $product['description_short']; ?></div>
                    <?php if(!empty($product['attribute_groups'])) { ?>
                        <div class="m-product_text"><span style="line-height:2em;">О продукте:</span><br></div>
                        <ul class="m-product_list">
                                <?php
                                foreach($product['attribute_groups'] as $aGroup) { 
                                    if($aGroup['attribute_group_id'] == '7') { 
                                        foreach($aGroup['attribute'] as $attribute) {
                                        ?>
                                            <li>
                                                    <?php echo $attribute['name']; ?>: <?php echo $attribute['text']; ?>
                                            </li>
                                        <?php  
                                        }
                                    } 
                                } 
                                ?>
                        </ul>
                    <?php } ?>
                    <div class="size-0">
                            <div class="m-product_select">
                                <select name="tech" class="tech">
                                        <?php for($i=1; $i<=5; $i++) { ?>
                                            <option value="<?php echo $i; ?>"><?php echo $i; ?> шт.</option>
                                        <? } ?>
                                </select> 
                            </div>
                            <input type="hidden" class="product_price" value="<?php if(!empty($product['special'])) echo (int)$product['special']; else echo (int)$product['price']; ?>">
                            <div class="m-product_price"><?php if(!empty($product['special'])) echo (int)$product['special']; else echo (int)$product['price']; ?> руб</div>
                    </div>
                    <a href="#" class="m-product_submit">Добавить в корзину</a>
            </div>
    </div>
</div>
<?php } ?>