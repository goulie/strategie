@extends('voyager::master')

@section('page_title', __('voyager::generic.viewing') . ' ' . $dataType->getTranslatedAttribute('display_name_plural'))

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="{{ $dataType->icon }}"></i> {{ $dataType->getTranslatedAttribute('display_name_plural') }}
        </h1>
        @can('add', app($dataType->model_name))
            <a href="{{ route('voyager.' . $dataType->slug . '.create') }}" class="btn btn-success btn-add-new">
                <i class="voyager-plus"></i> <span>{{ __('voyager::generic.add_new') }}</span>
            </a>
        @endcan

        @can('edit', app($dataType->model_name))
            @if (!empty($dataType->order_column) && !empty($dataType->order_display_column))
                <a href="{{ route('voyager.' . $dataType->slug . '.order') }}" class="btn btn-primary btn-add-new">
                    <i class="voyager-list"></i> <span>{{ __('voyager::bread.order') }}</span>
                </a>
            @endif
        @endcan
        @can('delete', app($dataType->model_name))
            @if ($usesSoftDeletes)
                <input type="checkbox" @if ($showSoftDeleted) checked @endif id="show_soft_deletes"
                    data-toggle="toggle" data-on="{{ __('voyager::bread.soft_deletes_off') }}"
                    data-off="{{ __('voyager::bread.soft_deletes_on') }}">
            @endif
        @endcan
        @foreach ($actions as $action)
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
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        @if ($isServerSide)
                            <form method="get" class="form-search">
                                <div id="search-input">
                                    <div class="col-2">
                                        <select id="search_key" name="key">
                                            @foreach ($searchNames as $key => $name)
                                                <option value="{{ $key }}"
                                                    @if ($search->key == $key || (empty($search->key) && $key == $defaultSearchKey)) selected @endif>{{ $name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-2">
                                        <select id="filter" name="filter">
                                            <option value="contains" @if ($search->filter == 'contains') selected @endif>
                                                {{ __('voyager::generic.contains') }}</option>
                                            <option value="equals" @if ($search->filter == 'equals') selected @endif>=
                                            </option>
                                        </select>
                                    </div>
                                    <div class="input-group col-md-12">
                                        <input type="text" class="form-control"
                                            placeholder="{{ __('voyager::generic.search') }}" name="s"
                                            value="{{ $search->value }}">
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
                            </form>
                        @endif
                        <div class="table-responsive">
                            <table id="dataTable" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>
                                            Code contrat
                                        </th>
                                        <th>
                                            Collaborateur
                                        </th>
                                        <th>
                                            Type contrat
                                        </th>
                                        <th>
                                            Date d'entrée
                                        </th>
                                        <th>
                                            Date de fin
                                        </th>
                                        <th>
                                            Durée
                                        </th>
                                        <th>
                                            Fiche contrat signée
                                        </th>
                                        <th>
                                            STATUT
                                        </th>

                                        <th class="actions text-right dt-not-orderable">
                                            {{ __('voyager::generic.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dataTypeContent as $data)
                                      
                                        <tr>
                                            <td>
                                                {{ $data->code_contrat }}
                                            </td>

                                            <td>
                                                {{ $data->personel?->Nom }} {{ $data->personel?->Prenoms }} - <span
                                                    class="label label-info">{{ $data->personel?->Matricule }}</span>
                                            </td>
                                            <td>
                                                <span class="label label-dark"> {{ $data->type_contrat }}</span>
                                            </td>
                                            <td>
                                                <span style="font-weight: bold">
                                                    {{ \Carbon\Carbon::parse($data->date_debut)->locale('fr')->isoFormat('LL') }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($data->date_fin != null)
                                                    <span style="font-weight: bold;color: red">
                                                    @else
                                                        <span style="font-weight: bold;color: green">
                                                @endif
                                                {{ $data->date_fin == null ? 'Durée Indéterminé' : \Carbon\Carbon::parse($data->date_fin)->locale('fr')->isoFormat('LL') }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ $data->duree == null ? 'Durée indéterminée' : $data->duree . ' Mois' }}
                                            </td>
                                            <td>

                                                @php
                                                    $downloadLink = null;
                                                    $originalName = null;

                                                    if (!empty($data->contrat_file)) {
                                                        $files = json_decode($data->contrat_file, true);

                                                        // Cas : fichier multiple (Voyager par défaut)
                                                        if (is_array($files) && isset($files[0]['download_link'])) {
                                                            $downloadLink = $files[0]['download_link'];
                                                            $originalName = $files[0]['original_name'] ?? 'contrat.pdf';
                                                        }

                                                        // Cas : fichier simple
                                                        if (is_string($files)) {
                                                            $downloadLink = $files;
                                                            $originalName = basename($files);
                                                        }
                                                    }
                                                @endphp

                                                @if ($downloadLink)
                                                    <a href="{{ '/gouvernances/public/storage/' . $downloadLink }}"
                                                        target="_blank" class="btn-sm btn-block btn-success pull-right"
                                                        download>
                                                        <i class="bi bi-cloud-download-fill"></i> Télécharger
                                                    </a>
                                                @else
                                                    <span class="label label-danger">
                                                        Aucun contrat signé disponible
                                                    </span>
                                                @endif

                                            </td>
                                            <td>
                                                @if ($data->statut == App\Models\RhContrat::STATUT_ACTIF)
                                                    <span class="label label-success">
                                                        {{ $data->statut }}
                                                    </span>
                                                @else
                                                    <span class="label label-danger">
                                                        {{ $data->statut }}
                                                    </span>
                                                @endif

                                            </td>
                                            <td>
                                                @if ($data->statut == App\Models\RhContrat::STATUT_ACTIF)
                                                    <a class="btn-sm btn-danger pull-right delete"
                                                        data-id="{{ $data->id }}" id="delete-{{ $data->id }}">
                                                        <i class="voyager-trash"></i> <span
                                                            class="hidden-xs hidden-sm">Supprimer</span>
                                                    </a>

                                                    <a href="{{ route('voyager.' . $dataType->slug . '.edit', $data->id) }}"
                                                        class="btn-sm btn-primary pull-right edit">
                                                        <i class="voyager-edit"></i> <span
                                                            class="hidden-xs hidden-sm">Modifier</span>
                                                    </a>
                                                @endif

                                                <a href="javascript:void(0);"
                                                    class="btn-sm btn-info pull-right btn-show-participant"
                                                    data-id="{{ $data->id }}">
                                                    <i class="voyager-list"></i> <span class="hidden-xs hidden-sm"> Détails

                                                    </span>
                                                </a>


                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if ($isServerSide)
                            <div class="pull-left">
                                <div role="status" class="show-res" aria-live="polite">
                                    {{ trans_choice('voyager::generic.showing_entries', $dataTypeContent->total(), [
                                        'from' => $dataTypeContent->firstItem(),
                                        'to' => $dataTypeContent->lastItem(),
                                        'all' => $dataTypeContent->total(),
                                    ]) }}
                                </div>
                            </div>
                            <div class="pull-right">
                                {{ $dataTypeContent->appends([
                                        's' => $search->value,
                                        'filter' => $search->filter,
                                        'key' => $search->key,
                                        'order_by' => $orderBy,
                                        'sort_order' => $sortOrder,
                                        'showSoftDeleted' => $showSoftDeleted,
                                    ])->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Single delete modal --}}
    <div class="modal modal-danger fade" tabindex="-1" id="delete_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                        aria-label="{{ __('voyager::generic.close') }}"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><i class="voyager-trash"></i> {{ __('voyager::generic.delete_question') }}
                        {{ strtolower($dataType->getTranslatedAttribute('display_name_singular')) }}?</h4>
                </div>
                <div class="modal-footer">
                    <form action="#" id="delete_form" method="POST">
                        {{ method_field('DELETE') }}
                        {{ csrf_field() }}
                        <input type="submit" class="btn btn-danger pull-right delete-confirm"
                            value="{{ __('voyager::generic.delete_confirm') }}">
                    </form>
                    <button type="button" class="btn btn-default pull-right"
                        data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="modal modal-primary fade" tabindex="-1" id="participantModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                        aria-label="{{ __('voyager::generic.close') }}"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">
                        <i class="voyager-list"></i> Détails du contrat
                    </h4>
                </div>

                <div class="modal-body">
                    <div id="participantModalContent"></div>
                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-default pull-right"
                        data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>


@stop

@section('css')
    @if (!$dataType->server_side && config('dashboard.data_tables.responsive'))
        <link rel="stylesheet" href="{{ voyager_asset('lib/css/responsive.dataTables.min.css') }}">
    @endif
@stop

@section('javascript')
    <!-- DataTables -->
    @if (!$dataType->server_side && config('dashboard.data_tables.responsive'))
        <script src="{{ voyager_asset('lib/js/dataTables.responsive.min.js') }}"></script>
    @endif
    <script>
        $(document).ready(function() {
            @if (!$dataType->server_side)
                var table = $('#dataTable').DataTable({!! json_encode(
                    array_merge(
                        [
                            'order' => $orderColumn,
                            'language' => __('voyager::datatable'),
                            'columnDefs' => [['targets' => 'dt-not-orderable', 'searchable' => false, 'orderable' => false]],
                        ],
                        config('voyager.dashboard.data_tables', []),
                    ),
                    true,
                ) !!});
            @else
                $('#search-input select').select2({
                    minimumResultsForSearch: Infinity
                });
            @endif

            @if ($isModelTranslatable)
                $('.side-body').multilingual();
                //Reinitialise the multilingual features when they change tab
                $('#dataTable').on('draw.dt', function() {
                    $('.side-body').data('multilingual').init();
                })
            @endif
            $('.select_all').on('click', function(e) {
                $('input[name="row_id"]').prop('checked', $(this).prop('checked')).trigger('change');
            });
        });


        var deleteFormAction;
        $('td').on('click', '.delete', function(e) {
            $('#delete_form')[0].action = '{{ route('voyager.' . $dataType->slug . '.destroy', '__id') }}'.replace(
                '__id', $(this).data('id'));
            $('#delete_modal').modal('show');
        });

        @if ($usesSoftDeletes)
            @php
                $params = [
                    's' => $search->value,
                    'filter' => $search->filter,
                    'key' => $search->key,
                    'order_by' => $orderBy,
                    'sort_order' => $sortOrder,
                ];
            @endphp
            $(function() {
                $('#show_soft_deletes').change(function() {
                    if ($(this).prop('checked')) {
                        $('#dataTable').before(
                            '<a id="redir" href="{{ route('voyager.' . $dataType->slug . '.index', array_merge($params, ['showSoftDeleted' => 1]), true) }}"></a>'
                        );
                    } else {
                        $('#dataTable').before(
                            '<a id="redir" href="{{ route('voyager.' . $dataType->slug . '.index', array_merge($params, ['showSoftDeleted' => 0]), true) }}"></a>'
                        );
                    }

                    $('#redir')[0].click();
                })
            })
        @endif
        $('input[name="row_id"]').on('change', function() {
            var ids = [];
            $('input[name="row_id"]').each(function() {
                if ($(this).is(':checked')) {
                    ids.push($(this).val());
                }
            });
            $('.selected_ids').val(ids);
        });
    </script>

    <script>
        $(document).on('click', '.btn-show-participant', function() {

            let participantId = $(this).data('id');

            $('#participantModal').modal('show');
            $('#participantModalContent').html(
                '<div class="text-center"><span class="spinner-border"></span></div>');

            $.ajax({
                url: '/gouvernances/rh/' + participantId + '/contrats-details',
                type: 'GET',
                success: function(response) {
                    $('#participantModalContent').html(response);
                },
                error: function() {
                    $('#participantModalContent').html(
                        '<p class="text-danger">Erreur de chargement</p>');
                }
            });
        });
    </script>

@stop
