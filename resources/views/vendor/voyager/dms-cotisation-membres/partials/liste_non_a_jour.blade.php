@extends('voyager::master')

@section('page_title', 'LISTE DES Membres Non à Jour')

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
                            LISTE COMPLETE DES listS DES MEMBRES NON À JOUR
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-bordered table-hover" id="listTable">
                    <thead>
                        <tr>
                            <th scope="col">Membre</th>
                            <th scope="col">Type Membre</th>
                            <th scope="col">Date dernier paiement</th>
                            <th scope="col">Date Expiration</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($lists as $list)
                        @php
                            $membre = App\Models\DmsMembre::find($list->membre_id);
                        @endphp
                        
                            <tr>
                                <th scope="row">
                                    {{ $membre->libelle_membre }}
                                </th>
                                <td>
                                    {{ $membre->plan_adhesion?->title_plan }}
                                </td>
                                <td>{{ \Carbon\Carbon::parse($list->date_paiement)->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                                </td>
                                <td>{{ $list->date_echeance }}</td>
                                
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
