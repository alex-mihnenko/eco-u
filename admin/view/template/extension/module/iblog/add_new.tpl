<form method="post" enctype="multipart/form-data" id="PostForm">
<input type="hidden" name="store_id" value="<?php echo $store_id; ?>" />
<?php if (isset($post_id)) { ?>
<input type="hidden" name="post_id" id="post_id" value="<?php echo $post_id; ?>" />
<?php } ?>
<div class="container-fluid tabbable">
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
              <div class="col-md-2">
                <h5><strong><span class="required">* </span><?php echo $entry_title; ?></strong></h5>
                <span class="help"><i class="fa fa-info-circle"></i><?php echo $entry_title_blog_help; ?>.</span>
              </div>
              <div class="col-md-5">
                <input type="text" name="post_description[<?php echo $language['language_id']; ?>][title]" size="100" value="<?php echo isset($post_description[$language['language_id']]) ? $post_description[$language['language_id']]['title'] : ''; ?>" class="form-control" />
              </div>
            </div>
            <hr />
            <div class="row">
              <div class="col-md-2">
                <h5><strong><span class="required">* </span><?php echo $entry_excerpt; ?></strong></h5>
                <span class="help"><i class="fa fa-info-circle"></i><?php echo $entry_excerpt_help; ?> </span>
              </div>
              <div class="col-md-5">
                <textarea class="form-control" name="post_description[<?php echo $language['language_id']; ?>][excerpt]" id="excerpt<?php echo $language['language_id']; ?>"  cols="40" rows="5"><?php echo isset($post_description[$language['language_id']]) ? $post_description[$language['language_id']]['excerpt'] : ''; ?></textarea>
              </div>
            </div>
            <hr /> 
            <div class="row">
              <div class="col-md-2">
                <h5><strong><span class="required">* </span><?php echo $entry_body; ?></strong></h5>
                <span class="help"><i class="fa fa-info-circle"></i><?php echo $entry_body_help; ?></span>
              </div>
              <div class="col-md-10">
                <textarea class="summernote" name="post_description[<?php echo $language['language_id']; ?>][body]" id="body<?php echo $language['language_id']; ?>"><?php echo isset($post_description[$language['language_id']]) ? $post_description[$language['language_id']]['body'] : ''; ?></textarea>
              </div>
            </div>
            <hr /> 
            <div class="row">
              <div class="col-md-2">
                <h5><strong><?php echo $entry_meta_description; ?></strong></h5>
                <span class="help"><i class="fa fa-info-circle"></i><?php echo $entry_meta_description; ?></span>
              </div>
              <div class="col-md-5">
                <textarea  class="form-control" name="post_description[<?php echo $language['language_id']; ?>][meta_description]" cols="40" rows="4"><?php echo isset($post_description[$language['language_id']]) ? $post_description[$language['language_id']]['meta_description'] : ''; ?></textarea>
              </div>
            </div>
            <hr /> 
            <div class="row">
              <div class="col-md-2">
                <h5><strong><?php echo $entry_meta_keyword; ?></strong></h5>
                <span class="help"><i class="fa fa-info-circle"></i><?php echo $entry_meta_keyword_help; ?></span>
              </div>
              <div class="col-md-5">
                <textarea  class="form-control" name="post_description[<?php echo $language['language_id']; ?>][meta_keyword]" cols="40" rows="4"><?php echo isset($post_description[$language['language_id']]) ? $post_description[$language['language_id']]['meta_keyword'] : ''; ?></textarea>
              </div>
            </div>
      </div>
      <?php } ?>
  </div>
