@extends('voyager::master')

@section('page_title', 'Importation des cotisations')

@section('page_header')
    <h1 class="page-title">
        <i class="voyager-upload"></i>
        Importation des cotisations
    </h1>
    @include('voyager::multilingual.language-selector')
@stop

@section('content')
    <div class="page-content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <!-- Zone de téléchargement -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="border-dashed p-4 text-center rounded" style="background-color: #f8f9fa;">
                                    <i class="voyager-upload" style="font-size: 48px; color: #22A7F0;"></i>
                                    <h4>Déposer votre fichier ici</h4>
                                    <p class="text-muted mb-3">Format supporté: .xlsx, .xls, .csv</p>

                                    <form id="importForm" enctype="multipart/form-data">
                                        {{ csrf_field() }}
                                        <div class="form-group">
                                            <input type="file" class="form-control" id="fichier" name="fichier"
                                                accept=".xlsx,.xls,.csv" required>
                                            <small class="form-text text-muted">Le fichier doit contenir les colonnes:
                                                Matricule, Annee,
                                                date_paiement</small>
                                        </div>

                                        <div class="form-group">
                                            <label for="annee">Année de référence</label>
                                            <select class="form-control" id="annee" name="annee">
                                                <option value="">Sélectionner une année</option>
                                                @for ($i = date('Y'); $i >= 2000; $i--)
                                                    <option value="{{ $i }}"
                                                        {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>

                                        <button type="submit" class="btn btn-primary" id="btnImport">
                                            <i class="voyager-upload"></i> Importer
                                        </button>

                                        <a href="javascript:void(0);" onclick="downloadTemplate()" class="btn btn-default">
                                            <i class="voyager-download"></i> Télécharger le modèle
                                        </a>
                                    </form>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="border rounded p-4" style="background-color: #fff;">
                                    <h4><i class="voyager-info-circled" style="color: #22A7F0;"></i> Instructions</h4>
                                    <ul class="list-unstyled">
                                        <li class="mb-2"><i class="voyager-check" style="color: #26B99A;"></i> Format
                                            Excel obligatoire avec en-têtes</li>
                                        <li class="mb-2"><i class="voyager-check" style="color: #26B99A;"></i> Colonne
                                            <strong>Matricule</strong> : identifiant du membre
                                        </li>
                                        <li class="mb-2"><i class="voyager-check" style="color: #26B99A;"></i> Colonne
                                            <strong>Annee</strong> : année de cotisation (ex: 2024)
                                        </li>
                                        <li class="mb-2"><i class="voyager-check" style="color: #26B99A;"></i> Colonne
                                            <strong>date_paiement</strong> : format JJ/MM/AAAA ou "N/A"
                                        </li>
                                        <li><i class="voyager-warning" style="color: #F39C12;"></i> Les doublons (même
                                            membre, même année) seront ignorés</li>
                                    </ul>

                                    <div class="alert alert-warning mt-3">
                                        <small>
                                            <i class="voyager-lightbulb"></i>
                                            <strong>Note :</strong> Pour "N/A" dans date_paiement, le système utilisera le
                                            1er janvier de l'année de cotisation.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Résultats de l'importation -->
                        <div id="resultsContainer" style="display: none;">
                            <div class="alert" id="resultAlert">
                                <h4 class="alert-heading" id="resultTitle"></h4>
                                <div class="row mt-3">
                                    <div class="col-md-3">
                                        <div class="panel" style="background-color: #26B99A; color: white;">
                                            <div class="panel-body text-center">
                                                <h1 id="successCount" style="font-size: 2.5rem; font-weight: 300;">0</h1>
                                                <p class="mb-0">Importés</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="panel" style="background-color: #E74C3C; color: white;">
                                            <div class="panel-body text-center">
                                                <h1 id="failedCount" style="font-size: 2.5rem; font-weight: 300;">0</h1>
                                                <p class="mb-0">Échecs</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="panel" style="background-color: #22A7F0; color: white;">
                                            <div class="panel-body text-center">
                                                <h1 id="totalCount" style="font-size: 2.5rem; font-weight: 300;">0</h1>
                                                <p class="mb-0">Total</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="panel" style="background-color: #7F8C8D; color: white;">
                                            <div class="panel-body text-center">
                                                <button class="btn btn-light" onclick="toggleErrors()">
                                                    <i class="voyager-list"></i> Voir les détails
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Détails des erreurs -->
                            <div id="errorsContainer" style="display: none;">
                                <div class="panel panel-bordered mt-3">
                                    <div class="panel-heading" style="background-color: #f8f9fa;">
                                        <h4 class="panel-title">
                                            <i class="voyager-warning" style="color: #F39C12;"></i>
                                            Détails des erreurs
                                        </h4>
                                    </div>
                                    <div class="panel-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th width="50">#</th>
                                                        <th>Message d'erreur</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="errorsList">
                                                    <!-- Les erreurs seront insérées ici -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tableau d'exemple -->
                        <div class="panel panel-bordered mt-4">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <i class="voyager-data"></i> Exemple de fichier attendu
                                </h4>
                            </div>
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Matricule</th>
                                                <th>Annee</th>
                                                <th>date_paiement</th>
                                                <th>Explication</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>MAFF122023-831</td>
                                                <td>2022</td>
                                                <td>N/A</td>
                                                <td><span class="label label-info">Date par défaut (01/01/2022)</span></td>
                                            </tr>
                                            <tr>
                                                <td>MACT112023-964</td>
                                                <td>2022</td>
                                                <td>N/A</td>
                                                <td><span class="label label-info">Date par défaut (01/01/2022)</span></td>
                                            </tr>
                                            <tr>
                                                <td>MACT112020-155</td>
                                                <td>2022</td>
                                                <td>02/08/2024</td>
                                                <td><span class="label label-success">Date spécifique</span></td>
                                            </tr>
                                            <tr>
                                                <td>MTEST2023-001</td>
                                                <td>2023</td>
                                                <td>15/06/2023</td>
                                                <td><span class="label label-success">Date spécifique</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('javascript')
    <script>
        $(document).ready(function() {
            // Gestion de la soumission du formulaire
            $('#importForm').submit(function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const btn = $('#btnImport');

                // Désactiver le bouton pendant l'import
                btn.prop('disabled', true).html(
                    '<i class="voyager-refresh voyager-refresh-animate"></i> Import en cours...');

                // Cacher les résultats précédents
                $('#resultsContainer').hide();
                $('#errorsContainer').hide();

                // Envoyer la requête AJAX
                $.ajax({
                    url: '{{ route('import.cotisations') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            showResults(response.data, response.errors);
                            // Afficher une notification Voyager
                            toastr.success(response.message ||
                                'Importation terminée avec succès');
                        } else {
                            showError(response.message || 'Erreur lors de l\'importation');
                            toastr.error(response.message || 'Erreur lors de l\'importation');
                        }
                    },
                    error: function(xhr) {
                        let message = 'Erreur serveur';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.error) {
                            message = xhr.responseJSON.error;
                        }
                        showError(message);
                        toastr.error(message);
                    },
                    complete: function() {
                        // Réactiver le bouton
                        btn.prop('disabled', false).html(
                            '<i class="voyager-upload"></i> Importer');
                    }
                });
            });

            // Validation du fichier
            $('#fichier').change(function() {
                const file = this.files[0];
                if (file) {
                    const allowedExtensions = /(\.xlsx|\.xls|\.csv)$/i;
                    if (!allowedExtensions.exec(file.name)) {
                        toastr.error('Format de fichier non supporté. Utilisez .xlsx, .xls ou .csv');
                        $(this).val('');
                    }
                }
            });
        });

        function showResults(data, errors) {
            // Mettre à jour les compteurs
            $('#successCount').text(data.imported);
            $('#failedCount').text(data.failed);
            $('#totalCount').text(data.total);

            // Mettre à jour le titre et la couleur de l'alerte
            const resultAlert = $('#resultAlert');
            const resultTitle = $('#resultTitle');

            if (data.failed === 0) {
                resultAlert.removeClass('alert-warning alert-danger').addClass('alert-success');
                resultTitle.html('<i class="voyager-check"></i> Importation réussie !');
            } else if (data.imported === 0) {
                resultAlert.removeClass('alert-success alert-warning').addClass('alert-danger');
                resultTitle.html('<i class="voyager-x"></i> Importation échouée');
            } else {
                resultAlert.removeClass('alert-success alert-danger').addClass('alert-warning');
                resultTitle.html('<i class="voyager-warning"></i> Importation partielle');
            }

            // Afficher les erreurs si elles existent
            if (errors && errors.length > 0) {
                const errorsList = $('#errorsList');
                errorsList.empty();

                errors.forEach((error, index) => {
                    errorsList.append(`
                        <tr>
                            <td>${index + 1}</td>
                            <td>${error}</td>
                        </tr>
                    `);
                });
            }

            // Afficher les résultats
            $('#resultsContainer').show();
        }

        function showError(message) {
            $('#resultAlert').removeClass('alert-success alert-warning').addClass('alert-danger');
            $('#resultTitle').html(`<i class="voyager-x"></i> ${message}`);
            $('#successCount').text('0');
            $('#failedCount').text('0');
            $('#totalCount').text('0');
            $('#resultsContainer').show();
        }

        function toggleErrors() {
            $('#errorsContainer').toggle();
        }

        function downloadTemplate() {
            // Créer un fichier CSV template
            const headers = ['Matricule', 'Annee', 'date_paiement'];
            const rows = [
                ['MAFF122023-831', '2022', 'N/A'],
                ['MACT112023-964', '2022', 'N/A'],
                ['MACT112020-155', '2022', '02/08/2024']
            ];

            let csvContent = headers.join(',') + '\n';
            rows.forEach(row => {
                csvContent += row.join(',') + '\n';
            });

            // Créer un blob et déclencher le téléchargement
            const blob = new Blob([csvContent], {
                type: 'text/csv;charset=utf-8;'
            });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);

            link.setAttribute('href', url);
            link.setAttribute('download', 'template_cotisations.csv');
            link.style.visibility = 'hidden';

            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            toastr.success('Modèle téléchargé avec succès');
        }
    </script>

    <style>
        .border-dashed {
            border: 2px dashed #dee2e6;
            border-radius: 4px;
        }

        .border-dashed:hover {
            border-color: #22A7F0;
        }

        .voyager-refresh-animate {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .panel-body {
            padding: 20px;
        }

        .panel-heading {
            padding: 15px 20px;
        }
    </style>
@stop
