<div class="wrap">
<h2>Invoice Settings</h2>

<form method="post" action="options.php">
    <?php settings_fields( 'invoice-plugin-settings-group' ); ?>
    <?php do_settings_sections( 'invoice-plugin-settings-group' ); ?>
    <table class="form-table">
		
        <tr valign="top">
			<th scope="row">Invoice Admin Emails</th>
			<td><textarea rows="10" cols="50" name="invoice_admin_emails" ><?php echo get_option('invoice_admin_emails'); ?></textarea></td>
        </tr>	
 	
    </table>
    
    <?php submit_button(); ?>

</form>

</div>
