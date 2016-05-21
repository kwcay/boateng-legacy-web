<?php
/**
 * Copyright Di Nkomo(TM) 2015, all rights reserved.
 *
 * @brief   Route definitions for API v0.1
 */


Route::get('/', function() {
    return 'Di Nkɔmɔ API 0.1';
});

// General resource endpoints.
Route::get('{resourceName}/count', 'ApiController@count');
Route::get('{resourceName}/search/{query}', 'ApiController@search');

// Definition endpoints.
Route::get('{definitionType}/title/{title}', 'DefinitionController@findBytitle');
Route::options('definition/{id?}', 'ApiController@options');
Route::resource('definition', 'DefinitionController', ['except' => ['index', 'create', 'edit']]);

// Tag endpoints
Route::resource('tag', 'TagController', ['except' => ['create', 'edit']]);

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
