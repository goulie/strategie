<div class="modal-body" style="background-color: #fdfdfd;">

    {{-- 1. LOGIQUE PHP --}}
    @php
        $debut = \Carbon\Carbon::parse($contrat->date_debut);
        $fin = \Carbon\Carbon::parse($contrat->date_fin);
        $aujourdhui = \Carbon\Carbon::now();
        $joursRestants = $aujourdhui->diffInDays($fin, false);
        $estExpire = $joursRestants < 0;

        if ($contrat->statut == App\Models\RhContrat::STATUT_INACTIF) {
            $classStatus = 'danger';
            $iconStatus = 'voyager-x';
        } else {
            $classStatus = 'success';
            $iconStatus = 'voyager-check';
        }
    @endphp

    {{-- 2. EN-TÊTE : PHOTO + IDENTITÉ + STATUT --}}
    <div class="row"
        style="display: flex; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 20px; margin-bottom: 20px;">

        {{-- COLONNE GAUCHE : PHOTO --}}
        <div class="col-md-3 text-center">
            {{-- Espace pour la photo : Cercle avec image ou icône par défaut --}}
            <div
                style="width: 80px; height: 80px; margin: 0 auto; overflow: hidden; border-radius: 50%; border: 3px solid #e1e1e1; background: #fff; display: flex; align-items: center; justify-content: center;">
                @if (!empty($contrat->personel->avatar))
                    {{-- Si une image existe dans Voyager --}}
                    <img src="{{ Voyager::image($contrat->personel->avatar) }}" style="width: 100%; height: auto;">
                @else
                    {{-- Fallback si pas de photo --}}
                    <i class="voyager-person" style="font-size: 40px; color: #ccc;"></i>
                @endif
            </div>
        </div>

        {{-- COLONNE DROITE : INFOS & MATRICULE --}}
        <div class="col-md-9">
            <h4 style="margin-top: 0; margin-bottom: 5px; font-weight: bold; font-size: 18px;">
                {{ $contrat->personel?->Nom ?? 'Nom' }} {{ $contrat->personel?->Prenoms ?? 'Inconnu' }}
            </h4>

            <p style="color: #777; margin-bottom: 8px; font-size: 13px;">
                <i class="voyager-id"></i> Matricule : <strong>{{ $contrat->personel?->Matricule ?? 'N/A' }}</strong>
            </p>

            <span class="label label-{{ $classStatus }}" style="font-size: 11px; padding: 5px 10px;">
                <i class="{{ $iconStatus }}"></i> {{ $contrat->statut }}
            </span>
        </div>
    </div>

    {{-- 3. TIMELINE (DATES) --}}
    <div class="row text-center" style="margin-bottom: 20px;">
        <div class="col-xs-4" style="border-right: 1px solid #eee;">
            <small class="text-muted" style="text-transform: uppercase; font-size: 10px;">Début</small><br>
            <span style="font-weight: bold; color: #333;">{{ $debut->format('d/m/Y') }}</span>
        </div>
        <div class="col-xs-4" style="border-right: 1px solid #eee;">
            <small class="text-muted" style="text-transform: uppercase; font-size: 10px;">Reste</small><br>
            <span class="text-{{ $classStatus }}" style="font-weight: bold; font-size: 16px;">
                {{ $estExpire ? 0 : intval($joursRestants) }} j
            </span>
        </div>
        <div class="col-xs-4">
            <small class="text-muted" style="text-transform: uppercase; font-size: 10px;">Fin</small><br>
            <span style="font-weight: bold; color: #333;">{{ $fin->format('d/m/Y') }}</span>
        </div>
    </div>

    {{-- 4. DÉTAILS TECHNIQUES --}}
    <div class="row">
        <div class="col-md-12">
            <ul class="list-group" style="margin-bottom: 10px; font-size: 13px;">
                <li class="list-group-item clearfix">
                    <strong>Code contrat</strong>
                    <span class="pull-right">
                        <span class="label label-primary">{{ $contrat->code_contrat }}</span>
                    </span>
                </li>
                <li class="list-group-item clearfix">
                    <strong>Type de Contrat</strong>
                    <span class="pull-right label label-primary">{{ $contrat->type_contrat }}</span>
                </li>
                <li class="list-group-item clearfix">
                    <strong>Durée</strong>
                    <span class="pull-right">{{ $contrat->duree }} Mois</span>
                </li>
                <li class="list-group-item clearfix">
                    <strong>Renouvellement</strong>
                    <span class="pull-right">
                        @if ($contrat->renouvellement)
                            <span class="text-success">Oui</span>
                        @else
                            <span class="text-danger">Non</span>
                        @endif
                    </span>
                </li>


            </ul>
        </div>
    </div>

    {{-- 5. REMARQUES (Optionnel) --}}
    @if (!empty($contrat->remarques))
        <div class="row" style="margin-top: 10px;">
            <div class="col-md-12">
                <div
                    style="background: #eef7fa; padding: 10px; border-left: 3px solid #17a2b8; font-size: 12px; border-radius: 3px;">
                    <strong>Note:</strong> {{ $contrat->remarques }}
                </div>
            </div>
        </div>
    @endif


    @if ($contrat->statut == App\Models\RhContrat::STATUT_INACTIF)
@php
    $terminaisons = $contrat->contratTerminaisons;
    $collaborateur = $contrat->personel;
@endphp
    
        {{-- <div class="row">
            <div class="col-md-12">
                <ul class="list-group" style="margin-bottom: 10px; font-size: 13px;">
                    <li class="list-group-item clearfix">
                        <strong>Code contrat</strong>
                        <span class="pull-right">
                            <span class="label label-primary">{{ $contrat->code_contrat }}</span>
                        </span>
                    </li>
                    <li class="list-group-item clearfix">
                        <strong>Type de Contrat</strong>
                        <span class="pull-right label label-primary">{{ $contrat->type_contrat }}</span>
                    </li>
                    <li class="list-group-item clearfix">
                        <strong>Durée</strong>
                        <span class="pull-right">{{ $contrat->duree }} Mois</span>
                    </li>
                    <li class="list-group-item clearfix">
                        <strong>Renouvellement</strong>
                        <span class="pull-right">
                            @if ($contrat->renouvellement)
                                <span class="text-success">Oui</span>
                            @else
                                <span class="text-danger">Non</span>
                            @endif
                        </span>
                    </li>


                </ul>
            </div>
        </div> --}}
    @endif
</div>
