<?php echo $header; ?>
<hr class="indent xl">
<hr class="indent xl">
<hr class="indent md">


<div class="container">

    <?php echo $column_left; ?>
  
    <div id="content" class="text-align-center">
      <?php echo $content_top; ?>

      <h1 class="h1"><?php echo $heading_title; ?></h1>
      <hr class="indent sm">

      <?php echo $text_message; ?>
      <hr class="indent md">

      <a href="<?php echo $continue; ?>" class="btn btn-primary"><?php echo $button_continue; ?></a>
    </div>

    <?php echo $content_bottom; ?>

    <?php echo $column_right; ?>
</div>


<hr class="indent xl">
<hr class="indent xl">
<?php echo $footer; ?>