<table class="wp-list-table widefat fixed striped posts">
<thead>
<tr class="iedit author-self level-0  type-invoices status-publish hentry">
	<td style="width: 30px;">#</td>
	<td class="title column-title has-row-actions column-primary page-title">Name</td>	
	<td class="title column-title has-row-actions column-primary page-title">Description</td>	
	<td class="title column-title has-row-actions column-primary page-title">Payed</td>	
	<td class="title column-title has-row-actions column-primary page-title">Link(click to select)</td>	
</tr> 
</thead>
<?php
$type = 'invoices';
$args=array(
  'post_type' => $type,
  'post_status' => 'publish',
  'posts_per_page' => -1,
  'caller_get_posts'=> 1,
  'order' => 'desc'
);
$my_query = null;
$my_query = new WP_Query($args);
if( $my_query->have_posts() ) {
	$h = 1;
  while ($my_query->have_posts()) : $my_query->the_post(); ?>
<tr class="iedit author-self level-0  type-invoices status-publish hentry">
	<td style="width: 30px;"><?=$h?></td>
	<td class="title column-title has-row-actions column-primary page-title"><?=get_the_title()?></td>	
	<td class="title column-title has-row-actions column-primary page-title"><?=get_the_content()?></td>	
	<td class="title column-title has-row-actions column-primary page-title"><?php $status =get_post_meta(get_the_ID(), "invoice_payed", $single = true);  echo (!empty($status)) ? 'Yes' : 'No'; ?></td>	
	<td class="title column-title has-row-actions column-primary page-title"><span id="sct_<?=$h?>" onclick="selectText('sct_<?=$h?>')"><?php echo wp_get_shortlink(get_the_ID()); ?></span></td>	
</tr> 
   
    <?php
	$h++;
  endwhile;
}
wp_reset_query();  // Restore global post data stomped by the_post().

?>

</table>

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