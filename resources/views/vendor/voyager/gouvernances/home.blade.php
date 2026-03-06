@extends('voyager::master')

@section('page_title', __('voyager::generic.viewing') . ' ' . $dataType->getTranslatedAttribute('display_name_plural'))

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="{{ $dataType->icon }}"></i> {{ $dataType->getTranslatedAttribute('display_name_plural') }}
        </h1>
        @can('add', app($dataType->model_name))
            <a href="{{ route('voyager.' . $dataType->slug . '.create') }}" class="btn btn-success btn-add-new">
                <i class="voyager-plus"></i> <span>{{ __('voyager::generic.add_new') }}</span>
            </a>
        @endcan
        @can('delete', app($dataType->model_name))
            

            <button class="btn btn-info" id="btnImport">
                <i class="voyager-upload"></i> Importer un fichier Excel
            </button>
        @endcan
        @can('edit', app($dataType->model_name))
            @if (!empty($dataType->order_column) && !empty($dataType->order_display_column))
                <a href="{{ route('voyager.' . $dataType->slug . '.order') }}" class="btn btn-primary btn-add-new">
                    <i class="voyager-list"></i> <span>{{ __('voyager::bread.order') }}</span>
                </a>
            @endif
        @endcan

        @include('voyager::multilingual.language-selector')
    </div>
@stop

