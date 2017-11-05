<?php

Route::get('/', 'HomeController@index');
Route::get('/login', function() {
	return view('auth/login');
});

Route::get('/logout', function() {
	Auth::logout();
	return redirect('login');
});
Route::post('/login', 'UserController@login');
Route::post('/register', 'UserController@register');

Route::group(['prefix' => 'user'], function() {
	Route::get('/', 'UserController@index');
	Route::get('/addfriends', 'UserController@addfriends');
	Route::post('/getuser', 'UserController@getuser');
	Route::post('/update', 'UserController@update');
	Route::post('/upload', 'UserController@upload');
});

Route::group(['prefix' => 'twitter'], function() {
	Route::post('/post', 'TwitterController@post');
	Route::post('/list', 'TwitterController@list');
});

Route::group(['prefix' => 'friends'], function() {
	Route::get('/', 'UserController@friends');
	Route::post('/list', 'UserController@list');
	Route::post('/add', 'UserController@add');
	Route::post('/remove', 'UserController@remove');
});

