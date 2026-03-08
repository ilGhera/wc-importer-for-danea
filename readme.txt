=== ilGhera Danea Importer for WooCommerce ===
Contributors: ghera74
Tags: Fattura elettronica, Danea Easyfatt, gestionale, prodotti, sincronizzazione
Version: 1.4.2
Requires at least: 6.0
Tested up to: 6.9
WC tested up to: 10
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Import and sync Danea Easyfatt customers and suppliers with your WooCommerce store. Premium version also supports products and orders.


== Description ==

If you've built your online store with WooCommerce and you're using Danea Easyfatt as management software, you definitely need ilGhera Danea Importer for WooCommerce.
You'll be able to import client and suppliers width this free version, also products and orders with the premium one.


SOME AVAILABLE TOOLS:

* Import customers
* Customers and suppliers import now supports the fiscal fields coming from the Exporter plugin.
* (Premium) Avoid product name update with a dedicated option
* (Premium) Avoid product URL update with a dedicated option
* (Premium) Import Producer from Danea and add it to the product as attribute
* (Premium) Option for showing the Producer in front-end or not
* (Premium) Import Danea custom fields and add them to the product as attributes
* (Premium) Add a name to the custom fields coming from Danea
* (Premium) Option for showing the custom fields in front-end or not

https://youtu.be/LZ6urO531OE


== Installation ==

To install **ilGhera Danea Importer for WooCommerce**, you can follow two methods, depending on whether you install it directly from the WordPress dashboard or via manual download:

#### 1. Installation via WordPress Dashboard (Recommended Method)

    1.  From your WordPress Dashboard, navigate to Plugins > Add New.
    2.  Search for "ilGhera Danea Importer for WooCommerce".
    3.  Click "Install Now" and then "Activate".

#### 2. Manual Installation from WordPress.org (via FTP/SFTP)

    1.  Download the ilGhera Danea Importer for WooCommerce zip file from WordPress.org.
    2.  Unzip the file on your computer.
    3.  Upload the `woocommerce-importer-for-danea` folder (the unzipped folder) to your `/wp-content/plugins/` directory on your server, using your favorite FTP/SFTP client.
    4.  Once the upload is complete, go to the Plugins page in your WordPress Dashboard.
    5.  Locate "ilGhera Danea Importer for WooCommerce" and click "Activate".

Once activated, you'll find the plugin options in your WordPress in **WooCommerce > Danea Importer for WC**.


== Screenshots ==

1. Choose the user role and import your Danea EasyFatt suppliers list
2. Products import - General settings
3. Products import - Custom fields
4. Products import - Remote
5. Products import - File
6. Choose the user role and import your Danea EasyFatt client list
7. Import orders in WooCommerce
8. WooCommerce Role Based Price support


== Changelog ==

= 1.4.2 =
Release Date: 8 March 2026

    * Enhancement: (Premium) New option to import EAN/GTIN barcode from Danea (only valid numeric codes).
    * Enhancement: (Premium) Order import now supports product variations (Size/Color).
    * Enhancement: (Premium) Automatic creation of variable products with variations during order import.
    * Enhancement: (Premium) Tax calculation in order imports now uses Danea totals to avoid rounding differences.
    * Enhancement: WordPress 6.9 support 
    * Enhancement: WooCommerce 10 support 
    * Update: (Premium) ilghera-notice library to v1.2.0.
    * Update: (Premium) Plugin Update Checker.
    * Update: Translations.


= 1.4.1 =
Release Date: 29 September 2025

    * Enhancement: Plugin renamed
    * Enhancement: New plugin images
    * Enhancement: WooCommerce 10 support

= 1.4.0 =
Release Date: 13 June 2025

    * Enhancement: Added full compatibility with WooCommerce High-Performance Order Storage (HPOS) for improved order management and database efficiency.
    * Enhancement: Optimized plugin loading performance and stability by refining class instantiation timing, ensuring smoother plugin initialization.
    * Enhancement: (Premium) Introduced comprehensive options for Action Scheduler task cleanup, allowing administrators to define retention days for completed, failed, and canceled tasks, improving database hygiene.
    * Enhancement: (Premium) Implemented CRUD (Create, Read, Update, Delete) methods for products and orders, providing more robust and efficient data management capabilities.
    * Enhancement: (Premium) Enhanced product attribute synchronization; if a product attribute is removed in Danea, the plugin now automatically deletes any dependent WooCommerce product variations during synchronization, ensuring data consistency.
    * Enhancement: (Premium) Action Scheduler is now used for CSV product imports, significantly improving performance and enabling the seamless import of thousands of products.
    * Enhancement: (Premium) Improved product image synchronization, ensuring more reliable and efficient handling of images during imports and updates.
    * Enhancement: Adhered to WordPress Coding Standards for improved code quality, readability, and future maintainability.
    * Enhancement: (Premium) Enhanced Custom Field Handling: Implemented a robust logic for Danea Easyfatt custom fields (CustomField1-4).
    * Conditional Tag Appending: (Premium) Product tags are now cleared before import unless at least one custom field configured as a 'tag' explicitly has the 'append' option enabled. This ensures precise control over existing tags.
    * Optimized Saving: (Premium) Product attributes and tags are now saved only once at the end of the `danea_custom_fields` method, improving performance and data consistency.
    * Enhancement: (Premium) Detailed Synchronization Logging: Added comprehensive information about the synchronization process to the plugin's log for better monitoring and troubleshooting.
    * Enhancement: (Premium) Progress Bar Enhancements: The progress bar now accurately reflects the advancement of product deletion during synchronization, providing a clearer overview of the import process.
    * Update: (Premium) Action Scheduler
    * Update: (Premium) Plugin Update Checker
    * Update: Translations

