<?php

// Simple pages
Route::get('/',         'SimplePage@home');
Route::get('/about',    'SimplePage@about');
Route::get('/stats',    'SimplePage@stats');
Route::get('/api',      'SimplePage@api');
Route::get('/hello',    'SimplePage@welcome');

// User pages
Route::get('/login',    'PageController@showLoginForm');

// Resource editing
Route::group(['prefix' => 'edit'], function()
{
    Route::get('/def',  'DefinitionController@showEditForm');
    Route::get('/lang',  'LanguageController@showEditForm');

});

Route::post('/save/{what?}','EditController@saveRes');

// API
Route::filter('api', 'ApiController');
Route::group(array('prefix' => 'res', 'before' => 'api'), function()
{
    // Language methods
    Route::resource('language', 'LanguageController');
    Route::get('/language/search/{query?}', 'LanguageController@search');
    
    // Definition methods
    Route::resource('definition', 'DefinitionController');
    Route::get('/definition/search/{query?}', 'DefinitionController@search');
    
});

// Dictionary pages
Route::get('/{lang}',           'PageController@showLangPage');
Route::get('/{lang}/{word}',    'PageController@showWordPage');




Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);
