<?php
/**
 * Copyright Di Nkomo(TM) 2015, all rights reserved
 *
 */


// General pages
Route::get('/',         ['as' => 'admin.index', 'uses' => 'PageController@admin']);
Route::get('import',    ['as' => 'admin.import', 'uses' => 'PageController@import']);
Route::get('backup',    ['as' => 'admin.backup', 'uses' => 'PageController@backup']);

// Resources
Route::resource('alphabet', 'Admin\AlphabetController', ['except' => ['create', 'show', 'store']]);
Route::resource('country', 'Admin\CountryController', ['except' => ['create', 'show', 'store']]);
Route::resource('definition', 'Admin\DefinitionController', ['except' => ['create', 'show', 'store']]);
Route::resource('language', 'Admin\LanguageController', ['except' => ['create', 'show', 'store']]);
Route::resource('reference', 'ReferenceController', ['except' => ['create', 'show', 'store']]);
Route::resource('tag', 'Admin\TagController', ['except' => ['create', 'show', 'store']]);
// Route::resource('user', 'Admin\UserController', ['except' => ['create', 'show', 'store']]);
Route::get('user', 'UserController@index')->name('admin.user.index');

// Resource import.
// Route::post('import', ['as' => 'admin.import.action', 'uses' => 'Data\v050\DataController@import']);
Route::post('import', ['as' => 'admin.import.action', 'uses' => 'ImportController@import']);

// Resource export
// Route::get('export/{resource}.{format}', ['as' => 'export.resource', 'uses' => 'Data\v041\DataController@export']);
Route::get('export/{resource}.{format}', ['as' => 'export.resource', 'uses' => 'Admin\ExportController@export']);
