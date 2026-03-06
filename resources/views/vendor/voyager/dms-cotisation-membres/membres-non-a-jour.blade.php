@extends('voyager::master')

@section('page_title', 'Membres Non à Jour')

@section('content')
    <!-- Dans Voyager ou votre dashboard -->
    <div class="card">
        <div class="card-header" style="background-color: #dc3545; color: white;">
            <h5 class="mb-0">
                <i class="voyager-warning"></i> Membres Non à Jour
            </h5>
        </div>
        <div class="card-body">
            <form id="formNonAJour" method="GET" action="{{ route('membres.non-a-jour') }}">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="annee">Année</label>
                            <select name="annee" id="annee" class="form-control">
                                @for ($i = date('Y') - 2; $i <= date('Y') + 1; $i++)
                                    <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>
                                        {{ $i }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="retard_min">Retard minimum (jours)</label>
                            <input type="number" name="retard_min" id="retard_min" class="form-control" value="0"
                                min="0">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="format">Format d'export</label>
                            <select name="format" id="format" class="form-control">
                                <option value="">Afficher à l'écran</option>
                                <option value="excel">Excel</option>
                                <option value="csv">CSV</option>
                                <option value="pdf">PDF</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <button type="submit" class="btn btn-danger">
                        <i class="voyager-search"></i> Rechercher
                    </button>
                    <button type="button" class="btn btn-success" onclick="exportNonAJour()">
                        <i class="voyager-download"></i> Exporter
                    </button>
                </div>
            </form>

            @if (isset($membres) && $membres->count() > 0)
                <div class="mt-4">
                    <div class="alert alert-warning">
                        <i class="voyager-info-circled"></i>
                        <strong>{{ $totalNonAJour }}</strong> membres sur {{ $totalMembres }} ne sont pas à jour pour
                        {{ $annee }}
                    </div>

                    <table class="table table-hover">
                        <!-- Tableau d'affichage -->
                    </table>
                </div>
            @endif
        </div>
    </div>


@stop

@section('css')

@stop


@section('javascript')
    <script>
        function exportNonAJour() {
            const form = $('#formNonAJour');
            const format = $('#format').val() || 'excel';

            // Changer la méthode pour POST si besoin
            form.attr('action', '{{ route('membres.non-a-jour.export') }}');
            form.attr('method', 'POST');

            // Ajouter le token CSRF
            if (!$('input[name="_token"]', form).length) {
                form.append('<input type="hidden" name="_token" value="{{ csrf_token() }}">');
            }

            form.submit();

            // Remettre en GET pour l'affichage
            setTimeout(() => {
                form.attr('action', '{{ route('membres.non-a-jour') }}');
                form.attr('method', 'GET');
                $('input[name="_token"]', form).remove();
            }, 100);
        }
    </script>
@stop
