@extends('voyager::master')

@section('page_title', 'Ajouter une facture')

@section('css')
    <style>
        /* Espacement général */
        .dataTables_wrapper .row {
            align-items: center;
        }

        /* Select "Afficher X entrées" */
        .dataTables_length select {
            border-radius: 8px;
            padding: 5px 10px;
            border: 1px solid #dee2e6;
        }

        /* Barre de recherche */
        .dataTables_filter input {
            border-radius: 20px;
            padding: 6px 15px;
            border: 1px solid #dee2e6;
        }

        /* Boutons export */
        .dt-buttons .btn {
            border-radius: 20px !important;
            padding: 6px 15px;
            font-size: 13px;
            margin-left: 5px;
        }

        /* Tableau */
        #agrTable {
            border-radius: 10px;
            overflow: hidden;
        }

        #agrTable thead {
            background-color: #003366;
            color: white;
        }

        #agrTable tbody tr:hover {
            background-color: #f2f6fc;
            transition: 0.2s;
        }

        /* Pagination */
        .dataTables_paginate .paginate_button {
            border-radius: 50px !important;
            margin: 0 3px;
        }
    </style>

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css">


    @include('voyager::dms-agr-facturations.partials.style')
@stop

@section('content')
    <div class="page-content container-fluid">
        <div class="access-ribbon"
            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <!-- Groupe gauche : select + bouton filtrer inline -->
            <div style="display: flex; align-items: center; gap: 10px;">
                <!-- Select année -->
                <select class="form-control" id="annee-select" name="annee" style="width: 100px;">
                    <option value="">-- Toutes les années --</option>
                    @for ($y = 2022; $y <= date('Y') + 1; $y++)
                        <option value="{{ $y }}" {{ $annee == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>

                <!-- Bouton Filtrer -->
                <button class="btn btn-agr" id="btn-filtrer">
                    <i class="bi bi-search"></i> Filtrer
                </button>
            </div>

            <!-- Bouton AJOUTER à droite -->
            @can('add', app("App\Models\DmsAgrCotisation"))
            <div class="ribbon-actions">
                <a href="{{ route('voyager.dms-agr-cotisations.create') }}" class="btn btn-agr">
                    <i class="bi bi-plus-circle"></i> AJOUTER
                </a>
            </div>

            @endcan
        </div>

        <div class="container-fluid">

            <div class="row kpi-row">
                <div class="col-md-4">
                    <div class="kpi-main-card">
                        <div class="kpi-main-label">Chiffre d'Affaire Global (ANNEE {{ $annee }})</div>
                        <div class="kpi-main-value">{{ number_format($ca['ca'], 0, ',', ' ') }} <small
                                style="font-size: 12px;">FCFA</small></div>
                        @php
                            $previousYear = $annee - 1;
                        @endphp
                        @if ($ca['tendance'] == 'up')
                            <div class="text-success">
                                <i class="bi bi-caret-up-fill"></i> +{{ $ca['pourcentage'] }}% vs {{ $previousYear }}
                            </div>
                        @elseif ($ca['tendance'] == 'down')
                            <div class="text-danger">
                                <i class="bi bi-caret-down-fill"></i> {{ $ca['pourcentage'] }}% vs {{ $previousYear }}
                            </div>
                        @else
                            <div class="text-muted">
                                <i class="bi bi-caret-right-fill"></i> {{ $ca['pourcentage'] }}% vs {{ $previousYear }}
                            </div>
                        @endif

                    </div>
                </div>

                <div class="col-md-4">
                    <div class="kpi-main-card">
                        <div class="kpi-main-label">Taux de réalisation de l'objectif (ANNEE {{ $annee }})</div>
                        <div class="kpi-main-value">{{ $taux_realisation['taux_realisation'] }} %</div>
                        <div class="text-primary">
                            <span class="label label-success">
                                Budget Total:
                                {{ number_format($taux_realisation['budget_total'], 0, ',', ' ') }} FCFA
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="kpi-main-card">
                        <div class="kpi-main-label">
                            Top Performance (ANNEE {{ $annee }})
                        </div>

                        <div class="kpi-main-value" style="font-size: 20px; padding: 5px 0;">
                            {{ $top['activite'] ?? 'Aucune activité' }}
                        </div>

                        <div class="{{ ($top['pourcentage'] ?? 0) > 0 ? 'text-success' : 'text-muted' }}">
                            Part : {{ $top['pourcentage'] ?? 0 }} % du CA
                        </div>
                    </div>
                </div>

            </div>

            <div class="row">

                <!-- GRAPHIQUE MENSUEL PAR ACTIVITÉ -->
                <div class="col-md-12">
                    <div class="access-container">
                        <div class="access-header">
                            <i class="bi bi-bar-chart-fill"></i> TOTAL PAR ACTIVITÉ PAR MOIS (ANNEE {{ $annee }})
                        </div>
                        <div class="chart-container">
                            <canvas id="monthlyActivityChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">

                <!-- GRAPHIQUE MENSUEL PAR ACTIVITÉ -->
                <div class="col-md-12">
                    <div class="access-container">
                        <div class="access-header">
                            <i class="bi bi-bar-chart-fill"></i> BALANCE PAR EVENEMENT SUR L'ANNEE - {{ $annee }}
                        </div>
                        <div class="chart-container">
                            <canvas id="financeChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- KPI PAR ACTIVITÉ (TOTAL ANNUEL) -->
                <div class="col-md-12">
                    <div class="access-container">
                        <div class="access-header">
                            <i class="bi bi-pie-chart"></i> RÉPARTITION PAR ACTIVITÉ (ANNEE {{ $annee }})
                        </div>
                        <div class="table-responsive">
                            <table class="table table-kpi table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th>Activité</th>
                                        <th class="text-right">Montant Total</th>
                                        <th width="50%">Part (%)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($stats['details'] as $item)
                                        <tr>
                                            <td>
                                                <span class="label label-primary">{{ $item->evenement }}</span> <br />
                                                {{ $item->activite }}
                                            </td>

                                            <td class="text-right">
                                                {{ number_format($item->total, 0, ',', '.') }}
                                            </td>

                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar {{ $item->color }}" role="progressbar"
                                                        style="width: {{ $item->pourcentage }}%;"
                                                        aria-valuenow="{{ $item->pourcentage }}" aria-valuemin="0"
                                                        aria-valuemax="100">

                                                        {{ $item->pourcentage }}%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach

                                    <tr class="total-row">
                                        <td><strong>TOTAL GLOBAL</strong></td>
                                        <td class="text-right">
                                            <strong>{{ number_format($stats['total_general'], 0, ',', '.') }}</strong>
                                        </td>
                                        <td><strong>100%</strong></td>
                                    </tr>
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>

            </div>


            <div class="row">
                <!-- LISTE DES SAISIES -->
                <div class="col-md-12">
                    <div class="access-container">
                        <div class="access-header">
                            <span><i class="bi bi-list-check"></i> LISTE DES ACTIVITÉS SAISIES</span>
                            <span class="badge" style="background: white; color: #0056b3;"
                                id="countBadge">{{ count($cotisations) }} Entrées</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="agrTable">
                                <thead>
                                    <tr>
                                        <th style="width:5%">Date paiement</th>
                                        <th>Année</th>
                                        <th>Évènement</th>
                                        <th>Activité</th>
                                        <th>Service</th>
                                        <th>Type Contributeur</th>
                                        <th>Contributeur</th>
                                        <th class="hidden">Organisation</th>
                                        <th class="hidden">Pays</th>
                                        <th class="hidden">Email</th>
                                        <th class="hidden">Téléphone</th>
                                        <th>Type Tarif</th>
                                        <th class="hidden">Quantité</th>
                                        <th>Prix Unitaire</th>
                                        <th class="hidden">Montant Normal</th>
                                        <th>Montant Total</th>
                                        <th class="hidden">Observations</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($cotisations as $c)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($c->date_paiement)->format('d/m/Y') }}</td>
                                            <td>{{ $c->annee }}</td>
                                            <td>{{ $c->evenement?->libelle_event ?? 'N/A' }}</td>
                                            <td>{{ $c->activite?->libelle_activite ?? 'N/A' }}</td>
                                            <td>{{ $c->service?->libelle_service ?? 'N/A' }}</td>
                                            <td>
                                                @if ($c->type_contributeur === 'membre')
                                                    <span class="badge badge-primary">Membre</span>
                                                @else
                                                    <span class="badge badge-secondary">Non membre</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($c->type_contributeur === 'membre' && $c->membre)
                                                    {{ $c->membre->libelle_membre ?? $c->membre }}
                                                @else
                                                    {{ $c->nom_complet ?? 'N/A' }}
                                                @endif
                                            </td>
                                            <td class="hidden">{{ $c->organisation ?? 'N/A' }}</td>
                                            <td class="hidden">{{ $c->pays?->libelle_pays ?? 'N/A' }}</td>
                                            <td class="hidden">{{ $c->email ?? 'N/A' }}</td>
                                            <td class="hidden">{{ $c->telephone ?? 'N/A' }}</td>
                                            <td>
                                                @if ($c->type_tarif === 'normal')
                                                    <span class="badge badge-success">Normal</span>
                                                @else
                                                    <span class="badge badge-warning">Spécial</span>
                                                @endif
                                            </td>
                                            <td class="text-right hidden">{{ $c->quantite }}</td>
                                            <td class="text-right">{{ number_format($c->prix_unitaire, 0, ',', ' ') }}
                                                FCFA</td>
                                            <td class="text-right">{{ number_format($c->montant_normal, 0, ',', ' ') }}
                                                FCFA</td>
                                            <td class="text-right hidden">
                                                {{ number_format($c->montant_total, 0, ',', ' ') }}
                                                FCFA</td>
                                            <td class="hidden">
                                                <span class="obs-text" title="{{ $c->observations ?? '' }}">
                                                    {{ Str::limit($c->observations, 30) ?? 'N/A' }}
                                                </span>
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>
                                {{-- <tfoot>
                                    <tr>
                                        <th colspan="12" class="text-right">Totaux:</th>
                                        <th class="text-right" id="total-quantite">0</th>
                                        <th class="text-right" id="total-prix-unitaire">0 FCFA</th>
                                        <th class="text-right" id="total-montant-normal">0 FCFA</th>
                                        <th class="text-right" id="total-montant-total">0 FCFA</th>
                                        <th colspan="2"></th>
                                    </tr>
                                </tfoot> --}}
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @php
                $events = App\Models\DmsEvenementsAgr::whereYear('date_event', $annee)->get();

                $labels = [];
                $depensesData = [];
                $cotisationsData = [];
                $soldesData = [];

                foreach ($events as $event) {
                    $totalDepense = App\Models\DmsAgrDepense::getSumDepensesByEvent($event->id);
                    $totalCotisation = App\Models\DmsAgrCotisation::getSumCotisationsByEvent($event->id);

                    $labels[] = $event->libelle_event;
                    $depensesData[] = $totalDepense ?? 0;
                    $cotisationsData[] = $totalCotisation ?? 0;
                    $soldesData[] = ($totalCotisation ?? 0) - ($totalDepense ?? 0);
                }
            @endphp
            @json($depensesData)
        </div>
    </div>


