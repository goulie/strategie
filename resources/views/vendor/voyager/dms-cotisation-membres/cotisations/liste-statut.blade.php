@extends('voyager::master')

@section('page_title', $titre)

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="voyager-people"></i> {{ $titre }}
        </h1>
        {{-- <a href="{{ route('voyager.dms-membres.index') }}" class="btn btn-success">
            <i class="voyager-list"></i> Tous les membres
        </a> --}}
        @include('voyager::multilingual.language-selector')
    </div>
@stop

@section('content')
    <div class="page-content browse container-fluid">
        @include('voyager::alerts')
        
        <div class="row">
            <div class="col-md-12">
                
                {{-- Filtres et statistiques --}}
                <div class="row" style="margin-bottom: 20px;">
                    <div class="col-md-12">
                        <div class="panel panel-bordered">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="statistique-mini-card">
                                            <div class="statistique-mini-icon">
                                                <i class="voyager-people"></i>
                                            </div>
                                            <div class="statistique-mini-content">
                                                <h3>{{ number_format($stats['nombre'] ?? $membres->count()) }}</h3>
                                                <p>Nombre de membres</p>
                                            </div>
                                        </div>
                                    </div>
                                    @if(isset($stats['montant']))
                                    <div class="col-md-4">
                                        <div class="statistique-mini-card">
                                            <div class="statistique-mini-icon">
                                                <i class="voyager-dollar"></i>
                                            </div>
                                            <div class="statistique-mini-content">
                                                <h3>{{ number_format($stats['montant'], 0, ',', ' ') }} XOF</h3>
                                                <p>Montant total</p>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    <div class="col-md-4">
                                        <div class="statistique-mini-card">
                                            <div class="statistique-mini-icon">
                                                <i class="voyager-calendar"></i>
                                            </div>
                                            <div class="statistique-mini-content">
                                                <h3>{{ $annee ?? 'Toutes' }}</h3>
                                                <p>Année</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <hr>
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <p class="text-muted">{{ $sousTitre }}</p>
                                    </div>
                                </div>
                                
                                {{-- Filtres rapides --}}
                                <div class="row" style="margin-top: 15px;">
                                    <div class="col-md-3">
                                        <select id="annee-filter" class="form-control">
                                            <option value="">-- Filtrer par année --</option>
                                            @for($y = date('Y'); $y >= 2020; $y--)
                                                <option value="{{ $y }}" {{ isset($annee) && $annee == $y ? 'selected' : '' }}>
                                                    {{ $y }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select id="type-plan-filter" class="form-control">
                                            <option value="">-- Type de plan --</option>
                                            <option value="ACTIF">Membres Actifs</option>
                                            <option value="INDIVIDUEL">Membres Individuels</option>
                                            <option value="AFFILIE">Membres Affiliés</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <button id="btnResetFilters" class="btn btn-default btn-block">
                                            <i class="voyager-refresh"></i> Réinitialiser
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Boutons d'export --}}
                <div class="btn-group" style="margin-bottom: 10px;">
                    <button id="btnExportExcel" class="btn btn-sm btn-success">
                        <i class="voyager-file-text"></i> Export Excel
                    </button>
                    
                    
                </div>
                
                {{-- Table DataTable --}}
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table id="membres-table" class="table table-hover table-striped dataTable">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Nom / Organisation</th>
                                        <th>Email</th>
                                        <th>Plan d'adhésion</th>
                                        <th>Type de plan</th>
                                        <th>Pays</th>
                                        <th>Date d'adhésion</th>
                                        <th>Dernière cotisation</th>
                                        <th>Date échéance</th>
                                        <th>Montant</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($membres as $membre)
                                        @php
                                            // Déterminer la cotisation à afficher
                                            if ($typeStatut == 'AJOUR') {
                                                $cotisation = $membre->cotisations->first();
                                                $estAJour = true;
                                            } elseif ($typeStatut == 'NONAJOUR') {
                                                $cotisation = $membre->derniere_cotisation ?? null;
                                                $estAJour = false;
                                            } else {
                                                $cotisation = $membre->derniere_cotisation ?? null;
                                                $estAJour = false;
                                            }
                                            
                                            $montant = $cotisation ? $cotisation->montant : 0;
                                            $dateEcheance = $cotisation ? $cotisation->date_echeance : null;
                                            
                                            if ($typeStatut == 'AJOUR') {
                                                $statutClass = 'label-success';
                                                $statutText = 'À jour';
                                            } elseif ($typeStatut == 'NONAJOUR') {
                                                $statutClass = 'label-danger';
                                                $statutText = 'Non à jour';
                                            } else {
                                                $statutClass = 'label-warning';
                                                $statutText = 'Expiré';
                                            }
                                        @endphp
                                        <tr>
                                            <td>{{ $membre->code_membre ?? 'N/A' }}</td>
                                            <td>
                                                <strong>{{ $membre->libelle_membre }}</strong>
                                                @if($membre->organisation)
                                                    <br><small class="text-muted">{{ $membre->organisation }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $membre->email ?? 'N/A' }}</td>
                                            <td>{{ $membre->plan_adhesion->title_plan ?? 'N/A' }}</td>
                                            <td>
                                                @if($membre->plan_adhesion)
                                                    <span class="label label-info">{{ $membre->plan_adhesion->type_plan_adhesion }}</span>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>{{ $membre->pays->libelle_pays ?? 'N/A' }}</td>
                                            <td data-order="{{ $membre->date_adhesion }}">
                                                {{ $membre->date_adhesion ? Carbon\Carbon::parse($membre->date_adhesion)->format('d/m/Y') : 'N/A' }}
                                            </td>
                                            <td data-order="{{ $cotisation->date_paiement ?? '' }}">
                                                {{ $cotisation && $cotisation->date_paiement ? Carbon\Carbon::parse($cotisation->date_paiement)->format('d/m/Y') : 'Jamais' }}
                                            </td>
                                            <td data-order="{{ $dateEcheance ?? '' }}">
                                                {{ $dateEcheance ? Carbon\Carbon::parse($dateEcheance)->format('d/m/Y') : 'N/A' }}
                                                @if($dateEcheance && !$estAJour)
                                                    <br><small class="text-danger">Expirée</small>
                                                @endif
                                            </td>
                                            <td data-order="{{ $montant }}">
                                                {{ $montant > 0 ? number_format($montant, 0, ',', ' ') . ' XOF' : '0 XOF' }}
                                            </td>
                                            <td>
                                                <span class="label {{ $statutClass }}">{{ $statutText }}</span>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('voyager.dms-membres.show', $membre->id) }}" 
                                                       class="btn btn-sm btn-info" title="Voir">
                                                        <i class="voyager-eye"></i>
                                                    </a>
                                                    {{-- <a href="{{ route('voyager.dms-membres.edit', $membre->id) }}" 
                                                       class="btn btn-sm btn-warning" title="Modifier">
                                                        <i class="voyager-edit"></i>
                                                    </a> --}}
                                                    {{-- <a href="{{ route('membres.cotisations', $membre->id) }}" 
                                                       class="btn btn-sm btn-success" title="Cotisations">
                                                        <i class="voyager-wallet"></i>
                                                    </a> --}}
                                                    @if($typeStatut == 'NONAJOUR' || $typeStatut == 'EXPIRE')
                                                    {{-- <a href="{{ route('voyager.dms-cotisation-membres.create', ['membre_id' => $membre->id]) }}" 
                                                       class="btn btn-sm btn-primary" title="Enregistrer cotisation">
                                                        <i class="voyager-dollar"></i>
                                                    </a> --}}
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="13" class="text-center">
                                                <div class="alert alert-info">
                                                    <i class="voyager-info-circled"></i> Aucun membre trouvé
                                                </div>
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
@stop

