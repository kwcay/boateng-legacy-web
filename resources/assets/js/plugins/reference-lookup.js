/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved
 *
 */
(function ( $ ) {

    /**
     *
     */
    $.fn.referenceLookup = function( setup ) {

        var $resLookup = this.resourceLookup($.extend(setup, {
            valueField: 'id',
            labelField: 'name',
            searchField: ['name', 'fullCitation'],
            apiEndpoint: App.root + 'api/0.1/reference/search/:query?embed=name,fullCitation',
            render:
            {
                item: function(item, escape) {
                    return  '<div>' +
                                '<span class="name">' + escape(item.name) + '</span>' +
                            '</div>';
                },

                option: function(item, escape)
                {
                    // Return formatted HTML
                    return  '<div>' +
                                '<span class="label">' + escape(item.fullCitation) + '</span>' + hint +
                            '</div>';
                }
            }
        }));

        return this;
    };

}( jQuery ));
