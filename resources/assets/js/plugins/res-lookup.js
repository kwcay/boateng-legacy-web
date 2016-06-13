/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved
 *
 */
(function ( $ ) {

    /**
     *
     */
    $.fn.resourceLookup = function( setup ) {

        // Selectize setup.
        setup = setup || {};
        setup.valueField = setup.valueField || 'uniqueId';
        setup.delimiter = setup.delimiter || ',';
        setup.labelField = setup.labelField || 'title';
        setup.searchField = setup.searchField || [setup.labelField];
        setup.options = setup.options || setup.selectizeItems;
        setup.selectizePlugins = setup.selectizePlugins || null;
        setup.create = setup.create || false;
        setup.maxItems = setup.maxItems || 100;
        setup.loadThrottle = setup.loadThrottle || 600;
        setup.render = setup.render || {};

        // Default to tag API endpoint.
        setup.apiEndpoint = setup.apiEndpoint || App.root +'api/0.1/tag/search/:query';

        /**
         *
         */
        setup.load = function(query, callback) {

            if (!query.trim().length) return callback();

            $.ajax({
                url: this.apiEndpoint.replace(':query', App.urlencode(query.trim())),
                type: 'GET',
                error: function() {
                    callback();
                },
                success: function(results) {
                    callback(results);
                }
            });
        }.bind(setup);

        if (typeof setup.render.item != 'function')
        {
            /**
             *
             */
            setup.render.item = function(item, escape) {
                return  '<div>' +
                            '<span class="name">' + escape(item[this.labelField]) + '</span>' +
                        '</div>';
            }.bind(setup);
        }

        if (typeof setup.render.score != 'function')
        {
            /**
             *
             */
            setup.render.score = function(search) {
                return this.getScoreFunction(search);
            };
        }

        if (typeof setup.render.option != 'function')
        {
            /**
             *
             */
            setup.render.option = function(item, escape) {
                return  '<div>' +
                            '<span class="label">' + escape(item[this.labelField]) + '</span>' +
                        '</div>';
            }.bind(setup);
        }

        // Add "remote" class.
        this.addClass('remote');

        // Initialize selectize input.
        var $select = this.selectize(setup);

        return this;
    };

}( jQuery ));
