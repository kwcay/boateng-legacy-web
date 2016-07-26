/**
 * Copyright Di Nkɔmɔ(TM) 2016, all rights reserved.
 *
 * @author  Frank Yaw (frank@frnk.ca)
 */
(function ( $ ) {

    /**
     *
     */
    $.fn.tagSearch = function( options ) {

        options = options || {};

        // Add "remote" class.
        this.addClass('remote');

        // Initialize selectize input.
        var $select = this.selectize({
            valueField: 'uniqueId',
            labelField: 'title',
            searchField: ['title'],
            options: options.selectizeItems,
            plugins: (options.selectizePlugins || null),
            create: true,
            maxItems: (options.maxItems || 20),
            render: {

                /**
                 *
                 */
                item: function(item, escape) {
                    return  '<div>' +
                                '<span class="name">' + escape(item.title) + '</span>' +
                            '</div>';
                },

                /**
                 *
                 */
                score: function(search) {
                    return this.getScoreFunction(search);
                },

                /**
                 * @param object item
                 * @param function escpae
                 */
                option: function(item, escape)
                {
                    return  '<div>' +
                                '<span class="label">' + escape(item.title) + '</span>' +
                            '</div>';
                }
            },

            /**
             *
             */
            load: function(query, callback) {

                if (!query.trim().length) return callback();

                $.ajax({
                    url: App.root +'api/0.1/tag/search/' + App.urlencode(query.trim()),
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
