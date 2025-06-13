<?php
/**
 * Products template
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/admin
 *
 * @since 1.4.0
 */

defined( 'ABSPATH' ) || exit;

$tax_included               = ''; 
$use_suppliers              = ''; 
$display_producer           = ''; 
$display_supplier           = ''; 
$display_sup_product_code   = ''; 
$regular_price_list         = ''; 
$sale_price_list            = ''; 
$size_type                  = ''; 
$weight_type                = ''; 
$notes_as_description       = ''; 
$short_description          = ''; 
$exclude_description        = ''; 
$exclude_title              = ''; 
$exclude_url                = ''; 
$deleting_categories        = ''; 
$deleted_products           = ''; 
$replace_products           = ''; 
$products_variations_prices = ''; 
$products_not_available     = ''; 
$publish_new_products       = ''; 
?>

<div id="wcifd-products" class="wcifd-admin">

	<ul class="subsubsub wcifd">
		<li><a class="current" data-link="wcifd-products-general"><?php esc_html_e( 'General', 'wc-importer-for-danea' ); ?></a> | </li>
		<li><a data-link="wcifd-products-fields"><?php esc_html_e( 'Custom fields', 'wc-importer-for-danea' ); ?></a> | </li>
		<li><a data-link="wcifd-products-remote"><?php esc_html_e( 'Remote', 'wc-importer-for-danea' ); ?></a> | </li>
		<li><a data-link="wcifd-products-file"><?php esc_html_e( 'Import file', 'wc-importer-for-danea' ); ?></a></li>
	</ul>

	<div class="clear"></div>

	<?php
	require 'wcifd-products-general-template.php';
	require 'wcifd-products-custom-fields-template.php';
	require 'wcifd-products-remote-template.php';
	require 'wcifd-products-file-template.php';
	?>

</div>

