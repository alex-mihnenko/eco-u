<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
 <div class="page-header">
    <div class="container-fluid">
      <h1><i class="fa fa-list-alt"></i>&nbsp;<?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
	<?php echo (empty($moduleData['LicensedOn'])) ? base64_decode('ICAgIDxkaXYgY2xhc3M9ImFsZXJ0IGFsZXJ0LWRhbmdlciBmYWRlIGluIj4NCiAgICAgICAgPGJ1dHRvbiB0eXBlPSJidXR0b24iIGNsYXNzPSJjbG9zZSIgZGF0YS1kaXNtaXNzPSJhbGVydCIgYXJpYS1oaWRkZW49InRydWUiPsOXPC9idXR0b24+DQogICAgICAgIDxoND5XYXJuaW5nISBVbmxpY2Vuc2VkIHZlcnNpb24gb2YgdGhlIG1vZHVsZSE8L2g0Pg0KICAgICAgICA8cD5Zb3UgYXJlIHJ1bm5pbmcgYW4gdW5saWNlbnNlZCB2ZXJzaW9uIG9mIHRoaXMgbW9kdWxlISBZb3UgbmVlZCB0byBlbnRlciB5b3VyIGxpY2Vuc2UgY29kZSB0byBlbnN1cmUgcHJvcGVyIGZ1bmN0aW9uaW5nLCBhY2Nlc3MgdG8gc3VwcG9ydCBhbmQgdXBkYXRlcy48L3A+PGRpdiBzdHlsZT0iaGVpZ2h0OjVweDsiPjwvZGl2Pg0KICAgICAgICA8YSBjbGFzcz0iYnRuIGJ0bi1kYW5nZXIiIGhyZWY9ImphdmFzY3JpcHQ6dm9pZCgwKSIgb25jbGljaz0iJCgnYVtocmVmPSNpc2Vuc2Vfc3VwcG9ydF0nKS50cmlnZ2VyKCdjbGljaycpIj5FbnRlciB5b3VyIGxpY2Vuc2UgY29kZTwvYT4NCiAgICA8L2Rpdj4=') : '' ?>
	<?php if ($error_warning) { ?>
		<div class="alert alert-danger autoSlideUp"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>
	<?php } ?>
    <?php if ($success) { ?>
        <div class="alert alert-success autoSlideUp"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <script>$('.autoSlideUp').delay(3000).fadeOut(600, function(){ $(this).show().css({'visibility':'hidden'}); }).slideUp(600);</script>
    <?php } ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-list"></i>&nbsp;<span style="vertical-align:middle;font-weight:bold;"><?php echo $text_settings; ?></span></h3>
            <div class="storeSwitcherWidget">
            	<div class="form-group">
                	<button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-pushpin"></span>&nbsp;<?php echo $store['name']; if($store['store_id'] == 0) echo " <strong>(".$text_default.")</strong>"; ?>&nbsp;<span class="caret"></span><span class="sr-only"><?php echo $text_toggle_dropdown; ?></span></button>
                	<ul class="dropdown-menu" role="menu">
                    	<?php foreach ($stores  as $st) { ?>
                            <li><a href="index.php?route=<?php echo $modulePath; ?>&store_id=<?php echo $st['store_id'];?>&token=<?php echo $token; ?>"><?php echo $st['name']; ?></a></li>
                    	<?php } ?> 
                	</ul>
            	</div>
            </div>
        </div>
        <div class="panel-body">
            <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form"> 
                <input type="hidden" name="store_id" value="<?php echo $store['store_id']; ?>" />
                <div class="tabbable">
                    <div class="tab-navigation form-inline">
                        <ul class="nav nav-tabs mainMenuTabs" id="mainTabs">
							<li><a href="#control_panel" role="tab" data-toggle="tab"><i class="fa fa-power-off"></i>&nbsp;<?php echo $text_control_panel;?></a></li>
                            <li id="blogs_tab"><a href="#blogs" role="tab" data-toggle="tab"><i class="fa fa-file-text"></i>&nbsp;<?php echo $text_blog_posts; ?></a></li>
							<li><a href="#categories" role="tab" data-toggle="tab"><i class="fa fa-folder-open-o"></i>&nbsp;<?php echo $text_blog_categories; ?></a></li>
							<li><a href="#listing" role="tab" data-toggle="tab"><i class="fa fa-indent"></i>&nbsp;<?php echo $text_blog_listing; ?></a></li>
							<li><a href="#posts" role="tab" data-toggle="tab"><i class="fa fa-file-text-o"></i>&nbsp;<?php echo $text_post_view; ?></a></li>
                            <li><a href="#isense_support" role="tab" data-toggle="tab"><i class="fa fa-external-link"></i>&nbsp;<?php echo $text_support; ?></a></li>
                        </ul>
                        <div class="tab-buttons"> 
                        	<a id="addNewPost" href="<?php echo $url->link($modulePath.'/newBlogPost', 'token=' . $token."&store_id=".$store['store_id'], 'SSL'); ?>" class="btn btn-info addNewPost"><i class="fa fa-file-text-o"></i>&nbsp;&nbsp;<?php echo $button_add_post; ?></a>
                            <a href="<?php echo $catalog_url; ?>index.php?route=<?php echo $feedPath; ?>" target="_blank" class="btn btn-default"><i class="fa fa-rss"></i>&nbsp;&nbsp;<?php echo $button_feed; ?></a>&nbsp;&nbsp;&nbsp;&nbsp;
                            <button type="submit" class="btn btn-success save-changes"><i class="fa fa-check"></i>&nbsp;<?php echo $button_save; ?></button>
                            <a onclick="location = '<?php echo $cancel; ?>'" class="btn btn-warning"><i class="fa fa-times"></i>&nbsp;<?php echo $button_cancel; ?></a>
                        </div> 
                    </div><!-- /.tab-navigation --> 
                    <div class="tab-content">
                    	<?php
                        if (!function_exists('modification_vqmod')) {
                        	function modification_vqmod($file) {
                        		if (class_exists('VQMod')) {
                       				return VQMod::modCheck(modification($file), $file);
                        		} else {
                        			return modification($file);
                       			}
                        	}
                        }
						?> 
						<div id="control_panel" class="tab-pane"><?php require_once modification_vqmod(DIR_APPLICATION.'view/template/'.$modulePath.'/tab_controlpanel.php'); ?></div>
						<div id="blogs" class="tab-pane"><?php require_once modification_vqmod(DIR_APPLICATION.'view/template/'.$modulePath.'/tab_blogs.php'); ?></div>
 						<div id="categories" class="tab-pane"><?php require_once modification_vqmod(DIR_APPLICATION.'view/template/'.$modulePath.'/tab_categories.php'); ?></div>
                        <div id="listing" class="tab-pane"><?php require_once modification_vqmod(DIR_APPLICATION.'view/template/'.$modulePath.'/tab_listing.php'); ?></div>
                        <div id="posts" class="tab-pane"><?php require_once modification_vqmod(DIR_APPLICATION.'view/template/'.$modulePath.'/tab_posts.php'); ?></div>
                        <div id="isense_support" class="tab-pane"><?php require_once modification_vqmod(DIR_APPLICATION.'view/template/'.$modulePath.'/tab_support.php'); ?></div>
                    </div> <!-- /.tab-content --> 
                </div><!-- /.tabbable -->
            </form>
        </div> 
    </div>
  </div>
