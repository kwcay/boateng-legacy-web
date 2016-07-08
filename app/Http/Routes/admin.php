<?php
/**
 * Copyright Di Nkomo(TM) 2015, all rights reserved
 *
 */


// General pages
Route::get('/', 'Admin\AdminController@index')->name('admin.index');
Route::get('import', 'Admin\AdminController@import')->name('admin.import');

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
Route::get('export/{resource}.{format}', 'Admin\AdminController@export')->name('admin.export');

// Backups
Route::post('backup/upload', 'Admin\BackupController@upload')->name('admin.backup.upload');
Route::get('backup/download/{time}/{file}', 'Admin\BackupController@download')->name('admin.backup.download');
Route::patch('backup/restore/{file}', 'Admin\BackupController@restore')->name('admin.backup.restore');
Route::delete('backup/destroy/{file}', 'Admin\BackupController@destroy')->name('admin.backup.destroy');
Route::resource('backup', 'Admin\BackupController', ['only' => ['index', 'create']]);
