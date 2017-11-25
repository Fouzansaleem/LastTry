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

Auth::routes();

Route::get('/home', 'HomeController@index');

Route::resource('Company','Company\CompanyController');
Route::resource('Branch','Branch\BranchController');
Route::resource('Department','Department\DepartmentController');
Route::resource('Material','Material\MaterialController');
Route::resource('Product','Product\ProductController');
Route::resource('Production','Production\ProductionController');
