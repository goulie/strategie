@extends('voyager::master')

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .personnel-select-wrapper {
            margin-bottom: 15px;
        }

        .personnel-info {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-top: 10px;
            display: none;
        }

        .personnel-info.active {
            display: block;
        }

        .info-row {
            margin-bottom: 5px;
        }

        .info-label {
            font-weight: bold;
            color: #495057;
            min-width: 120px;
            display: inline-block;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #5897fb;
            color: white;
        }

        .required-field::after {
            content: " *";
            color: red;
        }

        .image-preview-container {
            margin-top: 10px;
            text-align: center;
        }

        .image-preview {
            max-width: 200px;
            max-height: 200px;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 5px;
            background: #f8f9fa;
        }

        .image-upload-area {
            border: 2px dashed #ccc;
            border-radius: 5px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .image-upload-area:hover {
            border-color: #3490dc;
            background: #f8f9fa;
        }

        .image-upload-area i {
            font-size: 48px;
            color: #6c757d;
            margin-bottom: 10px;
        }

        .image-upload-area.dragover {
            border-color: #3490dc;
            background: #e3f2fd;
        }
    </style>
@stop

@section('page_title', __('voyager::generic.' . (isset($dataTypeContent->id) ? 'edit' : 'add')) . ' ' .
    $dataType->getTranslatedAttribute('display_name_singular'))

@section('page_header')
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i>
        {{ __('voyager::generic.' . (isset($dataTypeContent->id) ? 'edit' : 'add')) . ' ' . $dataType->getTranslatedAttribute('display_name_singular') }}
    </h1>
    @include('voyager::multilingual.language-selector')
@stop

@section('content')
    <div class="page-content container-fluid">
        @include('voyager::alerts')
        <div class="row">
            <div class="col-md-12">
                <form class="form-edit-add" role="form"
                    action="@if (isset($dataTypeContent->id)) {{ route('voyager.' . $dataType->slug . '.update', $dataTypeContent->id) }}@else{{ route('voyager.' . $dataType->slug . '.store') }} @endif"
                    method="POST" enctype="multipart/form-data">
                    <!-- PUT method if we are editing -->
                    @if (isset($dataTypeContent->id))
                        {{ method_field('PUT') }}
                    @endif
                    {{ csrf_field() }}

                    <div class="panel panel-bordered">
                        <div class="panel-body">
                            <div class="row">
                                <!-- Colonne de gauche -->
                                <div class="col-md-6">
                                    <!-- Sélection du personnel -->
                                    <div class="personnel-select-wrapper">
                                        <label for="personnel_id" class="required-field">{{ __('Personnel') }}</label>
                                        <select class="form-control select2" id="personnel_id" name="personnel_id" required>
                                            <option value="">{{ __('Sélectionner un personnel') }}</option>
                                            @php
                                                // Récupérer tous les personnels actifs
                                                $personnels = \App\Models\RhListePersonnel::where('status', 'Actif')
                                                    ->orderBy('nom')
                                                    ->orderBy('prenoms')
                                                    ->get();

                                                $selectedPersonnel = isset($dataTypeContent->personnel_id)
                                                    ? \App\Models\RhListePersonnel::find($dataTypeContent->personnel_id)
                                                    : null;
                                            @endphp

                                            @foreach ($personnels as $personnel)
                                                <option value="{{ $personnel->id }}"
                                                    data-matricule="{{ $personnel->matricule }}"
                                                    data-nom="{{ $personnel->nom }}"
                                                    data-prenoms="{{ $personnel->prenoms }}"
                                                    data-sex="{{ $personnel->sexe }}"
                                                    data-service="{{ $personnel->service->libelle ?? 'Non défini' }}"
                                                    data-poste="{{ $personnel->poste ?? 'Non défini' }}"
                                                    @if (isset($dataTypeContent->personnel_id) && $dataTypeContent->personnel_id == $personnel->id) selected @endif
                                                    @if (old('personnel_id') == $personnel->id) selected @endif>
                                                    {{ $personnel->nom }} {{ $personnel->prenoms }} -
                                                    {{ $personnel->matricule }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('personnel_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror

                                        <!-- Informations du personnel sélectionné -->
                                        <div class="personnel-info" id="personnel-info">
                                            <h6>Informations du personnel</h6>
                                            <div class="info-row">
                                                <span class="info-label">Matricule:</span>
                                                <span id="info-matricule">-</span>
                                            </div>
                                            <div class="info-row">
                                                <span class="info-label">Nom complet:</span>
                                                <span id="info-nom-complet">-</span>
                                            </div>
                                            <div class="info-row">
                                                <span class="info-label">Sexe:</span>
                                                <span id="info-sex">-</span>
                                            </div>
                                            <div class="info-row">
                                                <span class="info-label">Service:</span>
                                                <span id="info-service">-</span>
                                            </div>
                                            <div class="info-row">
                                                <span class="info-label">Poste:</span>
                                                <span id="info-poste">-</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Nom complet de l'enfant -->
                                    <div class="form-group">
                                        <label for="nom_complet" class="required-field">{{ __('Nom complet') }}</label>
                                        <input type="text" class="form-control" id="nom_complet" name="nom_complet"
                                            placeholder="{{ __('Entrez le nom complet de l\'enfant') }}"
                                            value="{{ old('nom_complet', $dataTypeContent->nom_complet ?? '') }}" required>
                                        @error('nom_complet')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Date de naissance -->
                                    <div class="form-group">
                                        <label for="date_naissance"
                                            class="required-field">{{ __('Date de naissance') }}</label>
                                        <input type="date" class="form-control" id="date_naissance" name="date_naissance"
                                            value="{{ old('date_naissance', isset($dataTypeContent->date_naissance) ? \Carbon\Carbon::parse($dataTypeContent->date_naissance)->format('Y-m-d') : '') }}"
                                            max="{{ date('Y-m-d') }}" required>
                                        @error('date_naissance')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                        <small class="form-text text-muted" id="age-calculated"></small>
                                    </div>

                                    <!-- Sexe -->
                                    <div class="form-group">
                                        <label for="sexe" class="required-field">{{ __('Sexe') }}</label>
                                        <select class="form-control" id="sexe" name="sexe" required>
                                            <option value="">{{ __('Sélectionner') }}</option>
                                            <option value="Masculin" @if (old('sexe', $dataTypeContent->sexe ?? '') == 'Masculin') selected @endif>
                                                Masculin
                                            </option>
                                            <option value="Féminin" @if (old('sexe', $dataTypeContent->sexe ?? '') == 'Féminin') selected @endif>
                                                Féminin
                                            </option>
                                        </select>
                                        @error('sexe')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Colonne de droite -->
                                <div class="col-md-6">
                                    <!-- Photo de l'enfant -->
                                    <div class="form-group">
                                        <label for="image">{{ __('Photo de l\'enfant') }}</label>

                                        <!-- Zone de téléchargement -->
                                        <div class="image-upload-area" id="image-upload-area">
                                            <i class="voyager-camera"></i>
                                            <p>Cliquez ici ou glissez-déposez une image</p>
                                            <small class="text-muted">Formats acceptés: JPG, PNG, GIF (Max: 2MB)</small>
                                        </div>

                                        <!-- Champ fichier caché -->
                                        <input type="file" class="form-control d-none" id="image" name="image"
                                            accept=".jpg,.jpeg,.png,.gif,.bmp" onchange="previewImage(event)">
                                        @error('image')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror

                                        <!-- Aperçu de l'image -->
                                        <div class="image-preview-container" id="image-preview-container">
                                            @if (isset($dataTypeContent->image) && $dataTypeContent->image)
                                                <div class="mb-2">
                                                    <img src="{{ Voyager::image($dataTypeContent->image) }}"
                                                        alt="Photo actuelle" class="image-preview" id="current-image">
                                                    <div class="mt-2">
                                                        <button type="button" class="btn btn-sm btn-danger"
                                                            onclick="removeImage()">
                                                            <i class="voyager-trash"></i> Supprimer la photo
                                                        </button>
                                                    </div>
                                                </div>
                                            @else
                                                <img src="" alt="Aperçu" class="image-preview d-none"
                                                    id="image-preview">
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Extrait de naissance -->
                                    <div class="form-group">
                                        <label for="extrait_naissance">{{ __('Extrait de naissance') }}</label>
                                        @if (isset($dataTypeContent->extrait_naissance) && $dataTypeContent->extrait_naissance)
                                            <div class="mb-2">
                                                <a href="{{ Storage::url($dataTypeContent->extrait_naissance) }}"
                                                    target="_blank" class="btn btn-sm btn-info">
                                                    <i class="voyager-download"></i> Voir le fichier actuel
                                                </a>
                                            </div>
                                        @endif
                                        <input type="file" class="form-control" id="extrait_naissance"
                                            name="extrait_naissance" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                        @error('extrait_naissance')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Formats acceptés: PDF, JPG, PNG, DOC (Max: 5MB)
                                        </small>
                                    </div>

                                    <!-- Statut -->
                                    <div class="form-group">
                                        <label for="status">{{ __('Statut') }}</label>
                                        <select class="form-control" id="status" name="status">
                                            <option value="Actif" @if (old('status', $dataTypeContent->status ?? 'Actif') == 'Actif') selected @endif>
                                                Actif
                                            </option>
                                            <option value="Inactif" @if (old('status', $dataTypeContent->status ?? '') == 'Inactif') selected @endif>
                                                Inactif
                                            </option>
                                        </select>
                                        @error('status')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Informations complémentaires -->
                                    @if (isset($dataTypeContent->id))
                                        <div class="form-group">
                                            <label>{{ __('Informations') }}</label>
                                            <div class="info-box bg-light p-3">
                                                <div class="info-row">
                                                    <span class="info-label">Créé le:</span>
                                                    <span>{{ $dataTypeContent->created_at?->format('d/m/Y H:i') }}</span>
                                                </div>
                                                @if ($dataTypeContent->updated_at != $dataTypeContent->created_at)
                                                    <div class="info-row">
                                                        <span class="info-label">Modifié le:</span>
                                                        <span>{{ $dataTypeContent->updated_at->format('d/m/Y H:i') }}</span>
                                                    </div>
                                                @endif
                                                @if ($dataTypeContent->date_naissance)
                                                    <div class="info-row">
                                                        <span class="info-label">Âge:</span>
                                                        <span id="current-age">
                                                            {{ \Carbon\Carbon::parse($dataTypeContent->date_naissance)->age }}
                                                            ans
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="panel-footer">
                            <button type="submit"
                                class="btn btn-primary save">{{ __('voyager::generic.save') }}</button>
                            <a href="{{ route('voyager.' . $dataType->slug . '.index') }}"
                                class="btn btn-default">{{ __('voyager::generic.cancel') }}</a>
                        </div>
                    </div>

                    <!-- Champ caché pour suppression d'image -->
                    <input type="hidden" name="remove_image" id="remove_image" value="0">
                </form>
            </div>
        </div>
    </div>
@stop

@section('javascript')
    <script src="{{ voyager_asset('lib/js/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ voyager_asset('js/select2/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialiser Select2 pour le personnel
            $('#personnel_id').select2({
                placeholder: "{{ __('Rechercher un personnel...') }}",
                allowClear: true,
                width: '100%',
                templateResult: formatPersonnelOption,
                templateSelection: formatPersonnelSelection
            });

            // Afficher les informations du personnel sélectionné
            $('#personnel_id').on('change', function() {
                updatePersonnelInfo();
            });

            // Calculer l'âge en temps réel
            $('#date_naissance').on('change', function() {
                calculateAge();
            });

            // Initialiser au chargement
            updatePersonnelInfo();
            calculateAge();

            // Gestion de l'upload d'image
            setupImageUpload();

            function formatPersonnelOption(personnel) {
                if (!personnel.id) {
                    return personnel.text;
                }

                var matricule = $(personnel.element).data('matricule');
                var nom = $(personnel.element).data('nom');
                var prenoms = $(personnel.element).data('prenoms');

                var $option = $(
                    '<div>' +
                    '<strong>' + nom + ' ' + prenoms + '</strong>' +
                    '<br><small class="text-muted">Matricule: ' + matricule + '</small>' +
                    '</div>'
                );
                return $option;
            }

            function formatPersonnelSelection(personnel) {
                if (!personnel.id) {
                    return personnel.text;
                }
                var nom = $(personnel.element).data('nom');
                var prenoms = $(personnel.element).data('prenoms');
                return nom + ' ' + prenoms;
            }

            function updatePersonnelInfo() {
                var selectedOption = $('#personnel_id option:selected');
                var personnelInfo = $('#personnel-info');

                if (selectedOption.val()) {
                    personnelInfo.addClass('active');
                    $('#info-matricule').text(selectedOption.data('matricule') || '-');
                    $('#info-nom-complet').text(
                        (selectedOption.data('nom') || '') + ' ' + (selectedOption.data('prenoms') || '')
                    );
                    $('#info-sex').text(selectedOption.data('sex') || '-');
                    $('#info-service').text(selectedOption.data('service') || '-');
                    $('#info-poste').text(selectedOption.data('poste') || '-');
                } else {
                    personnelInfo.removeClass('active');
                }
            }

            function calculateAge() {
                var birthDate = $('#date_naissance').val();
                if (birthDate) {
                    var today = new Date();
                    var birth = new Date(birthDate);
                    var age = today.getFullYear() - birth.getFullYear();
                    var m = today.getMonth() - birth.getMonth();

                    if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) {
                        age--;
                    }

                    // Calculer les mois supplémentaires
                    var months = today.getMonth() - birth.getMonth();
                    if (months < 0) {
                        months += 12;
                    }

                    var ageText = age + ' ans';
                    if (age == 0) {
                        var totalMonths = (today.getFullYear() - birth.getFullYear()) * 12;
                        totalMonths += today.getMonth() - birth.getMonth();
                        ageText = totalMonths + ' mois';
                    } else if (months > 0) {
                        ageText += ' et ' + months + ' mois';
                    }

                    $('#age-calculated').html('<i class="voyager-calendar"></i> Âge: ' + ageText);
                } else {
                    $('#age-calculated').text('');
                }
            }

            function setupImageUpload() {
                var uploadArea = $('#image-upload-area');
                var fileInput = $('#image');
                var previewContainer = $('#image-preview-container');
                var preview = $('#image-preview');
                var currentImage = $('#current-image');

                // Clic sur la zone d'upload
                uploadArea.click(function() {
                    fileInput.click();
                });

                // Drag and drop
                uploadArea.on('dragover', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $(this).addClass('dragover');
                });

                uploadArea.on('dragleave', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $(this).removeClass('dragover');
                });

                uploadArea.on('drop', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $(this).removeClass('dragover');

                    var files = e.originalEvent.dataTransfer.files;
                    if (files.length > 0) {
                        fileInput[0].files = files;
                        previewImage({
                            target: fileInput[0]
                        });
                    }
                });
            }

            function previewImage(event) {
                var input = event.target;
                var previewContainer = $('#image-preview-container');
                var preview = $('#image-preview');
                var currentImage = $('#current-image');
                var removeImageBtn = $('#remove-image-btn');

                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function(e) {
                        // Cacher l'image actuelle s'il y en a une
                        if (currentImage.length) {
                            currentImage.hide();
                            currentImage.next('.mt-2').hide();
                        }

                        // Afficher la nouvelle image
                        preview.attr('src', e.target.result);
                        preview.removeClass('d-none');

                        // Réinitialiser le champ de suppression
                        $('#remove_image').val('0');
                    }

                    reader.readAsDataURL(input.files[0]);
                }
            }

            function removeImage() {
                // Cacher l'image actuelle
                $('#current-image').hide();
                $('.mt-2').hide();

                // Afficher l'aperçu comme vide
                $('#image-preview').addClass('d-none').attr('src', '');

                // Réinitialiser le champ fichier
                $('#image').val('');

                // Activer le champ caché pour suppression
                $('#remove_image').val('1');
            }

            // Validation du formulaire
            $('form').on('submit', function(e) {
                var isValid = true;
                var requiredFields = $('[required]');

                requiredFields.each(function() {
                    if (!$(this).val().trim()) {
                        isValid = false;
                        $(this).addClass('is-invalid');
                        var fieldName = $(this).attr('name');
                        var errorMessage = $(this).data('error-message') || 'Ce champ est requis';
                        if (!$('#' + fieldName + '-error').length) {
                            $(this).after('<div class="invalid-feedback" id="' + fieldName +
                                '-error">' + errorMessage + '</div>');
                        }
                    } else {
                        $(this).removeClass('is-invalid');
                        $('#' + $(this).attr('name') + '-error').remove();
                    }
                });

                // Validation spécifique pour la date de naissance
                var birthDate = $('#date_naissance').val();
                if (birthDate) {
                    var birth = new Date(birthDate);
                    var today = new Date();
                    if (birth > today) {
                        isValid = false;
                        $('#date_naissance').addClass('is-invalid');
                        if (!$('#date_naissance-future-error').length) {
                            $('#date_naissance').after(
                                '<div class="invalid-feedback" id="date_naissance-future-error">La date de naissance ne peut pas être dans le futur</div>'
                                );
                        }
                    }
                }

                // Validation de la taille de l'image
                var imageInput = $('#image')[0];
                if (imageInput.files && imageInput.files[0]) {
                    var fileSize = imageInput.files[0].size / 1024 / 1024; // en MB
                    if (fileSize > 2) {
                        isValid = false;
                        $('#image').addClass('is-invalid');
                        if (!$('#image-size-error').length) {
                            $('#image').after(
                                '<div class="invalid-feedback" id="image-size-error">L\'image ne doit pas dépasser 2MB</div>'
                                );
                        }
                    }
                }

                if (!isValid) {
                    e.preventDefault();
                    // Scroll vers le premier champ invalide
                    var firstInvalid = $('.is-invalid').first();
                    if (firstInvalid.length) {
                        $('html, body').animate({
                            scrollTop: firstInvalid.offset().top - 100
                        }, 500);
                    }
                }
            });

            // Supprimer les messages d'erreur lors de la saisie
            $('input, select').on('input change', function() {
                $(this).removeClass('is-invalid');
                $('#' + $(this).attr('name') + '-error').remove();
                $('#date_naissance-future-error').remove();
                $('#image-size-error').remove();
            });
        });
    </script>
@stop
