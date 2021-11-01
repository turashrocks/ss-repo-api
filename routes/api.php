<?php

// Public routes
Route::get('me', 'User\MeController@getMe');

// movies
Route::get('movies', 'Movies\MovieController@index');
Route::get('movies/{id}', 'Movies\MovieController@findMovie');
Route::get('movies/slug/{slug}', 'Movies\MovieController@findBySlug');


//users
Route::get('users', 'User\UserController@index');
Route::get('user/{username}', 'User\UserController@findByUsername');
Route::get('users/{id}/movies', 'Movies\MovieController@getForUser');

// Studio
Route::get('studios/slug/{slug}', 'Studios\StudiosController@findBySlug');
Route::get('studios/{id}/movies', 'Movies\MovieController@getForStudio');

// Search Movies
Route::get('search/movies', 'Movies\MovieController@search');
Route::get('search/movieers', 'User\UserController@search');

// Route group for authenticated users only
Route::group(['middleware' => ['auth:api']], function(){
    Route::post('logout', 'Auth\LoginController@logout');
    Route::put('settings/profile', 'User\SettingsController@updateProfile');
    Route::put('settings/password', 'User\SettingsController@updatePassword');


    // Upload Movies
    Route::post('movies', 'Movies\UploadController@upload');
    Route::put('movies/{id}', 'Movies\MovieController@update');
    Route::get('movies/{id}/byUser', 'Movies\MovieController@userOwnsMovie');
    
    Route::delete('movies/{id}', 'Movies\MovieController@destroy');

    // Likes and Unlikes
    Route::post('movies/{id}/like', 'Movies\MovieController@like');
    Route::get('movies/{id}/liked', 'Movies\MovieController@checkIfUserHasLiked');

    // Comments
    Route::post('movies/{id}/comments', 'Movies\CommentController@store');
    Route::put('comments/{id}', 'Movies\CommentController@update');
    Route::delete('comments/{id}', 'Movies\CommentController@destroy');

    // Studios
    Route::post('studios', 'Studios\StudiosController@store');
    Route::get('studios/{id}', 'Studios\StudiosController@findById');
    Route::get('studios', 'Studios\StudiosController@index');
    Route::get('users/studios', 'Studios\StudiosController@fetchUserStudios');
    Route::put('studios/{id}', 'Studios\StudiosController@update');
    Route::delete('studios/{id}', 'Studios\StudiosController@destroy');
    Route::delete('studios/{studio_id}/users/{user_id}', 'Studios\StudiosController@removeFromStudio');
    
    // Invitations
    Route::post('invitations/{studioId}', 'Studios\InvitationsController@invite');
    Route::post('invitations/{id}/resend', 'Studios\InvitationsController@resend');
    Route::post('invitations/{id}/respond', 'Studios\InvitationsController@respond');
    Route::delete('invitations/{id}', 'Studios\InvitationsController@destroy');

    // Chats
    Route::post('chats', 'Chats\ChatController@sendMessage');
    Route::get('chats', 'Chats\ChatController@getUserChats');
    Route::get('chats/{id}/messages', 'Chats\ChatController@getChatMessages');
    Route::put('chats/{id}/markAsRead', 'Chats\ChatController@markAsRead');
    Route::delete('messages/{id}', 'Chats\ChatController@destroyMessage');
    
});

// Routes for guests only
Route::group(['middleware' => ['guest:api']], function(){
    Route::post('register', 'Auth\RegisterController@register');
    Route::post('verification/verify/{user}', 'Auth\VerificationController@verify')->name('verification.verify');
    Route::post('verification/resend', 'Auth\VerificationController@resend');
    Route::post('login', 'Auth\LoginController@login');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');

    


});

