/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved
 *
 */
(function ( $ ) {

    /**
     *
     */
    $.fn.alphabetLookup = function( setup ) {

        var $resLookup = this.resourceLookup($.extend(setup, {
            valueField: 'code',
            labelField: 'name',
            searchField: ['code', 'name', 'transliteration'],
            apiEndpoint: App.root + 'api/0.1/alphabet/search/:query',
            render:
            {
                item: function(item, escape) {
                    return  '<div>' +
                                '<span class="name">' + escape(item.name) + '</span>' +
                            '</div>';
                },

                option: function(item, escape)
                {
                    // Language title
                    var title = item.name;
                    if (item.transliteration && item.transliteration.length)
                        title += ' ('+ item.transliteration +')';

                    // Return formatted HTML
                    return  '<div>' +
                                '<span class="label">' + escape(title) + '</span>' +
                            '</div>';
                }
            }
        }));

        return this;
    };

}( jQuery ));
