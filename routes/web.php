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

Route::get('login', 'UserController@login');
Route::get('logineddata.json', 'UserController@logindata');
Route::get('index', 'UserController@index');
Route::get('my', 'UserController@my');
Route::get('item', 'ItemController@index');

Route::get('stmbuyIndex', 'StmbuyController@index');
Route::get('stmbuyTest', 'StmbuyController@test');
Route::get('stmbuyHtmlTest', 'StmbuyController@htmlTest');


Route::get('stmbuyAutoSale', 'StmbuyController@autoSale');
Route::get('autoSale', 'SaleController@autoSale');