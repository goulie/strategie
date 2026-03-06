@extends('voyager::master')

@section('page_title', 'Ajouter une cotisation')

@section('css')
    <!-- DataTables Core CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap4.min.css">

    <style>
        /* Barre d'outils style Ruban Office */
        .access-ribbon {
            background-color: #ffffff;
            border-bottom: 2px solid #0056b3;
            padding: 10px 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .ribbon-actions .btn {
            background: none;
            border: 1px solid transparent;
            color: #0056b3;
            text-align: center;
            padding: 5px 15px;
            font-weight: 500;
        }

        .ribbon-actions .btn:hover {
            background-color: #e7f1ff;
            border: 1px solid #0056b3;
        }

        .ribbon-actions .btn i {
            display: block;
            font-size: 20px;
            margin-bottom: 2px;
        }

        /* Conteneur principal */
        .access-container {
            background-color: #fff;
            border: 1px solid #0056b3;
            margin-bottom: 25px;
            border-radius: 4px;
            box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.05);
        }

        .access-header {
            background-color: #0056b3;
            color: white;
            padding: 10px 15px;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Formulaire de saisie */
        .form-section {
            padding: 20px;
            background-color: #fff;
        }

        label {
            font-weight: 600;
            color: #0056b3;
            margin-bottom: 5px;
        }

        .form-control {
            border-radius: 2px;
            border: 1px solid #bccce0;
            box-shadow: none;
        }

        .form-control:focus {
            border-color: #0056b3;
            box-shadow: 0 0 5px rgba(0, 86, 179, 0.15);
        }

        .btn-submit {
            background-color: #0056b3;
            color: white;
            border: none;
            padding: 10px 25px;
            font-weight: bold;
            margin-top: 10px;
            border-radius: 2px;
        }

        .btn-submit:hover {
            background-color: #003d82;
            color: white;
        }

        /* Liste des AGR */
        .table-management {
            margin-bottom: 0;
        }

        .table-management thead th {
            background-color: #f8faff;
            color: #0056b3;
            border-bottom: 2px solid #0056b3 !important;
            font-size: 11px;
            text-transform: uppercase;
        }

        .table-management tbody tr:hover {
            background-color: #f0f7ff;
        }

        .action-btns .btn {
            padding: 2px 8px;
            font-size: 12px;
        }

        .status-badge {
            font-size: 10px;
            padding: 3px 8px;
            border-radius: 12px;
            background-color: #e7f1ff;
            color: #0056b3;
            border: 1px solid #0056b3;
        }

        /* Style spécifique pour les observations dans le tableau */
        .obs-text {
            font-size: 11px;
            color: #666;
            font-style: italic;
            max-width: 150px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .dataTables_wrapper .row {
            padding-top: 10px !important;
        }

        /* style pour bouton Radio */
        .radio-inputs {
            display: flex;
            justify-content: center;
            align-items: center;
            max-width: 350px;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        .radio-inputs>* {
            margin: 6px;
        }

        .radio-input:checked+.radio-tile {
            border-color: #2260ff;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
            color: #2260ff;
        }

        .radio-input:checked+.radio-tile:before {
            transform: scale(1);
            opacity: 1;
            background-color: #2260ff;
            border-color: #2260ff;
        }

        .radio-input:checked+.radio-tile .radio-icon svg {
            fill: #2260ff;
        }

        .radio-input:checked+.radio-tile .radio-label {
            color: #2260ff;
        }

        .radio-input:focus+.radio-tile {
            border-color: #2260ff;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1), 0 0 0 4px #b5c9fc;
        }

        .radio-input:focus+.radio-tile:before {
            transform: scale(1);
            opacity: 1;
        }

        .radio-tile {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 180px;
            /* min-height: 80px; */
            border-radius: 0.5rem;
            border: 2px solid #b5bfd9;
            background-color: #fff;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
            transition: 0.15s ease;
            cursor: pointer;
            position: relative;
        }

        .radio-tile:before {
            content: "";
            position: absolute;
            display: block;
            width: 0.75rem;
            height: 0.75rem;
            border: 2px solid #b5bfd9;
            background-color: #fff;
            border-radius: 50%;
            top: 0.25rem;
            left: 0.25rem;
            opacity: 0;
            transform: scale(0);
            transition: 0.25s ease;
        }

        .radio-tile:hover {
            border-color: #2260ff;
        }

        .radio-tile:hover:before {
            transform: scale(1);
            opacity: 1;
        }

        .radio-icon svg {
            width: 2rem;
            height: 2rem;
            fill: #494949;
        }

        .radio-label {
            color: #707070;
            transition: 0.375s ease;
            text-align: center;
            font-size: 13px;
        }

        .radio-input {
            clip: rect(0 0 0 0);
            -webkit-clip-path: inset(100%);
            clip-path: inset(100%);
            height: 1px;
            overflow: hidden;
            position: absolute;
            white-space: nowrap;
            width: 1px;
        }
    </style>

@stop

@section('page_header')
    <div class="container-fluid">
        <div class="access-ribbon">
            <div style="font-weight: bold; color: #0056b3; font-size: 16px;">
                <i class="bi bi-database-gear"></i> ACTIVITÉ GENERATRICE DE REVENUE (AGR)
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">
                <!-- FORMULAIRE DE SAISIE -->
                <div class="col-md-12">
                    <div class="access-container">
                        <div class="access-header">
                            <span><i class="bi bi-pencil-square"></i> NOUVELLE AGR</span>
                        </div>
                        <div class="form-section">
                            <form id="agrForm">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4">
                                        {{-- EVENEMENT --}}
                                        <div class="form-group">
                                            <label>Évènement</label>
                                            <select class="form-control" name="evenement_id" required>
                                                <option value="" disabled selected>-- Sélectionner --</option>
                                                @foreach (App\Models\DmsEvenementsAgr::ActiveEventsAgrs() as $e)
                                                    <option value="{{ $e->id }}">{{ $e->libelle_event }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        {{-- ACTIVITÉ --}}
                                        <div class="form-group">
                                            <label>Activité</label>
                                            <select class="form-control" id="activite" name="activite_id" required>
                                                <option value="" disabled selected>-- Sélectionner --</option>
                                                @foreach (App\Models\DmsActiviteAgr::active()->get() as $a)
                                                    <option value="{{ $a->id }}">{{ $a->libelle_activite }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        {{-- SERVICE --}}
                                        <div class="form-group">
                                            <label>Service</label>
                                            <select class="form-control" id="service" name="service_id" required>
                                                <option value="" disabled selected>-- Choisir activité d'abord --
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        {{-- TYPE CONTRIBUTEUR --}}
                                        <div class="form-group">
                                            <label>Type de contributeur</label>
                                            <div class="radio-inputs">
                                                <label>
                                                    <input class="radio-input" type="radio" name="type_contributeur"
                                                        value="membre">
                                                    <span class="radio-tile"> Membre </span>
                                                </label>
                                                <label>
                                                    <input class="radio-input" type="radio" name="type_contributeur"
                                                        value="non_membre">
                                                    <span class="radio-tile"> Non membre </span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        {{-- TARIF --}}
                                        <div class="form-group">
                                            <label>Type de tarif</label>
                                            <div class="radio-inputs">
                                                <label>
                                                    <input class="radio-input" type="radio" name="type_tarif"
                                                        value="normal" checked>
                                                    <span class="radio-tile"> Tarif normal </span>
                                                </label>
                                                <label>
                                                    <input class="radio-input" type="radio" name="type_tarif"
                                                        value="special">
                                                    <span class="radio-tile"> Tarif spécial</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                {{-- MEMBRE --}}
                                <div class="form-group" id="bloc-membre">
                                    <label>Membre</label>
                                    <select class="form-control select2" name="membre_id">
                                        <option value="" disabled selected>-- Sélectionner --</option>
                                        @foreach (App\Models\DmsMembre::all() as $m)
                                            <option value="{{ $m->id }}">{{ $m->libelle_membre }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- NON MEMBRE --}}
                                <div id="bloc-non-membre" style="display:none">
                                    <div class="form-group">
                                        <label>Nom complet</label>
                                        <input type="text" class="form-control" name="nom_complet">
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Organisation</label>
                                                <input type="text" class="form-control" name="organisation">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label>Pays</label>
                                            <select class="form-control select2" name="pays_id">
                                                <option value="" disabled selected>-- Sélectionner --</option>
                                                @foreach (App\Models\Pay::all() as $m)
                                                    <option value="{{ $m->id }}">{{ $m->libelle_pays }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>Email</label>
                                            <input type="email" class="form-control" name="email">
                                        </div>
                                        <div class="col-md-6">
                                            <label>Téléphone</label>
                                            <input type="text" class="form-control" name="telephone">
                                        </div>
                                    </div>
                                </div>




                                <div class="row">
                                    <div class="col-md-3">
                                        {{-- QUANTITÉ --}}
                                        <div class="form-group">
                                            <label>Quantité</label>
                                            <input type="number" id="quantite" name="quantite" class="form-control"
                                                value="1" min="1" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        {{-- PRIX UNITAIRE --}}
                                        <div class="form-group">
                                            <label>Prix unitaire</label>
                                            <input type="number" id="prix_unitaire" class="form-control"
                                                name="prix_unitaire" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        {{-- DATE --}}
                                        <div class="form-group">
                                            <label>Date paiement</label>
                                            <input type="date" name="date_paiement" class="form-control"
                                                max="{{ date('Y-m-d') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        {{-- ANNÉE --}}
                                        <div class="form-group">
                                            <label>Année</label>
                                            <select name="annee" class="form-control" required>
                                                @for ($y = 2022; $y <= date('Y') + 1; $y++)
                                                    <option value="{{ $y }}"
                                                        {{ $y == date('Y') ? 'selected' : '' }}>
                                                        {{ $y }}
                                                    </option>
                                                @endfor
                                            </select>
                                            <input type="hidden" name="montant" id="montant_hidden">
                                        </div>
                                    </div>
                                </div>

                                {{-- TOTAL AMÉLIORÉ --}}
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="total-container">
                                            <div class="total-label">TOTAL À PAYER</div>
                                            <div class="total-amount" id="total_affiche">0 FCFA</div>
                                        </div>
                                    </div>
                                </div>

                                <button class="btn btn-primary btn-block">Enregistrer</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- LISTE DES SAISIES -->
                <div class="col-md-12">
                    <div class="access-container">
                        <div class="access-header">
                            <span><i class="bi bi-list-check"></i> LISTE DES ACTIVITÉS SAISIES</span>
                            <span class="badge" style="background: white; color: #0056b3;"
                                id="countBadge">{{ count($cotisations) }} Entrées</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="agrTable">
                                <thead>
                                    <tr>
                                        <th>Date paiement</th>
                                        <th>Année</th>
                                        <th>Évènement</th>
                                        <th>Activité</th>
                                        <th>Service</th>
                                        <th>Type Contributeur</th>
                                        <th>Contributeur</th>
                                        <th>Organisation</th>
                                        <th>Pays</th>
                                        <th>Email</th>
                                        <th>Téléphone</th>
                                        <th>Type Tarif</th>
                                        <th>Quantité</th>
                                        <th>Prix Unitaire</th>
                                        <th>Montant Normal</th>
                                        <th>Montant Total</th>
                                        <th>Observations</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($cotisations as $c)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($c->date_paiement)->format('d/m/Y') }}</td>
                                            <td>{{ $c->annee }}</td>
                                            <td>{{ $c->evenement?->libelle_event ?? 'N/A' }}</td>
                                            <td>{{ $c->activite?->libelle_activite ?? 'N/A' }}</td>
                                            <td>{{ $c->service?->libelle_service ?? 'N/A' }}</td>
                                            <td>
                                                @if ($c->type_contributeur === 'membre')
                                                    <span class="badge badge-primary">Membre</span>
                                                @else
                                                    <span class="badge badge-secondary">Non membre</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($c->type_contributeur === 'membre' && $c->membre)
                                                    {{ $c->membre->libelle_membre ?? $c->membre }}
                                                @else
                                                    {{ $c->nom_complet ?? 'N/A' }}
                                                @endif
                                            </td>
                                            <td>{{ $c->organisation ?? 'N/A' }}</td>
                                            <td>{{ $c->pays?->libelle_pays ?? 'N/A' }}</td>
                                            <td>{{ $c->email ?? 'N/A' }}</td>
                                            <td>{{ $c->telephone ?? 'N/A' }}</td>
                                            <td>
                                                @if ($c->type_tarif === 'normal')
                                                    <span class="badge badge-success">Normal</span>
                                                @else
                                                    <span class="badge badge-warning">Spécial</span>
                                                @endif
                                            </td>
                                            <td class="text-right">{{ $c->quantite }}</td>
                                            <td class="text-right">{{ number_format($c->prix_unitaire, 0, ',', ' ') }}
                                                FCFA</td>
                                            <td class="text-right">{{ number_format($c->montant_normal, 0, ',', ' ') }}
                                                FCFA</td>
                                            <td class="text-right">{{ number_format($c->montant_total, 0, ',', ' ') }}
                                                FCFA</td>
                                            <td>
                                                <span class="obs-text" title="{{ $c->observations ?? '' }}">
                                                    {{ Str::limit($c->observations, 30) ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td class="action-btns" style="white-space: nowrap;">
                                                <a href="{{ route('dms.agr.edit', $c->id) }}"
                                                    class="btn btn-sm btn-info" title="Modifier">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button class="btn btn-sm btn-danger btn-delete"
                                                    data-id="{{ $c->id }}" title="Supprimer">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                                <button class="btn btn-sm btn-info btn-view"
                                                    data-id="{{ $c->id }}" title="Voir détails">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="12" class="text-right">Totaux:</th>
                                        <th class="text-right" id="total-quantite">0</th>
                                        <th class="text-right" id="total-prix-unitaire">0 FCFA</th>
                                        <th class="text-right" id="total-montant-normal">0 FCFA</th>
                                        <th class="text-right" id="total-montant-total">0 FCFA</th>
                                        <th colspan="2"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL D'ÉDITION -->
    <div class="modal fade" id="editModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h4>Modifier la cotisation AGR</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit_id" name="id">

                        {{-- ACTIVITÉ --}}
                        <div class="form-group">
                            <label>Type d'activité</label>
                            <select class="form-control" name="activiy_id" id="edit_activiy" required>
                                <option value="" disabled selected>-- Sélectionner --</option>
                                @foreach (App\Models\DmsAgrCotisation::Activities() as $a)
                                    <option value="{{ $a->id }}">{{ $a->libelle }}</option>
                                @endforeach
                            </select>
                        </div>



                        {{-- MEMBRE --}}
                        <div class="form-group" id="edit_bloc-membre">
                            <label>Membre</label>
                            <select class="form-control select2" name="membre_id" id="edit_membre_id">
                                <option value="" disabled selected>-- Sélectionner --</option>
                                @foreach (App\Models\DmsMembre::all() as $m)
                                    <option value="{{ $m->id }}">{{ $m->libelle_membre }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- NON MEMBRE --}}
                        <div id="edit_bloc-non-membre" style="display:none">
                            <div class="form-group">
                                <label>Nom complet</label>
                                <input type="text" class="form-control" name="nom_complet" id="edit_nom_complet">
                            </div>
                            <div class="form-group">
                                <label>Organisation</label>
                                <input type="text" class="form-control" name="organisation" id="edit_organisation">
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Email</label>
                                    <input type="email" class="form-control" name="email" id="edit_email">
                                </div>
                                <div class="col-md-6">
                                    <label>Téléphone</label>
                                    <input type="text" class="form-control" name="telephone" id="edit_telephone">
                                </div>
                            </div>
                        </div>

                        {{-- DATE / ANNÉE / MONTANT --}}
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label>Date paiement</label>
                                <input type="date" class="form-control" name="date_paiement" id="edit_date" required>
                            </div>
                            <div class="col-md-4">
                                <label>Année</label>
                                <select class="form-control" name="annee" id="edit_annee" required>
                                    @for ($y = 2022; $y <= date('Y') + 1; $y++)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>Montant (FCFA)</label>
                                <input type="number" class="form-control" name="montant" id="edit_montant" required
                                    min="1">
                            </div>
                        </div>

                        {{-- OBS --}}
                        <div class="form-group mt-3">
                            <label>Observations</label>
                            <textarea class="form-control" name="observations" id="edit_observations"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- CSS pour le total amélioré --}}
    <style>
        /* ================================
                               STYLE WINDOWS 11 - BOUTON ENREGISTRER
                               ================================ */

        .btn-primary {
            background: #0056b3 !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            border-radius: 4px !important;
            /* Coins légèrement arrondis style Windows */
            padding: 10px 24px !important;
            font-weight: 400 !important;
            font-size: 14px !important;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif !important;
            letter-spacing: 0.3px !important;
            text-transform: none !important;
            /* Pas de majuscules style Windows */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
            position: relative !important;
            transition: all 0.1s ease !important;
            width: 100% !important;
            margin-top: 16px !important;
            color: white !important;
            /* cursor: default !important; */
            /* Curseur style Windows */
            /* line-height: 1.5 !important; */
        }

        /* Effet de survol style Windows 11 */
        .btn-primary:hover {
            background: #0066cc !important;
            box-shadow: 0 4px 8px rgba(0, 86, 179, 0.2) !important;
            border-color: rgba(255, 255, 255, 0.2) !important;
        }

        /* Effet de pression (comme un vrai bouton Windows) */
        .btn-primary:active {
            background: #004494 !important;
            transform: scale(0.99) !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1) !important;
            border-color: rgba(0, 0, 0, 0.1) !important;
        }

        /* Focus style Windows (anneau de focus) */
        .btn-primary:focus {
            outline: 2px solid #0056b3 !important;
            outline-offset: 2px !important;
            box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.8) !important;
        }

        /* État désactivé style Windows */
        .btn-primary:disabled {
            background: #cccccc !important;
            color: #666666 !important;
            box-shadow: none !important;
            cursor: not-allowed !important;
            border-color: #dddddd !important;
        }

        /* ================================
                               STYLE WINDOWS 11 - SPAN TOTAL (PANEL DE PARAMÈTRES)
                               ================================ */

        .total-container {
            background: #f5f5f5 !important;
            /* Fond style Windows 10/11 */
            border-radius: 8px !important;
            padding: 12px 16px !important;
            margin: 16px 0 12px !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05) !important;
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            color: #000000 !important;
            transition: all 0.1s ease !important;
            border: 1px solid #e0e0e0 !important;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif !important;
        }

        /* Effet de survol léger */
        .total-container:hover {
            background: #ffffff !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
            border-color: #0056b3 !important;
        }

        .total-label {
            font-size: 13px !important;
            font-weight: 500 !important;
            letter-spacing: 0.3px !important;
            text-transform: uppercase !important;
            color: #333333 !important;
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
        }

        /* Icône style Windows */
        .total-label::before {
            content: '' !important;
            display: inline-block !important;
            width: 16px !important;
            height: 16px !important;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%230056b3'%3E%3Cpath d='M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.5 15h-3v-2h3v2zm0-4h-3V7h3v6z'/%3E%3C/svg%3E") !important;
            background-size: contain !important;
            opacity: 0.8 !important;
        }

        .total-amount {
            font-size: 20px !important;
            font-weight: 600 !important;
            background: white !important;
            padding: 4px 16px !important;
            border-radius: 4px !important;
            border: 1px solid #d0d0d0 !important;
            color: #0056b3 !important;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.03) !important;
            letter-spacing: 0.5px !important;
            line-height: 1.5 !important;
            font-family: 'Segoe UI', monospace !important;
        }

        .total-amount::after {
            content: ' FCFA' !important;
            font-size: 14px !important;
            font-weight: 400 !important;
            margin-left: 4px !important;
            color: #666666 !important;
        }

        /* Animation style Windows (subtile) */
        .total-amount.changed {
            animation: windowsPulse 0.2s ease !important;
        }

        @keyframes windowsPulse {
            0% {
                background: white !important;
                border-color: #d0d0d0 !important;
            }

            50% {
                background: #e6f0ff !important;
                border-color: #0056b3 !important;
            }

            100% {
                background: white !important;
                border-color: #d0d0d0 !important;
            }
        }

        /* ================================
                               STYLE PANEL DE CONTRÔLE WINDOWS
                               ================================ */


        /* Checkboxes et radios style Windows */
        .radio-inputs {
            background: #f8f9fa !important;
            padding: 8px !important;
            border-radius: 4px !important;
            border: 1px solid #e0e0e0 !important;
        }

        .radio-tile {
            font-family: 'Segoe UI', system-ui, sans-serif !important;
            font-size: 13px !important;
        }
    </style>
