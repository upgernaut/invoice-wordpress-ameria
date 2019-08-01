<div class="wrap">
<h2>Generate invoice</h2>
<?php 

if(isset($_POST) && !empty($_POST))
{
	//create custom post payment
	$title     = $_POST['invoice_customer_name'];
	$content   = $_POST['invoice_description'];
	$post_type = 'invoices';  

	//the array of arguements to be inserted with wp_insert_post
	$new_post = array(
	'post_title'    => $title,
	'post_content'  => $content,
	'post_status'   => 'publish',          
	'post_type'     => $post_type 
	);

	//insert the the post into database by passing $new_post to wp_insert_post
	//store our post ID in a variable $pid
	$pid = wp_insert_post($new_post);

	//we now use $pid (post id) to help add out post meta data
	
	add_post_meta($pid, 'invoice_address', $_POST['invoice_address'], true);
	add_post_meta($pid, 'invoice_phone', $_POST['invoice_phone'], true);
	add_post_meta($pid, 'invoice_amount', $_POST['invoice_amount'], true);
	add_post_meta($pid, 'invoice_amount_amd', $_POST['invoice_amount_amd'], true);
	add_post_meta($pid, 'invoice_procedures', $_POST['procedures'], true);
	add_post_meta($pid, 'specific_procedure_invoices', $_POST['specific_procedure_invoices'], true);
	add_post_meta($pid, 'specific_procedure_invoices_price', $_POST['specific_procedure_invoices_price'], true);
	
	if($pid)
	{
		?>
			<div id="message" class="updated notice notice-success is-dismissible below-h2"><p>Post created. <a href="<?php echo get_permalink($pid); ?>">View post</a></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div><br />
			
			<div >Edit invoice: <a target="_blank" href="<?php echo get_edit_post_link( $pid ); ?>"><?php echo get_edit_post_link( $pid ); ?></a></div><br>

			<div >Frontend: <span id="selectable" onclick="selectText('selectable')"><?php echo wp_get_shortlink($pid); ?></span></div>
			
			<div style="color: gray; font-style:italic">Click on link to select</div>
			<script type="text/javascript">
				function selectText(containerid) {
					if (document.selection) {
						var range = document.body.createTextRange();
						range.moveToElementText(document.getElementById(containerid));
						range.select();
					} else if (window.getSelection) {
						var range = document.createRange();
						range.selectNode(document.getElementById(containerid));
						window.getSelection().addRange(range);
					}
				}
			</script>			
		<?php
		 
	}
}

