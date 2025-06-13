=== ilGhera WooCommerce Importer for Danea - Premium ===
Contributors: ghera74
Tags: Fattura elettronica, Danea Easyfatt, gestionale, prodotti, sincronizzazione
Version: 1.4.0
Requires at least: 6.0
Tested up to: 6.8
WC tested up to: 9


Import suppliers, clients and products, from Danea Easyfatt into your WooCommerce store.

== Description ==

Se hai realizzato il tuo negozio online con WooCommerce ed utilizzi Danea Easyfatt come gestionale, **ilGhera WooCommerce Importer per Danea - Premium** è lo strumento indispensabile per far comunicare efficacemente le due piattaforme. Il nostro software è **certificato Danea Easyfatt**, garantendo la massima affidabilità e un'integrazione fluida.

ilGhera WooCommerce Importer for Danea - Premium ti permette di importare e sincronizzare facilmente i tuoi dati:

* **Fornitori:** Importa l'elenco dei fornitori direttamente in WordPress come utenti, assegnando loro un ruolo specifico (tramite file CSV). Questo ti consente una gestione centralizzata e flessibile dei tuoi contatti commerciali.
* **Clienti:** Carica l'elenco dei tuoi clienti da Danea, anche loro come utenti WordPress con un ruolo dedicato (tramite file CSV), semplificando la gestione del tuo database clienti e la loro interazione con il negozio.
* **Prodotti (CSV):** Sincronizza l'intero elenco dei tuoi prodotti da Danea Easyfatt direttamente in WooCommerce (tramite file CSV), permettendoti di aggiornare le schede prodotto esistenti o creare nuove voci di catalogo in modo rapido.
* **Prodotti (POST HTTP con Variazioni e Immagini):** Ottieni la massima automazione per i tuoi aggiornamenti di catalogo. Questo metodo ti consente di sincronizzare i tuoi prodotti in tempo reale, comprese le variazioni di taglie/colori e le immagini associate, grazie alla ricezione di un POST HTTP inviato direttamente da Danea Easyfatt.
* **Ordini:** Importa l'elenco degli ordini da Danea direttamente in WooCommerce. Il plugin gestisce automaticamente la creazione dei prodotti mancanti all'interno degli ordini e ti offre l'opzione di inserire i nuovi clienti, garantendo una completa e accurata tracciabilità delle transazioni.

**ENGLISH**

If you've built your online store with WooCommerce and you're using Danea Easyfatt as management software, you definitely need **ilGhera WooCommerce Importer for Danea - Premium**! Our software is **Danea Easyfatt certified**, ensuring maximum reliability and seamless integration.

ilGhera WooCommerce Importer for Danea - Premium allows you to easily import and synchronize your data:

* **Suppliers:** Import your supplier list directly into WordPress as users, assigning them a specific role (via CSV file). This allows for centralized and flexible management of your business contacts.
* **Clients:** Upload your client list from Danea, also as WordPress users with a dedicated role (via CSV file), simplifying your customer database management and their interaction with the store.
* **Products (CSV):** Synchronize your entire product list from Danea Easyfatt directly into WooCommerce (via CSV file), allowing you to quickly update existing product sheets or create new catalog entries.
* **Products (HTTP POST with Variations and Images):** Achieve maximum automation for your catalog updates. This method allows you to synchronize your products in real-time, including size/color variations and associated images, by receiving an HTTP POST sent directly from Danea Easyfatt.
* **Orders:** Import your order list from Danea directly into WooCommerce. The plugin automatically handles the creation of missing products within orders and offers the option to add new clients, ensuring complete and accurate transaction traceability.


== Installation ==

Per installare **ilGhera WooCommerce Importer for Danea - Premium**, puoi seguire due metodi:

#### 1. Installazione tramite Bacheca di WordPress (Metodo Consigliato)

    1.  Dalla Bacheca del tuo sito WordPress, naviga su **Plugin > Aggiungi nuovo**.
    2.  Clicca sul pulsante **"Carica plugin"** in cima alla pagina.
    3.  Seleziona il file zip compresso di **ilGhera WooCommerce Importer for Danea - Premium** che hai scaricato.
    4.  Completa il processo di installazione e attiva il plugin.

