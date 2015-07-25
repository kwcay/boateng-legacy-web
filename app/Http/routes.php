<?php

// General pages.
Route::get('/',          'SimplePageController@home');
Route::get('/about',     'SimplePageController@about');
Route::get('/stats',     'SimplePageController@stats');
Route::get('/api',       'SimplePageController@api');
Route::get('/hello',     'SimplePageController@welcome');

// Authentication routes.
Route::get('login',     ['as' => 'auth.login', 'uses' => 'Auth\AuthController@getLogin']);
Route::post('login',    ['as' => 'auth.login.action', 'uses' => 'Auth\AuthController@postLogin']);
Route::get('logout',    ['as' => 'auth.logout', 'uses' => 'Auth\AuthController@getLogout']);

// Registration routes.
Route::get('signup',    ['as' => 'auth.register', 'uses' => 'Auth\AuthController@getRegister']);
Route::post('signup',   ['as' => 'auth.register.action', 'uses' => 'Auth\AuthController@postRegister']);

// Language endpoints.
Route::resource('language',                 'LanguageController');
Route::get('/language/search/{query?}',     'LanguageController@search');
Route::post('/language/search/{query?}',    'LanguageController@search');

// Definition endpoints.
Route::resource('definition',               'DefinitionController');
Route::get('/definition/search/{query?}',   'DefinitionController@search');
Route::post('/definition/search/{query?}',  'DefinitionController@search');

// User pages
//Route::get('/login',    'PageController@showLoginForm');
Route::controllers([
//    'auth' => 'Auth\AuthController',
    'password' => 'Auth\PasswordController',
]);

// Admin stuff
//Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function()
Route::group(['prefix' => 'admin'], function()
{
    // General pages
    Route::get('/',         ['as' => 'admin', 'uses' => 'AdminPageController@index']);
    Route::get('/import',   ['as' => 'admin.import', 'uses' => 'AdminPageController@import']);
    Route::get('/export',   ['as' => 'admin.export', 'uses' => 'AdminPageController@export']);

    // Resource import.
    Route::post('/import',  ['as' => 'admin.import.action', 'uses' => 'DataController@import']);

    // Resource export
    Route::get('/export/{resource}.{format}', ['as' => 'export.resource', 'uses' => 'DataController@export']);
});

// Dictionary pages
Route::get('/{lang}',           'LanguageController@show');
Route::get('/{lang}/+edit',     'LanguageController@edit');
Route::get('/{lang}/+add',      'DefinitionController@create');
Route::get('/{lang}/{word}',    'DefinitionController@show');

