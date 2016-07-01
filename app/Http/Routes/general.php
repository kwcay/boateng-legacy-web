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
Route::get('about/api', 'PageController@api')->name('about.api');
Route::get('about/in-numbers', 'PageController@stats')->name('about.stats');
Route::get('about/story', 'PageController@story')->name('about.story');
Route::get('about/supporters', 'PageController@sponsors')->name('about.sponsors');
Route::get('about/team', 'PageController@team')->name('about.team');
Route::get('about/agorɔ', 'PageController@learningApp')->name('about.learning-app');
Route::get('about/safoa', 'PageController@translationEngine')->name('about.translation-engine');
Route::get('humans.txt', 'PageController@humans');
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
Route::get('+language', 'LanguageController@walkthrough')->name('language.create');
Route::post('language', 'LanguageController@store')->name('language.store');

//
// Reference pages.
//
Route::get('+reference', 'ReferenceController@walkthrough')->name('reference.create');
Route::post('reference', 'ReferenceController@store')->name('reference.store');

// User pages.
Route::get('u/{id}', 'UserController@show')->name('user.show');

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

    Route::resource('reference', 'ReferenceController', $resActions);
    Route::patch('reference/{id}/restore', 'ReferenceController@restore');
    Route::delete('reference/{id}/commit', 'ReferenceController@forceDestroy');

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
    Route::get('oauth/{service}', 'AuthController@redirectToProvider')->name('auth.oauth');
    Route::get('oauth/{service}/callback', 'AuthController@handleProviderCallback');
    Route::get('login', 'AuthController@getLogin')->name('auth.login');
    Route::post('logout', 'AuthController@postLogin')->name('auth.login.post');
    Route::get('logout', 'AuthController@getLogout')->name('auth.logout');
});

// Redirects.
Route::get('add',           'PageController@redirectAdd');
Route::get('agoro',         'PageController@redirectAgoro');
Route::get('contribute',    'PageController@redirectContribute');
Route::get('home',          'PageController@redirectHome');
Route::get('in-numbers',    'PageController@redirectInNumbers');
Route::get('stats',         'PageController@redirectStats');
