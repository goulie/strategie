<?php

namespace App\Http\Controllers\DMS;

use App\Http\Controllers\Controller;
use App\Models\Continent;
use App\Models\DmsCotisationMembre;
use App\Models\Genre;
use App\Models\Gouvernance;
use App\Models\Pay;
use App\Models\Region;
use Illuminate\Http\Request;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;

class SuivieDesRecettesController extends VoyagerBaseController
{
    public function index(Request $request)
    {
        $rows = DmsCotisationMembre::statsParLigneBudgetaireEtMois($request->get('year'));
        $selectedYear  = $request->get('year') ?? now()->year;
        $data = [];

        foreach ($rows as $row) {
            $key = $row->ligne_id;

            if (!isset($data[$key])) {
                $data[$key] = [
                    'module' => $row->libelle_module,
                    'code_ligne' => $row->ligne_budgetaire,
                    'libelle_ligne' => $row->libelle_ligne,
                    'mois' => array_fill(1, 12, 0)
                ];
            }

            $data[$key]['mois'][$row->mois] = $row->total;
        }

        $dataType = Voyager::model('DataType')->where('slug', '=', 'dms-suivie-recettes')->firstOrFail();

        return view('voyager::dms-suivie-recettes.index', compact('data', 'dataType', 'selectedYear'));
    }
}
