var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Less
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {

    //mix.sass('app.scss');

    mix.styles([
        'base.css',
        'dialogs.css',
        'footer.css',
        'forms.css',
        'selectize.css',
    ], 'public/assets/main.css', 'resources/assets/css/');

});
