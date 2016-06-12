/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved
 *
 */
(function ( $ ) {

    /**
     *
     */
    $.fn.definitionLookup = function() {

        var $resLookup = this.resourceLookup({
            labelField: 'mainTitle',
            apiEndpoint: App.root + 'api/0.1/definition/search/:query',
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
        });

        return this;
    };

}( jQuery ));