</div>
<hr />
<div class="container-fluid tabbable">
    <div class="row">
      <div class="col-md-2">
        <h5><strong><?php echo $entry_slug; ?></strong></h5>
        <span class="help"><i class="fa fa-info-circle"></i><?php echo $entry_slug ?></span>
      </div>
      <div class="col-md-5">
        <input type="text" class="form-control" name="slug" id="form-slug" value="<?php echo $slug; ?>" size="100" />
      </div>
    </div>
    <hr />
	<div class="row">
      <div class="col-md-2">
        <h5><strong><?php echo $entry_image; ?></strong></h5>
        <span class="help"><i class="fa fa-info-circle"></i><?php echo $entry_image_help; ?></span>
      </div>
      <div class="col-md-5">
      	<div>
            <a href="" id="thumb-image" data-toggle="image" class="img-thumbnail"><img src="<?php echo $thumb; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a>
            <input type="hidden" name="image" value="<?php echo $image; ?>" id="input-image" />
        </div>
      </div>
    </div>
    <hr />

    <div class="row">
      <div class="col-md-2">
        <h5><strong><?php echo $entry_small_image; ?></strong></h5>
        <span class="help"><i class="fa fa-info-circle"></i><?php echo $entry_small_image_help; ?></span>
      </div>
      <div class="col-md-5">
        <div>
            <a href="" id="thumb_small" data-toggle="image" class="img-thumbnail"><img src="<?php echo $thumb_small; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a>
            <input type="hidden" name="small_image" value="<?php echo $small_image; ?>" id="input-small-image" />
        </div>
      </div>
    </div>
    <hr />
	<div class="row">
      <div class="col-md-2">
        <h5><strong><?php echo $entry_author; ?></strong></h5>
        <span class="help"><i class="fa fa-info-circle"></i><?php echo $entry_author_help; ?></span>
      </div>
      <div class="col-md-5">
        <select class="form-control" name="author_id">
          <?php foreach ($authors as $author) { ?>
          <?php if (isset($author_id) && $author['author_id'] == $author_id) { ?>
          <option value="<?php echo $author['author_id']; ?>" selected="selected"><?php echo $author['name']; ?></option>
          <?php } else { ?>
          <option value="<?php echo $author['author_id']; ?>"><?php echo $author['name']; ?></option>
          <?php } ?>
          <?php } ?>
        </select>
   	  </div>
    </div>
      <hr />
	<div class="row">
      <div class="col-md-2">
        <h5><strong><?php echo $entry_show_author; ?></strong></h5>
        <span class="help"><i class="fa fa-info-circle"></i><?php echo $entry_show_author_help; ?></span>
      </div>
      <div class="col-md-5">
   		<select name="show_author" class="form-control">
          <?php if (!isset($show_author) || $show_author == 1) { ?>
          <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
          <option value="0"><?php echo $text_disabled; ?></option>
          <?php } else { ?>
          <option value="1"><?php echo $text_enabled; ?></option>
          <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
          <?php } ?>
        </select>
   	  </div>
    </div>
     <hr />
	<div class="row">
      <div class="col-md-2">
        <h5><strong><?php echo $entry_category; ?></strong></h5>
        <span class="help"><i class="fa fa-info-circle"></i><?php echo $entry_category_help; ?> </span>
      </div>
      <div class="col-md-5">
          <div class="well well-sm" style="height: 150px; overflow: auto;">
            <?php foreach ($categories as $category) { ?>
            <div class="checkbox">
              <label>
             	<?php if(isset($category_id) && !empty($category_id)) { $category_id_unser = unserialize($category_id); } else { $category_id = array(); } ?>
                <?php if (!empty($category_id_unser) && in_array($category['category_id'], $category_id_unser)) { ?>
                <input type="checkbox" name="category_id[]" value="<?php echo $category['category_id']; ?>" checked="checked" />
                <?php echo $category['name']; ?>
                <?php } else { ?>
                <input type="checkbox" name="category_id[]" value="<?php echo $category['category_id']; ?>" />
                <?php echo $category['name']; ?>
                <?php } ?>
              </label>
            </div>
            <?php } ?>
          </div>
   	  </div>
    </div>
    <hr />
	<div class="row">
      <div class="col-md-2">
        <h5><strong><?php echo $entry_date_published; ?></strong></h5>
        <span class="help"><i class="fa fa-info-circle"></i><?php echo $entry_date_published; ?></span>
      </div>
      <div class="col-md-5">
        <input type="text" name="date_published" iblog="form-datepublished" value="<?php echo $date_published; ?>" size="12" data-date-format="YYYY-MM-DD HH:mm" class="datetime form-control" />
	  </div>
	</div>
    <hr />
	<div class="row">
      <div class="col-md-2">
        <h5><strong><?php echo $entry_status; ?></strong></h5>
        <span class="help"><i class="fa fa-info-circle"></i><?php echo $entry_post_status; ?></span>
      </div>
      <div class="col-md-5">
        <select name="status" class="form-control">
          <?php if ($status) { ?>
          <option value="1" selected="selected"><?php echo $text_published; ?></option>
          <option value="0"><?php echo $text_draft; ?></option>
          <?php } else { ?>
          <option value="1"><?php echo $text_published; ?></option>
          <option value="0" selected="selected"><?php echo $text_draft; ?></option>
          <?php } ?>
        </select>
	  </div>
	</div>
    <hr />
     <div class="row">
      <div class="col-md-2">
        <h5><strong><?php echo $entry_sort_order; ?></strong></h5>
        <span class="help"><i class="fa fa-info-circle"></i><?php echo $entry_sort_order_post_help ?></span>
      </div>
      <div class="col-md-5">
        <input type="text" class="form-control" name="sort_order_post" id="sort_order_post" value="<?php echo $sort_order_post; ?>" size="100" />
      </div>
    </div>
    <hr />
	<div class="row">
      <div class="col-md-2">
        <h5><strong><?php echo $entry_featured; ?></strong></h5>
        <span class="help"><i class="fa fa-info-circle"></i><?php echo $entry_featured_help; ?></span>
      </div>
      <div class="col-md-5">
        <input type="checkbox" name="featured" value="1" <?php echo !empty($featured) ? 'checked="checked" ' : ''; ?>/>
	  </div>
	</div>
</div>
</form>

<script type="text/javascript" src="view/javascript/summernote/summernote.js"></script>
<link href="view/javascript/summernote/summernote.css" rel="stylesheet" />
<script type="text/javascript" src="view/javascript/summernote/opencart.js"></script>  

<script type="text/javascript"><!--

$('.datetime').datetimepicker({
	pickDate: true,
	pickTime: true
});
$('#langs a:first').tab('show'); // Select first tab
$('#langs a').click(function (e) {
  e.preventDefault()
  $(this).tab('show')
})


$('input[name="post_description[<?php echo $language_id; ?>][title]"]').change(function() {

		var slug = $(this).val();
		
		slug = slug.toLowerCase();
		slug = slug.replace(/[^\w\s-]/gi, '');
		slug = slug.replace(/[\s]+|-|_+/g, "-");
		slug = slug.replace(/-+/g, "-");
		slug = slug.replace(/(^-)|(-$)/g, "");
		
		$('#form-slug').val(slug);
	});

//--></script> 
