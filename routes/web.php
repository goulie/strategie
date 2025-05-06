<?php

use App\Http\Controllers\LoginFormController;
use Illuminate\Support\Facades\Route;

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


Route::group(['prefix' => 'admin'], function () {

    Voyager::routes();

    Route::get('login', [LoginFormController::class, 'index'])->name('voyager.login');
});

Auth::routes(['register' => false]);

Route::get('/home', function () {
    return app(\TCG\Voyager\Http\Controllers\VoyagerController::class)->index();
})->name('home');
