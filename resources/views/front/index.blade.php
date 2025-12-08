@extends('front.layout')

@section('content')
    <div class="mb-3 row">
        <div class="col-md-2">
            <label>Année</label>
            <select id="filtre-annee" class="form-control">
                <option value="">Toutes</option>
                @foreach ($annees as $a)
                    <option value="{{ $a }}">{{ $a }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <label>Status</label>
            <select id="filtre-status" class="form-control">
                <option value="">Tous</option>
                @foreach ($statusList as $s)
                    <option value="{{ $s }}">{{ $s }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <label>Pays</label>
            <select id="filtre-pays" class="form-control">
                <option value="">Tous</option>
                @foreach ($pays as $p)
                    <option value="{{ $p->id }}">{{ $p->libelle_pays }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-12 mt-2">
            <button type="reset" class="btn btn-secondary btn-sm" id="resetFilters">Réinitialiser</button>
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

    <!-- MODAL DETAILS -->
    <div class="modal fade" id="detailModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Détails de la gouvernance</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
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

                            <!-- Relations -->
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

            </div>
        </div>
    </div>
@endsection


@section('scripts')
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
        let table = $('#gouvernance-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('front.data') }}",
                data: function(d) {
                    d.annee = $('#filtre-annee').val();
                    d.status = $('#filtre-status').val();
                    d.pays_id = $('#filtre-pays').val();
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
            ]
        });

        // recharge le tableau quand un filtre change
        $('#filtre-annee, #filtre-status, #filtre-pays').on('change', function() {
            table.ajax.reload();
        });


        $(document).on('click', '.detailBtn', function() {

            let id = $(this).data('id');

            $.ajax({
                url: "/gouvernances/front/detail/" + id,
                type: "GET",
                success: function(g) {

                    // Champs simples
                    $('#detail-num_ordre').text(g.num_ordre);
                    $('#detail-annee').text(g.annee);
                    $('#detail-status').text(g.status);
                    $('#detail-sigle_societe').text(g.sigle_societe);
                    $('#detail-denomination').text(g.denomination);
                    $('#detail-date_creation').text(g.date_creation);

                    $('#detail-ep_eu').text(g.ep_eu);
                    $('#detail-membre').text(g.membre);
                    $('#detail-representant').text(g.representant);
                    $('#detail-fonction').text(g.fonction);
                    $('#detail-date_denomination').text(g.date_denomination);
                    $('#detail-observation').text(g.observation);
                    $('#detail-created_at').text(g.created_at);

                    // Relations
                    $('#detail-continent').text(g.continent ? g.continent.nom : '');
                    $('#detail-region').text(g.region ? g.region.nom : '');
                    $('#detail-pays').text(g.pays ? g.pays.nom : '');
                    $('#detail-genre').text(g.genre ? g.genre.libelle : '');

                    // Affiche la modal
                    $('#detailModal').modal('show');
                }
            });

        });

        // bouton reset
        // bouton reset
        $('#resetFilters').on('click', function() {

            // 1. vider tous les champs
            $('.filtre-annee').each(function() {

                if (this.tagName === 'SELECT') {
                    // remettre sur la première option
                    this.selectedIndex = 1;
                } else {
                    // input / date / text
                    $(this).val('');
                }
            });

            // 2. si tu utilises select2, décommente :
            // $('.filtre-gouv.select2').val(null).trigger('change');

            // 3. effacer aussi la recherche globale éventuelle
            table.search('').columns().search('');

            // 4. redessiner le tableau
            table.draw();
        });
    </script>
@endsection
