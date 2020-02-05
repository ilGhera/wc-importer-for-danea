
=== Woocommerce Importer for Danea ===
Contributors: ghera74
Tags: Fattura elettronica, Woocommerce, Danea Easyfatt, ecommerce, exporter, csv, shop, orders, products, gestionale
Version: 1.1.0
Requires at least: 4.0
Tested up to: 5.3
Stable tag: 1.1.0


Import suppliers from Danea Easyfatt into your Woocommerce store. With the Premium version, you'll be able to import also clients, products and orders.

----

Importa l'elenco dei tuoi fornitori, da Danea EasyFatt al tuo store Woocommerce. Con la versione Premium, sarai in grado di importare anche clienti, prodotti e ordini.


== Description ==

**ENGLISH**

If you've built your online store with Woocommerce and you're using Danea Easyfatt as management software, you definitely need Woocommerce Importer for Danea.
You'll be able to import suppliers width this free version, also clients and products with the premium one.

**NEW ON THIS VERSION**

* Impost customers
* Customers and suppliers import now supports the fiscal fields coming from the Exporter plugin.
* (Premium) Avoid product name update with a dedicated option
* (Premium) Import Producer from Danea and add it to the product as attribute
* (Premium) Option for showing the Producer in front-end or not
* (Premium) Import Danea custom fields and add them to the product as attributes
* (Premium) Add a name to the custom fields coming from Danea 
* (Premium) Option for showing the custom fields in front-end or not


**ITALIANO**

Se hai realizzato il tuo negozio online con Woocommerce ed utilizzi Danea Easyfatt come gestionale, Woocommerce Importer per Danea è lo strumento che ti serve perché le due piattaforme siano in grado di comunicare.
Woocommerce Importer for Danea ti permette di importare:

* L'elenco dei fornitori, sotto forma di utenti Wordpress a cui si è assegnato un ruolo specifico (CSV).
* (Premium) L'elenco dei prodotti, sotto forma di prodotti Woocommerce (CSV).
* (Premium) L'elenco dei clienti, sotto forma di utenti Wordpress a cui si è assegnato un ruolo specifico (CSV).
* (Premium) L'elenco dei prodotti, con relative variazioni taglie/colori e immagini, attraverso la ricezione di un POST HTTP inviato da Danea Easyfatt.
* (Premium )L'elenco degli ordini, con creazione automatica dei prodotti mancanti ed inserimento opzionale dei nuovi clienti.

**LE NOVITÀ DI QUESTA VERSIONE**

* Importazione dei clienti da Danea
* L'importazione di clienti e fornitori supporta ora i campi fiscali utilizzati anche dall'Exporter
* (Premium) Opzione per escludere il nome del prodotto dalla sincronizzazione
* (Premium) Importazione del campo Produttore di Danea come attributo di prodotto WooCommerce
* (Premium) Opzione per mostrare o meno il campo Produttore in front-end
* (Premium) Importazione dei campi aggiuntivi di Danea come attributi di prodotto WooCommerce
* (Premium) Possibilità di assegnare un nome personalizzato agli attributi creati con l'importazione dei campi aggiuntivi di Danea.
* (Premium) Opzione per mostrare o meno i campi aggiuntivi in front-end


== Installation ==

From your WordPress dashboard

* Visit 'Plugins > Add New'
* Search for 'Woocommerce Importer for Danea' and download it.
* Activate Woocommerce Importer for Danea from your Plugins page.
* Once Activated, go to Woocommerce/ Woocommerce Importer for Danea.


From WordPress.org

* Download Woocommerce Importer for Danea
* Upload the 'woocommerce-importer-for-danea' directory to your '/wp-content/plugins/' directory, using your favorite method (ftp, sftp, scp, etc...)
* Activate Woocommerce Importer for Danea from your Plugins page.
* Once Activated, go to Woocommerce/ WC Importer for Danea.


== Screenshots ==

1. Choose the user role and import your Danea EasyFatt suppliers list
2. Products import - General settings
3. Products import - Custom fields
4. Products import - Remote
5. Products import - File
6. Choose the user role and import your Danea EasyFatt client list
7. Import orders in Woocommerce
8. WooCommerce Role Based Price support

== Changelog ==

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
* Enhancement: (Premium) Choose which Danea menu list use for the Woocommerce regular price, and a second one for the sell price.
* Enhancement: (Premium) Import product weight and dimension from Danea, gross or net. 
* Enhancement: (Premium) Use part of the Danea product description for the short description in Woocommerce.
* Enhancement: (Premium) Exclude product description in update synchronizations.
* Enhancement: (Premium) New products imported can now be published directly.
* Enhancement: (Premium) New plugin update checker.
* Bug fix: PHP Notices


= 0.9.4 =
Release Date: 11 April, 2016

* Enhancement: Shop manager can now handle the plugin options.
* Enhancement: Better tabs navigation.
* Enhancement: (Premium) Now you can import/ update products directly from Danea (Ctrl+P)
* Enhancement: (Premium) Danea sizes and colors now are imported as Woocommerce variations.
* Enhancement: (Premium) Choose if import also product images.
* Enhancement: (Premium) Woocommerce variations previously exported, now are imported correctly linked to the parent product.
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
