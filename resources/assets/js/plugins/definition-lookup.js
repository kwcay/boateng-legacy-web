/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved
 *
 */
(function ( $ ) {

    /**
     *
     */
    $.fn.definitionLookup = function( setup ) {

        var $resLookup = this.resourceLookup($.extend(setup, {
            labelField: 'mainTitle',
            apiEndpoint: App.root + 'api/0.1/definition/search/:query?lang=' + (setup.lang || ''),
            render:
            {
                option: function(item, escape) {
                    return  '<div>' +
                                '<span class="label">' +
                                    escape(item.mainTitle) +
                                    ' (' + escape(item.translation.practical.eng) + ')' +
                                '</span>' +
                            '</div>';
                }
            }
        }));

        return this;
    };

}( jQuery ));
