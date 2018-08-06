<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
	
	<style type="text/css">
		main {
			display: block;
			position: relative;
			width: 1000px;
			height: auto;
			min-height: 1400px; 
			margin: 0 auto;
		}

		/* Table */
			.table {
				display: block;
				width: 100%;
				height: auto;
				border: 1px solid #dee2e6;
			    border-radius: 4px;
			    overflow: hidden;
			}

			table {
			    border-collapse: collapse;
			}

			table {
			    width: 100%;
			    max-width: 100%;
			    background-color: transparent;
			}

			table tbody tr {
				background-color: #FFFFFF
			    -webkit-transition: background-color .5s;
				transition: background-color .5s;
			}

			table tbody tr:nth-of-type(odd) {
			    background-color: rgba(0,0,0,.05);
			}

			table tbody tr:hover {
				background-color: #D6E9A7;
			}

			table td, table th {
			    padding: .75rem;
			    vertical-align: top;
			    border: 1px solid #dee2e6;
			    text-align: left;
			}

			table td, table th {
			    padding: .75rem;
			    vertical-align: top;
			    border: 1px solid #dee2e6;
			}
		/* Table */

		/* Helper */
			.text-color-red { color: #EF5E67; }
			.text-color-green { color: #D6E9A7; }

			hr {
				border: none;
				border-bottom: 1px solid rgba(0, 0, 0, .1);
				margin: 15px 0px;
			}
		/* Helper */
	</style>
</head>


<body>

	<main>
	    <h1 style="text-align: center;">Технологическая карта на сборку</h1>
	    <h2 style="text-align: center;"><?php echo date('d.m.Y'); ?> <span style="font-weight: normal;"><?php echo date('H:i'); ?></span></h2>
	    <br><br>

	    <!-- Retail CRM -->
	    	<!--
		    <php foreach ($orders as $keyOrder => $order) { ?>
		    	<php if( !empty($order['products']) ) { ?>
			    
					<h4><span style="font-weight: normal;">Номер заказа: </span><php echo $order['order_id']; ?></h4>
					
					<div class="table">
		                <table>
		                  <thead><tr> <th>Название</th> <th style="width: 200px; text-align: right;">В заказ</th> </tr></thead>

		                  <tbody>
		    					<php foreach ($order['products'] as $keyProduct => $product) { ?>
		                  			<tr>
		                  				<td><php echo $product->offer->name; ?></td>
		                  				<td style="width: 200px; text-align: right;">
		                  					<php foreach ($product->properties as $keyProperty => $property) { ?>
		                  						<php echo $property->value; ?>
											<php } ?>
		                  				</td>
		                  			</tr>
								<php } ?>
		                  </tbody>
		                </table>
		             </div>

		    		<br><hr><br>

				<php } ?>
			<php } ?>
			-->
	    <!-- Retail CRM -->

	    <!-- Opencart -->
	    	<?php $count_orders = 1; ?>

		    <?php foreach ($orders as $keyOrder => $order) { ?>
		    	
		    	<?php if( !empty($order['products']) ) { ?>

			    	<div style="page-break-inside:avoid;">
						<h4>
							<span style="font-weight: normal;">Номер заказа: </span><?php echo $order['order_id']; ?>
							<span style="float: right; font-weight: normal;"><?php echo $count_orders; ?> из <?php echo count($orders); ?></span>
						</h4>
						
						<div class="table">
			                <table>
			                  <thead><tr>
			                  	<th>#</th> <th>Название</th> <th style="width: 200px; text-align: right;">В заказ</th>
			                  	<th>#</th> <th>Название</th> <th style="width: 200px; text-align: right;">В заказ</th>
			                  </tr></thead>

			                  <tbody>
			                  		<?php $count = 1; ?>
									<?php $count_cols = 1; ?>
									
									<?php $cols = 2; ?>

			                  		<tr>
				    					<?php foreach ($order['products'] as $keyProduct => $product) { ?>

			                  				<?php if( $count_cols > $cols  ) { ?>
												<?php $count_cols = 1; ?>
			                  					</tr><tr>
											<?php } ?>

				                  				<td>
				                  					<?php echo $count; ?>
				                  				</td>
				                  				<td>
				                  					<?php echo $product['name']; ?>
				                  				</td>
				                  				<td style="width: 200px; text-align: right;">
				                  					<?php echo $product['variant']; ?> <?php echo $product['unit']; ?> x <?php echo $product['amount']; ?>
				                  				</td>
											<?php $count_cols++; ?>
											<?php $count++; ?>
										<?php } ?>
			                  		</tr>
			                  </tbody>
			                </table>
			            </div>

		    			<br><hr><br>
			        </div>

			        <?php $count_orders++; ?>
				<?php } ?>
			<?php } ?>
	    <!-- Opencart -->
	</main>

</body>


<script type="text/javascript">
  window.onload = function() {
    // ---
      setTimeout(function(){
        window.print();
      }, 1500);
    // ---
  }
</script>

</html>
