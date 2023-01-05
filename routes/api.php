<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('/payReminder/{id}', 'App\Http\Controllers\MessageController@payReminder');

Route::get('/webhooks', 'App\Http\Controllers\MessageController@handleVerificationRequest');
Route::post('/webhooks', 'App\Http\Controllers\MessageController@processWebhook');

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('/logout', 'AuthController@logout');

    //Route::post('/payReminder/{id}', 'App\Http\Controllers\MessageController@payReminder');

    Route::prefix('contacts')->group(function () {
        Route::get('/', 'App\Http\Controllers\ContactController@index');

        Route::get('/{id}', 'App\Http\Controllers\ContactController@show');

        Route::post('/', 'App\Http\Controllers\ContactController@create');

        Route::put('/{id}', 'App\Http\Controllers\ContactController@update');

        Route::delete('/{id}', 'App\Http\Controllers\ContactController@delete');
    });
});

Route::post('/login', 'App\Http\Controllers\AuthController@login')->name('login');
