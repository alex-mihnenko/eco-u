<?php if(!empty($custom_css)): ?>
	<style>
		<?php echo htmlspecialchars_decode($custom_css); ?>
  </style>
<?php endif; ?>

<div class="title"><h3 class="h5 text-upper"> <i class="fa fa-bars"></i> <?php echo $heading_title; ?></h3></div>

<div class="list-group">
  <?php if ($category_id == 0) { ?>
    <a href="/blog" class="list-group-item active"><?php echo $text_all; ?></a>
  <?php } else { ?>
    <a href="/blog" class="list-group-item"><?php echo $text_all; ?></a>
  <?php } ?>

  <?php foreach ($categories as $category) { ?>

    <?php if ($category['category_id'] == $category_id) { ?>
      <a href="<?php echo $category['href']; ?>" class="list-group-item active"><?php echo $category['name']; ?></a>
      <?php if ($category['children']) { ?>
        <?php foreach ($category['children'] as $child) { ?>
          <?php if ($child['category_id'] == $child_id) { ?>
            <a href="<?php echo $child['href']; ?>" class="list-group-item active">&nbsp;&nbsp;&nbsp;- <?php echo $child['name']; ?></a>
          <?php } else { ?>
            <a href="<?php echo $child['href']; ?>" class="list-group-item">&nbsp;&nbsp;&nbsp;- <?php echo $child['name']; ?></a>
          <?php } ?>
        <?php } ?>
      <?php } ?>
    <?php } else { ?>
     <a href="<?php echo $category['href']; ?>" class="list-group-item"><?php echo $category['name']; ?></a>
    <?php } ?>

  <?php } ?>
</div>

<hr class="indent md">
	