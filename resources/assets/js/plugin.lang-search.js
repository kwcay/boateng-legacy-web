/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved
 *
 */
(function ( $ ) {

    /**
     *
     */
    $.fn.langSearch = function( options ) {

        // Add "remote" class.
        this.addClass('remote');

        // Initialize selectize input.
        var $select = this.selectize({
            valueField: 'code',
            labelField: 'name',
            searchField: ['code', 'name', 'altNames'],
            options: options.selectizeItems,
            plugins: (options.selectizePlugins || null),
            create: false,
            maxItems: (options.maxItems || 1),
            render: {

                /**
                 *
                 */
                item: function(item, escape) {
                    return  '<div>' +
                                '<span class="name">' + escape(item.name) + '</span>' +
                            '</div>';
                },

                /**
                 *
                 */
                score: function(search) {
                    return this.getScoreFunction(search);
                },

                /**
                 *
                 */
                option: function(item, escape)
                {
                    // Language title
                    var title   = item.name;
                    if (item.parentName && item.parentName.length)
                        title += ' (a sub-language of '+ item.parentName +')';

                    // Add a short desciption
                    var hint = '';
                    if (item.altNames && item.altNames.length)
                        hint = '<span class="hint"> &mdash; Also known as '+ item.altNames + '</span>';

                    // Return formatted HTML
                    return  '<div>' +
                                '<span class="label">' + escape(title) + '</span>' + hint +
                            '</div>';
                }
            },

            /**
             *
             */
            load: function(query, callback) {
                if (!query.trim().length) return callback();
                $.ajax({
                    url: App.root +'0.1/language/search/' + App.urlencode(query.trim()),
                    type: 'GET',
                    error: function() {
                        callback();
                    },
                    success: function(results) {
                        callback(results);
                    }
                });
            },

            /**
             *
             */
            onItemAdd: options.onItemAdd || null
        });

        return this;
    };

}( jQuery ));
