<?php
/**
 * Copyright Di Nkomo(TM) 2015, all rights reserved
 */

// API v 0.1
Route::group(['prefix' => 'v0.1', 'middleware' => ['api.headers']], function()
{
    Route::get('/', function() {
        return 'v0.1';
    });

    // Language endpoints.
    Route::resource('language', 'LanguageController', ['except' => ['create', 'store', 'destroy']]);

    // Resource count.
    Route::get('/{definitionType}/count',   'API\v01\ApiController@count');

    // Resource lookup.
    Route::get('/{definitionType}/search/{query}',   'API\v01\ApiController@search');

    // General lookup
    Route::get('/search/{query}', 'API\v01\ApiController@searchAllResources');
});

// Static pages.
Route::get('/',             ['as' => 'home', 'uses' => 'PageController@home']);
Route::get('/about',        ['as' => 'about', 'uses' => 'PageController@about']);
Route::get('/in-numbers',   ['as' => 'stats', 'uses' => 'PageController@stats']);
Route::get('/api',          'PageController@api');

// Authentication routes.
Route::get('login',     ['as' => 'auth.login', 'uses' => 'Auth\AuthController@getLogin']);
Route::post('login',    ['as' => 'auth.login.action', 'uses' => 'Auth\AuthController@postLogin']);
Route::get('logout',    ['as' => 'auth.logout', 'uses' => 'Auth\AuthController@getLogout']);

// Registration routes.
Route::get('signup',    ['as' => 'auth.register', 'uses' => 'Auth\AuthController@getRegister']);
Route::post('signup',   ['as' => 'auth.register.action', 'uses' => 'Auth\AuthController@postRegister']);

// Language endpoints.
Route::resource('language',                 'LanguageController', ['except' => ['index', 'show']]);
Route::get('/language/search/{query?}',     'LanguageController@search');
Route::post('/language/search/{query?}',    'LanguageController@search');

// Definition endpoints.
Route::resource('definition',               'DefinitionController', ['only' => ['create', 'edit', 'store', 'update', 'destroy']]);
Route::get('/definition/search/{query}',    'DefinitionController@search');
Route::post('/definition/search/{query}',   'DefinitionController@search');
Route::post('/definition/exists/{title}',   'DefinitionController@exists');

// Translation endpoints.
Route::resource('translation',  'TranslationController');

// Audio endpoints.
Route::resource('audio',        'AudioController');

// Authentication routes.
Route::get('/login',        ['as' => 'auth.login', 'uses' => 'Auth\AuthController@getLogin']);
Route::post('/login',       ['as' => 'auth.login.post', 'uses' => 'Auth\AuthController@postLogin']);
Route::get('/logout',       ['as' => 'auth.logout', 'uses' => 'Auth\AuthController@getLogout']);

// Redirects.
Route::get('stats', function() { return redirect(route('stats')); });

//
// Admin area.
//
Route::group(['prefix' => 'admin'/*, 'middleware' => 'auth'*/], function()
{
    // General pages
    Route::get('/',         ['as' => 'admin', 'uses' => 'AdminController@index']);
    Route::get('/import',   ['as' => 'admin.import', 'uses' => 'AdminController@import']);
    Route::get('/export',   ['as' => 'admin.export', 'uses' => 'AdminController@export']);
    Route::get('/list/def', ['as' => 'admin.list.definitions', 'uses' => 'AdminController@getDefinitionList']);
    Route::get('/list/lang',['as' => 'admin.list.languages', 'uses' => 'AdminController@getLanguageList']);

    // Resource import.
    Route::post('/import', ['as' => 'admin.import.action', 'uses' => 'Data\v041\DataController@import']);

    // Resource export
    Route::get('/export/{resource}.{format}',
        ['as' => 'export.resource', 'uses' => 'Data\v041\DataController@export']);
});

// Redirects.
Route::get('/home', function() {
    return redirect(route('home'));
});

// Dictionary pages
Route::get('/+lang',            ['as' => 'language.walkthrough', 'uses' => 'LanguageController@walkthrough']);
Route::get('/{lang}',           ['as' => 'language.show', 'uses' => 'LanguageController@show']);
Route::get('/{lang}/+word',     ['as' => 'definition.create.word', 'uses' => 'DefinitionController@createWord']);
Route::get('/{lang}/+name',     ['as' => 'definition.create.name', 'uses' => 'DefinitionController@createName']);
Route::get('/{lang}/+phrase',   ['as' => 'definition.create.phrase', 'uses' => 'DefinitionController@createPhrase']);
Route::get('/{lang}/+poem',     ['as' => 'definition.create.poem', 'uses' => 'DefinitionController@createPoem']);
Route::get('/{lang}/+story',    ['as' => 'definition.create.story', 'uses' => 'DefinitionController@createStory']);
Route::get('/{lang}/{word}',    'DefinitionController@show');