@section('content')
    <div class="container-fluid">


        <div class="mb-3 row">
            <div class="col-md-2">
                <label>Année</label>
                <select id="filtre-annee" class="form-control filtre-select">
                    <option value="">Toutes</option>
                    @foreach ($annees as $a)
                        <option value="{{ $a }}">{{ $a }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label>Status</label>
                <select id="filtre-status" class="form-control filtre-select">
                    <option value="">Tous</option>
                    @foreach ($statusList as $s)
                        <option value="{{ $s }}">{{ $s }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label>Pays</label>
                <select id="filtre-pays" class="form-control filtre-select">
                    <option value="">Tous</option>
                    @foreach ($pays as $p)
                        <option value="{{ $p->id }}">{{ $p->libelle_pays }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label>Régions</label>
                <select id="filtre-region" class="form-control filtre-select">
                    <option value="">Toutes</option>
                    @foreach ($regions as $p)
                        <option value="{{ $p->id }}">{{ $p->libelle }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label>Continents</label>
                <select id="filtre-continent" class="form-control filtre-select">
                    <option value="">Tous</option>
                    @foreach ($continents as $p)
                        <option value="{{ $p->id }}">{{ $p->libelle }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label>Genre</label>
                <select id="filtre-genre" class="form-control filtre-select">
                    <option value="">Tous</option>
                    @foreach ($genres as $p)
                        <option value="{{ $p->id }}">{{ $p->libelle_genre }}</option>
                    @endforeach
                </select>
            </div>

        </div>
        <div class="col-md-12 mt-2 text-center row">
            <button type="button" class="btn btn-info btn-sm" id="resetFilters">Réinitialiser <i
                    class="bi bi-arrow-repeat fa-2x"></i></button>
        </div>
    </div>
    <div class="page-content browse container-fluid">
        @include('voyager::alerts')
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table id="gouvernance-table" class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Année</th>
                                        <th>Status</th>
                                        <th>Sigle Société</th>
                                        <th>Dénomination</th>
                                        <th>Pays</th>
                                        <th>Date Création</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('voyager::gouvernances.partials.details')

    <div class="modal fade" id="importModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="importForm" enctype="multipart/form-data">
                    @csrf

                    <div class="modal-header">
                        <h4 class="modal-title">Importer des Gouvernances</h4>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <label>Choisir un fichier Excel (.xlsx / .xls)</label>
                            <input type="file" name="file" class="form-control" required>
                        </div>

                        <!-- Loader -->
                        <div id="loader" style="display:none; text-align:center;">
                            <img src="/loader.gif" width="60"><br>
                            <small>Importation en cours...</small>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-primary">Importer</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
@stop




@section('css')
    @if (!$dataType->server_side && config('dashboard.data_tables.responsive'))
        <link rel="stylesheet" href="{{ voyager_asset('lib/css/responsive.dataTables.min.css') }}">
    @endif
@stop

@section('javascript')
    <!-- DataTables -->
    {{--  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script> --}}
    {{-- <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script> --}}

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // OUVERTURE MODAL
            $('#importModal').modal('show');
            $('#btnImport').click(function() {
                //$('#importModal').modal('show');
                alert('test');
            });

            // SUBMIT AJAX
            $('#importForm').submit(function(e) {
                e.preventDefault();

                let formData = new FormData(this);

                // Afficher loader
                $('#loader').show();

                $.ajax({
                    url: "{{ route('gouvernance.import') }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,

                    success: function(res) {
                        $('#loader').hide();
                        $('#importModal').modal('hide');

                        Swal.fire({
                            icon: 'success',
                            title: 'Importation réussie',
                            text: 'Les données ont été importées avec succès !'
                        });

                        // Rafraîchir la page du listing
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    },

                    error: function(xhr) {
                        $('#loader').hide();

                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: xhr.responseJSON?.message ??
                                "Une erreur est survenue."
                        });
                    }
                });
            });


            // Initialisation de la DataTable
            let table = $('#gouvernance-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('front.data') }}",
                    data: function(d) {
                        d.annee = $('#filtre-annee').val();
                        d.status = $('#filtre-status').val();
                        d.pays_id = $('#filtre-pays').val();
                        d.region_id = $('#filtre-region').val();
                        d.continent_id = $('#filtre-continent').val();
                        d.genre_id = $('#filtre-genre').val();
                    }
                },
                columns: [{
                        data: 'num_ordre'
                    },
                    {
                        data: 'annee'
                    },
                    {
                        data: 'status'
                    },
                    {
                        data: 'sigle_societe'
                    },
                    {
                        data: 'denomination'
                    },
                    {
                        data: 'pays'
                    },
                    {
                        data: 'date_creation'
                    },
                    {
                        data: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],

                pageLength: 25,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "Tous"]
                ]
            });

            // Recharge le tableau quand un filtre change
            $('.filtre-select').on('change', function() {
                table.ajax.reload();
            });

            // Gestion des détails
            $(document).on('click', '.detailBtn', function() {
                let id = $(this).data('id');

                // Afficher un loader
                $('#detailModal .modal-body').html(
                    '<div class="text-center p-5"><i class="fas fa-spinner fa-spin fa-2x"></i></div>');
                $('#detailModal').modal('show');

                $.ajax({
                    url: "/gouvernances/front/detail/" + id,
                    type: "GET",
                    success: function(g) {
                        // Vérifier si la réponse est valide
                        if (!g) {
                            $('#detailModal .modal-body').html(
                                '<div class="alert alert-danger">Données non disponibles</div>'
                            );
                            return;
                        }

                        // Mettre à jour les champs du modal
                        const formatDate = (dateString) => {
                            if (!dateString) return '';
                            const date = new Date(dateString);
                            return date.toLocaleDateString('fr-FR');
                        };

                        const formatText = (text) => {
                            return text || '';
                        };

                        $('#detailModal .modal-body').html(`
                            <table class="table table-striped table-bordered">
                                <tbody>
                                    <tr><th># (num_ordre)</th><td>${formatText(g.num_ordre)}</td></tr>
                                    <tr><th>Année</th><td>${formatText(g.annee)}</td></tr>
                                    <tr><th>Status</th><td>${formatText(g.status)}</td></tr>
                                    <tr><th>Sigle Société</th><td>${formatText(g.sigle_societe)}</td></tr>
                                    <tr><th>Dénomination</th><td>${formatText(g.denomination)}</td></tr>
                                    <tr><th>Date Création</th><td>${formatDate(g.date_creation)}</td></tr>
                                    <tr><th>EP / EU</th><td>${formatText(g.ep_eu)}</td></tr>
                                    <tr><th>Membre</th><td>${formatText(g.membre)}</td></tr>
                                    <tr><th>Représentant</th><td>${formatText(g.representant)}</td></tr>
                                    <tr><th>Fonction</th><td>${formatText(g.fonction)}</td></tr>
                                    <tr><th>Date Dénomination</th><td>${formatDate(g.date_denomination)}</td></tr>
                                    <tr><th>Continent</th><td>${g.continent ? g.continent.libelle : ''}</td></tr>
                                    <tr><th>Région</th><td>${g.region ? g.region.libelle : ''}</td></tr>
                                    <tr><th>Pays</th><td>${g.pays ? g.pays.libelle_pays : ''}</td></tr>
                                    <tr><th>Genre</th><td>${g.genre ? g.genre.libelle_genre : ''}</td></tr>
                                    <tr><th>Observation</th><td>${formatText(g.observation)}</td></tr>
                                    <tr><th>Created At</th><td>${formatDate(g.created_at)}</td></tr>
                                </tbody>
                            </table>
                        `);
                    },
                    error: function(xhr) {
                        $('#detailModal .modal-body').html(`
                            <div class="alert alert-danger">
                                Erreur lors du chargement des données
                                ${xhr.status === 404 ? ' - Enregistrement non trouvé' : ''}
                            </div>
                        `);
                    }
                });
            });

            // Bouton reset
            $('#resetFilters').on('click', function() {
                // Réinitialiser tous les selects
                $('.filtre-select').each(function() {
                    $(this).val('');
                });

                // Recharger le tableau
                table.ajax.reload();
            });


            // === DELETE ===
            $(document).on('click', '.deleteBtn', function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Supprimer ?',
                    text: "Cette action est irréversible",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Oui, supprimer'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "/gouvernances/front/delete/" + id,
                            type: "DELETE",
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function() {
                                table.ajax.reload();
                                Swal.fire("Supprimé", "L'élément a été supprimé",
                                    "success");
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
