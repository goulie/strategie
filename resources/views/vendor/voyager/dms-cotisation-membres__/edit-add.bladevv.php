@extends('voyager::master')

@section('page_title', 'Ajouter une cotisation')

@section('content')
    <div class="container-fluid">
        <style>
            /* --- Styles Personnalisés pour Moderniser Bootstrap 3 --- */
            body {
                background-color: #f5f8fa;
            }
            
            .page-header-custom {
                background: linear-gradient(135deg, #6c5ce7, #a29bfe);
                color: white;
                padding: 20px 30px;
                border-radius: 0 0 20px 20px;
                margin-bottom: 30px;
                box-shadow: 0 4px 15px rgba(108, 92, 231, 0.3);
            }

            .page-header-custom h1 {
                margin: 0;
                font-size: 24px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 1px;
            }

            .modern-card {
                background: #fff;
                border-radius: 15px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.08);
                border: none;
                overflow: hidden;
                margin-bottom: 30px;
            }

            .modern-card-header {
                background: #fff;
                padding: 20px 30px;
                border-bottom: 1px solid #eee;
            }

            .modern-card-body {
                padding: 30px;
            }

            /* Inputs Styling */
            .form-group label {
                text-transform: uppercase;
                font-size: 11px;
                color: #b2bec3;
                font-weight: 700;
                letter-spacing: 0.5px;
                margin-bottom: 8px;
            }

            .form-control {
                border-radius: 10px;
                height: 50px;
                border: 2px solid #f1f2f6;
                box-shadow: none;
                transition: all 0.3s ease;
                font-size: 15px;
            }

            .form-control:focus {
                border-color: #6c5ce7;
                box-shadow: 0 0 0 3px rgba(108, 92, 231, 0.1);
            }

            .input-group-addon {
                background: #f1f2f6;
                border: 2px solid #f1f2f6;
                border-right: none;
                border-radius: 10px 0 0 10px;
                color: #636e72;
                font-weight: bold;
            }

            /* Radio Tiles (Le truc "Fun") */
            .radio-tile-group {
                display: flex;
                gap: 15px;
            }

            .input-container {
                position: relative;
                height: 100%;
                flex-grow: 1;
            }

            .input-container input {
                position: absolute;
                height: 100%;
                width: 100%;
                margin: 0;
                cursor: pointer;
                z-index: 2;
                opacity: 0;
            }

            .radio-tile {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                height: 100%;
                border: 2px solid #f1f2f6;
                border-radius: 10px;
                padding: 15px;
                transition: all 0.3s ease;
                cursor: pointer;
            }

            .radio-tile i {
                font-size: 24px;
                margin-bottom: 5px;
                color: #b2bec3;
            }

            .radio-tile span {
                font-weight: 600;
                color: #636e72;
            }

            /* Active State for Radios */
            input:checked + .radio-tile {
                background-color: #6c5ce7;
                border-color: #6c5ce7;
                color: white;
                box-shadow: 0 5px 15px rgba(108, 92, 231, 0.3);
            }

            input:checked + .radio-tile i,
            input:checked + .radio-tile span {
                color: white;
            }

            /* Highlight Section */
            .highlight-box {
                background: linear-gradient(135deg, #00b894, #55efc4);
                color: white;
                padding: 20px;
                border-radius: 15px;
                text-align: center;
                margin-bottom: 20px;
                box-shadow: 0 5px 15px rgba(0, 184, 148, 0.3);
            }

            .highlight-box h3 {
                margin: 0;
                font-size: 32px;
                font-weight: 800;
            }
            .highlight-box small {
                font-size: 14px;
                opacity: 0.9;
            }

            /* Info Box */
            .info-alert-custom {
                background-color: #ffeaa7;
                border-left: 5px solid #fdcb6e;
                color: #d35400;
                padding: 15px;
                border-radius: 5px;
                margin-bottom: 20px;
            }

            /* Button Styling */
            .btn-submit-custom {
                background: #6c5ce7;
                color: white;
                border: none;
                padding: 15px 40px;
                border-radius: 50px;
                font-weight: bold;
                font-size: 16px;
                box-shadow: 0 5px 15px rgba(108, 92, 231, 0.4);
                transition: transform 0.2s;
                width: 100%;
            }

            .btn-submit-custom:hover {
                transform: translateY(-2px);
                background: #5f4dd0;
                color: white;
            }

            .select2-container .select2-selection--single {
                height: 50px !important;
                border: 2px solid #f1f2f6 !important;
                border-radius: 10px !important;
                padding-top: 10px;
            }
            .select2-selection__arrow {
                top: 12px !important;
            }

            /* Loading Animation */
            @keyframes voyager-refresh-animate {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }

            .voyager-refresh-animate {
                animation: voyager-refresh-animate 1s linear infinite;
                display: inline-block;
            }
        </style>

        {{-- Header Stylisé --}}
        <div class="row">
            <div class="col-md-12">
                <div class="page-header-custom">
                    <h1><i class="voyager-wallet"></i> Nouvelle Cotisation</h1>
                </div>
            </div>
        </div>

        <form action="{{ route('store_cotisation.store') }}" method="POST" id="cotisationForm">
            @csrf
            <input type="hidden" name="cotisation_existante_id" id="cotisation_existante_id">
            <input type="hidden" name="est_complement" id="est_complement" value="0">
            <input type="hidden" name="montant_total" id="montant_total_hidden">
            <input type="hidden" name="montant_a_payer_hidden" id="montant_a_payer_hidden">
            <input type="hidden" name="reste_a_payer" id="reste_a_payer_hidden">

            <div class="row">
                <!-- COLONNE GAUCHE : IDENTIFICATION & CONTEXTE -->
                <div class="col-md-7">
                    <div class="modern-card">
                        <div class="modern-card-header">
                            <h4 class="title"><i class="voyager-person"></i> Identification du Membre</h4>
                        </div>
                        <div class="modern-card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="membre_id">Sélectionner un membre</label>
                                        <select name="membre_id" id="membre_id" class="form-control select2" required>
                                            <option value="">-- Recherche par nom --</option>
                                            @foreach (App\Models\DmsMembre::with('plan_adhesion')->get() as $membre)
                                                <option value="{{ $membre->id }}" {{ old('membre_id') == $membre->id ? 'selected' : '' }}>
                                                    {{ $membre->libelle_membre }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('membre_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="annee_cotisation">Année concernée</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="voyager-calendar"></i></span>
                                            <select name="annee_cotisation" id="annee_cotisation" class="form-control" required>
                                                @php
                                                    $currentYear = date('Y');
                                                    $years = [];
                                                    for ($i = 0; $i <= 3; $i++) $years[] = $currentYear - $i;
                                                @endphp
                                                @foreach ($years as $year)
                                                    <option value="{{ $year }}" {{ old('annee_cotisation', $currentYear) == $year ? 'selected' : '' }}>
                                                        {{ $year }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Montant Total Attendu</label>
                                        <div class="input-group">
                                            <input type="text" id="montant_total_display" class="form-control" placeholder="0" readonly 
                                                   style="font-weight: bold; color: #6c5ce7; background: #fff;">
                                            <span class="input-group-addon">XOF</span>
                                        </div>
                                        <div id="loading-montant" style="display: none; color: #6c5ce7; margin-top: 5px; font-size: 12px;">
                                            <i class="voyager-refresh voyager-refresh-animate"></i> Chargement des données...
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section info cotisation existante (Dynamique) -->
                            <div id="section_cotisation_existante" class="info-alert-custom" style="display: none;">
                                <h5><i class="voyager-info-circled"></i> Historique de paiement</h5>
                                <div style="display: flex; justify-content: space-between; margin-top: 10px;">
                                    <div>Déjà payé : <strong><span id="montant_deja_paye">0</span> XOF</strong></div>
                                    <div>Reste : <strong class="text-danger"><span id="reste_existant">0</span> XOF</strong></div>
                                </div>
                                <hr style="border-color: rgba(0,0,0,0.1); margin: 10px 0;">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="payer_reste" name="payer_reste" value="1" style="transform: scale(1.2); margin-right: 5px;">
                                    <label class="form-check-label" for="payer_reste" style="color: #d35400; cursor: pointer;">
                                        Solder le reste dû maintenant
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- COLONNE DROITE : PAIEMENT -->
                <div class="col-md-5">
                    <div class="modern-card">
                        <div class="modern-card-header">
                            <h4 class="title"><i class="voyager-credit-card"></i> Détails du Paiement</h4>
                        </div>
                        <div class="modern-card-body">
                            
                            <!-- Widget Reste à Payer (Visuel) -->
                            <div class="highlight-box">
                                <small>NOUVEAU SOLDE APRÈS PAIEMENT</small>
                                <h3><span id="reste_apres_paiement_display">0</span> <span style="font-size: 16px;">XOF</span></h3>
                            </div>

                            <div class="form-group">
                                <label>Type de versement</label>
                                <div class="radio-tile-group">
                                    <div class="input-container">
                                        <input type="radio" name="statut" id="statut_total" value="total" checked>
                                        <div class="radio-tile">
                                            <i class="voyager-check-circle"></i>
                                            <span>Total</span>
                                        </div>
                                    </div>
                                    <div class="input-container">
                                        <input type="radio" name="statut" id="statut_partiel" value="partiel">
                                        <div class="radio-tile">
                                            <i class="voyager-pie-chart"></i>
                                            <span>Partiel</span>
                                        </div>
                                    </div>
                                </div>
                                @error('statut') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label for="montant_a_payer">Montant à payer (XOF)</label>
                                <input type="number" name="montant_a_payer" id="montant_a_payer" class="form-control" placeholder="0" required 
                                       style="font-size: 18px; font-weight: bold; color: #2d3436;">
                                <small class="text-muted" id="info_montant"></small>
                                @error('montant_a_payer') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="mode_paiement">Mode</label>
                                        <select name="mode_paiement" id="mode_paiement" class="form-control" required>
                                            <option value="">-- Sélectionnez --</option>
                                            <option value="cash" {{ old('mode_paiement') == 'cash' ? 'selected' : '' }}>Espèces (Cash)</option>
                                            <option value="en_ligne" {{ old('mode_paiement') == 'en_ligne' ? 'selected' : '' }}>En ligne</option>
                                            <option value="virement" {{ old('mode_paiement') == 'virement' ? 'selected' : '' }}>Virement</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="date_paiement">Date</label>
                                        <input type="date" name="date_paiement" id="date_paiement" class="form-control" 
                                               value="{{ old('date_paiement', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="reference">Référence (Optionnel)</label>
                                <input type="text" name="reference" id="reference" class="form-control" 
                                       placeholder="Ex: CHQ-0892" value="{{ old('reference') }}">
                            </div>

                            <br>
                            <button type="button" class="btn-submit-custom" onclick="soumettreFormulaire()">
                                <i class="voyager-check"></i> Encaisser
                            </button>
                            <div class="text-center" style="margin-top: 15px;">
                                <a href="#" onclick="window.history.back()" style="color: #b2bec3;">Annuler</a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Variables globales
        let montantTotal = 0;
        let resteExistant = 0;
        let montantDejaPaye = 0;
        let aCotisationExistante = false;

        document.addEventListener('DOMContentLoaded', function() {
            // Initialiser Select2
            if (typeof $.fn.select2 !== 'undefined') {
                $('#membre_id').select2({
                    placeholder: "Rechercher un membre...",
                    allowClear: true,
                    width: '100%'
                });
            }

            // Événements
            $('#membre_id').on('change', chargerMontantMembre);
            document.getElementById('annee_cotisation').addEventListener('change', chargerMontantMembre);
            document.getElementById('montant_a_payer').addEventListener('input', calculerResteApresPaiement);
            document.getElementById('payer_reste').addEventListener('change', gererPaiementReste);
            
            // Événements pour les boutons radio
            document.getElementById('statut_total').addEventListener('change', gererTypePaiement);
            document.getElementById('statut_partiel').addEventListener('change', gererTypePaiement);

            // Initialiser la date max
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('date_paiement').max = today;
        });

        function chargerMontantMembre() {
            const membreId = $('#membre_id').val();
            const annee = document.getElementById('annee_cotisation').value;

            if (!membreId) {
                reinitialiserChamps();
                return;
            }

            // UI Loading
            document.getElementById('loading-montant').style.display = 'block';
            document.getElementById('section_cotisation_existante').style.display = 'none';
            document.getElementById('montant_total_display').style.opacity = '0.5';

            // Appel AJAX
            fetch(`/gouvernances/admin/dms/membre/${membreId}/montant-cotisation?annee=${annee}`)
                .then(response => {
                    if (!response.ok) throw new Error('Erreur réseau');
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        montantTotal = data.data.montant_total;
                        aCotisationExistante = data.data.a_cotisation_existante;
                        resteExistant = data.data.reste_a_payer;
                        montantDejaPaye = data.data.montant_deja_paye;

                        // Remplir les champs
                        document.getElementById('montant_total_display').value = montantTotal.toLocaleString('fr-FR');
                        document.getElementById('montant_total_hidden').value = montantTotal;
                        document.getElementById('cotisation_existante_id').value = data.data.cotisation_id || '';

                        // Gestion cotisation existante
                        if (aCotisationExistante) {
                            document.getElementById('section_cotisation_existante').style.display = 'block';
                            document.getElementById('montant_deja_paye').textContent = montantDejaPaye.toLocaleString('fr-FR');
                            document.getElementById('reste_existant').textContent = resteExistant.toLocaleString('fr-FR');

                            if (resteExistant > 0) {
                                document.getElementById('payer_reste').checked = true;
                                document.getElementById('est_complement').value = '1';
                                gererPaiementReste();
                            } else {
                                document.getElementById('payer_reste').checked = false;
                                document.getElementById('est_complement').value = '0';
                                gererTypePaiement();
                            }
                        } else {
                            document.getElementById('payer_reste').checked = false;
                            document.getElementById('est_complement').value = '0';
                            gererTypePaiement();
                        }
                        
                        // Mettre à jour les calculs
                        calculerResteApresPaiement();
                    } else {
                        Swal.fire('Erreur', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error(error);
                    Swal.fire('Erreur', 'Impossible de récupérer les données.', 'error');
                })
                .finally(() => {
                    document.getElementById('loading-montant').style.display = 'none';
                    document.getElementById('montant_total_display').style.opacity = '1';
                });
        }

        function gererPaiementReste() {
            const isPayerReste = document.getElementById('payer_reste').checked;
            const inputMontant = document.getElementById('montant_a_payer');

            if (isPayerReste && resteExistant > 0) {
                // Force le mode "Total" pour le reste
                document.getElementById('statut_total').checked = true;
                inputMontant.value = resteExistant;
                inputMontant.readOnly = true;
                inputMontant.style.backgroundColor = '#f1f2f6';
                document.getElementById('est_complement').value = '1';
                document.getElementById('info_montant').textContent = "Solde automatique du reste dû.";
            } else {
                inputMontant.readOnly = false;
                inputMontant.style.backgroundColor = '#fff';
                inputMontant.value = '';
                document.getElementById('est_complement').value = '0';
                document.getElementById('info_montant').textContent = "";
                gererTypePaiement();
            }
            calculerResteApresPaiement();
        }

        function gererTypePaiement() {
            const isTotal = document.getElementById('statut_total').checked;
            const inputMontant = document.getElementById('montant_a_payer');
            const isPayerReste = document.getElementById('payer_reste').checked;

            if (isPayerReste && resteExistant > 0) return;

            if (isTotal) {
                // Paiement Total
                let totalCible = aCotisationExistante ? resteExistant : montantTotal;
                inputMontant.value = totalCible;
                inputMontant.readOnly = true;
                inputMontant.style.backgroundColor = '#f1f2f6';
                document.getElementById('info_montant').textContent = "Montant complet pré-rempli.";
            } else {
                // Paiement Partiel
                inputMontant.value = '';
                inputMontant.readOnly = false;
                inputMontant.style.backgroundColor = '#fff';
                inputMontant.placeholder = "Saisissez le montant...";
                document.getElementById('info_montant').textContent = "Saisissez un acompte.";
                inputMontant.focus();
            }
            calculerResteApresPaiement();
        }

        function calculerResteApresPaiement() {
            const montantSaisi = parseFloat(document.getElementById('montant_a_payer').value) || 0;
            let nouveauReste = 0;

            if (aCotisationExistante) {
                nouveauReste = resteExistant - montantSaisi;
            } else {
                nouveauReste = montantTotal - montantSaisi;
            }

            if (nouveauReste < 0) nouveauReste = 0;

            // Mise à jour visuelle
            document.getElementById('reste_apres_paiement_display').textContent = nouveauReste.toLocaleString('fr-FR');
            document.getElementById('reste_a_payer_hidden').value = nouveauReste;
            document.getElementById('montant_a_payer_hidden').value = montantSaisi;

            // Ajustement automatique du statut
            if (!document.getElementById('montant_a_payer').readOnly && montantSaisi > 0) {
                if (nouveauReste <= 0) {
                    document.getElementById('statut_total').checked = true;
                } else {
                    document.getElementById('statut_partiel').checked = true;
                }
            }
        }

        function reinitialiserChamps() {
            montantTotal = 0;
            resteExistant = 0;
            montantDejaPaye = 0;
            aCotisationExistante = false;

            document.getElementById('montant_total_display').value = '';
            document.getElementById('montant_a_payer').value = '';
            document.getElementById('section_cotisation_existante').style.display = 'none';
            document.getElementById('reste_apres_paiement_display').textContent = '0';
            document.getElementById('montant_total_hidden').value = '';
            document.getElementById('cotisation_existante_id').value = '';
            document.getElementById('info_montant').textContent = '';
            
            document.getElementById('statut_total').checked = true;
            gererTypePaiement();
        }

        function soumettreFormulaire() {
            const membreId = $('#membre_id').val();
            const montantSaisi = parseFloat(document.getElementById('montant_a_payer').value) || 0;
            const modePaiement = document.getElementById('mode_paiement').value;
            const datePaiement = document.getElementById('date_paiement').value;

            // Validations de base
            if (!membreId) {
                Swal.fire('Attention', 'Veuillez sélectionner un membre.', 'warning');
                return;
            }
            
            if (montantSaisi <= 0) {
                Swal.fire('Attention', 'Le montant doit être supérieur à 0.', 'warning');
                return;
            }
            
            if (!modePaiement) {
                Swal.fire('Attention', 'Veuillez sélectionner un mode de paiement.', 'warning');
                return;
            }
            
            if (!datePaiement) {
                Swal.fire('Attention', 'La date est requise.', 'warning');
                return;
            }

            // Vérification plafonds
            let plafond = aCotisationExistante ? resteExistant : montantTotal;
            if (montantSaisi > plafond) {
                Swal.fire('Erreur', `Le montant ne peut pas dépasser ${plafond.toLocaleString('fr-FR')} XOF.`, 'error');
                return;
            }

            // Message de confirmation
            let message = '';
            if (document.getElementById('payer_reste').checked) {
                message = `<b>Paiement du reste dû :</b> ${montantSaisi.toLocaleString('fr-FR')} XOF`;
            } else if (aCotisationExistante) {
                message = `<b>Paiement complémentaire :</b> ${montantSaisi.toLocaleString('fr-FR')} XOF<br>
                          (Déjà payé: ${montantDejaPaye.toLocaleString('fr-FR')} XOF)`;
            } else {
                message = `<b>Nouvelle cotisation :</b> ${montantSaisi.toLocaleString('fr-FR')} XOF sur ${montantTotal.toLocaleString('fr-FR')} XOF`;
            }

            Swal.fire({
                title: 'Confirmer le paiement ?',
                html: message + `<br><small>Mode: ${document.getElementById('mode_paiement').options[document.getElementById('mode_paiement').selectedIndex].text}</small>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#6c5ce7',
                cancelButtonColor: '#d63031',
                confirmButtonText: 'Oui, valider',
                cancelButtonText: 'Annuler',
                backdrop: true,
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // Afficher un indicateur de chargement
                    const submitBtn = document.querySelector('.btn-submit-custom');
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="voyager-refresh voyager-refresh-animate"></i> Enregistrement...';
                    submitBtn.disabled = true;
                    
                    // Soumettre le formulaire
                    document.getElementById('cotisationForm').submit();
                }
            });
        }

        // Réinitialiser le formulaire au reset
        document.getElementById('cotisationForm').addEventListener('reset', function() {
            setTimeout(() => {
                reinitialiserChamps();
                document.getElementById('date_paiement').value = new Date().toISOString().split('T')[0];
            }, 100);
        });
    </script>
@stop