<?php if(!empty($custom_css)): ?>
	<style>
		<?php echo htmlspecialchars_decode($custom_css); ?>
  </style>
<?php endif; ?>

<div class="title"><h3 class="h4 text-upper"> <i class="fa fa-retweet"></i> <?php echo $heading_title; ?></h3></div>
<hr class="indent xs">

<div class="widget">
    <?php if (!empty($posts)) { ?>
		<?php foreach ($posts as $post) { ?>
			<div class="post-widget">
				<a href="<?php echo $post['href']; ?>"<?php echo ($post['post_id'] == $post_id) ? ' class="active"' : ''; ?>><?php echo $post['title']; ?></a>
				<hr class="indent xs">
			</div>
		<?php } ?>
	<?php } else { ?>
			<div class="iblog-noposts"><?php echo $no_posts; ?></div>
	<?php } ?>
</div>

<hr class="indent md">