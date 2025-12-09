<?php

use App\Http\Controllers\Front\PageController;
use App\Http\Controllers\GouvernanceController;
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
    return redirect()->route('voyager.login');
});


Route::group(['prefix' => 'admin'], function () {

    Voyager::routes();

    Route::post('/gouvernance/import', [GouvernanceController::class, 'import'])
        ->name('gouvernance.import');

    Route::get('login', [LoginFormController::class, 'index'])->name('voyager.login');
});

Auth::routes(['register' => false]);

Route::get('/home', function () {
    return app(\TCG\Voyager\Http\Controllers\VoyagerController::class)->index();
})->name('home');

Route::group(['prefix' => 'front', 'controller' => PageController::class], function () {

    Route::get('/', 'index')->name('front.index');
    Route::get('/data', 'data')->name('front.data');
    Route::get('/detail/{id}', 'detail')->name('front.detail');

    Route::post('/store', 'store')->name('front.store');
    Route::get('/edit/{id}', 'edit')->name('front.edit');
    Route::post('/update/{id}', 'update')->name('front.update');
    Route::delete('/delete/{id}', 'delete')->name('front.delete');
});
