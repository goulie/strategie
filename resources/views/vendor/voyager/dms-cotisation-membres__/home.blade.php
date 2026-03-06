@extends('voyager::master')

@section('page_title', 'Membership Dashboard')
@php
    // Déterminer l'année sélectionnée
$anneeSelectionnee = request('annee', date('Y'));

// Récupérer les statistiques pour l'année sélectionnée
    $cotisationsParMois = App\Models\DmsCotisationMembre::getMontantsParMois($anneeSelectionnee);

    //
    $statistiques = App\Models\DmsCotisationMembre::getCotisationsParTypePlanAdhesion($anneeSelectionnee);

    // Déterminer le titre du graphique en fonction de l'année
$titreGraphique = 'COTISATIONS MEMBRES ' . $anneeSelectionnee;

if ($anneeSelectionnee == date('Y')) {
    $titreGraphique .= ' (Année en cours)';
} elseif ($anneeSelectionnee > date('Y')) {
    $titreGraphique .= ' (Année future)';
    }

@endphp

@section('content')

    <div class="container-fluid">
        <div class="section-header">RECHERCHE & ACTIONS</div>
        <div class="filter-bar">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex gap-2">
                        @php
                            $currentYear = date('Y');
                            $years = [];

                            $startYear = 2022;
                            $yearsCount = $currentYear - $startYear + 1;

                            for ($i = 0; $i < $yearsCount; $i++) {
                                $year = $startYear + $i;
                                $years[] = $year;
                            }

                            $years[] = $currentYear + 1;

                            rsort($years);
                        @endphp

                        <form method="GET" id="yearForm">
                            <select class="form-select form-select-sm" style="width: 180px;" name="annee" id="annee"
                                class="form-control" onchange="this.form.submit()">
                                @foreach ($years as $year)
                                    <option value="{{ $year }}"
                                        {{ request('annee', $currentYear) == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                        @if ($year == $currentYear)
                                            (Année en cours)
                                        @elseif($year == $currentYear + 1)
                                            (Année prochaine)
                                        @endif
                                    </option>
                                @endforeach
                            </select>

                            <button type="button" class="btn btn-add btn-sm d-flex align-items-center gap-1">
                                <i class="bi bi-search"></i> RECHERCHER
                            </button>
                            @if (request()->has('annee'))
                                <a href="{{ route('voyager.dms-cotisation-membres.index') }}" class="btn btn-default"
                                    style="margin-left: 10px;">
                                    <i class="voyager-x"></i> Réinitialiser
                                </a>
                            @endif
                        </form>
                    </div>

                </div>
                <div class="col-md-4 text-end">
                    <a href="{{ route('voyager.dms-cotisation-membres.create') }}"
                        class="btn btn-add btn-sm d-flex align-items-center gap-1 ms-auto">
                        <i class="bi bi-plus-circle"></i> NOUVELLE COTISATION
                    </a>
                </div>
            </div>
        </div>

        <!-- LIGNE 2 : CARTES CUMULE ET GRAPHIQUE -->
        <div class="section-header">CUMULE COTISATIONS MEMBRES {{ $anneeSelectionnee }}</div>
        <div class="row">

            <div class="col-md-4">
                <div class="kpi-card">
                    <div class="kpi-card-header">CUMULE COTISATIONS</div>
                    <div class="kpi-card-body">
                        <span
                            class="kpi-amount">{{ number_format(App\Models\DmsCotisationMembre::totalCotisationsAnnuelles($anneeSelectionnee)) }}
                            XOF</span>
                        <span class="kpi-count"><i class="bi bi-people-fill"></i>
                            {{ number_format(App\Models\DmsCotisationMembre::nbMembresCotisants($anneeSelectionnee)) }}
                            Membres</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="kpi-card">
                    <div class="kpi-card-header">CUMULE COTISATIONS NVEAUX MEMBRES </div>
                    <div class="kpi-card-body">
                        <span
                            class="kpi-amount">{{ number_format(App\Models\DmsCotisationMembre::nouveauxMembresAnnuelsCotisations($anneeSelectionnee)) }}
                            XOF</span>
                        <span class="kpi-count"><i class="bi bi-people-fill"></i>
                            {{ number_format(App\Models\DmsCotisationMembre::nouveauxMembresAnnuels($anneeSelectionnee)) }}
                            Membres</span>
                    </div>
                </div>

            </div>

            <div class="col-md-4">
                <div class="kpi-card">
                    <div class="kpi-card-header">BUDGET {{ $anneeSelectionnee }} </div>
                    <div class="kpi-card-body">
                        <span class="kpi-amount">N/A XOF</span>
                        <span class="kpi-count"><i class="bi bi-people-fill"></i> N/A Membres</span>
                    </div>
                </div>

            </div>
        </div>

        <div class="row">
            <!-- Graphique -->
            <div class="col-md-12">
                <p class="small text-muted mb-2"><b></b></p>
                <figure class="highcharts-figure">
                    <div id="recouvrementChart" style="height: 500px; min-height: 300px;"></div>

                </figure>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="kpi-card">
                    <div class="kpi-card-header">MEMBRES A JOUR</div>
                    <div class="kpi-card-body">
                        <span
                            class="kpi-amount">{{ number_format(App\Models\DmsCotisationMembre::membresAJourStats($anneeSelectionnee)['montant']) }}</span>
                        <span class="kpi-count"><i class="bi bi-people-fill"></i>
                            {{ number_format(App\Models\DmsCotisationMembre::membresAJourStats($anneeSelectionnee)['nombre']) }}
                            Membres</span>
                    </div>
                </div>

            </div>
            <div class="col-md-4">
                <div class="kpi-card">
                    <div class="kpi-card-header">MEMBRES NON A JOUR</div>
                    <div class="kpi-card-body">
                        <span
                            class="kpi-amount">{{ number_format(App\Models\DmsMembre::membresSansPaiementStats($anneeSelectionnee)['montant']) }}
                            XOF</span>
                        <span class="kpi-count"><i class="bi bi-people-fill"></i>
                            {{ number_format(App\Models\DmsMembre::membresSansPaiementStats($anneeSelectionnee)['nombre']) }}
                            Membres</span>
                    </div>
                </div>

            </div>
            <div class="col-md-4">
                <div class="kpi-card">
                    <div class="kpi-card-header">MEMBRES A REACTIVER</div>
                    <div class="kpi-card-body">
                        <span class="kpi-amount">N/A XOF</span>
                        <span class="kpi-count"><i class="bi bi-people-fill"></i> N/A Membres</span>
                    </div>
                </div>

            </div>
        </div>

        @php
            use Illuminate\Support\Facades\DB;
            use Carbon\Carbon;
            $recouvrement = DB::table('dms_cotisation_membres')
                ->select(DB::raw('MONTH(date_paiement) as mois'), DB::raw('SUM(montant) as total'))
                ->where('annee_cotisation', $annee)
                ->whereNotNull('date_paiement')
                ->groupBy(DB::raw('MONTH(date_paiement)'))
                ->orderBy(DB::raw('MONTH(date_paiement)'))
                ->get();

            $cotisationsParMois = array_fill(1, 12, 0);

            foreach ($recouvrement as $row) {
                // Conversion en Millions FCFA
                $cotisationsParMois[$row->mois] = round($row->total / 1_000_000, 2);
            }

            // Highcharts attend un tableau indexé 0 → 11
            $cotisationsParMois = array_values($cotisationsParMois);
        @endphp
        <ul>
            @foreach ($recouvrement as $recouvrement)
                <li>{{ Carbon::createFromDate($recouvrement->mois)->format('F') }} :
                    {{ number_format($recouvrement->total) }} XOF</li>
            @endforeach
        </ul>
        <!-- LIGNE 3 : DETAILS PAR CATEGORIE (ACCORDION BS5) -->
        <div class="section-header">DÉTAILS ET RÉPARTITION</div>

        <div class="accordion mb-4" id="kpiAccordion">
            @foreach ($statistiques as $statistique)
                <div id="faq" role="tablist" aria-multiselectable="true">
                    @if (App\Models\DmsPlanAdhesion::PLAN_ADHESION_ACTIF == $statistique->type_plan_adhesion)
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="questionOne3">
                                <h5 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#faq" href="#answerOne3" aria-expanded="false"
                                        aria-controls="answerOne">
                                        MEMBRES ACTIFS : <span
                                            class="label label-info">{{ number_format($statistique->total_montant) . ' XOF' }}</span>
                                    </a>
                                </h5>
                            </div>
                            <div id="answerOne3" class="panel-collapse collapse" role="tabpanel"
                                aria-labelledby="questionOne">
                                <div class="panel-body">
                                    <div class="row tight-row">

                                        @forelse (App\Models\DmsCotisationMembre::getStatsParAnneeEtTypePlan($anneeSelectionnee, $statistique->type_plan_adhesion)['plans'] as $plan)
                                            <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">
                                                <div class="stat-card">
                                                    <div class="stat-icon-wrapper">
                                                        <i class="bi bi-wallet2"></i>
                                                    </div>
                                                    <h3>{{ $plan['title_plan'] }}</h3>
                                                    <span class="stat-value">{{ number_format($plan['montant_total']) }}
                                                        <span class="text-xof">XOF</span></span>
                                                    <div class="stat-detail">
                                                        <i class="bi bi-people-fill"></i>
                                                        {{ $plan['nombre_membres_cotisants'] > 1 ? $plan['nombre_membres_cotisants'] . ' membres' : $plan['nombre_membres_cotisants'] . ' membre' }}
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="col-12">
                                                <div class="alert alert-info">
                                                    <i class="bi bi-info-circle"></i> Aucune cotisation trouvée pour ce
                                                    type de
                                                    plan.
                                                </div>
                                            </div>
                                        @endforelse



                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                @if (App\Models\DmsPlanAdhesion::PLAN_ADHESION_INDIVIDUEL == $statistique->type_plan_adhesion)
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="questionOne">
                            <h5 class="panel-title">
                                <a data-toggle="collapse" data-parent="#faq" href="#answerOne2" aria-expanded="false"
                                    aria-controls="answerOne2">
                                    MEMBRES INDIVIDUELS : <span
                                        class="label label-info">{{ number_format($statistique->total_montant) . ' XOF' }}</span>
                                </a>
                            </h5>
                        </div>
                        <div id="answerOne2" class="panel-collapse collapse" role="tabpanel"
                            aria-labelledby="questionOne">
                            <div class="panel-body">
                                <div class="row tight-row">
                                    @forelse (App\Models\DmsCotisationMembre::getStatsParAnneeEtTypePlan($anneeSelectionnee, $statistique->type_plan_adhesion)['plans'] as $plan)
                                        <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">
                                            <div class="stat-card">
                                                <div class="stat-icon-wrapper">
                                                    <i class="bi bi-wallet2"></i>
                                                </div>
                                                <h3>{{ $plan['title_plan'] }}</h3>
                                                <span class="stat-value">{{ number_format($plan['montant_total']) }}
                                                    <span class="text-xof">XOF</span></span>
                                                <div class="stat-detail">
                                                    <i class="bi bi-people-fill"></i>
                                                    {{ $plan['nombre_membres_cotisants'] > 1 ? $plan['nombre_membres_cotisants'] . ' membres' : $plan['nombre_membres_cotisants'] . ' membre' }}
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12">
                                            <div class="alert alert-info">
                                                <i class="bi bi-info-circle"></i> Aucune cotisation trouvée pour ce
                                                type de
                                                plan.
                                            </div>
                                        </div>
                                    @endforelse



                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if (App\Models\DmsPlanAdhesion::PLAN_ADHESION_AFFILIE == $statistique->type_plan_adhesion)
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="questionOne">
                            <h5 class="panel-title">
                                <a data-toggle="collapse" data-parent="#faq" href="#answerOne1" aria-expanded="false"
                                    aria-controls="answerOne1">
                                    MEMBRES AFFILIÉS : <span
                                        class="label label-info">{{ number_format($statistique->total_montant) . ' XOF' }}</span>
                                </a>
                            </h5>
                        </div>
                        <div id="answerOne1" class="panel-collapse collapse" role="tabpanel"
                            aria-labelledby="questionOne">
                            <div class="panel-body">
                                <div class="row tight-row">
                                    @forelse (App\Models\DmsCotisationMembre::getStatsParAnneeEtTypePlan($anneeSelectionnee, $statistique->type_plan_adhesion)['plans'] as $plan)
                                        <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">
                                            <div class="stat-card">
                                                <div class="stat-icon-wrapper">
                                                    <i class="bi bi-wallet2"></i>
                                                </div>
                                                <h3>{{ $plan['title_plan'] }}</h3>
                                                <span class="stat-value">{{ number_format($plan['montant_total']) }}
                                                    <span class="text-xof">XOF</span></span>
                                                <div class="stat-detail">
                                                    <i class="bi bi-people-fill"></i>
                                                    {{ $plan['nombre_membres_cotisants'] > 1 ? $plan['nombre_membres_cotisants'] . ' membres' : $plan['nombre_membres_cotisants'] . ' membre' }}
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12">
                                            <div class="alert alert-info">
                                                <i class="bi bi-info-circle"></i> Aucune cotisation trouvée pour ce
                                                type de
                                                plan.
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        {{-- <!-- NOUVELLE SECTION : CARTES col-md-3 -->
        <div class="section-header">INDICATEURS RAPIDES (COL-MD-3)</div>
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="stat-card-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="mini-card-title">Membres à jour</div>
                            <div class="fs-4 fw-bold text-primary">258</div>
                        </div>
                        <i class="bi bi-check-all fs-1 text-primary opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="mini-card-title">Retards (Avril N)</div>
                            <div class="fs-4 fw-bold text-danger">84</div>
                        </div>
                        <i class="bi bi-calendar-x fs-1 text-danger opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="mini-card-title">Budget Prévu</div>
                            <div class="fs-5 fw-bold text-dark">15.2M FCFA</div>
                        </div>
                        <i class="bi bi-piggy-bank fs-1 text-secondary opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="mini-card-title">Performance</div>
                            <div class="fs-4 fw-bold text-success">92%</div>
                        </div>
                        <i class="bi bi-lightning-charge fs-1 text-success opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- UTILITAIRES -->
        <div class="utility-box d-flex align-items-center">
            <span class="small text-muted me-3"><b>OUTILS :</b></span>
            <div class="btn-group">
                <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-printer"></i> Imprimer</button>
                <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-file-earmark-excel"></i> Excel</button>
                <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-envelope-at"></i> Relances</button>
                <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-gear"></i> Config.</button>
            </div>
        </div> --}}
    </div>
@stop

@section('css')

    @include('voyager::dms-cotisation-membres.partials.style')

@stop

@section('javascript')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/series-label.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script src="https://code.highcharts.com/themes/adaptive.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->


    <script>
        // Initialiser le graphique Highcharts
        function initChart() {
            Highcharts.chart('recouvrementChart', {
                title: {
                    text: "{{ $titreGraphique }}",
                    align: 'left'
                },
                subtitle: {
                    text: 'Coût total mensuel en Millions FCFA (MF)',
                    align: 'left'
                },
                xAxis: {
                    categories: [
                        'Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin',
                        'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'
                    ]
                },
                yAxis: {
                    title: {
                        text: 'Montant (MF)'
                    }
                },
                series: [{
                    name: 'Coût total',
                    data: @json($cotisationsParMois)
                }],
                tooltip: {
                    valueSuffix: ' MF'
                }
            });
        }

        // Initialiser le graphique
        initChart();


        // Rafraîchir le graphique quand on change d'année
        $('#annee').on('change', function() {
            // Vous pouvez ajouter ici un loader pendant le chargement
            $('#container').html(
                '<div class="text-center"><i class="voyager-refresh voyager-refresh-animate"></i> Chargement...</div>'
            );
        });
    </script>
@stop
