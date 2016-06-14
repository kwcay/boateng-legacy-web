<?php
/**
 * Copyright Di Nkomo(TM) 2015, all rights reserved
 *
 */


// General pages
Route::get('/',         ['as' => 'admin.index', 'uses' => 'AdminController@index']);
Route::get('import',    ['as' => 'admin.import', 'uses' => 'AdminController@import']);
Route::get('export',    ['as' => 'admin.export', 'uses' => 'AdminController@export']);
Route::get('backup',    ['as' => 'admin.backup', 'uses' => 'AdminController@backup']);

// Resources
Route::resource('alphabet', 'Admin\AlphabetController', ['except' => ['create', 'show', 'store']]);
Route::resource('country', 'Admin\CountryController', ['except' => ['create', 'show', 'store']]);
Route::resource('language', 'Admin\LanguageController', ['except' => ['create', 'show', 'store']]);
Route::resource('definition', 'Admin\DefinitionController', ['except' => ['create', 'show', 'store']]);
Route::resource('tag', 'Admin\TagController', ['except' => ['create', 'show', 'store']]);

// Resource import.
// Route::post('import', ['as' => 'admin.import.action', 'uses' => 'Data\v050\DataController@import']);
Route::post('import', ['as' => 'admin.import.action', 'uses' => 'ImportController@import']);

// Resource export
// Route::get('export/{resource}.{format}', ['as' => 'export.resource', 'uses' => 'Data\v041\DataController@export']);
Route::get('export/{resource}.{format}', ['as' => 'export.resource', 'uses' => 'Admin\ExportController@export']);
