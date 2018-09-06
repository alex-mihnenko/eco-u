<nav class="account">
  <?php if ($logged) { ?>
    <a href="<?php echo $account; ?>" 		class="<?php if( $route == parse_url($account, PHP_URL_PATH)) echo 'active'; ?>"><?php echo $text_account; ?></a>

    <a href="<?php echo $testimonials; ?>" 	class="<?php if( $route == parse_url($testimonials, PHP_URL_PATH)) echo 'active'; ?>"><?php echo $text_testimonials; ?></a>

    <a href="<?php echo $edit; ?>" 			class="<?php if( $route == parse_url($edit, PHP_URL_PATH)) echo 'active'; ?>"><?php echo $text_edit; ?></a>
  <?php } ?>
</nav>
