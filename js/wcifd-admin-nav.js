/**
 * Script menu di navigazione
 * @author ilGhera
 * @package wc-importer-for-danea-premium/js
 * @since 1.6.0
 */
jQuery(document).ready(function ($) {

	var wcifd_pagination = function( submenu = false ) {

		var contents = submenu ? '.wcifd-products-sub' : '.wcifd-admin';
		var toggle   = submenu ? '.subsubsub.wcifd a' : 'h2#wcifd-admin-menu a';
		var active   = submenu ? 'current' : 'nav-tab-active'; 
		var url      = window.location.href.split("#")[0];
		var hash     = window.location.href.split("#")[1];
		var oldHash;
		var sub;

		if(hash) {

	        $(contents).hide();

			$('#' + hash).show();

			sub = hash.split("products-")[1];

			if (sub) {
				
				oldHash = hash;
				hash    = hash.split("-" + sub)[0];

			} else if('wcifd-products' == hash) {

				oldHash = 'wcifd-products-general';//temp
			    $('#' + oldHash).fadeIn(200);	
			
			}

		    $('#' + hash).fadeIn(200);	
	        $(toggle + '.' + active).removeClass(active);

	        $(toggle).each(function(){

	        	if($(this).data('link') == hash || $(this).data('link') == oldHash) {
	        		
	        		$(this).addClass(active);
	        	
	        	}

	        })
	        
	        $('html, body').animate({
	        	scrollTop: 0
	        }, 'slow');
		}

		$(toggle).click(function () {
	        
	        $(contents).hide();

	        $("#" + $(this).data("link")).fadeIn(200);
	        $(toggle + '.' + active).removeClass(active);
	        $(this).addClass(active);

	        window.location = url + '#' + $(this).data('link');

	        $('html, body').scrollTop(0);

	    })
	        	
	}
	
    wcifd_pagination();
    wcifd_pagination(true);


    /**
     * Visualizzazione dei campi liberi di Danea
     *
     * @return void
     */
    var custom_fields_options = function() {

    	var field;
    	var fieldImport;
    	var fieldDisplay;
    	var fieldName;

    	$('.wcifd-custom-field').each(function(){

    		fieldDisplay = $('.field-display', $(this));
    		fieldName    = $('.field-name', $(this));

    		if( ! $('.field-import .tzCheckBox', $(this)).hasClass('checked') ) {

    			$(fieldDisplay).hide();
    			$(fieldName).hide();

    		}

    	})


    	$('.field-import .tzCheckBox').on('click', function(){
    	
    		fieldImport  = $(this).parent();
    		field        = (fieldImport).parent();
    		fieldDisplay = $('.field-display', field);
    		fieldName    = $('.field-name', field);

    		if ($(this).hasClass('checked')) {

    			$(fieldDisplay).show('slow');
    			$(fieldName).show('slow');
    		
    		} else {

    			$(fieldDisplay).hide('slow');
    			$(fieldName).hide('slow');

    		}

    	})

	}

    custom_fields_options();

    
    /**
	 * Esegue Chosen
     *
     * @return void
	 */
	var wcifdChosen = function() {

		jQuery(function($){

			$('.wcifd-select').chosen({
		
				disable_search_threshold: 10,
				width: '200px'
			
			});

		})

	}

    wcifdChosen();

});
