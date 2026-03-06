<script>
    let montantTotal = 0;

    $(document).ready(function() {

        $('#membre_id').select2({
            width: '100%',
            placeholder: '-- Sélectionner --'
        }).on('change', function() {
            updateMembreInfos();
        });

        $('input[name="type_versement"]').on('change', function() {
            updateMontant();
        });

        $('#montant').on('input', function() {
            calculReste();
        });

        updateMembreInfos();
        updateMontant();
    });

    /* ===================== */
    /*  FONCTIONS PRINCIPALES */
    /* ===================== */

    function updateMembreInfos() {
        let option = $('#membre_id option:selected');

        montantTotal = parseInt(option.data('montant')) || 0;
        let plan = option.data('plan') || '---';

        $('#display-categorie').text(plan);
        $('#display-montant-auto').text(format(montantTotal) + ' CFA');
        $('input[name="montant_total_attendu"]').val(montantTotal);

        calculReste();
    }

    function updateMontant() {
        let type = $('input[name="type_versement"]:checked').val();

        if (type === 'TOTAL' && montantTotal > 0) {
            $('#montant').val(montantTotal).prop('readonly', true);
        } else {
            $('#montant').prop('readonly', false);
        }

        calculReste();
    }

    function calculReste() {
        let paye = parseInt($('#montant').val()) || 0;
        let reste = montantTotal - paye;

        if (reste < 0) reste = 0;

        $('#display-restant').text(format(reste) + ' CFA');
        $('#reste_a_payer').val(reste);
    }

    /* ===================== */
    /*  UTILITAIRE */
    /* ===================== */

    function format(valeur) {
        return new Intl.NumberFormat('fr-FR').format(valeur);
    }
</script>
