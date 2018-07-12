<?php echo $header; ?>

<style type="text/css">
  .config-item { margin-bottom: 15px; }
</style>

<?php echo $column_left; ?>


<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-flat" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-flat" class="form-horizontal">
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-cost"><?php echo $entry_cost; ?></label>
            <div class="col-sm-10">
              <input type="text" name="mkadout_cost" value="<?php echo $mkadout_cost; ?>" placeholder="<?php echo $entry_cost; ?>" id="input-cost" class="form-control" />
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-netcost"><?php echo $entry_netcost; ?></label>
            <div class="col-sm-10">
              <input type="hidden" name="mkadout_netcost" value="<?php echo $mkadout_netcost; ?>" placeholder="<?php echo $entry_netcost; ?>" id="input-netcost" class="form-control" />

              <br>
              <div class="netcost-config-list">
                <div class="config-items">
                  <?php $netcost_config_list = json_decode( html_entity_decode($mkadout_netcost, ENT_QUOTES, 'UTF-8') ); ?>

                  <?php foreach($netcost_config_list as $key => $item) { ?>
                    <div class="row config-item">
                      <div class="col-sm-3">
                        <div class="input-group">
                          <span class="input-group-addon" id="basic-addon1"><?php echo $text_from; ?></span>
                          <input type="number" name="from" value="<?php echo $item->from ?>" class="form-control" />
                        </div>
                      </div>
                      
                      <div class="col-sm-3">
                        <div class="input-group">
                          <span class="input-group-addon" id="basic-addon1"><?php echo $text_to; ?></span>
                          <input type="number" name="to" value="<?php echo $item->to ?>" class="form-control" />
                        </div>
                      </div>
                      
                      <div class="col-sm-4">
                        <div class="input-group">
                          <span class="input-group-addon" id="basic-addon1"><?php echo $text_cost; ?></span>
                          <input type="number" name="cost" value="<?php echo $item->cost ?>" class="form-control" />
                        </div>
                      </div>

                      <div class="col-sm-2">
                        <button type="button" class="btn btn-danger pull-right" data-action="remove-netcost-item"><i class="fa fa-trash"></i></button>
                      </div>
                    </div>
                  <?php } ?>
                </div>
                <br>
                
                <div>
                  <button type="button" class="btn btn-primary pull-right" data-action="add-netcost-item"><i class="fa fa-plus"></i></button>
                </div>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-milecost"><?php echo $entry_milecost; ?></label>
            <div class="col-sm-10">
              <input type="text" name="mkadout_milecost" value="<?php echo $mkadout_milecost; ?>" placeholder="<?php echo $entry_milecost; ?>" id="input-milecost" class="form-control" />
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-tax-class"><?php echo $entry_tax_class; ?></label>
            <div class="col-sm-10">
              <select name="mkadout_tax_class_id" id="input-tax-class" class="form-control">
                <option value="0"><?php echo $text_none; ?></option>
                <?php foreach ($tax_classes as $tax_class) { ?>
                <?php if ($tax_class['tax_class_id'] == $mkadout_tax_class_id) { ?>
                <option value="<?php echo $tax_class['tax_class_id']; ?>" selected="selected"><?php echo $tax_class['title']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $tax_class['tax_class_id']; ?>"><?php echo $tax_class['title']; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-geo-zone"><?php echo $entry_geo_zone; ?></label>
            <div class="col-sm-10">
              <select name="mkadout_geo_zone_id" id="input-geo-zone" class="form-control">
                <option value="0"><?php echo $text_all_zones; ?></option>
                <?php foreach ($geo_zones as $geo_zone) { ?>
                <?php if ($geo_zone['geo_zone_id'] == $mkadout_geo_zone_id) { ?>
                <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
            <div class="col-sm-10">
              <select name="mkadout_status" id="input-status" class="form-control">
                <?php if ($mkadout_status) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
            <div class="col-sm-10">
              <input type="text" name="mkadout_sort_order" value="<?php echo $mkadout_sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" />
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function(){
    // ---
      $(document).on('click', '[data-action="add-netcost-item"]', function(){
        var $this = $(this);
        var $container = $this.parents('.netcost-config-list').find('.config-items');

        $container.append(''+
          '<div class="row config-item">'+
            '<div class="col-sm-3">'+
              '<div class="input-group">'+
                '<span class="input-group-addon" id="basic-addon1"><?php echo $text_from; ?></span>'+
                '<input type="number" name="from" value="" class="form-control" />'+
              '</div>'+
            '</div>'+
            
            '<div class="col-sm-3">'+
              '<div class="input-group">'+
                '<span class="input-group-addon" id="basic-addon1"><?php echo $text_to; ?></span>'+
                '<input type="number" name="to" value="" class="form-control" />'+
              '</div>'+
            '</div>'+
            
            '<div class="col-sm-4">'+
              '<div class="input-group">'+
                '<span class="input-group-addon" id="basic-addon1"><?php echo $text_cost; ?></span>'+
                '<input type="number" name="cost" value="" class="form-control" />'+
              '</div>'+
            '</div>'+

            '<div class="col-sm-2">'+
              '<button type="button" class="btn btn-danger pull-right" data-action="remove-netcost-item"><i class="fa fa-trash"></i></button>'+
            '</div>'+
          '</div>'+
        '');
      });


      $(document).on('click', '[data-action="remove-netcost-item"]', function(){
        var $this = $(this);
        var $item = $this.parents('.config-item');

        $item.remove();
      });


      $('form').submit(function(){
        // ---
          var config = [];

          $('form').find('.netcost-config-list .config-item').each(function(){
            // ---
              var from = $(this).find('[name="from"]').val();
              var to = $(this).find('[name="to"]').val();
              var cost = $(this).find('[name="cost"]').val();

              config.push( {'from': from, 'to': to, 'cost': cost} );
            // ---
          });

          $('form').find('[name="mkadout_netcost"]').val( JSON.stringify(config) );

          return true;
        // ---
      });
    // ---
  });  
</script>

<?php echo $footer; ?> 