= 1.3.2 =
Release Date: 7 October 2024

    * Enhancement: Plugin renamed
    * Enhancement: New plugin images
    * Enhancement: WordPress 6.6 support
    * Enhancement: WooCommerce 9.3.3 support


= 1.3.1 =
Release Date: 18 September 2023

    * Enhancement: (Premium) Progress bar on products import
    * Enhancement: WordPress translation system
    * Update: (Premium) Action Scheduler
    * Update: (Premium) Plugin Update Checker
    * Update: (Premium) ilGhera admin notice
    * Update: Translations
    * Bug fix: Unencoded characters in plugin's options page
    * Bug fix: (Premium) Unencoded characters in Danea's error message
    * Bug fix: (Premium) New products attributes not created during the import


= 1.3.0 =
Release Date: 1 August 2023

    * Enhancement: (Premium) Danea Notes field as short product description
    * Enhancement: (Premium) Import Danea custom fields as product tags
    * Enhancement: (Premium) Split Danea custom fields values by commas and create tags/attributes
    * Enhancement: (Premium) Option append tags on importing Danea custom fields
    * Enhancement: Better user interface
    * Enhancement: WordPress Coding Standard
    * Enhancement: (Premium) More details in log file in case of products/variations not imported/updated
    * Update: (Premium) Action Scheduler
    * Update: (Premium) Plugin Update Checker
    * Update: Translations
    * Bug fix: (Premium) Custom label not assigned to Danea custom fields imported


= 1.2.1 =
Release Date: 16 March 2023

    * Update: (Premium) Action Scheduler
    * Update: (Premium) Plugin Update Checker
    * Update: (Premium) License notice
    * Bug fix: Error importing users by CSV file with contents in multiple lines


= 1.2.0 =
Release Date: 7 October 2021

    * Enhancement: Replace all WooCommerce products in case of full update coming from Danea Easyfatt
    * Enhancement: Danea "Notes" fied content can now be used in place of "HTML description" if missing
    * Bug fix: Special characters not allowed in product title.


= 1.1.3 =
Release Date: 5 March 2021

    * Enhancement: (Premium) Import and display in front-end the supplier name
    * Enhancement: (Premium) Import and display in front-end the supplier product code
    * Bug fix: (Premium) Import variable products with no sku via CSV
    * Bug fix: (Premium) No update of variations based on custom attributes previously imported via CSV


= 1.1.2 =
Release Date: 10 February 2021

    * Enhancement: (Premium) Exclude new products import if not in stock
    * Enhancement: (Premium) Exclude variations prices from products update


= 1.1.1 =
Release Date: 19 April 2020

    * Enhancement: (Premium) Action Scheduler library is now used to synchronize products
    * Enhancement: (Premium) Exclude product URL from updates


= 1.1.0 =
Release Date: 05 February 2020

    * Enhancement: Import customers
    * Enhancement: Customers and suppliers import now supports the fiscal fields coming from the Exporter plugin.
    * Enhancement: New product sub-menu for a better navigation
    * Enhancement: (Premium) Avoid product name update with a dedicated option
    * Enhancement: (Premium) Import Producer from Danea and add it to the product as attribute
    * Enhancement: (Premium) Option for showing the Producer in front-end or not
    * Enhancement: (Premium) Import Danea custom fields and add them to the product as attributes
    * Enhancement: (Premium) Add a name to the custom fields coming from Danea
    * Enhancement: (Premium) Option for showing the custom fields in front-end or not


= 1.0.0 =
Release Date: 24 January, 2018

    * Enhancement: Update users imported if already present.
    * Enhancement: (Premium) Danea tax classes imported during synchronization.
    * Enhancement: (Premium) Choose which Danea menu list use for the WooCommerce regular price, and a second one for the sell price.
    * Enhancement: (Premium) Import product weight and dimension from Danea, gross or net.
    * Enhancement: (Premium) Use part of the Danea product description for the short description in WooCommerce.
    * Enhancement: (Premium) Exclude product description in update synchronizations.
    * Enhancement: (Premium) New products imported can now be published directly.
    * Enhancement: (Premium) New plugin update checker.
    * Bug fix: PHP Notices


= 0.9.4 =
Release Date: 11 April, 2016

    * Enhancement: Shop manager can now handle the plugin options.
    * Enhancement: Better tabs navigation.
    * Enhancement: (Premium) Now you can import/ update products directly from Danea (Ctrl+P)
    * Enhancement: (Premium) Danea sizes and colors now are imported as WooCommerce variations.
    * Enhancement: (Premium) Choose if import also product images.
    * Enhancement: (Premium) WooCommerce variations previously exported, now are imported correctly linked to the parent product.
    * Enhancement: (Premium) Now are imported also the subcategories.
    * Enhancement: (Premium) Using the supplier as post author is now an option.


= 0.9.1 =
Release Date: 06 November, 2016

    * Enhancement: If the Company field is presents, the name will be moved to referent.
    * Enhancement: Added the Shipping address.
    * Enhancement: Fiscal code and P.IVA fields are now recognized by checking the specific plugin installed.


= 0.9.0 =
Release Date: 10 October, 2016

    * First release
