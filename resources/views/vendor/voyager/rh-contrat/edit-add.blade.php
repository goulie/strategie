@php
    $edit = !is_null($dataTypeContent->getKey());
    $add = is_null($dataTypeContent->getKey());
@endphp

@extends('voyager::master')

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('page_title', __('voyager::generic.' . ($edit ? 'edit' : 'add')) . ' ' .
    $dataType->getTranslatedAttribute('display_name_singular'))

@section('page_header')
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i>
        {{ __('voyager::generic.' . ($edit ? 'edit' : 'add')) . ' ' . $dataType->getTranslatedAttribute('display_name_singular') }}
    </h1>

@stop

@section('content')
    <div class="page-content edit-add container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <!-- form start -->
                    <form role="form" class="form-edit-add"
                        action="{{ $edit ? route('voyager.' . $dataType->slug . '.update', $dataTypeContent->getKey()) : route('voyager.' . $dataType->slug . '.store') }}"
                        method="POST" enctype="multipart/form-data">
                        <!-- PUT Method if we are editing -->
                        @if ($edit)
                            {{ method_field('PUT') }}
                        @endif

                        <!-- CSRF TOKEN -->
                        {{ csrf_field() }}

                        <div class="panel-body">

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="collaborateur_id">Collaborateur</label>
                                        <select class="form-control" id="collaborateur_id" name="collaborateur_id" required>
                                            <option value="">-- Sélectionner un collaborateur --</option>
                                            @foreach ($collaborateurs as $collaborateur)
                                                <option value="{{ $collaborateur->id }}"
                                                    {{ $collaborateur->id ? 'selected' : '' }}>
                                                    {{ $collaborateur->Nom }} {{ $collaborateur->Prenoms }} <strong> - {{ $collaborateur->Matricule }}</strong>
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">

                                <!-- Type de contrat -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="type_contrat">Type de contrat</label>
                                        <select class="form-control" id="type_contrat" name="type_contrat" required>
                                            <option value="">-- Sélectionner --</option>
                                            <option value="stage"
                                                {{ ($dataTypeContent->type_contrat ?? '') == 'stage' ? 'selected' : '' }}>
                                                Stage</option>
                                            <option value="interim"
                                                {{ ($dataTypeContent->type_contrat ?? '') == 'interim' ? 'selected' : '' }}>
                                                Intérim</option>
                                            <option value="cdd"
                                                {{ ($dataTypeContent->type_contrat ?? '') == 'cdd' ? 'selected' : '' }}>CDD
                                            </option>
                                            <option value="cdi"
                                                {{ ($dataTypeContent->type_contrat ?? '') == 'cdi' ? 'selected' : '' }}>CDI
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Date début -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="date_debut">Date début</label>
                                        <input type="date" class="form-control" id="date_debut" name="date_debut"
                                            value="{{ $dataTypeContent->date_debut ?? '' }}">
                                    </div>
                                </div>

                                <!-- Date fin -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="date_fin">Date fin</label>
                                        <input type="date" class="form-control" id="date_fin" name="date_fin"
                                            value="{{ $dataTypeContent->date_fin ?? '' }}">
                                    </div>
                                </div>

                                <!-- Durée en mois -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="duree_mois">Durée (mois)</label>
                                        <input type="number" class="form-control" id="duree_mois" name="duree_mois"
                                            min="1" placeholder="Ex: 12"
                                            value="{{ $dataTypeContent->duree_mois ?? '' }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="remarques">Remarques</label>
                                        <textarea class="form-control" id="remarques" name="remarques" rows="3"
                                            placeholder="Observations, clauses particulières...">{{ $dataTypeContent->remarques ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="panel-footer">

                            <button type="submit" class="btn btn-primary save">{{ __('voyager::generic.save') }}</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>


@stop

@section('javascript')

@stop
