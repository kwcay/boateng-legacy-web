/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved
 *
 * @author  Frank Yaw (frank@frnk.ca)
 */
(function ( $ ) {

    /**
     *
     */
    $.fn.alphabetField = function( options ) {

        // Add "remote" class.
        this.addClass('remote');

        // Convert selectize items to array of objects
        if (typeof options.selectizeItems === 'object')
        {
            if (!Array.isArray(options.selectizeItems) || typeof options.selectizeItems.length === undefined)
            {
                var itemsArray = [];

                for (var key in options.selectizeItems)
                {
                    var item = {};
                    item[key] = options.selectizeItems[key];
                    itemsArray.push(item);
                }

                options.selectizeItems = itemsArray;
            }
        }

        // Initialize selectize input.
        var $select = this.selectize({
            valueField: 'code',
            labelField: 'name',
            searchField: ['code', 'name', 'transliteration'],
            options: options.selectizeItems,
            plugins: (options.selectizePlugins || null),
            create: false,
            maxItems: (options.maxItems || 10),
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
                option: function(item, escape)
                {
                    // Return formatted HTML
                    return  '<div>' +
                                '<span class="label">' + escape(item.name) + '</span>' +
                                ' (' + escape(item.code) + ')' +
                            '</div>';
                }
            },

            /**
             *
             */
            load: function(query, callback) {
                if (!query.trim().length) return callback();
                $.ajax({
                    url:  '/api/0.1/alphabet/search/' + App.urlencode(query.trim()),
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
