@extends('voyager::master')

@section('page_title', 'Modifier la cotisation AGR')

@section('css')
    <!-- DataTables Core CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap4.min.css">

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.0.0/dist/select2-bootstrap4.min.css">

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
            font-size: 13px;
        }

        .form-control,
        .select2-container--bootstrap4 .select2-selection {
            border-radius: 2px !important;
            border: 1px solid #bccce0 !important;
            box-shadow: none !important;
            height: 38px !important;
        }

        .form-control:focus,
        .select2-container--bootstrap4 .select2-selection:focus {
            border-color: #0056b3 !important;
            box-shadow: 0 0 5px rgba(0, 86, 179, 0.15) !important;
        }

        /* Boutons */
        .btn-primary {
            background: #0056b3 !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            border-radius: 4px !important;
            padding: 10px 24px !important;
            font-weight: 400 !important;
            font-size: 14px !important;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif !important;
            letter-spacing: 0.3px !important;
            text-transform: none !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
            transition: all 0.1s ease !important;
            color: white !important;
        }

        .btn-primary:hover {
            background: #0066cc !important;
            box-shadow: 0 4px 8px rgba(0, 86, 179, 0.2) !important;
        }

        .btn-primary:active {
            background: #004494 !important;
            transform: scale(0.99) !important;
        }

        .btn-primary:focus {
            outline: 2px solid #0056b3 !important;
            outline-offset: 2px !important;
            box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.8) !important;
        }

        .btn-secondary {
            background: #6c757d !important;
            border: 1px solid #5a6268 !important;
            color: white !important;
        }

        .btn-secondary:hover {
            background: #5a6268 !important;
        }

        /* Badges */
        .badge-primary {
            background-color: #0056b3;
            color: white;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
        }

        .badge-secondary {
            background-color: #6c757d;
            color: white;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
        }

        .badge-success {
            background-color: #28a745;
            color: white;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
        }

        .badge-warning {
            background-color: #ffc107;
            color: #212529;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
        }

        .badge-info {
            background-color: #17a2b8;
            color: white;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
        }

        /* Boutons radio style Windows */
        .radio-inputs {
            display: flex;
            background: #f8f9fa;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #e0e0e0;
            gap: 10px;
        }

        .radio-inputs label {
            flex: 1;
            margin: 0;
            cursor: pointer;
        }

        .radio-input {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .radio-tile {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            background: white;
            border: 2px solid #b5bfd9;
            border-radius: 4px;
            transition: all 0.2s ease;
            font-size: 13px;
            font-family: 'Segoe UI', sans-serif;
            text-align: center;
        }

        .radio-input:checked+.radio-tile {
            border-color: #0056b3;
            background-color: #e6f0ff;
            color: #0056b3;
            font-weight: 500;
        }

        .radio-tile:hover {
            border-color: #0056b3;
        }

        /* Blocs conditionnels */
        #bloc-membre,
        #bloc-non-membre {
            margin-top: 15px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 4px;
            border-left: 3px solid #0056b3;
        }

        /* Total container */
        .total-container {
            background: #f5f5f5;
            border-radius: 8px;
            padding: 12px 16px;
            margin: 16px 0 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid #e0e0e0;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        .total-container:hover {
            background: #ffffff;
            border-color: #0056b3;
        }

        .total-label {
            font-size: 13px;
            font-weight: 500;
            text-transform: uppercase;
            color: #333333;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .total-label::before {
            content: '';
            display: inline-block;
            width: 16px;
            height: 16px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%230056b3'%3E%3Cpath d='M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.5 15h-3v-2h3v2zm0-4h-3V7h3v6z'/%3E%3C/svg%3E");
            background-size: contain;
            opacity: 0.8;
        }

        .total-amount {
            font-size: 20px;
            font-weight: 600;
            background: white;
            padding: 4px 16px;
            border-radius: 4px;
            border: 1px solid #d0d0d0;
            color: #0056b3;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.03);
            letter-spacing: 0.5px;
            font-family: 'Segoe UI', monospace;
        }

        .total-amount::after {
            content: ' FCFA';
            font-size: 14px;
            font-weight: 400;
            margin-left: 4px;
            color: #666666;
        }

        .total-amount.changed {
            animation: windowsPulse 0.2s ease;
        }

        @keyframes windowsPulse {
            0% {
                background: white;
                border-color: #d0d0d0;
            }

            50% {
                background: #e6f0ff;
                border-color: #0056b3;
            }

            100% {
                background: white;
                border-color: #d0d0d0;
            }
        }

        /* Card info */
        .info-card {
            background-color: #e7f1ff;
            border-left: 4px solid #0056b3;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .info-card i {
            color: #0056b3;
            font-size: 1.2rem;
            margin-right: 10px;
        }

        /* Action buttons */
        .action-buttons {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .action-buttons .btn {
            min-width: 120px;
        }

        /* Error styles */
        .is-invalid {
            border-color: #dc3545 !important;
        }

        .invalid-feedback {
            display: block;
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
        }
    </style>
@stop

@section('page_header')
    <div class="container-fluid">
        <div class="access-ribbon">
            <div style="font-weight: bold; color: #0056b3; font-size: 16px;">
                <i class="bi bi-pencil-square"></i> MODIFICATION COTISATION AGR
            </div>
            <div class="ribbon-actions">
                <a href="{{ route('voyager.dms-agr-cotisations.index') }}" class="btn">
                    <i class="bi bi-arrow-left"></i> Retour à la liste
                </a>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <!-- Info card -->
                    <div class="info-card">
                        <i class="bi bi-info-circle"></i>
                        <span>Vous êtes en train de modifier la cotisation du
                            <strong>{{ \Carbon\Carbon::parse($cotisation->date_paiement)->format('d/m/Y') }}</strong> pour
                            <strong>{{ $cotisation->evenement?->libelle_event ?? 'N/A' }}</strong></span>
                    </div>

                    <!-- FORMULAIRE DE MODIFICATION -->
                    <div class="access-container">
                        <div class="access-header">
                            <span><i class="bi bi-pencil-square"></i> MODIFIER LA COTISATION AGR</span>
                        </div>
                        <div class="form-section">
                            <form id="editAgrForm" method="POST" action="{{ route('dms.agr.update', $cotisation->id) }}">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-4">
                                        {{-- EVENEMENT --}}
                                        <div class="form-group">
                                            <label>Évènement </label>
                                            <select class="form-control @error('evenement_id') is-invalid @enderror"
                                                name="evenement_id" id="evenement_id" required>
                                                <option value="" disabled>-- Sélectionner --</option>
                                                @foreach (App\Models\DmsEvenementsAgr::ActiveEventsAgrs() as $e)
                                                    <option value="{{ $e->id }}"
                                                        {{ old('evenement_id', $cotisation->evenement_id) == $e->id ? 'selected' : '' }}>
                                                        {{ $e->libelle_event }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('evenement_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        {{-- ACTIVITÉ --}}
                                        <div class="form-group">
                                            <label>Activité </label>
                                            <select class="form-control @error('activite_id') is-invalid @enderror"
                                                id="activite" name="activite_id" required>
                                                <option value="" disabled>-- Sélectionner --</option>
                                                @foreach (App\Models\DmsActiviteAgr::active()->get() as $a)
                                                    <option value="{{ $a->id }}"
                                                        {{ old('activite_id', $cotisation->activite_id) == $a->id ? 'selected' : '' }}>
                                                        {{ $a->libelle_activite }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('activite_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        {{-- SERVICE --}}
                                        <div class="form-group">
                                            <label>Service </label>
                                            <select class="form-control @error('service_id') is-invalid @enderror"
                                                id="service" name="service_id" required>
                                                <option value="" disabled>-- Choisir activité d'abord --</option>
                                            </select>
                                            @error('service_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        {{-- TYPE CONTRIBUTEUR --}}
                                        <div class="form-group">
                                            <label>Type de contributeur </label>
                                            <div class="radio-inputs">
                                                <label>
                                                    <input class="radio-input" type="radio" name="type_contributeur"
                                                        value="membre"
                                                        {{ old('type_contributeur', $cotisation->type_contributeur) == 'membre' ? 'checked' : '' }}>
                                                    <span class="radio-tile"> Membre </span>
                                                </label>
                                                <label>
                                                    <input class="radio-input" type="radio" name="type_contributeur"
                                                        value="non_membre"
                                                        {{ old('type_contributeur', $cotisation->type_contributeur) == 'non_membre' ? 'checked' : '' }}>
                                                    <span class="radio-tile"> Non membre </span>
                                                </label>
                                            </div>
                                            @error('type_contributeur')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        {{-- TARIF --}}
                                        <div class="form-group">
                                            <label>Type de tarif </label>
                                            <div class="radio-inputs">
                                                <label>
                                                    <input class="radio-input" type="radio" name="type_tarif"
                                                        value="normal"
                                                        {{ old('type_tarif', $cotisation->type_tarif) == 'normal' ? 'checked' : '' }}>
                                                    <span class="radio-tile"> Tarif normal </span>
                                                </label>
                                                <label>
                                                    <input class="radio-input" type="radio" name="type_tarif"
                                                        value="special"
                                                        {{ old('type_tarif', $cotisation->type_tarif) == 'special' ? 'checked' : '' }}>
                                                    <span class="radio-tile"> Tarif spécial</span>
                                                </label>
                                            </div>
                                            @error('type_tarif')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                {{-- MEMBRE --}}
                                <div class="form-group" id="bloc-membre"
                                    style="{{ old('type_contributeur', $cotisation->type_contributeur) == 'membre' ? '' : 'display:none' }}">
                                    <label>Membre </label>
                                    <select class="form-control select2 @error('membre_id') is-invalid @enderror"
                                        name="membre_id" id="membre_id">
                                        <option value="" disabled selected>-- Sélectionner --</option>
                                        @foreach (App\Models\DmsMembre::all() as $m)
                                            <option value="{{ $m->id }}"
                                                {{ old('membre_id', $cotisation->membre_id) == $m->id ? 'selected' : '' }}>
                                                {{ $m->libelle_membre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('membre_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- NON MEMBRE --}}
                                <div id="bloc-non-membre"
                                    style="{{ old('type_contributeur', $cotisation->type_contributeur) == 'non_membre' ? '' : 'display:none' }}">
                                    <div class="form-group">
                                        <label>Nom complet </label>
                                        <input type="text"
                                            class="form-control @error('nom_complet') is-invalid @enderror"
                                            name="nom_complet"
                                            value="{{ old('nom_complet', $cotisation->nom_complet) }}">
                                        @error('nom_complet')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Organisation</label>
                                                <input type="text"
                                                    class="form-control @error('organisation') is-invalid @enderror"
                                                    name="organisation"
                                                    value="{{ old('organisation', $cotisation->organisation) }}">
                                                @error('organisation')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label>Pays</label>
                                            <select class="form-control select2 @error('pays_id') is-invalid @enderror"
                                                name="pays_id">
                                                <option value="" disabled selected>-- Sélectionner --</option>
                                                @foreach (App\Models\Pay::all() as $p)
                                                    <option value="{{ $p->id }}"
                                                        {{ old('pays_id', $cotisation->pays_id) == $p->id ? 'selected' : '' }}>
                                                        {{ $p->libelle_pays }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('pays_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>Email</label>
                                            <input type="email"
                                                class="form-control @error('email') is-invalid @enderror" name="email"
                                                value="{{ old('email', $cotisation->email) }}">
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label>Téléphone</label>
                                            <input type="text"
                                                class="form-control @error('telephone') is-invalid @enderror"
                                                name="telephone" value="{{ old('telephone', $cotisation->telephone) }}">
                                            @error('telephone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        {{-- QUANTITÉ --}}
                                        <div class="form-group">
                                            <label>Quantité </label>
                                            <input type="number" id="quantite" name="quantite"
                                                class="form-control @error('quantite') is-invalid @enderror"
                                                value="{{ old('quantite', $cotisation->quantite) }}" min="1"
                                                required>
                                            @error('quantite')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        {{-- PRIX UNITAIRE --}}
                                        <div class="form-group">
                                            <label>Prix unitaire</label>
                                            <input type="number" id="prix_unitaire" class="form-control"
                                                name="prix_unitaire"
                                                value="{{ old('prix_unitaire', $cotisation->prix_unitaire) }}"
                                                {{ old('type_tarif', $cotisation->type_tarif) == 'normal' ? 'readonly' : '' }}>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        {{-- DATE --}}
                                        <div class="form-group">
                                            <label>Date paiement </label>
                                            <input type="date" name="date_paiement"
                                                class="form-control @error('date_paiement') is-invalid @enderror"
                                                max="{{ date('Y-m-d') }}"
                                                value="{{ old('date_paiement', $cotisation->date_paiement) }}" required>
                                            @error('date_paiement')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        {{-- ANNÉE --}}
                                        <div class="form-group">
                                            <label>Année </label>
                                            <select name="annee"
                                                class="form-control @error('annee') is-invalid @enderror" required>
                                                @for ($y = 2022; $y <= date('Y') + 1; $y++)
                                                    <option value="{{ $y }}"
                                                        {{ old('annee', $cotisation->annee) == $y ? 'selected' : '' }}>
                                                        {{ $y }}
                                                    </option>
                                                @endfor
                                            </select>
                                            @error('annee')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <input type="hidden" name="montant" id="montant_hidden"
                                                value="{{ old('montant', $cotisation->montant_total) }}">
                                        </div>
                                    </div>
                                </div>

                                {{-- Observations --}}
                                <div class="form-group">
                                    <label>Observations</label>
                                    <textarea class="form-control @error('observations') is-invalid @enderror" name="observations" rows="2">{{ old('observations', $cotisation->observations) }}</textarea>
                                    @error('observations')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- TOTAL --}}
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="total-container">
                                            <div class="total-label">TOTAL À PAYER</div>
                                            <div class="total-amount" id="total_affiche">
                                                {{ number_format($cotisation->montant_total, 0, ',', ' ') }} FCFA
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="action-buttons">
                                    <a href="{{ route('voyager.dms-agr-cotisations.index') }}" class="btn btn-secondary">
                                        <i class="bi bi-x-circle"></i> Annuler
                                    </a>
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                        <i class="bi bi-check-circle"></i> Mettre à jour
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('javascript')
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // ================================
            // VARIABLES
            // ================================

            let prixUnitaire = {{ old('prix_unitaire', $cotisation->prix_unitaire) ?? 0 }};
            let serviceIdInitial = {{ old('service_id', $cotisation->service_id) ?? 0 }};

            const $activite = $('#activite');
            const $service = $('#service');
            const $quantite = $('#quantite');
            const $prixInput = $('#prix_unitaire');
            const $totalAffiche = $('#total_affiche');
            const $montantHidden = $('#montant_hidden');
            const $membreSelect = $('#membre_id');
            const $typeContrib = $('input[name="type_contributeur"]');
            const $typeTarif = $('input[name="type_tarif"]');
            const $blocMembre = $('#bloc-membre');
            const $blocNonMembre = $('#bloc-non-membre');
            const $datePaiement = $('input[name="date_paiement"]');

            // ================================
            // INITIALISATION
            // ================================

            // Initialisation Select2
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            // Bloquer date future
            let today = new Date().toISOString().split('T')[0];
            $datePaiement.attr('max', today);

            // ================================
            // CHARGER SERVICES SELON ACTIVITÉ
            // ================================

            function loadServices(activiteId, selectedServiceId = null) {
                if (!activiteId) return;

                $service.html('<option value="" disabled>Chargement...</option>');

                fetch('/gouvernances/admin/dms/services-by-activite/' + activiteId)
                    .then(res => res.json())
                    .then(data => {
                        $service.html('<option value="" disabled selected>-- Sélectionner --</option>');

                        data.forEach(service => {
                            let selected = (selectedServiceId && service.id == selectedServiceId) ?
                                'selected' : '';
                            $service.append(
                                `<option value="${service.id}" ${selected}>${service.libelle_service}</option>`
                                );
                        });

                        if (selectedServiceId) {
                            chargerPrix();
                        }
                    });
            }

            // Charger les services au chargement de la page
            let activiteId = $activite.val();
            if (activiteId) {
                loadServices(activiteId, serviceIdInitial);
            }

            $activite.change(function() {
                let activiteId = $(this).val();
                loadServices(activiteId);
                resetPrix();
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
                    $membreSelect.prop('required', false).val('').trigger('change');
                }
                chargerPrix();
            });

            // ================================
            // CHARGER PRIX VIA BACKEND
            // ================================

            function chargerPrix() {
                let serviceId = $service.val();
                if (!serviceId) return;

                let type = $typeContrib.filter(':checked').val();
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

                        if ($typeTarif.filter(':checked').val() === 'normal') {
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

                let typeTarif = $typeTarif.filter(':checked').val();
                let total = 0;

                if (typeTarif === 'normal') {
                    total = prixUnitaire * qte;
                } else {
                    let prixSpecial = parseFloat($prixInput.val()) || 0;
                    total = prixSpecial * qte;
                }

                let totalFormatted = total.toLocaleString('fr-FR') + ' FCFA';

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

            // Calcul initial
            calculerTotal();

            // ================================
            // RESET PRIX
            // ================================

            function resetPrix() {
                prixUnitaire = 0;
                $prixInput.val('');
                calculerTotal();
            }

            // ================================
            // VALIDATION FORMULAIRE
            // ================================

            $('#editAgrForm').on('submit', function(e) {
                let type = $typeContrib.filter(':checked').val();

                if (!type) {
                    toastr.error("Veuillez sélectionner un type de contributeur");
                    e.preventDefault();
                    return false;
                }

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

                // Afficher la confirmation
                return confirm('Êtes-vous sûr de vouloir modifier cette cotisation ?');
            });

            // ================================
            // GESTION DES ERREURS BACKEND
            // ================================

            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    toastr.error("{{ $error }}", 'Erreur de validation');
                @endforeach
            @endif

            @if (session('success'))
                toastr.success("{{ session('success') }}", 'Succès');
            @endif

            @if (session('error'))
                toastr.error("{{ session('error') }}", 'Erreur');
            @endif
        });
    </script>
@stop