@push('javascript')
    <script>
        $(document).ready(function() {
            
            // Initialisation de DataTable
            var table = $('#membres-table').DataTable({
                dom: "Brtf<'row'<'col-sm-5'i><'col-sm-7'p>>",
                pageLength: 25,
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
                        title: '{{ $titre }}',
                        exportOptions: {
                            columns: [0,1,2,3,4,5,6,7,8,9,10,11], // Toutes sauf Actions
                            format: {
                                body: function(data, row, column, node) {
                                    return data.replace(/<[^>]*>/g, '').trim();
                                }
                            }
                        },
                        text: '<i class="voyager-file-text"></i> Excel',
                        className: 'btn btn-sm btn-success'
                    },
                    {
                        extend: 'pdf',
                        title: '{{ $titre }}',
                        exportOptions: {
                            columns: [0,1,2,3,4,5,6,7,8,9,10,11],
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
                        title: '{{ $titre }}',
                        exportOptions: {
                            columns: [0,1,2,3,4,5,6,7,8,9,10,11],
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
                        title: '{{ $titre }}',
                        exportOptions: {
                            columns: [0,1,2,3,4,5,6,7,8,9,10,11],
                            format: {
                                body: function(data, row, column, node) {
                                    return data.replace(/<[^>]*>/g, '').trim();
                                }
                            }
                        },
                        text: '<i class="voyager-printer"></i> Imprimer',
                        className: 'btn btn-sm btn-info'
                    }
                ],
                order: [[7, 'desc']], // Tri par date d'adhésion
                orderMulti: true,
                ordering: true,
                orderCellsTop: true,
                pagingType: "full_numbers",
                autoWidth: false,
                stateSave: true,
                responsive: true,
                columnDefs: [
                    {
                        targets: [-1], // Dernière colonne (Actions)
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Lier les boutons d'export
            $('#btnExportExcel').on('click', function() {
                table.button(0).trigger();
            });
            
            $('#btnExportPDF').on('click', function() {
                table.button(1).trigger();
            });
            
            $('#btnCopy').on('click', function() {
                table.button(2).trigger();
            });
            
            $('#btnPrint').on('click', function() {
                table.button(3).trigger();
            });

            // Ajouter les filtres de colonnes
            $('#membres-table thead tr:first-child th').each(function(index) {
                var title = $(this).text().trim();
                
                // Ne pas ajouter de filtre pour la colonne Actions
                if (index !== $('#membres-table thead th').length - 1) {
                    var filterInput = $('<br><input type="text" class="column-filter form-control input-sm" placeholder="🔍 Filtrer ' + title + '" />');
                    $(this).append(filterInput);
                    
                    filterInput.on('click', function(e) {
                        e.stopPropagation();
                    });
                }
            });

            // Logique de filtrage
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
                    }, 300);
                });
            });

            // Filtres externes
            $('#annee-filter').on('change', function() {
                var annee = $(this).val();
                if(annee) {
                    var url = new URL(window.location.href);
                    url.pathname = url.pathname.replace(/\/\d+$/, '') + '/' + annee;
                    window.location.href = url.toString();
                }
            });

            // Filtre par type de plan (à implémenter côté client ou serveur)
            $('#type-plan-filter, #ligne-budgetaire-filter').on('change', function() {
                applyFilters();
            });

            function applyFilters() {
                var typePlan = $('#type-plan-filter').val();
                var ligneBudgetaire = $('#ligne-budgetaire-filter').val();
                
                // Filtrage côté client
                $.fn.dataTable.ext.search.push(
                    function(settings, data, dataIndex) {
                        var typePlanCell = data[4]; // Colonne Type de plan
                        var ligneCell = data[5]; // Colonne Ligne budgétaire
                        
                        var typePlanOk = !typePlan || typePlanCell.includes(typePlan);
                        var ligneOk = !ligneBudgetaire || ligneCell.includes($('#ligne-budgetaire-filter option:selected').text());
                        
                        return typePlanOk && ligneOk;
                    }
                );
                
                table.draw();
                
                // Nettoyer
                $.fn.dataTable.ext.search.pop();
            }

            // Réinitialiser les filtres
            $('#btnResetFilters').on('click', function() {
                $('#annee-filter').val('');
                $('#type-plan-filter').val('');
                $('#ligne-budgetaire-filter').val('');
                
                table.columns().every(function() {
                    var column = this;
                    $('input', column.header()).val('');
                    column.search('').draw();
                });
                
                // Recharger si filtre année
                var currentUrl = window.location.href;
                if(currentUrl.includes('/non-a-jour/') || currentUrl.includes('/a-jour/')) {
                    var baseUrl = currentUrl.replace(/\/\d+$/, '');
                    window.location.href = baseUrl;
                }
            });
        });
    </script>
