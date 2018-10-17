<?php echo $header; ?>

<?php echo $column_left; ?>

jquery.datetimepicker

<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button id="button-send" data-loading-text="<?php echo $text_loading; ?>" data-toggle="tooltip" title="<?php echo $button_send; ?>" class="btn btn-primary" onclick="create();"><i class="fa fa-save"></i></button>
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
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-shopping-cart"></i> <?php echo $heading_title; ?></h3>
      </div>
      <div class="panel-body">
        <form class="form-horizontal">

          <div class="form-group required">
            <div class="col-sm-2">
              <input type="text" name="subject" value="" placeholder="<?php echo $entry_number; ?>" id="input-number" class="form-control" />
            </div>
            <div class="col-sm-2">
              <!-- <div class="input-group date">
                  <input type="text" name="date" value="<?php echo $date; ?>" placeholder="<?php echo $entry_date; ?>" id="input-date-available" class="form-control" />
              </div> -->
              <div class="input-append date form_datetime">
                <input size="16" type="text" readonly value="<?php echo date('d.m.Y H:i', time()); ?>" placeholder="<?php echo $entry_date; ?>" id="input-date-available" class="form-control">
              </div>
               
              <script type="text/javascript">
                  $("#input-date-available").datetimepicker({
                    locale: 'ru',
                    format: "DD.MM.YYYY HH:ii",
                    icons: {
                        time: "fa fa-clock-o",
                        date: "fa fa-calendar",
                        up: "fa fa-arrow-up",
                        down: "fa fa-arrow-down"
                    }
                  });
              </script>   
            </div>
            <div class="col-sm-2">
              <!-- <input type="checkbox" name="subject" value="" placeholder="" id="input-held" class="form-control" checked="true" /><?php echo $entry_held; ?> -->
            </div>
          </div>

          <div class="form-group">
            <div class="col-sm-4">
            	<label class="control-label" for="input-organisation"><span data-toggle="tooltip" title="<?php echo $help_product; ?>"><?php echo $entry_organisation; ?></span></label>
            	
            	<select name="to" id="input-organisation" class="form-control">
            	</select>  
            </div>

            <div class="col-sm-4">
              <label class="control-label" for="input-stock"><span data-toggle="tooltip" title="<?php echo $help_product; ?>"><?php echo $entry_stock; ?></span></label>
            	
            	<select name="to" id="input-stock" class="form-control">
            	</select>  
            </div>

            <div class="col-sm-4">
              <label class="control-label" for="input-project"><span data-toggle="tooltip" title="<?php echo $help_product; ?>"><?php echo $entry_project; ?></span></label>
            	
            	<select name="to" id="input-to" class="form-control">
            	</select>  
            </div>
          </div>

          <div class="form-group to" id="to-product">
            <label class="col-sm-2 control-label" for="input-product"><span data-toggle="tooltip" title="<?php echo $help_product; ?>"><?php echo $entry_product; ?></span></label>
            <div class="col-sm-10">
              <input type="text" name="products" value="" placeholder="<?php echo $entry_product; ?>" id="input-product" class="form-control" />
              <hr class="indent md">
              
              <div class="products">
                <div class="col-md-4"> <label class="control-label">Наименование</label> </div>
                <div class="col-md-1"> <label class="control-label">Количество</label> </div>
                <div class="col-md-1"> <label class="control-label">Остаток</label> </div>
                <div class="col-md-2"> <label class="control-label">Цена</label> </div>
                <div class="col-md-2"> <label class="control-label">Сумма</label> </div>
                <div class="col-md-2"> <label class="control-label">Причина списания</label> </div>
                
                <hr class="indent xs">
                <hr>
                <hr class="indent sm">

                <div class="list">
                </div>
              </div>
            </div>
          </div>

          <div class="form-group">
	            <div class="col-sm-6">
	              <textarea name="message" placeholder="<?php echo $entry_comment; ?>" id="input-comment" class="form-control" style="resize: vertical;" readonly></textarea>
	            </div>
	            <div class="col-sm-6">
	            	<div class="row">
	            		<div class="col-sm-6">
	            			<label class="control-label"><?php echo $text_total; ?></label>
	            		</div>
	            		<div class="col-sm-6">
	            			<label class="control-label" data-marker="loss-total">0,00</label>
	            		</div>
	            	</div>
	            </div>
          </div>



        </form>


      </div>
    </div>
  </div>

