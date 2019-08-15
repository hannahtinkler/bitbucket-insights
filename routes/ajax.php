<?php

Route::middleware('auth')->group(function () {
    Route::get('refresh', 'BitbucketDataController@index')->name('refresh');
    Route::get('settings', 'SettingsController@index')->name('settings');
});
