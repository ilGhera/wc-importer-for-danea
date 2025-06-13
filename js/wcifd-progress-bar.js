/**
 * Script progress bar
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/js
 *
 * @since 1.4.0
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

                var del = 0;
                var totActions = parseInt(response.import);
                var completeMessage;

                if ( totActions == 0 ) {

                    totActions = parseInt(response.delete);

                    if ( totActions > 0 ) {
                        del = 1;
                    }
                }
                console.log( 'TOT. PRODUCTS', totActions );
                console.log( 'DELETE', del );

                if ( totActions > 0 ) {

                    console.log( 'UPDATE IN PROGRESS!' );

                    // Change the progress bar message
                    if ( del ) {
                        $('.wcifd-progress-bar-text').html( options.deleteMessage );
                        completeMessage = options.completedDeleteMessage;
                    } else {
                        completeMessage = options.completedMessage;
                    }

                    // Show the progress bar
                    $('.ilghera-notice-warning.catalog-update').show('slow');

                    var run = 0;
                    var width = 0;
                    var data2, currentWidth, diff;
                    var updateData = setInterval( function(){

                        data2 = {
                            'action': 'get-scheduled-actions',
                            'delete': del,
                            'nonce': response.nonce
                        }

                        $.post(ajaxurl, data2, function(resp){

                            if ( resp == totActions ) {
                                run = 1;
                            }

                            if ( resp > 0 ) {

                                diff = totActions - resp;
                                currentWidth = ( diff / totActions ) * 100;

                            } else {

                                run = 1;
                                clearInterval( updateData );
                                currentWidth = 100;
                            }

                            console.log( 'REMAINING PRODUCTS', resp );
                            console.log( 'TOT. PRODUCTS', totActions );
                            console.log( 'PERC. COMPLETED ', currentWidth );

                            if ( 1 == run ) {

                                $('#wcifd-progress').css( 'width', currentWidth + '%' );
                                $('#wcifd-progress-bar span').html( Math.ceil( currentWidth ) + '%' );

                                if ( resp == 0) {

                                    $('.wcifd-progress-bar-text').html( completeMessage );

                                    run = 0;
                                }
                            }
                        })

                    }, 500 );
                }

            }, 'json')
        })
    }

    wcifdProgressBar();
})
