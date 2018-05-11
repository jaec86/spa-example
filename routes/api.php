<?php

use Illuminate\Http\Request;

// Auth
Route::post('/login', 'Auth\LoginController');
Route::post('/register', 'Auth\RegisterController');
Route::post('/activate/{token}', 'Auth\ActivateAccountController');
Route::post('/password/forgot', 'Auth\ForgotPasswordController');
Route::post('/password/reset/{token}', 'Auth\ResetPasswordController');
Route::post('/jwt/refresh', 'Auth\RefreshTokenController');
Route::post('/jwt/invalidate', 'Auth\InvalidateTokenController');

// Profile
Route::get('/profile', 'Profile\InfoController');
Route::put('/profile', 'Profile\UpdateController');
Route::put('/profile/password', 'Profile\PasswordController');

