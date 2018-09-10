<?php echo $header; ?>

<hr class="indent xl">
<hr class="indent md">

<?php echo $column_left; ?>
<?php echo $content_top; ?>

<div class="container-wrapper" id="account-testimonials">

    <div class="container">

      <h1 class="h2"><?php echo $heading_title; ?></h1>
      <p><?php echo $sub_heading_title_add; ?></p>
      <hr class="indent sm">
      
      <form action="index.php" method="post" enctype="multipart/form-data" class="form-horizontal post">
        
        <textarea class="form-control bordered justify" name="text" placeholder="<?php echo $entry_testimonials; ?>" required=""></textarea>
        <hr class="indent sm">

        <div class="grid-row adaptive">
          <div class="grid-col col-50 flex-col">
            <hr class="indent xxs">
            <div class="btn-toggle rating">
                <button type="button" data-value="1" class="btn-red"><i class="fa fa-star-o"></i></button>
                <button type="button" data-value="2" class="btn-red"><i class="fa fa-star-o"></i></button>
                <button type="button" data-value="3" class="btn-red"><i class="fa fa-star-o"></i></button>
                <button type="button" data-value="4" class="btn-red"><i class="fa fa-star-o"></i></button>
                <button type="button" data-value="5" class="btn-red"><i class="fa fa-star-o"></i></button>

                <input type="text" name="rating" value="" required="">
            </div>
            <hr class="indent xs">
          </div>

          <div class="grid-col col-50 flex-col">
            <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
            <input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>">

            <button type="submit" class="btn btn-primary btn-sm"><?php echo $button_submit; ?></button>
          </div>
        </div>

      </form>

      <hr class="indent lg">

      <h1 class="h3"><?php echo $heading_title; ?></h1>
      <hr class="indent sm">
        
      <div class="testimonials-list"></div>
      <hr class="indent xs">
    </div>


</div>

<hr class="indent lg">


<?php echo $content_bottom; ?>
<?php echo $column_right; ?>

<?php echo $footer; ?>