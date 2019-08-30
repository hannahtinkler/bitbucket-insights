<?php

Route::get('login', 'Auth\LoginController@index')->name('login');
Route::get('persist', 'Auth\LoginController@store')->name('persist');
Route::get('logout', 'Auth\LoginController@destroy')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', 'DashboardController@index')->name('dashboard');
    Route::get('/merges', 'MergeController@index')->name('merges');
    Route::get('/pull-requests', 'PullRequestController@index')->name('pull-requests');
});
