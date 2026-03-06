<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\DMS\AGRController;
use App\Http\Controllers\DMS\CotisationController;
use App\Http\Controllers\DMS\ExportsDmsController;
use App\Http\Controllers\DMS\MembresController;
use App\Http\Controllers\Front\PageController;
use App\Http\Controllers\GouvernanceController;
use App\Http\Controllers\LoginFormController;
use App\Http\Controllers\RH\ContratsController;
use App\Http\Controllers\RH\ListePersonnelController;
use App\Http\Controllers\Ticket\ConversationController;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;

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
    Route::group(['prefix' => 'ticket'], function () {
        Route::post('/send', [ConversationController::class, 'openTicket'])
            ->name('open.ticket');

        Route::get('/reply/{id}', [ConversationController::class, 'replyTicket'])
            ->name('tickets.reply');

        Route::post('/reply', [ConversationController::class, 'SendreplyTicket'])
            ->name('send.tickets.reply');
    });


    Route::group(['prefix' => 'dms'], function () {
        Route::get('/membre/{id}/montant-cotisation', [CotisationController::class, 'getMontantCotisation']);
        Route::post('/store_cotisation', [CotisationController::class, 'store_cotisation'])
            ->name('cotisation.store');

        Route::post('/update_cotisation', [CotisationController::class, 'update_cotisation'])
            ->name('cotisation.update');

        //Chager les cotisations en liste
        Route::get('/cotisations/load', [CotisationController::class, 'loadCotisations'])
            ->name('cotisations.load');

        //Chager les détails d'une cotisation
        Route::get('/cotisations/{id}/details', [CotisationController::class, 'getDetails'])
            ->name('cotisations.details');

        //export des cotisations
        Route::post('/export-cotisations', [ExportsDmsController::class, 'export'])->name('cotisations.export');

        /* //export des membres non à jour
        Route::get('/membres/non-a-jour', [ExportsDmsController::class, 'membresNonAJour'])
            ->name('membres.non-a-jour');

        //Liste des membres non à jour
        Route::get('/liste-membres-non-a-jour', [CotisationController::class, 'liste_membres_non_a_jour'])
            ->name('membres.non-a-jour.liste');

        Route::post('/membres/non-a-jour/export', [ExportsDmsController::class, 'exportMembresNonAJour'])
            ->name('membres.non-a-jour.export'); */

        // Routes pour les listes de membres à jour
        Route::get('/membres/a-jour/{annee?}', [MembresController::class, 'membresAJour'])
            ->name('membres.a-jour.liste');

        //Liste des membres non à jour
        Route::get('/membres/non-a-jour/{annee?}', [MembresController::class, 'membresNonAJour'])
            ->name('membres.non-a-jour.liste');
            
        //Liste des membres expirés
        Route::get('/membres/expires', [MembresController::class, 'membresExpires'])
            ->name('membres.expires.liste');

        // Filtrer les membres par plan d'adhésion et année
        Route::get('/membres/filter/{typePlan}/{annee}/{planId?}', [MembresController::class, 'filterByPlan'])
            ->name('membres.filter.by.plan');

        //Route gestion des AGR
        Route::post('agr/store', [
            'uses' => 'App\Http\Controllers\DMS\AGRController@store',
            'as'   => 'dms.agr.store'
        ]);

        //Get price AGR
        Route::post('/get-activity-price', [AGRController::class, 'AmountAGR'])->name('dms.get-activity-price');

        //Get AGR BY MONTH AND PER YEAR
        Route::get('/chart-data/{annee?}', [AGRController::class, 'chartData']);

        Route::put('/agr/{id}', [
            'uses' => 'App\Http\Controllers\DMS\AGRController@update',
            'as'   => 'dms.agr.update'
        ]);

        Route::get('/agr/{id}/edit', [
            'uses' => 'App\Http\Controllers\DMS\AGRController@edit',
            'as'   => 'dms.agr.edit'
        ]);

        Route::delete('/agr/{id}', [
            'uses' => 'App\Http\Controllers\DMS\AGRController@destroy',
            'as'   => 'dms.agr.destroy'
        ]);

        //Get AGR services by activity dropdown
        Route::get('/services-by-activite/{id}', [AGRController::class, 'byActivite']);
    });

    Voyager::routes();

    //logs des actions
    Route::get('activity-logs', [ActivityLogController::class, 'index'])
        ->name('voyager.activity-logs.index');


    Route::post('/gouvernance/import', [GouvernanceController::class, 'import'])
        ->name('gouvernance.import');

    Route::get('login', [LoginFormController::class, 'index'])->name('voyager.login');
    Route::get('/export-membres', [MembresController::class, 'exportAll'])->name('export.membres.all');
    //Route::get('/liste-by-objectif', [MembresController::class, 'ListeByObjectif'])->name('members.by.objective');
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

Route::group(['prefix' => 'rh'], function () {

    Route::get('/personnels/create', [ListePersonnelController::class, 'create'])
        ->name('voyager.personnels.create');

    Route::post('personnels/store', [ListePersonnelController::class, 'store'])
        ->name('voyager.personnels.store');

    Route::get('/{id}/contrats-details', [ContratsController::class, 'details'])->name('contrats.details');
});

Route::get('/get_template', [CotisationController::class, 'get_template']);
Route::post('/import-cotisations', [CotisationController::class, 'export_template'])->name('import.cotisations');
   
/* Route::get('/statistiques/membres', [StatistiquesMembresController::class, 'index'])
    ->name('statistiques.membres');

Route::post('/statistiques/membres/objectif', [StatistiquesMembresController::class, 'mettreAJourObjectif'])
    ->name('statistiques.membres.objectif');

Route::get('/statistiques/membres/export', [StatistiquesMembresController::class, 'exporterExcel'])
    ->name('statistiques.membres.export'); */
