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
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
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
    .form-section { padding: 15px; }
    
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
        <div class="col-md-4">
            <div class="access-container">
                <div class="access-header">
                    <span><i class="bi bi-plus-circle"></i> OUVRIR UN TICKET</span>
                </div>
                <div class="form-section">
                    <form id="ticketForm">
                        <div class="form-group">
                            <label>N° Ticket (Auto)</label>
                            <input type="text" class="form-control" value="TK-{{ date('Ymd') }}-042" readonly style="background: #eee;">
                        </div>

                        <div class="form-group">
                            <label>Objet du problème</label>
                            <input type="text" class="form-control" placeholder="Ex: Erreur lors de l'enregistrement..." required>
                        </div>

                        <div class="form-group">
                            <label>Service concerné</label>
                            <select class="form-control" required>
                                <option value="">-- Choisir un service --</option>
                                <option>Assistance Technique (IT)</option>
                                <option>Service Financier</option>
                                <option>Ressources Humaines</option>
                                <option>Logistique & Matériel</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Description détaillée</label>
                            <textarea class="form-control" rows="4" placeholder="Décrivez votre problème ici..."></textarea>
                        </div>

                        <div class="form-group">
                            <label>Pièce jointe (Capture, PDF...)</label>
                            <input type="file" id="fileInput" style="display: none;">
                            <div class="attachment-preview" onclick="document.getElementById('fileInput').click()">
                                <i class="bi bi-cloud-upload"></i> Cliquez pour joindre un fichier
                            </div>
                        </div>

                        <button type="submit" class="btn btn-submit btn-block">
                            <i class="bi bi-send-check"></i> ENVOYER LA DEMANDE
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- INTERFACE DE CONVERSATION (CHAT) -->
        <div class="col-md-8">
            <div class="access-container">
                <div class="access-header">
                    <span><i class="bi bi-chat-dots"></i> HISTORIQUE DE CONVERSATION - TICKET #8921</span>
                    <span class="badge" style="background: #28a745;">En cours de traitement</span>
                </div>
                
                <div class="chat-box" id="chatContainer">
                    <!-- Message Reçu -->
                    <div class="message received">
                        <strong>Support IT (Jean-Paul)</strong><br>
                        Bonjour, nous avons bien reçu votre demande concernant l'exportation des données AGR. Pouvez-vous nous envoyer une capture d'écran de l'erreur ?
                        <div class="message-info">Aujourd'hui, 09:15</div>
                    </div>

                    <!-- Message Envoyé -->
                    <div class="message sent">
                        Voici la capture d'écran du message d'erreur SQL qui s'affiche au clic sur le bouton "Exporter".
                        <div style="margin-top:5px; padding: 5px; background: rgba(255,255,255,0.2); border-radius: 5px;">
                            <i class="bi bi-file-earmark-image"></i> error_sql_log.png
                        </div>
                        <div class="message-info">Aujourd'hui, 09:22</div>
                    </div>

                    <!-- Message Reçu -->
                    <div class="message received">
                        <strong>Support IT (Jean-Paul)</strong><br>
                        Merci. Nos ingénieurs travaillent sur la correction. Nous reviendrons vers vous d'ici 30 minutes.
                        <div class="message-info">Aujourd'hui, 09:25</div>
                    </div>
                </div>

                <!-- Zone de saisie rapide du chat -->
                <div class="panel-footer" style="background: white; border-top: 1px solid #0056b3;">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Répondre ici...">
                        <span class="input-group-btn">
                            <button class="btn btn-primary" style="background: #0056b3; border: none;" type="button">
                                <i class="bi bi-send"></i>
                            </button>
                        </span>
                    </div>
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