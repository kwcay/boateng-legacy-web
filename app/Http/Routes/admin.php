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
Route::get('list/def',  'AdminController@getDefinitionList')->name('admin.list.definitions');
// Route::get('list/def',  ['as' => 'admin.list.definitions', 'uses' => 'AdminController@getDefinitionList']);
Route::get('list/lang', 'AdminController@getLanguageList')->name('admin.list.languages');
// Route::get('list/lang', ['as' => 'admin.list.languages', 'uses' => 'AdminController@getLanguageList']);

// Resources
Route::resource('alphabet',     'Admin\AlphabetController');
Route::resource('country',      'Admin\CountryController');
Route::resource('language',     'Admin\LanguageController');
Route::resource('definition',   'Admin\DefinitionController');
Route::resource('translation',  'Admin\TranslationController');
Route::resource('audio',        'AudioController');

// Resource import.
// Route::post('import', ['as' => 'admin.import.action', 'uses' => 'Data\v050\DataController@import']);
Route::post('import', ['as' => 'admin.import.action', 'uses' => 'ImportController@import']);

// Resource export
// Route::get('export/{resource}.{format}', ['as' => 'export.resource', 'uses' => 'Data\v041\DataController@export']);
Route::get('export/{resource}.{format}', ['as' => 'export.resource', 'uses' => 'ExportController@export']);
