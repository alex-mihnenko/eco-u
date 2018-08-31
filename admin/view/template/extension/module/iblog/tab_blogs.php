<div id="PostsWrapper<?php echo $store['store_id']; ?>"> </div>
<script type="text/javascript">
$(document).ready(function(){
	 $.ajax({
		url: "index.php?route=<?php echo $modulePath; ?>/getPosts&token=<?php echo $token; ?>&page=1&store_id=<?php echo $store['store_id']; ?>",
		type: 'get',
		dataType: 'html',
		success: function(data) {		
			$("#PostsWrapper<?php echo $store['store_id']; ?>").html(data);
		}
	 });
});

	function removePost(id) {
		var r=confirm("Are you sure you want to remove this entry?");
		if (r==true) {
			$.ajax({
				url: 'index.php?route=<?php echo $modulePath; ?>/removePost&token=<?php echo $token; ?>',
				type: 'post',
				data: {'id': id},
				success: function(response) {
				location.reload();
			}
		});
	 }
	}
</script>