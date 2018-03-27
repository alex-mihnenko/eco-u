<?php if($page_cart || $has_success) { ?>
<div class="remodal modal-basket" data-remodal-id="modal-basket">
<?php if(0) { ?>
        <button class="remodal-clear">Очистить корзину</button>
<?php } ?>
        <button data-remodal-action="close" class="remodal-close"></button>
        
        <ul class="liTabs_cart t_wrap t_wrap_2">
            <li class="t_item t_item_2 t_item_p1">
                <a class="t_link t_link_2" href="#"></a>
                <div class="t_content"><?php echo $page_cart; ?></div>
            </li>
            <li class="t_item t_item_2 t_item_p2">
                <a class="t_link t_link_2" href="#"></a>
                <div class="t_content"><?php echo $page_customer; ?></div>
            </li>
            <li class="t_item t_item_2 t_item_p3">
                <a class="t_link t_link_2" href="#"></a>
                <div class="t_content"><?php echo $page_payment; ?></div>
            </li>
            <li class="t_item t_item_2 t_item_p4">
                <a class="t_link t_link_2" href="#"></a>
                <div class="t_content"><?php echo $page_success; ?></div>
            </li>
        </ul>
        
</div>
<?php } else { ?>
<div class="remodal modal-basket" data-remodal-id="modal-basket">
        <button data-remodal-action="close" class="remodal-close"></button>
        <div class="m-basket_border">
                <div class="m-basket_title">Ваша корзина пуста</div>
                <div class="m-basket_title-message">Добавьте сюда свежие и полезные продукты</div>
        </div>
        <div class="m-basket_padding">
        </div>
</div>
<?php } ?>























<?php if(0) { ?>
<div id="cart" class="btn-group btn-block">
  <button type="button" data-toggle="dropdown" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-inverse btn-block btn-lg dropdown-toggle"><i class="fa fa-shopping-cart"></i> <span id="cart-total"><?php echo $text_items; ?></span></button>
  <ul class="dropdown-menu pull-right">
    <?php if ($products || $vouchers) { ?>
    <li>
      <table class="table table-striped">
        <?php foreach ($products as $product) { ?>
        <tr>
          <td class="text-center"><?php if ($product['thumb']) { ?>
            <a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-thumbnail" /></a>
            <?php } ?></td>
          <td class="text-left"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
            <?php if ($product['option']) { ?>
            <?php foreach ($product['option'] as $option) { ?>
            <br />
            - <small><?php echo $option['name']; ?> <?php echo $option['value']; ?></small>
            <?php } ?>
            <?php } ?>
            <?php if ($product['recurring']) { ?>
            <br />
            - <small><?php echo $text_recurring; ?> <?php echo $product['recurring']; ?></small>
            <?php } ?></td>
          <td class="text-right">x <?php echo $product['quantity']; ?></td>
          <td class="text-right"><?php echo $product['total']; ?></td>
          <td class="text-center"><button type="button" onclick="cart.remove('<?php echo $product['cart_id']; ?>');" title="<?php echo $button_remove; ?>" class="btn btn-danger btn-xs"><i class="fa fa-times"></i></button></td>
        </tr>
        <?php } ?>
        <?php foreach ($vouchers as $voucher) { ?>
        <tr>
          <td class="text-center"></td>
          <td class="text-left"><?php echo $voucher['description']; ?></td>
          <td class="text-right">x&nbsp;1</td>
          <td class="text-right"><?php echo $voucher['amount']; ?></td>
          <td class="text-center text-danger"><button type="button" onclick="voucher.remove('<?php echo $voucher['key']; ?>');" title="<?php echo $button_remove; ?>" class="btn btn-danger btn-xs"><i class="fa fa-times"></i></button></td>
        </tr>
        <?php } ?>
      </table>
    </li>
    <li>
      <div>
        <table class="table table-bordered">
          <?php foreach ($totals as $total) { ?>
          <tr>
            <td class="text-right"><strong><?php echo $total['title']; ?></strong></td>
            <td class="text-right"><?php echo $total['text']; ?></td>
          </tr>
          <?php } ?>
        </table>
        <p class="text-right"><a href="<?php echo $cart; ?>"><strong><i class="fa fa-shopping-cart"></i> <?php echo $text_cart; ?></strong></a>&nbsp;&nbsp;&nbsp;<a href="<?php echo $checkout; ?>"><strong><i class="fa fa-share"></i> <?php echo $text_checkout; ?></strong></a></p>
      </div>
    </li>
    <?php } else { ?>
    <li>
      <p class="text-center"><?php echo $text_empty; ?></p>
    </li>
    <?php } ?>
  </ul>
</div>
<?php } ?>