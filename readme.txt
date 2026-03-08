=== ilGhera Danea Importer for WooCommerce - Premium ===
Contributors: ghera74
Tags: Fattura elettronica, Danea Easyfatt, gestionale, prodotti, sincronizzazione
Version: 1.7.5
Requires at least: 6.0
Tested up to: 6.8
WC tested up to: 9


Import suppliers, clients and products, from Danea Easyfatt into your WooCommerce store.

== Description ==

Se hai realizzato il tuo negozio online con WooCommerce ed utilizzi Danea Easyfatt come gestionale, **ilGhera Danea Importer for WooCommerce - Premium** è lo strumento indispensabile per far comunicare efficacemente le due piattaforme. Il nostro software è **certificato Danea Easyfatt**, garantendo la massima affidabilità e un'integrazione fluida.

ilGhera Danea Importer for WooCommerce - Premium ti permette di importare e sincronizzare facilmente i tuoi dati:

* **Fornitori:** Importa l'elenco dei fornitori direttamente in WordPress come utenti, assegnando loro un ruolo specifico (tramite file CSV). Questo ti consente una gestione centralizzata e flessibile dei tuoi contatti commerciali.
* **Clienti:** Carica l'elenco dei tuoi clienti da Danea, anche loro come utenti WordPress con un ruolo dedicato (tramite file CSV), semplificando la gestione del tuo database clienti e la loro interazione con il negozio.
* **Prodotti (CSV):** Sincronizza l'intero elenco dei tuoi prodotti da Danea Easyfatt direttamente in WooCommerce (tramite file CSV), permettendoti di aggiornare le schede prodotto esistenti o creare nuove voci di catalogo in modo rapido.
* **Prodotti (POST HTTP con Variazioni e Immagini):** Ottieni la massima automazione per i tuoi aggiornamenti di catalogo. Questo metodo ti consente di sincronizzare i tuoi prodotti in tempo reale, comprese le variazioni di taglie/colori e le immagini associate, grazie alla ricezione di un POST HTTP inviato direttamente da Danea Easyfatt.
* **Ordini:** Importa l'elenco degli ordini da Danea direttamente in WooCommerce. Il plugin gestisce automaticamente la creazione dei prodotti mancanti all'interno degli ordini e ti offre l'opzione di inserire i nuovi clienti, garantendo una completa e accurata tracciabilità delle transazioni.

**ENGLISH**

If you've built your online store with WooCommerce and you're using Danea Easyfatt as management software, you definitely need **ilGhera Danea Importer for WooCommerce - Premium**! Our software is **Danea Easyfatt certified**, ensuring maximum reliability and seamless integration.

ilGhera Danea Importer for WooCommerce - Premium allows you to easily import and synchronize your data:

* **Suppliers:** Import your supplier list directly into WordPress as users, assigning them a specific role (via CSV file). This allows for centralized and flexible management of your business contacts.
* **Clients:** Upload your client list from Danea, also as WordPress users with a dedicated role (via CSV file), simplifying your customer database management and their interaction with the store.
* **Products (CSV):** Synchronize your entire product list from Danea Easyfatt directly into WooCommerce (via CSV file), allowing you to quickly update existing product sheets or create new catalog entries.
* **Products (HTTP POST with Variations and Images):** Achieve maximum automation for your catalog updates. This method allows you to synchronize your products in real-time, including size/color variations and associated images, by receiving an HTTP POST sent directly from Danea Easyfatt.
* **Orders:** Import your order list from Danea directly into WooCommerce. The plugin automatically handles the creation of missing products within orders and offers the option to add new clients, ensuring complete and accurate transaction traceability.


== Installation ==

Per installare **ilGhera Danea Importer for WooCommerce - Premium**, puoi seguire due metodi:

#### 1. Installazione tramite Bacheca di WordPress (Metodo Consigliato)

    1.  Dalla Bacheca del tuo sito WordPress, naviga su "Plugin > Aggiungi nuovo".
    2.  Clicca sul pulsante "Carica plugin" in cima alla pagina.
    3.  Seleziona il file zip compresso di "ilGhera Danea Importer for WooCommerce - Premium" che hai scaricato.
    4.  Completa il processo di installazione e attiva il plugin.

#### 2. Installazione Manuale (via FTP/SFTP)

    1.  Decomprimi il file zip di "ilGhera Danea Importer for WooCommerce - Premium" sul tuo computer.
    2.  Carica la cartella "wc-importer-for-danea-premium" (la cartella scompattata) nella tua directory "/wp-content/plugins/" sul server, usando il tuo client FTP/SFTP preferito.
    3.  Una volta completato il caricamento, vai alla pagina "Plugin" nella tua Bacheca WordPress.
    4.  Trova "ilGhera Danea Importer for WooCommerce - Premium" e clicca su "Attiva".

