<?php echo $header; ?>

<?php echo $column_left; ?>

<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-marketing" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
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

    <?php if ($text_success) { ?>
    <div class="alert alert-success"><i class="fa fa-exclamation-circle"></i> <?php echo $text_success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>

    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
      </div>
      <div class="panel-body">

        <?php if ($form == 'add') { ?>
          <div class="well">

            <div class="row">

              <div class="col-sm-4">
                <div class="form-group search-product">
                  <label class="control-label" for="input-name"><?php echo $entry_name; ?></label>
                  <input type="text" name="product_name" value="" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
                </div>
              </div>

            </div>

          </div>
        <?php } ?>


        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-marketing" class="form-horizontal">


          <div class="row">

            <div class="col-xs-12 col-sm-12 col-md-12">
              <img src="<?php echo $product_info['image']; ?>" alt="" title="" data-placeholder="" data-target="image" class="hidden-xs hidden-sm" style="float: left; width: 100px; height: auto; margin: 0px 15px 0px 0px;" />

              <h1 data-target="name"><?php echo $product_info['name']; ?></h1>
              <h2><?php echo $text_weight_title; ?> <span data-target="weight"><?php echo intval($product_info['weight']); ?></span> <?php echo $text_weight; ?></h2>
            </div>
          </div>

          <hr>

          <div class="row">

            <div class="col-xs-6 col-sm-6 col-md-4">
              <label class="control-label" for="input-name"><?php echo $entry_quantity; ?> (<?php echo $text_weight; ?>)</label>
              <hr class="indent xxs">

              <input type="number" name="quantity" value="<?php echo $product_info['quantity']; ?>" placeholder="<?php echo $entry_quantity; ?>" id="input-quantity" class="form-control input-lg" />
              <?php if ($error_quantity) { ?><div class="text-danger"><?php echo $error_quantity; ?></div><?php } ?>
              <hr class="indent sm">
            </div>

            <div class="col-xs-6 col-sm-6 col-md-4">
              <label class="control-label" for="input-name"><?php echo $entry_purchase_price; ?> (<?php echo $text_price; ?>)</label>
              <hr class="indent xxs">

              <input type="number" name="purchase_price" value="<?php echo $product_info['purchase_price']; ?>" placeholder="<?php echo $entry_purchase_price; ?>" id="input-purchase_price" class="form-control input-lg"/>
              <hr class="indent sm">
            </div>

            <div class="col-xs-6 col-sm-6 col-md-4">
              <label class="control-label" for="input-name"><?php echo $entry_total; ?> (<?php echo $text_price; ?>)</label>
              <hr class="indent xxs">

              <input type="number" name="total_price" value="<?php echo ($product_info['total_price']); ?>" placeholder="<?php echo $entry_total; ?>" id="input-total_price" class="form-control input-lg"/>
              <hr class="indent sm">
            </div>

          </div>

          <hr class="indent sm">

          <div class="btn-group" data-toggle="buttons" data-action="purchased">
            <label class="btn btn-success btn-lg" data-value="1">
              <input type="radio" name="options" id="purchased-1" autocomplete="off"> <?php echo $text_purchased; ?>
            </label>
            <label class="btn btn-danger btn-lg" data-value="0">
              <input type="radio" name="options" id="purchased-0" autocomplete="off"> <?php echo $text_not_purchased; ?>
            </label>
          </div>

          <hr>


          <div class="row">

            <div class="col-xs-12 col-sm-6 col-md-4">
              <h2 class="h3"><?php echo $text_supplier; ?></h2>
              <div data-target="supplier">
                <select name="supplier_id" class="form-control">
                  <?php foreach( $suppliers as $key => $supplier ) { ?>
                    <?php if( $supplier['supplier_id'] == $product_info['supplier_id'] ) { ?>
                      <option value="<?php echo $supplier['supplier_id'] ?>" selected><?php echo $supplier['name'] ?></option>
                    <?php } else { ?>
                      <option value="<?php echo $supplier['supplier_id'] ?>"><?php echo $supplier['name'] ?></option>
                    <?php } ?>
                  <?php } ?>
                </select>
              </div>
              <hr class="indent sm">
            </div>

            <div class="col-xs-12 col-sm-6 col-md-4">
              <h2 class="h3"><?php echo $text_manufacturer; ?></h2>
              <div data-target="manufacturer">
                <select name="manufacturer_id" class="form-control">
                  <?php foreach( $manufacturers as $key => $manufacturer ) { ?>
                    <?php if( $manufacturer['manufacturer_id'] == $product_info['manufacturer_id'] ) { ?>
                      <option value="<?php echo $manufacturer['manufacturer_id'] ?>" selected><?php echo $manufacturer['name'] ?></option>
                    <?php } else { ?>
                      <option value="<?php echo $manufacturer['manufacturer_id'] ?>"><?php echo $manufacturer['name'] ?></option>
                    <?php } ?>
                  <?php } ?>
                </select>
              </div>
              <hr class="indent sm">
            </div>

            <div class="col-xs-12 col-sm-12 hidden-md hidden-lg hidden-xl">
              <img src="<?php echo $product_info['image']; ?>" alt="" title="" data-placeholder="" data-target="image" style="width: 100%; height: auto;" />
              <hr class="indent sm">
            </div>

          </div>

          <input type="hidden" name="weight_class_id" value="<?php echo $product_info['weight_class_id']; ?>" id="input-weight_class_id" class="form-control"/>
          <input type="hidden" name="purchased" value="<?php echo $product_info['purchased']; ?>" id="input-purchased" class="form-control"/>
          <input type="hidden" name="not_purchased" value="<?php echo $product_info['not_purchased']; ?>" id="input-not_purchased" class="form-control"/>

          <input type="hidden" name="product_id" value="<?php echo $product_info['product_id']; ?>" id="input-product_id" class="form-control"/>
          <input type="hidden" name="procurement_id" value="<?php echo $procurement_id; ?>" id="input-procurement_id" class="form-control"/>

        </form>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function(){
    // ---

      // Calcilates
        $('input[name="quantity"]').on('change', function(){
          // ---

            var $form = $(this).parents('form');

            var quantity = parseFloat($form.find('input[name="quantity"]').val());
            var purchase_price = parseFloat($form.find('input[name="purchase_price"]').val());
            var total_price = parseFloat($form.find('input[name="total_price"]').val());
            
            if( purchase_price > 0 ) {
              $form.find('input[name="total_price"]').val( quantity*purchase_price );
            }
            else if( total_price > 0 ) {
              $form.find('input[name="purchase_price"]').val( total_price/quantity );
            }
            

          // ---
        });

        $('input[name="purchase_price"]').on('change', function(){
          // ---

            var $form = $(this).parents('form');

            var quantity = parseFloat($form.find('input[name="quantity"]').val());
            var purchase_price = parseFloat($form.find('input[name="purchase_price"]').val());
            var total_price = parseFloat($form.find('input[name="total_price"]').val());
            
            if( quantity > 0 ) {
              $form.find('input[name="total_price"]').val( purchase_price*quantity );
            }
            else if( total_price > 0 ) {
              $form.find('input[name="quantity"]').val( total_price/purchase_price );
            }
            

          // ---
        });

        $('input[name="total_price"]').on('change', function(){
          // ---

            var $form = $(this).parents('form');

            var quantity = parseFloat($form.find('input[name="quantity"]').val());
            var purchase_price = parseFloat($form.find('input[name="purchase_price"]').val());
            var total_price = parseFloat($form.find('input[name="total_price"]').val());
            
            if( quantity > 0 ) {
              $form.find('input[name="purchase_price"]').val( total_price/quantity );
            }
            else if( purchase_price > 0 ) {
              $form.find('input[name="quantity"]').val( total_price/purchase_price );
            }
            

          // ---
        });
      // ---


      // Purchased
        if( $('form').find('input[name="purchased"]').val() == 1 ){
          $('form').find('.btn-group[data-action="purchased"] label[data-value="1"]').addClass('active');
          $('form').find('.btn-group[data-action="purchased"] label[data-value="1"] input').attr('checked');

          $('form').find('.btn-group[data-action="purchased"] label[data-value="0"]').removeClass('active');
          $('form').find('.btn-group[data-action="purchased"] label[data-value="0"] input').removeAttr('checked');
        }

        
        if( $('form').find('input[name="not_purchased"]').val() == 1 ){
          $('form').find('.btn-group[data-action="purchased"] label[data-value="0"]').addClass('active');
          $('form').find('.btn-group[data-action="purchased"] label[data-value="0"] input').attr('checked');

          $('form').find('.btn-group[data-action="purchased"] label[data-value="1"]').removeClass('active');
          $('form').find('.btn-group[data-action="purchased"] label[data-value="1"] input').removeAttr('checked');
        }


        $('.btn-group[data-action="purchased"]').on('click', 'label', function(){
          // ---

            var $form = $(this).parents('form');
            var $this = $(this);
            var value = parseInt($(this).attr('data-value'));    

            if( value == 1 ){
              $form.find('input[name="purchased"]').val( 1 );
              $form.find('input[name="not_purchased"]').val( 0 );
            }
            else{
              $form.find('input[name="purchased"]').val( 0 );
              $form.find('input[name="not_purchased"]').val( 1 );
            }


            $('button[type="submit"]').trigger('click');
          // ---
        });
      // ---

      // Add product
        $('.search-product').on('click', '.dropdown-menu li', function(){
          var $form = $('form');
          
          var $this = $(this);
          var product_id = parseInt($this.attr('data-value'));

          $.post('index.php?route=procurement/products/getProductForAdd&token=<?php echo $token; ?>', {product_id:product_id}, function(data){
            // ---
              
              $form.find('[data-target="name"]').html(data.product.name);
              $form.find('[data-target="weight"]').html(data.product.weight);
              $form.find('[data-target="image"]').attr('src', data.product.image);
              
              $form.find('[data-target="supplier"] select').val(data.product.supplier_id);
              $form.find('[data-target="manufacturer"] select').val(data.product.manufacturer_id);
              console.log(data.product.supplier_id);
              console.log(data.product.manufacturer_id);

              $form.find('input[name="weight_class_id"]').val(data.product.weight_class_id);
              $form.find('input[name="purchased"]').val(0);
              $form.find('input[name="not_purchased"]').val(0);

              $form.find('input[name="quantity"]').val(data.product.quantity);
              $form.find('input[name="purchase_price"]').val(data.product.purchase_price)
              $form.find('input[name="total_price"]').val( data.product.quantity*data.product.purchase_price );
              
              $form.find('input[name="product_id"]').val(data.product.product_id);

              console.log(data);
            // ---
          },'json');
        });
      // ---
    // ---
  });
</script>



<script type="text/javascript">
  $('input[name=\'product_name\']').autocomplete({
    'source': function(request, response) {
      $.ajax({
        url: 'index.php?route=catalog/product/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
        dataType: 'json',
        success: function(json) {
          response($.map(json, function(item) {
            return {
              label: item['name'],
              value: item['product_id']
            }
          }));
        }
      });
    },
    'select': function(item) {
      $('input[name=\'product_name\']').val(item['label']);
    }
  });
</script>
<?php echo $footer; ?>