<table id="PostsWrapper<?php echo $store_id; ?>" class="table table-bordered table-hover" width="100%">
      <thead>
        <tr class="table-header">
          <td class="left" width="3%"><strong><?php echo $entry_id; ?></strong></td>
          <td class="left" width="15%"><strong><?php echo $entry_name; ?></strong></td>
          <td class="left" width="8%"><strong><?php echo $entry_actions; ?></strong></td>
        </tr>
      </thead>
  	<?php if (!empty($categories)) { ?>
		<?php $i=0; foreach ($categories as $category) { ?>
        	<tbody>
				<tr>
					<td class="left">
						<?php echo $category['category_id']; ?>
					</td>
					<td class="left">
						<?php echo $category['name']; ?>
					</td>
                    <td class="center actions">
						<a href="<?php echo $url_link->link($modulePath.'/newCategory', 'token='.$token.'&store_id='.$store_id.'&category_id='.$category['category_id'], 'SSL'); ?>" class="btn btn-xs btn-primary editCategory"><i class="fa fa-pencil"></i> <?php echo $entry_edit; ?></a> <a onclick="removeItem('<?php echo $category['category_id']; ?>')" class="btn btn-xs btn-danger"><i class="fa fa-times"></i> <?php echo $entry_remove; ?></a>
					</td>
				</tr>
			</tbody>
        <?php } ?>
	 <?php } else { ?>
		<tr>
			<td class="center" colspan="7"><?php echo $entry_no_categories; ?></td>
		</tr>
	<?php } ?>
    <tfoot><tr><td colspan="10">
    	<br />
    	<div class="row">
          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right"><?php echo $results; ?></div>
        </div>
    </td></tr></tfoot>
</table>
<script>
$(document).ready(function(){
	$('#PostsWrapper<?php echo $store_id; ?> .pagination a').click(function(e){
		e.preventDefault();
		$.ajax({
			url: this.href,
			type: 'get',
			dataType: 'html',
			success: function(data) {				
				$("#PostsWrapper<?php echo $store_id; ?>").html(data);
			}
		});
	 });		 
});
</script>