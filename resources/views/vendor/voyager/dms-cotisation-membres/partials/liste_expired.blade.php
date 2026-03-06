@extends('voyager::master')

@section('page_title', 'LISTE DES MEMBRES EXPIRÉS')

@section('css')
    @include('voyager::dms-agr-facturations.partials.style')
@stop

@section('page_header')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="section-header text-center">
                    <div class="alert alert-info">
                        <div class="h4 text-white" style="font-weight: bold;">
                            <i class="bi bi-info-circle"></i>
                            LISTE COMPLETE DES COTISATIONS DES MEMBRES ( {{ $anneeSelectionnee }} )
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-bordered table-hover" id="cotisationTable">
                    <thead>
                        <tr>
                            <th scope="col">Membre</th>
                            <th scope="col">Type Membre</th>
                            <th scope="col">Date paiement</th>
                            <th scope="col">Année cotisation</th>
                            <th scope="col">Reste a payer</th>
                            <th scope="col">Mode de paiement</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($cotisations as $cotisation)
                            <tr>
                                <th scope="row">
                                    {{ $cotisation->membre?->libelle_membre }}
                                </th>
                                <td>{{ $cotisation->membre?->plan_adhesion?->title_plan }}</td>
                                <td>{{ \Carbon\Carbon::parse($cotisation->date_paiement)->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                                </td>
                                <td>{{ $cotisation->annee_cotisation }}</td>
                                <td>{{ $cotisation->reste_a_payer }}</td>
                                <td>{{ $cotisation->mode_paiement }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <h4 class="text-muted">Aucun membre cotisant</h4>
                                    <p class="text-muted">Aucun membre cotisant pour le moment</p>
                                    </th>

                            </tr>
                        @endforelse


                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop

@section('javascript')

@stop
