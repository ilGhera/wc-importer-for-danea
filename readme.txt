=== Woocommerce Importer for Danea ===
Contributors: ghera74
Tags: Woocommerce, Danea, Easyfatt, ecommerce, importer, csv, shop, products, suppliers, customers
Version: 1.0.0
Requires at least: 4.0
Tested up to: 4.9
Stable tag: 1.0.0



Import suppliers from Danea Easyfatt into your Woocommerce store. With the Premium version, you'll be able to import also clients, products and orders.

----

Importa l'elenco dei tuoi fornitori, da Danea EasyFatt al tuo store Woocommerce. Con la versione Premium, sarai in grado di importare anche clienti, prodotti e ordini.


== Description ==

**ENGLISH**

If you've built your online store with Woocommerce and you're using Danea Easyfatt as management software, you definitely need Woocommerce Importer for Danea.
You'll be able to import suppliers width this free version, also clients and products with the premium one.


**NEW ON THIS VERSION**

* Update users imported if already present.
* (Premium) Danea tax classes imported during synchronization.
* (Premium) Choose which Danea menu list use for the Woocommerce regular price, and a second one for the sell price.
* (Premium) Import product weight and dimension from Danea, gross or net. 
* (Premium) Use part of the Danea product description for the short description in Woocommerce.
* (Premium) Exclude product description in update synchronizations.
* (Premium) New products imported can now be published directly.


**ITALIANO**

Se hai realizzato il tuo negozio online con Woocommerce ed utilizzi Danea Easyfatt come gestionale, Woocommerce Importer per Danea è lo strumento che ti serve perché le due piattaforme siano in grado di comunicare.
Woocommerce Importer for Danea ti permette di importare:

* L'elenco dei fornitori, sotto forma di utenti Wordpress a cui si è assegnato un ruolo specifico (CSV).
* (Premium) L'elenco dei prodotti, sotto forma di prodotti Woocommerce (CSV).
* (Premium) L'elenco dei clienti, sotto forma di utenti Wordpress a cui si è assegnato un ruolo specifico (CSV).
* (Premium) L'elenco dei prodotti, con relative variazioni taglie/colori e immagini, attraverso la ricezione di un POST HTTP inviato da Danea Easyfatt.
* (Premium )L'elenco degli ordini, con creazione automatica dei prodotti mancanti ed inserimento opzionale dei nuovi clienti.


**LE NOVITÀ DI QUESTA VERSIONE**

* Durante l'importazione dei fornitori, eventuali utenti già presenti vengono ora aggiornati.
* (Premium) Nuovo metodo di importazione delle aliquote IVA provenienti da Danea Easyfatt.
* (Premium) Possibilità di selezionare diversi listini Danea per importare il normale prezzo di vendita e quello in offerta.
* (Premium) Importazione di peso e misure dei prodotti, scegliendo se utilizzare il valori lordi o netti di Danea.
* (Premium) Descrizione breve del prodotto Woocommerce generata in automatico.
* (Premium) Possibilità di escludere la descrizione prodotto dalla sincronizzzione.
* (Premium) I nuovi prodotti importati possono ora essere pubblicati direttamente.


== Installation ==
From your WordPress dashboard

<ul>
<li>Visit 'Plugins > Add New'</li>
<li>Search for 'Woocommerce Importer for Danea' and download it.</li>
<li>Activate Woocommerce Importer for Danea from your Plugins page.</li>
<li>Once Activated, go to Woocommerce/ Woocommerce Importer for Danea.</li>
</ul>



From WordPress.org

<ul>
<li>Download Woocommerce Importer for Danea</li>
<li>Upload the 'woocommerce-importer-for-danea' directory to your '/wp-content/plugins/' directory, using your favorite method (ftp, sftp, scp, etc...)</li>
<li>Activate Woocommerce Importer for Danea from your Plugins page.</li>
<li>Once Activated, go to Woocommerce/ WC Importer for Danea.</li>
</ul>


== Screenshots ==

1. Choose the user role and import your Danea EasyFatt suppliers list
2. Products import options (Premium)
3. Orders import options (Premium)


== Changelog ==


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
