<?php echo $header; ?>

<?php echo $column_left; ?>

<!-- Content top -->
<?php echo $content_top; ?>
<!-- END Content top -->

<!-- Container -->
<hr class="indent xl">
<hr class="indent xl">

<div id="account" class="testimonials">
    <div class="container-wrapper">
      <div class="container">

        <div class="container-center">
            <h1 class="h3"><?php echo $heading_title; ?></h1>
            <hr class="indent sm">
        </div>

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
              <input type="hidden" name="order_id" value="">
              <input type="hidden" name="customer_id" value="">

              <button type="submit" class="btn btn-primary"><?php echo $button_submit; ?></button>
            </div>
          </div>

        </form>
        <hr class="indent lg">
          
        <!-- <div class="testimonials-list"> <div class="text-align-center"><i class="fa fa-spinner fa-pulse fa-spin"></i></div> </div> -->
        <div class="testimonials-list">
          <?php if( $items) { ?>
            <?php foreach ($items as $key => $item) { ?>
              <?php echo $item; ?>
            <?php } ?>
          <?php } ?>
        </div>
        <hr class="indent xs">

        <form action="index.php" method="post" enctype="multipart/form-data" class="form-horizontal answer" id="form-testimonials-answer">
          <button type="button" class="close" data-action="close"> </button>
          
          <textarea class="form-control bordered justify" name="text" placeholder="<?php echo $entry_testimonials; ?>" required=""></textarea>
          <input type="hidden" name="rating" value="0">
          <input type="hidden" name="parent_id" value="0">

          <hr class="indent xs">
          <div class="grid-row adaptive">
            <div class="grid-col col-50 flex-col">
            </div>

            <div class="grid-col col-50 flex-col">
              <button type="submit" class="btn btn-primary"><?php echo $button_submit; ?></button>
            </div>
          </div>
        </form>

      </div>
    </div>
</div>

<hr class="indent xl">
<hr class="indent xl d-none d-md-block">


<?php echo $content_bottom; ?>

<?php echo $column_right; ?>

<?php echo $footer; ?>