@stop

@section('javascript')

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

    <!-- Buttons -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap4.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        $(document).ready(function() {

            $('#agrTable').DataTable({
                responsive: true,
                processing: true,
                order: [
                    [0, 'desc']
                ],
                pageLength: 10,

                lengthMenu: [
                    [5, 10, 20, 50, 100, -1],
                    [5, 10, 20, 50, 100, "Tout"]
                ],

                dom: "<'row mb-3'<'col-md-6'l><'col-md-6 text-right'B>>" +
                    "<'row'<'col-md-12'f>>" +
                    "<'row'<'col-md-12'tr>>" +
                    "<'row mt-2'<'col-md-5'i><'col-md-7'p>>",

                buttons: [{
                        extend: 'excelHtml5',
                        text: '📊 Excel',
                        className: 'btn btn-success btn-sm',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    },


                ],

                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json",
                    lengthMenu: "Afficher _MENU_ entrées",
                }

            });

        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const ctx = document.getElementById('financeChart').getContext('2d');

            const financeChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($labels),

                    datasets: [{
                            label: 'Dépenses (XOF)',
                            data: @json($depensesData),
                            backgroundColor: 'rgba(255, 99, 132, 0.7)'
                        },
                        {
                            label: 'Cotisations (XOF)',
                            data: @json($cotisationsData),
                            backgroundColor: 'rgba(75, 192, 192, 0.7)'
                        },
                        {
                            label: 'Solde (XOF)',
                            data: @json($soldesData),
                            backgroundColor: 'rgba(54, 162, 235, 0.7)'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' +
                                        new Intl.NumberFormat('fr-FR').format(context.raw) + ' XOF';
                                }
                            }
                        },
                        legend: {
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return new Intl.NumberFormat('fr-FR').format(value);
                                }
                            }
                        }
                    }
                }
            });

        });
    </script>

    {{-- <div class="chart-container" style="height:400px;">
    <canvas id="monthlyActivityChart"></canvas>
</div> --}}

    <script>
        window.onload = function() {

            // 🔹 Charger les données depuis l'API
            fetch('/gouvernances/admin/dms/chart-data/{{ $annee }}')
                .then(response => response.json())
                .then(result => {

                    // 🔹 Labels des mois
                    const labels = [...Array(12).keys()].map(i => {
                        return new Date(0, i).toLocaleString('fr-FR', {
                            month: 'short'
                        });
                    });

                    // 🔹 Préparer les activités et leurs données par mois
                    let activities = {};

                    result.forEach(item => {
                        if (!activities[item.activite]) {
                            activities[item.activite] = Array(12).fill(0);
                        }
                        // Assigner le montant au mois correspondant (index 0 = janvier)
                        activities[item.activite][item.mois - 1] = item.total;
                    });

                    // 🔹 Construire les datasets pour Chart.js
                    const datasets = Object.keys(activities).map((key, index) => ({
                        label: key,
                        data: activities[key],
                        backgroundColor: getColor(index),
                    }));

                    // 🔹 Initialiser le graphique
                    const ctx = document.getElementById('monthlyActivityChart').getContext('2d');

                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels,
                            datasets
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                x: {
                                    stacked: true
                                },
                                y: {
                                    stacked: true,
                                    beginAtZero: true,
                                    ticks: {
                                        callback: value => (value / 1000000) + 'M'
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const value = context.parsed.y;
                                            return context.dataset.label + ': ' +
                                                new Intl.NumberFormat('fr-FR').format(value) + ' FCFA';
                                        }
                                    }
                                }
                            }
                        }
                    });

                })
                .catch(error => console.error("Erreur chargement graphique:", error));

            // 🔹 Palette de couleurs
            function getColor(index) {
                const colors = [
                    '#0056b3', '#4e86c1', '#82b1ff',
                    '#bccce0', '#003366', '#336699',
                    '#6699cc', '#99ccff', '#cce6ff'
                ];
                return colors[index % colors.length];
            }
        };
    </script>


    <script>
        $(document).ready(function() {
            $('#btn-filtrer').on('click', function() {
                filtrerParAnnee();
            });

            // Filtrer quand on appuie sur Entrée dans le select
            $('#annee-select').on('keypress', function(e) {
                if (e.which == 13) {
                    filtrerParAnnee();
                }
            });

            function filtrerParAnnee() {
                var annee = $('#annee-select').val();
                var currentUrl = new URL(window.location.href);
                var params = new URLSearchParams(currentUrl.search);

                if (annee) {
                    params.set('annee', annee);
                } else {
                    params.delete('annee');
                }

                // Reconstruire l'URL avec les paramètres
                var newUrl = currentUrl.pathname + '?' + params.toString();

                window.location.href = newUrl;
            }
        });
    </script>
@endsection
