@extends('front.layout')

@section('content')
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

        <div class="col-12 mt-2">
            <button type="button" class="btn btn-secondary btn-sm" id="resetFilters">Réinitialiser</button>
        </div>

    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Liste des gouvernances</h3>
                </div>
            </div>
            <div class="table-responsive small">
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


    <!-- MODAL DETAILS -->
    <div class="modal fade" id="detailModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Détails de la gouvernance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <table class="table table-striped table-bordered">
                        <tbody>
                            <tr>
                                <th># (num_ordre)</th>
                                <td id="detail-num_ordre"></td>
                            </tr>
                            <tr>
                                <th>Année</th>
                                <td id="detail-annee"></td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td id="detail-status"></td>
                            </tr>
                            <tr>
                                <th>Sigle Société</th>
                                <td id="detail-sigle_societe"></td>
                            </tr>
                            <tr>
                                <th>Dénomination</th>
                                <td id="detail-denomination"></td>
                            </tr>
                            <tr>
                                <th>Date Création</th>
                                <td id="detail-date_creation"></td>
                            </tr>
                            <tr>
                                <th>EP / EU</th>
                                <td id="detail-ep_eu"></td>
                            </tr>
                            <tr>
                                <th>Membre</th>
                                <td id="detail-membre"></td>
                            </tr>
                            <tr>
                                <th>Représentant</th>
                                <td id="detail-representant"></td>
                            </tr>
                            <tr>
                                <th>Fonction</th>
                                <td id="detail-fonction"></td>
                            </tr>
                            <tr>
                                <th>Date Dénomination</th>
                                <td id="detail-date_denomination"></td>
                            </tr>
                            <tr>
                                <th>Continent</th>
                                <td id="detail-continent"></td>
                            </tr>
                            <tr>
                                <th>Région</th>
                                <td id="detail-region"></td>
                            </tr>
                            <tr>
                                <th>Pays</th>
                                <td id="detail-pays"></td>
                            </tr>
                            <tr>
                                <th>Genre</th>
                                <td id="detail-genre"></td>
                            </tr>
                            <tr>
                                <th>Observation</th>
                                <td id="detail-observation"></td>
                            </tr>
                            <tr>
                                <th>Created At</th>
                                <td id="detail-created_at"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
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
                        searchable: false,
                        render: function(data, type, row) {
                            /* return `
                                <button class="btn btn-sm btn-info detailBtn" data-id="${row.id}">
                                    <i class="bi bi-eye-fill"></i>Details
                                </button>
                            `; */
                        }
                    }
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'
                },
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
                    '<div class="text-center p-5"><i class="bi bi-hourglass-split fa-2x"></i></div>');
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
        });
    </script>
@endsection
