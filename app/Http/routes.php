<?php

// Static pages.
Route::get('/',             ['as' => 'home', 'uses' => 'StaticPageController@home']);
Route::get('/about',        ['as' => 'about', 'uses' => 'StaticPageController@about']);
Route::get('/in-numbers',   ['as' => 'stats', 'uses' => 'StaticPageController@stats']);
Route::get('/api',       'StaticPageController@api');

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
Route::post('/definition/exists/{title}',    'DefinitionController@exists');

// Translation endpoints.
Route::resource('translation',  'TranslationController');

// Audio endpoints.
Route::resource('audio',        'AudioController');

// Authentication routes.
// Route::controllers([
//     'auth' => 'Auth\AuthController',
//     'password' => 'Auth\PasswordController',
// ]);
Route::get('/login',        ['as' => 'auth.login', 'uses' => 'Auth\AuthController@getLogin']);
Route::post('/login',       ['as' => 'auth.login.post', 'uses' => 'Auth\AuthController@postLogin']);
Route::get('/logout',       ['as' => 'auth.logout', 'uses' => 'Auth\AuthController@getLogout']);

// Redirects.
Route::get('stats', function() { return redirect(route('stats')); });

// Admin stuff
//Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function()
Route::group(['prefix' => 'admin'], function()
{
    // General pages
    Route::get('/',         ['as' => 'admin', 'uses' => 'AdminController@index']);
    Route::get('/import',   ['as' => 'admin.import', 'uses' => 'AdminController@import']);
    Route::get('/export',   ['as' => 'admin.export', 'uses' => 'AdminController@export']);

    // Resource import.
    Route::post('/import',  ['as' => 'admin.import.action', 'uses' => 'DataController@import']);

    // Resource export
    Route::get('/export/{resource}.{format}', ['as' => 'export.resource', 'uses' => 'DataController@export']);
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
