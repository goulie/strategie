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
        }

        /* Styles spécifiques au Chat */
        .chat-box {
            height: 400px;
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
            <!-- FORMULAIRE DE CRÉATION DE TICKET -->
            <div class="col-md-12">
                <div class="access-container">
                    <div class="access-header">
                        <span><i class="bi bi-plus-circle"></i> OUVRIR UN TICKET</span>
                    </div>
                    <div class="form-section">
                        <form action="{{ route('open.ticket') }}" method="POST">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ auth()->id() }}">

                            <div class="form-group">
                                <label>Objet</label>
                                <input type="text" name="subject" class="form-control" placeholder="Objet du ticket"
                                    required>
                            </div>

                            <div class="form-group">
                                <label>Priorité</label>
                                <select name="priority" class="form-control" required>
                                    <option value="Basse">Basse</option>
                                    <option value="Moyenne">Moyenne</option>
                                    <option value="Haute">Haute</option>
                                </select>
                            </div>


                            <div class="form-group">
                                <label>Assigner à</label>
                                <select name="assigne_to" class="form-control" required>
                                    <option value="">-- Choisir un utilisateur --</option>
                                    @foreach (App\Models\User::all() as $user)
                                        @if ($user->id != auth()->id())
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Message</label>
                                <textarea name="message" class="form-control" rows="4" placeholder="Votre message..." required></textarea>
                            </div>

                            
                            

                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send-check"></i> Envoyer
                            </button>
                        </form>
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
            alert('Ticket envoyé avec succès ! Un agent vous répondra sous peu.');
        };

        // Auto-scroll du chat vers le bas
        var chatContainer = document.getElementById('chatContainer');
        chatContainer.scrollTop = chatContainer.scrollHeight;
    </script>
@stop
