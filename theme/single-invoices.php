<?php
session_start();
while ( have_posts() ) : the_post();

$_SESSION['invid'] = get_the_ID();

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=0.3">
    <title></title>

	<link rel="stylesheet" href="<?php echo plugins_url('../css/reset.css', __FILE__); ?>" type="text/css" media="all" />
	<link rel="stylesheet" href="<?php echo plugins_url('../css/style.css', __FILE__); ?>" type="text/css" media="all" />


  </head>
  <body>


	<div class="container firstContainer">
		<div class="block770 clearfix">
			<div class="pull-left"><img src="<?php echo plugins_url('../css/img/logo.png', __FILE__); ?>" /></div>
			<div class="pull-right invoiceTitle">Final Details for Order #915 </div>
		</div>
	</div>
	<div class="container secondContainer">
		<div class="block770 clearfix">
			<div class="pull-left orderBasic">
				<div><strong>Order Placed:</strong> <?php echo get_the_date('F j, Y') ?></div>
				<div><strong>GetTreated Order Number:</strong> <?php the_ID() ?></div>
				<div><strong>Order Total:</strong> $<?php echo get_post_meta($post->ID, "invoice_amount", $single = true); ?></div>
			</div>
			<script>
				function print_invoice()
				{
					window.print();
				}
			</script>
			<div class="pull-right printInvoicePage" onclick="print_invoice()">print this page for your records</div>
		</div>
	</div>	
	<div class="container thirdContainer">
		<div class="block770 clearfix text-center"><div><?php echo get_the_date('F j, Y') ?></div></div>
	</div>	
	
	<div class="container fourthContainer">
		<div class="block770 clearfix">
			<div>
				<div class="fourthContainer_title"><strong>Customer Information</strong></div>
				<div class="fourthContainer_info"><?php echo get_the_title() ?></div>
				<div class="fourthContainer_info"><?php echo get_post_meta($post->ID, "invoice_address", $single = true ); ?></div>
				<div class="fourthContainer_info"><?php echo get_post_meta($post->ID, "invoice_phone", $single = true ); ?></div>
			</div>
		</div>
	</div>

	<div class="container fifthContainer">
		<div class="block770 clearfix">
			<div class="fifthContainerTableHandler">
				<table class="">
					<thead>
						<tr>
							<td>Procedures</td>
							<td>Quantity</td>
							<td>Price/Unit</td>
							<td>Total Price</td>
						</tr>
					</thead>
					<tbody>
						<?php foreach(get_post_meta($post->ID, "invoice_procedures", $single = true) as $invProcedure) { 
							$procExploded = explode('_',$invProcedure);
							?>
							
							<tr>
								<td><?php echo $procExploded[0] ?></td>
								<td>1</td>
								<td><?php echo $procExploded[1] ?></td>
								<td>$ <?php echo $procExploded[1] ?></td>
							</tr>	
						<?php } ?>
						<?php 
						$specific_procedure_invoices = get_post_meta($post->ID, "specific_procedure_invoices", $single = true );
						if(!empty($specific_procedure_invoices))
						{
							?>
							<tr>
								<tr>
									<td><?php echo get_post_meta($post->ID, "specific_procedure_invoices", $single = true ); ?></td>
									<td>1</td>
									<td><?php echo get_post_meta($post->ID, "specific_procedure_invoices_price", $single = true ); ?></td>
									<td>$ <?php echo get_post_meta($post->ID, "specific_procedure_invoices_price", $single = true ); ?></td>
								</tr>								
							</tr>		
							
							<?php
							
						}
						
						?>						
					</tbody>
					
					
				</table>
			</div>
		</div>
	</div>	
	<div class="container sixContainer">
		<div class="block770 clearfix">
		<?php 
		$invoice_payed = get_post_meta($post->ID, "invoice_payed", $single = true );
		if(empty($invoice_payed))
		{
			?>
				<div class="pull-left totalTitle">Total Amount To Be Paid</div>
			<?php
		}
		else
		{
			?>
			<div class="pull-left totalTitle">Total Amount Paid</div>
			<?php
		}			
		
		?>			
			
			<div class="pull-right totalAmount"><strong>$<?php echo get_post_meta($post->ID, "invoice_amount", $single = true); ?> USD </strong>(Tax Included)<br> <?php echo get_post_meta($post->ID, "invoice_amount_amd", $single = true); ?> AMD (approximately) </div>
		</div>
	</div>
		<?php 
		$invoice_payed = get_post_meta($post->ID, "invoice_payed", $single = true );
		if(empty($invoice_payed))
		{
			?>
				<div class="container sevenContainer">
					<div class="block770 clearfix">
						<a target="_blank" href="<?php echo get_site_url() ?>/invpy/"  class="invoiceButton">PROCEED TO PAYMENT</a>
					</div>
				</div>	
			<?php
		}
		?>	

	
	<div class="container">
		<div class="block770 clearfix">
			<div class="text-center noteVat">Please note: This is not a VAT invoice.</div>
		</div>
	</div>	

	<div class="container">
		<div class="block770 clearfix">
			<div class="text-center termsInvoice"><a href="/">terms & conditions</a> | <a href="/">privecy policy</a> | <a href="/">liability agreement</a>  © 2015 all rights reserved Get Treated Medical Travel</div>
		</div>
	</div>		

		
	

<!--
<h2>The invoice #<?php the_ID() ?> - <?php the_date('d-M-Y') ?></h2>
<form action="<?php echo get_site_url() ?>/invpy/" method="post" target="_blank">
	<table class="invoice_plugin_table">
		<tr>
			<td>Name</td>
			<td><?php echo get_the_title() ?></td>
		</tr>
		<tr>
			<td>Procedures</td>
			<td><?php echo implode("\n<br>", get_post_meta($post->ID, "invoice_procedures", $single = true) ); ?></td>
		</tr>
		<?php 
		$specific_procedure_invoices = get_post_meta($post->ID, "specific_procedure_invoices", $single = true );
		if(!empty($specific_procedure_invoices))
		{
			?>
			<tr>
				<td>Additional procedure(if there)</td>
				<td><?php echo get_post_meta($post->ID, "specific_procedure_invoices", $single = true ); ?></td>
			</tr>		
			
			<?php
			
		}
		
		?>
	
		<tr>
			<td>Amount</td>
			<td>$ <?php echo get_post_meta($post->ID, "invoice_amount", $single = true); ?> (tax included)</td>
		</tr>
		<?php 
		$invoice_payed = get_post_meta($post->ID, "invoice_payed", $single = true );
		if(empty($invoice_payed))
		{
			?>
			
				<tr>
					<td></td>
					<td><input type="submit" value="Proceed to payment"></td>
				</tr>	
			<?php
			
		}
		else
		{
			?>
		<tr>
			<td>Payment status</td>
			<td>Payment confirmed through AmeriaBank</td>
		</tr>
			<?php
		}
		
		?>		

	</table>
	
</form>
-->
<?php
endwhile;

?>

   <!-- <script src="http://code.jquery.com/jquery-latest.min.js"></script>
	<script type="text/javascript" src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
    <script src="js/scripts.js"></script>-->
  </body>
</html>
