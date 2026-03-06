@extends('voyager::master')

@section('page_title', 'Ajouter une cotisation')

@section('page_header')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h1 class="text-primary" style="font-weight: bold;">COTISATION</h1>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <form action="{{ route('store_cotisation.store') }}" method="POST" id="cotisationForm">
                            @csrf
                            <input type="hidden" name="cotisation_existante_id" id="cotisation_existante_id">
                            <input type="hidden" name="est_complement" id="est_complement" value="0">

                            <div class="row">
                                <!-- 1. Sélection du membre -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="membre_id" style="margin-right: 10px;">Sélectionner un membre :</label>
                                        <select name="membre_id" id="membre_id" class="form-control select2" required>
                                            <option value="">-- Choisir un membre --</option>
                                            @foreach (App\Models\DmsMembre::with('plan_adhesion')->get() as $membre)
                                                <option value="{{ $membre->id }}"
                                                    {{ old('membre_id') == $membre->id ? 'selected' : '' }}>
                                                    {{ $membre->libelle_membre }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('membre_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- 2. Année de cotisation -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="annee_cotisation" style="margin-right: 10px;">Année de cotisation :</label>
                                        <select name="annee_cotisation" id="annee_cotisation" class="form-control" required>
                                            @php
                                                $currentYear = date('Y');
                                                $years = [];
                                                for ($i = 0; $i <= 3; $i++) {
                                                    $years[] = $currentYear - $i;
                                                }
                                                rsort($years);
                                            @endphp
                                            @foreach ($years as $year)
                                                <option value="{{ $year }}"
                                                    {{ old('annee_cotisation', $currentYear) == $year ? 'selected' : '' }}>
                                                    {{ $year }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('annee_cotisation')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- 3. Montant total (désactivé) -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="montant_total" style="margin-right: 10px;">Montant total (XOF) :</label>
                                        <div class="input-group">
                                            <input type="number" name="montant_total" id="montant_total"
                                                class="form-control" placeholder="0" readonly>
                                            <span class="input-group-addon">XOF</span>
                                        </div>
                                        <input type="hidden" name="montant_total" id="montant_total_hidden">
                                        <div id="loading-montant" style="display: none; color: #007bff; margin-top: 5px;">
                                            <i class="voyager-refresh voyager-refresh-animate"></i> Chargement...
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section info cotisation existante -->
                            <div id="section_cotisation_existante" class="alert alert-info" style="display: none;">
                                <h5><i class="voyager-info-circled"></i> Information sur la cotisation existante</h5>
                                <p>Ce membre a déjà une cotisation pour cette année.</p>
                                <p><strong>Déjà payé :</strong> <span id="montant_deja_paye"></span> XOF</p>
                                <p><strong>Reste à payer :</strong> <span id="reste_existant"></span> XOF</p>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="payer_reste" name="payer_reste"
                                        value="1">
                                    <label class="form-check-label" for="payer_reste">
                                        Payer uniquement le reste dû
                                    </label>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label style="margin-right: 10px; display: block;">Type de paiement :</label>
                                        <div class="mydict">
                                            <div>
                                                <label>
                                                    <input type="radio" name="statut" id="statut_total" value="total"
                                                        checked>
                                                    <span>Paiement Total</span>
                                                </label>
                                                <label>
                                                    <input type="radio" name="statut" id="statut_partiel" value="partiel">
                                                    <span>Paiement Partiel</span>
                                                </label>
                                            </div>
                                        </div>
                                        @error('statut')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- 4. Montant à payer -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="montant_a_payer" style="margin-right: 10px;">Montant à payer (XOF) :</label>
                                        <div class="input-group">
                                            <input type="number" name="montant_a_payer" id="montant_a_payer"
                                                class="form-control" placeholder="0" required>
                                            <span class="input-group-addon">XOF</span>
                                        </div>
                                        <input type="hidden" name="montant_a_payer_hidden" id="montant_a_payer_hidden">
                                        @error('montant_a_payer')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                        <small class="form-text text-muted" id="info_montant"></small>
                                    </div>
                                </div>

                                <!-- 5. Reste après ce paiement -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="reste_apres_paiement" style="margin-right: 10px;">Reste après paiement (XOF) :</label>
                                        <div class="input-group">
                                            <input type="number" name="reste_apres_paiement" id="reste_apres_paiement"
                                                class="form-control" placeholder="0" readonly>
                                            <span class="input-group-addon">XOF</span>
                                        </div>
                                        <input type="hidden" name="reste_a_payer" id="reste_a_payer_hidden">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- 6. Date de paiement -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="date_paiement" style="margin-right: 10px;">Date de paiement :</label>
                                        <input type="date" name="date_paiement" id="date_paiement"
                                            class="form-control" value="{{ old('date_paiement', date('Y-m-d')) }}"
                                            max="{{ date('Y-m-d') }}" required>
                                        @error('date_paiement')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- 7. Mode de paiement -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="mode_paiement" style="margin-right: 10px;">Mode de paiement :</label>
                                        <select name="mode_paiement" id="mode_paiement" class="form-control" required>
                                            <option value="">-- Choisir un mode --</option>
                                            <option value="cash" {{ old('mode_paiement') == 'cash' ? 'selected' : '' }}>
                                                Cash</option>
                                            <option value="en_ligne"
                                                {{ old('mode_paiement') == 'en_ligne' ? 'selected' : '' }}>En ligne
                                            </option>
                                            <option value="virement"
                                                {{ old('mode_paiement') == 'virement' ? 'selected' : '' }}>Virement
                                            </option>
                                        </select>
                                        @error('mode_paiement')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- 8. Référence du paiement -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="reference" style="margin-right: 10px;">Référence/N° de transaction :</label>
                                        <input type="text" name="reference" id="reference" class="form-control"
                                            placeholder="Ex: CHQ-001, VIR-2024001" value="{{ old('reference') }}">
                                        @error('reference')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <hr>
                                    <div class="text-right">
                                        <button type="reset" class="btn btn-secondary">
                                            <i class="voyager-refresh"></i> Annuler
                                        </button>
                                        <button type="button" class="btn btn-primary btn-lg"
                                            onclick="soumettreFormulaire()" style="padding: 10px 30px;">
                                            <i class="voyager-plus"></i> Enregistrer le paiement
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('javascript')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let montantTotal = 0;
        let resteExistant = 0;
        let montantDejaPaye = 0;
        let aCotisationExistante = false;

        document.addEventListener('DOMContentLoaded', function() {
            // Initialiser la date max
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('date_paiement').max = today;
            
            // Écouter les changements
            document.getElementById('membre_id').addEventListener('change', chargerMontantMembre);
            document.getElementById('annee_cotisation').addEventListener('change', chargerMontantMembre);
            document.getElementById('montant_a_payer').addEventListener('input', calculerResteApresPaiement);
            document.getElementById('payer_reste').addEventListener('change', gererPaiementReste);
            document.getElementById('statut_total').addEventListener('change', gererTypePaiement);
            document.getElementById('statut_partiel').addEventListener('change', gererTypePaiement);
            
            // Initialiser Select2 si disponible
            if (typeof $.fn.select2 !== 'undefined') {
                $('#membre_id').select2({
                    placeholder: "-- Choisir un membre --",
                    allowClear: true
                });
            }
        });

        function chargerMontantMembre() {
            const membreId = document.getElementById('membre_id').value;
            const annee = document.getElementById('annee_cotisation').value;

            if (!membreId) {
                reinitialiserChamps();
                return;
            }

            // Afficher l'indicateur de chargement
            document.getElementById('loading-montant').style.display = 'block';
            document.getElementById('section_cotisation_existante').style.display = 'none';

            // Appel AJAX avec URL corrigée
            fetch(`/gouvernances/admin/dms/membre/${membreId}/montant-cotisation?annee=${annee}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur réseau');
                    }
                    return response.json();
                })
                .then(data => {
                    
                    if (data.success) {
                        montantTotal = data.data.montant_total;
                        aCotisationExistante = data.data.a_cotisation_existante;
                        resteExistant = data.data.reste_a_payer;
                        montantDejaPaye = data.data.montant_deja_paye;

                        // Mettre à jour les champs
                        document.getElementById('montant_total') = montantTotal;
                        document.getElementById('montant_total_hidden').value = montantTotal;

                        document.getElementById('cotisation_existante_id').value = data.data.cotisation_id || '';

                        // Gérer l'affichage selon s'il y a une cotisation existante
                        if (aCotisationExistante) {
                            document.getElementById('section_cotisation_existante').style.display = 'block';
                            document.getElementById('montant_deja_paye').textContent = montantDejaPaye.toLocaleString('fr-FR');
                            document.getElementById('reste_existant').textContent = resteExistant.toLocaleString('fr-FR');

                            // Si reste > 0, cocher automatiquement "payer le reste"
                            if (resteExistant > 0) {
                                document.getElementById('payer_reste').checked = true;
                                document.getElementById('est_complement').value = '1';
                                gererPaiementReste();
                            } else {
                                document.getElementById('payer_reste').checked = false;
                                document.getElementById('est_complement').value = '0';
                                gererPaiementReste();
                            }
                        } else {
                            // Nouvelle cotisation
                            document.getElementById('payer_reste').checked = false;
                            document.getElementById('est_complement').value = '0';
                            gererPaiementReste();
                        }

                        // Réinitialiser les calculs
                        gererTypePaiement();

                    } else {
                        Swal.fire('Erreur', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    Swal.fire('Erreur', 'Impossible de charger les informations du membre', 'error');
                })
                .finally(() => {
                    document.getElementById('loading-montant').style.display = 'none';
                });
        }

        function gererPaiementReste() {
            const payerReste = document.getElementById('payer_reste').checked;
            const montantAPayer = document.getElementById('montant_a_payer');

            if (payerReste && resteExistant > 0) {
                // Paiement du reste uniquement
                montantAPayer.value = resteExistant;
                montantAPayer.setAttribute('readonly', true);
                montantAPayer.style.backgroundColor = '#f8f9fa';
                document.getElementById('info_montant').textContent = 'Paiement du reste dû uniquement';
                document.getElementById('est_complement').value = '1';
            } else {
                // Nouveau paiement ou paiement complet
                montantAPayer.removeAttribute('readonly');
                montantAPayer.style.backgroundColor = '';
                montantAPayer.value = '';
                document.getElementById('info_montant').textContent = '';
                document.getElementById('est_complement').value = '0';
            }

            calculerResteApresPaiement();
        }

        function gererTypePaiement() {
            const isPartiel = document.getElementById('statut_partiel').checked;
            const montantAPayer = document.getElementById('montant_a_payer');
            const payerReste = document.getElementById('payer_reste').checked;

            if (payerReste && resteExistant > 0) {
                // Si on paie le reste, on ne change pas le montant
                return;
            }

            if (isPartiel) {
                // Paiement partiel - l'utilisateur saisit le montant
                montantAPayer.removeAttribute('readonly');
                montantAPayer.style.backgroundColor = '';
                montantAPayer.placeholder = "Saisir le montant payé";
                document.getElementById('info_montant').textContent = 'Saisissez le montant que vous payez maintenant';
            } else {
                // Paiement total
                if (!aCotisationExistante) {
                    montantAPayer.value = montantTotal;
                } else if (resteExistant > 0) {
                    montantAPayer.value = resteExistant;
                } else {
                    montantAPayer.value = montantTotal;
                }
                montantAPayer.setAttribute('readonly', true);
                montantAPayer.style.backgroundColor = '#f8f9fa';
                document.getElementById('info_montant').textContent = 'Paiement total de la cotisation';
            }

            calculerResteApresPaiement();
        }

        function calculerResteApresPaiement() {
            const montantAPayer = parseFloat(document.getElementById('montant_a_payer').value) || 0;
            let nouveauReste = 0;

            if (aCotisationExistante) {
                // Pour une cotisation existante
                nouveauReste = resteExistant - montantAPayer;
                if (nouveauReste < 0) nouveauReste = 0;
            } else {
                // Pour une nouvelle cotisation
                nouveauReste = montantTotal - montantAPayer;
                if (nouveauReste < 0) nouveauReste = 0;
            }

            // Mettre à jour les champs
            document.getElementById('reste_apres_paiement').value = nouveauReste.toLocaleString('fr-FR');
            document.getElementById('reste_a_payer_hidden').value = nouveauReste;
            document.getElementById('montant_a_payer_hidden').value = montantAPayer;

            // Mettre à jour le statut en fonction du nouveau reste
            if (nouveauReste === 0) {
                document.getElementById('statut_total').checked = true;
            } else {
                document.getElementById('statut_partiel').checked = true;
            }
        }

        function reinitialiserChamps() {
            montantTotal = 0;
            resteExistant = 0;
            montantDejaPaye = 0;
            aCotisationExistante = false;

            document.getElementById('montant_total').value = '';
            document.getElementById('montant_total_hidden').value = '';
            document.getElementById('montant_a_payer').value = '';
            document.getElementById('montant_a_payer_hidden').value = '';
            document.getElementById('reste_apres_paiement').value = '';
            document.getElementById('reste_a_payer_hidden').value = '';
            document.getElementById('section_cotisation_existante').style.display = 'none';
            document.getElementById('cotisation_existante_id').value = '';
            document.getElementById('est_complement').value = '0';
            document.getElementById('payer_reste').checked = false;
            document.getElementById('info_montant').textContent = '';
        }

        function soumettreFormulaire() {
            // Validation
            const membreId = document.getElementById('membre_id').value;
            const montantAPayer = parseFloat(document.getElementById('montant_a_payer').value) || 0;
            const payerReste = document.getElementById('payer_reste').checked;
            const montantTotal = parseFloat(document.getElementById('montant_total_hidden').value) || 0;

            if (!membreId) {
                Swal.fire('Erreur', 'Veuillez sélectionner un membre', 'error');
                return;
            }

            if (montantAPayer <= 0) {
                Swal.fire('Erreur', 'Le montant à payer doit être supérieur à 0', 'error');
                return;
            }

            if (payerReste && montantAPayer > resteExistant) {
                Swal.fire('Erreur', 'Le montant payé ne peut pas dépasser le reste dû', 'error');
                return;
            }

            if (!payerReste && aCotisationExistante && montantAPayer > resteExistant) {
                Swal.fire('Erreur', 'Le montant payé ne peut pas dépasser le reste dû', 'error');
                return;
            }

            if (!payerReste && !aCotisationExistante && montantAPayer > montantTotal) {
                Swal.fire('Erreur', 'Le montant payé ne peut pas dépasser le montant total', 'error');
                return;
            }

            // Message de confirmation personnalisé
            let message = 'Voulez-vous enregistrer ce paiement ?';
            if (payerReste) {
                message = `Voulez-vous enregistrer le paiement du reste (${montantAPayer.toLocaleString('fr-FR')} XOF) ?`;
            } else if (aCotisationExistante) {
                message = `Ce membre a déjà payé ${montantDejaPaye.toLocaleString('fr-FR')} XOF. Voulez-vous ajouter ce paiement de ${montantAPayer.toLocaleString('fr-FR')} XOF ?`;
            }

            Swal.fire({
                title: 'Confirmer',
                text: message,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Oui, enregistrer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Soumettre le formulaire
                    document.getElementById('cotisationForm').submit();
                }
            });
        }
    </script>
@stop