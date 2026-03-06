@extends('voyager::master')

@section('page_title', 'Support & Ticketing')

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        /* Intégration Charte Graphique */
        .access-ribbon {
            background-color: #ffffff;
            border-bottom: 2px solid #0056b3;
            padding: 10px 25px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .access-container {
            background-color: #fff;
            border: 1px solid #0056b3;
            margin-bottom: 25px;
            border-radius: 4px;
            overflow: hidden;
        }

        .access-header {
            background-color: #0056b3;
            color: white;
            padding: 10px 15px;
            font-weight: bold;
            font-size: 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Liste des conversations */
        .ticket-list {
            height: 600px;
            overflow-y: auto;
        }

        .ticket-item {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            transition: 0.2s;
        }

        .ticket-item:hover {
            background-color: #f0f7ff;
        }

        .ticket-item.active {
            background-color: #e7f1ff;
            border-left: 4px solid #0056b3;
        }

        .ticket-item .ticket-meta {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            margin-bottom: 5px;
        }

        .ticket-item .ticket-subject {
            font-weight: bold;
            font-size: 13px;
            color: #333;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Styles spécifiques au Chat */
        .chat-box {
            height: 450px;
            overflow-y: auto;
            padding: 20px;
            background: #f8faff;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .message {
            max-width: 80%;
            padding: 12px;
            border-radius: 10px;
            font-size: 13px;
            position: relative;
        }

        .message.received {
            align-self: flex-start;
            background: white;
            border: 1px solid #dee2e6;
            color: #333;
        }

        .message.sent {
            align-self: flex-end;
            background: #0056b3;
            color: white;
        }

        .message-info {
            font-size: 10px;
            margin-top: 5px;
            opacity: 0.8;
        }

        /* Formulaire de saisie */
        .form-section {
            padding: 15px;
        }

        .btn-submit {
            background-color: #0056b3;
            color: white;
            font-weight: bold;
            transition: 0.3s;
        }

        .btn-submit:hover {
            background-color: #003d82;
            color: white;
        }

        .attachment-preview {
            border: 1px dashed #0056b3;
            padding: 10px;
            text-align: center;
            margin-top: 10px;
            color: #0056b3;
            cursor: pointer;
        }
    </style>
@stop

@section('page_header')
    <div class="container-fluid">
        <div class="access-ribbon">
            <div style="font-weight: bold; color: #0056b3; font-size: 18px;">
                <i class="bi bi-headset"></i> CENTRE DE SUPPORT TECHNIQUE
            </div>
            <div class="ribbon-actions">
                <button class="btn btn-default" onclick="window.history.back()">
                    <i class="bi bi-arrow-left"></i> Quitter
                </button>
            </div>
        </div>

        <div class="row">
            <!-- LISTE DES CONVERSATIONS / TICKETS -->
            <div class="col-md-12">
                <div class="access-container">
                    <div class="access-header">
                        <span><i class="bi bi-chat-left-text"></i> MES TICKETS</span>
                        <a href="{{ route('voyager.tickets.create') }}" class="btn btn-xs btn-default"
                            style="color: #0056b3; padding: 1px 5px;">
                            <i class="bi bi-plus"></i>
                        </a>
                    </div>
                    <div class="ticket-list">
                        @forelse ($tickets as $ticket)
                            <div class="ticket-item {{ $loop->first ? 'active' : '' }}">
                                <div class="ticket-meta">
                                    <span class="text-primary">#{{ $ticket->id }}</span>
                                    <span>{{ $ticket->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="ticket-subject">{{ $ticket->subject }}</div>
                                <div class="small text-muted">
                                    Statut:
                                    <span
                                        class="text-{{ $ticket->status == 'En cours' || $ticket->status == 'Open' ? 'success' : 'danger' }}">
                                        {{ $ticket->status }}
                                    </span>
                                </div>

                                <!-- Bouton répondre -->
                                <div class="mt-2">
                                    <a href="{{ route('tickets.reply', $ticket->id) }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-reply"></i> Répondre
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-muted p-3">Aucun ticket trouvé.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('javascript')
    <script>
        document.getElementById('ticketForm').onsubmit = function(e) {
            e.preventDefault();
            alert('Ticket envoyé avec succès !');
        };

        var chatContainer = document.getElementById('chatContainer');
        chatContainer.scrollTop = chatContainer.scrollHeight;
    </script>
@stop
