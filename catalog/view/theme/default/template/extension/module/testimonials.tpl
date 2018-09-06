<?php echo $header; ?>

<hr class="indent xl">
<hr class="indent md">

<?php echo $column_left; ?>
<?php echo $content_top; ?>

<div class="container-wrapper" id="testimonials">

    <div class="container">

      <h1 class="h2"><?php echo $heading_title; ?></h1>
      <p><?php echo $sub_heading_title; ?></p>
      <hr class="indent sm">
      
      <?php if( count($testimonials)>0 )  { ?>
        <div class="testimonials-list">
          <?php foreach ( $testimonials as $key => $testimonial) { ?>
            <?php echo $testimonial; ?>
          <?php } ?>
        </div>
        <hr class="indent xs">
        
        <div><?php echo $pagination; ?></div>
        <hr class="indent xxs">
        <div class="pagination-results"><?php echo $results; ?></div>
      <?php } else { ?>
        <p><?php echo $text_empty; ?></p>
      <?php } ?>
    </div>


</div>

<hr class="indent lg">


<?php echo $content_bottom; ?>
<?php echo $column_right; ?>

<?php echo $footer; ?>