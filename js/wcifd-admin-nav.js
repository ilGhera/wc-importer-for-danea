/**
 * Script menu di navigazione
 * @author ilGhera
 * @package wc-importer-for-danea-premium/js
 * @version 1.0.2
 */
jQuery(document).ready(function ($) {

	var wcifd_pagination = function() {

		var $contents = $('.wcifd-admin')
		var url = window.location.href.split("#")[0];
		var hash = window.location.href.split("#")[1];

		if(hash) {
	        $contents.hide();		    
		    $('#' + hash).fadeIn(200);		
	        $('h2#wcifd-admin-menu a.nav-tab-active').removeClass("nav-tab-active");
	        $('h2#wcifd-admin-menu a').each(function(){
	        	if($(this).data('link') == hash) {
	        		$(this).addClass('nav-tab-active');
	        	}
	        })
	        
	        $('html, body').animate({
	        	scrollTop: 0
	        }, 'slow');
		}

		$("h2#wcifd-admin-menu a").click(function () {
	        var $this = $(this);
	        
	        $contents.hide();
	        $("#" + $this.data("link")).fadeIn(200);
	        $('h2#wcifd-admin-menu a.nav-tab-active').removeClass("nav-tab-active");
	        $this.addClass('nav-tab-active');

	        window.location = url + '#' + $this.data('link');

	        $('html, body').scrollTop(0);

	    })
	        	
	}
	
    wcifd_pagination();

});
