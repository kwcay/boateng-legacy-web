<?php
/**
 * Copyright Di Nkomo(TM) 2015, all rights reserved
 *
 */


// General pages
Route::get('/',         ['as' => 'admin', 'uses' => 'AdminController@index']);
Route::get('import',    ['as' => 'admin.import', 'uses' => 'AdminController@import']);
Route::get('export',    ['as' => 'admin.export', 'uses' => 'AdminController@export']);
// Route::get('list/def',  'AdminController@getDefinitionList')->name('admin.list.definitions');
Route::get('list/def',  ['as' => 'admin.list.definitions', 'uses' => 'AdminController@getDefinitionList']);
// Route::get('list/lang', 'AdminController@getLanguageList')->name('admin.list.languages');
Route::get('list/lang', ['as' => 'admin.list.languages', 'uses' => 'AdminController@getLanguageList']);

// Resources
Route::resource('language',     'LanguageController');
Route::resource('definition',   'DefinitionController');
Route::resource('translation',  'TranslationController');
Route::resource('audio',        'AudioController');

// Resource import.
Route::post('import', ['as' => 'admin.import.action', 'uses' => 'Data\v050\DataController@import']);

// Resource export
Route::get('export/{resource}.{format}', ['as' => 'export.resource', 'uses' => 'Data\v041\DataController@export']);
