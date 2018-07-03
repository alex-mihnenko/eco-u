<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

	<link href="css/core.css" rel="stylesheet">
	
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
	    <h1 style="text-align: center;">Список на фасовку</h1>
	    <br><br>


		<?php foreach ($packing as $keyPacking => $pack) { ?>
			<div class="table">
                <table>
                	<thead><tr> <th><?php echo $pack[0]->offer->name; ?></th> <th style="width: 200px; text-align: right;">На фасовку</th> </tr></thead>

                 	<tbody>
    					<?php $count = 1; ?>
    					<?php foreach ($pack as $keyProduct => $product) { ?>
                  			<tr>
                  				<td><b> #<?php echo $count; ?> </b></td>
                  				<td style="width: 200px; text-align: right;">
                  					<?php foreach ($product->properties as $keyProperty => $property) { ?>
                  						<?php echo $property->value; ?>
									<?php } ?>
                  				</td>
                  			</tr>
    						
    						<?php $count++; ?>
						<?php } ?>
					</tbody>
                </table>
             </div>

    		<br><hr><br>
		<?php } ?>
	</main>

</body>


<script type="text/javascript">
  window.onload = function() {
    // ---
      setTimeout(function(){
        //window.print();
      }, 1500);
    // ---
  }
</script>

</html>
