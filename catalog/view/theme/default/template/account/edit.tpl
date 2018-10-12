<?php echo $header; ?>

<!-- Content top -->
<?php echo $content_top; ?>
<!-- END Content top -->

<!-- Container -->
<hr class="indent xl">
<hr class="indent xl">

<div id="account" class="edit">
  <div class="container text-align-center">

    <div class="container-center">
        <h1 class="h3 margin padding no"><?php echo $heading_title; ?></h1>
        <hr class="indent xs">

        <p>
            Управление контактными данными и подпиской
        </p>

        <hr class="indent sm">
    </div>


    <form class="form-personal">
        <div class="f-p_box">
          <input type="text" data-name="customer_firstname" placeholder="Имя" value="<?php echo $customer['firstname']; ?>" class="f-p_input">
        </div>

        <div class="f-p_box">
            <input type="text" data-name="customer_telephone" placeholder="Телефон" value="<?php echo $customer['telephone']; ?>" class="f-p_input" id="phone">
        </div>

        <div class="f-p_box">
          <?php
          $re = '/[0-9]+@eco-u.ru/';
          if(1 === preg_match_all($re, $customer['email'], $matches, PREG_SET_ORDER, 0)) {
          ?>
              <input type="hidden" data-name="customer_email_virtual" value="<?php echo $customer['email']; ?>" class="f-p_input">
              <input type="text" data-name="customer_email" placeholder="EMAIL" value="" class="f-p_input">
          <? } else { ?>
              <input type="text" data-name="customer_email" placeholder="EMAIL" value="<?php echo $customer['email']; ?>" class="f-p_input">
          <? } ?>
        </div>

        <div class="f-p_box2" style="display: none;">
          <?php $lastAddress = count($customer['addresses'])-1;
          foreach($customer['addresses'] as $i => $address) { ?>
          <div class="f-p_address_container" data-index="<?php echo $address['address_id']; ?>">
              <div class="f-p_address_remove <?php if($i == $lastAddress) { ?>last<?php } ?>" data-target="<?php echo $address['address_id']; ?>">&times;</div>
              <input type="text" name="dynamic[]" data-name="customer_address" data-target-id="<?php echo $address['address_id']; ?>" placeholder="Адрес Доставки" value="<?php echo $address['value']; ?>" class="f-p_input">
          </div>
          <?php } ?>
          <div class="f-p_plus"></div>
        </div>

        <div class="f-p_chek">
            <input type="checkbox" id="myId1" name="myName1" <?php if($newsletter) { ?>checked=""<?php } ?>>
            <label for="myId1">
                <span class="pseudo-checkbox"></span>
                <span class="label-text">Я согласен получать информацию о специальных предложениях</span>
            </label>
        </div>
        <hr class="indent md">
        
        <div class="text-align-center">
            <button class="btn account-edit-btn" type="button">Сохранить изменения</button>
        </div>
    </form>
    <hr class="indent md">

    <!-- <div class="social">
        <a href="https://www.instagram.com/ecou_shop/" target="_blank"> <i class="svg" data-src="icon-instagram.svg"><php loadSvg('name', 'icon-instagram.svg'); ?></i> Подписаться на Instagram</a>
        <hr class="indent xs">

        <a href="https://t.me/ecou_shop" target="_blank"> <i class="svg" data-src="icon-telegram.svg"><php loadSvg('name', 'icon-telegram.svg'); ?></i> Подписаться на Telegram</a>
    </div> -->

</div>

<hr class="indent xl">
<hr class="indent xl d-none d-md-block">

<?php echo $content_bottom; ?>

<?php echo $footer; ?> 
