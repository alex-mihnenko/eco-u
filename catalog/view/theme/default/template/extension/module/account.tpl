<nav class="account">
  <?php if ($logged) { ?>
    <a href="<?php echo $account; ?>" 		class="<?php if( $route == parse_url($account, PHP_URL_PATH)) echo 'active'; ?>">		<i class="fa fa-bars d-block d-sm-none"></i> <span class="d-none d-sm-block"><?php echo $text_account; ?></span> </a>

    <a href="<?php echo $testimonials; ?>" 	class="<?php if( $route == parse_url($testimonials, PHP_URL_PATH)) echo 'active'; ?>">	<i class="fa fa-comments-o d-block d-sm-none"></i> <span class="d-none d-sm-block"><?php echo $text_testimonials; ?></span> </a>

    <a href="<?php echo $edit; ?>" 			class="<?php if( $route == parse_url($edit, PHP_URL_PATH)) echo 'active'; ?>">			<i class="fa fa-user-circle d-block d-sm-none"></i> <span class="d-none d-sm-block"><?php echo $text_edit; ?></span> </a>

    <a href="<?php echo $vegan_card; ?>" 	class="<?php if( $route == parse_url($vegan_card, PHP_URL_PATH)) echo 'active'; ?>">	<i class="fa fa-credit-card-alt d-block d-sm-none"></i> <span class="d-none d-sm-block"><?php echo $text_vegan_card; ?></span> </a>

    <a href="<?php echo $bonus; ?>" 		class="<?php if( $route == parse_url($bonus, PHP_URL_PATH)) echo 'active'; ?>">			<i class="fa fa-envira d-block d-sm-none"></i> <span class="d-none d-sm-block"><?php echo $text_bonus; ?></span> </a>
  <?php } ?>
</nav>
