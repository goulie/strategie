@extends('voyager::master')

@section('page_title', 'Modifier une cotisation')

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
            justify-content: space-between;
        }

        .access-header h2 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
            color: #333;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 10px;
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
        }

        .access-footer {
            background-color: #e8e8e8;
            padding: 10px 15px;
            border-top: 1px solid #ccc;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-access {
            background: linear-gradient(to bottom, #ffffff, #e0e0e0);
            border: 1px solid #888;
            color: #333;
            font-weight: bold;
            padding: 7px 20px;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-access:hover {
            background: #d0d0d0;
            text-decoration: none;
            color: #333;
        }

        .btn-save {
            background: linear-gradient(to bottom, #5cb85c, #449d44);
            color: white;
            border-color: #398439;
        }

        .btn-save:hover {
            background: #449d44;
            color: white;
        }

        .btn-cancel {
            background: linear-gradient(to bottom, #f8f9fa, #e2e6ea);
            border-color: #dae0e5;
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

        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #4a6785;
            padding: 10px 15px;
            margin-bottom: 20px;
            font-size: 12px;
        }

        .info-box strong {
            color: #4a6785;
        }

        .montant-partiel {
            background-color: #fff9c4 !important;
            border-color: #fbc02d !important;
        }

        .montant-total {
            background-color: #d4edda !important;
            border-color: #c3e6cb !important;
        }

        .montant-excedent {
            background-color: #ffeaa7 !important;
            border-color: #fdcb6e !important;
            color: #d63031 !important;
        }

        .montant-retard {
            background-color: #f8d7da !important;
            border-color: #f5c6cb !important;
            color: #721c24 !important;
        }

        .back-link {
            color: #337ab7;
            text-decoration: none;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
        }

        .back-link:hover {
            color: #23527c;
            text-decoration: underline;
        }

        .readonly-field {
            background-color: #f5f5f5 !important;
            border-color: #ddd !important;
            color: #666 !important;
            cursor: not-allowed !important;
        }
    </style>
@stop

@section('content')
    <div class="page-content container-fluid">
        <div class="access-container">
            <!-- Header -->
            <div class="access-header">
                <h2>
                    <span class="voyager-edit"></span>
                    Modifier une Cotisation
                </h2>
                <a href="{{ route('voyager.dms-cotisation-membres.index') }}" class="back-link">
                    <span class="voyager-angle-left"></span> Retour à la liste
                </a>
            </div>

            <div class="access-body">
                <!-- Informations sur la cotisation existante -->
                <div class="info-box">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Cotisation N° {{ $cotisation->id }}</strong><br>
                            <small>Membre : {{ $cotisation->membre->libelle_membre ?? 'N/A' }}</small><br>
                            <small>Créée le : {{ \Carbon\Carbon::parse($cotisation->created_at)->format('d/m/Y H:i') }}</small>
                        </div>
                        <div class="col-md-6">
                            <small>Statut actuel : 
                                <span class="badge 
                                    @if($cotisation->status === 'TOTAL') badge-success
                                    @elseif($cotisation->status === 'PARTIEL') badge-warning
                                    @elseif($cotisation->status === 'EN_RETARD') badge-danger
                                    @elseif($cotisation->status === 'ANNULÉ') badge-secondary
                                    @endif">
                                    {{ $cotisation->status }}
                                </span>
                            </small><br>
                            <small>Dernière modification : {{ \Carbon\Carbon::parse($cotisation->updated_at)->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                </div>

                <form id="cotisationEditForm" method="POST" action="{{ route('voyager.dms-cotisation-membres.update', $cotisation->id) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <!-- Colonne de Gauche : Identification -->
                        <div class="col-md-6">
                            <div class="section-title">1. Identification du Membre</div>
                            
                            <div class="form-group">
                                <label for="membre_id">Membre (non modifiable)</label>
                                <div class="read-only-box">
                                    {{ $cotisation->membre->libelle_membre ?? 'N/A' }}
                                </div>
                                <input type="hidden" id="membre_id" name="membre_id" value="{{ $cotisation->membre_id }}">
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Catégorie / Plan d'adhésion</label>
                                        <div id="display-categorie" class="read-only-box">
                                            {{ $cotisation->membre->plan_adhesion->title_plan ?? 'Non défini' }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Montant Annuel Attendu</label>
                                <div id="display-montant-auto" class="read-only-box montant-total">
                                    {{ number_format($cotisation->montant_total_attendu ?? $cotisation->membre->plan_adhesion->price_xof ?? 0, 0, ',', ' ') }} CFA
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Montant déjà payé</label>
                                        <div class="read-only-box montant-total">
                                            {{ number_format($cotisation->montant, 0, ',', ' ') }} CFA
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Reste actuel à payer</label>
                                        <div id="display-restant-actuel" class="read-only-box 
                                            @if($cotisation->reste_a_payer > 0) montant-partiel
                                            @else montant-total @endif">
                                            {{ number_format($cotisation->reste_a_payer, 0, ',', ' ') }} CFA
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Colonne de Droite : Paiement -->
                        <div class="col-md-6 col-divider">
                            <div class="section-title">2. Modification du Paiement</div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Type de versement *</label><br>
                                        <label class="radio-inline">
                                            <input type="radio" name="type_versement" value="TOTAL" 
                                                {{ strtoupper($cotisation->type_versement) == 'TOTAL' ? 'checked' : '' }}> Total
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="type_versement" value="PARTIEL"
                                                {{ strtoupper($cotisation->type_versement) == 'PARTIEL' ? 'checked' : '' }}> Partiel
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="reference">Référence / N° Reçu</label>
                                        <input type="text" id="reference" name="reference" class="form-control"
                                            value="{{ old('reference', $cotisation->reference) }}"
                                            placeholder="Ex: OM-458922">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="montant">Nouveau montant versé *</label>
                                        <input type="number" id="montant" name="montant" class="form-control"
                                            min="0" step="100" required 
                                            value="{{ old('montant', $cotisation->montant) }}"
                                            oninput="calculerReste()">
                                        <small class="text-muted">Ancien montant: {{ number_format($cotisation->montant, 0, ',', ' ') }} CFA</small>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Nouveau reste à payer</label>
                                        <div id="display-restant" class="read-only-box montant-partiel">
                                            {{ number_format($cotisation->reste_a_payer, 0, ',', ' ') }} CFA
                                        </div>
                                        <input type="hidden" id="reste_a_payer" name="reste_a_payer" 
                                            value="{{ old('reste_a_payer', $cotisation->reste_a_payer) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="mode_paiement">Mode de paiement *</label>
                                        <select id="mode_paiement" name="mode_paiement" class="form-control" required>
                                            <option value="" disabled>-- Choisir --</option>
                                            <option value="Cash" {{ $cotisation->mode_paiement == 'Cash' ? 'selected' : '' }}>Cash</option>
                                            <option value="En ligne" {{ $cotisation->mode_paiement == 'En ligne' ? 'selected' : '' }}>En ligne</option>
                                            <option value="cheque" {{ $cotisation->mode_paiement == 'cheque' ? 'selected' : '' }}>Chèque</option>
                                            <option value="virement" {{ $cotisation->mode_paiement == 'virement' ? 'selected' : '' }}>Virement</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="annee_cotisation">Année *</label>
                                        <select id="annee_cotisation" name="annee_cotisation" class="form-control" required>
                                            @php
                                                $currentYear = date('Y');
                                                $selectedYear = old('annee_cotisation', $cotisation->annee_cotisation);
                                            @endphp
                                            @for ($i = $currentYear - 4; $i <= $currentYear + 1; $i++)
                                                <option value="{{ $i }}" 
                                                    {{ $selectedYear == $i ? 'selected' : '' }}>
                                                    {{ $i }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="date_paiement">Date de paiement *</label>
                                        <input type="date" id="date_paiement" name="date_paiement" class="form-control"
                                            value="{{ old('date_paiement', \Carbon\Carbon::parse($cotisation->date_paiement)->format('Y-m-d')) }}"
                                            required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="date_echeance">Date d'échéance</label>
                                        <input type="date" id="date_echeance" name="date_echeance" class="form-control readonly-field"
                                            value="{{ \Carbon\Carbon::parse($cotisation->date_echeance)->format('Y-m-d') }}"
                                            readonly>
                                        <small class="text-muted">Calculée automatiquement (31 mars année+1)</small>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="status">Statut</label>
                                        <select id="status" name="status" class="form-control">
                                            <option value="TOTAL" {{ strtoupper($cotisation->status) == 'TOTAL' ? 'selected' : '' }}>TOTAL</option>
                                            <option value="PARTIEL" {{ strtoupper($cotisation->status) == 'PARTIEL' ? 'selected' : '' }}>PARTIEL</option>
                                            <option value="ANNULÉ" {{ strtoupper($cotisation->status) == 'ANNULÉ' ? 'selected' : '' }}>ANNULÉ</option>
                                            <option value="EN_RETARD" {{ strtoupper($cotisation->status) == 'EN_RETARD' ? 'selected' : '' }}>EN RETARD</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="observation">Observations</label>
                                <textarea class="form-control" name="observation" id="observation" rows="2"
                                    placeholder="Notes supplémentaires sur la modification...">{{ old('observation', $cotisation->observation) }}</textarea>
                            </div>

                            <!-- Champs cachés pour les calculs automatiques -->
                            <input type="hidden" id="montant_total_attendu" name="montant_total_attendu"
                                value="{{ $cotisation->montant_total_attendu ?? $cotisation->membre->plan_adhesion->price_xof ?? 0 }}">
                            <input type="hidden" id="user_id" name="user_id" value="{{ auth()->id() }}">
                        </div>
                    </div>
                </form>
            </div>

            <div class="access-footer">
                <div>
                    <button type="button" class="btn btn-access btn-cancel" onclick="window.history.back()">
                        <span class="voyager-x"></span> Annuler
                    </button>
                </div>
                <div>
                    <button type="button" class="btn btn-access" onclick="reinitialiserModifications()">
                        <span class="voyager-refresh"></span> Réinitialiser
                    </button>
                    <button type="button" class="btn btn-access btn-save" onclick="updateRecord()">
                        <span class="voyager-check"></span> Mettre à jour
                    </button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('javascript')
    <script>
        // Variables globales
        let montantTotal = {{ $cotisation->montant_total_attendu ?? $cotisation->membre->plan_adhesion->price_xof ?? 0 }};
        let montantInitial = {{ $cotisation->montant }};
        let resteInitial = {{ $cotisation->reste_a_payer }};

        // Initialiser au chargement
        $(document).ready(function() {
            // Initialiser la date d'échéance
            updateEcheanceDate();

            // Écouter le changement d'année pour mettre à jour la date d'échéance
            $('#annee_cotisation').on('change', function() {
                updateEcheanceDate();
            });

            // Écouter le changement du type de versement
            $('input[name="type_versement"]').on('change', function() {
                calculerReste();
            });

            // Écouter le changement de montant
            $('#montant').on('input', function() {
                calculerReste();
            });

            // Initialiser les calculs
            calculerReste();

            // Afficher un message si des anciennes valeurs existent
            @if($errors->any())
                toastr.error('Veuillez corriger les erreurs dans le formulaire.', 'Erreur de validation');
            @endif
        });

        // Calculer le reste à payer
        function calculerReste() {
            const montantPaye = parseFloat($('#montant').val()) || 0;
            const reste = montantTotal - montantPaye;
            const displayRestant = $('#display-restant');
            const resteInput = $('#reste_a_payer');
            const statusSelect = $('#status');
            const typeVersement = $('input[name="type_versement"]:checked').val();

            // Mettre à jour l'affichage
            displayRestant.removeClass('montant-partiel montant-total montant-excedent montant-retard');
            
            if (reste > 0) {
                displayRestant.text(formatCurrency(reste) + ' CFA');
                displayRestant.addClass('montant-partiel');
                resteInput.val(reste);
                statusSelect.val('PARTIEL');
                $('input[name="type_versement"][value="PARTIEL"]').prop('checked', true);
            } else if (reste === 0 && montantPaye > 0) {
                displayRestant.text('0 CFA');
                displayRestant.addClass('montant-total');
                resteInput.val(0);
                statusSelect.val('TOTAL');
                $('input[name="type_versement"][value="TOTAL"]').prop('checked', true);
            } else if (reste < 0) {
                displayRestant.text(formatCurrency(Math.abs(reste)) + ' CFA (excédent)');
                displayRestant.addClass('montant-excedent');
                resteInput.val(0);
                statusSelect.val('TOTAL');
                $('input[name="type_versement"][value="TOTAL"]').prop('checked', true);
            } else {
                displayRestant.text('0 CFA');
                resteInput.val(0);
            }

            // Vérifier si la date d'échéance est dépassée
            const dateEcheance = new Date($('#date_echeance').val());
            const aujourdhui = new Date();
            if (reste > 0 && dateEcheance < aujourdhui) {
                displayRestant.addClass('montant-retard');
                statusSelect.val('EN_RETARD');
            }
        }

        // Mettre à jour la date d'échéance (31 mars de l'année suivante)
        function updateEcheanceDate() {
            const annee = parseInt($('#annee_cotisation').val()) || new Date().getFullYear();
            const dateEcheance = new Date(annee + 1, 2, 31); // 2 = mars (0-indexé)
            
            $('#date_echeance').val(dateEcheance.toISOString().split('T')[0]);
            calculerReste(); // Recalculer pour vérifier si en retard
        }

        // Formater la devise
        function formatCurrency(amount) {
            return new Intl.NumberFormat('fr-FR', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(amount);
        }

        // Valider les données avant mise à jour
        function validateForm() {
            const errors = [];

            // Validation du montant
            const montantPaye = parseFloat($('#montant').val()) || 0;
            if (montantPaye <= 0) {
                errors.push('Le montant payé doit être supérieur à 0');
            }

            if (montantPaye > montantTotal * 1.5) { // Limite à 150% du montant total
                errors.push('Le montant payé semble trop élevé. Veuillez vérifier.');
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

                if (datePaiement > aujourdhui.setHours(23, 59, 59, 999)) {
                    errors.push('La date de paiement ne peut pas être dans le futur');
                }
            }

            // Validation de l'année
            const annee = $('#annee_cotisation').val();
            const currentYear = new Date().getFullYear();
            if (annee < (currentYear - 4) || annee > (currentYear + 1)) {
                errors.push('L\'année de cotisation doit être entre ' + (currentYear - 4) + ' et ' + (currentYear + 1));
            }

            // Vérifier si le membre a d'autres cotisations pour la même année
            const membreId = $('#membre_id').val();
            const anneeCotisation = $('#annee_cotisation').val();
            const cotisationId = {{ $cotisation->id }};

            // Cette vérification pourrait être faite côté serveur
            // Pour l'instant, on se contente d'un avertissement
            if (anneeCotisation != {{ $cotisation->annee_cotisation }}) {
                const confirmation = confirm('Attention : Vous changez l\'année de cotisation.\n\n' +
                    'Ce membre pourrait avoir d\'autres cotisations pour cette nouvelle année.\n' +
                    'Souhaitez-vous continuer ?');
                
                if (!confirmation) {
                    return ['Annulation par l\'utilisateur'];
                }
            }

            return errors;
        }

        // Mettre à jour l'enregistrement
        function updateRecord() {
            // S'assurer que les calculs sont à jour
            calculerReste();

            // Validation
            const errors = validateForm();
            if (errors.length > 0) {
                if (errors[0] !== 'Annulation par l\'utilisateur') {
                    errors.forEach(error => {
                        toastr.error(error, 'Erreur de validation', {
                            timeOut: 5000,
                            closeButton: true
                        });
                    });
                }
                return;
            }

            // Demander confirmation pour les modifications importantes
            const nouveauMontant = parseFloat($('#montant').val()) || 0;
            const nouvelAnnee = $('#annee_cotisation').val();
            const ancienAnnee = {{ $cotisation->annee_cotisation }};
            
            let message = 'Confirmez-vous la modification de cette cotisation ?\n\n';
            message += `Montant : ${formatCurrency(montantInitial)} → ${formatCurrency(nouveauMontant)} CFA\n`;
            
            if (nouvelAnnee != ancienAnnee) {
                message += `Année : ${ancienAnnee} → ${nouvelAnnee}\n`;
            }
            
            message += `\nCes modifications seront enregistrées dans l'historique.`;

            if (!confirm(message)) {
                return;
            }

            // Afficher l'indicateur de chargement
            const btn = $('button[onclick="updateRecord()"]');
            const originalText = btn.html();
            btn.html('<i class="voyager-refresh voyager-animate-spin"></i> Mise à jour...');
            btn.prop('disabled', true);

            // Soumettre le formulaire
            $('#cotisationEditForm').submit();
        }

        // Réinitialiser les modifications
        function reinitialiserModifications() {
            if (confirm('Voulez-vous réinitialiser toutes les modifications effectuées ?')) {
                // Réinitialiser les champs aux valeurs originales
                $('#montant').val(montantInitial);
                $('#reference').val('{{ $cotisation->reference }}');
                $('#mode_paiement').val('{{ $cotisation->mode_paiement }}');
                $('#annee_cotisation').val({{ $cotisation->annee_cotisation }});
                $('#date_paiement').val('{{ \Carbon\Carbon::parse($cotisation->date_paiement)->format("Y-m-d") }}');
                $('#status').val('{{ strtoupper($cotisation->status) }}');
                $('#observation').val('{{ $cotisation->observation }}');
                
                // Réinitialiser les radios
                const typeVersement = '{{ strtoupper($cotisation->type_versement) }}';
                $('input[name="type_versement"][value="' + typeVersement + '"]').prop('checked', true);
                
                // Recalculer
                calculerReste();
                updateEcheanceDate();
                
                toastr.info('Modifications réinitialisées', 'Information', {
                    timeOut: 2000
                });
            }
        }

        // Formater les nombres pour l'affichage
        function formatNumber(number) {
            return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
        }
    </script>

    <!-- Script pour gérer les erreurs de validation -->
    @if($errors->any())
    <script>
        $(document).ready(function() {
            // Afficher les erreurs de validation
            @foreach($errors->all() as $error)
                toastr.error('{{ $error }}', 'Erreur');
            @endforeach
        });
    </script>
    @endif
@endsection