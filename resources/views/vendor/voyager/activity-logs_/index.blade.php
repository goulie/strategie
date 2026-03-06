@extends('voyager::master')

@section('page_title', 'Historique des actions')

@section('content')
    <div class="page-content container-fluid">

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Utilisateur</th>
                    <th>Action</th>
                    <th>Table</th>
                    <th>Détails</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($logs as $log)
                    <tr>
                        <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $log->user->name ?? 'Système' }}</td>
                        <td>
                            <span class="badge badge-{{ $log->action === 'deleted' ? 'danger' : 'primary' }}">
                                {{ strtoupper($log->action) }}
                            </span>
                        </td>
                        <td>{{ $log->table_name }} #{{ $log->record_id }}</td>
                        <td>
                            <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#log{{ $log->id }}">
                                Voir
                            </button>
                        </td>
                    </tr>

                    {{-- Modal --}}
                    <div class="modal fade" id="log{{ $log->id }}">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4>Détails de la modification</h4>
                                </div>
                                <div class="modal-body">
                                    <h5>Avant</h5>
                                    <pre>{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>

                                    <h5>Après</h5>
                                    <pre>{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </tbody>
        </table>

        {{ $logs->links() }}
    </div>
@endsection
