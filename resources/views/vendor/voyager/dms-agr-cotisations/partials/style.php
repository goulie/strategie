<style>
    /* Barre d'outils style Ruban Office */
    .access-ribbon {
        background-color: #ffffff;
        border-bottom: 2px solid #0056b3;
        padding: 10px 25px;
        margin-bottom: 20px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        display: flex;
        justify-content: flex-end;
        /* Aligné à droite car titre supprimé */
        align-items: center;
    }

    .btn-agr {
        background-color: #0056b3;
        color: white;
        border: none;
        padding: 8px 20px;
        font-weight: bold;
        border-radius: 2px;
        transition: all 0.3s;
        margin-right: 10px;
    }

    .btn-agr:hover {
        background-color: #003d82;
        color: #fff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* KPI Cards Styles */
    .kpi-row {
        margin-bottom: 20px;
    }

    .kpi-main-card {
        background: #fff;
        border: 1px solid #dee2e6;
        border-top: 4px solid #0056b3;
        padding: 20px;
        text-align: center;
        border-radius: 4px;
    }

    .kpi-main-value {
        font-size: 28px;
        font-weight: bold;
        color: #0056b3;
        margin: 10px 0;
    }

    .kpi-main-label {
        font-size: 12px;
        text-transform: uppercase;
        color: #777;
        font-weight: 600;
    }

    /* Section Tableaux et Graphiques */
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
    }

    .table-kpi {
        margin-bottom: 0;
    }

    .table-kpi thead th {
        background-color: #f8faff;
        color: #0056b3;
        font-size: 11px;
        text-transform: uppercase;
        border-bottom: 2px solid #0056b3 !important;
    }

    .total-row {
        background-color: #e7f1ff !important;
        font-weight: bold;
        color: #0056b3;
    }

    .progress-bar-custom {
        height: 8px;
        margin-bottom: 0;
        background-color: #eee;
        border-radius: 10px;
    }

    .progress-bar-fill {
        background-color: #0056b3;
    }

    .chart-container {
        padding: 15px;
        position: relative;
        height: 350px;
    }
</style>