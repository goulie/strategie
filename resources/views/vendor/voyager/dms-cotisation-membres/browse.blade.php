@extends('voyager::master')

@section('page_title', 'Ajouter une facture')

@section('css')
    @include('voyager::dms-agr-facturations.partials.style')
@stop

@section('content')
    <div class="page-content container-fluid">
        <div class="access-ribbon">
            <div class="ribbon-actions">
                <button class="btn btn-agr">
                    <i class="bi bi-plus-circle"></i> AJOUTER UN AGR
                </button>
                <button class="btn btn-default">
                    <i class="bi bi-download"></i> Rapport Annuel
                </button>
                <button class="btn btn-default" onclick="window.print()">
                    <i class="bi bi-printer"></i> Imprimer
                </button>
            </div>
        </div>

        <div class="container-fluid">

            <!-- KPI GLOBAUX DE L'ANNÉE -->
            <div class="row kpi-row">
                <div class="col-md-3">
                    <div class="kpi-main-card">
                        <div class="kpi-main-label">Chiffre d'Affaire Global 2024</div>
                        <div class="kpi-main-value">145.250.000 <small style="font-size: 12px;">FCFA</small></div>
                        <div class="text-success"><i class="bi bi-caret-up-fill"></i> +12% vs 2023</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="kpi-main-card">
                        <div class="kpi-main-label">Total Membres Actifs</div>
                        <div class="kpi-main-value">124</div>
                        <div class="text-primary">85% Taux de rétention</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="kpi-main-card">
                        <div class="kpi-main-label">Activités Réalisées</div>
                        <div class="kpi-main-value">42</div>
                        <div class="text-info">8 Activités en cours</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="kpi-main-card">
                        <div class="kpi-main-label">Top Performance</div>
                        <div class="kpi-main-value" style="font-size: 20px; padding: 5px 0;">Webinaires</div>
                        <div class="text-muted">Part : 32% du CA</div>
                    </div>
                </div>
            </div>

            <div class="row">
                
                <!-- GRAPHIQUE MENSUEL PAR ACTIVITÉ -->
                <div class="col-md-12">
                    <div class="access-container">
                        <div class="access-header">
                            <i class="bi bi-bar-chart-fill"></i> TOTAL PAR ACTIVITÉ PAR MOIS (ANNÉE EN COURS)
                        </div>
                        <div class="chart-container">
                            <canvas id="monthlyActivityChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- KPI PAR ACTIVITÉ (TOTAL ANNUEL) -->
                <div class="col-md-12">
                    <div class="access-container">
                        <div class="access-header">
                            <i class="bi bi-pie-chart"></i> RÉPARTITION ANNUELLE PAR ACTIVITÉ
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
                                    <tr>
                                        <td>Formations / Produits</td>
                                        <td class="text-right">45.000.000</td>
                                        <td>
                                            <div class="progress progress-bar-custom">
                                                <div class="progress-bar progress-bar-fill" style="width: 31%"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Annuaire</td>
                                        <td class="text-right">12.500.000</td>
                                        <td>
                                            <div class="progress progress-bar-custom">
                                                <div class="progress-bar progress-bar-fill" style="width: 8%"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Bulletins & AfWA News</td>
                                        <td class="text-right">8.750.000</td>
                                        <td>
                                            <div class="progress progress-bar-custom">
                                                <div class="progress-bar progress-bar-fill" style="width: 6%"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Webinaires & Panels</td>
                                        <td class="text-right">35.000.000</td>
                                        <td>
                                            <div class="progress progress-bar-custom">
                                                <div class="progress-bar progress-bar-fill" style="width: 24%"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Forums Régionaux</td>
                                        <td class="text-right">44.000.000</td>
                                        <td>
                                            <div class="progress progress-bar-custom">
                                                <div class="progress-bar progress-bar-fill" style="width: 30%"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="total-row">
                                        <td>TOTAL GLOBAL</td>
                                        <td class="text-right">145.250.000</td>
                                        <td>100%</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@stop

@section('javascript')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Initialisation du graphique Chart.js
        window.onload = function() {
            const ctx = document.getElementById('monthlyActivityChart').getContext('2d');

            const labels = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sept', 'Oct', 'Nov', 'Déc'];

            // Simulation de données par activité
            const data = {
                labels: labels,
                datasets: [{
                        label: 'Formations',
                        data: [5, 7, 10, 8, 12, 9, 6, 4, 11, 15, 13, 10].map(v => v * 1000000),
                        backgroundColor: '#0056b3',
                    },
                    {
                        label: 'Annuaire',
                        data: [2, 1, 3, 2, 2, 4, 2, 1, 3, 2, 1, 2].map(v => v * 1000000),
                        backgroundColor: '#4e86c1',
                    },
                    {
                        label: 'Webinaires',
                        data: [4, 6, 5, 7, 8, 6, 7, 5, 9, 8, 7, 6].map(v => v * 1000000),
                        backgroundColor: '#82b1ff',
                    },
                    {
                        label: 'Forums',
                        data: [0, 0, 15, 0, 0, 20, 0, 0, 10, 0, 0, 12].map(v => v * 1000000),
                        backgroundColor: '#bccce0',
                    }
                ]
            };

            new Chart(ctx, {
                type: 'bar',
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: {
                                    size: 11
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += new Intl.NumberFormat('fr-FR').format(context.parsed.y) +
                                            ' FCFA';
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            stacked: true,
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return (value / 1000000) + 'M';
                                }
                            }
                        }
                    }
                }
            });
        };
    </script>
@endsection
