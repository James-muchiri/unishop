<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/adminSignIn', 'Auth\LoginController@getadminsignIn')->name('getadminSignIn');
Route::post('/adminSignIn', 'Auth\LoginController@adminsignIn')->name('adminSignIn');
Route::get('/admin_forgot_password', 'Auth\LoginController@admin_forgot_password')->name('admin_forgot_password');
Route::post('/admin_forgot_password', 'Auth\LoginController@admin_rest_password')->name('admin_forgot_password');
Route::get('/resetPassword/{token}/{email}', 'Auth\LoginController@resetPassword')->name('resetPassword');
Route::post('/reset_Password', 'Auth\LoginController@reset_Password')->name('reset_Password');

Route::get('/admin_signOut', 'Auth\LoginController@adminsignOut')->name('adminsignOut');