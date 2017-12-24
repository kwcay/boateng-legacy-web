<?php

/** @var \Illuminate\Routing\Router $router */

$router->group([
    'prefix'     => Localization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect']
], function() use ($router) {
    // General routes
    $router->get('/', 'PageController@home')->name('home');
    $router->get('/about', 'PageController@about')->name('about');

    // Definition routes
    $router->get('/gem/+/{type?}/{lang?}', 'DefinitionController@create')->name('definition.create');
    $router->get('/random/{lang?}', 'DefinitionController@random')->name('definition.random');
    $router->resource('gem', 'DefinitionController', [
        'except' => ['index', 'create'],
        'names' => [
            'store' => 'definition.store',
            'show' => 'definition.show',
            'edit' => 'definition.edit',
            'update' => 'definition.update',
            'destroy' => 'definition.destroy',
        ]
    ]);

    // Language routes
    $router->get('/lang/+', 'LanguageController@create')->name('language.create');
    $router->get('/lang/{code}', 'LanguageController@show')->name('language');
    $router->resource('lang', 'LanguageController', [
        'except' => ['index', 'create'],
        'names' => [
            'store' => 'language.store',
            'show' => 'language.show',
            'edit' => 'language.edit',
            'update' => 'language.update',
            'destroy' => 'language.destroy',
        ]
    ]);
    $router->get('/learn/{code?}', 'LanguageController@learn')->name('language.learn');

    // Member routes
    $router->get('/login', 'Auth\LoginController@showLoginForm')->name('login');
    $router->post('/login', 'Auth\LoginController@login')->name('login.post');
    $router->get('/logout', 'Auth\LoginController@logout')->name('logout');
    $router->get('/member', 'MemberController@index');
    $router->get('/member/settings', 'MemberController@settings');

    // Search routes
    $router->get('/search', 'PageController@getSearchPage')->name('search');
});

// Miscellaneous
$router->get('/osd.xml', 'SearchController@openSearchDescription')->name('search.osd');
$router->get('/suggest.{format}', 'SearchController@suggest')->name('search.suggest');
$router->get('/auth/{service}', 'PageController@notImplemented')->name('oauth');
$router->get('/map', 'PageController@sitemap')->name('sitemap');
$router->get('/_noga', 'PageController@setNoTrackingCookie');
