<?php
/**
 * Copyright Di Nkomo(TM) 2015, all rights reserved
 *
 * @brief   Route definitions for API v0.1
 */


Route::get('/', function() {
    return 'Di Nkɔmɔ API 0.1';
});

// Definition endpoints.
Route::get('{definitionType}/count', 'ApiController@count');
Route::get('{definitionType}/search/{query}', 'ApiController@search');
Route::get('{definitionType}/title/{title}', 'DefinitionController@findBytitle');
Route::options('definition/{id?}', 'ApiController@options');
Route::resource('definition', 'DefinitionController', ['except' => ['index', 'create', 'edit']]);

// Language endpoints.
Route::resource('language', 'LanguageController', ['except' => ['create', 'store', 'destroy']]);

// Authentication endpoints.
Route::post('auth/local', 'AuthController@login');
Route::options('auth/local', 'ApiController@options');

// General lookup
Route::get('search/{query}', 'ApiController@searchAllResources');

// Sitemap
Route::get('map', 'ApiController@map');

// OpenSearch description.
Route::get('os', 'ApiController@openSearchDescription')->name('api.os');
