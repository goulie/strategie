@extends('voyager::master')

@section('page_title', 'Statistiques des Membres vs Objectifs')



@section('content')
    <div class="container">



        @php
            $plans = App\Models\DmsPlanAdhesion::withCount('membres')->get();
            $TotalMembers = App\Models\DmsMembre::get()->count();
            $ToAffectMembers = $TotalMembers - $plans->sum('membres_count');
            $nbMembresParMois = App\Models\DmsMembre::nbMembresParMois();
        @endphp



    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">


                    <div class="card-body">
                        <!-- Graphique -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div id="chart-container" style="height: 400px;"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body table-responsive p-0">
                                        <div class="row">

                                            <div class="col-md-6 col-md-offset-3 col-sm-offset-6 col-sm-3 text-center">
                                                <a href="{{ route('voyager.dms-membres.index') }}" class="btn btn-primary btn-add-new">
                                                    <i class="voyager-plus"></i> <span>Liste complète des Membres</span>
                                                </a>
                                                <h3 style="color: #000">Listes des Membres de l'annee
                                                    {{ $annee }}</h3>
                                                <form method="GET" action="{{ route('voyager.dms-membres.index') }}"
                                                    class="form-inline">
                                                    <div class="input-group input-group-sm"
                                                        style="padding-top: 20px;color: #000">
                                                        <label for="annee">Choisissez une annee</label>
                                                        <select name="annee" class="form-control"
                                                            onchange="this.form.submit()">
                                                            @foreach ($anneesDisponibles as $anneeOption)
                                                                <option value="{{ $anneeOption }}"
                                                                    {{ $annee == $anneeOption ? 'selected' : '' }}>
                                                                    {{ $anneeOption }}
                                                                </option>
                                                            @endforeach
                                                        </select>

                                                    </div>
                                                </form>
                                            </div>
                                            
                                        </div>
                                        <table class="table table-hover table-striped" id="dataTable">
                                            <thead>
                                                <tr>
                                                    <th>Matricule</th>
                                                    <th>Intitulé</th>
                                                    <th>Pays</th>
                                                    <th>Organisation</th>
                                                    <th>Type de Membre</th>
                                                    <th>Date Adhésion</th>
                                                    <th>Ligne Budgetaire</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($membres as $membre)
                                                    <tr>
                                                        <td>
                                                            <span
                                                                class="label label-primary">{{ $membre->code_membre }}</span></span>
                                                        </td>
                                                        <td>
                                                            <span
                                                                class="label label-default">{{ $membre->libelle_membre }}</span>
                                                        </td>
                                                        <td><span
                                                                class="label label-default">{{ $membre->pays?->libelle_pays ?? '-' }}</span>
                                                        </td>
                                                        <td><span
                                                                class="label label-default">{{ $membre->organisation ?? '-' }}</span>
                                                        </td>
                                                        <td><span
                                                                class="label label-default">{{ $membre->plan_adhesion?->title_plan ?? '-' }}</span>
                                                        </td>
                                                        <td><span
                                                                class="label label-default">{{ $membre->date_adhesion ?? '-' }}</span>
                                                        </td>
                                                        <td><span
                                                                class="label label-default">{{ $membre->linge_budgetaire?->libelle_ligne ?? '-' }}</span>
                                                        </td>
                                                        <td>
                                                            <span class="label label-light">
                                                                <a href="{{ route('voyager.dms-membres.edit', $membre->id) }}"
                                                                    class="btn btn-primary btn-sm"><i
                                                                        class="bi bi-pencil-square"></i></a>
                                                                <a href="{{ route('voyager.dms-membres.destroy', $membre->id) }}"
                                                                    class="btn btn-danger btn-sm"><i
                                                                        class="bi bi-trash3-fill"></i></a>

                                                                <button class="btn btn-xs btn-primary btn-carte-membre"
                                                                    data-id="{{ $membre->id }}">
                                                                    <i class="voyager-id-card"></i> <i
                                                                        class="bi bi-eye-fill"></i>
                                                                </button>
                                                            </span>

                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center">
                                                            Aucun membre trouvé pour l'année {{ $annee }}
                                                        </td>
                                                    </tr>
                                                @endforelse
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
    </div>

    <div class="modal fade" id="carteMembreModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">

                <div class="modal-header bg-primary text-white">
                    <h4 class="modal-title">
                        <i class="voyager-id-card"></i> Carte d’identité du membre
                    </h4>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body" id="carte-membre-body">
                    <div class="text-center">
                        <i class="voyager-refresh spinner"></i>
                        <p>Chargement…</p>
                    </div>
                </div>

            </div>
        </div>
    </div>



@endsection


@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <style>
        #chart-container {
            min-width: 310px;
            margin: 0 auto;
        }

        .highcharts-credits {
            display: none;
        }
    </style>
@endpush

@push('javascript')
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>

    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>

    <script>
        // Données PHP vers JavaScript
        const chartData = @json($dataChart);

        // Initialiser le graphique
        const chart = Highcharts.chart('chart-container', {
            chart: {
                type: 'spline'
            },
            title: {
                text: `Comparaison Membres vs Objectifs - ${chartData.annee}`
            },
            subtitle: {
                text: 'Évolution mensuelle des adhésions par rapport aux objectifs fixés'
            },
            xAxis: {
                categories: chartData.labels,
                crosshair: true
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Nombre de membres'
                }
            },
            tooltip: {
                shared: true,
                valueSuffix: ' membres'
            },
            plotOptions: {
                spline: {
                    marker: {
                        enabled: true
                    }
                }
            },
            series: [{
                name: 'Membres acquis',
                data: chartData.membres,
                color: '#3498db',
                marker: {
                    symbol: 'circle'
                }
            }, {
                name: 'Objectif',
                data: chartData.objectifs,
                color: '#e74c3c',
                dashStyle: 'shortdot',
                marker: {
                    symbol: 'square'
                }
            }],
            exporting: {
                buttons: {
                    contextButton: {
                        menuItems: ['downloadPNG', 'downloadJPEG', 'downloadPDF', 'downloadSVG']
                    }
                }
            }
        });

        // Gestion du formulaire d'objectif
        $('#formObjectif').submit(function(e) {
            e.preventDefault();

            $.ajax({
                url: '{{ route('statistiques.membres.objectif') }}',
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    alert('Objectif mis à jour avec succès!');
                    // Recharger la page pour mettre à jour le graphique
                    location.reload();
                },
                error: function() {
                    alert('Erreur lors de la mise à jour');
                }
            });
        });
    </script>
@endpush

@section('javascript')
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable({
                dom: 'Bfrtip',
                pageLength: 10,
                ordering: true,
                searching: true,

                buttons: [{
                    extend: 'excelHtml5',
                    text: 'Export Excel',
                    title: 'Recettes {{ $selectedYear ?? now()->year }}',
                    filename: 'recettes_{{ $selectedYear ?? now()->year }}',
                    exportOptions: {
                        columns: ':visible'
                    }
                }],

                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'
                }
            });
        });
    </script>
@stop
