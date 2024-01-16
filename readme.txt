=== WooCommerce Importer for Danea - Premium ===
Contributors: ghera74
Tags: Fattura elettronica, WooCommerce, Danea Easyfatt, ecommerce, exporter, csv, shop, orders, products, gestionale
Version: 1.6.4
Requires at least: 4.0
Tested up to: 6.4


Import suppliers, clients and products, from Danea Easyfatt into your WooCommerce store.

== Description ==

Se hai realizzato il tuo negozio online con WooCommerce ed utilizzi Danea Easyfatt come gestionale, WooCommerce Importer per Danea è lo strumento che ti serve perché le due piattaforme siano in grado di comunicare.

*Software certificato Danea Easyfatt*

WooCommerce Importer for Danea - Premium ti permette di importare:

* L'elenco dei fornitori, sotto forma di utenti Wordpress a cui si è assegnato un ruolo specifico (CSV).
* L'elenco dei clienti, sotto forma di utenti Wordpress a cui si è assegnato un ruolo specifico (CSV).
* L'elenco dei prodotti, sotto forma di prodotti WooCommerce (CSV).
* L'elenco dei prodotti, con relative variazioni taglie/colori e immagini, attraverso la ricezione di un POST HTTP inviato da Danea Easyfatt.
* L'elenco degli ordini, con creazione automatica dei prodotti mancanti ed inserimento opzionale dei nuovi clienti.


**ENGLISH**

If you've built your online store with WooCommerce and you're using Danea Easyfatt as management software, you definitely need WooCommerce Importer for Danea - Premium!
You'll be able to import suppliers, clients and products.

*Danea Easyfatt certified*

== Installation ==

* Upload the ‘woocommerce-importer-for-danea-premium’ directory to your ‘/wp-content/plugins/’ directory, using your favorite method (ftp, sftp, scp, etc…)
* Activate WooCommerce Importer for Danea – Premium from your Plugins page.
* Once Activated, go to WooCommerce/ WC Importer for Danea.

----

* Per installare WooCommerce Importer for Danea, dalla Bacheca del tuo sito Wordpress vai alla voce Plugin/ Aggiungi nuovo.
* Clicca sul pulsante "Carica plugin" e seleziona la cartella compressa appena scaricata.
* Completato il processo di installazione, troverai nel menù WooCommerce la pagina opzioni con tutte le informazioni necessarie all'utilizzo di WooCommerce Importer for Danea - Premium.



== Changelog ==

= 1.6.4 =
Release Date: 16 January 2024 

* Enhancement: Auto deletion of orphan SKUs 
* Enhancement: Management of the single product unit of measurement 
* Update: Plugin Update Checker 
* Bug fix: Creation of dynamic property deprecated in PHP 8.2


= 1.6.3 =
Release Date: 23 October 2023 

* Enhancement: Products update with CRUD methods 
* Bug fix: Lookup table update missed 


= 1.6.2 =
Release Date: 20 September 2023 

* Bug fix: Product replacement not working 


= 1.6.1 =
Release Date: 18 September 2023 

* Enhancement: Progress bar on products import 
* Enhancement: WordPress translation system 
* Update: Action Scheduler 
* Update: Plugin Update Checker 
* Update: Translations
* Update: ilGhera admin notice 
* Bug fix: Unencoded characters in plugin's options page
* Bug fix: Unencoded characters in Danea's error message
* Bug fix: New products attributes not created during the import 


= 1.6.0 =
Release Date: 7 July 2023 

* Enhancement: Danea Notes field as short product description 
* Enhancement: Import Danea custom fields as product tags
* Enhancement: Split Danea custom fields values by commas and create tags/attributes 
* Enhancement: Option append tags on importing Danea custom fields 
* Enhancement: Better user interface 
* Enhancement: WordPress Coding Standard 
* Enhancement: More details in log file in case of products/variations not imported/updated 
* Update: Action Scheduler 
* Update: Plugin Update Checker 
* Update: Translations
* Bug fix: Custom label not assigned to Danea custom fields imported 


= 1.5.6 =
Release Date: 15 March 2023

* Update: Action Scheduler 
* Update: Plugin Update Checker 
* Update: License notice 
* Bug fix: Error importing users by CSV file with contents in multiple lines  


= 1.5.5 =
Release Date: 14 February 2023

* Enhancement: New license notice 
* Update: Action Scheduler 


= 1.5.4 =
Release Date: 7 November 2022

