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
Route::resource('alphabet',     'Admin\AlphabetController');
Route::resource('country',      'Admin\CountryController');
Route::resource('language',     'Admin\LanguageController');
Route::resource('definition',   'Admin\DefinitionController');
Route::resource('tag',          'Admin\TagController');
Route::resource('translation',  'Admin\TranslationController');
Route::resource('audio',        'AudioController');

// Resource import.
// Route::post('import', ['as' => 'admin.import.action', 'uses' => 'Data\v050\DataController@import']);
Route::post('import', ['as' => 'admin.import.action', 'uses' => 'ImportController@import']);

// Resource export
// Route::get('export/{resource}.{format}', ['as' => 'export.resource', 'uses' => 'Data\v041\DataController@export']);
Route::get('export/{resource}.{format}', ['as' => 'export.resource', 'uses' => 'Admin\ExportController@export']);