</div>

<div class="modal" id="addPostModal" tabindex="-3" role="dialog" aria-labelledby="addPostModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" style="width: 90% !important;margin: 20px auto;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?php echo $entry_submit; ?></span></button>
        <h4 class="modal-title" id="addPostModalLabel"><?php echo $entry_post_details; ?></h4>
      </div>
      <div class="modal-body" style="overflow:auto;" id="addPostModalBody">
      </div>
      <div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i> <?php echo $entry_close; ?></button>
		<button class="btn btn-primary" id="submitPost"><i class="fa fa-file-text-o"></i>&nbsp;<?php echo $button_submit; ?></button>
      </div>
    </div>
  </div>
</div>


<script type="text/javascript">
$('.tab-buttons').delegate('.addNewPost', 'click', function(e){
	e.preventDefault();
	e.stopPropagation();
	$('#addPostModalBody').load($(this).attr('href'));
	$('#addPostModal').modal();
});	
$('#blogs').delegate('.editPost', 'click', function(e){
	e.preventDefault();
	e.stopPropagation();
	$('#addPostModalBody').load($(this).attr('href'));
	$('#addPostModal').modal();
});
$('.modal-footer').delegate('#submitPost', 'click', function(e){
	e.preventDefault();

  
   <?php if(version_compare(VERSION, '2.2.0.0', "<")) { ?>
     <?php foreach ($languages as $language) { ?>
      var content = $('#body<?php echo $language['language_id']; ?>').html($('#body<?php echo $language['language_id']; ?>').code());
    <?php } ?>
   <?php } ?>

	var z= true;
	if (!z) {
		alert("Please make sure that you have filled in all required fields and then try again.");
	} else {
		$.ajax({
			url: 'index.php?route=<?php echo $modulePath; ?>/updatePost&token=<?php echo $token; ?>',
			type: 'post',
			data: $('#PostForm').serialize(),
			dataType: 'json', 
			success: function(response) {
	
				if (response.error) {
					alert(response.error);
				} else {
					if ($("#post_id").length > 0){
						alert('The blog post was updated successfully!');
					} else {
						alert('The blog post was added successfully!');
					}
					$('#addPostModal').modal('hide');
					location.reload();
				}
			}
		});
	}
});

$('#addPostModal').on('hidden.bs.modal', function (e) {
  $('.bootstrap-datetimepicker-widget').remove();
});
				
$(document).ready(function() {
	if (window.location.hash == "#control_panel") {
		$('a[href="' + window.location.hash + '"]').click(); }
	if (window.location.hash == "#blogs") {
		$('a[href="' + window.location.hash + '"]').click(); }
	if (window.location.hash == "#addNew") {
		$('#addNewPost').click(); }
	$('#iblog li a').on('click', function(e) { 
		if ($(this).attr('tab')=='addNew') {
			$('#addNewPost').click();
		} else {
			$('a[href="#'+$(this).attr('tab')+'"]').tab('show');
		}
	});
});
</script>
<?php echo $footer; ?>