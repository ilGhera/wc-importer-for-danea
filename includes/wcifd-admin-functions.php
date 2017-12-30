<?php
/*
WOOCOMMERCE IMPORTER FOR DANEA - PREMIUM | ADMIN FUNCTIONS
*/


add_action( 'admin_init', 'wcifd_register_style' );
add_action( 'admin_menu', 'wcifd_add_menu' );

add_action( 'admin_init', 'wcifd_register_js_menu' );
add_action( 'admin_menu', 'wcifd_js_menu' );


//CREATE WCIFD STYLE
function wcifd_register_style() {
	wp_enqueue_style( 'wcifd-style', plugins_url('css/wc-importer-for-danea.css', 'wc-importer-for-danea-premium/css'));
}


//CALL THE MENU NAVIGATION SCRIPT
function wcifd_register_js_menu() {
	wp_register_script('wcifd-admin-nav', plugins_url('js/wcifd-admin-nav.js', 'wc-importer-for-danea-premium/js'), array('jquery'), '1.0', true );
}

function wcifd_js_menu() {
	wp_enqueue_script('wcifd-admin-nav');
}


//MENU
function wcifd_add_menu() {
	$wcifd_page = add_submenu_page( 'woocommerce','WCIFD Options', 'WC Importer for Danea', 'manage_woocommerce', 'wc-importer-for-danea', 'wcifd_options');
	
	//Richiamo lo style per wcifd
	// add_action( 'admin_print_styles-' . $wcifd_page, 'wcifd_add_style' );
	//Richiamo lo script per wcifd
	add_action( 'admin_print_scripts-' . $wcifd_page, 'wcifd_js_menu');
	
	return $wcifd_page;
}


