<?php echo $header; ?>

<hr class="indent xl">
<hr class="indent xl">

<?php echo $column_left; ?>
<?php echo $content_top; ?>

<div class="container-wrapper" id="account-testimonials">

    <div class="container">

      <form action="index.php" method="post" enctype="multipart/form-data" class="form-horizontal post">
        
        <textarea class="form-control bordered justify" name="text" placeholder="<?php echo $entry_testimonials; ?>" required=""></textarea>
        <hr class="indent sm">

        <div class="flex flex-row">
          <div class="flex-col flex-col-50">
            <hr class="indent xxs">
            <div class="btn-toggle light">
                <button type="button" data-value="1" class="btn-green active"><i class="svg" data-src="icon-thumb-up.svg"><?php loadSvg('name', 'icon-thumb-up.svg'); ?></i></button>
                <button type="button" data-value="0" class="btn-red"><i class="svg" data-src="icon-thumb-down.svg"><?php loadSvg('name', 'icon-thumb-down.svg'); ?></i></button>

                <input type="hidden" name="good" value="1">
            </div>
          </div>

          <div class="flex-col flex-col-50">
            <button type="submit" class="btn btn-primary btn-sm"><?php echo $button_submit; ?></button>
          </div>
        </div>

      </form>
      <hr class="indent lg">

      <h1 class="h3"><?php echo $heading_title; ?></h1>
      <hr class="indent sm">
        
      <div class="testimonials-list"></div>
      <hr class="indent xs">

      <form action="index.php" method="post" enctype="multipart/form-data" class="form-horizontal answer" id="form-testimonials-answer">
        <button type="button" class="close" data-action="close"> </button>
        
        <textarea class="form-control bordered justify" name="text" placeholder="<?php echo $entry_testimonials; ?>" required=""></textarea>
        <input type="hidden" name="good" value="1">
        <input type="hidden" name="parent_id" value="0">

        <hr class="indent xs">
        <div class="flex flex-row">
          <div class="flex-col flex-col-50">
          </div>

          <div class="flex-col flex-col-50">
            <button type="submit" class="btn btn-primary btn-sm"><?php echo $button_submit; ?></button>
          </div>
        </div>
      </form>
    </div>


</div>

<hr class="indent lg">


<?php echo $content_bottom; ?>
<?php echo $column_right; ?>

<?php echo $footer; ?>