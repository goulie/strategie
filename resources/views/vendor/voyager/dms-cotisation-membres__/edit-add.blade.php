@extends('voyager::master')

@section('page_title', $edit ? 'Modifier une cotisation' : 'Ajouter une cotisation')

@section('css')
    <style>
        /* Style inspiré de Microsoft Access pour Voyager */
        .access-container {
            max-width: 100%;
            margin: 10px auto;
            background-color: #ffffff;
            border: 1px solid #707070;
            box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.2);
        }

        .access-header {
            background: linear-gradient(to bottom, #f0f0f0, #d9d9d9);
            padding: 10px 15px;
            border-bottom: 2px solid #a0a0a0;
            display: flex;
            align-items: center;
        }

        .access-header h2 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
            color: #333;
            text-transform: uppercase;
        }

        .access-body {
            padding: 20px;
            background-color: #f5f5f5;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            font-weight: 600;
            color: #444;
            font-size: 12px;
            margin-bottom: 3px;
            display: block;
        }

        .form-control {
            height: 34px;
            padding: 4px 8px;
            font-size: 13px;
            border-radius: 2px;
            border: 1px solid #999;
            width: 100%;
        }

        .read-only-box {
            background-color: #e9ecef;
            border: 1px solid #ccc;
            padding: 5px 10px;
            height: 34px;
            border-radius: 2px;
            color: #333;
            font-weight: bold;
            font-size: 13px;
            line-height: 24px;
            display: flex;
            align-items: center;
        }

        .access-footer {
            background-color: #e8e8e8;
            padding: 10px 15px;
            border-top: 1px solid #ccc;
            text-align: right;
            display: flex;
            justify-content: space-between;
        }

        .btn-access {
            background: linear-gradient(to bottom, #ffffff, #e0e0e0);
            border: 1px solid #888;
            color: #333;
            font-weight: bold;
            padding: 7px 20px;
            font-size: 13px;
            cursor: pointer;
            border-radius: 3px;
        }

        .btn-access:hover {
            background: #d0d0d0;
        }

        .btn-save {
            background: linear-gradient(to bottom, #5cb85c, #449d44);
            color: white;
            border-color: #398439;
        }

        .btn-save:hover {
            background: linear-gradient(to bottom, #449d44, #398439);
        }

        .section-title {
            background-color: #4a6785;
            color: white;
            padding: 6px 10px;
            margin-bottom: 15px;
            font-size: 13px;
            font-weight: bold;
            border-radius: 2px;
        }

        .col-divider {
            border-left: 1px solid #ddd;
            padding-left: 25px;
        }

        textarea.form-control {
            min-height: 80px;
            resize: vertical;
        }

        .radio-inline {
            margin-right: 15px;
            display: inline-flex;
            align-items: center;
        }

        .radio-inline input {
            margin-right: 5px;
        }

        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 3px;
            font-size: 13px;
        }

        .alert-warning {
            background-color: #fff3cd;
            border-color: #ffeaa7;
            color: #856404;
        }

        .select2-container .select2-selection--single {
            height: 34px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 32px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 32px;
        }

        /* Styles pour la recherche et le tableau */
        #cotisations-table {
            font-size: 12px;
            background: white;
        }

        #cotisations-table th {
            background-color: #4a6785;
            color: white;
            font-weight: bold;
            padding: 8px;
        }

        #cotisations-table td {
            padding: 8px;
            vertical-align: middle;
        }

        .status-badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            display: inline-block;
        }

        .status-TOTAL {
            background-color: #d4edda;
            color: #155724;
        }

        .status-PARTIEL {
            background-color: #fff3cd;
            color: #856404;
        }

        .highlight {
            background-color: #ffffcc !important;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
        }

        .action-buttons .btn {
            padding: 3px 8px;
            font-size: 11px;
        }

        .loading-spinner {
            text-align: center;
            padding: 20px;
            color: #666;
        }

        .no-results {
            text-align: center;
            padding: 30px;
            color: #666;
            font-style: italic;
        }

        /* Animation pour la modal */
        .modal.fade .modal-dialog {
            transform: translate(0, -50px);
            transition: transform 0.3s ease-out;
        }

        .modal.show .modal-dialog {
            transform: translate(0, 0);
        }

        /* Style pour les badges */
        .badge {
            font-weight: 500;
            letter-spacing: 0.3px;
        }

        /* Style pour les cartes dans la modal */
        .modal-body .row {
            margin-bottom: -15px;
        }

        /* Hover effect pour les boutons d'action */
        .btn-outline-primary:hover {
            background-color: #4a6785;
            border-color: #4a6785;
            color: white;
        }

        .btn-outline-success:hover {
            background-color: #28a745;
            border-color: #28a745;
            color: white;
        }

        .btn-outline-warning:hover {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #212529;
        }

        /* Style pour la barre de progression */
        .progress {
            overflow: visible;
        }

        .progress-bar {
            position: relative;
            overflow: visible;
        }

        .progress-bar span {
            position: absolute;
            right: 10px;
            top: -20px;
            color: #333;
            font-weight: 600;
        }

        /* Animation pour les cartes */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-content>div {
            animation: fadeInUp 0.5s ease-out;
        }

        /* Animation pour la modal */
        .modal.fade .modal-dialog {
            transform: translate(0, -50px);
            transition: transform 0.3s ease-out;
        }

        .modal.show .modal-dialog {
            transform: translate(0, 0);
        }

        /* Style pour les badges */
        .badge {
            font-weight: 500;
            letter-spacing: 0.3px;
        }

        /* Style pour les cartes dans la modal */
        .modal-body .row {
            margin-bottom: -15px;
        }

        /* Hover effect pour les boutons d'action */
        .btn-outline-primary:hover {
            background-color: #4a6785;
            border-color: #4a6785;
            color: white;
        }

        .btn-outline-success:hover {
            background-color: #28a745;
            border-color: #28a745;
            color: white;
        }

        .btn-outline-warning:hover {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #212529;
        }

        /* Style pour la barre de progression */
        .progress {
            overflow: visible;
        }

        .progress-bar {
            position: relative;
            overflow: visible;
        }

        .progress-bar span {
            position: absolute;
            right: 10px;
            top: -20px;
            color: #333;
            font-weight: 600;
        }

        /* Animation pour les cartes */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-content>div {
            animation: fadeInUp 0.5s ease-out;
        }
    </style>
