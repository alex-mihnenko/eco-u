<?php echo $header; ?>

<?php echo $column_left; ?>

<!-- Content top -->
<?php echo $content_top; ?>
<!-- END Content top -->

<!-- Container -->
<hr class="indent xl">
<hr class="indent xl">

<div id="account" class="vegan-card">
    <div class="container-wrapper">
      <div class="container">

        <div class="container-center text-align-center">
            <h1 class="h3"><?php echo $heading_title; ?></h1>
            <hr class="indent xs">

            <p class=""><?php echo $sub_title; ?></p>
        </div>

        <form class="form-personal">
            <div class="card">
                <span>Vegetarian Card</span>

                <hr class="indent md d-none d-sm-block">
                <hr class="indent xs d-block d-sm-none">
                <input type="teext" id="vegan_card" name="vegan_card" value="<?php echo $customer['vegan_card']; ?>" placeholder="Номер карты" class="form-control" />
            </div>
            <hr class="indent md">
            
            <div class="text-align-center">
                <button class="btn account-edit-vegan-card" type="button">Сохранить изменения</button>
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