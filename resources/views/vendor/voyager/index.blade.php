@extends('voyager::master')

@section('page_title', 'Tableau de Bord AGR')

@section('css')
    @include('voyager::dms-agr-facturations.partials.style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        /* Vos styles existants */
        .access-ribbon {
            background-color: #ffffff;
            border-bottom: 2px solid #0056b3;
            padding: 10px 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .access-container {
            background-color: #fff;
            border: 1px solid #0056b3;
            margin-bottom: 25px;
            border-radius: 4px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .access-header {
            background-color: #0056b3;
            color: white;
            padding: 10px 15px;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Styles KPI */
        .kpi-card {
            background: #fff;
            border-left: 5px solid #0056b3;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .kpi-title {
            font-size: 12px;
            text-transform: uppercase;
            color: #666;
        }

        .kpi-value {
            font-size: 24px;
            font-weight: bold;
            color: #0056b3;
        }

        .chart-wrapper {
            padding: 15px;
            min-height: 300px;
        }
    </style>

<style>
    .google-visualization-tooltip {
        border: 2px solid #0056b3 !important;
        border-radius: 5px !important;
        padding: 12px !important;
        font-family: sans-serif !important;
        min-width: 150px;
    }
    .google-visualization-tooltip b {
        color: #0056b3;
        font-size: 15px;
        display: block;
        margin-bottom: 5px;
    }
</style>
@stop

@section('page_header')
    <div class="container-fluid">
        <div class="access-ribbon">
            <div style="font-weight: bold; color: #0056b3; font-size: 18px;">
                <i class="bi bi-speedometer2"></i> DASHBOARD DES ACTIVITÉS (AGR)
            </div>
            <div class="ribbon-actions">
                <button class="btn btn-default" onclick="window.location.reload()">
                    <i class="bi bi-arrow-clockwise"></i> Actualiser
                </button>
                <button class="btn btn-primary" style="background: #0056b3; border:none;">
                    <i class="bi bi-file-earmark-pdf"></i> Exporter Rapport
                </button>
            </div>
        </div>

        <div class="col-md-12">
            <div class="access-container">
                <div class="access-header">
                    <span><i class="bi bi-globe"></i> CARTOGRAPHIE MONDIALE DES MEMBRES</span>
                    <span class="badge" style="background: white; color: #0056b3;">Données Interactives</span>
                </div>
                <div class="chart-wrapper" style="height: 500px; padding: 10px;">
                    <div id="world_map_interactive" style="width: 100%; height: 100%;"></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="kpi-card">
                    <div class="kpi-title">Total Encaissé (2024)</div>
                    <div class="kpi-value">45,850,000 <small>CFA</small></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="kpi-card" style="border-left-color: #28a745;">
                    <div class="kpi-title">Nombre d'Activités</div>
                    <div class="kpi-value">124</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="kpi-card" style="border-left-color: #ffc107;">
                    <div class="kpi-title">Membres Actifs</div>
                    <div class="kpi-value">38</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="kpi-card" style="border-left-color: #17a2b8;">
                    <div class="kpi-title">Taux de Croissance</div>
                    <div class="kpi-value">+12.5%</div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="access-container">
                    <div class="access-header">
                        <span><i class="bi bi-pie-chart"></i> RÉPARTITION PAR TYPE D'ACTIVITÉ</span>
                    </div>
                    <div class="chart-wrapper">
                        <canvas id="chartActivites"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="access-container">
                    <div class="access-header">
                        <span><i class="bi bi-graph-up-arrow"></i> ÉVOLUTION MENSUELLE (FCFA)</span>
                    </div>
                    <div class="chart-wrapper">
                        <canvas id="chartEvolution"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="access-container">
                    <div class="access-header">
                        <span><i class="bi bi-trophy"></i> TOP 5 MEMBRES BÉNÉFICIAIRES</span>
                    </div>
                    <div class="chart-wrapper">
                        <canvas id="chartTopMembres"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="access-container">
                    <div class="access-header">
                        <span><i class="bi bi-bullseye"></i> PERFORMANCE PAR CATÉGORIE</span>
                    </div>
                    <div class="chart-wrapper">
                        <canvas id="chartPerformance"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="access-container">
                    <div class="access-header">
                        <span><i class="bi bi-speedometer"></i> TAUX DE RÉALISATION ANNUEL</span>
                    </div>
                    <div class="chart-wrapper" style="position: relative;">
                        <canvas id="chartGauge"></canvas>
                        <div
                            style="position: absolute; top: 65%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                            <span id="gaugeValue" style="font-size: 30px; font-weight: bold; color: #0056b3;">75%</span><br>
                            <small style="color: #666; text-transform: uppercase;">de l'objectif</small>
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
        // Configuration globale des couleurs
        const colors = ['#0056b3', '#007bff', '#3391ff', '#66adff', '#99c9ff', '#cce5ff'];

        // 1. Graphique Activités (Doughnut)
        new Chart(document.getElementById('chartActivites'), {
            type: 'doughnut',
            data: {
                labels: ['Formations', 'Webinaires', 'Forums', 'Benchmarking', 'Autres'],
                datasets: [{
                    data: [40, 20, 15, 15, 10],
                    backgroundColor: colors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // 2. Evolution Mensuelle (Line)
        new Chart(document.getElementById('chartEvolution'), {
            type: 'line',
            data: {
                labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
                datasets: [{
                    label: 'Recettes 2024',
                    data: [5000000, 7500000, 6000000, 9000000, 8500000, 11000000],
                    borderColor: '#0056b3',
                    backgroundColor: 'rgba(0, 86, 179, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // 3. Top Membres (Horizontal Bar)
        new Chart(document.getElementById('chartTopMembres'), {
            type: 'bar',
            data: {
                labels: ['SODECI', 'ONEP', 'SENELEC', 'CAMWATER', 'SEG'],
                datasets: [{
                    label: 'Montant versé (FCFA)',
                    data: [12000000, 9500000, 7000000, 5000000, 4000000],
                    backgroundColor: '#0056b3'
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // 4. Performance (Radar)
        new Chart(document.getElementById('chartPerformance'), {
            type: 'polarArea',
            data: {
                labels: ['Digital', 'Présentiel', 'Sponsoring', 'Adhésion'],
                datasets: [{
                    data: [85, 60, 90, 70],
                    backgroundColor: [
                        'rgba(0, 86, 179, 0.7)',
                        'rgba(40, 167, 69, 0.7)',
                        'rgba(255, 193, 7, 0.7)',
                        'rgba(23, 162, 184, 0.7)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // 4. Graphique Jauge (Doughnut semi-circulaire)
        const targetValue = 75; // Valeur actuelle
        const remainingValue = 100 - targetValue; // Reste pour atteindre 100%

        new Chart(document.getElementById('chartGauge'), {
            type: 'doughnut',
            data: {
                labels: ['Réalisé', 'Reste'],
                datasets: [{
                    data: [targetValue, remainingValue],
                    backgroundColor: ['#0056b3', '#e7f1ff'], // Bleu foncé vs Bleu très clair
                    borderWidth: 0,
                    circumference: 180, // Demi-cercle
                    rotation: 270, // Début en bas à gauche
                    cutout: '80%' // Épaisseur de la jauge
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }, // Cacher la légende pour l'effet jauge
                    tooltip: {
                        enabled: false
                    }
                }
            }
        });
    </script>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>
    google.charts.load('current', {
        'packages': ['geochart']
    });
    google.charts.setOnLoadCallback(drawMap);

    function drawMap() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Country');
        data.addColumn('number', 'Recettes (FCFA)');
        data.addColumn({type: 'string', role: 'tooltip', p: {html: true}});

        data.addRows([
            ['Ivory Coast', 15000000, '<b>Côte d\'Ivoire</b><br>Membres: SODECI, ONEP<br>CA: 15,000,000 FCFA'],
            ['Senegal', 8500000, '<b>Sénégal</b><br>Membres: SEN\'EAU<br>CA: 8,500,000 FCFA'],
            ['Nigeria', 12000000, '<b>Nigeria</b><br>Membres: LWC<br>CA: 12,000,000 FCFA'],
            ['France', 4500000, '<b>France</b><br>Partenaire: AFD<br>CA: 4,500,000 FCFA'],
            ['Cameroon', 7000000, '<b>Cameroun</b><br>Membres: CAMWATER<br>CA: 7,000,000 FCFA'],
            ['Morocco', 20000000, '<b>Maroc</b><br>Membres: ONEE<br>CA: 20,000,000 FCFA']
        ]);

        var options = {
            colorAxis: {colors: ['#e7f1ff', '#0056b3']}, // Dégradé de bleu
            backgroundColor: '#ffffff',
            datalessRegionColor: '#f5f5f5',
            defaultColor: '#f5f5f5',
            tooltip: { isHtml: true, trigger: 'focus' } // 'focus' déclenche au clic
        };

        var chart = new google.visualization.GeoChart(document.getElementById('world_map_interactive'));
        chart.draw(data, options);

        // Rendre la carte responsive
        window.onresize = function() {
            chart.draw(data, options);
        };
    }
</script>
@stop