@stop

@section('content')
    <div class="page-content container-fluid">
        <div class="access-container">
            <!-- Header -->
            <div class="access-header">
                <span class="voyager-edit" style="margin-right:10px;"></span>
                <h2>{{ $edit ? 'Modifier ' : '' }} cotisation</h2>
            </div>

            <div class="access-body">
                <form id="cotisationForm" method="POST"
                    action="{{ $edit ? route('voyager.dms-cotisation-membres.update', $dataTypeContent->id) : route('cotisation.store') }}">
                    @csrf


                    <div class="row">
                        <!-- Colonne de Gauche : Identification -->
                        <div class="col-md-6">
                            <div class="section-title">1. Identification du Membre</div>
                            <div class="form-group">
                                <label for="membre_id">Sélectionner un membre</label>
                                <select class="select2 form-control" id="membre_id" name="membre_id" required
                                    {{ $edit ? 'disabled' : '' }}>
                                    <option value="" selected disabled>-- Sélectionner dans la liste --</option>
                                    @foreach ($membres as $membre)
                                        <option value="{{ $membre->id }}"
                                            data-plan="{{ $membre->plan_adhesion?->title_plan }}"
                                            data-montant="{{ $membre->plan_adhesion?->price_xof }}"
                                            {{ old('membre_id', $dataTypeContent->membre_id ?? '') == $membre->id ? 'selected' : '' }}>
                                            {{ $membre->libelle_membre }}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($edit)
                                    <input type="hidden" name="membre_id" value="{{ $dataTypeContent->membre_id }}">
                                    <small class="text-muted">Le membre ne peut pas être modifié.</small>
                                @endif
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Catégorie / Plan d'adhésion</label>
                                        <div id="display-categorie" class="read-only-box">
                                            {{ $edit ? $dataTypeContent->membre->plan_adhesion->title_plan ?? '---' : '---' }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Montant Annuel Attendu</label>
                                <div id="display-montant-auto" class="read-only-box"
                                    style="background-color: #fff9c4; border-color: #fbc02d;">
                                    {{ $edit ? number_format($dataTypeContent->montant_total_attendu ?? 0, 0, ',', ' ') . ' CFA' : '0 CFA' }}
                                </div>
                            </div>
                        </div>

                        <!-- Colonne de Droite : Paiement -->
                        <div class="col-md-6 col-divider">
                            <div class="section-title">2. Détails du Paiement</div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Type de versement</label><br>
                                        <label class="radio-inline">
                                            <input type="radio" name="type_versement" value="TOTAL"
                                                {{ old('type_versement', $dataTypeContent->status ?? 'TOTAL') == 'TOTAL' ? 'checked' : 'checked' }}>
                                            Total
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="type_versement" value="PARTIEL"
                                                {{ old('type_versement', $dataTypeContent->status ?? '') == 'PARTIEL' ? 'checked' : '' }}>
                                            Partiel
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="reference">Référence Facture</label>
                                        <input type="text" id="reference" name="reference" class="form-control"
                                            placeholder="Ex: OM-458922"
                                            value="{{ old('reference', $dataTypeContent->reference ?? '') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="montant">Somme versée</label>
                                        <input type="number" id="montant" name="montant" class="form-control"
                                            min="0" step="100" required
                                            value="{{ old('montant', $dataTypeContent->montant ?? '') }}">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Reste à payer</label>
                                        <div id="display-restant" class="read-only-box" style="color: #d9534f;">
                                            {{ $edit ? number_format($dataTypeContent->reste_a_payer ?? 0, 0, ',', ' ') . ' CFA' : '0 CFA' }}
                                        </div>
                                        <input type="hidden" id="reste_a_payer" name="reste_a_payer"
                                            value="{{ old('reste_a_payer', $dataTypeContent->reste_a_payer ?? 0) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="mode_paiement">Mode de paiement</label>
                                        <select id="mode_paiement" name="mode_paiement" class="form-control" required>
                                            <option value="" disabled>-- Choisir --</option>
                                            <option value="Cash"
                                                {{ old('mode_paiement', $dataTypeContent->mode_paiement ?? '') == 'Cash' ? 'selected' : '' }}>
                                                Cash</option>
                                            <option value="En ligne"
                                                {{ old('mode_paiement', $dataTypeContent->mode_paiement ?? '') == 'En ligne' ? 'selected' : '' }}>
                                                En ligne</option>
                                            <option value="cheque"
                                                {{ old('mode_paiement', $dataTypeContent->mode_paiement ?? '') == 'cheque' ? 'selected' : '' }}>
                                                Chèque</option>
                                            <option value="virement"
                                                {{ old('mode_paiement', $dataTypeContent->mode_paiement ?? '') == 'virement' ? 'selected' : '' }}>
                                                Virement</option>
                                                <option value="virement"
                                                {{ old('mode_paiement', $dataTypeContent->mode_paiement ?? '') == 'Mobile Money' ? 'selected' : '' }}>
                                                Mobile Money</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="annee_cotisation">Année</label>
                                        <select id="annee_cotisation" name="annee_cotisation" class="form-control" required>
                                            @for ($i = date('Y') - 4; $i <= date('Y') + 1; $i++)
                                                <option value="{{ $i }}"
                                                    {{ old('annee_cotisation', $dataTypeContent->annee_cotisation ?? date('Y')) == $i ? 'selected' : '' }}>
                                                    {{ $i }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="date_paiement">Date</label>
                                        <input type="date" id="date_paiement" name="date_paiement" class="form-control"
                                            value="{{ old('date_paiement', optional($dataTypeContent->date_paiement)->format('Y-m-d') ?? date('Y-m-d')) }}"
                                            required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="observation">Observations</label>
                                <textarea class="form-control" name="observation" id="observation" rows="2"
                                    placeholder="Notes supplémentaires sur le paiement...">{{ old('observation', $dataTypeContent->observation ?? '') }}</textarea>
                            </div>

                            <!-- Champs cachés pour les calculs automatiques -->
                            <input type="hidden" id="montant_total_attendu" name="montant_total_attendu"
                                value="{{ old('montant_total_attendu', $dataTypeContent->montant_total_attendu ?? 0) }}">
                            <input type="hidden" id="date_echeance" name="date_echeance"
                                value="{{ old('date_echeance', optional($dataTypeContent->date_echeance)->format('Y-m-d') ?? '') }}">
                            <input type="hidden" id="status" name="status"
                                value="{{ old('status', $dataTypeContent->status ?? 'TOTAL') }}">
                        </div>
                    </div>
                </form>
            </div>

            <div class="access-footer">
                <button type="button" class="btn btn-access" onclick="resetForm()">Nouveau</button>
                <div>
                    <a href="{{ route('voyager.dms-cotisation-membres.index') }}" class="btn btn-access"
                        style="margin-right:10px;">
                        <span class="voyager-list"></span> Retour à la liste
                    </a>
                    <button type="button" class="btn btn-access btn-save" onclick="saveRecord()">
                        <span class="voyager-check"></span> {{ $edit ? 'Mettre à jour' : 'Enregistrer' }} la cotisation
                    </button>
                </div>
            </div>
        </div>



        <!-- Après la liste des cotisations récentes, ajoutez : -->
        <div class="cotisations-list">
            <div class="section-title" style="display: flex; justify-content: space-between; align-items: center;">
                <span>Toutes les cotisations</span>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <!-- Filtre par année -->
                    <select id="filter-year" class="form-control" style="width: 120px; height: 30px; font-size: 12px;">
                        @for ($i = date('Y') - 4; $i <= date('Y') + 1; $i++)
                            <option value="{{ $i }}" {{ ($annee ?? date('Y')) == $i ? 'selected' : '' }}>
                                {{ $i }}
                            </option>
                        @endfor
                        <option value="all">Toutes</option>
                    </select>

                    <!-- Barre de recherche -->
                    <div class="input-group" style="width: 250px;">
                        <input type="text" id="search-cotisations" class="form-control"
                            placeholder="Rechercher un membre, référence, montant..."
                            style="height: 30px; font-size: 12px;">

                    </div>
                </div>

                <!-- Boutons d'export -->
                <div class="btn-group" role="group" style="margin-left: 10px;">
                    <a href="javascript:void(0);" class="btn btn-success btn-sm" onclick="exportData('excel')"
                        style="height: 30px; font-size: 12px;">
                        <i class="voyager-download"></i> Exporter
                    </a>
                    
                    <a href="{{ route('membres.non-a-jour') }}" class="btn btn-danger btn-sm" style="height: 30px; font-size: 12px;margin: 5px 10px;">
                        <i class="voyager-search"></i> Voir les membres non à jour
                    </a>
                </div>
            </div>

            <!-- Tableau des cotisations -->
            <div class="table-responsive">
                <table class="table table-hover" id="cotisations-table">
                    <thead>
                        <tr>
                            <th width="5%">ID</th>
                            <th width="20%">Membre</th>
                            <th width="10%">Année</th>
                            <th width="10%">Montant</th>
                            <th width="10%">Reste</th>
                            <th width="10%">Statut</th>
                            <th width="15%">Date paiement</th>
                            <th width="10%">Mode</th>
                            <th width="10%">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="cotisations-body">
                        <!-- Les données seront chargées ici -->
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div id="pagination-container" class="text-center" style="margin-top: 20px;"></div>

            <!-- Compteur de résultats -->
            <div id="results-count" class="text-muted" style="font-size: 12px; margin-top: 10px;"></div>
        </div>

    </div>
@stop

@section('javascript')
    <script>
        // Variables globales
        let montantTotal = 0;
        let selectedMember = null;
        let isEditMode = {{ $edit ? 'true' : 'false' }};

        // Initialiser au chargement
        $(document).ready(function() {
            // Initialiser Select2
            $('#membre_id').select2({
                placeholder: '-- Sélectionner dans la liste --',
                width: '100%',
                allowClear: true,
                {{ $edit ? 'disabled: true,' : '' }}
            }).on('change', function() {
                updateMemberInfo();
            });

            // Initialiser les valeurs pour le mode édition
            if (isEditMode) {
                const membreId = $('#membre_id').val();
                if (membreId) {
                    const option = $('#membre_id option:selected');
                    const plan = option.data('plan') || 'Non défini';
                    montantTotal = parseFloat(option.data('montant')) || 0;

                    $('#display-categorie').text(plan);
                    $('#display-montant-auto').text(formatCurrency(montantTotal) + ' CFA');
                    $('#montant_total_attendu').val(montantTotal);

                    // Mettre à jour le reste à payer
                    const montantPaye = parseFloat($('#montant').val()) || 0;
                    const reste = montantTotal - montantPaye;
                    updateResteDisplay(reste);
                }
            }

            // Initialiser la date d'échéance
            updateEcheanceDate();

            // Écouter le changement d'année pour mettre à jour la date d'échéance
            $('#annee_cotisation').on('change', function() {
                updateEcheanceDate();
            });

            // Écouter le changement du type de versement
            $('input[name="type_versement"]').on('change', function() {
                updateMontantInput();
            });

            // Écouter le changement de montant
            $('#montant').on('input', function() {
                calculerReste();
            });

            // Initialiser l'état du montant
            updateMontantInput();

            // Si en mode création, mettre le focus sur le champ membre
            if (!isEditMode) {
                $('#membre_id').select2('focus');
            }

            // Ajouter la validation au formulaire
            $('#cotisationForm').on('submit', function(e) {
                if (!validateForm()) {
                    e.preventDefault();
                    return false;
                }
            });
        });

        // Mettre à jour les informations du membre sélectionné
        function updateMemberInfo() {
            const membreSelect = document.getElementById('membre_id');
            const selectedOption = membreSelect.options[membreSelect.selectedIndex];

            if (selectedOption && selectedOption.value) {
                const plan = selectedOption.getAttribute('data-plan') || 'Non défini';
                montantTotal = parseFloat(selectedOption.getAttribute('data-montant')) || 0;
                selectedMember = selectedOption.value;

                // Mettre à jour l'affichage
                $('#display-categorie').text(plan);
                $('#display-montant-auto').text(formatCurrency(montantTotal) + ' CFA');
                $('#montant_total_attendu').val(montantTotal);

                // Mettre à jour le montant selon le type de versement
                updateMontantInput();

                // Focus sur le champ montant
                $('#montant').focus();
            } else {
                resetMemberInfo();
            }
        }

        // Réinitialiser les informations du membre
        function resetMemberInfo() {
            $('#display-categorie').text('---');
            $('#display-montant-auto').text('0 CFA');
            $('#montant_total_attendu').val(0);
            montantTotal = 0;
            selectedMember = null;

            // Réinitialiser le montant
            $('#montant').val('');
            updateResteDisplay(0);
        }

        // Mettre à jour l'état du champ montant selon le type de versement
        function updateMontantInput() {
            const typeVersement = $('input[name="type_versement"]:checked').val();

            if (typeVersement === 'TOTAL' && montantTotal > 0) {
                $('#montant').val(montantTotal).prop('readonly', true);
                $('#status').val('TOTAL');
            } else {
                $('#montant').prop('readonly', false);
                if (!isEditMode) {
                    $('#montant').val('');
                    $('#montant').focus();
                }
                $('#status').val('PARTIEL');
            }

            calculerReste();
        }

        // Calculer le reste à payer
        function calculerReste() {
            const montantPaye = parseFloat($('#montant').val()) || 0;
            const reste = montantTotal - montantPaye;

            // Validation : ne pas dépasser le montant total
            if (montantPaye > montantTotal && montantTotal > 0) {
                toastr.warning('Le montant payé ne peut pas dépasser ' + formatCurrency(montantTotal) + ' CFA',
                    'Avertissement');
                $('#montant').val(montantTotal);
                updateResteDisplay(0);
                return;
            }

            updateResteDisplay(reste);
        }

        // Mettre à jour l'affichage du reste
        function updateResteDisplay(reste) {
            const displayRestant = $('#display-restant');
            const resteInput = $('#reste_a_payer');

            if (reste > 0) {
                displayRestant.text(formatCurrency(reste) + ' CFA');
                displayRestant.css('color', '#d9534f'); // Rouge pour dette
                resteInput.val(reste);
                $('#status').val('PARTIEL');
            } else if (reste === 0 && montantTotal > 0) {
                displayRestant.text('0 CFA');
                displayRestant.css('color', '#5cb85c'); // Vert pour payé
                resteInput.val(0);
                $('#status').val('TOTAL');
            } else if (reste < 0) {
                displayRestant.text(formatCurrency(Math.abs(reste)) + ' CFA (excédent)');
                displayRestant.css('color', '#f0ad4e'); // Orange pour excédent
                resteInput.val(0);
                $('#status').val('TOTAL');
            } else {
                displayRestant.text('0 CFA');
                displayRestant.css('color', '#333');
                resteInput.val(0);
            }
        }

        // Mettre à jour la date d'échéance (31 mars de l'année suivante)
        function updateEcheanceDate() {
            const annee = parseInt($('#annee_cotisation').val()) || new Date().getFullYear();
            const dateEcheance = new Date(annee + 1, 2, 31); // 2 = mars (0-indexé)

            $('#date_echeance').val(dateEcheance.toISOString().split('T')[0]);
        }

        // Formater la devise
        function formatCurrency(amount) {
            return new Intl.NumberFormat('fr-FR', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(amount);
        }

        // Valider les données avant enregistrement
        function validateForm() {
            const errors = [];

            // Validation du membre
            if (!$('#membre_id').val()) {
                errors.push('Veuillez sélectionner un membre');
            }

            // Validation du montant
            const montantPaye = parseFloat($('#montant').val()) || 0;
            if (montantPaye <= 0) {
                errors.push('Le montant payé doit être supérieur à 0');
            }

            // Validation du mode de paiement
            const modePaiement = $('#mode_paiement').val();
            if (!modePaiement) {
                errors.push('Veuillez sélectionner un mode de paiement');
            }

            // Validation de la date
            if (!$('#date_paiement').val()) {
                errors.push('Veuillez saisir une date de paiement');
            } else {
                const datePaiement = new Date($('#date_paiement').val());
                const aujourdhui = new Date();

                // Autoriser les dates futures ? (À adapter selon vos besoins)
                // if (datePaiement > aujourdhui) {
                //     errors.push('La date de paiement ne peut pas être dans le futur');
                // }
            }

            // Afficher les erreurs
            if (errors.length > 0) {
                errors.forEach(error => {
                    toastr.error(error, 'Erreur de validation', {
                        timeOut: 5000,
                        closeButton: true
                    });
                });
                return false;
            }

            return true;
        }

        // Enregistrer avec AJAX
        function saveRecord() {
            // Validation
            if (!validateForm()) {
                return;
            }

            // S'assurer que les calculs sont à jour
            calculerReste();

            // Afficher l'indicateur de chargement
            const btn = $('button[onclick="saveRecord()"]');
            const originalText = btn.html();
            btn.html('<i class="voyager-refresh voyager-animate-spin"></i> Enregistrement...');
            btn.prop('disabled', true);

            // Préparer les données du formulaire
            const formData = new FormData($('#cotisationForm')[0]);

            // Ajouter l'ID en mode édition
            if (isEditMode) {
                formData.append('id', '{{ $edit ? $dataTypeContent->id : '' }}');
            }

            // Déterminer l'URL en fonction du mode
            const url = isEditMode ?
                "{{ route('cotisation.update') }}" :
                "{{ route('cotisation.store') }}";

            // Envoyer les données
            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message, 'Succès', {
                            timeOut: 3000,
                            onHidden: function() {
                                if (response.redirect) {
                                    window.location.href = response.redirect;
                                }
                            }
                        });
                    } else {
                        toastr.error(response.message, 'Erreur', {
                            timeOut: 5000
                        });
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Une erreur est survenue lors de l\'enregistrement';

                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        // Erreurs de validation Laravel
                        const errors = xhr.responseJSON.errors;
                        errorMessage = Object.values(errors)[0][0];
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.status === 409) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.status === 419) {
                        errorMessage = 'Session expirée. Veuillez rafraîchir la page.';
                    } else if (xhr.status === 404) {
                        errorMessage = 'Cotisation non trouvée.';
                    }

                    toastr.error(errorMessage, 'Erreur', {
                        timeOut: 5000,
                        closeButton: true
                    });
                },
                complete: function() {
                    // Restaurer le bouton
                    btn.html(originalText);
                    btn.prop('disabled', false);
                }
            });
        }

        // Réinitialiser le formulaire (uniquement en mode création)
        function resetForm() {
            if (isEditMode) {
                toastr.warning('Le formulaire ne peut pas être réinitialisé en mode édition', 'Information');
                return;
            }

            // Réinitialiser Select2
            $('#membre_id').val('').trigger('change');

            // Réinitialiser les champs
            resetMemberInfo();

            $('input[name="type_versement"][value="TOTAL"]').prop('checked', true);
            $('#reference').val('');
            $('#montant').val('');
            $('#reste_a_payer').val('0');
            $('#mode_paiement').val('');
            $('#date_paiement').val('{{ date('Y-m-d') }}');
            $('#observation').val('');
            $('#annee_cotisation').val('{{ date('Y') }}');

            // Mettre à jour les contrôles
            updateMontantInput();
            updateEcheanceDate();

            // Remettre le focus sur le champ membre
            $('#membre_id').select2('focus');

            toastr.info('Formulaire réinitialisé', 'Information', {
                timeOut: 2000
            });
        }

        // Fonction pour soumettre le formulaire traditionnellement (fallback)
        function submitFormTraditional() {
            if (validateForm()) {
                $('#cotisationForm').submit();
            }
        }
    </script>

    <script>
        $(document).ready(function() {
            // Variables globales
            let currentPage = 1;
            let searchTerm = '';
            let filterYear = '{{ $annee ?? date('Y') }}';

            // Charger les cotisations au démarrage
            loadCotisations();

            // Recherche en temps réel (délai de 500ms)
            let searchTimeout;
            $('#search-cotisations').on('keyup', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    searchTerm = $(this).val().trim();
                    currentPage = 1; // Retour à la première page
                    loadCotisations();
                }, 500);
            });

            // Filtre par année
            $('#filter-year').on('change', function() {
                filterYear = $(this).val();
                currentPage = 1;
                loadCotisations();
            });

            // Fonction pour charger les cotisations via AJAX
            function loadCotisations() {
                const tbody = $('#cotisations-body');
                const pagination = $('#pagination-container');
                const resultsCount = $('#results-count');

                // Afficher le spinner de chargement
                tbody.html(`
                <tr>
                    <td colspan="9" class="loading-spinner">
                        <i class="voyager-refresh voyager-animate-spin"></i> 
                        Chargement des cotisations...
                    </td>
                </tr>
            `);

                // Paramètres de la requête
                const params = {
                    page: currentPage,
                    search: searchTerm,
                    year: filterYear,
                    per_page: 10 // Nombre d'éléments par page
                };

                // Requête AJAX
                $.ajax({
                    url: '{{ route('cotisations.load') }}',
                    method: 'GET',
                    data: params,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            // Vider le tableau
                            tbody.empty();

                            if (response.data.length > 0) {
                                // Remplir le tableau avec les données
                                response.data.forEach(function(cotisation) {
                                    const row = createCotisationRow(cotisation);
                                    tbody.append(row);
                                });

                                // Afficher la pagination
                                renderPagination(response);

                                // Afficher le nombre de résultats
                                resultsCount.html(`
                                Affichage de ${response.from} à ${response.to} 
                                sur ${response.total} cotisation(s)
                            `);

                                // Surligner les termes de recherche
                                if (searchTerm) {
                                    highlightSearchTerms(searchTerm);
                                }
                            } else {
                                // Aucun résultat
                                tbody.html(`
                                <tr>
                                    <td colspan="9" class="no-results">
                                        Aucune cotisation trouvée
                                    </td>
                                </tr>
                            `);
                                pagination.empty();
                                resultsCount.html('Aucun résultat');
                            }
                        } else {
                            toastr.error('Erreur lors du chargement des cotisations', 'Erreur');
                        }
                    },
                    error: function(xhr) {
                        tbody.html(`
                        <tr>
                            <td colspan="9" class="text-danger">
                                Erreur de chargement : ${xhr.responseJSON?.message || 'Erreur serveur'}
                            </td>
                        </tr>
                    `);
                        console.error('Erreur AJAX:', xhr);
                    }
                });
            }

            // Créer une ligne du tableau
            function createCotisationRow(cotisation) {
                const datePaiement = cotisation.date_paiement ?
                    new Date(cotisation.date_paiement).toLocaleDateString('fr-FR') :
                    'N/A';

                const montantFormatted = formatCurrency(cotisation.montant);
                const resteFormatted = formatCurrency(cotisation.reste_a_payer);

                // URL d'édition (si disponible)
                const editUrl = '{{ url('admin/dms-cotisation-membres') }}/' + cotisation.id + '/edit';

                return `
                <tr data-id="${cotisation.id}">
                    <td>${cotisation.id}</td>
                    <td class="membre-cell" data-membre="${cotisation.membre_libelle.toLowerCase()}">
                        ${cotisation.membre_libelle}
                    </td>
                    <td>${cotisation.annee_cotisation}</td>
                    <td><strong>${montantFormatted} CFA</strong></td>
                    <td>${resteFormatted} CFA</td>
                    <td>
                        <span class="status-badge status-${cotisation.status}">
                            ${cotisation.status}
                        </span>
                    </td>
                    <td>${datePaiement}</td>
                    <td>${cotisation.mode_paiement}</td>
                    <td class="action-buttons">
                        <a href="${editUrl}" class="btn btn-sm btn-primary" title="Modifier">
                            <i class="voyager-edit"></i>
                        </a>
                        <button class="btn btn-sm btn-info" onclick="viewDetails(${cotisation.id})" title="Détails">
                            <i class="voyager-eye"></i>
                        </button>
                    </td>
                </tr>
            `;
            }

            // Rendre la pagination
            function renderPagination(response) {
                const pagination = $('#pagination-container');
                pagination.empty();

                if (response.last_page > 1) {
                    let paginationHtml = '<nav><ul class="pagination pagination-sm justify-content-center">';

                    // Bouton précédent
                    if (response.current_page > 1) {
                        paginationHtml += `
                        <li class="page-item">
                            <a class="page-link" href="#" onclick="changePage(${response.current_page - 1})">
                                &laquo;
                            </a>
                        </li>
                    `;
                    }

                    // Numéros de page
                    for (let i = 1; i <= response.last_page; i++) {
                        if (i === 1 || i === response.last_page ||
                            (i >= response.current_page - 2 && i <= response.current_page + 2)) {

                            const active = i === response.current_page ? 'active' : '';
                            paginationHtml += `
                            <li class="page-item ${active}">
                                <a class="page-link" href="#" onclick="changePage(${i})">
                                    ${i}
                                </a>
                            </li>
                        `;
                        } else if (i === response.current_page - 3 || i === response.current_page + 3) {
                            paginationHtml +=
                                '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                    }

                    // Bouton suivant
                    if (response.current_page < response.last_page) {
                        paginationHtml += `
                        <li class="page-item">
                            <a class="page-link" href="#" onclick="changePage(${response.current_page + 1})">
                                &raquo;
                            </a>
                        </li>
                    `;
                    }

                    paginationHtml += '</ul></nav>';
                    pagination.html(paginationHtml);
                }
            }

            // Changer de page
            window.changePage = function(page) {
                currentPage = page;
                loadCotisations();

                // Scroll vers le tableau
                $('html, body').animate({
                    scrollTop: $('#cotisations-table').offset().top - 100
                }, 500);
            }

            // Surligner les termes de recherche
            function highlightSearchTerms(term) {
                term = term.toLowerCase();

                $('#cotisations-body tr').each(function() {
                    const row = $(this);
                    let found = false;

                    // Rechercher dans toutes les colonnes (sauf actions)
                    row.find('td').not(':last-child').each(function() {
                        const text = $(this).text().toLowerCase();
                        if (text.includes(term)) {
                            found = true;
                            // Surligner le texte correspondant
                            $(this).addClass('highlight');
                        } else {
                            $(this).removeClass('highlight');
                        }
                    });

                    // Si aucun terme trouvé, cacher la ligne
                    if (term && !found) {
                        row.hide();
                    } else {
                        row.show();
                    }
                });
            }

            // Formater la devise
            function formatCurrency(amount) {
                return new Intl.NumberFormat('fr-FR').format(amount);
            }

            // Voir les détails d'une cotisation (modal)
            window.viewDetails = function(id) {
                $.ajax({
                    url: '{{ url('admin/dms/cotisations') }}/' + id + '/details',
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            // Afficher une modal avec les détails
                            showDetailsModal(response.data);
                        }
                    },
                    error: function() {
                        toastr.error('Erreur lors du chargement des détails', 'Erreur');
                    }
                });
            }


            // Afficher la modal de détails
            function showDetailsModal(data) {
                // Créer ou récupérer la modal
                let modal = $('#cotisation-details-modal');
                if (modal.length === 0) {
                    modal = $(`
            <div class="modal fade" id="cotisation-details-modal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header" style="background: linear-gradient(135deg, #4a6785 0%, #2c3e50 100%); color: white; border-bottom: none;">
                            <h5 class="modal-title" style="font-weight: 600;">
                                <i class="voyager-documentation" style="margin-right: 10px;"></i>
                                Détails de la cotisation
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" style="color: white; opacity: 0.8;">
                                <span style="font-size: 28px;">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body" id="modal-details-content" style="padding: 25px;">
                            <!-- Contenu chargé dynamiquement -->
                        </div>
                        <div class="modal-footer" style="border-top: 1px solid #e9ecef; padding: 15px 25px;">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 4px;">
                                <i class="voyager-x" style="margin-right: 5px;"></i>
                                Fermer
                            </button>
                            <a href="{{ url('admin/dms-cotisation-membres') }}/${data.id}/edit" 
                               class="btn btn-primary" style="border-radius: 4px;">
                                <i class="voyager-edit" style="margin-right: 5px;"></i>
                                Modifier
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        `).appendTo('body');
                }

                // Formatage des dates
                const formatDate = (dateStr) => {
                    if (!dateStr) return 'Non spécifié';
                    const date = new Date(dateStr);
                    return date.toLocaleDateString('fr-FR', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                };

                // Calcul du pourcentage payé
                const pourcentagePaye = data.montant_total_attendu > 0 ?
                    Math.round((data.montant / data.montant_total_attendu) * 100) :
                    0;

                // Barre de progression
                const progressBar = `
        <div class="progress mt-2" style="height: 12px; border-radius: 6px; background-color: #e9ecef;">
            <div class="progress-bar ${pourcentagePaye >= 100 ? 'bg-success' : 'bg-warning'}" 
                 role="progressbar" 
                 style="width: ${Math.min(pourcentagePaye, 100)}%; border-radius: 6px;"
                 aria-valuenow="${pourcentagePaye}" 
                 aria-valuemin="0" 
                 aria-valuemax="100">
                <span style="font-size: 10px; font-weight: bold;">${pourcentagePaye}%</span>
            </div>
        </div>
    `;

                // Badge de statut
                const statusBadge = data.status === 'TOTAL' ?
                    `<span class="badge badge-success" style="padding: 5px 12px; font-size: 12px; border-radius: 12px;">
                <i class="voyager-check" style="margin-right: 3px;"></i>${data.status}
           </span>` :
                    `<span class="badge badge-warning" style="padding: 5px 12px; font-size: 12px; border-radius: 12px;">
                <i class="voyager-dollar" style="margin-right: 3px;"></i>${data.status}
           </span>`;

                // Card CSS inline
                const cardStyle = `
        background: white;
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        padding: 20px;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    `;

                const cardHeaderStyle = `
        color: #2c3e50;
        font-weight: 600;
        font-size: 16px;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f0f0f0;
        display: flex;
        align-items: center;
    `;

                const detailRowStyle = `
        margin-bottom: 10px;
        padding-bottom: 10px;
        border-bottom: 1px dashed #f0f0f0;
    `;

                const labelStyle = `
        color: #666;
        font-size: 13px;
        font-weight: 500;
        display: block;
        margin-bottom: 3px;
    `;

                const valueStyle = `
        color: #333;
        font-size: 14px;
        font-weight: 600;
    `;

                // Remplir le contenu
                const content = `
        <!-- En-tête résumé -->
        <div style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); 
                    padding: 20px; 
                    border-radius: 8px; 
                    margin-bottom: 25px;
                    border-left: 4px solid #4a6785;">
            <div class="row">
                <div class="col-md-8">
                    <h6 style="color: #4a6785; margin-bottom: 5px;">
                        <i class="voyager-person" style="margin-right: 8px;"></i>
                        ${data.membre_libelle}
                    </h6>
                    <p style="color: #666; font-size: 13px; margin-bottom: 5px;">
                        <i class="voyager-tag" style="margin-right: 5px;"></i>
                        ${data.plan_adhesion || 'Plan non spécifié'}
                    </p>
                </div>
                <div class="col-md-4 text-right">
                    ${statusBadge}
                    <div style="margin-top: 10px; font-size: 20px; font-weight: bold; color: #2c3e50;">
                        ${formatCurrency(data.montant)} CFA
                    </div>
                </div>
            </div>
        </div>

        <!-- Progression du paiement -->
        <div style="${cardStyle}">
            <div style="${cardHeaderStyle}">
                <i class="voyager-trending-up" style="margin-right: 10px; color: #4a6785;"></i>
                Progression du paiement
            </div>
            <div style="${detailRowStyle}">
                <div style="${labelStyle}">Montant total attendu</div>
                <div style="${valueStyle}">${formatCurrency(data.montant_total_attendu)} CFA</div>
            </div>
            <div style="${detailRowStyle}">
                <div style="${labelStyle}">Montant déjà payé</div>
                <div style="color: #28a745; font-size: 16px; font-weight: bold;">
                    ${formatCurrency(data.montant)} CFA
                </div>
            </div>
            <div style="${detailRowStyle}">
                <div style="${labelStyle}">Reste à payer</div>
                <div style="${valueStyle}; color: ${data.reste_a_payer > 0 ? '#dc3545' : '#28a745'}">
                    ${formatCurrency(data.reste_a_payer)} CFA
                </div>
            </div>
            ${progressBar}
            <div class="text-center mt-2" style="color: #666; font-size: 12px;">
                ${pourcentagePaye}% du montant total payé
            </div>
        </div>

        <!-- Détails de la transaction -->
        <div class="row">
            <div class="col-md-6">
                <div style="${cardStyle}">
                    <div style="${cardHeaderStyle}">
                        <i class="voyager-calendar" style="margin-right: 10px; color: #4a6785;"></i>
                        Informations temporelles
                    </div>
                    <div style="${detailRowStyle}">
                        <div style="${labelStyle}">Année de cotisation</div>
                        <div style="${valueStyle}">
                            <span class="badge badge-info" style="background-color: #e3f2fd; color: #1976d2;">
                                ${data.annee_cotisation}
                            </span>
                        </div>
                    </div>
                    <div style="${detailRowStyle}">
                        <div style="${labelStyle}">Date de paiement</div>
                        <div style="${valueStyle}">
                            <i class="voyager-watch" style="margin-right: 5px; color: #666;"></i>
                            ${new Date(data.date_paiement).toLocaleDateString('fr-FR', { 
                                weekday: 'short', 
                                year: 'numeric', 
                                month: 'long', 
                                day: 'numeric' 
                            })}
                        </div>
                    </div>
                    <div style="${detailRowStyle}">
                        <div style="${labelStyle}">Date d'échéance</div>
                        <div style="${valueStyle}">
                            <i class="voyager-alarm-clock" style="margin-right: 5px; color: #666;"></i>
                            ${data.date_echeance ? new Date(data.date_echeance).toLocaleDateString('fr-FR') : 'Non définie'}
                        </div>
                    </div>
                    <div>
                        <div style="${labelStyle}">Enregistrée le</div>
                        <div style="${valueStyle}; font-size: 13px; color: #666;">
                            ${new Date(data.created_at).toLocaleDateString('fr-FR')} à 
                            ${new Date(data.created_at).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div style="${cardStyle}">
                    <div style="${cardHeaderStyle}">
                        <i class="voyager-credit-card" style="margin-right: 10px; color: #4a6785;"></i>
                        Méthode de paiement
                    </div>
                    <div style="${detailRowStyle}">
                        <div style="${labelStyle}">Mode de paiement</div>
                        <div style="${valueStyle}">
                            <span class="badge" style="background-color: ${getPaymentMethodColor(data.mode_paiement)}; 
                                                         color: white; 
                                                         padding: 4px 10px;
                                                         border-radius: 4px;">
                                ${data.mode_paiement}
                            </span>
                        </div>
                    </div>
                    <div style="${detailRowStyle}">
                        <div style="${labelStyle}">Référence / N° reçu</div>
                        <div style="${valueStyle}">
                            ${data.reference || '<span style="color: #999; font-style: italic;">Non spécifiée</span>'}
                        </div>
                    </div>
                    <div>
                        <div style="${labelStyle}">ID de la transaction</div>
                        <div style="${valueStyle}; font-family: monospace; color: #4a6785; font-size: 14px;">
                            #${data.id}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Observations -->
        ${data.observation ? `
                                    <div style="${cardStyle}">
                                        <div style="${cardHeaderStyle}">
                                            <i class="voyager-bubble" style="margin-right: 10px; color: #4a6785;"></i>
                                            Observations
                                        </div>
                                        <div style="background-color: #f8f9fa; 
                                                    padding: 15px; 
                                                    border-radius: 6px; 
                                                    border-left: 3px solid #4a6785;
                                                    font-style: italic;
                                                    color: #555;
                                                    font-size: 14px;">
                                            <i class="voyager-info-circled" style="margin-right: 8px; color: #4a6785;"></i>
                                            ${data.observation}
                                        </div>
                                    </div>
                                ` : ''} `;

                $('#modal-details-content').html(content);
                modal.modal('show');
            }

            // Fonction pour obtenir la couleur selon le mode de paiement
            function getPaymentMethodColor(method) {
                const colors = {
                    'Cash': '#28a745',
                    'En ligne': '#17a2b8',
                    'cheque': '#ffc107',
                    'virement': '#007bff'
                };
                return colors[method] || '#6c757d';
            }

            // Fonction pour formater la devise
            function formatCurrency(amount) {
                return new Intl.NumberFormat('fr-FR').format(amount);
            }

            // Fonctions pour les actions
            function printDetails(id) {
                toastr.info('Préparation de l\'impression...', 'Information', {
                    timeOut: 2000
                });
                setTimeout(() => {
                    window.open(`{{ url('admin/cotisations') }}/${id}/print`, '_blank');
                }, 500);
            }

            function sendReceipt(id) {
                toastr.info('Envoi du reçu en cours...', 'Information');

                $.ajax({
                    url: `{{ url('admin/cotisations') }}/${id}/send-receipt`,
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message, 'Succès', {
                                timeOut: 3000
                            });
                        } else {
                            toastr.warning(response.message, 'Information', {
                                timeOut: 4000
                            });
                        }
                    },
                    error: function() {
                        toastr.error('Erreur lors de l\'envoi du reçu', 'Erreur');
                    }
                });
            }

            // Rafraîchir automatiquement toutes les 30 secondes
            setInterval(function() {
                if (searchTerm === '' && currentPage === 1) {
                    loadCotisations();
                }
            }, 30000);
        });


        // Fonction d'export améliorée
        window.exportData = function(format) {
            // Afficher un indicateur de chargement
            const exportBtn = $('.export-dropdown');
            const originalHtml = exportBtn.html();

            exportBtn.html(`
        <span class="voyager-refresh voyager-animate-spin" style="margin-right: 5px;"></span>
        Préparation de l'export...
    `);
            exportBtn.prop('disabled', true);

            // Récupérer les paramètres
            const params = {
                format: format,
                search: $('#search-cotisations').val(),
                year: $('#filter-year').val(),
                _token: $('meta[name="csrf-token"]').attr('content')
            };

            // Créer un formulaire
            const form = $('<form>', {
                method: 'POST',
                action: '{{ route('cotisations.export') }}',
                target: '_blank',
                style: 'display: none;'
            });

            // Ajouter les champs
            $.each(params, function(key, value) {
                form.append($('<input>', {
                    type: 'hidden',
                    name: key,
                    value: value
                }));
            });

            // Ajouter le formulaire et le soumettre
            $('body').append(form);
            form.submit();

            // Supprimer le formulaire après soumission
            setTimeout(() => {
                form.remove();

                // Restaurer le bouton
                exportBtn.html(originalHtml);
                exportBtn.prop('disabled', false);

                // Afficher un message de succès
                const formatNames = {
                    'excel': 'Excel',
                    'csv': 'CSV',
                    'pdf': 'PDF'
                };

                toastr.success(
                    `Export ${formatNames[format]} en cours de téléchargement`,
                    'Succès', {
                        timeOut: 3000
                    }
                );

            }, 1000);
        }

        // Export avec progression (pour les gros fichiers)
        window.exportWithProgress = function(format) {
            // Afficher une modal de progression
            if (!$('#export-progress-modal').length) {
                $('body').append(`
            <div class="modal fade" id="export-progress-modal" tabindex="-1">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Export en cours</h5>
                        </div>
                        <div class="modal-body text-center">
                            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                                <span class="sr-only">Chargement...</span>
                            </div>
                            <p class="mt-3" id="export-progress-text">Préparation des données...</p>
                            <div class="progress mt-2">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                     id="export-progress-bar" 
                                     role="progressbar" 
                                     style="width: 0%">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `);
            }

            $('#export-progress-modal').modal('show');

            // Simuler une progression (dans la réalité, vous auriez besoin d'un WebSocket)
            let progress = 0;
            const progressInterval = setInterval(() => {
                progress += 10;
                $('#export-progress-bar').css('width', progress + '%');

                if (progress >= 100) {
                    clearInterval(progressInterval);
                    $('#export-progress-text').text('Export terminé !');

                    // Lancer l'export réel
                    setTimeout(() => {
                        exportData(format);
                        $('#export-progress-modal').modal('hide');
                    }, 500);
                } else if (progress < 30) {
                    $('#export-progress-text').text('Récupération des données...');
                } else if (progress < 70) {
                    $('#export-progress-text').text('Formatage du fichier...');
                } else {
                    $('#export-progress-text').text('Finalisation...');
                }
            }, 200);
        }
    </script>

    <script>
        function exportNonAJour() {
            const form = $('#formNonAJour');
            const format = $('#format').val() || 'excel';

            // Changer la méthode pour POST si besoin
            form.attr('action', '{{ route('membres.non-a-jour.export') }}');
            form.attr('method', 'POST');

            // Ajouter le token CSRF
            if (!$('input[name="_token"]', form).length) {
                form.append('<input type="hidden" name="_token" value="{{ csrf_token() }}">');
            }

            form.submit();

            // Remettre en GET pour l'affichage
            setTimeout(() => {
                form.attr('action', '{{ route('membres.non-a-jour') }}');
                form.attr('method', 'GET');
                $('input[name="_token"]', form).remove();
            }, 100);
        }
    </script>
@endsection
