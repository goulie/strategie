<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Continent;
use App\Models\Genre;
use App\Models\Gouvernance;
use App\Models\Pay;
use App\Models\Region;
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
        $regions = Region::select('libelle','id')->get();
        $continents = Continent::select('libelle','id')->get();
        $genres = Genre::select('libelle_genre','id')->get();

        return view('front.index', compact('annees', 'statusList', 'pays','continents','regions','genres'));
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

        // FILTRE REGION
        if ($request->region_id != '') {
            $query->where('region_id', $request->region_id);
        }

        // FILTRE continent
        if ($request->continent_id != '') {
            $query->where('continent_id', $request->continent_id);
        }

        // FILTRE continent
        if ($request->genre_id != '') {
            $query->where('genre_id', $request->genre_id);
        }
        
        return DataTables::of($query)
            ->addColumn('pays', fn($g) => $g->pays?->libelle_pays)
            ->addColumn('actions', function ($g) {
                return '
        <button class="btn btn-info btn-sm detailBtn" data-id="' . $g->id . '">Détails</button>
        <button class="btn btn-primary btn-sm">Éditer</button>                   
        <button class="btn btn-danger btn-sm deleteBtn" data-id="' . $g->id . '">Supprimer</button>
';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function detail($id)
    {
        $g = Gouvernance::with(['pays', 'region', 'continent', 'genre'])->findOrFail($id);

        return response()->json($g);
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'annee' => 'required',
            'status' => 'required',
            'sigle_societe' => 'required',
            'denomination' => 'required',
            'pays_id' => 'required|exists:pays,id',
        ]);

        Gouvernance::create($data);

        return response()->json(['success' => true]);
    }

    public function edit($id)
    {
        return Gouvernance::findOrFail($id);
    }

    public function update(Request $r, $id)
    {
        $g = Gouvernance::findOrFail($id);

        $data = $r->validate([
            'annee' => 'required',
            'status' => 'required',
            'sigle_societe' => 'required',
            'denomination' => 'required',
            'pays_id' => 'required|exists:pays,id',
        ]);

        $g->update($data);

        return response()->json(['success' => true]);
    }

    public function delete($id)
    {
        Gouvernance::destroy($id);

        return response()->json(['success' => true]);
    }
}