</div>


<script type="text/javascript">
  $(document).ready(function(){

    // Init
      load();
    // ---

    // Load
      function load(){
        // ---
          $.post('index.php?route=report/ms_loss/load&token=<?php echo $token; ?>', function(data){
            // ---
              console.log(data.organisation);
              console.log(data.store);

              $.each(data.organisation, function(key, organisation){
                $('#input-organisation').append('<option value="'+JSON.stringify(organisation.meta)+'">'+organisation.name+'</option>');
              });

              $.each(data.store, function(key, store){
                $('#input-stock').append('<option value="'+JSON.stringify(store.meta)+'">'+store.name+'</option>');
              });
            // ---
          }, 'json');
        // ---
      }
    // ---

    // Products
      $('input[name=\'products\']').autocomplete({
        'source': function(request, response) {
          $.ajax({
            url: 'index.php?route=catalog/product/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
            dataType: 'json',
            success: function(json) {
              response($.map(json, function(item) {
                return {
                  label: item['name'],
                  value: item['product_id'],
                  ms_id: item['ms_id'],
                  price: item['price']
                }
              }));
            }
          });
        },
        'select': function(item) {
           $.post('index.php?route=report/ms_loss/product&token=<?php echo $token; ?>', {ms_id:item['ms_id']}, function(data){
            // ---
              console.log(data.product);


              $('input[name=\'products\']').val('');

              $('#input-product' + item['value']).remove();

              $('#input-product').parent().find('.products .list').append(''+
                  '<div class="row product" id="product' + item['value'] + '">'+
                    '<div class="col-md-4"> <i class="fa fa-minus-circle"></i> <label class="control-label">' + item['label'] + '</label> </div>'+
                    '<div class="col-md-1"> <input type="number" name="quantity" value="1" placeholder="" class="form-control input-sm" required=""/> </div>'+
                    '<div class="col-md-1"> <input type="number" name="stock" value="0" placeholder="" class="form-control input-sm" required="" readonly/> </div>'+
                    '<div class="col-md-2"> <input type="number" name="price" value="' + item['price'] + '" placeholder="" class="form-control input-sm" required="" readonly/> </div>'+
                    '<div class="col-md-2"> <input type="number" name="total" value="' + item['price'] + '" placeholder="" class="form-control input-sm" required="" readonly/> </div>'+
                    '<div class="col-md-2"> <input type="text" name="cause" value="" placeholder="" class="form-control input-sm" required="" readonly/> </div>'+
                    '<input type="hidden" name="position" id="' + item['ms_id'] + '" value="' + item['ms_id'] + '" />'+
                  '</div>'+
                  '<hr class="indent xs">'+
              '');

              totalSummary();
            // ---
          }, 'json');

        }
      });

      $('.products .list').delegate('.fa-minus-circle', 'click', function() {
        $(this).parents('.product').remove();
        totalSummary();
      });

      $('.products .list').on('change', '[name="quantity"]', function() {
        var quantity = parseInt($(this).val());
        var price = parseFloat($(this).parents('.product').find('[name="price"]').val());

        $(this).parents('.product').find('[name="total"]').val( quantity*price );

        totalSummary();
      });

      function totalSummary() {
        // ---
          var total = 0;

          $('.products .list').find('.product').each(function(){
            total = total + parseFloat($(this).find('[name="total"]').val());
          });

          $('[data-marker="loss-total"]').text(total);
        // ---
      }
    // ---

   });

  // Submit
    function create(){
      // ---
        var positions = [];

        $('.product').each(function(key, product){
          var item = { quantity:parseFloat($(this).find('[name="quantity"]').val()), ms_id:$(this).find('[name="position"]').val() }
          positions.push( item  );
        });

        if( positions.length > 0 ){
          // ---
            $.post('index.php?route=report/ms_loss/create&token=<?php echo $token; ?>', {positions:positions}, function(data){
              // ---
                console.log(data);
              // ---
            }, 'json');
          // ---
        }

        return false;
      // ---
    }
  // ---
</script>
<?php echo $footer; ?>