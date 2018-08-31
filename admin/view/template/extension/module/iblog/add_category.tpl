<form method="post" enctype="multipart/form-data" id="CategoryForm">
<input type="hidden" name="store_id" value="<?php echo $store_id; ?>" />
<?php if (isset($category_id)) { ?>
<input type="hidden" name="category_id" id="category_id" value="<?php echo $category_id; ?>" />
<?php } ?>
<div class="container-fluid tabbable new_category_form">
	<div class="tab-navigation form-inline">
        <ul id="langs" class="nav nav-tabs" role="tablist">
            <?php foreach ($languages as $language) { ?>
                <li><a href="#language<?php echo $language['language_id']; ?>"><img src="<?php echo $language['flag_url']; ?>" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></a></li>
            <?php } ?>
        </ul>	
	</div>
	<div class="tab-content">
      <?php foreach ($languages as $language) { ?>
		<div class="tab-pane" id="language<?php echo $language['language_id']; ?>">
			<div class="row">
				<div class="col-sm-2">
					<h5><strong><span class="required">* </span><?php echo $entry_title; ?></strong></h5>
					<span class="help"><i class="fa fa-info-circle"></i><?php echo $entry_title_help; ?></span>
				</div>
				<div class="col-sm-10">
					<input type="text" name="category_description[<?php echo $language['language_id']; ?>][name]" size="100" value="<?php echo isset($category_description[$language['language_id']]) ? $category_description[$language['language_id']]['name'] : ''; ?>" class="form-control" />
				</div>
			</div>
			<div class="row">
				<div class="col-sm-2">
					<h5><strong><span class="required">* </span><?php echo $entry_description; ?></strong></h5>
					<span class="help"><i class="fa fa-info-circle"></i><?php echo $entry_description_help; ?></span>
				</div>
				<div class="col-sm-10">
                	<textarea class="summernote" name="category_description[<?php echo $language['language_id']; ?>][description]" id="description<?php echo $language['language_id']; ?>"><?php echo isset($category_description[$language['language_id']]) ? $category_description[$language['language_id']]['description'] : ''; ?></textarea>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-2">
					<h5><strong><span class="required">* </span><?php echo $entry_meta_title; ?></strong></h5>
				</div>
				<div class="col-sm-10">
					<input type="text" name="category_description[<?php echo $language['language_id']; ?>][meta_title]" value="<?php echo isset($category_description[$language['language_id']]) ? $category_description[$language['language_id']]['meta_title'] : ''; ?>" placeholder="<?php echo $entry_meta_title; ?>" id="input-meta-title<?php echo $language['language_id']; ?>" class="form-control" />
					<?php if (isset($error_meta_title[$language['language_id']])) { ?>
						<div class="text-danger"><?php echo $error_meta_title[$language['language_id']]; ?></div>
					<?php } ?>
				</div>
			</div>
			<div class="row">     
				<div class="col-sm-2">
					<h5><strong><?php echo $entry_meta_description; ?></strong></h5>
				</div>
				<div class="col-sm-10">
					<textarea name="category_description[<?php echo $language['language_id']; ?>][meta_description]" rows="5" placeholder="<?php echo $entry_meta_description; ?>" id="input-meta-description<?php echo $language['language_id']; ?>" class="form-control"><?php echo isset($category_description[$language['language_id']]) ? $category_description[$language['language_id']]['meta_description'] : ''; ?></textarea>
				</div>
			</div>
			<div class="row">     
				<div class="col-sm-2">
					<h5><strong><?php echo $entry_meta_keyword; ?></strong></h5>
				</div>
				<div class="col-sm-10">
					<textarea name="category_description[<?php echo $language['language_id']; ?>][meta_keyword]" rows="5" placeholder="<?php echo $entry_meta_keyword; ?>" id="input-meta-keyword<?php echo $language['language_id']; ?>" class="form-control"><?php echo isset($category_description[$language['language_id']]) ? $category_description[$language['language_id']]['meta_keyword'] : ''; ?></textarea>
				</div>
			</div>
		</div>
      <?php } ?>
    <div class="form-group" style="margin:20px auto 40px auto;">
    	<div class="row">
    		<div class="col-sm-2">
				<h5><strong><?php echo $entry_parent; ?></strong></h5>
    			<span class="help"><i class="fa fa-info-circle"></i><?php echo $entry_parent_help; ?></span>
    		</div>
   		 	<div class="col-sm-10">
    			<input type="text" name="path" value="<?php echo $path; ?>" placeholder="<?php echo $entry_parent; ?>" id="input-parent" class="form-control" />
				<input type="hidden" name="parent_id" value="<?php echo $parent_id; ?>" />
    		</div>
    	</div>
    	<div class="row">
    		<div class="col-sm-2">
				<h5><strong><?php echo $entry_keyword; ?></strong></h5>
    			<span class="help"><i class="fa fa-info-circle"></i><?php echo $entry_keyword_help; ?></span>
    		</div>
    		<div class="col-sm-10">
    			<input type="text" name="slug" value="<?php echo $slug; ?>" placeholder="<?php echo $entry_keyword; ?>" id="input-keyword" class="form-control" />
    			<?php if ($error_keyword) { ?>
    				<div class="text-danger"><?php echo $error_keyword; ?></div>
    			<?php } ?>                
    		</div>
    	</div>
    	<div class="row">
    		<div class="col-sm-2">
				<h5><strong><?php echo $entry_image; ?></strong></h5>
    		</div>
                <div class="col-sm-10"><a href="" id="thumb-image" data-toggle="image" class="img-thumbnail"><img src="<?php echo $thumb; ?>" alt="" title="" /></a>
                  <input type="hidden" name="image" value="<?php echo $image; ?>" id="input-image" />
                </div>
    		</div>                
      </div>
  </div>
</form>

<script type="text/javascript" src="view/javascript/summernote/summernote.js"></script>
<link href="view/javascript/summernote/summernote.css" rel="stylesheet" />
<script type="text/javascript" src="view/javascript/summernote/opencart.js"></script>  

<script type="text/javascript"><!--
$('#langs a:first').tab('show'); // Select first tab
$('#langs a').click(function (e) {
  e.preventDefault()
  $(this).tab('show')
})
//--></script> 

<script type="text/javascript"><!--
$('input[name=\'path\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=<?php echo $modulePath; ?>/autocompleteCategory&store_id=<?php echo $store_id; ?>&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				json.unshift({
					category_id: 0,
					name: '<?php echo $text_none; ?>'
				});

				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['category_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'path\']').val(item['label']);
		$('input[name=\'parent_id\']').val(item['value']);
	}
});

$('input[name="category_description[<?php echo $language_id; ?>][name]"]').change(function() {

		var slug = $(this).val();
		
		slug = slug.toLowerCase();
		slug = slug.replace(/[^\w\s-]/gi, '');
		slug = slug.replace(/[\s]+|-|_+/g, "-");
		slug = slug.replace(/-+/g, "-");
		slug = slug.replace(/(^-)|(-$)/g, "");
		
		$('#input-keyword').val(slug);
	
});
//--></script> 
