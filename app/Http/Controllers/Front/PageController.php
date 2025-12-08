<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Gouvernance;
use App\Models\Pay;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PageController extends Controller
{
    public function index()
    {
        // Pour remplir les combos
        $annees = Gouvernance::select('annee')->distinct()->pluck('annee');
        $statusList = Gouvernance::select('status')->distinct()->pluck('status');
        $pays = Pay::orderBy('libelle_pays')->get();

        return view('front.index', compact('annees', 'statusList', 'pays'));
    }

    public function data(Request $request)
    {

        $query = Gouvernance::with('pays')
            ->select([
                'gouvernances.*'
            ]);

        // FILTRE ANNEE
        if ($request->annee != '') {
            $query->where('annee', $request->annee);
        }

        // FILTRE STATUS
        if ($request->status != '') {
            $query->where('status', $request->status);
        }

        // FILTRE PAYS
        if ($request->pays_id != '') {
            $query->where('pays_id', $request->pays_id);
        }

        return DataTables::of($query)
            ->addColumn('pays', fn($g) => $g->pays?->libelle_pays)
            ->addColumn('actions', function ($g) {
                return '
        <button class="btn btn-info btn-sm detailBtn" data-id="' . $g->id . '">Détails</button>
        <button class="btn btn-primary btn-sm">Éditer</button>';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function detail($id)
    {
        $g = Gouvernance::with(['pays', 'region', 'continent', 'genre'])->findOrFail($id);

        return response()->json($g);
    }
}
