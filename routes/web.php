<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group([
    'prefix' => Localization::setLocale(),
    'middleware' => [ 'localeSessionRedirect', 'localizationRedirect' ]
], function() {
    // General routes
    Route::get('/',         'PageController@home')->name('home');
    Route::get('/search',   'PageController@search')->name('search');
    Route::get('/about',    'PageController@about')->name('about');

    // Definition routes
    Route::get('/gem/{id}', 'DefinitionController@show')->name('definition');
    Route::get('/random/{lang?}', 'DefinitionController@random')->name('definition.random');

    // Language routes
    Route::get('/lang/{code}', 'LanguageController@show')->name('language');

    // User routes
    Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login');
    Route::post('/login', 'Auth\LoginController@login')->name('login.post');
    Route::get('/logout', 'Auth\LoginController@logout')->name('logout');
});

// Miscellaneous
Route::get('/auth/{service}', 'PageController@notImplemented')->name('oauth');
Route::get('/map', 'PageController@sitemap')->name('sitemap');
Route::get('/_noga', 'PageController@setNoTrackingCookie');
