
var Forms =
{
    setupLangSearch: function(input, options, max, plugins)
    {
        if (!$(input)) return;
        $(input).selectize({
            valueField: 'code',
            labelField: 'name',
            searchField: ['code', 'name', 'altNames'],
            options: options,
            plugins: (plugins || null),
            create: false,
            maxItems: (max || 1),
            render: {
                item: function(item, escape) {
                    return  '<div>' +
                                '<span class="name">' + escape(item.name) + '</span>' +
                            '</div>';
                },
                score: function(search) {
                    return this.getScoreFunction(search);
                },
                option: function(item, escape)
                {
                    // Language title
                    var title   = item.name;
                    if (item.parentName && item.parentName.length)
                        title += ' (a sub-language of '+ item.parentName +')';
                    
                    // Add a short desciption
                    var hint = '';
                    if (item.altNames && item.altNames.length)
                        hint = '<span class="hint"> &mdash; Also known as '+ item.altNames.join(', ') + '</span>';
                    
                    // Return formatted HTML
                    return  '<div>' +
                                '<span class="label">' + escape(title) + '</span>' + hint +
                            '</div>';
                }
            },
            load: function(query, callback) {
                if (!query.trim().length) return callback();
                $.ajax({
                    url: App.root +'language/search/' + App.urlencode(query.trim()),
                    type: 'POST',
                    error: function() {
                        callback();
                    },
                    success: function(obj) {
                        callback(obj.results.languages);
                    }
                });
            }
        });
    },
	
	log: function(msg) {
		if (console) console.log('Forms.js - '+ msg);
	}
};
