<!-- Categories products -->
<div class="tabs__block active" id="container-products-categories">
	<div class="button-tabs2 button-alphabetic-shadow" data-remodal-target="modal9">КАТАЛОГ</div>
	<div class="clearfix rel">

		<div id="contentcontainer2">
			<div class="container">

                <?php foreach($categories as $category) {  ?>
                    <div id="l-p_<?php echo $category['id'] ?>">
                        <?php foreach($category['sub'] as $sub_index => $subcategory) { ?>
                            
                            <?php if(!isset($products_catsorted[$category['id']]['sub'][$subcategory['id']])) continue; ?>
                            <?php $lCount = (int)$subcategory['total']; ?>

                            <div id="l-p_<?php echo $category['id'] . ('_' . $sub_index) ?>" class="rel">
                                <?php if(!empty($subcategory['image'])) { ?>
                                    <div class="big-thumb"><img src="/image/<?php echo $subcategory['image']; ?>" alt=""></div>
                                <?php } ?>

                                <div class="l-p_title"><?php echo $subcategory['name']; ?></div>
    							
                                <ul class="list-letter ll-open">
                                    <?php $iCount = 0; ?>
                                    
                                    <?php foreach($products_catsorted[$category['id']]['sub'][$subcategory['id']] as $key => $product) { ?>
                                        <?php if(($product['quantity'] <= 0 && $product['stock_status_id'] == 5) || $product['status'] != 1) { ?>
                                            <?php $lCount--; ?>
                                            <?php continue; ?>
                                        <?php } ?>

                                        <?php if($iCount > 4) break; ?>
                                        <?php $iCount++; ?>

                                        <li data-product="<?php echo $product['product_id']; ?>">
                                            <div id="catsorted_prod_<?php echo $product['product_id']; ?>" itemscope itemtype="http://schema.org/Product" itemprop="itemListElement">
                                                <meta itemprop="position" content="<?php echo $key; ?>" />

                                                <div class="box-p_o">
                                                   <meta content="<?php echo $product['thumb']; ?>" itemprop="image">
                                                    
                                                    <a href="<?php echo $product['href']; ?>" class="p-o_thumb" target="_blank">
                                                       <img <?php if(!empty($product['thumb'])) echo 'src="/new_design/img/spinner.gif" data-src="'.$product['thumb'].'" class="b-lazy"'; else echo 'src="/image/eco_logo.jpg"'; ?> alt="<?php echo $product['name']; ?>">
                                                    </a>

                                                    <div class="p-o_block">
                                                        <?php if(isset($product['composite_price'])) { ?>
                                                            <input type="hidden" class="composite_price" value='<?php echo $product['composite_price']?>'>
                                                        <?php } ?>
                                                        <?php if(isset($product['discount_sticker'])) { ?>
                                                            <div class="p-o_discount sticker_discount">-<?php echo $product['discount_sticker']; ?>%</div>
                                                        <?php } elseif($product['sticker_class']) { ?>
                                                            <div class="p-o_discount sticker_<?php echo $product['sticker_class']; ?>"><span><?php echo $product['sticker_name']; ?></span></div>
                                                        <?php } ?>

                                                        <div class="p-o_link">
                                                            <meta itemprop="name" content="<?php echo $product['name']; ?>">
                                                            <a href="<?php echo $product['href']; ?>" itemprop="url" target="_blank"><?php echo $product['name']; ?></a>
                                                        </div>

                                                        <div class="p-o_short-descr"><?php echo $product['description_short']; ?></div>
                                                            
                                                        <div class="clearfix" itemscope itemtype="http://schema.org/Offer" itemprop="offers">
                                                            <?php if($product['quantity'] > 0 || $product['stock_status_id'] == 7) { ?>
                                                                <div class="p-o_select">
                                                                    <?php if(empty($product['weight_variants'])) { ?>
                                                                        <select name="tech" class="tech">
                                                                            <?php for($i=1; $i<=5; $i++) { ?>
                                                                                <option value="<?php echo $i; ?>"><?php echo $i; ?> <?php echo $product['weight_class']; ?></option>
                                                                            <?php } ?>
                                                                        </select> 
                                                                    <?php } else { ?>
                                                                        <select name="tech" class="tech">
                                                                            <?php  $arVariants = explode(',', $product['weight_variants']); ?>
                                                                            <?php foreach($arVariants as $i => $variant) { ?>
                                                                                <option value="<?php echo $i; ?>"><?php echo trim($variant); ?> <?php echo $product['weight_class']; ?></option>
                                                                            <?php } ?>
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
                                                            <?php } elseif($product['quantity'] <= 0 && $product['stock_status_id'] == 6) { ?>
                                                                <div class="p-o_select">
                                                                    <?php if(empty($product['weight_variants'])) { ?>
                                                                        <select name="tech" class="tech">
                                                                                <?php for($i=1; $i<=5; $i++) { ?>
                                                                                    <option value="<?php echo $i; ?>"><?php echo $i; ?> <?php echo $product['weight_class']; ?></option>
                                                                                <?php } ?>
                                                                        </select> 
                                                                    <?php } else { ?>
                                                                        <select name="tech" class="tech">
                                                                                <?php 
                                                                                $arVariants = explode(',', $product['weight_variants']);
                                                                                foreach($arVariants as $i => $variant) { ?>
                                                                                    <option value="<?php echo $i; ?>"><?php echo trim($variant); ?> <?php echo $product['weight_class']; ?></option>
                                                                                <?php } ?>
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
                                                                    <div class="p-o_submit n-a_time" rel="tooltip" title="<?php echo $product['available_in_time']; ?>"></div>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    <?php } ?>
                                </ul>

                                <div class="show-more sm-lg" data-mode="catsort" data-target="<?php echo $subcategory['id']; ?>"  style="<?php if($lCount <= 5) { ?>visibility:hidden;<?php } ?>">еще <?php echo ($lCount-5); ?> продуктов</div>
                                <div class="show-more sm-md" data-mode="catsort" data-target="<?php echo $subcategory['id']; ?>"  style="<?php if($lCount <= 4) { ?>visibility:hidden;<?php } ?>">еще <?php echo ($lCount-4); ?> продуктов</div>
                                <div class="show-more sm-sm" data-mode="catsort" data-target="<?php echo $subcategory['id']; ?>"  style="<?php if($lCount <= 3) { ?>visibility:hidden;<?php } ?>">еще <?php echo ($lCount-3); ?> продуктов</div>
                                <div class="show-more sm-xs" data-mode="catsort" data-target="<?php echo $subcategory['id']; ?>"  style="<?php if($lCount <= 2) { ?>visibility:hidden;<?php } ?>">еще <?php echo ($lCount-2); ?> продуктов</div>
                            </div>

                        <?php } ?>
                    </div>
                <?php } ?>

			</div> <!-- container -->
		</div> <!-- contentcontainer2 -->

	</div>
</div>
<!-- END Categories products -->