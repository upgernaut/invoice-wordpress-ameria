<?php
/**
 * @package Gettreated Invoice Plugin
 * @version 1.6
 */
/*
Plugin Name: Gettreated Invoice Plugin
Plugin URI: 
Description: This plugin is for generate and track invoices for clients. Works using ameriabank payment system.
Author: Aram
Version: 1.6
Author URI: 
*/


if (!class_exists('Gettreated_Invoice_Plugin'))
{
  class Gettreated_Invoice_Plugin
  {
	public $meta_boxes;
    public $_name;
	
    public $page_title;
    public $page_name;
    public $page_id;	
	
	
	/* initializing plugin */
    public function __construct()
    {
		
		$this->_name      = array('invpy','invret');
		$this->page_title = array('Invoice paypage','Invoice back');
		$this->page_name  = $this->_name;		
		
		$this->meta_boxes = array(
				"invoice_address" => array(
					"name" => "invoice_address",
					"std" => "",
					'type' => 'textarea',
					"title" => "Customer Address",
					"description" => "<div style='padding:0 0 10px 190px;'></div>"),
				"invoice_phone" => array(
					"name" => "invoice_phone",
					"std" => "",
					'type' => 'input',
					"title" => "Customer Phone Number",
					"description" => "<div style='padding:0 0 10px 190px;'></div>"),					
				"invoice_procedures" => array(
					"name" => "invoice_procedures",
					"std" => "",
					'type' => 'textarea_procedures',
					"title" => "Procedures",
					"description" => "<div style='padding:0 0 10px 190px;'></div>"),			
				"specific_procedure_invoices" => array(
					"name" => "specific_procedure_invoices",
					"std" => "",
					'type' => 'input',
					"title" => "Specific procedure",
					"description" => "<div style='padding:0 0 10px 190px;'></div>"),	
				"specific_procedure_invoices_price" => array(
					"name" => "specific_procedure_invoices_price",
					"std" => "",
					'type' => 'input',
					"title" => "Specific procedure price",
					"description" => "<div style='padding:0 0 10px 190px;'></div>"),						
				"invoice_amount" => array(
					"name" => "invoice_amount",
					"std" => "",
					'type' => 'input',
					"title" => "Amount",
					"description" => "<div style='padding:0 0 10px 190px;'></div>"),
				"invoice_amount_amd" => array(
					"name" => "invoice_amount_amd",
					"std" => "",
					'type' => 'input',
					"title" => "Amount AMD",
					"description" => "<div style='padding:0 0 10px 190px;'></div>"),						
				"invoice_payment_id" => array(
					"name" => "invoice_payment_id",
					"std" => "",
					'type' => 'input',
					"title" => "Payment ID (can be checked in bank)",
					"description" => "<div style='padding:0 0 10px 190px;'></div>"),					
				"invoice_payed" => array(
					"name" => "invoice_payed",
					"std" => "",
					"type"=>"checkbox",
					"title" => "Paid",        
					"description" => ""),	
			);

		add_filter('template_include', array($this, 'invoice_set_template'));		
		add_action( 'admin_menu', array($this, 'setup_dashboard_page_invoice_ameria'), 15);
		add_action( 'init', array($this, 'create_invoice_post_type'), 5);
		// add_action( 'init', array($this, 'invoice_posttype_permalink_rewrite'), 5);
		
	  
		add_action( 'admin_menu', array($this, 'create_invoice_meta_box'), 15);
		add_action( 'save_post', array($this, 'invoice_save_postdata'), 15);
		add_action( 'admin_enqueue_scripts', array($this, 'my_admin_theme_style'), 15);
		
		register_activation_hook(__FILE__, array($this, 'invoice_plugin_activate'));
		register_deactivation_hook(__FILE__, array($this, 'invoice_plugin_deactivate'));		

    }
	
	public function invoice_posttype_permalink_rewrite() {
		// add to our plugin init function
		global $wp_rewrite;
		$gallery_structure = '/invoices/%post_id%';
		$wp_rewrite->add_rewrite_tag("%invoices%", '([^/]+)', "invoices=");
		$wp_rewrite->add_permastruct('invoices', $gallery_structure, false);
	}	
	public function ameria_clearing_xchng()
	{
		/* to turn all content to array */
		/* dollari kursy yst yahoo.finance-i */

		$xmlString = file_get_contents('http://www.ameriabank.am/rssch.aspx?type=1');

		$xml = simplexml_load_string($xmlString, "SimpleXMLElement", LIBXML_NOCDATA);


		$json = json_encode($xml);
		$array = json_decode($json,TRUE);
		// print_r($array['channel']['item']['description']);die;


		// example of how to use basic selector to retrieve HTML contents

		include(__DIR__.'/inc/simple_html_dom.php');

		 
		// get DOM from URL or file
		$html = str_get_html($array['channel']['item']['description']);

		// find all link

		$usd = $html->find('td');
		return $usd = $usd[4]->plaintext; 
		
	}
	

	public function my_admin_theme_style() {
		wp_enqueue_script('invoice-admin', plugins_url('/js/invoice-admin.js', __FILE__));
	}
	/* actions at activation plugin */
	public function invoice_plugin_activate()
	{
	  global $wpdb;      
		add_option( 'invoice_plugin_last_insert_id' );
		foreach($this->_name as $pgkey=>$pgval)
		{
			delete_option($pgval.'_page_title');
			add_option($pgval.'_page_title', $this->page_title[$pgkey], '', 'yes');	

			delete_option($pgval.'_page_name');
			add_option($pgval.'_page_name', $this->page_name[$pgkey], '', 'yes');	

		
		}

		foreach($this->page_title as $pgkey=>$pgval)
		{
			$the_page[$pgkey] = get_page_by_title($pgval);			
		}

	  if (!array_filter($the_page))
	  {
		 foreach($this->page_title as $pgkey=>$pgval)
		 {
			 
			// Create post object
			$_p = array();
			$_p['post_title']     = $pgval;
			$_p['post_content']   = "This text may be overridden by the plugin. You shouldn't edit it.";
			$_p['post_status']    = 'publish';
			$_p['post_type']      = 'page';
			$_p['comment_status'] = 'closed';
			$_p['ping_status']    = 'closed';
			$_p['post_category'] = array(1); // the default 'Uncatrgorised'
			$_p['post_name']    =  $this->page_name[$pgkey];
			// Insert the post into the database
			$this->page_id[] = wp_insert_post($_p);
		 }
	  }
	  else
	  {
		 foreach($the_page as $thpgkey=>$thpgval)
		 {
			 


		//make sure the page is not trashed...
		$the_page[$thpgkey]->post_status = 'publish';
		$this->page_id[] = wp_update_post($the_page[$thpgkey]);
		
		 }
	  }
		
		foreach($this->_name as $pgkey=>$pgval)
		{
		  delete_option($pgval.'_page_id');
		  add_option($pgval.'_page_id', $this->page_id[$pgkey]);
			
		}
	}	
	
	/* actions at deactivation */
    public function invoice_plugin_deactivate()
    {
      $this->invoice_plugin_deletePage(true);
      $this->invoice_plugin_deleteOptions();
    }	
	
	/* delete create pages at activation */
    private function invoice_plugin_deletePage($hard = false)
    {
      global $wpdb;
		foreach($this->_name as $pgkey=>$pgval)
		{			
		  $id = get_option($pgval.'_page_id');
		  if($id && $hard == true)
			wp_delete_post($id, true);
		  elseif($id && $hard == false)
			wp_delete_post($id);
		}
    }

	/* delete options created at activation */
    private function invoice_plugin_deleteOptions()
    {
		delete_option( 'invoice_plugin_last_insert_id' );
		foreach($this->_name as $pgkey=>$pgval)
		{			
		  delete_option($pgval.'_page_title');
		  delete_option($pgval.'_page_name');
		  delete_option($pgval.'_page_id');
		}		

    }	
	
	/* setting template for pages and single for invoices */
	public function invoice_set_template( $template ){
		if(is_page('invpy'))
		{
			$template =  __DIR__.'/theme/page-invpy.php';
		}
		
		if(is_page('invret'))
		{
			$template =  __DIR__.'/theme/page-invret.php';
		}
		
		if(is_singular('invoices') ){
			//WordPress couldn't find an 'event' template. Use plug-in instead:
			$template =  __DIR__.'/theme/single-invoices.php';
		}

		return $template;
	}		
	
	/* setup payment pages and dashboard pages */
	public function setup_dashboard_page_invoice_ameria() {
		add_action( 'admin_menu', array($this, 'setup_dashboard_page_invoice_ameria'), 15);
		add_action( 'admin_init', array($this, 'register_invoice_plugin_settings'), 15);

// add_menu_page('My Custom Page', 'My Custom Page', 'manage_options', 'my-top-level-slug');

		
			
			
		add_submenu_page( 'edit.php?post_type=invoices', 'Create Invoice', 'Create Invoice', 'manage_options', 'crinvoice',array($this,'settings_page_invoice_ameria'));
		add_submenu_page( 'edit.php?post_type=invoices', 'Invoice Plugin Settings', 'Invoice Plugin Settings', 'manage_options', 'invoice_plugin_settings',array($this,'invoice_plugin_settings_add'));
		add_submenu_page( 'edit.php?post_type=invoices', 'Invoices list table', 'Invoices list table', 'manage_options', 'invoice_plugin_invoice_list',array($this,'invoices_list_table'));
	}
	
	/* setting invoices list page at dashboard */
	public function invoices_list_table() 
	{	
		
		// include_once(get_template_directory() . '/inc/settings.php' );
		// var_dump(__DIR__.'/inc/invoices-list-table.php');
		include_once(__DIR__.'/inc/invoices-list-table.php' );
	}
	
	/* emailing */
	public static function invoice_emailing($email,$subject,$text)
	{
		ob_start();
		include(__DIR__.'/inc/email_header.php');	
		echo $text;
		include(__DIR__.'/inc/email_footer.php');
		$message = ob_get_clean();	
		ob_end_flush();
		// out($message); die;
		$status = wp_mail($email, $subject, $message); 
		return $status;
	}	
	
	/* plugin settings page at dashboard */
	public function register_invoice_plugin_settings() {
		//register our settings
		register_setting( 'invoice-plugin-settings-group', 'invoice_admin_emails' );

	}	
	
	/* plugin settings page at dashboard */
	public function settings_page_invoice_ameria() 
	{	
		global $post;
		// include_once(get_template_directory() . '/inc/settings.php' );
		include_once(__DIR__.'/inc/invoice-page.php' );
	}
	
	/* plugin settings page at dashboard */
	public function invoice_plugin_settings_add() 
	{
		// include_once(get_template_directory() . '/inc/settings.php' );
		include_once(__DIR__.'/inc/invoice-settings.php' );
	}	
	
	/* creating invoice post type */
	public function create_invoice_post_type() {
		register_post_type( 'invoices',
			array(
				'supports'           => array( 'title'),
				'labels' => array(
					'name'               => _x( 'Invoices', 'post type general name', 'your-plugin-textdomain' ),
					'singular_name'      => _x( 'Invoice', 'post type singular name', 'your-plugin-textdomain' ),
					'menu_name'          => _x( 'Invoices', 'admin menu', 'your-plugin-textdomain' ),
					'name_admin_bar'     => _x( 'Invoice', 'add new on admin bar', 'your-plugin-textdomain' ),
					'add_new'            => _x( 'Add new', 'book', 'your-plugin-textdomain' ),
					'add_new_item'       => __( 'Add new', 'your-plugin-textdomain' ),
					'new_item'           => __( 'New', 'your-plugin-textdomain' ),
					'edit_item'          => __( 'Edit invoice', 'your-plugin-textdomain' ),
					'view_item'          => __( 'View invoice', 'your-plugin-textdomain' ),
					'all_items'          => __( 'All invoices', 'your-plugin-textdomain' ),
					'search_items'       => __( 'Search invoice', 'your-plugin-textdomain' ),
					'parent_item_colon'  => __( 'Parent invoice:', 'your-plugin-textdomain' ),
					'not_found'          => __( 'Can\'t find invoice.', 'your-plugin-textdomain' ),
					'not_found_in_trash' => __( 'Can\'t find invoice in trash.', 'your-plugin-textdomain' )
				),
			'public' => true,
			'has_archive' => false,
			'menu_icon' => 'dashicons-pressthis',

			)
		);
	}

	/* assign invoice meta boxes  */
	public function invoice_meta_boxes() {
		global $post;
		
		$new_meta_boxes = $this->meta_boxes;
		
	   foreach ($new_meta_boxes as $meta_box) {
			$meta_box_value = get_post_meta($post->ID, $meta_box['name'], true);
			
			if($meta_box_value == "") {
				$meta_box_value = $meta_box['std']; 
			}
			if(isset($meta_box['type']) && $meta_box['type'] == 'textarea')
			{
				echo'<input type="hidden" name="'.$meta_box['name'].'_noncename" id="'.$meta_box['name'].'_noncename" value="'.wp_create_nonce( plugin_basename(__FILE__) ).'" />';
				echo'<table width="100%"><tbody>';
				echo'<tr><td align="right" style="width: 184px;"><strong>'.$meta_box['title'].' - </strong></td>';
				echo'<td><textarea style="width:100%;height:100px;border:1px solid black" name="'.$meta_box['name'].'">'.$meta_box_value.'</textarea></td></tr>';
				echo'</tbody></table>';
				echo'<label for="'.$meta_box['name'].'">'.$meta_box['description'].'</label>';		
			}
			else if(isset($meta_box['type']) && $meta_box['type'] == 'textarea_procedures')
			{
				$meta_box_value = implode(',',$meta_box_value);
				echo'<input type="hidden" name="'.$meta_box['name'].'_noncename" id="'.$meta_box['name'].'_noncename" value="'.wp_create_nonce( plugin_basename(__FILE__) ).'" />';
				echo'<table width="100%"><tbody>';
				echo'<tr><td align="right" style="width: 184px;"><strong>'.$meta_box['title'].' - </strong></td>';
				echo'<td><textarea style="width:100%;height:100px;border:1px solid black" name="'.$meta_box['name'].'">'.$meta_box_value.'</textarea></td></tr>';
				echo'</tbody></table>';
				echo'<label for="'.$meta_box['name'].'">'.$meta_box['description'].'</label>';		
			}			
			else if(isset($meta_box['type']) && $meta_box['type'] == 'input')
			{
				echo'<input type="hidden" name="'.$meta_box['name'].'_noncename" id="'.$meta_box['name'].'_noncename" value="'.wp_create_nonce( plugin_basename(__FILE__) ).'" />';
				echo'<table width="100%"><tbody>';
				echo'<tr><td align="right" style="width: 184px;"><strong>'.$meta_box['title'].' - </strong></td>';
				echo'<td><input type="text/number" name="'.$meta_box['name'].'" value="'.$meta_box_value.'" style="width: 100%;" /></td></tr>';
				echo'</tbody></table>';
				echo'<label for="'.$meta_box['name'].'">'.$meta_box['description'].'</label>';
			}
			else if(isset($meta_box['type']) && $meta_box['type'] == 'checkbox')
			{
				$checked = ($meta_box_value) ? 'checked="checked"' : "" ;
				echo'<input type="hidden" name="'.$meta_box['name'].'_noncename" id="'.$meta_box['name'].'_noncename" value="'.wp_create_nonce( plugin_basename(__FILE__) ).'" />';
				echo'<table width="100%"><tbody>';
				echo'<tr><td align="right" style="width: 184px;"><strong>'.$meta_box['title'].' - </strong></td>';
				echo'<td><input type="checkbox" name="'.$meta_box['name'].'" value="1" '. $checked .' /></td></tr>';
				echo'</tbody></table>';
				echo'<label for="'.$meta_box['name'].'">'.$meta_box['description'].'</label>';		
			}
			else if(isset($meta_box['type']) && $meta_box['type'] == 'dropdown')
			{
				$dropdown = "<select name='{$meta_box[name]}'>";
				foreach($meta_box['values'] as $mbvalue)
				{
					$selected = ($meta_box_value == $mbvalue) ? 'selected="selected"' : "" ;
					$dropdown .= "<option {$selected}>{$mbvalue}</option>";
				}
				$dropdown .= '</select>';
				echo'<input type="hidden" name="'.$meta_box['name'].'_noncename" id="'.$meta_box['name'].'_noncename" value="'.wp_create_nonce( plugin_basename(__FILE__) ).'" />';
				echo'<table width="100%"><tbody>';
				echo'<tr><td align="right" style="width: 184px;"><strong>'.$meta_box['title'].' - </strong></td>';
				echo'<td>'.$dropdown.'</td></tr>';
				echo'</tbody></table>';
				echo'<label for="'.$meta_box['name'].'">'.$meta_box['description'].'</label>';		
			}		
		}
	}
	
	/* action to create meta boxes */
	public function create_invoice_meta_box() {
		if ( function_exists('add_meta_box') ) {
			add_meta_box( 'new-meta-boxes', 'Invoice Details', array($this, 'invoice_meta_boxes'), 'invoices', 'normal', 'high' );
		}
	}
	
	/* saving meta boxes */
	public function invoice_save_postdata( $post_id ) {
		global $post;
		
		$new_meta_boxes = $this->meta_boxes;
	 
		foreach($new_meta_boxes as $meta_box) 
		{
			if($meta_box['type'] != 'textarea_procedures')
			{
			
				// Verify
				if ( !wp_verify_nonce( $_POST[$meta_box['name'].'_noncename'], plugin_basename(__FILE__) )) {
					return $post_id;
				}
		 
				if ( 'page' == $_POST['post_type'] ) {
					if ( !current_user_can( 'edit_page', $post_id ))
						return $post_id;
					} else {
						if ( !current_user_can( 'edit_post', $post_id ))
						return $post_id;
					}
		 
					$data = $_POST[$meta_box['name']];
		 
					if(get_post_meta($post_id, $meta_box['name']) == "")
						add_post_meta($post_id, $meta_box['name'], $data, true);
					elseif($data != get_post_meta($post_id, $meta_box['name'], true))
						update_post_meta($post_id, $meta_box['name'], $data);
					elseif($data == "")
						delete_post_meta($post_id, $meta_box['name'], get_post_meta($post_id, $meta_box['name'], true));
			}
		}
	}	
  }
}
$memberlist = new Gettreated_Invoice_Plugin();