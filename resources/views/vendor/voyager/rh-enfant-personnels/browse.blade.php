@extends('voyager::master')

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .table-actions {
            white-space: nowrap;
            width: 1%;
        }
        .badge-age {
            font-size: 12px;
            font-weight: bold;
            padding: 4px 8px;
            border-radius: 12px;
        }
        .badge-age-child {
            background-color: #e3f2fd;
            color: #1565c0;
        }
        .badge-age-teen {
            background-color: #fff3e0;
            color: #ef6c00;
        }
        .badge-age-adult {
            background-color: #e8f5e9;
            color: #2e7d32;
        }
        .personnel-info {
            font-size: 12px;
            color: #666;
            margin-top: 3px;
        }
        .status-badge {
            font-size: 11px;
            padding: 3px 8px;
            border-radius: 10px;
        }
        .status-active {
            background-color: #d4edda;
            color: #155724;
        }
        .status-inactive {
            background-color: #f8d7da;
            color: #721c24;
        }
        .image-thumbnail {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #f0f0f0;
        }
        .age-column {
            width: 80px;
            text-align: center;
        }
        .sex-column {
            width: 80px;
            text-align: center;
        }
        .status-column {
            width: 90px;
            text-align: center;
        }
        .image-column {
            width: 60px;
            text-align: center;
        }
        .filters-row {
            background: #f8f9fa;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }
        .filter-group {
            margin-bottom: 10px;
        }
        .filter-label {
            font-weight: bold;
            margin-bottom: 5px;
            color: #495057;
        }
        .export-buttons {
            margin-top: 20px;
        }
        .summary-card {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .summary-title {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 5px;
        }
        .summary-value {
            font-size: 24px;
            font-weight: bold;
            color: #343a40;
        }
        .summary-icon {
            font-size: 30px;
            color: #3490dc;
            margin-bottom: 10px;
        }
        .quick-actions {
            margin-top: 15px;
        }
        .age-filter {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .age-filter input {
            width: 80px;
        }
    </style>
@stop

@section('page_title', __('voyager::generic.viewing').' '.$dataType->getTranslatedAttribute('display_name_plural'))

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="{{ $dataType->icon }}"></i> {{ $dataType->getTranslatedAttribute('display_name_plural') }}
        </h1>
        @can('add', app($dataType->model_name))
            <a href="{{ route('voyager.'.$dataType->slug.'.create') }}" class="btn btn-success btn-add-new">
                <i class="voyager-plus"></i> <span>{{ __('voyager::generic.add_new') }}</span>
            </a>
        @endcan
        @can('delete', app($dataType->model_name))
            @include('voyager::partials.bulk-delete')
        @endcan
        @can('edit', app($dataType->model_name))
            @if(!empty($dataType->order_column) && !empty($dataType->order_display_column))
                <a href="{{ route('voyager.'.$dataType->slug.'.order') }}" class="btn btn-primary btn-add-new">
                    <i class="voyager-list"></i> <span>{{ __('voyager::bread.order') }}</span>
                </a>
            @endif
        @endcan
        @can('delete', app($dataType->model_name))
            @if($usesSoftDeletes)
                <input type="checkbox" @if ($showSoftDeleted) checked @endif id="show_soft_deletes" data-toggle="toggle" data-on="{{ __('voyager::bread.soft_deletes_off') }}" data-off="{{ __('voyager::bread.soft_deletes_on') }}">
            @endif
        @endcan
        @foreach($actions as $action)
            @if (method_exists($action, 'massAction'))
                @include('voyager::bread.partials.actions', ['action' => $action, 'data' => null])
            @endif
        @endforeach
        @include('voyager::multilingual.language-selector')
    </div>
@stop

@section('content')
    <div class="page-content browse container-fluid">
        @include('voyager::alerts')
        
        <!-- Filtres avancés -->
        

        <!-- Résumé statistique -->
        @php
            $totalEnfants = $dataTypeContent->count();
            $enfantsActifs = \App\Models\RhEnfantPersonnel::where('status', 'Actif')->count();
            $enfantsMasculin = \App\Models\RhEnfantPersonnel::where('sexe', 'Masculin')->count();
            $enfantsFeminin = \App\Models\RhEnfantPersonnel::where('sexe', 'Féminin')->count();
            
            // Calculer la répartition par âge
            $enfantsMoins5ans = \App\Models\RhEnfantPersonnel::whereRaw('TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) < 5')->count();
            $enfants5a12ans = \App\Models\RhEnfantPersonnel::whereRaw('TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) BETWEEN 5 AND 12')->count();
            $enfants13a18ans = \App\Models\RhEnfantPersonnel::whereRaw('TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) BETWEEN 13 AND 18')->count();
            $enfantsPlus18ans = \App\Models\RhEnfantPersonnel::whereRaw('TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) > 18')->count();
        @endphp

        <div class="row">
            <div class="col-md-3">
                <div class="summary-card">
                    <div class="summary-icon">
                        <i class="voyager-people"></i>
                    </div>
                    <div class="summary-title">Total des enfants</div>
                    <div class="summary-value">{{ $totalEnfants }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-card">
                    <div class="summary-icon">
                        <i class="voyager-check-circle" style="color: #28a745;"></i>
                    </div>
                    <div class="summary-title">Enfants actifs</div>
                    <div class="summary-value">{{ $enfantsActifs }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-card">
                    <div class="summary-icon">
                        <i class="voyager-check-circle" style="color: #28a745;"></i>
                    </div>
                    <div class="summary-title">Garçons</div>
                    <div class="summary-value">{{ $enfantsMasculin }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-card">
                    <div class="summary-icon">
                        <i class="voyager-check-circle" style="color: #28a745;"></i>
                    </div>
                    <div class="summary-title">Filles</div>
                    <div class="summary-value">{{ $enfantsFeminin }}</div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        @if ($isServerSide)
                            <form method="get" class="form-search">
                                <div id="search-input">
                                    <div class="col-2">
                                        <select id="search_key" name="key">
                                            @foreach($searchNames as $key => $name)
                                                <option value="{{ $key }}" @if($search->key == $key || (empty($search->key) && $key == $defaultSearchKey)) selected @endif>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-2">
                                        <select id="filter" name="filter">
                                            <option value="contains" @if($search->filter == "contains") selected @endif>{{ __('voyager::generic.contains') }}</option>
                                            <option value="equals" @if($search->filter == "equals") selected @endif>=</option>
                                        </select>
                                    </div>
                                    <div class="input-group col-md-12">
                                        <input type="text" class="form-control" placeholder="{{ __('voyager::generic.search') }}" name="s" value="{{ $search->value }}">
                                        <span class="input-group-btn">
                                            <button class="btn btn-info btn-lg" type="submit">
                                                <i class="voyager-search"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                                @if (Request::has('sort_order') && Request::has('order_by'))
                                    <input type="hidden" name="sort_order" value="{{ Request::get('sort_order') }}">
                                    <input type="hidden" name="order_by" value="{{ Request::get('order_by') }}">
                                @endif
                                @if(request()->has('sexe'))
                                    <input type="hidden" name="sexe" value="{{ request('sexe') }}">
                                @endif
                                @if(request()->has('status'))
                                    <input type="hidden" name="status" value="{{ request('status') }}">
                                @endif
                                @if(request()->has('age_min'))
                                    <input type="hidden" name="age_min" value="{{ request('age_min') }}">
                                @endif
                                @if(request()->has('age_max'))
                                    <input type="hidden" name="age_max" value="{{ request('age_max') }}">
                                @endif
                            </form>
                        @endif
                        
                        <!-- Export buttons -->
                        <div class="export-buttons">
                            {{-- <a href="{{ route('voyager.'.$dataType->slug.'.export', array_merge(request()->all(), ['format' => 'pdf'])) }}" 
                               class="btn btn-sm btn-danger" target="_blank">
                                <i class="voyager-file-text"></i> Exporter en PDF
                            </a>
                            <a href="{{ route('voyager.'.$dataType->slug.'.export', array_merge(request()->all(), ['format' => 'excel'])) }}" 
                               class="btn btn-sm btn-success" target="_blank">
                                <i class="voyager-data"></i> Exporter en Excel
                            </a> --}}
                        </div>

                        <div class="table-responsive">
                            <table id="dataTable" class="table table-hover">
                                <thead>
                                    <tr>
                                        @if($showCheckboxColumn)
                                            <th class="dt-not-orderable">
                                                <input type="checkbox" class="select_all">
                                            </th>
                                        @endif
                                        <th class="image-column">Photo</th>
                                        <th>Nom complet</th>
                                        <th>Personnel parent</th>
                                        <th>Date naissance</th>
                                        <th class="age-column">Âge</th>
                                        <th class="sex-column">Sexe</th>
                                        <th class="status-column">Statut</th>
                                        <th class="actions text-right dt-not-orderable">{{ __('voyager::generic.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dataTypeContent as $data)
                                    @php
                                        // Calculer l'âge
                                        $age = null;
                                        $ageDetail = '';
                                        if ($data->date_naissance) {
                                            $naissance = \Carbon\Carbon::parse($data->date_naissance);
                                            $now = \Carbon\Carbon::now();
                                            $age = $naissance->diffInYears($now);
                                            
                                            // Calcul détaillé (ans et mois)
                                            $ans = $naissance->diffInYears($now);
                                            $mois = $naissance->copy()->addYears($ans)->diffInMonths($now);
                                            
                                            if ($ans == 0) {
                                                $ageDetail = $mois . ' mois';
                                            } elseif ($mois == 0) {
                                                $ageDetail = $ans . ' ans';
                                            } else {
                                                $ageDetail = $ans . ' ans et ' . $mois . ' mois';
                                            }
                                        }
                                        
                                        // Déterminer la classe CSS pour l'âge
                                        $ageClass = 'badge-age';
                                        if ($age !== null) {
                                            if ($age < 5) {
                                                $ageClass .= ' badge-age-child';
                                            } elseif ($age >= 5 && $age <= 12) {
                                                $ageClass .= ' badge-age-teen';
                                            } else {
                                                $ageClass .= ' badge-age-adult';
                                            }
                                        }
                                    @endphp
                                    <tr>
                                        @if($showCheckboxColumn)
                                            <td>
                                                <input type="checkbox" name="row_id" id="checkbox_{{ $data->getKey() }}" value="{{ $data->getKey() }}">
                                            </td>
                                        @endif
                                        
                                        <!-- Photo -->
                                        <td>
                                            @if($data->image)
                                            {{ $data->image }}
                                                <img src="{{ Voyager::image($data->image) }}" 
                                                     alt="Photo" 
                                                     class="image-thumbnail"
                                                     data-toggle="tooltip" 
                                                     title="Voir la photo"
                                                     onclick="showImageModal('{{ Voyager::image($data->image) }}', '{{ $data->nom_complet }}')">
                                            @else
                                                <div class="text-center">
                                                    <i class="voyager-person" style="font-size: 24px; color: #ccc;"></i>
                                                </div>
                                            @endif
                                        </td>
                                        
                                        <!-- Nom complet -->
                                        <td>
                                            <strong>{{ $data->nom_complet }}</strong>
                                            @if($data->date_naissance)
                                                <div class="personnel-info">
                                                    <i class="voyager-calendar"></i> 
                                                    Né(e) le {{ \Carbon\Carbon::parse($data->date_naissance)->format('d/m/Y') }}
                                                </div>
                                            @endif
                                        </td>
                                        
                                        <!-- Personnel parent -->
                                        <td>
                                            @if($data->personnel)
                                                <strong>{{ $data->personnel->nom }} {{ $data->personnel->prenoms }}</strong>
                                                <div class="personnel-info">
                                                    <i class="voyager-tag"></i> {{ $data->personnel->matricule }}
                                                    @if($data->personnel->service)
                                                        <br><i class="voyager-office"></i> {{ $data->personnel->service->libelle ?? '' }}
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-muted">Non assigné</span>
                                            @endif
                                        </td>
                                        
                                        <!-- Date de naissance -->
                                        <td>
                                            @if($data->date_naissance)
                                                {{ \Carbon\Carbon::parse($data->date_naissance)->format('d/m/Y') }}
                                            @else
                                                <span class="text-muted">Non spécifiée</span>
                                            @endif
                                        </td>
                                        
                                        <!-- Âge -->
                                        <td class="age-column">
                                            @if($age !== null)
                                                <span class="{{ $ageClass }}" 
                                                      data-toggle="tooltip" 
                                                      title="{{ $ageDetail }}">
                                                    {{ $age }} ans
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        
                                        <!-- Sexe -->
                                        <td class="sex-column">
                                            @if($data->sexe == 'Masculin')
                                                <span class="badge badge-primary">
                                                    <i class="voyager-male"></i> M
                                                </span>
                                            @elseif($data->sexe == 'Féminin')
                                                <span class="badge badge-pink" style="background-color: #e83e8c; color: white;">
                                                    <i class="voyager-female"></i> F
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        
                                        <!-- Statut -->
                                        <td class="status-column">
                                            @if($data->status == 'Actif')
                                                <span class="status-badge status-active">
                                                    <i class="voyager-check-circle"></i> Actif
                                                </span>
                                            @else
                                                <span class="status-badge status-inactive">
                                                    <i class="voyager-x"></i> Inactif
                                                </span>
                                            @endif
                                        </td>
                                        
                                        <!-- Actions -->
                                        <td class="no-sort no-click bread-actions table-actions">
                                            @foreach($actions as $action)
                                                @if (!method_exists($action, 'massAction'))
                                                    @include('voyager::bread.partials.actions', ['action' => $action])
                                                @endif
                                            @endforeach
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if ($isServerSide)
                            <div class="pull-left">
                                <div role="status" class="show-res" aria-live="polite">{{ trans_choice(
                                    'voyager::generic.showing_entries', $dataTypeContent->total(), [
                                        'from' => $dataTypeContent->firstItem(),
                                        'to' => $dataTypeContent->lastItem(),
                                        'all' => $dataTypeContent->total()
                                    ]) }}</div>
                            </div>
                            <div class="pull-right">
                                {{ $dataTypeContent->appends([
                                    's' => $search->value,
                                    'filter' => $search->filter,
                                    'key' => $search->key,
                                    'order_by' => $orderBy,
                                    'sort_order' => $sortOrder,
                                    'showSoftDeleted' => $showSoftDeleted,
                                    'sexe' => request('sexe'),
                                    'status' => request('status'),
                                    'age_min' => request('age_min'),
                                    'age_max' => request('age_max'),
                                ])->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal pour afficher l'image en grand -->
    <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Photo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" alt="" style="max-width: 100%; max-height: 500px;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Single delete modal --}}
    <div class="modal modal-danger fade" tabindex="-1" id="delete_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><i class="voyager-trash"></i> {{ __('voyager::generic.delete_question') }} {{ strtolower($dataType->getTranslatedAttribute('display_name_singular')) }}?</h4>
                </div>
                <div class="modal-footer">
                    <form action="#" id="delete_form" method="POST">
                        {{ method_field('DELETE') }}
                        {{ csrf_field() }}
                        <input type="submit" class="btn btn-danger pull-right delete-confirm" value="{{ __('voyager::generic.delete_confirm') }}">
                    </form>
                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@stop

@section('css')
@if(!$dataType->server_side && config('dashboard.data_tables.responsive'))
    <link rel="stylesheet" href="{{ voyager_asset('lib/css/responsive.dataTables.min.css') }}">
@endif
@stop

@section('javascript')
    <!-- DataTables -->
    @if(!$dataType->server_side && config('dashboard.data_tables.responsive'))
        <script src="{{ voyager_asset('lib/js/dataTables.responsive.min.js') }}"></script>
    @endif
    <script>
        $(document).ready(function () {
            @if (!$dataType->server_side)
    var table = $('#dataTable').DataTable({
        order: @json($orderColumn),
        language: @json(__('voyager::datatable')),
        columnDefs: [
            { targets: [0, 8], searchable: false, orderable: false },
            { targets: 1, orderable: false },
            { targets: [5, 6, 7], orderable: true },
            { targets: '_all', searchable: true, orderable: true }
        ],
        @foreach(config('voyager.dashboard.data_tables', []) as $key => $value)
            '{{ $key }}': @json($value),
        @endforeach
    });
@else
                $('#search-input select').select2({
                    minimumResultsForSearch: Infinity
                });
            @endif

            @if ($isModelTranslatable)
                $('.side-body').multilingual();
                $('#dataTable').on('draw.dt', function(){
                    $('.side-body').data('multilingual').init();
                })
            @endif
            
            // Initialiser les tooltips
            $('[data-toggle="tooltip"]').tooltip();
            
            // Gestion des cases à cocher
            $('.select_all').on('click', function(e) {
                $('input[name="row_id"]').prop('checked', $(this).prop('checked')).trigger('change');
            });

            // Compter les enfants sélectionnés
            $('input[name="row_id"]').on('change', function () {
                var ids = [];
                var count = 0;
                $('input[name="row_id"]').each(function() {
                    if ($(this).is(':checked')) {
                        ids.push($(this).val());
                        count++;
                    }
                });
                $('.selected_ids').val(ids);
                
                // Mettre à jour le texte du bouton de suppression en masse
                if (count > 0) {
                    $('.bulk-delete-btn').text('Supprimer (' + count + ')');
                } else {
                    $('.bulk-delete-btn').text('Supprimer la sélection');
                }
            });
        });

        // Fonction pour afficher l'image en modal
        function showImageModal(imageSrc, title) {
            $('#modalImage').attr('src', imageSrc);
            $('#imageModalLabel').text('Photo de ' + title);
            $('#imageModal').modal('show');
        }

        // Confirmation avant suppression
        var deleteFormAction;
        $('td').on('click', '.delete', function (e) {
            var enfantName = $(this).closest('tr').find('td:nth-child(3) strong').text();
            $('#delete_form')[0].action = '{{ route('voyager.'.$dataType->slug.'.destroy', '__id') }}'.replace('__id', $(this).data('id'));
            $('#delete_modal .modal-title').html('<i class="voyager-trash"></i> Supprimer l\'enfant "' + enfantName + '"?');
            $('#delete_modal').modal('show');
        });

        @if($usesSoftDeletes)
            @php
                $params = [
                    's' => $search->value,
                    'filter' => $search->filter,
                    'key' => $search->key,
                    'order_by' => $orderBy,
                    'sort_order' => $sortOrder,
                    'sexe' => request('sexe'),
                    'status' => request('status'),
                    'age_min' => request('age_min'),
                    'age_max' => request('age_max'),
                ];
            @endphp
            $(function() {
                $('#show_soft_deletes').change(function() {
                    var url = $(this).prop('checked') 
                        ? '{{ route('voyager.'.$dataType->slug.'.index', array_merge($params, ['showSoftDeleted' => 1])) }}'
                        : '{{ route('voyager.'.$dataType->slug.'.index', array_merge($params, ['showSoftDeleted' => 0])) }}';
                    window.location.href = url;
                });
            });
        @endif

        // Exporter les données
        function exportData(format) {
            var form = $('#advanced-filters');
            $('<input>').attr({
                type: 'hidden',
                name: 'export_format',
                value: format
            }).appendTo(form);
            form.attr('action', '#');
            form.attr('target', '_blank');
            form.submit();
            
            // Retirer le champ caché et restaurer l'action
            setTimeout(function() {
                form.find('input[name="export_format"]').remove();
                form.removeAttr('action');
                form.removeAttr('target');
            }, 100);
        }

        // Fonction pour calculer l'âge d'un enfant
        function calculateAge(birthDate) {
            if (!birthDate) return null;
            
            var today = new Date();
            var birth = new Date(birthDate);
            var age = today.getFullYear() - birth.getFullYear();
            var m = today.getMonth() - birth.getMonth();
            
            if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) {
                age--;
            }
            
            return age;
        }
    </script>
@stop