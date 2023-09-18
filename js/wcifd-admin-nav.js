/**
 * Script menu di navigazione
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/js
 *
 * @since 1.6.1
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

            if ( 'wcifd-products' == $(this).data("link") ) {
                $('.subsubsub.wcifd a').removeClass('current');
                $('#wcifd-products .subsubsub.wcifd li:first-child a').addClass('current');
			    $('#wcifd-products-general').fadeIn(200);	
            }

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

        // Opzione uguale per ogni custom field
        $('.field-tag-append .tzCheckBox').on('click', function(){

            if ( $(this).hasClass('checked') ) {

                $('.field-tag-append .tzCheckBox').not(this).addClass('checked');
                $('.field-tag-append .tzCBContent').not(this).text('On');
                $('.field-tag-append input[type="checkbox"]').not(this).attr('checked', 'checked');

            } else {

                $('.field-tag-append .tzCheckBox').not(this).removeClass('checked');
                $('.field-tag-append .tzCBContent').not(this).text('Off');
                $('.field-tag-append input[type="checkbox"]').not(this).removeAttr('checked');

            }

        })

    	$('.wcifd-custom-field').each(function(){

            var field          = this;
    		var fieldTagAppend = $('.field-tag-append', field);
    		var fieldSplit     = $('.field-split', field);
    		var fieldDisplay   = $('.field-display', field);
    		var fieldName      = $('.field-name', field);

    		if( $('select', field).val() ) {

                if( 'tag' == $('select', field).val() ) {

                    $(fieldDisplay).hide();
                    $(fieldName).hide();

                } else {

                    $(fieldTagAppend).hide();

                }

            } else {

                $(fieldTagAppend).hide();
                $(fieldSplit).hide();
                $(fieldDisplay).hide();
                $(fieldName).hide();

            }

    		$('select', field).on('change', function(){

                if ( $(this).val() ) {

                    $(fieldSplit).show('slow');

                    if ( 'attribute' == $(this).val() ) {

                        $(fieldTagAppend).hide('slow');
                        $(fieldDisplay).show('slow');
                        $(fieldName).show('slow');
                    
                    } else {

                        $(fieldTagAppend).show('slow');
                        $(fieldDisplay).hide('slow');
                        $(fieldName).hide('slow');

                    }

                } else {

                    $(fieldTagAppend).hide('slow');
                    $(fieldSplit).hide('slow');
                    $(fieldDisplay).hide('slow');
                    $(fieldName).hide('slow');

                }

            })
            
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