@endpush

@section('css')
    <style>
        .statistique-mini-card {
            display: flex;
            align-items: center;
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .statistique-mini-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #007bff;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 24px;
        }
        
        .statistique-mini-content h3 {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
        }
        
        .statistique-mini-content p {
            margin: 0;
            color: #6c757d;
            font-size: 12px;
        }
        
        .column-filter {
            width: 100%;
            margin-top: 5px;
            font-size: 11px;
            height: 25px;
            padding: 2px 5px;
            border-radius: 3px;
            border: 1px solid #ddd;
        }
        
        .column-filter:focus {
            border-color: #66afe9;
            outline: 0;
            box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, .6);
        }
        
        .label-success {
            background-color: #5cb85c;
            color: white;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 11px;
        }
        
        .label-danger {
            background-color: #d9534f;
            color: white;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 11px;
        }
        
        .label-warning {
            background-color: #f0ad4e;
            color: white;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 11px;
        }
        
        .label-info {
            background-color: #5bc0de;
            color: white;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 11px;
        }
        
        .btn-group-sm .btn {
            margin-right: 2px;
        }
        
        table.dataTable thead .sorting:after,
        table.dataTable thead .sorting_asc:after,
        table.dataTable thead .sorting_desc:after {
            font-family: 'Glyphicons Halflings';
            margin-left: 5px;
        }
        
        table.dataTable thead .sorting:after {
            content: "\e150";
            opacity: 0.3;
        }
        
        table.dataTable thead .sorting_asc:after {
            content: "\e155";
            opacity: 1;
            color: #337ab7;
        }
        
        table.dataTable thead .sorting_desc:after {
            content: "\e156";
            opacity: 1;
            color: #337ab7;
        }
    </style>
@stop