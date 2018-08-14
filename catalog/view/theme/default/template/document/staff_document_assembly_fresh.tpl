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
				display: table;
			    border-collapse: collapse;

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

			/* Compact */
				.table.compact table {
	    			float: left;
				    table-layout: fixed;
	    			width: 50%;
				}

				.table.compact table:first-child {
					border-right: 15px solid #dee2e6;
				}
			/* Compact */
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
	    <h1 style="text-align: center;">Технологическая карта на сборку свежих продуктов</h1>
	    <h2 style="text-align: center;"><?php echo date('d.m.Y'); ?> <span style="font-weight: normal;"><?php echo date('H:i'); ?></span></h2>
	    <br><br>

	    <!-- Opencart -->
	    	<?php $count_categories = 1; ?>

	    	<?php foreach ($categories as $key_category => $category) { ?>
	    		<?php if( !empty($category) ) { ?>

	    			<div style="page-break-inside:avoid;">

						<h4>
							<span style="font-weight: normal;"></span><?php echo $key_category; ?>
							<span style="float: right; font-weight: normal;"><?php echo $count_categories; ?> из <?php echo count($categories); ?></span>
						</h4>

						<div class="table compact">
			                <table>
			                  <thead><tr>
			                  	<th>Нименование</th> <th>Вес</th> <th style="width: 100px; text-align: right;">Номер заказа</th>
			                  </tr></thead>

			                  <tbody>

			                  		<?php $count_products = 0; ?>

			    					<?php foreach ($category as $keyProducts => $products) { ?>

			                  			<?php if( round($count_products) == round(count($category)/2) ) { ?>
				                  				</tbody>
				                  			</table>

			                  				<table>
							                  <thead><tr>
							                  	<th>Нименование</th> <th>Вес</th> <th style="width: 100px; text-align: right;">Номер заказа</th>
							                  </tr></thead>

							                  <tbody>
			                  			<?php } ?>

			    						
			                  			<?php $count_details = 1; ?>
					    				
					    				<?php foreach ($products as $keyProduct => $product) { ?>
			                  				<tr>
			                  					<?php if( $count_details == 1 ) { ?>
					                  				<td rowspan="<?php echo count($products); ?>">
					                  					<?php echo $product['name']; ?>
					                  				</td>
													
													<?php $count_products++; ?>
												<?php } ?>

				                  				<td>
				                  					<?php echo $product['details']['variant']; ?> <?php echo $product['details']['unit']; ?> x <?php echo $product['details']['amount']; ?>
				                  				</td>

				                  				<td style="width: 200px; text-align: right;">
				                  					<?php echo $product['order_id']; ?>
				                  					<?php echo $count_products; ?>
				                  					<?php echo round(count($category)/2); ?>
				                  				</td>

			                  				</tr>
											
											<?php $count_details++; ?>
										<?php } ?>
									<?php } ?>
			                  </tbody>
			                </table>
			            </div>

		    			<br>

					</div>

					<?php $count_categories++; ?>
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
