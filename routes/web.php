<?php

Route::get('login', 'Auth\LoginController@index')->name('login');
Route::get('persist', 'Auth\LoginController@store')->name('persist');
Route::get('logout', 'Auth\LoginController@destroy')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', 'DashboardController@index')->name('dashboard');
    Route::get('/reviews', 'ReviewsController@index')->name('reviews');
    Route::get('/merges', 'MergesController@index')->name('merges');
    Route::get('/merges/all/{type}', 'AllMergesController@index')->name('merges.all');
    Route::get('/reviews/all/{type}', 'AllReviewsController@index')->name('reviews.all');
});
