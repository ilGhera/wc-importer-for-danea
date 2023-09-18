/**
 * Script progress bar
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/js
 *
 * @since 1.6.1
 */
jQuery(document).ready(function ($) {

    /**
     * The progress bar
     *
     * @return void
     */
    var wcifdProgressBar = function() {

		jQuery(function($){

            var i = 0;
            var data = {
                'action': 'get-total-actions',
            };

            $.post(ajaxurl, data, function(response){

                var totActions = response;
                console.log( 'TOT. PRODOTTI', totActions );

                if ( totActions > 0 ) {

                    console.log('TRASFERIMENTO IN CORSO!');

                    $('.ilghera-notice-warning.catalog-update').show('slow');

                    var run = 0;
                    var width = 0;
                    var data2, currentWidth, diff;
                    var updateData = setInterval( function(){

                        data2 = {
                            'action': 'get-scheduled-actions'
                        }

                        $.post(ajaxurl, data2, function(resp){

                            if ( resp == totActions ) {
                                run = 1;
                            }

                            if ( resp > 0 ) {

                                diff         = totActions - resp;
                                currentWidth = ( diff / totActions ) * 100;
                                // var id = setInterval(frame, 30);

                            } else {

                                run = 1;
                                clearInterval( updateData );
                                currentWidth = 100;

                            }

                            console.log( 'PRODOTTI RIMANENTI', resp );
                            console.log( 'TOT. PRODOTTI', totActions );
                            console.log( 'PERC. COMPLETAMENTO', currentWidth );

                            if ( 1 == run ) {

                                $('#wcifd-progress').css( 'width', currentWidth + '%' );
                                $('#wcifd-progress-bar span').html( Math.ceil( currentWidth ) + '%' );

                                if ( resp == 0) {

                                    $('.wcifd-progress-bar-text').html( options.completedMessage );

                                    run = 0;

                                }

                            }

                        })

                    }, 500 );

                }

            })

        })

    }

    wcifdProgressBar();

})
