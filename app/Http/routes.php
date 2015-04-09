<?php

// Simple pages
Route::get('/',         'SimplePage@home');
Route::get('/about',    'SimplePage@about');
Route::get('/stats',    'SimplePage@stats');
Route::get('/api',      'SimplePage@api');
Route::get('/hello',    'SimplePage@welcome');

// Resources
Route::get('/language/search/{query?}', 'LanguageController@search');
Route::get('/definition/search/{query?}', 'DefinitionController@search');
Route::post('/language/search/{query?}', 'LanguageController@search');
Route::post('/definition/search/{query?}', 'DefinitionController@search');
Route::resource('language', 'LanguageController');
Route::resource('definition', 'DefinitionController');

// User pages
Route::get('/login',    'PageController@showLoginForm');

Route::post('/save/{what?}','EditController@saveRes');

Route::group(['prefix' => 'dev'], function() {

    Route::get('/lang', function () {
        return App\Models\Language::all();
    });

    Route::get('/def', function () {
        return App\Models\Definition::all();
    });
});

// Dictionary pages
Route::get('/{lang}',           'LanguageController@show');
Route::get('/{lang}/{word}',    'DefinitionController@show');

