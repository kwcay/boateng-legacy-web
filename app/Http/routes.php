<?php

// Simple pages
Route::get('/',         'SimplePage@home');
Route::get('about',     'SimplePage@about');
Route::get('stats',     'SimplePage@stats');
Route::get('api',       'SimplePage@api');
Route::get('hello',     'SimplePage@welcome');

// Resources
Route::get('/language/search/{query?}', 'LanguageController@search');
Route::get('/definition/search/{query?}', 'DefinitionController@search');
Route::post('/language/search/{query?}', 'LanguageController@search');
Route::post('/definition/search/{query?}', 'DefinitionController@search');
Route::resource('language', 'LanguageController');
Route::resource('definition', 'DefinitionController');

// User pages
Route::get('/login',    'PageController@showLoginForm');
Route::controllers([
    'auth' => 'Auth\AuthController',
    'password' => 'Auth\PasswordController',
]);

// Admin stuff
//Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function() {
Route::group(['prefix' => 'admin'], function() {

    Route::get('/', 'AdminController@index');

    // Resource export
    Route::get('/export/language/{format?}', ['as' => 'export.language', 'uses' => 'LanguageController@export']);
    Route::get('/export/definition/{format?}', ['as' => 'export.definition', 'uses' => 'DefinitionController@export']);
    Route::get('/export/user/{format?}', ['as' => 'export.user', 'uses' => 'UserController@export']);

});
Route::get('import', 'AdminController@importPage');
Route::post('import', 'DataController@import');

// Dictionary pages
Route::get('/{lang}',           'LanguageController@show');
Route::get('/{lang}/+edit',     'LanguageController@edit');
Route::get('/{lang}/+add',      'DefinitionController@create');
Route::get('/{lang}/{word}',    'DefinitionController@show');