//OPTIONS PAGE
function wcifd_options() {
	
	//CAN YOU DO THAT?
	if ( !current_user_can( 'manage_woocommerce' ) )  {
		wp_die( __( 'It seems like you don\'t have permission to see this page', 'wcifd' ) );
	}

	//START PAGE TEMPLATE
	echo '<div class="wrap">'; 
	echo '<div class="wrap-left">';
	
	//IS WOOCOMMERCE INSTALLED?
	if ( !class_exists( 'WooCommerce' ) ) { ?>

		<div id="message" class="error"><p><strong>
			<?php echo __('ATTENTION! It seems like Woocommerce is not installed.', 'wcifd' ); ?>
		</strong></p></div>

	<?php exit; 
	} ?>	

	<div id="wcifd-generale">
	<?php
		//HEADER
		echo "<h1 class=\"wcifd main\">" . __( 'Woocommmerce Importer for Danea - Premium', 'wcifd' ) . "<span style=\"font-size:60%;\"> 1.0.0</span></h1>";

		//PLUGIN PREMIUM KEY
		$key = sanitize_text_field(get_option('wcifd-premium-key'));
		if(isset($_POST['wcifd-premium-key'])) {
		$key = sanitize_text_field($_POST['wcifd-premium-key']);
		update_option('wcifd-premium-key', $key);
		}
		echo '<form id="wcifd-options" method="post" action="">';
		echo '<label>' . __('Premium Key', 'wcifd') . '</label>';
		echo '<input type="text" class="regular-text" name="wcifd-premium-key" id="wcifd-premium-key" placeholder="' . __('Add your Premium Key', 'wcifd' ) . '" value="' . $key . '" />';
		echo '<p class="description">' . __('Add your Premium Key and keep update your copy of <strong>Woocommerce Importer for Danea - Premium</strong>.', 'wcifd') . '</p>';
		echo '<input type="hidden" name="done" value="1" />';
		echo '<input type="submit" class="button button-primary" value="' . __('Save ', 'wcifd') . '" />';
		echo '</form>';
	?>
	</div>
	        
	<div class="icon32 icon32-woocommerce-settings" id="icon-woocommerce"><br /></div>
	  <h2 id="wcifd-admin-menu" class="nav-tab-wrapper woo-nav-tab-wrapper">
        <a href="#" data-link="wcifd-suppliers" class="nav-tab nav-tab-active" onclick="return false;"><?php echo __('Suppliers', 'wcifd'); ?></a>
        <a href="#" data-link="wcifd-products" class="nav-tab" onclick="return false;"><?php echo __('Products', 'wcifd'); ?></a>
        <a href="#" data-link="wcifd-clients" class="nav-tab" onclick="return false;"><?php echo __('Clients', 'wcifd'); ?></a>    
        <a href="#" data-link="wcifd-orders" class="nav-tab" onclick="return false;"><?php echo __('Orders', 'wcifd'); ?></a>
	  </h2>
      
      
<!-- IMPORT SUPPLIERS AS WORDPRESS USERS -->     
      
    <div id="wcifd-suppliers" class="wcifd-admin" style="display: block;">

		<?php 
			global $wp_roles;
			$roles = $wp_roles->get_names();   
			$users_val = (isset($_POST['wcifd-users'])) ? sanitize_text_field($_POST['wcifd-users']) : get_option('wcifd-suppliers-role');
		?>

	    <!--Form Fornitori-->
	    <form name="wcifd-suppliers-import" id="wcifd-suppliers-import" class="wcifd-form"  method="post" enctype="multipart/form-data" action="">

			<table class="form-table">
				<tr>
					<th scope="row"><?php _e("User role", 'wcifd' ); ?></th>
					<td>
					<select class="wcifd-users-suppliers" name="wcifd-users-suppliers">
						<?php
						if($users_val) {
							echo '<option value=" ' .  $users_val . ' " selected="selected"> ' . $users_val . '</option>';	
							foreach ($roles as $key => $value) {
								if($key != $users_val) {
									echo '<option value=" ' .  $key . ' "> ' . $key . '</option>';
								}
							}
						} else {
							echo '<option value="Subscriber" selected="selected">Subscriber</option>';	
							foreach ($roles as $key => $value) {
								if($key != 'Subscriber') {
									echo '<option value=" ' .  $key . ' "> ' . $key . '</option>';
								}
							}
						} 
						?>
					</select>
					<p class="description"><?php _e('Select a Wordpress user role for your suppliers.', 'wcifd'); ?></p>
				</tr>

				<?php wp_nonce_field( 'wcifd-suppliers-import', 'wcifd-suppliers-nonce'); ?>
				<input type="hidden" name="suppliers-import" value="1">

				<tr>
					<th scope="row"><?php _e('Add suppliers', 'wcifd'); ?></th>
					<td>
						<input type="file" name="suppliers-list">
						<p class="description"><?php _e('Select your suppliers list file (.csv)', 'wcifd'); ?></p>
					</td>
				</tr>

			</table>

			<input type="submit" class="button-primary" value="<?php _e('Import Suppliers', 'wcifd'); ?>">

	    </form>

	    <?php wcifd_users('suppliers'); ?>
	 
	</div>


<!-- IMPORT PRODUCTS AS WOOCOMMERCE PRODUCTS -->

	<div id="wcifd-products" class="wcifd-admin">
	 
		<!--Product Form-->
		<form name="wcifd-products-settings" class="wcifd-form one-of" method="post" action="">
			
			<h2 class="title"><?php echo __('General settings', 'wcifd'); ?></h2>
			
			<table class="form-table">
				<?php 
				$tax_included = get_option('wcifd-tax-included');
				if(isset($_POST['tax-included'])) {
					$tax_included = sanitize_text_field($_POST['tax-included']);
					update_option('wcifd-tax-included', $tax_included);
				}
				
				$use_suppliers = get_option('wcifd-use-suppliers');
				if(isset($_POST['hidden-use-suppliers'])) {
					$use_suppliers = (isset($_POST['wcifd-use-suppliers'])) ? $_POST['wcifd-use-suppliers'] : 0;
					update_option('wcifd-use-suppliers', $use_suppliers); 	
					update_option('wcifd-current-user', get_current_user_id());			
				}

				$regular_price_list = get_option('wcifd-regular-price-list');
				if(isset($_POST['regular-price-list'])) {
					$regular_price_list = $_POST['regular-price-list'];
					update_option('wcifd-regular-price-list', $regular_price_list);
				}

				$sale_price_list = get_option('wcifd-sale-price-list');
				if(isset($_POST['sale-price-list'])) {
					$sale_price_list = $_POST['sale-price-list'];
					update_option('wcifd-sale-price-list', $sale_price_list);
				}

				$size_type = get_option('wcifd-size-type');
				if(isset($_POST['wcifd-size-type'])) {
					$size_type = $_POST['wcifd-size-type'];
					update_option('wcifd-size-type', $size_type);
				}

				$weight_type = get_option('wcifd-weight-type');
				if(isset($_POST['wcifd-weight-type'])) {
					$weight_type = $_POST['wcifd-weight-type'];
					update_option('wcifd-weight-type', $weight_type);
				}

				$short_description = get_option('wcifd-short-description');
				if(isset($_POST['short-description'])) {
					$short_description = $_POST['short-description'] ? $_POST['short-description'] : 0;
					update_option('wcifd-short-description', $short_description);
				}

				$exclude_description = get_option('wcifd-exclude-description');
				if(isset($_POST['exclude-description'])) {
					$exclude_description = $_POST['exclude-description'] ? $_POST['exclude-description'] : 0;
					update_option('wcifd-exclude-description', $exclude_description);
				}

				$publish_new_products = get_option('wcifd-publish-new-products');
				if(isset($_POST['publish-new-products'])) {
					$publish_new_products = $_POST['publish-new-products'] ? $_POST['publish-new-products'] : 0;
					update_option('wcifd-publish-new-products', $publish_new_products);
				}


				?>
				<tr>
					<th scope="row"><?php echo __('Prices imported with tax', 'wcifd'); ?></th>
					<td>
						<select name="tax-included" class="wcifd">
							<option value="1" <?php echo($tax_included == 1) ? ' selected="selected"' : ''; ?>><?php echo __(' Yes, I will import prices inclusive of tax', 'wcifd'); ?></option>
							<option value="0" <?php echo($tax_included == 0) ? ' selected="selected"' : ''; ?>><?php echo __('No, I will enter prices exclusive of tax', 'wcifd'); ?></option>
						</select>
						<p class="description"><?php echo __('In Danea you can choose if export prices with tax included or not. What are you going to import?', 'wcifd'); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php echo __('Regular price', 'wcifd'); ?></th>
					<td>
						<select name="regular-price-list" class="wcifd">
							<?php
							for($n=1; $n <= 9; $n++) {
								echo '<option value="' . $n . '"' . ($regular_price_list == $n ? 'selected="selected"' : '') . '>' . __('Price list ', 'wcifd') . $n . '</option>';
							}
							?>
						</select>
						<p class="description"><?php echo __('The Danea price list to use for Woocommerce regular price.', 'wcifd'); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php echo __('Sale price', 'wcifd'); ?></th>
					<td>
						<select name="sale-price-list" class="wcifd">
							<?php
							echo '<option>' . __('Select a price list', 'wcifd') . '</option>';
							for($n=1; $n <= 9; $n++) {
								echo '<option value="' . $n . '"' . ($sale_price_list == $n ? 'selected="selected"' : '') . '>' . __('Price list ', 'wcifd') . $n . '</option>';
							}
							?>
						</select>
						<p class="description"><?php echo __('The Danea price list to use for Woocommerce sale price.', 'wcifd'); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php echo __('Product size type', 'wcifd'); ?></th>
					<td>
						<select name="wcifd-size-type" class="wcifd">
							<option value="gross-size"<?php echo($size_type == 'gross-size') ? ' selected="selected"' : ''; ?>><?php echo __('Gross size', 'wcifd'); ?></option>
							<option value="net-size"<?php echo($size_type == 'net-size') ? ' selected="selected"' : ''; ?>><?php echo __('Net size', 'wcifd'); ?></option>
						</select>
						<p class="description"><?php echo __('Chose if import gross or net product size.', 'wcifd'); ?></p>
					</td>
				</tr>
				<tr>
				<tr>
					<th scope="row"><?php echo __('Product weight type', 'wcifd'); ?></th>
					<td>
						<select name="wcifd-weight-type" class="wcifd">
							<option value="gross-weight"<?php echo($weight_type == 'gross-weight') ? 'selected="selected"' : ''; ?>><?php echo __('Gross weight', 'wcifd'); ?></option>
							<option value="net-weight"<?php echo($weight_type == 'net-weight') ? 'selected="selected"' : ''; ?>><?php echo __('Net weight', 'wcifd'); ?></option>
						</select>
						<p class="description"><?php echo __('Chose if import gross or net product weight.', 'wcifd'); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php echo __('Short description', 'wcifd'); ?></th>
					<td>
    					<input type="hidden" name="short-description" value="0">
						<input type="checkbox" name="short-description" value="1"<?php echo $short_description == 1 ? ' checked="checked"' : ''; ?>>
						<?php echo __('Use the excerpt as short product description.', 'wcifd'); ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php echo __('Exclude product description', 'wcifd'); ?></th>
					<td>
    					<input type="hidden" name="exclude-description" value="0">
						<input type="checkbox" name="exclude-description" value="1"<?php echo $exclude_description == 1 ? ' checked="checked"' : ''; ?>>
						<?php echo __('Exclude descriptions for products updates.', 'wcifd'); ?>
					</td>
				</tr>
				<tr>
	    			<th scope="row"><?php _e('Suppliers', 'wcifd' ); ?></th>
	    			<td>
	    				<fieldset>
		    				<label for="wcifd-use-suppliers">
		    					<input type="hidden" name="hidden-use-suppliers" value="1">
								<input type="checkbox" class="wcifd-use-suppliers" name="wcifd-use-suppliers" value="1" <?php if(get_option('wcifd-use-suppliers') == 1 ) { echo 'checked="checked"'; } ?>>
								<?php echo __('Use the product supplier as post author.', 'wcifd'); ?>
		    				</label>
		    			</fieldset>
	    			</td>
	    		</tr>
	    		<tr>
					<th scope="row"><?php echo __('Publish new products', 'wcifd'); ?></th>
					<td>
    					<input type="hidden" name="publish-new-products" value="0">
						<input type="checkbox" name="publish-new-products" value="1"<?php echo $publish_new_products == 1 ? ' checked="checked"' : ''; ?>>
						<?php echo __('Publish new products directly.', 'wcifd'); ?>
					</td>
				</tr>
			</table>
			<input type="submit" class="button-primary" style="margin-top: 1.5rem;" value="<?php _e('Save Changes', 'wcifd' ) ; ?>">
		</form>

		<form name="wcifd-receive-products" id="wcifd-receive-products" class="wcifd-form one-of" method="post" action="">
			
			<h2 class="title"><?php echo __('Receive products from Danea', 'wcifd'); ?></h2>

			<p>
				<?php 
				echo __('Receive products directly from the XML sent by Danea via HTTP post.', 'wcifd') . '<br>'; 
				?>
			</p>

			<table class="form-table">
				<?php
				$premium_key = strtolower(get_option('wcifd-premium-key'));
				$url_code = get_option('wcifd-url-code');
				if(!$url_code) {
					$url_code = wcifd_rand_md5(6);
					add_option('wcifd-url-code', $url_code);
				}

				$receive_orders_url = __('Please insert your <strong>Premium Key</strong>', 'wcifd');
				if($premium_key) {
					$receive_orders_url = home_url() . '?key=' . $premium_key . '&code=' . $url_code . '&mode=data';					
				}

				$import_images = get_option('wcifd-import-images');
				if(isset($_POST['hidden-receive-images'])) {
					$import_images = (isset($_POST['wcifd-import-images'])) ? $_POST['wcifd-import-images'] : 0;
					update_option('wcifd-import-images', $import_images);
				} 
				?>	
				<tr>
					<th scope="row"><?php echo __('URL', 'wcifd'); ?></th>
					<td>
						<div class="wcifd-copy-url"><span<?php echo(!$premium_key ? ' class="wcifd-red"' : ''); ?>><?php echo $receive_orders_url; ?></span></div>
						<p class="description"><?php echo __('Add this URL to the <strong>Settings</strong> tab of the <strong>Products update</strong> function in Danea.', 'wcifd'); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php echo __('Import images', 'wcifd'); ?></th>
					<td>
						<input type="hidden" name="hidden-receive-images" value="1">
						<input type="checkbox" class="wcifd-import-images" name="wcifd-import-images" value="1" <?php echo($import_images == 1 ) ? 'checked="checked"' : ''; ?>>
						<?php echo __('Import products images from Danea.', 'wcifd'); ?>
					</td>
				</tr>
			</table>
			<input type="submit" class="button-primary" style="margin-top: 1.5rem;" value="<?php _e('Save Changes', 'wcifd' ) ; ?>">
		</form>

		<form name="wcifd-products-import" id="wcifd-products-import" class="wcifd-form one-of"  method="post" enctype="multipart/form-data" action="">

			<?php $update_products = (isset($_POST['update-products'])) ? sanitize_text_field($_POST['update-products']) : get_option('wcifd-update-products'); ?>


			<h2 class="title"><?php echo __('Import products from a file', 'wcifd'); ?></h2>

			<table class="form-table">
				<tr>
					<th scoper="row"><?php echo __('Products update', 'wcifd'); ?></th>
					<td>
						<select name="update-products">
								<option value="1" <?php echo($update_products == 1) ? ' selected="selected"' : ''; ?>><?php echo __('Update products', 'wcifd'); ?></option>
								<option value="0" <?php echo($update_products == 0) ? ' selected="selected"' : ''; ?>><?php echo __('Don\'t update products', 'wcifd'); ?></option>
						</select>
						<p class="description"><?php echo __('Do you want to update the products already present in Woocommerce?', 'wcifd'); ?></p>
					</td>
				</tr>
				<?php wp_nonce_field( 'wcifd-products-import', 'wcifd-products-nonce'); ?>
				<input type="hidden" name="products-import" value="1">
				<tr>
					<th scope="row"><?php _e('Add products', 'wcifd'); ?></th>
					<td>
						<input type="file" name="products-list">
						<p class="description"><?php _e('Select your products list file (.csv)', 'wcifd'); ?></p>
					</td>
				</tr>
			</table>
			<input type="submit" class="button-primary" value="<?php _e('Import Products', 'wcifd' ) ; ?>">
		</form>

		<?php wcifd_products(); ?>

	</div>
    

<!-- IMPORT CLIENTS AS WORDPRESS USERS -->     
      
    <div id="wcifd-clients" class="wcifd-admin">

    	<?php 
			global $wp_roles;
			$roles = $wp_roles->get_names();   
			$users_val = (isset($_POST['wcifd-users'])) ? sanitize_text_field($_POST['wcifd-users']) : get_option('wcifd-clients-role');
		?>

	    <!--Form Clienti-->
	    <form name="wcifd-clients-import" id="wcifd-clients-import" class="wcifd-form"  method="post" enctype="multipart/form-data" action="">

			<table class="form-table">
				<tr>
					<th scope="row"><?php _e("User role", 'wcifd' ); ?></th>
					<td>
					<select class="wcifd-users-clients" name="wcifd-users-clients">
						<?php
						if($users_val) {
							echo '<option value=" ' .  $users_val . ' " selected="selected"> ' . $users_val . '</option>';	
							foreach ($roles as $key => $value) {
								if($key != $users_val) {
									echo '<option value=" ' .  $key . ' "> ' . $key . '</option>';
								}
							}
						} else {
							echo '<option value="Subscriber" selected="selected">Subscriber</option>';	
							foreach ($roles as $key => $value) {
								if($key != 'Subscriber') {
									echo '<option value=" ' .  $key . ' "> ' . $key . '</option>';
								}
							}
						} 
						?>
					</select>
					<p class="description"><?php _e('Select a Wordpress user role for your clients.', 'wcifd'); ?></p>
				</tr>

				<?php wp_nonce_field( 'wcifd-clients-import', 'wcifd-clients-nonce'); ?>
				<input type="hidden" name="clients-import" value="1">

				<tr>
					<th scope="row"><?php _e('Add clients', 'wcifd'); ?></th>
					<td>
						<input type="file" name="clients-list">
						<p class="description"><?php _e('Select your clients list file (.csv)', 'wcifd'); ?></p>
					</td>
				</tr>

			</table>

			<input type="submit" class="button-primary" value="<?php _e('Import Clients', 'wcifd'); ?>">

	    </form>

	    <?php wcifd_users('clients'); ?>
 	</div>


<!-- IMPORT ORDERS AS WOOCOMMERCE ORDERS -->

	<div id="wcifd-orders" class="wcifd-admin">
	 
	<!--Product Form-->
	<form name="wcifd-orders-import" id="wcifd-orders-import" class="wcifd-form"  method="post" enctype="multipart/form-data" action="">
		<table class="form-table">

			<?php $wcifd_orders_add_users = (isset($_POST['wcifd-orders-add-users'])) ? sanitize_text_field($_POST['wcifd-orders-add-users']) : get_option('wcifd-orders-add-users'); ?>
			<tr>
				<th scope="row"><?php _e('New customers', 'wcifd'); ?></th>
				<td>
					<select name="wcifd-orders-add-users">
						<option name="" value="0"<?php echo($wcifd_orders_add_users == 0) ? ' selected="selected"' : ''; ?>><?php _e('Don\'t create users', 'wcifd'); ?></option>
						<option name="" value="1"<?php echo($wcifd_orders_add_users == 1) ? ' selected="selected"' : ''; ?>><?php _e('Create users', 'wcifd'); ?></option>
					</select>
					<p class="description"><?php _e('Add new customers as Wordpress users', 'wcifd'); ?></p>
				</td>
			</tr>

			<?php 
			if(isset($_POST['wcifd-orders-status'])) {
				$wcifd_orders_status = strtolower(str_replace(' ', '-', sanitize_text_field($_POST['wcifd-orders-status'])));
			} else {
				$wcifd_orders_status = get_option('wcifd-orders-status'); 
			}
			?>

			<tr>
		    	<th scope="row"><?php echo __('Orders status', 'wcifd'); ?></th>
		    	<td>
			    	<select name="wcifd-orders-status">
			    		<?php
			    		$statuses = wc_get_order_statuses();
			    		foreach ($statuses as $status) {
				    		echo '<option name="' . $status . '" value="' . $status . '"';
				    		echo ($wcifd_orders_status == strtolower(str_replace(' ', '-', $status))) ? ' selected="selected">' : '>';
				    		echo __($status, 'wcifd') . '</option>';
			    		}
			    		?>
			    	</select>
			    	<p class="description"><?php echo __('Select the status that you want to assign to the imported orders.', 'wcifd'); ?></p>
		    	</td>
		    </tr>

			<?php wp_nonce_field( 'wcifd-orders-import', 'wcifd-orders-nonce'); ?>
			<input type="hidden" name="orders-import" value="1">
			<tr>
				<th scope="row"><?php _e('Add orders', 'wcifd'); ?></th>
				<td>
					<input type="file" name="orders-list">
					<p class="description"><?php _e('Select your orders list file (.xml)', 'wcifd'); ?></p>
				</td>
			</tr>
		</table>
		<input type="submit" class="button-primary" value="<?php _e('Import Orders', 'wcifd' ) ; ?>">
	</form>

	<?php wcifd_orders(); ?>

	</div>


    </div><!--WRAP-LEFT-->
	
	<div class="wrap-right">
		<iframe width="300" height="900" scrolling="no" src="http://www.ilghera.com/images/wcifd-premium-iframe.html"></iframe>
	</div>
	<div class="clear"></div>
    
 </div><!--WRAP-->
	
    
    <?php
    
}

