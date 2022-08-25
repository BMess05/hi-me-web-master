<?php

Route::group(['prefix' => 'v1'], function () {
    Route::post('signup', 'Api\V1\UserController@signup');
    Route::post('login', 'Api\V1\UserController@login');
    Route::post('passwordreset', 'Api\V1\PasswordResetController@passwordreset');
    Route::get('notify', 'Api\V1\UserController@sendPushnotification');
    Route::get('mytestandroid', 'Api\V1\UserController@mytest');
});

Route::group(['prefix' => 'v1', 'middleware' => ['localization']], function () {
//Route::group(['prefix' => 'v1'], function () {
    Route::post('update_availability', 'Api\V1\UserController@update_availability');
    Route::post('SearchUsers', 'Api\V1\UserController@SearchUsers');
    Route::post('updateProfilepic', 'Api\V1\UserController@EditProfilepic');
    Route::post('updateProfiledata', 'Api\V1\UserController@updateProfiledata');
    Route::post('getprofiledata', 'Api\V1\UserController@getProfiledata');
    Route::post('addfriend', 'Api\V1\UserController@addFriends');
    Route::post('getfriendrequest', 'Api\V1\UserController@getRequest');
    Route::post('accept_friend_request', 'Api\V1\UserController@accept_friend_request');
    Route::post('get_friend', 'Api\V1\UserController@getFriends');
    Route::post('get_friend_history', 'Api\V1\UserController@getFriendshistory');
    Route::post('deleteHistory', 'Api\V1\UserController@deleteHistory');
    Route::post('updateLanguage', 'Api\V1\UserController@updateLanguage');
    Route::post('logout', 'Api\V1\UserController@logout');
    Route::post('updateLatlong', 'Api\V1\UserController@updateLatlong');
    Route::post('addCredits', 'Api\V1\UserController@addCredits');

    
});
