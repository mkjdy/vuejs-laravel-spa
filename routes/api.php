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
//public route
Route::group(['namespace' => 'App\Http\Controllers'], function () {
    // Route::post('/register', 'AuthController@register');
    Route::post('/login', 'AuthController@login');
});

//protected routes
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::group(['namespace' => 'App\Http\Controllers'], function () {
        //User
        Route::resource('users', UserController::class)->except(['create', 'show', 'edit']);

        //Auth
        Route::post('/logout', 'AuthController@logout');

        //Role
        Route::customRoleResource('roles', RoleController::class);

        //Permission
        Route::resource('permissions', PermissionController::class)->except(['create', 'store', 'show', 'edit', 'update', 'destroy']);
    });
});
/*Route::group(['namespace' => 'App\Http\Controllers'], function () {
    Route::get('/user/get', 'UserController@index');
});*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
