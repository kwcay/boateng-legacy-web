<?php

// Global rounting patterns.
Route::pattern('id',    '[0-9A-Za-z]+');
Route::pattern('lang',  '[a-z]{3}|[a-z]{3}-[a-z]{3}');

// Simple pages
Route::get('/',         'PageController@home');
Route::get('/about',    'PageController@getAboutPage');
Route::get('/stats',    'PageController@getStatsPage');
Route::get('/api',      'PageController@getApiPage');

// User pages
Route::get('/login',    'PageController@showLoginForm');

// Forms
Route::get('/edit',     'PageController@showEditPage');
Route::get('/edit/{id?}',   'PageController@showEditPage');

Route::post('/save/{what?}','EditController@saveRes');

// API
Route::filter('api', 'ApiController');
Route::group(array('prefix' => 'api', 'before' => 'api'), function()
{
    // Language methods
    Route::resource('language', 'LanguageController');
    Route::get('/language/search/{query?}', 'LanguageController@search');
    
    // Definition methods
    Route::resource('definition', 'DefinitionController');
    Route::get('/definition/search/{query?}', 'DefinitionController@search');
    
});

// Testing
Route::get('/dev',              'DevController@landing');
Route::get('/hello',            'HomeController@showWelcome');

// Dictionary pages
Route::get('/{lang}',           'PageController@showLangPage');
Route::get('/{lang}/{word}',    'PageController@showWordPage');




Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);
