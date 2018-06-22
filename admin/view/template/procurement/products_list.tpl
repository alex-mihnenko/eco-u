<?php echo $header; ?>

<?php echo $column_left; ?>

<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
      </div>

      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-sm-6">
            <h2><?php echo $text_document_number; ?> #<?php echo $procurement['procurement_id']; ?></h2>
            <h2 class="label label-default"><?php echo $text_document_date; ?> <?php echo $procurement['date_added']; ?></h2>
            <hr class="indent sm">
          </div>
          
          <div class="col-sm-6">
            <h3><span class="h4"><?php echo $text_total_price; ?></span> <span class="label label-success"><?php echo $total_price; ?> <?php echo $text_price; ?></span></h3>
            <h3><span class="h4"><?php echo $text_total_weight; ?></span> <span class="label label-danger"><?php echo $total_weight; ?> <?php echo $text_weight; ?></span></h3>
            <hr class="indent sm">
          </div>
        </div>


        <div class="well">
          <div class="row">
            
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-date-added"><?php echo $entry_date_added; ?></label>
                <div class="input-group date">
                  <input type="text" name="filter_date_added" value="<?php echo $filter_date_added; ?>" placeholder="<?php echo $entry_date_added; ?>" data-date-format="YYYY-MM-DD" id="input-date-added" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
            </div>

            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-name"><?php echo $entry_supplier; ?></label>
                <input type="text" name="filter_supplier" value="<?php echo $filter_supplier; ?>" placeholder="<?php echo $entry_supplier; ?>" id="input-name" class="form-control" />
              </div>
            </div>

            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-name"><?php echo $entry_name; ?></label>
                <input type="text" name="filter_name" value="<?php echo $filter_name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
              </div>
            </div>
            
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-category"><?php echo $entry_category; ?></label>
                <select name="filter_category" id="input-category" class="form-control">
                  <option value="0"></option>
                  <?php foreach ($categories as $category) { ?>
                    <?php if ($filter_category == $category['category_id']) { ?>
                      <option value="<?php echo $category['category_id']; ?>" selected><?php echo $category['name']; ?></option>
                    <?php } else { ?>
                      <option value="<?php echo $category['category_id']; ?>"><?php echo $category['name']; ?></option>
                    <?php } ?>
                  <?php } ?>
                </select>
              </div>
            </div>

            <div class="col-sm-3">
              <hr class="indent xs">
              <button type="button" id="button-filter" class="btn btn-primary justify"><i class="fa fa-filter"></i> <?php echo $button_filter; ?></button>
            </div>
          </div>
        </div>

        <form action="" method="post" enctype="multipart/form-data" id="form-marketing">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td class="text-left"><?php if ($sort == 'm.name') { ?>
                    <a href="<?php echo $sort_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_name; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_name; ?>"><?php echo $column_name; ?></a>
                    <?php } ?></td>
                  <td class="text-right"><?php echo $column_min; ?></td>
                  <td style="width: 100px;"></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($products) { ?>
                <?php foreach ($products as $product_id => $product) { ?>

                <?php if ($product[purchased]==1) { ?>
                  <tr class="success">
                  <?php } else if ($product[not_purchased]==1) { ?>
                  <tr class="danger">
                  <?php } else { ?>
                  <tr>
                  <?php } ?>

                    <td class="text-left">
                      <h4><?php echo $product['name']; ?></h4>
                      <span class="label label-info"><?php if( !empty($product['supplier']) ) { echo $product['supplier']; } else { echo $text_default; } ?></span>
                    </td>
                    <td class="text-right"><h4><?php echo $product['quantity']; ?> <span class="label label-success"><?php echo $product['weight_class']; ?></span></h4></td>
                    <td style="width: 100px;">
                      <a href="<?php echo $product['view']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-info"><i class="fa fa-eye"></i></a>
                      <a href="<?php echo $product['delete']; ?>" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                    </td>

                  </tr>
                <?php } ?>
                <?php } else { ?>
                <tr>
                  <td class="text-center" colspan="8"><?php echo $text_no_results; ?></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </form>
        <div class="row">
          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right"><?php echo $results; ?></div>
        </div>
      </div>
    </div>
  </div>

<script type="text/javascript"><!--
$('#button-filter').on('click', function() {
	url = 'index.php?route=procurement/products&token=<?php echo $token; ?>';
	
  var filter_date_added = $('input[name=\'filter_date_added\']').val();
  var filter_supplier = $('input[name=\'filter_supplier\']').val();
	var filter_name = $('input[name=\'filter_name\']').val();
  
  if (filter_date_added) { url += '&filter_date_added=' + encodeURIComponent(filter_date_added); }
  if (filter_supplier) { url += '&filter_supplier=' + encodeURIComponent(filter_supplier); }
  if (filter_name) { url += '&filter_name=' + encodeURIComponent(filter_name); }

  var filter_category = $('select[name=\'filter_category\']').val();
	
	if (filter_category) {
		url += '&filter_category=' + filter_category;
	}
	
	location = url;
});
//--></script> 
</div>




<script src="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<link href="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css" type="text/css" rel="stylesheet" media="screen" />

<script type="text/javascript">
  $('.date').datetimepicker({
    pickTime: false
  });
</script>

<?php echo $footer; ?>