<?php if(!empty($custom_css)): ?>
	<style>
		<?php echo htmlspecialchars_decode($custom_css); ?>
  </style>
<?php endif; ?>

<div class="l-p_title"><?php echo $heading_title; ?></div>

<div class="blog">
	<?php if ($featured_posts!== false && $featured=='yes') { ?>
		<div class="featured">

			<?php foreach ($featured_posts as $post) { ?>
				<a href="<?php echo $post['href']; ?>" class="post-featured" data-id="<?php echo $post['post_id']; ?>">
		            <div class="box">
		               <label class="title"><span><?php echo $post['title']; ?></span></label>

		                <?php if ($post['small_image']) { ?>
		                    <div class="image" style="background: url(<?php echo $post['small_image']; ?>) no-repeat center center scroll; -webkit-background-size: cover; -moz-background-size: cover; -o-background-size: cover; background-size: cover;"></div>
		                <?php } ?>
		            </div>
		    	</a>
		    <?php } ?>

		</div>
	<?php } ?>
</div>

<hr class="indent lg">
<hr class="indent lg">