@stop

@section('javascript')
    <script>
        $(document).ready(function() {

            // ================================
            // VARIABLES
            // ================================

            let prixUnitaire = 0;

            const $activite = $('#activite');
            const $service = $('#service');
            const $quantite = $('#quantite');
            const $prixInput = $('#prix_unitaire');
            const $totalAffiche = $('#total_affiche');
            const $montantHidden = $('#montant_hidden');
            const $membreSelect = $('select[name="membre_id"]');
            const $typeContrib = $('input[name="type_contributeur"]');
            const $typeTarif = $('input[name="type_tarif"]');
            const $blocMembre = $('#bloc-membre');
            const $blocNonMembre = $('#bloc-non-membre');
            const $datePaiement = $('input[name="date_paiement"]');

            // ================================
            // INITIALISATION
            // ================================

            $('.select2').select2({
                width: '100%'
            });

            $quantite.val(1).attr('min', 1);
            $prixInput.prop('readonly', true);
            $totalAffiche.text('0 FCFA');

            // Bloquer date future
            let today = new Date().toISOString().split('T')[0];
            $datePaiement.attr('max', today);

            // ================================
            // CHARGER SERVICES SELON ACTIVITÉ
            // ================================

            $activite.change(function() {

                let activiteId = $(this).val();

                if (!activiteId) return;

                $service.html('<option>Chargement...</option>');

                fetch('/gouvernances/admin/dms/services-by-activite/' + activiteId)
                    .then(res => res.json())
                    .then(data => {

                        $service.html('<option value="" disabled selected>-- Sélectionner --</option>');

                        data.forEach(service => {
                            $service.append(
                                `<option value="${service.id}">${service.libelle_service}</option>`
                            );
                        });

                        resetPrix();
                    });
            });

            // ================================
            // GESTION MEMBRE / NON MEMBRE
            // ================================

            $typeContrib.change(function() {

                if (this.value === 'membre') {
                    $blocMembre.slideDown(200);
                    $blocNonMembre.slideUp(200);
                    $membreSelect.prop('required', true);
                } else {
                    $blocMembre.slideUp(200);
                    $blocNonMembre.slideDown(200);
                    $membreSelect.prop('required', false).val('');
                }

                chargerPrix();
            });

            // ================================
            // CHARGER PRIX VIA BACKEND
            // ================================

            function chargerPrix() {

                let serviceId = $service.val();
                if (!serviceId) return;

                let type = $('input[name="type_contributeur"]:checked').val();
                let memberId = 0;

                if (type === 'membre') {
                    memberId = $membreSelect.val();
                    if (!memberId) return;
                }

                $.ajax({
                    url: "{{ route('dms.get-activity-price') }}",
                    method: "POST",
                    data: {
                        service_id: serviceId,
                        member_id: memberId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {

                        prixUnitaire = parseFloat(response.price) || 0;

                        if ($('input[name="type_tarif"]:checked').val() === 'normal') {
                            $prixInput.val(prixUnitaire);
                        }

                        calculerTotal();
                    },
                    error: function() {
                        toastr.error("Erreur récupération du prix");
                        resetPrix();
                    }
                });
            }

            $service.change(chargerPrix);
            $membreSelect.change(chargerPrix);

            // Ajout de la détection des changements pour les champs non-membre qui pourraient impacter le prix
            $('select[name="pays_id"]').change(function() {
                // Si le pays peut impacter le prix, on recharge le prix
                // À décommenter si vous voulez que le pays influence le prix
                // chargerPrix();
            });

            // ================================
            // TARIF NORMAL / SPECIAL
            // ================================

            $typeTarif.change(function() {

                if (this.value === 'normal') {
                    $prixInput.prop('readonly', true);
                    chargerPrix();
                } else {
                    $prixInput.prop('readonly', false);
                    prixUnitaire = parseFloat($prixInput.val()) || 0;
                }

                calculerTotal();
            });

            // ================================
            // CALCUL TOTAL
            // ================================

            function calculerTotal() {

                let qte = parseInt($quantite.val()) || 1;

                if (qte < 1) {
                    qte = 1;
                    $quantite.val(1);
                }

                let typeTarif = $('input[name="type_tarif"]:checked').val();

                let total = 0;

                if (typeTarif === 'normal') {
                    total = prixUnitaire * qte;
                } else {
                    let prixSpecial = parseFloat($prixInput.val()) || 0;
                    total = prixSpecial * qte;
                }

                let totalFormatted = total.toLocaleString('fr-FR') + ' FCFA';

                // Animation du total si changement
                if ($totalAffiche.text() !== totalFormatted) {
                    $totalAffiche.addClass('changed');
                    setTimeout(() => {
                        $totalAffiche.removeClass('changed');
                    }, 500);
                }

                $totalAffiche.text(totalFormatted);
                $montantHidden.val(total);
            }

            $quantite.on('input', calculerTotal);
            $prixInput.on('input', calculerTotal);

            // ================================
            // RESET PRIX
            // ================================

            function resetPrix() {
                prixUnitaire = 0;
                $prixInput.val('');
                $totalAffiche.text('0 FCFA');
                $montantHidden.val('');
            }

            // ================================
            // VALIDATION AVANT SUBMIT
            // ================================

            $('#agrForm').submit(function(e) {

                let type = $('input[name="type_contributeur"]:checked').val();

                if (type === 'membre' && !$membreSelect.val()) {
                    toastr.error("Veuillez sélectionner un membre");
                    e.preventDefault();
                    return false;
                }

                if (type === 'non_membre') {
                    if (!$('input[name="nom_complet"]').val()) {
                        toastr.error("Veuillez saisir le nom complet");
                        e.preventDefault();
                        return false;
                    }
                    if (!$('select[name="pays_id"]').val()) {
                        toastr.error("Veuillez sélectionner le pays");
                        e.preventDefault();
                        return false;
                    }
                    if (!$('input[name="email"]').val()) {
                        toastr.warning("L'email est optionnel mais recommandé");
                    }
                }

                if (!$service.val()) {
                    toastr.error("Veuillez sélectionner un service");
                    e.preventDefault();
                    return false;
                }

                if (!$datePaiement.val()) {
                    toastr.error("Veuillez choisir une date");
                    e.preventDefault();
                    return false;
                }

                let selectedDate = new Date($datePaiement.val());
                let now = new Date();

                if (selectedDate > now) {
                    toastr.error("La date ne peut pas être future");
                    e.preventDefault();
                    return false;
                }

            });

        });
    </script>

    <script>
        // ================================
        // SUBMIT AVEC BARRE DE PROGRESSION
        // ================================

        $('#agrForm').on('submit', function(e) {
            e.preventDefault();

            const $submitBtn = $(this).find('button[type="submit"]');
            const originalText = $submitBtn.html();

            // Créer une barre de progression
            const $progressBar = $('<div class="progress" style="height: 5px; margin-top: 10px;">' +
                '<div class="progress-bar progress-bar-striped progress-bar-animated" ' +
                'style="width: 0%; background-color: #0056b3;">' +
                '</div></div>');

            $submitBtn.after($progressBar);
            const $progress = $progressBar.find('.progress-bar');

            // Désactiver le bouton
            $submitBtn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Enregistrement...');

            let formData = new FormData(this);
            formData.set('montant', $('#montant_hidden').val());

            // Simuler la progression
            let progress = 0;
            const interval = setInterval(() => {
                progress += 10;
                if (progress <= 90) {
                    $progress.css('width', progress + '%');
                }
            }, 100);

            $.ajax({
                url: "{{ route('dms.agr.store') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    clearInterval(interval);
                    $progress.css('width', '100%');

                    setTimeout(() => {
                        toastr.success(response.message, 'Succès');
                        resetForm();
                        $progressBar.remove();
                        $submitBtn.prop('disabled', false).html(originalText);

                        // Recharger la table
                        if ($.fn.DataTable.isDataTable('#agrTable')) {
                            $('#agrTable').DataTable().ajax.reload();
                        } else {
                            location.reload();
                        }
                    }, 500);
                },
                error: function(xhr) {
                    clearInterval(interval);
                    $progressBar.remove();
                    $submitBtn.prop('disabled', false).html(originalText);

                    handleError(xhr);
                }
            });
        });

        // Fonction de réinitialisation du formulaire
        function resetForm() {
            $('#agrForm')[0].reset();
            $('#quantite').val(1);
            $('#prix_unitaire').val('');
            $('#total_affiche').text('0 FCFA');
            $('#montant_hidden').val('');
            $('.select2').val(null).trigger('change');
            $('#bloc-membre').show();
            $('#bloc-non-membre').hide();
            $('input[name="type_contributeur"]').prop('checked', false);
            $('input[name="type_tarif"]').first().prop('checked', true);
            $('.is-invalid').removeClass('is-invalid');
        }

        // Fonction de gestion d'erreur
        function handleError(xhr) {
            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors;
                let firstError = Object.values(errors)[0][0];
                toastr.error(firstError, 'Erreur de validation');

                // Surligner les champs en erreur
                $('.is-invalid').removeClass('is-invalid');
                $.each(errors, function(field) {
                    $('[name="' + field + '"]').addClass('is-invalid');
                });
            } else {
                toastr.error(
                    xhr.responseJSON?.message || 'Une erreur est survenue',
                    'Erreur serveur'
                );
            }
            console.error('Erreur AJAX:', xhr);
        }
    </script>

    <script>
        $(document).ready(function() {

            // Initialisation de DataTable
            var table = $('#agrTable').DataTable({
                dom: 'Bfrtip',
                buttons: [

                    {
                        text: '<i class="bi bi-file-excel"></i> Exporter en Excel',
                        extend: 'excelHtml5',
                        className: 'btn btn-success btn-sm',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15,
                                16
                            ],
                            format: {
                                body: function(data, row, column, node) {
                                    return data.replace(/<[^>]*>/g, '').trim();
                                }
                            }
                        }
                    },



                ],
                order: [
                    [0, 'desc']
                ], // Trier par date paiement décroissante
                pageLength: 25,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "Tous"]
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json'
                },
                columnDefs: [{
                        targets: [12, 13, 14, 15], // Colonnes numériques
                        className: 'text-right'
                    },
                    {
                        targets: [16], // Colonne observations
                        className: 'text-left',
                        width: '150px'
                    },
                    {
                        targets: [17], // Colonne actions
                        orderable: false,
                        searchable: false,
                        width: '100px'
                    }
                ],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();

                    // Fonction pour formater les nombres
                    function formatNumber(n) {
                        return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
                    }

                    // Calcul des totaux
                    var totalQuantite = api.column(12).data().reduce(function(a, b) {
                        return parseInt(a) + parseInt(b);
                    }, 0);

                    var totalPrixUnitaire = api.column(13).data().reduce(function(a, b) {
                        return parseFloat(a) + parseFloat(b);
                    }, 0);

                    var totalMontantNormal = api.column(14).data().reduce(function(a, b) {
                        return parseFloat(a) + parseFloat(b);
                    }, 0);

                    var totalMontantTotal = api.column(15).data().reduce(function(a, b) {
                        return parseFloat(a) + parseFloat(b);
                    }, 0);

                    // Mise à jour du footer
                    $(api.column(12).footer()).html(formatNumber(totalQuantite));
                    $(api.column(13).footer()).html(formatNumber(totalPrixUnitaire) + ' FCFA');
                    $(api.column(14).footer()).html(formatNumber(totalMontantNormal) + ' FCFA');
                    $(api.column(15).footer()).html(formatNumber(totalMontantTotal) + ' FCFA');
                }
            });
            // ================================
            // RECHERCHE PERSONNALISÉE
            // ================================

            // Recherche par colonne
            $('#agrTable thead tr').clone(true).appendTo('#agrTable thead');
            $('#agrTable thead tr:eq(1) th').each(function(i) {
                var title = $(this).text();
                if (i != 17) { // Pas de recherche sur la colonne actions
                    $(this).html(
                        '<input type="text" class="form-control form-control-sm" placeholder="Filtrer ' +
                        title + '" />');

                    $('input', this).on('keyup change', function() {
                        if (table.column(i).search() !== this.value) {
                            table.column(i).search(this.value).draw();
                        }
                    });
                } else {
                    $(this).html('');
                }
            });

            // ================================
            // BOUTON DE RAFRAÎCHISSEMENT
            // ================================

            $('<button class="btn btn-sm btn-warning ml-2"><i class="bi bi-arrow-repeat"></i> Rafraîchir</button>')
                .appendTo('div.dt-buttons')
                .on('click', function() {
                    table.ajax.reload();
                });

            // ================================
            // GESTION DES STATISTIQUES
            // ================================

            function updateStatistics() {
                var info = table.page.info();
                $('#countBadge').text(info.recordsDisplay + ' Entrées');
            }

            table.on('draw', updateStatistics);
            updateStatistics();
        });
    </script>



    <!-- DataTables Core JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>

    <!-- DataTables Buttons - Ordre important ! -->
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap4.min.js"></script>

    <!-- Extensions pour les exports -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <!-- Boutons HTML5 et autres -->
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.colVis.min.js"></script>

    <!-- Responsive (optionnel) -->
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap4.min.js"></script>
@stop
