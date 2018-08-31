<div style="margin-bottom:20px;"><a id="addNewCategory" href="<?php echo $url->link($modulePath.'/newCategory', 'token=' . $token."&store_id=".$store['store_id'], 'SSL'); ?>" class="btn btn-primary"><i class="fa fa-folder-open"></i>&nbsp;&nbsp;<?php echo $entry_add_new_category; ?></a></div>
<div id="CategoriesWrapper<?php echo $store['store_id']; ?>"> </div>

<div class="modal" id="addCategoryModal" tabindex="-3" role="dialog" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg"  style="width: 90% !important;margin: 20px auto;"">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button"  class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?php echo $entry_close; ?></span></button>
        <h4 class="modal-title" id="addCategoryModalLabel"><?php echo $entry_category_details; ?></h4>
      </div>
      <div class="modal-body" style="overflow:auto;" id="addCategoryModalBody">
      </div>
      <div class="modal-footer">
		<button class="btn btn-primary" id="submitCategory"><i class="fa fa-folder-open"></i>&nbsp;<?php echo $entry_add; ?></button>
		<button class="btn" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i><?php echo $entry_edit; ?></button>
      </div>
    </div>
  </div>
</div>


<script type="text/javascript">
$(document).ready(function(){
	 $.ajax({
		url: "index.php?route=<?php echo $modulePath; ?>/getCategories&token=<?php echo $token; ?>&page=1&store_id=<?php echo $store['store_id']; ?>",
		type: 'get',
		dataType: 'html',
		success: function(data) {		
			$("#CategoriesWrapper<?php echo $store['store_id']; ?>").html(data);
		}
	 });
});

	function removeItem(id) {      
		var r=confirm('<?php echo $entry_remove_category_confirm; ?>');
		if (r==true) {
			$.ajax({
				url: 'index.php?route=<?php echo $modulePath; ?>/removeCategories&token=<?php echo $token; ?>',
				type: 'post',
				data: {'id': id},
				success: function(response) {
				location.reload();
			}
		});
	 }
	}
	
	$(document).delegate('#addNewCategory', 'click', function(e){
		e.preventDefault();
		e.stopPropagation();
		$('#addCategoryModalBody').load($(this).attr('href'));
		$('#addCategoryModal').modal();
	});	
	
	$(document).delegate('.editCategory', 'click', function(e){
		e.preventDefault();
		e.stopPropagation();
		$('#addCategoryModalBody').load($(this).attr('href'));
		$('#addCategoryModal').modal();
	});
	
	$('.modal-footer').delegate('#submitCategory', 'click', function(e){
	e.preventDefault();
	
	<?php if(version_compare(VERSION, '2.2.0.0', "<")) { ?>
	<?php foreach ($languages as $language) { ?>
		var content = $('#description<?php echo $language['language_id']; ?>').html($('#description<?php echo $language['language_id']; ?>').code());
	<?php } ?>
	<?php } ?>

	var z= true;
	if (!z) {
		alert('<?php echo $entry_fill_required; ?>');
	} else {
		$.ajax({
			url: 'index.php?route=<?php echo $modulePath; ?>/updateCategory&token=<?php echo $token; ?>',
			type: 'post',
			data: $('#CategoryForm').serialize(),
			dataType: 'json',
			success: function(response) {
				
				if (response.error) {
					alert(response.error);
				} else {
					if ($("#category_id").length > 0){
						alert('<?php echo $entry_update_category_success; ?>');
					} else {
						alert('<?php echo $entry_add_category_success; ?>');
					}
					$('#addCategoryModal').modal('hide');
					location.reload();
				}
				
			}
		});
	}
});

</script>