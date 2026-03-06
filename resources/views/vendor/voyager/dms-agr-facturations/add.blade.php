@extends('voyager::master')

@section('page_title', 'Ajouter une cotisation')

@section('css')
    @include('voyager::dms-agr-facturations.partials.style')
@stop

@section('page_header')
    <div class="container-fluid">
        <div class="access-ribbon">
            <div style="font-weight: bold; color: #0056b3; font-size: 16px;">
                <i class="bi bi-database-gear"></i> GESTION DES ACTIVITÉS (AGR)
            </div>
            <div class="ribbon-actions">
                <button class="btn" onclick="window.history.back()">
                    <i class="bi bi-arrow-left-circle"></i>Retour Dashboard
                </button>
                <button class="btn" onclick="window.print()">
                    <i class="bi bi-printer"></i>Imprimer Liste
                </button>
                <button class="btn text-danger">
                    <i class="bi bi-x-circle"></i>Annuler Saisie
                </button>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">
                <!-- FORMULAIRE DE SAISIE -->
                <div class="col-md-4">
                    <div class="access-container">
                        <div class="access-header">
                            <span><i class="bi bi-pencil-square"></i> NOUVELLE AGR</span>
                        </div>
                        <div class="form-section">
                            <form id="agrForm">
                                <div class="form-group">
                                    <label>Type d'Activité</label>
                                    <select class="form-control" id="activite" required>
                                        <option value="">-- Sélectionner --</option>
                                        <option>Formations et Autres Produits</option>
                                        <option>Annuaire</option>
                                        <option>Bulletins & Insertion AfWA News</option>
                                        <option>J P O</option>
                                        <option>Webinaire</option>
                                        <option>Panels</option>
                                        <option>Forums Régionaux</option>
                                        <option>Visite de Benchmarking</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Membre Bénéficiaire</label>
                                    <input type="text" class="form-control" id="membre"
                                        placeholder="Ex: SODEPCI, ONEP..." required>
                                </div>

                                <div class="row">
                                    <div class="col-xs-6">
                                        <div class="form-group">
                                            <label>Date Paiement</label>
                                            <input type="date" class="form-control" id="date" required>
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="form-group">
                                            <label>Année Exercice</label>
                                            <input type="number" class="form-control" id="annee" value="2024"
                                                required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Montant du Versement (FCFA)</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="montant" placeholder="0" required>
                                        <span class="input-group-addon"
                                            style="background: #f8faff; color: #0056b3;">CFA</span>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-submit btn-block">
                                    <i class="bi bi-save"></i> ENREGISTRER L'OPÉRATION
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- LISTE DES SAISIES -->
                <div class="col-md-8">
                    <div class="access-container">
                        <div class="access-header">
                            <span><i class="bi bi-list-check"></i> LISTE DES ACTIVITÉS SAISIES</span>
                            <span class="badge" style="background: white; color: #0056b3;" id="countBadge">0 entrées</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-management table-bordered" id="agrTable">
                                <thead>
                                    <tr>
                                        <th width="80">Année</th>
                                        <th>Date</th>
                                        <th>Activité</th>
                                        <th>Membre</th>
                                        <th class="text-right">Montant</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Les données apparaîtront ici -->
                                </tbody>
                            </table>
                        </div>
                        <div class="panel-footer text-right" style="background: #f8faff;">
                            <strong>Total Saisi : <span id="totalSaisi" style="color: #0056b3;">0</span> FCFA</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('javascript')

@stop
