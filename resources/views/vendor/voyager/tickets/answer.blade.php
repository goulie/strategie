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

        /* Styles du Chat */
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
            margin-bottom: 10px;
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

        /* Design de la zone de réponse (Textarea) */
        .reply-section {
            background: #ffffff;
            border-top: 1px solid #0056b3;
            padding: 15px;
        }

        .reply-wrapper {
            border: 1px solid #ccc;
            border-radius: 4px;
            transition: border-color 0.3s;
        }

        .reply-wrapper:focus-within {
            border-color: #0056b3;
            box-shadow: 0 0 5px rgba(0, 86, 179, 0.2);
        }

        .reply-textarea {
            border: none;
            resize: none;
            width: 100%;
            padding: 10px;
            font-size: 13px;
            outline: none;
        }

        .reply-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 10px;
            background: #f9f9f9;
            border-top: 1px solid #eee;
        }

        .btn-attach {
            color: #666;
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
        }

        .btn-attach:hover { color: #0056b3; }

        .btn-send {
            background-color: #0056b3;
            color: white;
            border: none;
            padding: 6px 20px;
            border-radius: 3px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-send:hover { background-color: #003d82; }

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
            <div class="col-md-12">
                <div class="access-container">
                    <div class="access-header">
                        <span><i class="bi bi-chat-dots"></i> HISTORIQUE DE CONVERSATION - TICKET #{{ $id }}</span>
                        <span class="badge" style="background: #28a745;">Actif</span>
                    </div>
            
                    <div class="chat-box" id="chatContainer">
                        @foreach($ticket->messages as $msg)
                            @php $isMine = $msg->send_by == Auth::id(); @endphp
                            <div class="message {{ $isMine ? 'sent' : 'received' }}">
                                <span class="label label-primary" style="font-size: 12px; font-weight: bold;">{{ $msg->sender->name }}</span><br>
                                <p style="margin: 5px 0;">{{ $msg->message }}</p>
                                <br>
                                <div class="message-info">{{ $msg->created_at->diffForHumans() }}</div>
                            </div>
                        @endforeach
                    </div>
            
                    <!-- SECTION RÉPONSE REVISITÉE -->
                    <div class="reply-section">
                        <form action="{{ route('send.tickets.reply') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" value="{{ $id }}">
                            
                            <div class="reply-wrapper">
                                <textarea name="message" class="reply-textarea" rows="3" placeholder="Écrivez votre message ici..." required></textarea>
                                
                                <div class="reply-actions">
                                    
                                    
                                    <button type="submit" class="btn-send">
                                        <span>Envoyer</span> <i class="bi bi-send-fill"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('javascript')

@stop