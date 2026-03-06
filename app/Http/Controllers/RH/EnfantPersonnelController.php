<?php

namespace App\Http\Controllers;

use App\Models\RhEnfantPersonnel;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EnfantsPersonnelExport;
use Barryvdh\DomPDF\Facade\Pdf;
class EnfantPersonnelController extends VoyagerBaseController
{
    // =========================================
    // INDEX - Liste des enregistrements
    // =========================================
    public function index(Request $request)
    {
        // Appeler la méthode parente
        return parent::index($request);
    }

    // =========================================
    // CREATE - Formulaire de création
    // =========================================
    public function create(Request $request)
    {
        // Appeler la méthode parente
        return parent::create($request);
    }

    // =========================================
    // STORE - Sauvegarde d'un nouvel enregistrement
    // =========================================
    public function store(Request $request)
    {
        // Validation personnalisée si nécessaire
        /*
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'date_naissance' => 'required|date',
        ]);
        */
        
        // Traitement avant sauvegarde
        // $data = $request->all();
        // $data['slug'] = Str::slug($request->nom . '-' . $request->prenom);
        
        return parent::store($request);
    }

    // =========================================
    // SHOW - Affichage d'un enregistrement
    // =========================================
    public function show(Request $request, $id)
    {
        return parent::show($request, $id);
    }

    // =========================================
    // EDIT - Formulaire d'édition
    // =========================================
    public function edit(Request $request, $id)
    {
        return parent::edit($request, $id);
    }

    // =========================================
    // UPDATE - Mise à jour d'un enregistrement
    // =========================================
    public function update(Request $request, $id)
    {
        // Validation personnalisée si nécessaire
        /*
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
        ]);
        */
        
        // Traitement avant mise à jour
        // $data = $request->all();
        // $data['slug'] = Str::slug($request->nom . '-' . $request->prenom);
        
        return parent::update($request, $id);
    }

    // =========================================
    // DESTROY - Suppression d'un enregistrement
    // =========================================
    public function destroy(Request $request, $id)
    {
        return parent::destroy($request, $id);
    }

    // =========================================
    // ACTIONS PERSONNALISÉES
    // =========================================
    
    /**
     * Exemple d'action personnalisée
     */
    public function actionPersonnalisee(Request $request, $id)
    {
        // Récupérer l'enfant
        $enfant = RhEnfantPersonnel::findOrFail($id);
        
        // Logique personnalisée
        // ...
        
        // Redirection avec message
        return redirect()
            ->back()
            ->with([
                'message'    => 'Action personnalisée exécutée avec succès',
                'alert-type' => 'success',
            ]);
    }

    /**
     * Relation avec le personnel (exemple)
     */
    public function getEnfantsByPersonnel($personnelId)
    {
        $enfants = RhEnfantPersonnel::where('personnel_id', $personnelId)->get();
        
        return response()->json($enfants);
    }

    // =========================================
    // OVERRIDE DES MÉTHODES VOYAGER
    // =========================================
    
    /**
     * Personnaliser les données avant l'affichage
     */
    protected function getSlug(Request $request)
    {
        // Si vous avez besoin de modifier le slug
        return parent::getSlug($request);
    }
    
    /**
     * Personnaliser les données avant l'enregistrement
     */
    public function insertUpdateData($request, $slug, $rows, $data)
    {
        // Exemple: générer un slug personnalisé
        // if (isset($data['nom']) && isset($data['prenom'])) {
        //     $data['slug'] = Str::slug($data['nom'] . ' ' . $data['prenom']);
        // }
        
        return parent::insertUpdateData($request, $slug, $rows, $data);
    }
    
    /**
     * Personnaliser la requête pour l'index
     */
    protected function indexData($request, $slug, $dataType, $rows)
    {
        // Exemple: ajouter un tri par défaut
        // $dataTypeContent = $dataType->model::orderBy('created_at', 'DESC')->get();
        
        return parent::indexData($request, $slug, $dataType, $rows);
    }

    // =========================================
    // HOOKS / ÉVÉNEMENTS
    // =========================================
    
    /**
     * Après la création d'un enfant
     */
    protected function afterCreate($request, $data)
    {
        // Exemple: envoyer une notification
        // event(new EnfantCreated($data));
        
        // Exemple: créer un dossier physique
        // Storage::makeDirectory('enfants/' . $data->id);
        
        return $data;
    }
    
    /**
     * Après la mise à jour d'un enfant
     */
    protected function afterUpdate($request, $data)
    {
        // Logique après mise à jour
        return $data;
    }
    
    // =========================================
    // MÉTHODES UTILITAIRES
    // =========================================
    
    /**
     * Calcul de l'âge de l'enfant
     */
    public function calculerAge($dateNaissance)
    {
        if (!$dateNaissance) {
            return null;
        }
        
        $naissance = new \DateTime($dateNaissance);
        $aujourdhui = new \DateTime();
        $age = $naissance->diff($aujourdhui);
        
        return $age->y;
    }
    
    /**
     * Vérifier si l'enfant est majeur
     */
    public function estMajeur($dateNaissance)
    {
        $age = $this->calculerAge($dateNaissance);
        return $age >= 18;
    }

    // =========================================
    // SCOPES / FILTRES
    // =========================================
    
    /**
     * Filtrer les enfants par âge
     */
    public function scopeParAge($query, $ageMin = null, $ageMax = null)
    {
        if ($ageMin) {
            $dateMin = now()->subYears($ageMin);
            $query->where('date_naissance', '<=', $dateMin);
        }
        
        if ($ageMax) {
            $dateMax = now()->subYears($ageMax + 1);
            $query->where('date_naissance', '>=', $dateMax);
        }
        
        return $query;
    }

    public function export(Request $request)
{
    $format = $request->get('export_format', 'excel');
    $query = RhEnfantPersonnel::query();
    
    // Appliquer les filtres
    if ($request->has('sexe')) {
        $query->where('sexe', $request->sexe);
    }
    
    if ($request->has('status')) {
        $query->where('status', $request->status);
    }
    
    if ($request->has('age_min')) {
        $ageMinDate = now()->subYears($request->age_min);
        $query->where('date_naissance', '<=', $ageMinDate);
    }
    
    if ($request->has('age_max')) {
        $ageMaxDate = now()->subYears($request->age_max + 1);
        $query->where('date_naissance', '>=', $ageMaxDate);
    }
    
    $enfants = $query->with('personnel')->get();
    
    if ($format == 'pdf') {
        $pdf = Pdf::loadView('exports.enfants-pdf', compact('enfants'));
        return $pdf->download('enfants-personnel-' . date('Y-m-d') . '.pdf');
    }
    
    // Excel par défaut
    return Excel::download(new EnfantsPersonnelExport($enfants), 'enfants-personnel-' . date('Y-m-d') . '.xlsx');
}