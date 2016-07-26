/**
 * Copyright Di Nkɔmɔ(TM) 2016, all rights reserved.
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

                    // Use main title as a label.
                    var label = escape(item.mainTitle);

                    // Append translation to label.
                    if (item.translation && item.translation.practical) {
                        if (item.translation.practical.eng) {
                            label += ' (' + escape(item.translation.practical.eng) + ')';
                        }
                    }

                    return  '<div>' +
                                '<span class="label">' + label + '</span>' +
                            '</div>';
                }
            }
        }));

        return this;
    };

}( jQuery ));