* Enhancement: WordPress 6.1 support 
* Enhancement: PUC v5 support 
* Update: Plugin Update Checker


= 1.5.3 =
Release Date: 28 October 2022

* Update: Action Scheduler
* Update: Plugin Update Checker


= 1.5.2 =
Release Date: 20 November 2021

* Bug fix: Error deleting all products with database prefix different then wp_


= 1.5.1 =
Release Date: 29 October 2021

* Bug fix: Backslashes not allowed in product sku.


= 1.5.0 =
Release Date: 7 October 2021

* Enhancement: Replace all WooCommerce products in case of full update coming from Danea Easyfatt
* Enhancement: Danea "Notes" fied content can now be used in place of "HTML description" if missing
* Bug fix: Special characters not allowed in product title.


= 1.4.1 =
Release Date: 4 March 2021

* Enhancement: Import and display in front-end the supplier name 
* Enhancement: Import and display in front-end the supplier product code
* Bug fix: Import variable products with no sku via CSV
* Bug fix: No update of variations based on custom attributes previously imported via CSV


= 1.4.0 =
Release Date: 09 February 2021

* Enhancement: Exclude new products import if not in stock
* Enhancement: Exclude variations prices from products update  


= 1.3.10 =
Release Date: 09 July 2020

* Bug fix: Products shown as out of stock even with the WooCommerce manage stock option disabled


= 1.3.9 =
Release Date: 24 May 2020

* Bug fix: WooCoomerce Role Based Price values don't change in frontend because saved in transients


= 1.3.8 =
Release Date: 20 May 2020

* Bug fix: WooCoomerce Role Based Price values not importerd in product variations


= 1.3.7 =
Release Date: 15 May 2020

* Bug fix: PHP Warning:  preg_match() expects parameter 2 to be string, array given in .../wp-includes/formatting.php on line 1604
* Bug fix: PHP Warning:  strip_tags() expects parameter 1 to be string, array given in .../wp-includes/formatting.php on line 2232


= 1.3.6 =
Release Date: 13 May 2020

* Bug fix: Database tables not created on plugin activation
* Bug fix: PHP Notice: Array to string conversion... importing product tax name


= 1.3.5 =
Release Date: 08 May 2020

* Bug fix: Cannot declare class ActionScheduler, because the name is already in use in...


= 1.3.4 =
Release Date: 27 April 2020

* Bug fix: WooCommerce Role Based Price data not set


= 1.3.3 =
Release Date: 24 April 2020

* Bug fix: Product variations missed


= 1.3.2 =
Release Date: 23 April 2020

* Bug fix: Price and measurements of products not transferred


= 1.3.1 =
Release Date: 22 April 2020

* Enhancement: Improved images import
* Enhancement: Improved error handling 
* Bug fix: PHP Fatal error: ActionScheduler_Action::$args too long. To ensure the args column can be indexed, action args should not be more than %d characters when encoded as JSON.


= 1.3.0 =
Release Date: 19 April 2020

* Enhancement: Action Scheduler library is now used to synchronize products
* Enhancement: Exclude product URL from updates


= 1.2.2 =
Release Date: 10 March 2020

* Bug fix: Update of the wrong product in case of WordPress ID equal to the sku received by Danea Easyfatt


= 1.2.1 =
Release Date: 04 February 2020

* Bug fix: Cron activity not deleted if the product image was not found
* Bug fix: Italian translation missed


= 1.2.0 =
Release Date: 04 February 2020

* Enhancement: Avoid product name update with a dedicated option
* Enhancement: Customers and suppliers import now supports the fiscal fields coming from the Exporter plugin.
* Enhancement: Import Producer from Danea and add it to the product as attribute
* Enhancement: Option for showing the Producer in front-end or not
* Enhancement: Import Danea custom fields and add them to the product as attributes
* Enhancement: Add a name to the custom fields coming from Danea 
* Enhancement: Option for showing the custom fields in front-end or not
* Enhancement: New product sub-menu for a better navigation 
* Bug fix: Images not deleted in WordPress when removed in Danea Easyfatt 


= 1.1.7 =
Release Date: 27 June 2019

* Bug fix: With WC version >= 3.6.0 products out of stock even if available


= 1.1.6 =
Release Date: 15 April 2019

* Bug fix: Product variations not imported when all with zero quantity available


= 1.1.5 =
Release Date: 07 March 2019

* Enhancement: Added WooCommerce Role Based Price support
* Enhancement: Improved csv products import
* Bug fix: Images disappear on products update


= 1.1.4 =
Release Date: 04 March 2019

