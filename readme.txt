
=== Woocommerce Importer for Danea - Premium ===
Contributors: ghera74
Tags: Fattura elettronica, Woocommerce, Danea Easyfatt, ecommerce, exporter, csv, shop, orders, products, gestionale
Version: 1.1.7
Requires at least: 4.0
Tested up to: 5.2


Import suppliers, clients and products, from Danea Easyfatt into your Woocommerce store.

== Description ==

Se hai realizzato il tuo negozio online con Woocommerce ed utilizzi Danea Easyfatt come gestionale, Woocommerce Importer per Danea è lo strumento che ti serve perché le due piattaforme siano in grado di comunicare.
Woocommerce Importer for Danea - Premium ti permette di importare:

* L'elenco dei fornitori, sotto forma di utenti Wordpress a cui si è assegnato un ruolo specifico (CSV).
* L'elenco dei clienti, sotto forma di utenti Wordpress a cui si è assegnato un ruolo specifico (CSV).
* L'elenco dei prodotti, sotto forma di prodotti Woocommerce (CSV).
* L'elenco dei prodotti, con relative variazioni taglie/colori e immagini, attraverso la ricezione di un POST HTTP inviato da Danea Easyfatt.
* L'elenco degli ordini, con creazione automatica dei prodotti mancanti ed inserimento opzionale dei nuovi clienti.


**ENGLISH**

If you've built your online store with Woocommerce and you're using Danea Easyfatt as management software, you definitely need Woocommerce Importer for Danea - Premium!
You'll be able to import suppliers, clients and products.


== Installation ==

* Upload the ‘woocommerce-importer-for-danea-premium’ directory to your ‘/wp-content/plugins/’ directory, using your favorite method (ftp, sftp, scp, etc…)
* Activate Woocommerce Importer for Danea – Premium from your Plugins page.
* Once Activated, go to Woocommerce/ WC Importer for Danea.

----

* Per installare Woocommerce Importer for Danea, dalla Bacheca del tuo sito Wordpress vai alla voce Plugin/ Aggiungi nuovo.
* Clicca sul pulsante "Carica plugin" e seleziona la cartella compressa appena scaricata.
* Completato il processo di installazione, troverai nel menù Woocommerce la pagina opzioni con tutte le informazioni necessarie all'utilizzo di Woocommerce Importer for Danea - Premium.



== Changelog ==

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
* Enhancement: Choose which Danea menu list use for the Woocommerce regular price, and a second one for the sell price.
* Enhancement: Import product weight and dimension from Danea, gross or net. 
* Enhancement: Use part of the Danea product description for the short description in Woocommerce.
* Enhancement: Exclude product description in update synchronizations.
* Enhancement: New products imported can now be published directly.
* Enhancement: New plugin update checker.
* Bug fix: Different PHP Notices.
* Bug fix: Warning PHP in wcifd-functions.php on line 246, with different tax class than 22.
* Bug fix: Variable product attributes created in Woocommerce lost after synchronizations.
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
* Enhancement: Danea sizes and colors now are imported as Woocommerce variations.
* Enhancement: Choose if import also product images.
* Enhancement: Woocommerce variations previously exported, now are imported correctly linked to the parent product.
* Enhancement: Now are imported also the subcategories.
* Enhancement: Shop manager can now handle the plugin options.
* Enhancement: Using the supplier as post author is now an option.
* Bug fix: Product short description deleted after update.


= 0.9.3 =
Release Date: 21 December, 2016

* Enhancement: During the products import, now you can update what is already present in Woocommerce.


= 0.9.2 =
Release Date: 16 November, 2016

* Enhancement: Now is possible select if import products with prices inclusive tax or not.
* Bug fix: The product description was not get from the csv
* Bug fix: Products imported not visible in frontend


= 0.9.1 =
Release Date: 06 November, 2016

* Enhancement: The system now searches for existing products by them sku
* Enhancement: Fiscal code and P.IVA fields are now recognized by checking the specific plugin installed.
* Enhancement: Now you can import in Woocommerce the orders made in Danea EasyFatt
* Enhancement: During the orders import, if new products are found will be added as Woocommerce items, with "Imported" category assigned.
* Enhancement: You can choose if add new customers found during the orders import, as Wordpress users.
* Enhancement: You can set the order status for your imported orders.


= 0.9.0 =
Release Date: 07 October, 2016

* First release