Dopo l'attivazione, troverai le opzioni del plugin nel menù di WordPress in **WooCommerce > Danea Importer for WC**

---

**ENGLISH**

To install **ilGhera Danea Importer for WooCommerce - Premium**, you can follow two methods:

#### 1. Installation via WordPress Dashboard (Recommended Method)

    1.  From your WordPress Dashboard, navigate to "Plugins > Add New".
    2.  Click the "Upload Plugin" button at the top of the page.
    3.  Select the compressed zip file of "ilGhera Danea Importer for WooCommerce - Premium" that you downloaded.
    4.  Complete the installation process and activate the plugin.

#### 2. Manual Installation (via FTP/SFTP)

    1.  Unzip the "ilGhera Danea Importer for WooCommerce - Premium" zip file on your computer.
    2.  Upload the "wc-importer-for-danea-premium" directory (the unzipped folder) to your "/wp-content/plugins/" directory on your server, using your favorite FTP/SFTP client.
    3.  Once the upload is complete, go to the "Plugins" page in your WordPress Dashboard.
    4.  Locate "ilGhera Danea Importer for WooCommerce - Premium" and click "Activate".

Once activated, you'll find the plugin options in your WordPress in **WooCommerce > Danea Importer for WC**.


== Changelog ==

= 1.7.5 =
Release Date: 8 March 2026

    * Enhancement: New option to import EAN/GTIN barcode from Danea (only valid numeric codes).
    * Enhancement: Order import now supports product variations (Size/Color).
    * Enhancement: Automatic creation of variable products with variations during order import.
    * Enhancement: Tax calculation in order imports now uses Danea totals to avoid rounding differences.
    * Update: ilghera-notice library to v1.2.0.
    * Update: Plugin Update Checker.
    * Update: Translations.


= 1.7.4 =
Release Date: 22 July 2025

    * Enhancement: Support for WC User Role Based Pricing plugin.
    * Bug Fix: Issue where the short description was always being overwritten.
    * Bug Fix: Bug with product tag deletion in case of missing Danea custom fields import.


= 1.7.3 =
Release Date: June 17, 2025

    * Bug Fix: Resolved an issue where the "Products not available" option prevented all product imports due to an incorrect variable being used for stock status checks.


= 1.7.2 =
Release Date: June 15, 2025

    * Bug Fix: Resolved a fatal error caused by early instantiation of the notice class, ensuring proper plugin loading.
    * Bug Fix: Corrected an issue where product variations were incorrectly assigned the sale price as the regular price.


= 1.7.1 =
Release Date: 13 June 2025

    * Bug Fix: Addressed a fatal error (wp_verify_nonce() undefined) during plugin loading by ensuring the license check runs only when all WordPress core functions are available.
    * Bug Fix: Resolved an issue where product variations were incorrectly deleted if only one attribute (color or size) was defined.
    * Minor Fixes: Includes various small adjustments and typo corrections.
    * Update: Translations


= 1.7.0 =
Release Date: 11 June 2025

    * Enhancement: Added full compatibility with WooCommerce High-Performance Order Storage (HPOS) for improved order management and database efficiency.
    * Enhancement: Optimized plugin loading performance and stability by refining class instantiation timing, ensuring smoother plugin initialization.
    * Enhancement: Introduced comprehensive options for Action Scheduler task cleanup, allowing administrators to define retention days for completed, failed, and canceled tasks, improving database hygiene.
    * Enhancement: Implemented CRUD (Create, Read, Update, Delete) methods for products and orders, providing more robust and efficient data management capabilities.
    * Enhancement: Enhanced product attribute synchronization; if a product attribute is removed in Danea, the plugin now automatically deletes any dependent WooCommerce product variations during synchronization, ensuring data consistency.
    * Enhancement: Action Scheduler is now used for CSV product imports, significantly improving performance and enabling the seamless import of thousands of products.
    * Enhancement: Improved product image synchronization, ensuring more reliable and efficient handling of images during imports and updates.
    * Enhancement: Adhered to WordPress Coding Standards for improved code quality, readability, and future maintainability.
    * Enhancement: Enhanced Custom Field Handling: Implemented a robust logic for Danea Easyfatt custom fields (CustomField1-4).
    * Conditional Tag Appending: Product tags are now cleared before import unless at least one custom field configured as a 'tag' explicitly has the 'append' option enabled. This ensures precise control over existing tags.
    * Optimized Saving: Product attributes and tags are now saved only once at the end of the `danea_custom_fields` method, improving performance and data consistency.
    * Enhancement: Detailed Synchronization Logging: Added comprehensive information about the synchronization process to the plugin's log for better monitoring and troubleshooting.
    * Enhancement: Progress Bar Enhancements: The progress bar now accurately reflects the advancement of product deletion during synchronization, providing a clearer overview of the import process.
    * Update: Action Scheduler
    * Update: Plugin Update Checker
    * Update: Translations


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
