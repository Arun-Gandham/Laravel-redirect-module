<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\pageRedirectMiddleware;
use App\Http\Controllers\pageRedirectController;
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
Route::middleware([pageRedirectMiddleware::class])->group(function () {
    Route::get('/', function () {
        return view('welcome');
    });
    Route::get('/getRedirectUrls', function () {
        return view('   ');
    });
    Route::get('/check', function () {
        return view('welcome');
    });

    Route::get('/blogs', function () {
        return view('welcome');
    });





    Route::get('/get-redirect-links',[pageRedirectController::class,"getRedirectUrls"])->name('get.redirectlinks');
    Route::get('/redirect-list',[pageRedirectController::class,"getRedirectUrlsDatatable"])->name('api.pageredirect.datatable');
    Route::post('/create-pageredirect',[pageRedirectController::class,"createPageredirect"])->name('create.pageredirect');
    Route::post('/redirectlink-disable',[pageRedirectController::class,"toggleLinkDisable"])->name('redirectlink.enable.disable');
    Route::post('/redirectlink-delte',[pageRedirectController::class,"deleteRedirectLink"])->name('redirectlink.delete');
    Route::post('/redirectlink-update',[pageRedirectController::class,"updateRedirectLink"])->name('redirectlink.update');
    Route::post('/redirectlink-bulkaction-update',[pageRedirectController::class,"bulkActionUpdateRedirectLink"])->name('redirectlink.bulkaction.update');


});