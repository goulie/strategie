@extends('voyager::master')

@section('page_title', __('voyager::generic.viewing') . ' ' . __('Membres'))

@section('css')
    <!-- Icônes Bootstrap -->
    <link rel="stylesheet"
        href="https://www.google.com/search?q=https://cdn.jsdelivr.net/npm/bootstrap-icons%401.11.0/font/bootstrap-icons.css">
    <!-- DataTables Bootstrap 3 Integration -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap.min.css">

    <style>
        :root {
            --access-blue: #2b579a;
            --access-dark: #1a365d;
            --access-border: #d1d1d1;
            --access-light-blue: #eef3fb;
            --success-color: #27ae60;
            --danger-color: #e74c3c;
        }

        /* Fenêtre principale style Application */
        .access-container {
            background: #fff;
            border: 1px solid var(--access-border);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-top: 15px;
        }

        /* Ruban Supérieur (Ribbon) */
        .access-header {
            background-color: var(--access-blue);
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .access-header h1 {
            font-size: 18px;
            margin: 0;
            font-weight: 600;
            color: #fff;
        }

        /* Barre d'outils et de navigation */
        .access-nav-bar {
            background: #f3f3f3;
            border-bottom: 1px solid var(--access-border);
            padding: 8px 20px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        /* KPI Bar (Résumé des montants) */
        .access-kpi-bar {
            background: var(--access-light-blue);
            border-bottom: 2px solid var(--access-blue);
            padding: 12px 20px;
            display: flex;
            gap: 40px;
        }

        .kpi-box {
            display: flex;
            flex-direction: column;
        }

        .kpi-label {
            font-size: 11px;
            text-transform: uppercase;
            color: #555;
            font-weight: bold;
        }

        .kpi-value {
            font-size: 15px !important;
            font-weight: 700;
            color: var(--access-dark);
        }

        /* Styling de la Table DataGrid */
        .table-access thead th {
            background: #e9ecef;
            color: var(--access-dark);
            font-size: 11px;
            text-transform: uppercase;
            border-bottom: 1px solid var(--access-border) !important;
            border-right: 1px solid var(--access-border);
            vertical-align: middle !important;
        }

        .table-access tbody td {
            font-size: 12px;
            border-right: 1px solid #f0f0f0;
            vertical-align: middle !important;
        }

        .table-access tr:nth-child(even) {
            background: #f9f9f9;
        }

        .table-access tr:hover {
            background: #f0f4f8 !important;
        }

        /* Filtres sous colonnes */
        .column-filter {
            width: 100%;
            padding: 4px;
            font-size: 11px;
            border: 1px solid #ccc;
            margin-top: 5px;
            font-weight: normal;
        }

        /* Badges de statut */
        .access-badge {
            border-radius: 0;
            font-size: 10px;
            font-weight: bold;
            padding: 3px 8px;
            display: inline-block;
        }

        .bg-uptodate {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .bg-pending {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Boutons personnalisés */
        .btn-access-sm {
            border-radius: 0;
            font-size: 11px;
            font-weight: bold;
            padding: 5px 10px;
            text-transform: uppercase;
        }

        /* DataTables Custom UI */
        .dt-buttons {
            margin-bottom: 0 !important;
        }

        .dataTables_info {
            font-size: 11px;
            color: #777;
        }
    </style>


@stop

@section('content')
    <div class="page-content container-fluid">
        @include('voyager::alerts')

        <div class="access-container">
            <!-- HEADER / RIBBON -->
            <div class="access-header">
                <h1>
                    <i class="bi bi-people-fill"></i>
                    {{ $typePlanLibelle .' A JOUR' ?? 'RÉPERTOIRE DES MEMBRES' }}
                    @if (isset($annee)) 
                        <span style="opacity: 0.8;"> | ANNÉE {{ $annee }}</span>
                    @endif
                </h1>
                <div class="header-actions">
                    <button id="btnExport" class="btn btn-success btn-sm btn-access-sm">
                        <i class="bi bi-file-earmark-excel"></i> EXCEL
                    </button>
                    <a href="{{ route('voyager.dms-membres.create') }}" class="btn btn-primary btn-sm btn-access-sm"
                        style="background:#1d3d6d; border:none;">
                        <i class="bi bi-plus-circle"></i> NOUVEAU
                    </a>
                </div>
            </div>

            <!-- NAVIGATION BAR -->
            <div class="access-nav-bar">
                <a href="{{ route('voyager.dms-membres.index') }}" class="btn btn-default btn-xs">
                    <i class="bi bi-house"></i> TOUS LES MEMBRES
                </a>
                @if (isset($typePlan))
                    <a href="{{ route('membres.filter.by.plan', [$typePlan, $annee]) }}" class="btn btn-default btn-xs">
                        <i class="bi bi-filter"></i> {{ $typePlanLibelle }}
                    </a>
                @endif
                @if (isset($plan))
                    <span class="label label-info" style="border-radius:0; padding:5px 10px;">
                        <i class="bi bi-bookmark-fill"></i> PLAN : {{ $plan->title_plan }}
                    </span>
                    @else
                    <span class="label label-info" style="border-radius:0; padding:5px 10px;">
                        <i class="bi bi-bookmark-fill"></i> PLAN : Tous les plans
                    </span>
                @endif
            </div>

            <!-- KPI SUMMARY BAR -->
            <div class="access-kpi-bar">
                <div class="kpi-box">
                    <span class="kpi-label">Membres Affichés</span>
                    <span class="kpi-value text-primary">{{ $membres->count() }}</span>
                </div>
                <div class="kpi-box">
                    <span class="kpi-label">Total Cotisé (Période)</span>
                    <span class="kpi-value" style="color:var(--success-color)">
                        {{ number_format($montantTotal ?? 0, 0, ',', ' ') }} <small>XOF</small>
                    </span>
                </div>
                @if (isset($plan))
                    <div class="kpi-box">
                        <span class="kpi-label">Type de Plan</span>
                        <span class="kpi-value text-info">{{ $plan->title_plan ?? 'N/A' }}</span>
                    </div>
                @else
                    <div class="kpi-box">
                        <span class="kpi-label">Type de Plan</span>
                        <span class="kpi-value text-info">Tous les plans</span>
                    </div>
                @endif
            </div>

            <!-- TABLE CONTENT -->
            <div class="panel-body" style="padding:0;">
                <div class="table-responsive">
                    <table id="membres-table" class="table table-access table-hover">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Nom / Organisation</th>
                                <th>Email</th>
                                <th>Plan</th>
                                <th>Type de Plan</th>
                                <th>Pays</th>
                                <th>Adhésion</th>
                                <th class="text-center">Statut {{ $annee ?? date('Y') }}</th>
                                <th class="text-right">Montant</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($membres as $membre)
                                @php
                                    $cotisation = $membre->getCotisationPourAnnee($annee ?? date('Y'));
                                    $estAJour = $membre->estAJourPourAnnee($annee ?? date('Y'));
                                @endphp
                                <tr>
                                    <td class="font-bold">{{ $membre->code_membre }}</td>
                                    <td>
                                        <strong>{{ $membre->libelle_membre }}</strong>
                                        @if ($membre->organisation)
                                            <div class="text-muted" style="font-size:10px;">{{ $membre->organisation }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ $membre->email }}</td>
                                    <td>{{ $membre->plan_adhesion?->title_plan ?? 'N/A'}}</td>
                                    <td><span
                                            class="label label-default">{{ $membre->plan_adhesion?->type_plan_adhesion ?? 'N/A' }}</span>
                                    </td>
                                    <td>{{ $membre->pays->libelle_pays ?? 'N/A' }}</td>
                                    <td>{{ $membre->date_adhesion ? \Carbon\Carbon::parse($membre->date_adhesion)->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="text-center">
                                        @if ($estAJour)
                                            <span class="access-badge bg-uptodate">À JOUR</span>
                                        @else
                                            <span class="access-badge bg-pending">NON PAYÉ</span>
                                        @endif
                                    </td>
                                    <td class="text-right font-bold">
                                        {{ number_format($cotisation->montant ?? 0, 0, ',', ' ')  }} XOF
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- STATUS BAR (FOOTER) -->
            <div class="panel-footer"
                style="background:#e9ecef; border:none; font-size:10px; font-weight:bold; color:#666; padding:5px 20px;">
                <i class="bi bi-hdd-network-fill"></i> BASE DE DONNÉES CONNECTÉE | {{ strtoupper(date('d M Y - H:i')) }}
            </div>
        </div>
    </div>


@stop

@push('javascript')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialisation de DataTable avec toutes les options
            var table = $('#membres-table').DataTable({
                dom: "Brtf<'row'<'col-sm-5'i><'col-sm-7'p>>", // Ajout de B pour les boutons
                pageLength: 20,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json',
                    buttons: {
                        excel: 'Excel',
                        pdf: 'PDF',
                        copy: 'Copier',
                        print: 'Imprimer'
                    }
                },
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Liste_Membres_{{ $annee ?? date('Y') }}',
                        exportOptions: {
                            columns: ':visible',
                            format: {
                                body: function(data, row, column, node) {
                                    // Nettoyer les données pour l'export
                                    return data.replace(/<[^>]*>/g, '').trim();
                                }
                            }
                        },
                        text: '<i class="voyager-file-text"></i> Excel',
                        className: 'btn btn-sm btn-success'
                    },
                    {
                        extend: 'pdf',
                        title: 'Liste_Membres_{{ $annee ?? date('Y') }}',
                        exportOptions: {
                            columns: ':visible',
                            format: {
                                body: function(data, row, column, node) {
                                    return data.replace(/<[^>]*>/g, '').trim();
                                }
                            }
                        },
                        text: '<i class="voyager-file-text"></i> PDF',
                        className: 'btn btn-sm btn-danger'
                    },
                    {
                        extend: 'copy',
                        title: 'Liste_Membres_{{ $annee ?? date('Y') }}',
                        exportOptions: {
                            columns: ':visible',
                            format: {
                                body: function(data, row, column, node) {
                                    return data.replace(/<[^>]*>/g, '').trim();
                                }
                            }
                        },
                        text: '<i class="voyager-documentation"></i> Copier',
                        className: 'btn btn-sm btn-primary'
                    },
                    {
                        extend: 'print',
                        title: 'Liste des Membres - {{ $annee ?? date('Y') }}',
                        exportOptions: {
                            columns: ':visible',
                            format: {
                                body: function(data, row, column, node) {
                                    return data.replace(/<[^>]*>/g, '').trim();
                                }
                            }
                        },
                        text: '<i class="voyager-printer"></i> Imprimer',
                        className: 'btn btn-sm btn-info'
                    },
                    {
                        text: '<i class="voyager-params"></i> Colonnes',
                        className: 'btn btn-sm btn-warning',
                        action: function(e, dt, node, config) {
                            dt.columns().visible(!dt.columns().visible());
                        }
                    }
                ],
                order: [[7, 'desc']], // Tri par défaut sur la colonne Date d'adhésion (index 7)
                orderMulti: true, // Permet le tri multi-colonnes (Shift + clic)
                ordering: true, // Active le tri
                orderCellsTop: true, // Met les contrôles de tri en haut
                pagingType: "full_numbers", // Pagination complète avec premiers/derniers
                autoWidth: false,
                stateSave: true, // Sauvegarde l'état du tableau (tri, pagination, etc.)
                stateDuration: 60 * 60 * 24 * 7, // Sauvegarde pendant 7 jours
                responsive: true,
                columnDefs: [
                    {
                        targets: [-1], // Dernière colonne (Actions)
                        orderable: false, // Désactive le tri sur la colonne Actions
                        searchable: false // Désactive la recherche sur la colonne Actions
                    },
                    {
                        targets: '_all', // Toutes les colonnes
                        orderable: true, // Active le tri sur toutes les colonnes sauf celles spécifiées
                    }
                ],
                // Personnalisation du comportement de tri
                drawCallback: function(settings) {
                    // Ajouter des classes aux en-têtes triés
                    var api = this.api();
                    api.columns().every(function() {
                        var column = this;
                        var header = $(column.header());
                        if (column.order() !== null) {
                            header.addClass('sorting-column-active');
                        } else {
                            header.removeClass('sorting-column-active');
                        }
                    });
                }
            });
    
            // Lier le bouton export externe à l'action DataTable
            $('#btnExport').on('click', function() {
                table.buttons(0).trigger(); // Déclenche le premier bouton (Excel)
            });
    
            // Lier le bouton d'export personnalisé
            $('#btnExportExcel').on('click', function() {
                table.buttons(0).trigger();
            });
    
            $('#btnExportPDF').on('click', function() {
                table.buttons(1).trigger();
            });
    
            // Ajouter les filtres de colonnes style Access
            $('#membres-table thead tr:first-child th').each(function(index) {
                var title = $(this).text().trim();
                
                // Ne pas ajouter de filtre pour la colonne Actions
                if (index !== $('#membres-table thead th').length - 1) {
                    var filterInput = $('<br><input type="text" class="column-filter form-control input-sm" placeholder="🔍 Filtrer ' + title + '" />');
                    $(this).append(filterInput);
                    
                    // Empêcher le tri quand on clique sur l'input
                    filterInput.on('click', function(e) {
                        e.stopPropagation();
                    });
                }
            });
    
            // Logique de filtrage avec délai pour éviter trop de requêtes
            var searchTimeout;
            table.columns().every(function() {
                var that = this;
                $('input', this.header()).on('keyup change', function() {
                    clearTimeout(searchTimeout);
                    var value = this.value;
                    
                    searchTimeout = setTimeout(function() {
                        if (that.search() !== value) {
                            that.search(value).draw();
                        }
                    }, 300); // Délai de 300ms
                });
            });
    
            // Indicateurs de tri visuels
            $('#membres-table thead th').on('click', function() {
                var columnIdx = $(this).index();
                var column = table.column(columnIdx);
                
                // Retirer la classe active de toutes les colonnes
                $('#membres-table thead th').removeClass('sorting-asc sorting-desc');
                
                // Ajouter la classe appropriée
                if (column.order()[0] === 'asc') {
                    $(this).addClass('sorting-asc');
                } else if (column.order()[0] === 'desc') {
                    $(this).addClass('sorting-desc');
                }
            });
    
            // Bouton pour réinitialiser tous les filtres
            $('#btnResetFilters').on('click', function() {
                table.columns().every(function() {
                    var column = this;
                    $('input', column.header()).val('');
                    column.search('').draw();
                });
            });
    
            // Gestionnaire pour le tri multi-colonnes
            $('#membres-table thead th').on('keydown', function(e) {
                if (e.key === 'Shift') {
                    $(this).addClass('multi-sort');
                }
            }).on('keyup', function(e) {
                if (e.key === 'Shift') {
                    $(this).removeClass('multi-sort');
                }
            });
    
            // Sauvegarder l'état du tri
            table.on('order', function(e, settings) {
                var order = table.order();
                console.log('Tri actuel:', order); // Pour déboguer
            });
        });
    </script>
@endpush
