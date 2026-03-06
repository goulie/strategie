@extends('voyager::master')

@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

@endsection

@section('content')
    <div class="page-content">
        <div class="multistep-container">
            <h1 class="text-center" style="margin-bottom: 40px; color: #2c3e50;">
                <i class="fa fa-user-plus"></i> Enregistrement d'un Employé
            </h1>

            <!-- Progress Bar -->
            <div class="step-progress">
                <div class="progress-line" style="width: 0%"></div>
                <div class="step-item active" data-step="1">
                    <div class="step-circle">
                        <span>1</span>
                        <i class="fa fa-check"></i>
                    </div>
                    <div class="step-label">Identité</div>
                </div>
                <div class="step-item" data-step="2">
                    <div class="step-circle">
                        <span>2</span>
                        <i class="fa fa-check"></i>
                    </div>
                    <div class="step-label">Situation</div>
                </div>
                <div class="step-item" data-step="3">
                    <div class="step-circle">
                        <span>3</span>
                        <i class="fa fa-check"></i>
                    </div>
                    <div class="step-label">Professionnel</div>
                </div>
                <div class="step-item" data-step="4">
                    <div class="step-circle">
                        <span>4</span>
                        <i class="fa fa-check"></i>
                    </div>
                    <div class="step-label">Administration</div>
                </div>
                <div class="step-item" data-step="5">
                    <div class="step-circle">
                        <span>5</span>
                        <i class="fa fa-check"></i>
                    </div>
                    <div class="step-label">Contacts</div>
                </div>
            </div>

            <!-- Alert Container -->
            <div id="alert-container"></div>

            <form id="multistep-form" enctype="multipart/form-data">
                @csrf

                <!-- ÉTAPE 1: Identité -->
                <div class="form-step active" data-step="1">
                    <h3 class="step-title"><i class="fa fa-id-card"></i> Informations d'Identité</h3>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Matricule <span class="required">*</span></label>
                                <input type="text" name="matricule" class="form-control" required>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Sexe <span class="required">*</span></label>
                                <select name="sexe" class="form-control select2" required>
                                    <option value="">Sélectionner</option>
                                    <option value="M">Masculin</option>
                                    <option value="F">Féminin</option>
                                </select>
                                <span class="help-block"></span>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Nom <span class="required">*</span></label>
                                <input type="text" name="nom" class="form-control" required>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Prénoms <span class="required">*</span></label>
                                <input type="text" name="prenoms" class="form-control" required>
                                <span class="help-block"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Date de Naissance <span class="required">*</span></label>
                                <input type="date" name="date_naissance" class="form-control" required>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Lieu de Naissance <span class="required">*</span></label>
                                <input type="text" name="lieu_naissance" class="form-control" required>
                                <span class="help-block"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nationalité <span class="required">*</span></label>
                                <input type="text" name="nationalite" class="form-control" required>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>CNI/Passeport <span class="required">*</span></label>
                                <input type="text" name="cni_passeport" class="form-control" required>
                                <span class="help-block"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ÉTAPE 2: Situation Personnelle -->
                <div class="form-step" data-step="2">
                    <h3 class="step-title"><i class="fa fa-home"></i> Situation Personnelle</h3>

                    <div class="form-group">
                        <label>Situation Matrimoniale <span class="required">*</span></label>
                        <select name="situation_matrimoniale" class="form-control select2" required>
                            <option value="">Sélectionner</option>
                            <option value="célibataire">Célibataire</option>
                            <option value="marié(e)">Marié(e)</option>
                            <option value="divorcé(e)">Divorcé(e)</option>
                            <option value="veuf(ve)">Veuf(ve)</option>
                        </select>
                        <span class="help-block"></span>
                    </div>

                    <div class="form-group">
                        <label>Adresse <span class="required">*</span></label>
                        <textarea name="adresse" class="form-control" rows="3" required></textarea>
                        <span class="help-block"></span>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Contact Personnel <span class="required">*</span></label>
                                <input type="text" name="contact_personnel" class="form-control" required>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control">
                                <span class="help-block"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ÉTAPE 3: Informations Professionnelles -->
                <div class="form-step" data-step="3">
                    <h3 class="step-title"><i class="fa fa-briefcase"></i> Informations Professionnelles</h3>

                    <div class="form-group">
                        <label>Service <span class="required">*</span></label>
                        <select name="service_id" class="form-control select2" required>
                            <option value="">Sélectionner un service</option>
                            @foreach ($services ?? [] as $service)
                                <option value="{{ $service->id }}">{{ $service->nom }}</option>
                            @endforeach
                        </select>
                        <span class="help-block"></span>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Site de Travail <span class="required">*</span></label>
                                <input type="text" name="site_travail" class="form-control" required>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Qualification <span class="required">*</span></label>
                                <input type="text" name="qualification" class="form-control" required>
                                <span class="help-block"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Poste <span class="required">*</span></label>
                                <input type="text" name="poste" class="form-control" required>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Date d'Entrée <span class="required">*</span></label>
                                <input type="date" name="date_entree" class="form-control" required>
                                <span class="help-block"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ÉTAPE 4: Administration & Statut -->
                <div class="form-step" data-step="4">
                    <h3 class="step-title"><i class="fa fa-cog"></i> Administration & Statut</h3>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Numéro CNPS</label>
                                <input type="text" name="num_CNPS" class="form-control">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Numéro CMU</label>
                                <input type="text" name="num_CMU" class="form-control">
                                <span class="help-block"></span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Statut <span class="required">*</span></label>
                        <select name="status" class="form-control select2" required>
                            <option value="">Sélectionner</option>
                            <option value="actif">Actif</option>
                            <option value="inactif">Inactif</option>
                            <option value="suspendu">Suspendu</option>
                        </select>
                        <span class="help-block"></span>
                    </div>

                    <div class="form-group">
                        <label>Commentaire</label>
                        <textarea name="commentaire" class="form-control" rows="4"></textarea>
                        <span class="help-block"></span>
                    </div>
                </div>

                <!-- ÉTAPE 5: Contacts & Famille -->
                <div class="form-step" data-step="5">
                    <h3 class="step-title"><i class="fa fa-users"></i> Contacts & Famille</h3>

                    <h4 style="color: #34495e; margin-bottom: 20px; font-size: 16px;">
                        <i class="fa fa-exclamation-circle"></i> Personne à Contacter en Cas d'Urgence
                    </h4>

                    <div class="form-group">
                        <label>Nom Complet <span class="required">*</span></label>
                        <input type="text" name="nom_complet_personne_urgence" class="form-control" required>
                        <span class="help-block"></span>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Lien avec l'Employé <span class="required">*</span></label>
                                <select name="lien_personne_urgence" class="form-control select2" required>
                                    <option value="">Sélectionner</option>
                                    <option value="conjoint">Conjoint</option>
                                    <option value="parent">Parent</option>
                                    <option value="enfant">Enfant</option>
                                    <option value="frère">Frère</option>
                                    <option value="sœur">Sœur</option>
                                    <option value="autre">Autre</option>
                                </select>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group conditional-field" id="autre-lien-group">
                                <label>Préciser le Lien <span class="required">*</span></label>
                                <input type="text" name="autre_lien_personne_urgence" class="form-control">
                                <span class="help-block"></span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Contact d'Urgence <span class="required">*</span></label>
                        <input type="text" name="contact_personne_urgence" class="form-control" required>
                        <span class="help-block"></span>
                    </div>

                    <hr style="margin: 30px 0;">

                    <h4 style="color: #34495e; margin-bottom: 20px; font-size: 16px;">
                        <i class="fa fa-heart"></i> Informations Conjoint(e)
                    </h4>

                    <div class="form-group conditional-field" id="conjoint-group">
                        <label>Nom Complet du Conjoint <span class="required">*</span></label>
                        <input type="text" name="nom_complet_conjoint" class="form-control">
                        <span class="help-block"></span>
                    </div>

                    <div class="form-group conditional-field" id="extrait-conjoint-group">
                        <label>Extrait du Conjoint (PDF/Image)</label>
                        <input type="file" name="extrait_conjoint" class="form-control"
                            accept=".pdf,.jpg,.jpeg,.png">
                        <span class="help-block"></span>
                        <div class="file-preview" id="preview-extrait-conjoint"></div>
                    </div>

                    <hr style="margin: 30px 0;">

                    <h4 style="color: #34495e; margin-bottom: 20px; font-size: 16px;">
                        <i class="fa fa-file-text"></i> Document d'Identité
                    </h4>

                    <div class="form-group">
                        <label>Extrait de Naissance (PDF/Image)</label>
                        <input type="file" name="extrait_naissance" class="form-control"
                            accept=".pdf,.jpg,.jpeg,.png">
                        <span class="help-block"></span>
                        <div class="file-preview" id="preview-extrait-naissance"></div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="button" class="btn btn-prev btn-step" style="display: none;">
                        <i class="fa fa-arrow-left"></i> Précédent
                    </button>
                    <div></div>
                    <button type="button" class="btn btn-next btn-step">
                        Suivant <i class="fa fa-arrow-right"></i>
                    </button>
                    <button type="submit" class="btn btn-submit btn-step" style="display: none;">
                        <i class="fa fa-check"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('javascript')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            let currentStep = 1;
            const totalSteps = 5;

            // Initialize Select2
            $('.select2').select2({
                width: '100%',
                placeholder: 'Sélectionner...'
            });

            // Update progress bar
            function updateProgress() {
                const progress = ((currentStep - 1) / (totalSteps - 1)) * 100;
                $('.progress-line').css('width', progress + '%');

                $('.step-item').each(function() {
                    const step = $(this).data('step');
                    $(this).removeClass('active completed');

                    if (step < currentStep) {
                        $(this).addClass('completed');
                    } else if (step === currentStep) {
                        $(this).addClass('active');
                    }
                });
            }

            // Show/hide buttons
            function updateButtons() {
                if (currentStep === 1) {
                    $('.btn-prev').hide();
                } else {
                    $('.btn-prev').show();
                }

                if (currentStep === totalSteps) {
                    $('.btn-next').hide();
                    $('.btn-submit').show();
                } else {
                    $('.btn-next').show();
                    $('.btn-submit').hide();
                }
            }

            // Show alert
            function showAlert(message, type = 'danger') {
                const alert = `
            <div class="alert alert-${type} alert-step alert-dismissible fade in">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <i class="fa fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                ${message}
            </div>
        `;
                $('#alert-container').html(alert);
                $('html, body').animate({
                    scrollTop: 0
                }, 300);

                setTimeout(() => {
                    $('.alert-step').fadeOut();
                }, 5000);
            }

            // Validate current step
            function validateStep() {
                const $currentForm = $(`.form-step[data-step="${currentStep}"]`);
                let isValid = true;

                // Clear previous errors
                $currentForm.find('.form-group').removeClass('has-error');
                $currentForm.find('.help-block').text('');

                // Validate required fields
                $currentForm.find('[required]:visible').each(function() {
                    const $field = $(this);
                    const value = $field.val();

                    if (!value || value.trim() === '') {
                        isValid = false;
                        $field.closest('.form-group').addClass('has-error');
                        $field.siblings('.help-block').text('Ce champ est requis');
                    }
                });

                // Validate email format
                const $email = $currentForm.find('input[type="email"]');
                if ($email.length && $email.val()) {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test($email.val())) {
                        isValid = false;
                        $email.closest('.form-group').addClass('has-error');
                        $email.siblings('.help-block').text('Format email invalide');
                    }
                }

                return isValid;
            }

            // Save step data to session via AJAX
            function saveStepData() {
                const formData = new FormData($('#multistep-form')[0]);
                formData.append('current_step', currentStep);

                return $.ajax({
                    url: '{{ route('voyager.personnels.create') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
            }

            // Next button
            $('.btn-next').click(function() {
                if (!validateStep()) {
                    showAlert('Veuillez remplir tous les champs requis avant de continuer.');
                    return;
                }

                // Save current step data
                saveStepData().done(function(response) {
                    if (response.success) {
                        currentStep++;
                        $(`.form-step`).removeClass('active');
                        $(`.form-step[data-step="${currentStep}"]`).addClass('active');
                        updateProgress();
                        updateButtons();
                        $('html, body').animate({
                            scrollTop: 0
                        }, 300);
                    } else {
                        showAlert('Erreur lors de la sauvegarde des données.');
                    }
                }).fail(function() {
                    showAlert('Erreur de connexion au serveur.');
                });
            });

            // Previous button
            $('.btn-prev').click(function() {
                currentStep--;
                $(`.form-step`).removeClass('active');
                $(`.form-step[data-step="${currentStep}"]`).addClass('active');
                updateProgress();
                updateButtons();
                $('html, body').animate({
                    scrollTop: 0
                }, 300);
            });

            // Form submission
            $('#multistep-form').submit(function(e) {
                e.preventDefault();

                if (!validateStep()) {
                    showAlert('Veuillez remplir tous les champs requis.');
                    return;
                }

                const formData = new FormData(this);

                $.ajax({
                    url: '{{ route('voyager.personnels.create') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                        $('.btn-submit').prop('disabled', true).html(
                            '<i class="fa fa-spinner fa-spin"></i> Enregistrement...');
                    },
                    success: function(response) {
                        if (response.success) {
                            showAlert('Employé enregistré avec succès!', 'success');
                            setTimeout(() => {
                                window.location.href =
                                    '{{ route('voyager.personnels.create') }}';
                            }, 2000);
                        } else {
                            showAlert('Erreur lors de l\'enregistrement: ' + response.message);
                            $('.btn-submit').prop('disabled', false).html(
                                '<i class="fa fa-check"></i> Enregistrer');
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'Erreur lors de l\'enregistrement.';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = Object.values(xhr.responseJSON.errors).flat();
                            errorMsg = errors.join('<br>');
                        }
                        showAlert(errorMsg);
                        $('.btn-submit').prop('disabled', false).html(
                            '<i class="fa fa-check"></i> Enregistrer');
                    }
                });
            });

            // Conditional fields - Situation matrimoniale
            $('select[name="situation_matrimoniale"]').change(function() {
                if ($(this).val() === 'marié(e)') {
                    $('#conjoint-group, #extrait-conjoint-group').slideDown().removeClass(
                        'conditional-field');
                    $('input[name="nom_complet_conjoint"]').prop('required', true);
                } else {
                    $('#conjoint-group, #extrait-conjoint-group').slideUp().addClass('conditional-field');
                    $('input[name="nom_complet_conjoint"]').prop('required', false).val('');
                    $('input[name="extrait_conjoint"]').val('');
                }
            });

            // Conditional fields - Lien personne urgence
            $('select[name="lien_personne_urgence"]').change(function() {
                if ($(this).val() === 'autre') {
                    $('#autre-lien-group').slideDown().removeClass('conditional-field');
                    $('input[name="autre_lien_personne_urgence"]').prop('required', true);
                } else {
                    $('#autre-lien-group').slideUp().addClass('conditional-field');
                    $('input[name="autre_lien_personne_urgence"]').prop('required', false).val('');
                }
            });

            // File preview
            $('input[type="file"]').change(function() {
                const file = this.files[0];
                const previewId = '#preview-' + $(this).attr('name');
                const $preview = $(previewId);

                if (file) {
                    const fileType = file.type;
                    const fileName = file.name;
                    const fileSize = (file.size / 1024).toFixed(2);

                    if (fileType.includes('image')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            $preview.html(`
                        <img src="${e.target.result}" alt="Preview">
                        <div class="file-info">
                            <i class="fa fa-file-image-o"></i> ${fileName} (${fileSize} KB)
                        </div>
                    `).show();
                        };
                        reader.readAsDataURL(file);
                    } else if (fileType.includes('pdf')) {
                        $preview.html(`
                    <div class="file-info">
                        <i class="fa fa-file-pdf-o" style="font-size: 48px; color: #e74c3c;"></i>
                        <div>${fileName} (${fileSize} KB)</div>
                    </div>
                `).show();
                    }
                } else {
                    $preview.hide();
                }
            });

            // Initialize
            updateProgress();
            updateButtons();
        });
    </script>
@endsection
