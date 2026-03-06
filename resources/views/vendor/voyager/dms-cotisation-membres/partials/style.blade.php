<style>
    /* Variables de couleurs Access */
    :root {
        --access-blue: #2b579a;
        --access-dark: #1a365d;
        --access-light: #f0f4f8;
        --access-border: #d1d1d1;
        --access-gray-bg: #f8f9fa;
        --access-accent: #3b71ca;
    }

    /* En-tête de section (Ribbon Style) */
    .section-header {
        background-color: var(--access-blue);
        color: white;
        padding: 10px 18px;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        margin-bottom: 0;
        border-left: 6px solid var(--access-dark);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        display: flex;
        align-items: center;
    }

    .section-header i {
        margin-right: 10px;
        font-size: 16px;
    }

    /* Barre de filtres */
    .filter-bar {
        background: #fff;
        padding: 20px;
        border: 1px solid var(--access-border);
        border-top: none;
        margin-bottom: 25px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    /* Cartes KPIs (Style Objets Access) */
    .kpi-card {
        background: #fff;
        border: 1px solid var(--access-border);
        border-top: 3px solid var(--access-blue);
        margin-bottom: 20px;
        transition: all 0.2s ease;
        border-radius: 2px;
    }

    .kpi-card:hover {
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.08);
    }

    .kpi-card-header {
        background: var(--access-gray-bg);
        border-bottom: 1px solid var(--access-border);
        padding: 10px 15px;
        font-weight: 700;
        color: var(--access-dark);
        font-size: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .kpi-card-body {
        padding: 20px;
        text-align: center;
    }

    .kpi-amount {
        display: block;
        font-size: 28px;
        font-weight: 800;
        color: var(--access-blue);
        line-height: 1.2;
    }

    .kpi-count {
        display: block;
        font-size: 14px;
        color: #6c757d;
        margin-top: 8px;
    }

    /* Graphique Container */
    .chart-container {
        background: #fff;
        border: 1px solid var(--access-border);
        padding: 20px;
        margin-bottom: 25px;
        border-top: 3px solid var(--access-blue);
    }

    /* Accordion Custom Access (Style Panneau de Navigation) */
    .accordion-item {
        border-radius: 0 !important;
        border: 1px solid var(--access-border) !important;
        margin-bottom: 5px;
        background-color: #fff;
    }

    .accordion-button {
        background-color: var(--access-gray-bg) !important;
        color: var(--access-dark) !important;
        font-weight: 700 !important;
        border-radius: 0 !important;
        padding: 12px 20px;
        font-size: 14px;
        box-shadow: none !important;
    }

    .accordion-button:not(.collapsed) {
        border-bottom: 1px solid var(--access-border);
        background-color: #eef3fb !important;
        color: var(--access-blue) !important;
    }

    .accordion-button::after {
        background-size: 1rem;
    }

    /* Stat Cards (Style &quot;Fiches&quot;) */
    .stat-card {
        background: #fff;
        border: 1px solid #e1e1e1;
        padding: 15px;
        margin-bottom: 15px;
        border-left: 4px solid var(--access-blue);
        transition: transform 0.2s ease;
    }

    .stat-card:hover {
        background-color: #fcfcfc;
        transform: scale(1.02);
    }

    .stat-value {
        font-size: 18px;
        font-weight: 700;
        color: var(--access-dark);
        display: block;
    }

    .text-xof {
        font-size: 11px;
        color: #999;
        margin-left: 2px;
    }

    /* Boutons Access */
    .btn-access {
        background-color: #fff;
        border: 1px solid var(--access-blue);
        color: var(--access-blue);
        border-radius: 2px;
        font-weight: 600;
        padding: 6px 15px;
        font-size: 12px;
        transition: all 0.2s;
    }

    .btn-access:hover {
        background-color: var(--access-blue);
        color: #fff;
    }

    .btn-add {
        background-color: var(--access-blue);
        color: #fff;
        border: 1px solid var(--access-dark);
        border-radius: 2px;
        font-weight: 600;
        padding: 8px 18px;
        font-size: 13px;
        text-transform: uppercase;
    }

    .btn-add:hover {
        background-color: var(--access-dark);
        color: #fff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    /* Tableaux style Datagrid */
    .table-access {
        border: 1px solid var(--access-border);
    }

    .table-access thead th {
        background-color: var(--access-gray-bg);
        color: var(--access-dark);
        font-weight: 700;
        text-transform: uppercase;
        font-size: 11px;
        border-bottom: 2px solid var(--access-blue) !important;
    }

    /* Footer Utilitaires */
    .utility-box {
        background: #e9ecef;
        border: 1px solid var(--access-border);
        padding: 12px 20px;
        margin-top: 30px;
        display: flex;
        gap: 10px;
        align-items: center;
        border-radius: 2px;
    }
</style>
