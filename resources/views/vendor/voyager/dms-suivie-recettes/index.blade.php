@extends('voyager::master')

@section('page_title', __('voyager::generic.viewing') . ' ' . $dataType->getTranslatedAttribute('display_name_plural'))

@section('page_header')
    <div class="container-fluid">

    </div>
@stop

@php
    use Carbon\Carbon;
    Carbon::setLocale('fr');

    $mois = [
        1 => 'Janvier',
        2 => 'Février',
        3 => 'Mars',
        4 => 'Avril',
        5 => 'Mai',
        6 => 'Juin',
        7 => 'Juillet',
        8 => 'Août',
        9 => 'Septembre',
        10 => 'Octobre',
        11 => 'Novembre',
        12 => 'Décembre',
    ];
@endphp

@section('content')
    <div class="page-content browse container-fluid">

        <h2 style="text-align: center; color: #000000">LISTE DES RECETTES CLASSIQUES</h2>
        <div class="row text-center">
            <form method="GET" action="{{ route('voyager.dms-module-recettes.index') }}" class="form-inline mb-3">

                <div class="form-group">
                    <label for="year" style="margin-right:10px;">Filtrer l' Année :</label>
                    <select name="year" id="year" class="form-control" onchange="this.form.submit()">
                        @for ($y = now()->year; $y >= now()->year - 3; $y--)
                            <option value="{{ $y }}" {{ request('year', now()->year) == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>

            </form>


        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table id="recettesTable" class="table table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th>Module</th>
                                        <th>Code ligne</th>
                                        <th>Ligne budgétaire</th>
                                        @foreach ($mois as $m)
                                            <th>{{ $m }}</th>
                                        @endforeach
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $row)
                                        @php
                                            $totalLigne = array_sum($row['mois']);
                                        @endphp
                                        <tr>
                                            <td>{{ $row['module'] }}</td>
                                            <td>{{ $row['code_ligne'] }}</td>
                                            <td>{{ $row['libelle_ligne'] }}</td>

                                            @foreach ($row['mois'] as $total)
                                                <td class="text-end">
                                                    {{ $total > 0 ? number_format($total, 0, ',', ' ') : '-' }}
                                                </td>
                                            @endforeach
                                            <td class="text-end fw-bold">
                                                {{ number_format($totalLigne, 0, ',', ' ') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th><input type="text" class="form-control form-control-sm"
                                                placeholder="Filtrer Module" /></th>
                                        <th><input type="text" class="form-control form-control-sm"
                                                placeholder="Filtrer Code" /></th>
                                        <th><input type="text" class="form-control form-control-sm"
                                                placeholder="Filtrer Ligne" /></th>
                                        @foreach ($mois as $key => $m)
                                            <th><input type="text" class="form-control form-control-sm"
                                                    placeholder="Filtrer {{ $m }}" /></th>
                                        @endforeach
                                        <th><input type="text" class="form-control form-control-sm"
                                                placeholder="Filtrer Total" /></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>



@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
    <style>
        .dataTables_wrapper .dataTables_filter {
            float: right;
            margin-bottom: 15px;
        }

        .dataTables_wrapper .dataTables_length {
            float: left;
            margin-bottom: 15px;
        }

        .dataTables_wrapper .dataTables_paginate {
            float: right;
            margin-top: 15px;
        }

        .dt-buttons {
            margin-bottom: 15px;
        }

        tfoot input {
            width: 100% !important;
            box-sizing: border-box;
        }

        .dataTables_wrapper .dataTables_info {
            clear: both;
            float: left;
            padding-top: 0.755em;
        }

        .panel-body {
            overflow-x: auto;
        }

        table.dataTable thead th,
        table.dataTable tfoot th {
            vertical-align: middle;
            text-align: center;
        }
    </style>
@stop

@section('javascript')
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialisation de la DataTable
            var table = $('#recettesTable').DataTable({
                dom: 'Blfrtip',
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100, -1],
                ordering: true,
                searching: true,
                order: [
                    [0, 'asc']
                ],

                // Configuration des boutons
                buttons: [{
                        extend: 'excelHtml5',
                        text: '📥 Export Excel',
                        title: 'Recettes Classiques {{ $selectedYear ?? now()->year }}',
                        filename: 'recettes_classiques_{{ $selectedYear ?? now()->year }}',
                        exportOptions: {
                            columns: ':visible',
                            format: {
                                body: function(data, row, column, node) {
                                    // Formater les nombres pour Excel
                                    if (column >= 3 && column <= 14) { // Colonnes des mois
                                        return data === '-' ? 0 : data.replace(/\s/g, '');
                                    }
                                    return data;
                                }
                            }
                        },
                        customize: function(xlsx) {
                            var sheet = xlsx.xl.worksheets['sheet1.xml'];
                            // Ajouter un filtre automatique sur la première ligne
                            $('row c', sheet).attr('s', '52');
                        }
                    },
                    {
                        extend: 'colvis',
                        text: 'Colonnes',
                        columns: ':gt(2)' // Masquer les colonnes à partir de la 4ème
                    }
                ],

                // Configuration de la langue française
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'
                },

                // Configuration de la pagination
                pagingType: 'full_numbers',

                // Personnalisation de l'affichage
                initComplete: function() {
                    this.api().columns().every(function() {
                        var column = this;
                        var title = $(column.header()).text();

                        // Ajout des filtres individuels
                        if (title !== 'Total' && title !== 'Module' && title !== 'Code ligne' &&
                            title !== 'Ligne budgétaire') {
                            var select = $(
                                    '<select class="form-control form-control-sm"><option value="">Tous</option></select>'
                                )
                                .appendTo($(column.footer()).empty())
                                .on('change', function() {
                                    var val = $.fn.dataTable.util.escapeRegex($(this)
                                        .val());
                                    column.search(val ? '^' + val + '$' : '', true, false)
                                        .draw();
                                });

                            column.data().unique().sort().each(function(d, j) {
                                select.append('<option value="' + d + '">' + d +
                                    '</option>');
                            });
                        }
                    });
                },

                // Configuration responsive
                responsive: true,
                scrollX: true,
                autoWidth: false,

                // Personnalisation des informations affichées
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();

                    // Calcul du total général
                    var totalGeneral = 0;
                    api.column(15, {
                        search: 'applied'
                    }).nodes().each(function(node) {
                        var value = $(node).text().replace(/\s/g, '').replace(',', '');
                        if (!isNaN(value) && value.length !== 0) {
                            totalGeneral += parseFloat(value);
                        }
                    });

                    // Afficher le total général dans le footer
                    $(api.column(15).footer()).html(
                        '<strong>' + totalGeneral.toLocaleString('fr-FR') + '</strong>'
                    );
                }
            });

            // Appliquer les filtres des inputs du footer
            $('#recettesTable tfoot th input').on('keyup change', function() {
                var index = $(this).parent().index();
                table.column(index).search(this.value).draw();
            });

            // Personnalisation de l'apparence des boutons
            $('.dt-buttons').addClass('btn-group');
            $('.dt-button').addClass('btn btn-primary btn-sm');

            // Masquer le champ de recherche global si vous préférez utiliser les filtres individuels
            // $('.dataTables_filter').hide();
        });
    </script>

@stop
