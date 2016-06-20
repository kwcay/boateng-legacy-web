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
Route::get('about/agorɔ', 'PageController@agoro')->name('about.agoro');
Route::get('about/api', 'PageController@api')->name('about.api');
Route::get('about/in-numbers', 'PageController@stats')->name('stats');
Route::get('about/story', 'PageController@story')->name('story');
Route::get('about/supporters', 'PageController@sponsors')->name('sponsors');
Route::get('about/team', 'PageController@team')->name('team');
Route::get('sitemap/{topic?}', 'PageController@sitemap')->name('sitemap');
Route::get('+', 'PageController@contribute')->name('contribute');

//
// Alphabet pages.
//
Route::get('+alphabet', 'AlphabetController@walkthrough')->name('alphabet.create');
Route::post('alphabet', 'AlphabetController@store')->name('alphabet.store');

//
// Definition pages.
//
Route::get('{code}/+word', 'DefinitionController@createWord')->name('definition.create.word');
Route::get('{code}/+expression', 'DefinitionController@createexpression')->name('definition.create.expression');
Route::get('{code}/{definition}', 'DefinitionController@show')->name('definition.show');
Route::get('random', 'PageController@random')->name('definition.random');
Route::post('definition', 'DefinitionController@store')->name('definition.store');

//
// Language pages.
//
Route::get('{code}', 'LanguageController@show')->name('language.show');
Route::get('+lang', 'LanguageController@walkthrough')->name('language.create');
Route::post('language', 'LanguageController@store')->name('language.store');

// User pages.
Route::get('u/{id}')->name('user.show');

//
// Resource routes.
//
Route::group(['prefix' => 'r'], function()
{
    $resActions = ['only' => ['store', 'edit', 'update', 'destroy']];

    Route::resource('alphabet', 'AlphabetController', $resActions);
    Route::patch('alphabet/{id}/restore', 'AlphabetController@restore');
    Route::delete('alphabet/{id}/commit', 'AlphabetController@forceDestroy');

    Route::resource('country', 'CountryController', $resActions);
    Route::patch('country/{id}/restore', 'CountryController@restore');
    Route::delete('country/{id}/commit', 'CountryController@forceDestroy');

    Route::resource('definition', 'DefinitionController', $resActions);
    Route::patch('definition/{id}/restore', 'DefinitionController@restore');
    Route::delete('definition/{id}/commit', 'DefinitionController@forceDestroy');

    Route::resource('language', 'LanguageController', $resActions);
    Route::patch('language/{id}/restore', 'LanguageController@restore');
    Route::delete('language/{id}/commit', 'LanguageController@forceDestroy');

    Route::resource('tag', 'TagController', $resActions);
    Route::patch('tag/{id}/restore', 'TagController@restore');
    Route::delete('tag/{id}/commit', 'TagController@forceDestroy');

    Route::resource('user', 'UserController', $resActions);
    Route::patch('user/{id}/restore', 'UserController@restore');
    Route::delete('user/{id}/commit', 'UserController@forceDestroy');
});

//
// Authentication routes.
//
Route::group(['namespace' => 'Auth'], function()
{
    Route::get('login', 'AuthController@getLogin')->name('auth.login');
    Route::post('logout', 'AuthController@postLogin')->name('auth.login.post');
    Route::get('logout', 'AuthController@getLogout')->name('auth.logout');
});

// Redirects.
Route::get('add', function() { return redirect(route('contribute')); });
Route::get('agoro', function() { return redirect(route('about.agoro')); });
Route::get('home', function() { return redirect(route('home')); });
Route::get('stats', function() { return redirect(route('stats')); });
Route::get('in-numbers', function() { return redirect(route('stats')); });
Route::get('contribute', function() { return redirect(route('contribute')); });