?>
<form method="post" action="" method="post">

    <table class="form-table">
		
        <tr valign="top">
			<th scope="row">Customer Name</th>
			<td><textarea style="width: 380px;" rows="3" cols="50" name="invoice_customer_name" ></textarea></td>
        </tr>
        <tr valign="top">
			<th scope="row">Customer Address</th>
			<td><textarea style="width: 380px;" rows="3" cols="50" name="invoice_address" ></textarea></td>
        </tr>
        <tr valign="top">
			<th scope="row">Customer Phone</th>
			<td><input style="width: 380px;" type="text" cols="50" name="invoice_phone" ></td>
        </tr>		
	

		<?php 
		if(post_type_exists( 'procedure' ) )
		{
			
		
		?>
		<tr>
			<th>Choose procedure</th>
			<td class="clearfix">
				<div style="float:left;">
					<div>Dental</div>
					<div class="proceduresHandler opacity-skin">
						<?php
							$args = array(
								'post_type' => 'procedure',
								'posts_per_page' => -1,
								'tax_query' => array(
									array(
									'taxonomy' => 'procedures_tax',
									'field' => 'slug',
									'terms' => array('dental-procedures')
									)
								)
							);
							
							$query = new WP_Query($args);
							
							while($query->have_posts())
							{
								
								 $query->the_post();
								 
								 $data_prices = get_meta_by_prefix(get_the_ID(),'priceCur-');
								 
								 $data_prices_str = '';
								 
								 if($data_prices)
								 {
									foreach($data_prices as $dkey=>$dval)
									{
										$dexp = explode('priceCur-',$dkey);
										$data_prices_str .= ' data-price-'.$dexp[1].'="'.$dval.'"';
										
										if($dexp[1] == 'USD')
										{
											$usd_price = $dval;
										}										
									}
								 }
								?>
									<div>
										<input type="checkbox" <?php echo(isset($metaboxes['Procedures']) && !empty($metaboxes['Procedures']) && in_array(get_the_title(),unserialize($metaboxes['Procedures'][0]))) ? 'checked="checked"' : '' ; ?> name="procedures[]" value="<?php echo the_title(); ?>_<?php echo $usd_price; ?>" data-documents="<?php echo get_post_meta(get_the_ID(), 'Required documents', true); ?>" data-price="<?php echo get_post_meta(get_the_ID(), 'price', true); ?>" <?php echo $data_prices_str ?> id="checkbox-<?php echo $post->post_name; ?>" class="css-checkbox proceduresBox countTrigger" />
										<label for="checkbox-<?php echo $post->post_name; ?>" class="css-label radGroup1"><?php echo the_title(); ?>(<?php echo get_post_meta(get_the_ID(), 'price', true); ?>)</label>
									</div>											
								<?php										
							}
						?>

					</div>			
				</div>
				<div style="float:left;">
					<div>Plastic</div>
					<div class="proceduresHandler opacity-skin">
						<?php
							$args = array(
								'post_type' => 'procedure',
								'posts_per_page' => -1,
								'tax_query' => array(
									array(
									'taxonomy' => 'procedures_tax',
									'field' => 'slug',
									'terms' => array('plastic-surguries')
									)
								)
							);
							
							$query = new WP_Query($args);
							
							while($query->have_posts())
							{
								 $query->the_post();
								 
								 $data_prices = get_meta_by_prefix(get_the_ID(),'priceCur-');
								 
								 $data_prices_str = '';
								 
								 if($data_prices)
								 {
									foreach($data_prices as $dkey=>$dval)
									{
										$dexp = explode('priceCur-',$dkey);
										$data_prices_str .= ' data-price-'.$dexp[1].'="'.$dval.'"';
										
										if($dexp[1] == 'USD')
										{
											$usd_price = $dval;
										}
									}
								 }
								?>
									<div>
										<input type="checkbox" <?php echo(isset($metaboxes['Procedures']) && !empty($metaboxes['Procedures']) && in_array(get_the_title(),unserialize($metaboxes['Procedures'][0]))) ? 'checked="checked"' : '' ; ?> name="procedures[]" value="<?php echo the_title(); ?>_<?php echo $usd_price; ?>" data-documents="<?php echo get_post_meta(get_the_ID(), 'Required documents', true); ?>" data-price="<?php echo get_post_meta(get_the_ID(), 'price', true); ?>" <?php echo $data_prices_str ?> id="checkbox-<?php echo $post->post_name; ?>" class="css-checkbox proceduresBox countTrigger" />
										<label for="checkbox-<?php echo $post->post_name; ?>" class="css-label radGroup1"><?php echo the_title(); ?>(<?php echo get_post_meta(get_the_ID(), 'price', true); ?>)</label>
									</div>											
								<?php										
							}
						?>

					</div>			
				</div>					
			</td>
		</tr>
		<tr>
			<td>Specific procedure</td>
			<td>
				<div><input type="text" name="specific_procedure_invoices" placeholder="Name"></div>
				<div><input class="specific_procedure_invoices_price countTrigger" type="text" name="specific_procedure_invoices_price" placeholder="Price"></div>
			</td>
		</tr>
		<tr>
			<td>Total(USD):</td>
			<td>
				<input class="invoice_procedures_total" name="invoice_amount" type="hidden" value="0">
				<input  name="invoice_description" type="hidden" value="">
				<input class="invoice_procedures_total" type="text" disabled="disabled" value="0">
			</td>
		</tr>
		<tr>
			<td>Total(AMD) - approx:</td>
			<td>
				<input class="invoice_procedures_total_amd" name="invoice_amount_amd" type="hidden" value="0">
				
				<input class="invoice_procedures_total_amd" type="text" disabled="disabled" value="0">
			</td>
		</tr>		
		<tr>
			<td>Exchange rate (Ameriabank):</td>
			<td class="exchange_rate_ameriabank"><?php echo ($this->ameria_clearing_xchng()); ?></td>
		</tr>
		<?php } ?>
  	
    </table>

    <input type="submit" value="Generate invoice">

</form>

</div>
