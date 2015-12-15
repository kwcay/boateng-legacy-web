<?php
/**
 * Copyright Di Nkomo(TM) 2015, all rights reserved
 *
 */


//
// General pages.
//
Route::get('/', 'PageController@home')->name('home');
Route::get('about', 'PageController@about')->name('about');
Route::get('about/in-numbers', 'PageController@stats')->name('stats');
Route::get('about/author', 'PageController@author')->name('author');

//
// Language pages.
//
Route::get('+lang', 'LanguageController@walkthrough')->name('language.create');
Route::get('{code}', 'LanguageController@show')->name('language.show');
Route::resource('language', 'LanguageController', [
    'only' => ['store', 'update', 'destroy']
]);

//
// Definition pages.
//
Route::get('{code}/+word', 'DefinitionController@createWord')->name('definition.create.word');
Route::get('{code}/+phrase', 'DefinitionController@createPhrase')->name('definition.create.phrase');
Route::get('{code}/{definition}', 'DefinitionController@show')->name('definition.show');
Route::resource('definition', 'DefinitionController', [
    'only' => ['store', 'edit', 'update', 'destroy']
]);


//
// Authentication routes.
//
Route::get('login', 'Auth\AuthController@getLogin')->name('auth.login');
Route::post('logout', 'Auth\AuthController@postLogin')->name('auth.login.post');
Route::get('logout', 'Auth\AuthController@getLogout')->name('auth.logout');


//
// Admin area.
//
Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function()
{
    // General pages
    Route::get('/',         ['as' => 'admin', 'uses' => 'AdminController@index']);
    Route::get('import',    ['as' => 'admin.import', 'uses' => 'AdminController@import']);
    Route::get('export',    ['as' => 'admin.export', 'uses' => 'AdminController@export']);
    // Route::get('list/def',  'AdminController@getDefinitionList')->name('admin.list.definitions');
    Route::get('list/def',  ['as' => 'admin.list.definitions', 'uses' => 'AdminController@getDefinitionList']);
    // Route::get('list/lang', 'AdminController@getLanguageList')->name('admin.list.languages');
    Route::get('list/lang', ['as' => 'admin.list.languages', 'uses' => 'AdminController@getLanguageList']);

    // Resources
    Route::resource('language',     'LanguageController');
    Route::resource('definition',   'DefinitionController');
    Route::resource('translation',  'TranslationController');
    Route::resource('audio',        'AudioController');

    // Resource import.
    Route::post('import',
        ['as' => 'admin.import.action', 'uses' => 'Data\v041\DataController@import']);

    // Resource export
    Route::get('export/{resource}.{format}',
        ['as' => 'export.resource', 'uses' => 'Data\v041\DataController@export']);
});


//
// API v 0.1
//
Route::group(['prefix' => '0.1', 'namespace' => 'API\v01', 'middleware' => ['api.headers'/*, 'api.auth'*/]], function()
{
    Route::get('/', function() {
        return 'Di Nkɔmɔ API 0.1';
    });

    // Definition endpoints.
    Route::get('{definitionType}/count', 'ApiController@count');
    Route::get('{definitionType}/search/{query}', 'ApiController@search');
    Route::get('{definitionType}/title/{title}', 'DefinitionController@findBytitle');
    Route::options('definition/{id?}', 'ApiController@options');
    Route::resource('definition', 'DefinitionController', ['except' => ['create', 'edit']]);

    // Language endpoints.
    Route::resource('language', 'LanguageController', ['except' => ['create', 'store', 'destroy']]);

    // Authentication endpoints.
    Route::post('auth/local', 'AuthController@login');
    Route::options('auth/local', 'ApiController@options');

    // General lookup
    Route::get('search/{query}', 'ApiController@searchAllResources');
});

// OAuth2...
Route::group(['prefix' => 'oauth2'], function()
{

});

// Redirects.
Route::get('home', function() { return redirect(route('home')); });
Route::get('stats', function() { return redirect(route('stats')); });
Route::get('in-numbers', function() { return redirect(route('stats')); });