//UPDATE MESSAGE
function wcifd_update_message2( $plugin_data, $response) {
	$key = get_option('wcifd-premium-key');

	if(!$key) {

		$message = 'A <b>Premium Key</b> is required for keeping this plugin up to date. Please, add yours in the <a href="' . admin_url() . 'admin.php/?page=wc-importer-for-danea">options page</a> or click <a href="https://www.ilghera.com/product/woocommerce-importer-for-danea-premium/" target="_blank">here</a> for prices and details.';
	
	} else {
	
		$decoded_key = explode('|', base64_decode($key));
	    $bought_date = date( 'd-m-Y', strtotime($decoded_key[1]));
	    $limit = strtotime($bought_date . ' + 365 day');
	    $now = strtotime('today');

	    if($limit < $now) { 
	        $message = 'It seems like your <strong>Premium Key</strong> is expired. Please, click <a href="https://www.ilghera.com/product/woocommerce-importer-for-danea-premium/" target="_blank">here</a> for prices and details.';
	    } elseif($decoded_key[2] != 1572) {
	    	$message = 'It seems like your <strong>Premium Key</strong> is not valid. Please, click <a href="https://www.ilghera.com/product/woocommerce-importer-for-danea-premium/" target="_blank">here</a> for prices and details.';
	    }

	}
	echo ($message) ? '<br><span class="wcifd-alert">' . $message . '</span>' : '';

}
add_action('in_plugin_update_message-wc-importer-for-danea-premium/wc-importer-for-danea-premium.php', 'wcifd_update_message2', 10, 2);