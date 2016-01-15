<?php
/**
 * Copyright Di Nkomo(TM) 2015, all rights reserved
 *
 */


//
// General pages.
//
Route::get('/', 'PageController@home')->name('home');
Route::get('about', 'PageController@about')->name('about');
Route::get('about/author', 'PageController@author')->name('author');
Route::get('about/in-numbers', 'PageController@stats')->name('stats');
Route::get('about/story', 'PageController@story')->name('story');
Route::get('contribute', 'PageController@contribute')->name('contribute');
Route::get('sitemap/{topic?}', 'PageController@sitemap')->name('sitemap');

//
// Language pages.
//
Route::get('+lang', 'LanguageController@walkthrough')->name('language.create');
Route::get('{code}', 'LanguageController@show')->name('language.show');
Route::resource('language', 'LanguageController', [
    'only' => ['store', 'update', 'destroy']
]);

//
// Definition pages.
//
Route::get('{code}/+word', 'DefinitionController@createWord')->name('definition.create.word');
Route::get('{code}/+phrase', 'DefinitionController@createPhrase')->name('definition.create.phrase');
Route::get('{code}/{definition}', 'DefinitionController@show')->name('definition.show');
Route::get('random', 'PageController@random')->name('definition.random');
Route::resource('definition', 'DefinitionController', [
    'only' => ['store', 'edit', 'update', 'destroy']
]);


//
// Authentication routes.
//
Route::get('login', 'Auth\AuthController@getLogin')->name('auth.login');
Route::post('logout', 'Auth\AuthController@postLogin')->name('auth.login.post');
Route::get('logout', 'Auth\AuthController@getLogout')->name('auth.logout');

// Redirects.
Route::get('home', function() { return redirect(route('home')); });
Route::get('stats', function() { return redirect(route('stats')); });
Route::get('in-numbers', function() { return redirect(route('stats')); });