#### 2. Installazione Manuale (via FTP/SFTP)

    1.  Decomprimi il file zip di **ilGhera WooCommerce Importer for Danea - Premium** sul tuo computer.
    2.  Carica la cartella `wc-importer-for-danea-premium` (la cartella scompattata) nella tua directory `/wp-content/plugins/` sul server, usando il tuo client FTP/SFTP preferito.
    3.  Una volta completato il caricamento, vai alla pagina **Plugin** nella tua Bacheca WordPress.
    4.  Trova **"ilGhera WooCommerce Importer for Danea - Premium"** e clicca su **"Attiva"**.

Dopo l'attivazione, troverai le opzioni del plugin nel menù di WordPress in **WooCommerce > WC Importer for Danea**

---

**ENGLISH**

To install **ilGhera WooCommerce Importer for Danea - Premium**, you can follow two methods:

#### 1. Installation via WordPress Dashboard (Recommended Method)

1.  From your WordPress Dashboard, navigate to **Plugins > Add New**.
2.  Click the **"Upload Plugin"** button at the top of the page.
3.  Select the compressed zip file of **ilGhera WooCommerce Importer for Danea - Premium** that you downloaded.
4.  Complete the installation process and activate the plugin.

#### 2. Manual Installation (via FTP/SFTP)

1.  Unzip the **ilGhera WooCommerce Importer for Danea - Premium** zip file on your computer.
2.  Upload the `wc-importer-for-danea-premium` directory (the unzipped folder) to your `/wp-content/plugins/` directory on your server, using your favorite FTP/SFTP client.
3.  Once the upload is complete, go to the **Plugins** page in your WordPress Dashboard.
4.  Locate **"ilGhera WooCommerce Importer for Danea - Premium"** and click **"Activate"**.

Once activated, you'll find the plugin options in your WordPress in **WooCommerce > WC Importer for Danea**.


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

= 1.4.0 =
Release Date: 13 June 2025

    * Enhancement: Added full compatibility with WooCommerce High-Performance Order Storage (HPOS) for improved order management and database efficiency.
    * Enhancement: Optimized plugin loading performance and stability by refining class instantiation timing, ensuring smoother plugin initialization.
    * Enhancement: (Premium) Introduced comprehensive options for Action Scheduler task cleanup, allowing administrators to define retention days for completed, failed, and canceled tasks, improving database hygiene.
    * Enhancement: (Premium) Implemented **CRUD (Create, Read, Update, Delete) methods for products and orders**, providing more robust and efficient data management capabilities.
    * Enhancement: (Premium) Enhanced product attribute synchronization; if a product attribute is removed in Danea, the plugin now automatically deletes any dependent WooCommerce product variations during synchronization, ensuring data consistency.
    * Enhancement: (Premium) **Action Scheduler is now used for CSV product imports**, significantly improving performance and enabling the seamless import of thousands of products.
    * Enhancement: (Premium) **Improved product image synchronization**, ensuring more reliable and efficient handling of images during imports and updates.
    * Enhancement: Adhered to **WordPress Coding Standards** for improved code quality, readability, and future maintainability.
    * Enhancement: (Premium) **Enhanced Custom Field Handling:** Implemented a robust logic for Danea Easyfatt custom fields (CustomField1-4).
    * **Conditional Tag Appending: (Premium) **Product tags are now cleared before import unless at least one custom field configured as a 'tag' explicitly has the 'append' option enabled. This ensures precise control over existing tags.
    * **Optimized Saving: (Premium) **Product attributes and tags are now saved only once at the end of the `danea_custom_fields` method, improving performance and data consistency.
    * Enhancement: (Premium) **Detailed Synchronization Logging:** Added comprehensive information about the synchronization process to the plugin's log for better monitoring and troubleshooting.
    * Enhancement: (Premium) **Progress Bar Enhancements:** The progress bar now accurately reflects the advancement of product deletion during synchronization, providing a clearer overview of the import process.
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