* Enhancement: Import/ update products from an .xml file
* Bug fix: Single variations not created with missing parent products' prices
* Bug fix: HTML tags in product short description


= 1.1.3 =
Release Date: 14 February 2019

* Bug fix: WooCommerce product variations previously exported not assigned to the parent product


= 1.1.2 =
Release Date: 07 February 2019

* Bug fix: Images received but not assigned to the rispective products


= 1.1.1 =
Release Date: 25 January 2019

* Bug fix: "Indirizzo web inesistente" on sending products images from Danea Easyfatt


= 1.1.0 =
Release Date: 24 January 2019

* Enhancement: Add/ Delete products based on the Danea Easyfatt E-commerce option
* Enhancement: Option for updating products in trash
* Enhancement: Import of all the subcategories set in Danea Easyfatt
* Enhancement: Option for deleting product categories previously removed in danea Easyfatt 
* Enhancement: Better user interface
* Enhancement: General code improvement
* Bug fix: Server timeout while importing products
* Bug fix: Duplicated product images 
* Bug fix: Image not linked to his product in some specific cases 
* Bug fix: Fatal error while assigning subcategories to the imported products
* Bug fix: backorder option lost with synchronization


= 1.0.2 =
Release Date: 19 December, 2018

* Bug fix: Wrong category assigned with taxonomy terms with the same name.
* Bug fix: Error in case Danea "Note" field is used by the user for a different purpose.


= 1.0.1 =
Release Date: 20 April, 2018

* Enhancement: Tax classes imported are now added as single aliquots for a better assignment to products.
* Enhancement: Update users imported if already present.
* Bug fix: Products prices not imported for certain price lists.


= 1.0.0 =
Release Date: 29 December, 2017

* Enhancement: Danea tax classes imported during synchronization.
* Enhancement: Choose which Danea menu list use for the WooCommerce regular price, and a second one for the sell price.
* Enhancement: Import product weight and dimension from Danea, gross or net. 
* Enhancement: Use part of the Danea product description for the short description in WooCommerce.
* Enhancement: Exclude product description in update synchronizations.
* Enhancement: New products imported can now be published directly.
* Enhancement: New plugin update checker.
* Bug fix: Different PHP Notices.
* Bug fix: Warning PHP in wcifd-functions.php on line 246, with different tax class than 22.
* Bug fix: Variable product attributes created in WooCommerce lost after synchronizations.
* Bug fix: Post meta wcifd-danea-size-color duplications.


= 0.9.6 =
Release Date: 21 July, 2016

* Bug fix: Missed subcategory importing product by csv.
* Bug fix: Wrong subcategory assigned if different terms have the same name.
* Bug fix: Category names in lowercase.
* Bug fix: Attribute names in lowercase. Danea variation products (size & color) not recognized.
* Bug fix: Server timeout during products import.


= 0.9.5 =
Release Date: 13 April, 2016

* Bug fix: Product images imported but not attached to the parent articles.


= 0.9.4 =
Release Date: 11 April, 2016

* Enhancement: Now you can import/ update products directly from Danea (Ctrl+P)
* Enhancement: Danea sizes and colors now are imported as WooCommerce variations.
* Enhancement: Choose if import also product images.
* Enhancement: WooCommerce variations previously exported, now are imported correctly linked to the parent product.
* Enhancement: Now are imported also the subcategories.
* Enhancement: Shop manager can now handle the plugin options.
* Enhancement: Using the supplier as post author is now an option.
* Bug fix: Product short description deleted after update.


= 0.9.3 =
Release Date: 21 December, 2016

* Enhancement: During the products import, now you can update what is already present in WooCommerce.


= 0.9.2 =
Release Date: 16 November, 2016

* Enhancement: Now is possible select if import products with prices inclusive tax or not.
* Bug fix: The product description was not get from the csv
* Bug fix: Products imported not visible in frontend


= 0.9.1 =
Release Date: 06 November, 2016

* Enhancement: The system now searches for existing products by them sku
* Enhancement: Fiscal code and P.IVA fields are now recognized by checking the specific plugin installed.
* Enhancement: Now you can import in WooCommerce the orders made in Danea EasyFatt
* Enhancement: During the orders import, if new products are found will be added as WooCommerce items, with "Imported" category assigned.
* Enhancement: You can choose if add new customers found during the orders import, as Wordpress users.
* Enhancement: You can set the order status for your imported orders.


= 0.9.0 =
Release Date: 07 October, 2016

* First release
