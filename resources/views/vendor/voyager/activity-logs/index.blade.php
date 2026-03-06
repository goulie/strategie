@extends('voyager::master')

@section('page_title', 'Historique des actions')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .page-content {
            padding: 20px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: calc(100vh - 50px);
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 25px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border-left: 5px solid #22b8cf;
        }

        .page-title {
            font-size: 28px;
            color: #2c3e50;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .page-title i {
            color: #22b8cf;
            font-size: 32px;
        }

        .logs-count {
            background: #22b8cf;
            color: white;
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 16px;
            box-shadow: 0 4px 15px rgba(34, 184, 207, 0.3);
        }

        .table-container {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
        }

        .table {
            margin-bottom: 0;
        }

        .table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .table thead th {
            color: white;
            border: none;
            padding: 20px 15px;
            font-weight: 600;
            font-size: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table tbody tr {
            transition: all 0.3s ease;
            border-bottom: 1px solid #f1f3f4;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .table tbody td {
            padding: 18px 15px;
            vertical-align: middle;
            color: #495057;
            font-size: 14.5px;
        }

        .badge-action {
            padding: 8px 16px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 12px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .badge-created {
            background: #20c997;
            color: white;
        }

        .badge-updated {
            background: #17a2b8;
            color: white;
        }

        .badge-deleted {
            background: #dc3545;
            color: white;
        }

        .badge-other {
            background: #6c757d;
            color: white;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 14px;
        }

        .btn-view {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 8px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(79, 172, 254, 0.3);
        }

        .btn-view:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(79, 172, 254, 0.4);
        }

        .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }

        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 25px;
            border: none;
        }

        .modal-header h4 {
            margin: 0;
            font-weight: 600;
        }

        .modal-body {
            padding: 30px;
            max-height: 500px;
            overflow-y: auto;
        }

        .change-list {
            list-style: none;
            padding: 0;
        }

        .change-list li {
            padding: 15px;
            margin-bottom: 12px;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 4px solid #22b8cf;
            transition: all 0.3s ease;
        }

        .change-list li:hover {
            background: #edf2f7;
            transform: translateX(5px);
        }

        .field-name {
            font-weight: 600;
            color: #2c3e50;
            display: block;
            margin-bottom: 5px;
            font-size: 15px;
        }

        .change-value {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .old-value {
            background: #fee;
            color: #e74c3c;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13.5px;
            text-decoration: line-through;
        }

        .arrow {
            color: #95a5a6;
            font-size: 20px;
        }

        .new-value {
            background: #efe;
            color: #27ae60;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13.5px;
            font-weight: 600;
        }

        .no-changes {
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
            font-style: italic;
        }

        .no-changes i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #bdc3c7;
        }

        .pagination-container {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }

        .pagination {
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        }

        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: #667eea;
        }

        .pagination .page-link {
            color: #495057;
            border: none;
            margin: 0 5px;
            border-radius: 8px;
            padding: 12px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .pagination .page-link:hover {
            background-color: #f8f9fa;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }

            .table thead {
                display: none;
            }

            .table tbody tr {
                display: block;
                margin-bottom: 20px;
                border: 1px solid #e9ecef;
                border-radius: 10px;
            }

            .table tbody td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 12px 15px;
                border-bottom: 1px solid #f1f3f4;
            }

            .table tbody td:before {
                content: attr(data-label);
                font-weight: 600;
                color: #2c3e50;
                font-size: 13px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-history"></i>
                Historique des actions
            </h1>
            <div class="logs-count">
                <i class="fas fa-database"></i>
                {{ $logs->total() }} actions enregistrées
            </div>
        </div>

        <div class="table-container">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th><i class="fas fa-calendar-alt"></i> Date</th>
                        <th><i class="fas fa-user"></i> Utilisateur</th>
                        <th><i class="fas fa-bolt"></i> Action</th>
                        <th><i class="fas fa-table"></i> Table</th>
                        <th><i class="fas fa-info-circle"></i> Détails</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td data-label="Date">
                                <i class="far fa-clock text-primary me-2"></i>
                                {{ $log->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td data-label="Utilisateur">
                                <div class="user-info">
                                    <div class="user-avatar">
                                        {{ substr($log->user->name ?? 'S', 0, 1) }}
                                    </div>
                                    <span>{{ $log->user->name ?? 'Système' }}</span>
                                </div>
                            </td>
                            <td data-label="Action">
                                @php
                                    $actionClass = match ($log->action) {
                                        'created' => 'created',
                                        'updated' => 'updated',
                                        'deleted' => 'deleted',
                                        default => 'other',
                                    };
                                @endphp
                                <span class="badge-action badge-{{ $actionClass }}">
                                    <i
                                        class="fas fa-{{ $log->action === 'created' ? 'plus' : ($log->action === 'updated' ? 'edit' : ($log->action === 'deleted' ? 'trash' : 'cog')) }} me-1"></i>
                                    {{ ucfirst($log->action) }}
                                </span>
                            </td>
                            <td data-label="Table">
                                <i class="fas fa-database text-secondary me-2"></i>
                                {{ $log->table_name }}
                                <span class="text-muted">#{{ $log->record_id }}</span>
                            </td>
                            <td data-label="Détails">
                                <button class="btn btn-view" data-toggle="modal" data-target="#log{{ $log->id }}">
                                    <i class="fas fa-eye me-2"></i>
                                    Voir les détails
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h4 class="text-muted">Aucun historique disponible</h4>
                                <p class="text-muted">Aucune action n'a été enregistrée pour le moment</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($logs->hasPages())
            <div class="pagination-container">
                {{ $logs->links() }}
            </div>
        @endif

        {{-- Modals doivent être en dehors du tableau --}}
        @foreach ($logs as $log)
            <div class="modal fade" id="log{{ $log->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4>
                                <i class="fas fa-file-alt me-2"></i>
                                Détails de la modification
                            </h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <div class="info-card">
                                        <h6><i class="fas fa-user-clock me-2"></i> Utilisateur</h6>
                                        <p class="mb-0">{{ $log->user->name ?? 'Système' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-card">
                                        <h6><i class="fas fa-calendar me-2"></i> Date</h6>
                                        <p class="mb-0">{{ $log->created_at->format('d/m/Y H:i:s') }}</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-card">
                                        <h6><i class="fas fa-table me-2"></i> Table</h6>
                                        <p class="mb-0">{{ $log->table_name }} #{{ $log->record_id }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <h5 class="mb-4"><i class="fas fa-exchange-alt me-2"></i> Modifications</h5>
                            
                            @if(!empty($log->new_values) && is_array($log->new_values))
                                <ul class="change-list">
                                    @foreach ($log->new_values as $field => $newValue)
                                        @php
                                            // Récupérer l'ancienne valeur si disponible
                                            $oldValue = isset($log->old_values[$field]) ? $log->old_values[$field] : '-';
                                            
                                            // Gérer les valeurs NULL
                                            $oldValue = $oldValue ?? '-';
                                            $newValue = $newValue ?? '-';
                                            
                                            // Convertir les tableaux/objets en JSON
                                            if (is_array($oldValue) || is_object($oldValue)) {
                                                $oldValue = json_encode($oldValue, JSON_PRETTY_PRINT);
                                            }
                                            if (is_array($newValue) || is_object($newValue)) {
                                                $newValue = json_encode($newValue, JSON_PRETTY_PRINT);
                                            }
                                        @endphp
                                        <li>
                                            <span class="field-name">
                                                <i class="fas fa-tag me-2"></i>
                                                {{ ucfirst(str_replace('_', ' ', $field)) }}
                                            </span>
                                            <div class="change-value">
                                                <span class="old-value">
                                                    <i class="fas fa-arrow-left me-1"></i>
                                                    {{ $oldValue }}
                                                </span>
                                                <span class="arrow">
                                                    <i class="fas fa-long-arrow-alt-right"></i>
                                                </span>
                                                <span class="new-value">
                                                    {{ $newValue }}
                                                    <i class="fas fa-arrow-right ms-1"></i>
                                                </span>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @elseif(!empty($log->old_values) && is_array($log->old_values))
                                {{-- Cas d'une suppression --}}
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Données supprimées :
                                </div>
                                <ul class="change-list">
                                    @foreach ($log->old_values as $field => $value)
                                        <li>
                                            <span class="field-name">
                                                <i class="fas fa-tag me-2"></i>
                                                {{ ucfirst(str_replace('_', ' ', $field)) }}
                                            </span>
                                            <div class="change-value">
                                                <span class="old-value">
                                                    {{ is_array($value) || is_object($value) ? json_encode($value) : $value }}
                                                </span>
                                                <span class="arrow">
                                                    <i class="fas fa-long-arrow-alt-right"></i>
                                                </span>
                                                <span class="new-value text-danger">
                                                    SUPPRIMÉ
                                                </span>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="no-changes">
                                    <i class="fas fa-inbox"></i>
                                    <h5>Aucune modification détectée</h5>
                                    <p>Cette action ne contient pas de modifications de données spécifiques</p>
                                    
                                    {{-- Afficher les données disponibles pour inspection --}}
                                    @if(!empty($log->new_values))
                                        <div class="mt-3">
                                            <h6>Contenu de new_values :</h6>
                                            <pre>{{ var_export($log->new_values, true) }}</pre>
                                        </div>
                                    @endif
                                    @if(!empty($log->old_values))
                                        <div class="mt-3">
                                            <h6>Contenu de old_values :</h6>
                                            <pre>{{ var_export($log->old_values, true) }}</pre>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fas fa-times me-2"></i>
                                Fermer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection

@section('javascript')
    <script>
        $(document).ready(function() {
            // Animation pour les lignes du tableau
            $('.table tbody tr').each(function(i) {
                $(this).delay(i * 50).animate({
                    opacity: 1
                }, 200);
            });

            // Gestion des modals
            $('.modal').on('show.bs.modal', function() {
                $(this).find('.modal-content').addClass('animate__animated animate__fadeInUp');
            });

            // Tooltips
            $('[data-toggle="tooltip"]').tooltip();

            // Mise en évidence de l'action supprimée
            $('.badge-deleted').parent().parent().addClass('deleted-row');
        });
    </script>
    <style>
        .info-card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            border-left: 4px solid #667eea;
        }

        .info-card h6 {
            color: #2c3e50;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .deleted-row {
            background-color: #fff5f5 !important;
        }

        .deleted-row:hover {
            background-color: #ffeaea !important;
        }

        .animate__animated {
            animation-duration: 0.5s;
        }
    </style>
@endsection