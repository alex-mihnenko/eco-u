<div class="no-pictures">
<?php  $letter = ''; ?>

<?php foreach($products_tagsorted as $tag) { ?>
    <?php $new_letter = mb_strtoupper(mb_substr($tag, 0, 1)); ?>
    
    <?php if($new_letter != $letter) { ?>
        </div><div class="no-pictures">
        <div class="n-p_title"><?php echo $new_letter; ?></div>
    <?php } ?>

    <div class="n-p_list" data-remodal-target="modal5" data-tag="<?php echo $tag; ?>">
        <?php echo $tag; ?>
    </div>
    
    <?php $letter = $new_letter;?>
<?php } ?>
</div>