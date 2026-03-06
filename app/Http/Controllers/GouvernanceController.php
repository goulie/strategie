<?php

namespace App\Http\Controllers;

use App\Imports\GouvernanceImport;
use App\Models\Continent;
use App\Models\Genre;
use App\Models\Gouvernance;
use App\Models\Pay;
use App\Models\Region;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;
use TCG\Voyager\Facades\Voyager;

class GouvernanceController extends VoyagerBaseController
{
    public function index(Request $request)
    {
        $annees = Gouvernance::select('annee')->distinct()->pluck('annee');
        $statusList = Gouvernance::select('status')->distinct()->pluck('status');
        $pays = Pay::orderBy('libelle_pays')->get();
        $regions = Region::select('libelle', 'id')->get();
        $continents = Continent::select('libelle', 'id')->get();
        $genres = Genre::select('libelle_genre', 'id')->get();
        $dataType = Voyager::model('DataType')->where('slug', '=', 'gouvernances')->firstOrFail();
        
        return view('voyager::gouvernances.home', compact('annees', 'statusList', 'pays', 'continents', 'regions', 'genres','dataType'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        Excel::import(new GouvernanceImport, $request->file('file'));

        return back()->with('success', 'Importation réussie !');
    }
}
