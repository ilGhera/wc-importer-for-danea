<?php
/*
WOOCOMMERCE IMPORTER FOR DANEA | ADMIN FUNCTIONS
*/


add_action( 'admin_init', 'wcifd_register_style' );
add_action( 'admin_menu', 'wcifd_add_menu' );

add_action( 'admin_init', 'wcifd_register_js_menu' );
add_action( 'admin_menu', 'wcifd_js_menu' );


//CREATE WCIFD STYLE
function wcifd_register_style() {
	wp_register_style( 'wcifd-style', plugins_url('css/wc-importer-for-danea.css', 'wc-importer-for-danea/css'));
}

function wcifd_add_style() {
	wp_enqueue_style( 'wcifd-style');
}


//CALL THE MENU NAVIGATION SCRIPT
function wcifd_register_js_menu() {
	wp_register_script('wcifd-admin-nav', plugins_url('js/wcifd-admin-nav.js', 'wc-importer-for-danea/js'), array('jquery'), '1.0', true );
}

function wcifd_js_menu() {
	wp_enqueue_script('wcifd-admin-nav');
}


//MENU
function wcifd_add_menu() {
	$wcifd_page = add_submenu_page( 'woocommerce','WCIFD Options', 'WC Importer for Danea', 'manage_woocommerce', 'wc-importer-for-danea', 'wcifd_options');
	
	//Richiamo lo style per wcifd
	add_action( 'admin_print_styles-' . $wcifd_page, 'wcifd_add_style' );
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
		echo "<h1 class=\"wcifd main\">" . __( 'Woocommmerce Importer for Danea', 'wcifd' ) . "<span style=\"font-size:60%;\"> 1.0.0</span></h1>";
	?>
	</div>
	        
	<div class="icon32 icon32-woocommerce-settings" id="icon-woocommerce"><br /></div>
	  <h2 id="wcifd-admin-menu" class="nav-tab-wrapper woo-nav-tab-wrapper">
        <a href="#" data-link="wcifd-suppliers" class="nav-tab nav-tab-active" onclick="return false;"><?php echo __('Suppliers', 'wcifd'); ?></a>
		<a href="#" data-link="wcifd-products" class="nav-tab premium" onclick="return false;"><?php echo __('Products', 'wcifd'); ?></a>
        <a href="#" data-link="wcifd-clients" class="nav-tab premium" onclick="return false;"><?php echo __('Clients', 'wcifd'); ?></a>    
        <a href="#" data-link="wcifd-orders" class="nav-tab premium" onclick="return false;"><?php echo __('Orders', 'wcifd'); ?></a>
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
				<tr>
					<th scope="row"><?php echo __('Prices imported with tax', 'wcifd'); ?></th>
					<td>
						<select name="tax-included" class="wcifd" disabled="disabled">
							<option value="1"><?php echo __(' Yes, I will import prices inclusive of tax', 'wcifd'); ?></option>
						</select>
						<p class="description">
							<?php echo __('In Danea you can choose if export prices with tax included or not. What are you going to import?', 'wcifd'); ?>
				 			 <a href="https://www.ilghera.com/product/woocommerce-importer-for-danea-premium/" target="_blank">Upgrade</a>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php echo __('Regular price', 'wcifd'); ?></th>
					<td>
						<select name="regular-price-list" class="wcifd" disabled="disabled">
							<?php echo '<option value="">' . __('Price list ', 'wcifd') . '1</option>'; ?>
						</select>
						<p class="description">
							<?php echo __('The Danea price list to use for Woocommerce regular price.', 'wcifd'); ?>
				 			<a href="https://www.ilghera.com/product/woocommerce-importer-for-danea-premium/" target="_blank">Upgrade</a>	
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php echo __('Sale price', 'wcifd'); ?></th>
					<td>
						<select name="sale-price-list" class="wcifd" disabled="disabled">
							<?php echo '<option>' . __('Select a price list', 'wcifd') . '</option>'; ?>
						</select>
						<p class="description">
							<?php echo __('The Danea price list to use for Woocommerce sale price.', 'wcifd'); ?>
				 			<a href="https://www.ilghera.com/product/woocommerce-importer-for-danea-premium/" target="_blank">Upgrade</a>	
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php echo __('Product size type', 'wcifd'); ?></th>
					<td>
						<select name="wcifd-size-type" class="wcifd" disabled="disabled">
							<option value="gross-size" selected="selected"><?php echo __('Gross size', 'wcifd'); ?></option>
						</select>
						<p class="description">
							<?php echo __('Chose if import gross or net product size.', 'wcifd'); ?>
				 			<a href="https://www.ilghera.com/product/woocommerce-importer-for-danea-premium/" target="_blank">Upgrade</a>	
						</p>
					</td>
				</tr>
				<tr>
				<tr>
					<th scope="row"><?php echo __('Product weight type', 'wcifd'); ?></th>
					<td>
						<select name="wcifd-weight-type" class="wcifd" disabled="disabled">
							<option value="gross-weight" selected="selected"><?php echo __('Gross weight', 'wcifd'); ?></option>
						</select>
						<p class="description">
							<?php echo __('Chose if import gross or net product weight.', 'wcifd'); ?>
				 			<a href="https://www.ilghera.com/product/woocommerce-importer-for-danea-premium/" target="_blank">Upgrade</a>	
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php echo __('Short description', 'wcifd'); ?></th>
					<td>
						<input type="checkbox" name="short-description" value="" disabled="disabled">
						<?php echo __('Use the excerpt as short product description.', 'wcifd'); ?>
			 			<a href="https://www.ilghera.com/product/woocommerce-importer-for-danea-premium/" target="_blank">Upgrade</a>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php echo __('Exclude product description', 'wcifd'); ?></th>
					<td>
						<input type="checkbox" name="exclude-description" value="" disabled="disabled">
						<?php echo __('Exclude descriptions for products updates.', 'wcifd'); ?>
			 			<a href="https://www.ilghera.com/product/woocommerce-importer-for-danea-premium/" target="_blank">Upgrade</a>
					</td>
				</tr>
				<tr>
	    			<th scope="row"><?php _e("Suppliers", 'wcifd' ); ?></th>
	    			<td>
	    				<fieldset>
		    				<label for="wcifd-use-suppliers">
								<input type="checkbox" class="wcifd-use-suppliers" name="wcifd-use-suppliers" value="1" checked="checked" disabled="disabled">
								<?php echo __('Use the product supplier as post author.', 'wcifd'); ?>
					 			<a href="https://www.ilghera.com/product/woocommerce-importer-for-danea-premium/" target="_blank">Upgrade</a>
		    				</label>
		    			</fieldset>
	    			</td>
	    		</tr>
	    		<tr>
					<th scope="row"><?php echo __('Publish new products', 'wcifd'); ?></th>
					<td>
						<input type="checkbox" name="publish-new-products" value="0" checked="checked" disabled="disabled">
						<?php echo __('Publish new products directly.', 'wcifd'); ?>
			 			<a href="https://www.ilghera.com/product/woocommerce-importer-for-danea-premium/" target="_blank">Upgrade</a>
					</td>
				</tr>
			</table>
			<input type="submit" class="button-primary" style="margin-top: 1.5rem;" disabled="disabled" value="<?php _e('Save Changes', 'wcifd' ) ; ?>">
		</form>

		<form name="wcifd-receive-products" id="wcifd-receive-products" class="wcifd-form one-of" method="post" action="">
			
			<h2 class="title"><?php echo __('Receive products from Danea', 'wcifd'); ?></h2>

			<p>
				<?php 
				echo __('Receive products directly from the XML sent by Danea via HTTP post.', 'wcifd') . '<br>'; 
				?>
			</p>

			<table class="form-table">
				<tr>
					<th scope="row"><?php echo __('URL', 'wcifd'); ?></th>
					<td>
						<div class="wcifd-copy-url"><span class="wcifd-red"><?php echo __('Please insert your <strong>Premium Key</strong>', 'wcifd'); ?></span></div>
						<p class="description">
							<?php echo __('Add this URL to the <strong>Settings</strong> tab of the <strong>Products update</strong> function in Danea.', 'wcifd'); ?>
				 			 <a href="https://www.ilghera.com/product/woocommerce-importer-for-danea-premium/" target="_blank">Upgrade</a>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php echo __('Import images', 'wcifd'); ?></th>
					<td>
						<input type="checkbox" class="wcifd-import-images" name="wcifd-import-images" value="1" checked="checked" disabled="disabled">
						<?php echo __('Import products images from Danea.', 'wcifd'); ?>
			 			 <a href="https://www.ilghera.com/product/woocommerce-importer-for-danea-premium/" target="_blank">Upgrade</a>
					</td>
				</tr>
			</table>
			<input type="submit" class="button-primary" style="margin-top: 1.5rem;" disabled="disabled" value="<?php _e('Save Changes', 'wcifd' ) ; ?>">
		</form>

		<form name="wcifd-products-import" id="wcifd-products-import" class="wcifd-form one-of"  method="post" enctype="multipart/form-data" action="">

			<h2 class="title"><?php echo __('Import products from a file', 'wcifd'); ?></h2>

			<table class="form-table">
				<tr>
					<th scoper="row"><?php echo __('Products update', 'wcifd'); ?></th>
					<td>
						<select name="update-products" disabled="disabled">
								<option value="1" selected="selected"><?php echo __('Update products', 'wcifd'); ?></option>
						</select>
						<p class="description">
							<?php echo __('Do you want to update the products already present in Woocommerce?', 'wcifd'); ?>
				 			 <a href="https://www.ilghera.com/product/woocommerce-importer-for-danea-premium/" target="_blank">Upgrade</a>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e('Add products', 'wcifd'); ?></th>
					<td>
						<input type="file" name="products-list" disabled="disabled">
						<p class="description">
							<?php _e('Select your products list file (.csv)', 'wcifd'); ?>
				 			 <a href="https://www.ilghera.com/product/woocommerce-importer-for-danea-premium/" target="_blank">Upgrade</a>
						</p>
					</td>
				</tr>
			</table>
			<input type="submit" class="button-primary" disabled="disabled" value="<?php _e('Import Products', 'wcifd' ) ; ?>">
		</form>

	</div>
    

<!-- IMPORT CLIENTS AS WORDPRESS USERS -->     
      
    <div id="wcifd-clients" class="wcifd-admin">

	    <!--Form Clienti-->
	    <form name="wcifd-clients-import" id="wcifd-clients-import" class="wcifd-form"  method="post" enctype="multipart/form-data" action="">

			<table class="form-table">
				<tr>
					<th scope="row"><?php _e("User role", 'wcifd' ); ?></th>
					<td>
					<select class="wcifd-users-clients" name="wcifd-users-clients" disabled="disabled">
						<option value="Subscriber" selected="selected">Subscriber</option>
					</select>
					<p class="description">
						<?php _e('Select a Wordpress user role for your clients.', 'wcifd'); ?>
			 			 <a href="https://www.ilghera.com/product/woocommerce-importer-for-danea-premium/" target="_blank">Upgrade</a>
					</p>
				</tr>
				<tr>
					<th scope="row"><?php _e('Add clients', 'wcifd'); ?></th>
					<td>
						<input type="file" name="clients-list" disabled="disabled">
						<p class="description">
							<?php _e('Select your clients list file (.csv)', 'wcifd'); ?>
				 			 <a href="https://www.ilghera.com/product/woocommerce-importer-for-danea-premium/" target="_blank">Upgrade</a>
						</p>
					</td>
				</tr>

			</table>

			<input type="submit" class="button-primary" disabled="disabled" value="<?php _e('Import Clients', 'wcifd'); ?>">

	    </form>

 	</div>


<!-- IMPORT ORDERS AS WOOCOMMERCE ORDERS -->

	<div id="wcifd-orders" class="wcifd-admin">
	 
	<!--Product Form-->
	<form name="wcifd-orders-import" id="wcifd-orders-import" class="wcifd-form"  method="post" enctype="multipart/form-data" action="">
		<table class="form-table">
			<tr>
				<th scope="row"><?php _e('New customers', 'wcifd'); ?></th>
				<td>
					<select name="wcifd-orders-add-users" disabled="disabled">
						<option name="" value="0" selected="selected"><?php _e('Don\'t create users', 'wcifd'); ?></option>
					</select>
					<p class="description">
						<?php _e('Add new customers as Wordpress users', 'wcifd'); ?>
			 			 <a href="https://www.ilghera.com/product/woocommerce-importer-for-danea-premium/" target="_blank">Upgrade</a>
					</p>
				</td>
			</tr>
			<tr>
		    	<th scope="row"><?php echo __('Orders status', 'wcifd'); ?></th>
		    	<td>
			    	<select name="wcifd-orders-status" disabled="disabled">
			    		<option name="completed" value="completed">Completed</option>';
			    		}
			    		?>
			    	</select>
			    	<p class="description">
			    		<?php echo __('Select the status that you want to assign to the imported orders.', 'wcifd'); ?>
			 			 <a href="https://www.ilghera.com/product/woocommerce-importer-for-danea-premium/" target="_blank">Upgrade</a>
			    	</p>
		    	</td>
		    </tr>
			<tr>
				<th scope="row"><?php _e('Add orders', 'wcifd'); ?></th>
				<td>
					<input type="file" name="orders-list" disabled="disabled">
					<p class="description">
						<?php _e('Select your orders list file (.xml)', 'wcifd'); ?>
			 			 <a href="https://www.ilghera.com/product/woocommerce-importer-for-danea-premium/" target="_blank">Upgrade</a>
					</p>
				</td>
			</tr>
		</table>
		<input type="submit" class="button-primary" disabled="disabled" value="<?php _e('Import Orders', 'wcifd' ) ; ?>">
	</form>

	</div>

    </div><!--WRAP-LEFT-->
	
	<div class="wrap-right">
		<iframe width="300" height="1000" scrolling="no" src="http://www.ilghera.com/images/wcifd-iframe.html"></iframe>
	</div>

	<div class="clear"></div>
    
 </div><!--WRAP-->
	
    
    <?php
    
}